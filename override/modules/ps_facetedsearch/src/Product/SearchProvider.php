<?php

class SearchProviderOverride extends SearchProvider {
    const FEATURE_COLOR = '46';

    private function prepareActiveFiltersForRender(ProductSearchContext $context, ProductSearchResult $result)
    {
        $facetCollection = $result->getFacetCollection();

        // not all search providers generate menus
        if (empty($facetCollection)) {
            return null;
        }

        $facetsVar = array_map(
            [$this, 'prepareFacetForTemplate'],
            $facetCollection->getFacets()
        );

        $displayedFacets = [];
        $activeFilters = [];
        
        foreach ($facetsVar as $idx => $facet) {

            if(isset($facet['properties']['id_feature']) && 
                $facet['properties']['id_feature'] == SELF::FEATURE_COLOR) {

                usort($facet['filters'], function($a, $b) {
                    return $a['value'] <=> $b['value'];
                });
                
            }

            // Remove undisplayed facets
            if (!empty($facet['displayed'])) {
                $displayedFacets[] = $facet;
            }

            // Check if a filter is active
            foreach ($facet['filters'] as $filter) {
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }
        
        return [
            $activeFilters,
            $displayedFacets,
            $facetsVar,
        ];
    }

}