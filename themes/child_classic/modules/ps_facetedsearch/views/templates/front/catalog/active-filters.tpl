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
<section id="js-active-search-filters" class="{if $activeFilters|count}active_filters{else}hide{/if}">
    {block name='active_filters_title'}
        <p class="h6 {if $activeFilters|count}active-filter-title{else}hidden-xs-up{/if}">{l s='Active filters' d='Shop.Theme.Global'}</p>
    {/block}

    {if $activeFilters|count}
        <ul>
            {foreach from=$activeFilters item="filter"}
                {block name='active_filters_item'}
                    <li class="filter-block">
                        {l s='%1$s:' d='Shop.Theme.Catalog' sprintf=[$filter.facetLabel]}
                        {$filter.label}
                        <a class="js-search-link" href="{$filter.nextEncodedFacetsURL}"><i class="material-icons close">&#xE5CD;</i></a>
                    </li>
                {/block}
            {/foreach}
            <li class="filter-block my-filter-block-clear-all">
                <div id="_desktop_search_filters_clear_all" class="clear-all-wrapper">
                    <button data-search-url="{$clear_all_link}"
                            class="btn js-search-filters-clear-all">
                        {l s='Clear all' d='Shop.Theme.Actions'}
                        <i class="material-icons close">&#xE5CD;</i>
                    </button>
                </div>
            </li>
        </ul>
    {/if}
</section>
