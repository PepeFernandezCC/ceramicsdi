<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'Ps_FacetedsearchFiltersConverter.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'Ps_FacetedsearchFacetsURLSerializer.php';

use PrestaShop\PrestaShop\Core\Product\Search\URLFragmentSerializer;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\Facet;
use PrestaShop\PrestaShop\Core\Product\Search\FacetCollection;
use PrestaShop\PrestaShop\Core\Product\Search\Filter;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;

class Ps_FacetedsearchProductSearchProvider implements ProductSearchProviderInterface,FacetsRendererInterface
{
    private $module;
    private $filtersConverter;
    private $facetsSerializer;
    private $ajax;

    public function __construct(Ps_Facetedsearch $module)
    {
        $this->module = $module;
        $this->filtersConverter = new Ps_FacetedsearchFiltersConverter();
        $this->facetsSerializer = new Ps_FacetedsearchFacetsURLSerializer();
        $this->ajax = isset($_SERVER['HTTP_ACCEPT']) ? preg_match(
                '#\bapplication/json\b#',
                $_SERVER['HTTP_ACCEPT']
            ) : false;
    }

    public function getFacetCollectionFromEncodedFacets(
        ProductSearchQuery $query
    ) {
        // do not compute range filters, all info we need is encoded in $encodedFacets
        $compute_range_filters = false;
        $filterBlock = $this->module->getFilterBlock(
            [],
            $compute_range_filters
        );

        $queryTemplate = $this->filtersConverter->getFacetsFromFacetedSearchFilters(
            $filterBlock['filters']
        );

        $facets = $this->facetsSerializer->setFiltersFromEncodedFacets(
            $queryTemplate,
            $query->getEncodedFacets()
        );

        return (new FacetCollection())->setFacets($facets);
    }

    private function copyFiltersActiveState(
        array $sourceFacets,
        array $targetFacets
    ) {
        $copyByLabel = function (Facet $source, Facet $target) {
            foreach ($target->getFilters() as $targetFilter) {
                foreach ($source->getFilters() as $sourceFilter) {
                    if ($sourceFilter->getLabel() === $targetFilter->getLabel()) {
                        $targetFilter->setActive($sourceFilter->isActive());
                        break;
                    }
                }
            }
        };

        $copyByRangeValue = function (Facet $source, Facet $target) {
            foreach ($source->getFilters() as $sourceFilter) {
                if ($sourceFilter->isActive()) {
                    $foundRange = false;
                    /*foreach ($target->getFilters() as $targetFilter) {
                        $tFrom = $targetFilter->getValue()['from'];
                        $tTo = $targetFilter->getValue()['to'];
                        $sFrom = $sourceFilter->getValue()['from'];
                        $sTo = $sourceFilter->getValue()['to'];
                        if ($tFrom <= $sFrom && $sTo <= $tTo) {
                            $foundRange = true;
                            $targetFilter->setActive(true);
                            break;
                        }
                    }*/
                    if (!$foundRange) {
                        $filter = clone $sourceFilter;
                        $filter->setDisplayed(false);
                        $target->addFilter($filter);
                    }
                    break;
                }
            }
        };

        $copy = function (
            Facet $source,
            Facet $target
        ) use (
            $copyByLabel,
            $copyByRangeValue
        ) {
            if ($target->getProperty('range')) {
                $strategy = $copyByRangeValue;
            } else {
                $strategy = $copyByLabel;
            }

            $strategy($source, $target);
        };
        //remove when all are selected
        foreach ($sourceFacets as $k=>$v) {
            if($v->getWidgetType()=='rangeslider'){
                $v_filters = $v->getFilters();
                $v_active_filter_number = 0;
                foreach ($v_filters as $filter) {
                    $filter->isActive() && $v_active_filter_number++;
                }
                if(count($v_filters)==$v_active_filter_number)
                    unset($sourceFacets[$k]);
            }
        }
        foreach ($targetFacets as $targetFacet) {
            foreach ($sourceFacets as $sourceFacet) {
                if ($sourceFacet->getLabel() === $targetFacet->getLabel()) {
                    $copy($sourceFacet, $targetFacet);
                    break;
                }
            }
        }
    }

