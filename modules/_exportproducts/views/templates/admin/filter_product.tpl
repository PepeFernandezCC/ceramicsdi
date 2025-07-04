{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="filter_product_block">
    <div class="header_filter_product">
        {l s='Column Filter (Filter by Products)' mod='exportproducts'}
    </div>
    <div class="main_block_filter_product">
        <div class="label_list_filter">{l s='Select columns from the list for filtering:' mod='exportproducts'}</div>
        <div class="select_filter">
            <i class="mic-tune select_filter_icon"></i>
            <span>{l s='Choose the option for filter:' mod='exportproducts'}</span>
            <i class="mic-chevron-down-solid select_filter_arrow"></i>
        </div>
        <div class="drop-down-menu">
            <div class="select_filter_list">
                {if isset($filter_fields) && $filter_fields}
                    <div class="search-filter-container">
                        <input type="text" placeholder="{l s='Search...' mod='exportproducts'}" class="search-filter">
                    </div>
                    {foreach  $filter_fields as $filter_field}
                        <div data-label="{$filter_field['name']|escape:'htmlall':'UTF-8'}"
                            data-id="{$filter_field['id']|escape:'htmlall':'UTF-8'}"
                            data-type="{$filter_field['type']|escape:'htmlall':'UTF-8'}" class="filter_field">
                            {$filter_field['name']|escape:'htmlall':'UTF-8'}
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>
        <div class="selected_filter">
            <div class="selected_filter_list">
                {if isset($saved_filters) && $saved_filters}
                    {$saved_filters|escape:'htmlall':'UTF-8'|unescape}
                {/if}
            </div>
            <div class="not_has_selected_filter  {if !isset($saved_filters) || !$saved_filters}active{/if}">
                <div class="image_content"><img src="{$img_folder|escape:'htmlall':'UTF-8'}svg/empty_box.svg"></div>
                <div class="descr_content">{l s='There is no product options filtered. Add the first one or click «Continue». Note, if no options selected, the module will export all features!' mod='exportproducts'}</div>
            </div>
        </div>
    </div>
    {include file="{$path_tpl|escape:'htmlall':'UTF-8'}buttons_block.tpl"}
</div>


