{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{if $displayedFacets|count}
    <div id="search_filters" style="border-top: none !important; border-bottom: 1px solid">


        {assign var="borderTop" value=false}
        {assign var="areaArray" value=[12, 13, 16, 37, 38]}
        {assign var="colorArray" value=[41, 42, 43, 47, 44, 77, 78, 79, 83, 84, 85, 86]} 
        {assign var="subCategories" value=Category::getSubCategoriesArray($category.id, $language.id)} 

        {if $subCategories|@count > 0}
            {if in_array($category.id, $areaArray) 
                || in_array($category.id_parent, $areaArray)
                || in_array($category.id, $colorArray) 
                || in_array($category.id_parent, $colorArray)}

                <button class="accordion" data-label="subcategories" style="border-top: none">
                    {l s='Subcategories' d='Shop.Theme.Global'}
                </button>
                <section class="facet clearfix">
                    <ul id="subcategories">
                        {foreach $subCategories as $subCategory}
                            {if Category::categoryProductsCountById($subCategory.id_category) > 0}
                                <li class="facet-label">
                                    <a href="{$link->getCategoryLink($subCategory.id_category|intval)}" style="{if $category.id == $subCategory.id_category}font-weight:bold{/if}">{$subCategory.name}</a>
                                    {*<span data-filter="{$link->getCategoryLink($subCategory.id_category|intval)}" class="js-ofuscado-enlace" style="{if $category.id == $subCategory.id_category}font-weight:bold{/if}">{$subCategory.name}</span>*}
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                </section>

                {assign var="borderTop" value=true}
                
            {/if}
        {/if}

        {foreach from=$displayedFacets item="facet" name="facetLoop"}

            {assign var="centerColors" value=false}
            {if isset($facet.properties.id_feature) && $facet.properties.id_feature == '46'}
                {assign var="centerColors" value=true}
            {/if}

            {if $facet.label !== 'Categorías' || $category.id == $CATEGORY_INSTALACION_Y_MONTAJE_ID || $category.id == $CATEGORY_OTROS_MATERIALES_ID}
                {if !isset($facet.properties.id_feature) || 
                    $category.id == $CATEGORY_CERAMICA_ID || 
                    (isset($facet.properties.id_feature) && $facet.properties.id_feature !== $FEATURE_TIPO_ESTANCIA_ID)}
                    <button 
                        class="accordion" 
                        data-label="{$facet.label}"
                        {if !$borderTop}style="border-top: none"{/if}
                        >
                                {$facet.label} 
                    </button>
                    {assign var="borderTop" value=true}
                    <section class="facet clearfix">
                        {assign var=_expand_id value=10|mt_rand:100000}
                        {assign var=_collapse value=true}
                        {foreach from=$facet.filters item="filter"}
                            {if $filter.active}{assign var=_collapse value=false}{/if}
                        {/foreach}

                        {if in_array($facet.widgetType, ['radio', 'checkbox'])}
                            {block name='facet_item_other'}
                                <ul id="facet_{$_expand_id}" {if $centerColors}style="text-align:center"{/if}>

                                    {foreach from=$facet.filters key=filter_key item="filter"}
                                        {if $facet.label === 'Categorías'}
                                            {$mostrarFacet = !$filter.value|in_array:$FILTER_ESTILO && !$filter.value|in_array:$FILTER_ASPECTO}
                                        {else}
                                            {$mostrarFacet = true}
                                        {/if}
                                        {if $mostrarFacet || ($facet.label === 'Estilo' || $facet.label === 'Aspecto')}
                                            {if !$filter.displayed}
                                                {continue}
                                            {/if}

                                            {* PLANATEC *}

                                            {assign var="extraClass" value=""}
                                            {assign var="label" value="`$filter.label`"}

                                            {if isset($facet.properties.id_feature) && $facet.properties.id_feature == $FEATURE_FORMATO_ID }
                                                {if $filter.value|array_key_exists:$imgFormatos}
                                                    {assign var="extraClass" value="facet-image"}
                                                    {assign var="label" value="<img src='`$imgFormatos[$filter.value]`' height='25' loading='lazy' alt='format id: `$facet.properties.id_feature`'>"}
                                                {/if}
                                            {/if}

                                            
                                            {if isset($facet.properties.id_feature) && $facet.properties.id_feature == $FEATURE_COLOR }
                                                {if $filter.value|array_key_exists:$imgFormatos}
                                                    {assign var="extraClass" value="facet-colors"}
                                                    {assign var="label" value="<img src='`$imgFormatos[$filter.value]`' height='25' loading='lazy' alt='color id:`$facet.properties.id_feature`'>"}
                                                {/if}
                                            {/if}

                                            {* END PLANATEC *}
                                            <li class="{$extraClass} {if isset($filter.properties.color)}list-color{elseif isset($filter.properties.texture)}list-texture{/if}">
                                                <label class="facet-label{if $filter.active} active {/if}" for="facet_input_{$_expand_id}_{$filter_key}">
                                                    {if $facet.multipleSelectionAllowed}
                                                        <span class="custom-checkbox">
                                                            <input
                                                                    id="facet_input_{$_expand_id}_{$filter_key}"
                                                                    data-search-url="{$filter.nextEncodedFacetsURL}"
                                                                    type="checkbox"
                                                                    {if $filter.active }checked{/if}
                                                            >
                                                                {if isset($filter.properties.color)}
                                                                    <span class="color" style="background-color:{$filter.properties.color}"></span>
                                                                {elseif isset($filter.properties.texture)}
                                                                    <span class="color texture" style="background-image:url({$filter.properties.texture})"></span>  
                                                                {else}
                                                                    <span {if !$js_enabled} class="ps-shown-by-js" {/if}>
                                                                        <i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i>
                                                                    </span>
                                                                {/if}
                                                        </span>
                                                    {else}
                                                        <span class="custom-radio">
                                                            <input
                                                                    id="facet_input_{$_expand_id}_{$filter_key}"
                                                                    data-search-url="{$filter.nextEncodedFacetsURL}"
                                                                    type="radio"
                                                                    name="filter {$facet.label}"
                                                                    {if $filter.active }checked{/if}
                                                            >
                                                            <span {if !$js_enabled} class="ps-shown-by-js" {/if}></span>
                                                        </span>
                                                    {/if}


                                                
                                                    <span data-filter="{$filter.nextEncodedFacetsURL}" class="js-ofuscado-enlace _gray-darker search-link js-search-link {$extraClass}">
                                                        {$label nofilter}

                                                        {if $filter.magnitude and $show_quantities}
                                                            <span class="magnitude">({$filter.magnitude})</span>
                                                        {/if}
                                                    </span>
                                                  
                                                </label>
                                            </li>
                                        {/if}
                                    {/foreach}
                                </ul>
                            {/block}

                        {elseif $facet.widgetType == 'dropdown'}
                            {block name='facet_item_dropdown'}
                                <ul id="facet_{$_expand_id}">
                                    <li>
                                        <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown">
                                            <a class="select-title" rel="nofollow" data-toggle="dropdown"
                                               aria-haspopup="true"
                                               aria-expanded="false">
                                                {$active_found = false}
                                                <span>
                      {foreach from=$facet.filters item="filter"}
                          {if $filter.active}
                              {$filter.label}
                              {if $filter.magnitude and $show_quantities}
                                  ({$filter.magnitude})
                              {/if}
                              {$active_found = true}
                          {/if}
                      {/foreach}
                                                    {if !$active_found}
                                                        {l s='(no filter)' d='Shop.Theme.Global'}
                                                    {/if}
                        </span>
                                                <i class="material-icons float-xs-right">&#xE5C5;</i>
                                            </a>
                                            <div class="dropdown-menu">
                                                {foreach from=$facet.filters item="filter"}
                                                    {if !$filter.active}
                                                        {* <a rel="nofollow" href="{$filter.nextEncodedFacetsURL}" class="select-list js-search-link"> *}
                                                        <span data-filter="{$filter.nextEncodedFacetsURL}" class="js-ofuscado-enlace select-list js-search-link">
                                                            {$filter.label}
                                                            {if $filter.magnitude and $show_quantities}
                                                                ({$filter.magnitude})
                                                            {/if}
                                                        </span>
                                                        {*</a>*}
                                                    {/if}
                                                {/foreach}
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            {/block}

                        {elseif $facet.widgetType == 'slider'}
                            {block name='facet_item_slider'}
                                {foreach from=$facet.filters item="filter"}
                                    <ul id="facet_{$_expand_id}"
                                        class="faceted-slider {* PLANATEC collapse{if !$_collapse} in{/if} *}"
                                        data-slider-min="{$facet.properties.min}"
                                        data-slider-max="{$facet.properties.max}"
                                        data-slider-id="{$_expand_id}"
                                        data-slider-values="{$filter.value|@json_encode}"
                                        data-slider-unit="{$facet.properties.unit}"
                                        data-slider-label="{$facet.label}"
                                        data-slider-specifications="{$facet.properties.specifications|@json_encode}"
                                        data-slider-encoded-url="{$filter.nextEncodedFacetsURL}"
                                    >
                                        <li>
                                            <p id="facet_label_{$_expand_id}">
                                                {$filter.label}
                                            </p>

                                            <div id="slider-range_{$_expand_id}"></div>
                                        </li>
                                    </ul>
                                {/foreach}
                            {/block}
                        {/if}
                    </section>
                {/if}
            {/if}
        {/foreach}
    </div> 
       
{/if}