    private function getAvailableSortOrders()
    {
        return [
            (new SortOrder('product', 'position', 'asc'))->setLabel(
                $this->module->getTranslator()->trans('Relevance', array(), 'Modules.Facetedsearch.Shop')
            ),
            (new SortOrder('product', 'name', 'asc'))->setLabel(
                $this->module->getTranslator()->trans('Name, A to Z', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'name', 'desc'))->setLabel(
                $this->module->getTranslator()->trans('Name, Z to A', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'price', 'asc'))->setLabel(
                $this->module->getTranslator()->trans('Price, low to high', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'price', 'desc'))->setLabel(
                $this->module->getTranslator()->trans('Price, high to low', array(), 'Shop.Theme.Catalog')
            ),
        ];
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $result = new ProductSearchResult();
        $menu = $this->getFacetCollectionFromEncodedFacets($query);

        $order_by = $query->getSortOrder()->toLegacyOrderBy(true);
        $order_way = $query->getSortOrder()->toLegacyOrderWay();

        $facetedSearchFilters = $this->filtersConverter->getFacetedSearchFiltersFromFacets(
            $menu->getFacets()
        );

        $productsAndCount = $this->module->getProductByFilters(
            $query->getResultsPerPage(),
            $query->getPage(),
            $order_by,
            $order_way,
            $context->getIdLang(),
            $facetedSearchFilters
        );

        $result
            ->setProducts($productsAndCount['products'])
            ->setTotalProductsCount($productsAndCount['count'])
            ->setAvailableSortOrders($this->getAvailableSortOrders())
        ;

        $filterBlock = $this->module->getFilterBlock($facetedSearchFilters, true, Configuration::get($this->module->_prefix_st.'PRICE'), Configuration::get($this->module->_prefix_st.'WEIGHT'));
        $facets = $this->filtersConverter->getFacetsFromFacetedSearchFilters(
            $filterBlock['filters']
        );

        $this->copyFiltersActiveState(
            $menu->getFacets(),
            $facets
        );

        $this->labelRangeFilters($facets);

        $this->addEncodedFacetsToFilters($facets);
        $this->addPWEncodedFacets($facets);

        $this->hideZeroValues($facets);
        $this->hideUselessFacets($facets);

        $nextMenu = (new FacetCollection())->setFacets($facets);
        $result->setFacetCollection($nextMenu);
        $result->setEncodedFacets($this->facetsSerializer->serialize($facets));

        return $result;
    }

    private function labelRangeFilters(array $facets)
    {
        foreach ($facets as $facet) {
            if ($facet->getType() === 'weight') {
                $unit = Configuration::get('PS_WEIGHT_UNIT');
                foreach ($facet->getFilters() as $filter) {
                    $filter->setLabel(
                        sprintf(
                            '%1$s%2$s - %3$s%4$s',
                            Tools::displayNumber($filter->getValue()['from']),
                            $unit,
                            Tools::displayNumber($filter->getValue()['to']),
                            $unit
                        )
                    );
                }
            } elseif ($facet->getType() === 'price') {
                foreach ($facet->getFilters() as $filter) {
                    $filter->setLabel(
                        sprintf(
                            '%1$s - %2$s',
                            Tools::displayPrice($filter->getValue()['from']),
                            Tools::displayPrice($filter->getValue()['to'])
                        )
                    );
                }
            }
        }
    }

