<?php
/**
 * CERAMIC CONNECTION - Product sort (prototype)
 *
 * Adds a sort selector to category listings: name, price, format (tile size)
 * and material.
 *
 * Name and price are resolved natively by ps_facetedsearch. Format and
 * material are product features, which ps_facetedsearch cannot sort by (and
 * Validate::isOrderBy blocks injecting SQL through the "order" param), so for
 * those we ask the provider for the whole filtered result set, sort it in PHP
 * by the feature value and slice the requested page ourselves.
 */

use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CcProductSort extends Module
{
    const SORT_FIELD_FORMAT = 'ccformato';
    const SORT_FIELD_MATERIAL = 'ccmaterial';

    /** Upper bound of products fetched when sorting in PHP. */
    const MAX_PRODUCTS_TO_SORT = 5000;

    /**
     * Original pagination/sort of the query while a feature sort is running,
     * set in the "before" hook and restored in the "after" hook.
     *
     * @var array|null
     */
    protected $pendingFeatureSort = null;

    public function __construct()
    {
        $this->name = 'ccproductsort';
        $this->tab = 'front_office_features';
        $this->version = '0.1.0';
        $this->author = 'CERAMIC CONNECTION';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CC Product sort');
        $this->description = $this->l('Sort selector for category listings: name, price, format and material.');
        $this->ps_versions_compliancy = ['min' => '1.7.6', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionProductSearchProviderRunQueryBefore')
            && $this->registerHook('actionProductSearchProviderRunQueryAfter')
            && $this->registerHook('displayHeaderCategory')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    /**
     * Sort options shown to the customer, keyed by the "order" URL value.
     * An empty key means the default category order.
     *
     * @return array
     */
    public function getSortOptions()
    {
        return [
            '' => $this->l('Featured'),
            'product.name.asc' => $this->l('Name, A to Z'),
            'product.name.desc' => $this->l('Name, Z to A'),
            'product.price.asc' => $this->l('Price, low to high'),
            'product.price.desc' => $this->l('Price, high to low'),
            'product.' . self::SORT_FIELD_FORMAT . '.asc' => $this->l('Format, small to large'),
            'product.' . self::SORT_FIELD_FORMAT . '.desc' => $this->l('Format, large to small'),
            'product.' . self::SORT_FIELD_MATERIAL . '.asc' => $this->l('Material, A to Z'),
        ];
    }

    /**
     * Feature ID backing each custom sort field.
     *
     * @return array
     */
    protected function getFeatureSortFields()
    {
        return [
            self::SORT_FIELD_FORMAT => (int) FrontController::FEATURE_MEDIDA_ID,
            self::SORT_FIELD_MATERIAL => (int) FrontController::FEATURE_MATERIAL,
        ];
    }

    public function hookActionFrontControllerSetMedia()
    {
        if ($this->context->controller->php_self !== 'category') {
            return;
        }

        $this->context->controller->registerStylesheet(
            'module-ccproductsort',
            'modules/' . $this->name . '/views/css/ccproductsort.css'
        );
        $this->context->controller->registerJavascript(
            'module-ccproductsort',
            'modules/' . $this->name . '/views/js/ccproductsort.js',
            ['position' => 'bottom', 'priority' => 200]
        );
    }

    public function hookDisplayHeaderCategory()
    {
        $current = (string) Tools::getValue('order');
        $options = $this->getSortOptions();

        $this->context->smarty->assign([
            'ccproductsort_options' => $options,
            'ccproductsort_current' => isset($options[$current]) ? $current : '',
        ]);

        return $this->display(__FILE__, 'views/templates/hook/sort.tpl');
    }

    /**
     * If a feature sort is requested, swap it for the default order and ask
     * for every matching product (page 1, big page size).
     */
    public function hookActionProductSearchProviderRunQueryBefore($params)
    {
        $this->pendingFeatureSort = null;

        $query = $params['query'];
        $sortOrder = $query->getSortOrder();
        $featureFields = $this->getFeatureSortFields();

        if (!$sortOrder
            || $sortOrder->getEntity() !== 'product'
            || !isset($featureFields[$sortOrder->getField()])
        ) {
            return;
        }

        $this->pendingFeatureSort = [
            'sortOrder' => $sortOrder,
            'idFeature' => $featureFields[$sortOrder->getField()],
            'page' => $query->getPage(),
            'resultsPerPage' => $query->getResultsPerPage(),
        ];

        $query
            ->setSortOrder(new SortOrder('product', 'position', 'asc'))
            ->setPage(1)
            ->setResultsPerPage(self::MAX_PRODUCTS_TO_SORT);
    }

    public function hookActionProductSearchProviderRunQueryAfter($params)
    {
        $query = $params['query'];
        $result = $params['result'];

        $result->setAvailableSortOrders(array_merge(
            $result->getAvailableSortOrders(),
            $this->getCustomSortOrders()
        ));

        if ($this->pendingFeatureSort === null) {
            return;
        }

        $pending = $this->pendingFeatureSort;
        $this->pendingFeatureSort = null;

        $products = $this->sortProductsByFeature(
            $result->getProducts(),
            $pending['idFeature'],
            $pending['sortOrder']->getField(),
            $pending['sortOrder']->getDirection()
        );

        $offset = ($pending['page'] - 1) * $pending['resultsPerPage'];
        $result->setProducts(array_slice($products, $offset, $pending['resultsPerPage']));

        // Restore the query so pagination, facet URLs and the "current" sort
        // order reflect what the customer asked for.
        $query
            ->setSortOrder($pending['sortOrder'])
            ->setPage($pending['page'])
            ->setResultsPerPage($pending['resultsPerPage']);
        $result->setCurrentSortOrder($pending['sortOrder']);
    }

    /**
     * SortOrder objects for the feature sorts, so they also show up in
     * $listing.sort_orders.
     *
     * @return SortOrder[]
     */
    protected function getCustomSortOrders()
    {
        $sortOrders = [];
        foreach ($this->getSortOptions() as $key => $label) {
            if ($key === '') {
                continue;
            }
            list($entity, $field, $direction) = explode('.', $key);
            if (!array_key_exists($field, $this->getFeatureSortFields())) {
                continue;
            }
            $sortOrder = new SortOrder($entity, $field, $direction);
            $sortOrders[] = $sortOrder->setLabel($label);
        }

        return $sortOrders;
    }

    /**
     * Products without a value (or, for format, without a parseable size)
     * always go last. Ties keep the incoming (category position) order.
     *
     * @param array $products rows from the search provider, with id_product
     * @param int $idFeature
     * @param string $field
     * @param string $direction
     *
     * @return array
     */
    protected function sortProductsByFeature(array $products, $idFeature, $field, $direction)
    {
        if (count($products) < 2) {
            return $products;
        }

        $values = $this->getFeatureValues(array_column($products, 'id_product'), $idFeature);

        $keyed = [];
        foreach ($products as $position => $product) {
            $value = isset($values[$product['id_product']]) ? $values[$product['id_product']] : null;
            $keyed[] = [
                'product' => $product,
                'position' => $position,
                'key' => $field === self::SORT_FIELD_FORMAT
                    ? $this->getFormatSortKey($value)
                    : $this->getTextSortKey($value),
            ];
        }

        $sign = strtolower($direction) === 'desc' ? -1 : 1;

        usort($keyed, function ($a, $b) use ($sign) {
            if ($a['key'] === null || $b['key'] === null) {
                $cmp = ($a['key'] === null) - ($b['key'] === null);
            } elseif (is_string($a['key'])) {
                $cmp = $sign * strnatcmp($a['key'], $b['key']);
            } else {
                $cmp = $sign * ($a['key'] <=> $b['key']);
            }

            return $cmp !== 0 ? $cmp : $a['position'] - $b['position'];
        });

        return array_column($keyed, 'product');
    }

    /**
     * @param int[] $productIds
     * @param int $idFeature
     *
     * @return array id_product => feature value in the current language
     */
    protected function getFeatureValues(array $productIds, $idFeature)
    {
        $productIds = array_filter(array_map('intval', $productIds));
        if (empty($productIds)) {
            return [];
        }

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT fp.`id_product`, fvl.`value`
            FROM `' . _DB_PREFIX_ . 'feature_product` fp
            INNER JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl
                ON fvl.`id_feature_value` = fp.`id_feature_value`
                AND fvl.`id_lang` = ' . (int) $this->context->language->id . '
            WHERE fp.`id_feature` = ' . (int) $idFeature . '
            AND fp.`id_product` IN (' . implode(',', $productIds) . ')'
        );

        $values = [];
        foreach ($rows ?: [] as $row) {
            // A product may have several values; keep the first one.
            if (!isset($values[$row['id_product']])) {
                $values[$row['id_product']] = $row['value'];
            }
        }

        return $values;
    }

    /**
     * Surface of the piece in cm², from the first "AxB" in values like
     * "20x20 cm", "10 x 11,55 cm", "MALLA 30x30 cm" or
     * "20x20 PRECORTADO (10x10)". Values like "Saco de 3 kg" return null.
     *
     * @param string|null $value
     *
     * @return float|null
     */
    protected function getFormatSortKey($value)
    {
        if ($value === null
            || !preg_match('/(\d+(?:[.,]\d+)?)\s*[x×]\s*(\d+(?:[.,]\d+)?)/iu', $value, $matches)
        ) {
            return null;
        }

        return (float) str_replace(',', '.', $matches[1]) * (float) str_replace(',', '.', $matches[2]);
    }

    /**
     * @param string|null $value
     *
     * @return string|null
     */
    protected function getTextSortKey($value)
    {
        $value = $value === null ? '' : trim($value);
        if ($value === '') {
            return null;
        }

        return Tools::strtolower(Tools::replaceAccentedChars($value));
    }
}
