<?php
/**
 * CERAMIC CONNECTION - Product sort (prototype)
 *
 * Adds a sort selector to category listings, rendered inside the active
 * filters bar of ps_facetedsearch (theme template active-filters.tpl calls
 * the displayProductListSort hook) so filters and sort share the same block.
 *
 * Options: featured (default), name and price. Name and price are resolved
 * natively by ps_facetedsearch. "Featured" sorts by the product feature
 * "Prioridad" (1 first); products without priority go after, in their
 * category position. ps_facetedsearch cannot sort by a feature (and
 * Validate::isOrderBy blocks injecting SQL through the "order" param), so we
 * ask the provider for the whole filtered result set, sort it in PHP and
 * slice the requested page ourselves.
 */

use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CcProductSort extends Module
{
    /** Upper bound of products fetched when sorting in PHP. */
    const MAX_PRODUCTS_TO_SORT = 5000;

    /**
     * Original pagination of the query while the priority sort is running,
     * set in the "before" hook and restored in the "after" hook.
     *
     * @var array|null
     */
    protected $pendingPrioritySort = null;

    public function __construct()
    {
        $this->name = 'ccproductsort';
        $this->tab = 'front_office_features';
        $this->version = '0.2.0';
        $this->author = 'CERAMIC CONNECTION';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CC Product sort');
        $this->description = $this->l('Sort selector for category listings: featured (by priority), name and price.');
        $this->ps_versions_compliancy = ['min' => '1.7.6', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionProductSearchProviderRunQueryBefore')
            && $this->registerHook('actionProductSearchProviderRunQueryAfter')
            && $this->registerHook('displayProductListSort')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    /**
     * Sort options shown to the customer, keyed by the "order" URL value.
     * An empty key means the default order (priority).
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
        ];
    }

    protected function isCategoryPage()
    {
        return isset($this->context->controller->php_self)
            && $this->context->controller->php_self === 'category';
    }

    public function hookActionFrontControllerSetMedia()
    {
        if (!$this->isCategoryPage()) {
            return;
        }

        $this->context->controller->registerStylesheet(
            'module-ccproductsort',
            'modules/' . $this->name . '/views/css/ccproductsort.css',
            ['priority' => 1100]
        );
        $this->context->controller->registerJavascript(
            'module-ccproductsort',
            'modules/' . $this->name . '/views/js/ccproductsort.js',
            ['position' => 'bottom', 'priority' => 200]
        );
    }

    /**
     * Rendered from the theme's ps_facetedsearch active-filters.tpl, so it is
     * also re-rendered on every AJAX facet/sort update.
     */
    public function hookDisplayProductListSort($params = [])
    {
        if (!$this->isCategoryPage()) {
            return '';
        }

        $current = (string) Tools::getValue('order');
        $options = $this->getSortOptions();

        // The selector can be printed more than once (filters bar on mobile +
        // active filters bar), each instance needs its own id.
        $instance = isset($params['instance']) ? preg_replace('/[^a-z0-9_-]/i', '', (string) $params['instance']) : '';

        $this->context->smarty->assign([
            'ccproductsort_options' => $options,
            'ccproductsort_current' => isset($options[$current]) ? $current : '',
            'ccproductsort_id' => 'ccproductsort-select' . ($instance !== '' ? '-' . $instance : ''),
            'ccproductsort_instance' => $instance,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/sort.tpl');
    }

    /**
     * On category pages without an explicit sort, ask for every matching
     * product in category position order (page 1, big page size) so it can
     * be re-sorted by priority afterwards.
     */
    public function hookActionProductSearchProviderRunQueryBefore($params)
    {
        $this->pendingPrioritySort = null;

        $query = $params['query'];

        if ($query->getQueryType() !== 'category' || Tools::getValue('order')) {
            return;
        }

        $this->pendingPrioritySort = [
            'sortOrder' => $query->getSortOrder(),
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
        if ($this->pendingPrioritySort === null) {
            return;
        }

        $query = $params['query'];
        $result = $params['result'];
        $pending = $this->pendingPrioritySort;
        $this->pendingPrioritySort = null;

        $products = $this->sortProductsByPriority($result->getProducts());

        $offset = ($pending['page'] - 1) * $pending['resultsPerPage'];
        $result->setProducts(array_slice($products, $offset, $pending['resultsPerPage']));

        // Restore the query so pagination and facet URLs stay as requested.
        $query
            ->setSortOrder($pending['sortOrder'])
            ->setPage($pending['page'])
            ->setResultsPerPage($pending['resultsPerPage']);
    }

    /**
     * Priority ascending (1 first). Products without a numeric priority go
     * last. Ties keep the incoming (category position) order.
     *
     * @param array $products rows from the search provider, with id_product
     *
     * @return array
     */
    protected function sortProductsByPriority(array $products)
    {
        if (count($products) < 2) {
            return $products;
        }

        $values = $this->getFeatureValues(
            array_column($products, 'id_product'),
            (int) FrontController::FEATURE_PRIORITY
        );

        $keyed = [];
        foreach ($products as $position => $product) {
            $value = isset($values[$product['id_product']]) ? trim($values[$product['id_product']]) : '';
            $keyed[] = [
                'product' => $product,
                'position' => $position,
                'key' => is_numeric($value) ? (float) $value : null,
            ];
        }

        usort($keyed, function ($a, $b) {
            if ($a['key'] === null || $b['key'] === null) {
                $cmp = ($a['key'] === null) - ($b['key'] === null);
            } else {
                $cmp = $a['key'] <=> $b['key'];
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
}