    /**
     * This method generates a URL stub for each filter inside the given facets
     * and assigns this stub to the filters.
     * The URL stub is called 'nextEncodedFacets' because it is used
     * to generate the URL of the search once a filter is activated.
     */
    private function addEncodedFacetsToFilters(array $facets)
    {
        // first get the currently active facetFilter in an array
        $activeFacetFilters = $this->facetsSerializer->getActiveFacetFiltersFromFacets($facets);
        $urlSerializer = new URLFragmentSerializer();

        foreach ($facets as $facet) {
            $tempActiveFacetFilters = $activeFacetFilters;
            // If only one filter can be selected, we keep track of
            // the current active filter to disable it before generating the url stub
            // and not select two filters in a facet that can have only one active filter.
            if (!$facet->isMultipleSelectionAllowed()) {
                foreach ($facet->getFilters() as $filter) {
                    if ($filter->isActive()) {
                        // we have a currently active filter is the facet, remove it from the facetFilter array
                        $tempActiveFacetFilters = $this->facetsSerializer->removeFilterFromFacetFilters($tempActiveFacetFilters, $filter, $facet);
                        break;
                    }
                }
            }
            $filters = $facet->getFilters();
            $active_filters = array();
            if($facet->getWidgetType()=='rangeslider'){
                foreach ($filters as $filter) {
                    if ($filter->isActive())
                        $active_filters[] = $filter;
                }
            }
            foreach ($filters as $filter) {
                $facetFilters = $tempActiveFacetFilters;

                // toggle the current filter
                if ($filter->isActive()) {
                    if(count($active_filters)>1){
                        foreach ($active_filters as $af) {
                            $facetFilters = $this->facetsSerializer->removeFilterFromFacetFilters($facetFilters, $af, $facet);
                        }
                    }
                    else
                        $facetFilters = $this->facetsSerializer->removeFilterFromFacetFilters($facetFilters, $filter, $facet);
                } else {
                    $facetFilters = $this->facetsSerializer->addFilterToFacetFilters($facetFilters, $filter, $facet);
                }
                // We've toggled the filter, so the call to serialize
                // returns the "URL" for the search when user has toggled
                // the filter.
                $filter->setNextEncodedFacets(
                    $urlSerializer->serialize($facetFilters)
                );
            }
        }
    }
    private function addPWEncodedFacets(array $facets)
    {
        // first get the currently active facetFilter in an array
        $activeFacetFilters = $this->facetsSerializer->getActiveFacetFiltersFromFacets($facets);
        $urlSerializer = new URLFragmentSerializer();

        foreach ($facets as $facet) {
            if($facet->getType()!='price' && $facet->getType()!='weight' && $facet->getWidgetType()!='rangeslider')
                continue;
            $tempActiveFacetFilters = $activeFacetFilters;
            // If only one filter can be selected, we keep track of
            // the current active filter to disable it before generating the url stub
            // and not select two filters in a facet that can have only one active filter.
            if (!$facet->isMultipleSelectionAllowed()) {
                foreach ($facet->getFilters() as $filter) {
                    if ($filter->isActive()) {
                        // we have a currently active filter is the facet, remove it from the facetFilter array
                        $tempActiveFacetFilters = $this->facetsSerializer->removeFilterFromFacetFilters($tempActiveFacetFilters, $filter, $facet);
                        break;
                    }
                }
            }

            foreach ($facet->getFilters() as $filter) {
                $facetFilters = $tempActiveFacetFilters;
                $facetFilters = $this->facetsSerializer->addFilterToFacetFilters($facetFilters, $filter, $facet);
                if($facet->getType()=='price' || $facet->getType()=='weight'){
                    unset($facetFilters[$facet->getLabel()][1]);
                    unset($facetFilters[$facet->getLabel()][2]);
                }elseif($facet->getWidgetType()=='rangeslider'){
                    unset($facetFilters[$facet->getLabel()]);//move to be last one
                    $facetFilters[$facet->getLabel()]= [];
                }
                $facet->setProperty('url', $this->updateQueryString(array(
                    'q' => $urlSerializer->serialize($facetFilters),
                    'page' => null,
                )));
                break;
            }
        }
    }

    private function hideZeroValues(array $facets)
    {
        foreach ($facets as $facet) {
            foreach ($facet->getFilters() as $filter) {
                if ($filter->getMagnitude() === 0) {
                    $filter->setDisplayed(false);
                }
            }
        }
    }

    private function hideUselessFacets(array $facets)
    {
        foreach ($facets as $facet) {
            $usefulFiltersCount = 0;
            if($facet->getType()=='price' || $facet->getType()=='weight') {
                $facet->setDisplayed(count($facet->getFilters())>0);
            }elseif($facet->getWidgetType()=='rangeslider'){
                $facet->setDisplayed(count($facet->getFilters())>1 && $facet->getProperty('values'));
            }else {
                foreach ($facet->getFilters() as $filter) {
                    if ($filter->getMagnitude() > 0) {
                        ++$usefulFiltersCount;
                    }
                }
                $facet->setDisplayed(
                    $usefulFiltersCount > 1
                );
            }
        }
    }

