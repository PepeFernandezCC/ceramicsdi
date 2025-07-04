{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{nocache}
<div id="search_filters">

  {block name='facets_title'}
    <h4 class="text-uppercase h6">{l s='Filter By' d='Shop.Theme.Actions'}</h4>
  {/block}
  <div class="filter_block_content block_content">
    {block name='facets_clearall_button'}
      <div id="_desktop_search_filters_clear_all" class="hidden-md-down clear-all-wrapper">
        <button data-search-url="{$clear_all_link}" class="btn btn-tertiary js-search-filters-clear-all">
          <i class="fa fa-times"></i>
          {l s='Clear all' d='Shop.Theme.Actions'}
        </button>
      </div>
    {/block}

    {foreach from=$facets item="facet"}
      {if $facet.displayed}
        <section class="facet clearfix">
          <h1 class="h6 facet-title hidden-lg-up hidden-md-down">{$facet.label}</h1>
          {assign var=_expand_id value=10|mt_rand:100000}
          {assign var=_collapse value=false}
          {foreach from=$facet.filters item="filter"}
            {if $filter.active}{assign var=_collapse value=false}{/if}
          {/foreach}
          <div class="title " data-target="#facet_{$_expand_id}" data-toggle="collapse"{if !$_collapse} aria-expanded="true"{/if}>
            <h1 class="h6 facet-title">{$facet.label}</h1>
            <span class="pull-xs-right">
              <span class="navbar-toggler collapse-icons">
                <i class="fa fa-angle-down add" aria-hidden="true"></i>
                <i class="fa fa-angle-up remove" aria-hidden="true"></i>
              </span>
            </span>
          </div>

          {if $facet.widgetType !== 'dropdown'}

            {block name='facet_item_other'}
            {if ((isset($price_range_slider) && $price_range_slider && $facet.type=='price') || (isset($weight_range_slider) && $weight_range_slider && $facet.type=='weight') || $facet.widgetType=='rangeslider') &&  count($facet.filters)}
              <div class="st-range-box collapse{if !$_collapse} in{/if} st-noUi-style-{$range_style}" id="facet_{$_expand_id}">
                <div class="st-range-top st-range-bar {if $with_inputs && $facet.widgetType!='rangeslider'} with_inputs {elseif $disable_range_text} space_for_tooltips {/if}">
                  {if $with_inputs && $facet.widgetType!='rangeslider'}
                  <input class="st_lower_input form-control" />
                  {if !$vertical}<div class="value-split">-</div><input class="st_upper_input form-control" />{/if}
                  {elseif !$disable_range_text}
                  <span class="value-lower"></span>
                  <span class="value-split">-</span>
                  <span class="value-upper"></span>
                  {/if}
                </div>
                <div class="st_range_inner">
                <div class="st-range" data-jiazhong="{if $facet.widgetType=='rangeslider'}rangeslider{else}{$facet.type}{/if}" data-url="{$facet['properties']['url']}" data-min="{$facet['properties']['min']}" data-max="{$facet['properties']['max']}" data-lower="{if isset($facet['properties']['lower'])}{$facet['properties']['lower']}{else}{$facet['properties']['min']}{/if}" data-upper="{if isset($facet['properties']['upper'])}{$facet['properties']['upper']}{else}{$facet['properties']['max']}{/if}" data-values="{if isset($facet['properties']['values'])}{','|implode:$facet['properties']['values']}{/if}" data-prefix="{if isset($facet['properties']['prefix'])}{$facet['properties']['prefix']}{/if}" data-suffix="{if isset($facet['properties']['suffix'])}{$facet['properties']['suffix']}{/if}"></div>
                </div>
                <div class="st-range-bottom st-range-bar {if $with_inputs && $facet.widgetType!='rangeslider'} with_inputs {/if}">
                  {if $with_inputs && $facet.widgetType!='rangeslider' && $vertical}
                  <input class="st_upper_input form-control" />
                  {/if}
                </div>
              </div>
              {else}
              <ul id="facet_{$_expand_id}" class="collapse{if !$_collapse} in{/if}">
                {foreach from=$facet.filters key=filter_key item="filter"}
                  {if $filter.displayed}
                    <li class="{if isset($facet['properties']['filter_show_limit']) && $facet['properties']['filter_show_limit'] && $filter@iteration>$facet['properties']['filter_show_limit']} st_display_none {/if}">
                      <label class="facet-label{if $filter.active} active {/if}" for="facet_input_{$_expand_id}_{$filter_key}">
                        {if $facet.multipleSelectionAllowed}
                          <span class="custom-checkbox">
                            <input
                              id="facet_input_{$_expand_id}_{$filter_key}"
                              data-search-url="{$filter.nextEncodedFacetsURL}"
                              type="checkbox"
                              {if $filter.active } checked {/if}
                            >
                            {if isset($filter.properties.color)}
                              <span class="color" style="background-color:{$filter.properties.color}"></span>
                            {elseif isset($filter.properties.texture)}
                              <span class="color texture" style="background-image:url({$filter.properties.texture})"></span>
                            {else}
                              <span {if !$js_enabled} class="ps-shown-by-js" {/if}><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                            {/if}
                          </span>
                        {else}
                          <span class="custom-checkbox">
                            <input
                              id="facet_input_{$_expand_id}_{$filter_key}"
                              data-search-url="{$filter.nextEncodedFacetsURL}"
                              type="radio"
                              name="filter {$facet.label}"
                              {if $filter.active } checked {/if}
                            >
                            <span {if !$js_enabled} class="ps-shown-by-js" {/if}></span>
                          </span>
                        {/if}

                        <a
                          href="{$filter.nextEncodedFacetsURL}"
                          class="_gray-darker search-link js-search-link"
                          rel="nofollow"
                          >
                          {$filter.label}
                          {if $filter.magnitude && $ps_layered_show_qties}
                            <span class="magnitude">({$filter.magnitude})</span>
                          {/if}
                        </a>
                      </label>
                    </li>
                  {/if}
                {/foreach}
              </ul>
              {/if}
            {/block}

          {else}

            {block name='facet_item_dropdown'}
              <ul id="facet_{$_expand_id}" class="facet_dropdown collapse{if !$_collapse} in{/if}">
                <li>
                  <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown">
                    <a class="select-title" rel="nofollow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      {$active_found = false}
                      <span>
                        {foreach from=$facet.filters item="filter"}
                        {if $filter.active}
                        {$filter.label}
                        {if $filter.magnitude}
                        ({$filter.magnitude})
                        {/if}
                        {$active_found = true}
                        {/if}
                        {/foreach}
                        {if !$active_found}
                        {l s='(no filter)' d='Shop.Theme.Global'}
                        {/if}
                      </span>
                      <i class="material-icons pull-xs-right">&#xE5C5;</i>
                    </a>
                    <div class="dropdown-menu">
                      {foreach from=$facet.filters item="filter"}
                        {if !$filter.active}
                          <a
                            rel="nofollow"
                            href="{$filter.nextEncodedFacetsURL}"
                            class="select-list {if isset($facet['properties']['filter_show_limit']) && $facet['properties']['filter_show_limit'] && $filter@iteration>$facet['properties']['filter_show_limit']} st_display_none {/if}"
                            >
                            {$filter.label}
                            {if $filter.magnitude && $ps_layered_show_qties}
                              ({$filter.magnitude})
                            {/if}
                          </a>
                        {/if}
                      {/foreach}
                    </div>
                  </div>
                </li>
              </ul>
            {/block}

          {/if}
        </section>
      {/if}
    {/foreach}
  </div>
</div>
{/nocache}