    public function renderFacets(ProductSearchContext $context, ProductSearchResult $result)
    {
        $facetCollection = $result->getFacetCollection();
        // not all search providers generate menus
        if (empty($facetCollection)) {
            return '';
        }

        $facets = $facetCollection->getFacets();
        foreach ($facets as $facet) {
            if($facet->getType()=='price' || $facet->getType()=='weight')
            {
                foreach ($facet->getFilters() as $k => $filter) {
                    if ($filter->isActive()) {
                        $facet->setProperty('lower', $filter->getValue()['from']);
                        $facet->setProperty('upper', $filter->getValue()['to']);
                    }
                }
            }
            if($facet->getWidgetType()=='rangeslider')
            {
                $rangeslider_values = array();
                foreach ($facet->getFilters() as $k => $filter) {
                    if ($filter->isActive()) {
                        if(preg_match("/^([^\d]*)(.*?)([^\d]*)$/", $filter->getLabel(), $match)){
                            if($match[2])
                                $rangeslider_values[] = (float)$match[2];
                        }
                    }
                }
                if($rangeslider_values){
                    sort($rangeslider_values);
                    $facet->setProperty('lower', $rangeslider_values[0]);
                    $facet->setProperty('upper', $rangeslider_values[count($rangeslider_values)-1]);
                }
            }
        }

        $facetsVar = array_map(
            array($this, 'prepareFacetForTemplate'),
            $facets
        );

        $activeFilters = array();
        foreach ($facetsVar as $facet) {
            foreach ($facet['filters'] as $filter) {
                /*if($facet['type']=='feature'){
                    if ($filter['active'] && $filter['displayed']){
                        $activeFilters[] = $filter;
                    }
                } else*/
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }
        $template = 'module:ps_facetedsearch/views/templates/hook/facets.tpl';
        if(Module::isInstalled('stthemeeditor') && Module::isEnabled('stthemeeditor')){
            $template = _PS_THEME_DIR_.'/templates/catalog/_partials/facets.tpl';
        }
        return $this->module->fetch($template, array(
            'facets' => $facetsVar,
            'js_enabled' => $this->ajax,
            'activeFilters' => $activeFilters,
            'sort_order' => $result->getCurrentSortOrder()->toString(),
            'clear_all_link' => $this->updateQueryString(array('q' => null, 'page' => null)),
            'with_inputs' => Configuration::get($this->module->_prefix_st.'WITH_INPUTS'),
            'range_style' => Configuration::get($this->module->_prefix_st.'RANGE_STYLE'),
            'vertical' => Configuration::get($this->module->_prefix_st.'VERTICAL'),
            'price_range_slider' => Configuration::get($this->module->_prefix_st.'PRICE'),
            'weight_range_slider' => Configuration::get($this->module->_prefix_st.'WEIGHT'),
            'disable_range_text' => Configuration::get($this->module->_prefix_st.'DISABLE_RANGE_TEXT'),
            'ps_layered_show_qties' => Configuration::get('PS_LAYERED_SHOW_QTIES'),
        ));
    }

    /**
     * Renders an array of active filters.
     *
     * @param array $facets
     *
     * @return string the HTML of the facets
     */
    public function renderActiveFilters(ProductSearchContext $context, ProductSearchResult $result)
    {
        $facetCollection = $result->getFacetCollection();
        // not all search providers generate menus
        if (empty($facetCollection)) {
            return '';
        }

        $facetsVar = array_map(
            array($this, 'prepareFacetForTemplate'),
            $facetCollection->getFacets()
        );

        $activeFilters = array();
        foreach ($facetsVar as $facet) {
            if(isset($facet['properties']['values'])){
                $hebing = false;
                $all_active_values = array();
                foreach ($facet['filters'] as $filter) {
                    if($filter['active'])
                        $all_active_values[] = $filter['label'];
                    if ($filter['active'] && !$hebing) {
                        $hebing = $filter;
                        continue;
                    }
                }
                if($hebing){
                    if(count($all_active_values)>1){
                        sort($all_active_values);
                        $filter['label'] = $all_active_values[0].'-'.array_pop($all_active_values);
                    }
                    $activeFilters[] = $filter;
                }
            }else{
                foreach ($facet['filters'] as $filter) {
                    if($facet['type']=='feature'){
                        if ($filter['active'] && $filter['displayed']){
                            $activeFilters[] = $filter;
                        }
                    } elseif ($filter['active']) {
                        $activeFilters[] = $filter;
                    }
                }
            }
        }
        return Context::getContext()->smarty->fetch(_PS_THEME_DIR_.'/templates/catalog/_partials/active_filters.tpl', array(
            'activeFilters' => $activeFilters,
            'clear_all_link' => $this->updateQueryString(array('q' => null, 'page' => null))
        ));
    }
    protected function prepareFacetForTemplate(Facet $facet)
    {
        $facetsArray = $facet->toArray();
        foreach ($facetsArray['filters'] as &$filter) {
            $filter['facetLabel'] = $facet->getLabel();
            if ($filter['nextEncodedFacets']) {
                $filter['nextEncodedFacetsURL'] = $this->updateQueryString(array(
                    'q' => $filter['nextEncodedFacets'],
                    'page' => null,
                ));
            } else {
                $filter['nextEncodedFacetsURL'] = $this->updateQueryString(array(
                    'q' => null,
                ));
            }
        }
        unset($filter);

        return $facetsArray;
    }
    protected function updateQueryString(array $extraParams = null)
    {
        $uriWithoutParams = explode('?', $_SERVER['REQUEST_URI'])[0];
        $url = Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$uriWithoutParams;
        $params = array();
        parse_str($_SERVER['QUERY_STRING'], $params);

        if (null !== $extraParams) {
            foreach ($extraParams as $key => $value) {
                if (null === $value) {
                    unset($params[$key]);
                } else {
                    $params[$key] = $value;
                }
            }
        }

        ksort($params);

        if (null !== $extraParams) {
            foreach ($params as $key => $param) {
                if (null === $param || '' === $param) {
                    unset($params[$key]);
                }
            }
        } else {
            $params = array();
        }

        $queryString = str_replace('%2F', '/', http_build_query($params, '', '&'));

        return $url.($queryString ? "?$queryString" : '');
    }
}
