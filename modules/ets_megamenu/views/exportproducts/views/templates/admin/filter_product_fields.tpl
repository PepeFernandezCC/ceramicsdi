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
<div class="filter_product_fields_block">
    <div class="header_filter_product">
        {l s='Customize the required fields or add your own' mod='exportproducts'}
        <a href="https://faq.myprestamodules.com/product-catalog-csv-excel-xml-export-pro/how-to-create-google-merchant-center-feed-file-.html" target="_blank" id="google_merchant_center_link"><i class="mic-question-circle-solid-2"></i>{l s='Learn how to work with Google Merchant Center' mod='exportproducts'}<i class="mic-solid_external-link-alt"></i></a>
    </div>
    <div class="main_block_filter_product">
        <div class="main_block_with_fields">
            <div class="product_fields_tabs">
                <ul class="product_field_tabs_list">
                    {foreach  $product_fields as $key => $tab}
                        <li class="item_product_field_tab {if $key == 0}active{/if}"
                            data-tab="{$tab['tab']|escape:'htmlall':'UTF-8'}">{$tab['name']|escape:'htmlall':'UTF-8'}</li>
                    {/foreach}
                </ul>
            </div>
            <div class="product_fields_tabs_content">
                <div class="product_fields_header">
                    <div class="product_fields_header_search">
                        <div class="product_fields_search">
                            <input type="text" class="search_product_fields"
                                   placeholder="{l s='Search' mod='exportproducts'}">
                            <i class="mic-search-solid"></i>
                        </div>
                        <div class="product_fields_header_navigations">
                            <a class="add_all_fields">{l s='Add all fields' mod='exportproducts'}</a>
                        </div>
                        <div class="clear_both"></div>
                    </div>
                </div>
                <div class="product_fields_scroll_block">
                    <div class="product_fields_scroll">
                        {foreach  $product_fields as $k => $tab_data}
                            <ul data-tab="{$tab_data['tab']|escape:'htmlall':'UTF-8'}"
                                class="content_fields_tab {$tab_data['tab']|escape:'htmlall':'UTF-8'}Content  {if $k == 0}active{/if}  ">
                                {foreach  $tab_data['fields'] as $field}
                                    <li class="item_product_export_field {$tab_data['tab']|escape:'htmlall':'UTF-8'}{$field['id']|escape:'htmlall':'UTF-8'} {if !empty($field['gmf_id'])}gmf-field{/if}"
                                        data-name="{$field['name']|escape:'htmlall':'UTF-8'}"
                                        data-value="{$field['id']|escape:'htmlall':'UTF-8'}"
                                        data-gmf-id="{$field['gmf_id']|escape:'htmlall':'UTF-8'}"
                                        data-gmf-doc-link="{$field['gmf_doc_link']|escape:'htmlall':'UTF-8'}"
                                        data-tab="{$tab_data['tab']|escape:'htmlall':'UTF-8'}">
                                        <span class="product_field_name">{$field['name']|escape:'htmlall':'UTF-8'}</span>
                                        {if !empty($field['gmf_id'])}<a href="{$field['gmf_doc_link']}" class="gmf-label" target="_blank">{$field['gmf_id']|escape:'htmlall':'UTF-8'}<i class="mic-solid_external-link-alt"></i></a>{/if}
                                        <a class="select_export_field"><i class="mic-check-mark"></i></a>
                                    </li>
                                {/foreach}
                            </ul>
                        {/foreach}
                    </div>
                </div>
            </div>
            <div class="clear_both"></div>
        </div>
        <div class="block_with_selected_fields">
            <div class="selected_export_fields {if isset($selected_export_fields) && $selected_export_fields}active{/if}">
                <div class="label_selected_export_fields">
                    {l s='SELECTED FIELDS TO EXPORTING:' mod='exportproducts'}
                    <a class="remove_all_fields">{l s='Remove all fields' mod='exportproducts'}</a>
                    <div class="search_selected_fields_block">

                        <input type="text" class="search_selected_fields"
                               placeholder="{l s='Search' mod='exportproducts'}">
                        <i class="mic-search-solid"></i>

                    </div>

                </div>

                <div class="selected_export_fields_scroll">
                    <ul class="selected_export_fields_list">
                        {if isset($selected_export_fields) && $selected_export_fields}
                            {foreach $selected_export_fields as $k => $field}
                                <li class='selected_export_field {$field['field']|escape:'htmlall':'UTF-8'}{$field['tab']|escape:'htmlall':'UTF-8'}'
                                    data-name='{$field['name']|escape:'htmlall':'UTF-8'}'
                                    data-value='{$field['field']|escape:'htmlall':'UTF-8'}'
                                    data-gmf-id="{$field['gmf_id']|escape:'htmlall':'UTF-8'}"
                                    data-gmf-doc-link="{$field['gmf_doc_link']|escape:'htmlall':'UTF-8'}"
                                    data-tab='{$field['tab']|escape:'htmlall':'UTF-8'}'
                                    data-default-value='{$field['default_value']|escape:'htmlall':'UTF-8'}'
                                    {if isset($field['line']) && $field['line']}
                                        data-line='{$field['line']|escape:'htmlall':'UTF-8'}'
                                    {/if}
                                    {if isset($field['condition_field']) && $field['condition_field']}
                                        data-condition-field='{$field['condition_field']|escape:'htmlall':'UTF-8'}'
                                    {/if}
                                    {if isset($field['condition']) && $field['condition']}
                                        data-condition='{$field['condition']|escape:'htmlall':'UTF-8'}'
                                    {/if}
                                    {if isset($field['condition_value']) && $field['condition_value']}
                                        data-condition-value='{$field['condition_value']|escape:'htmlall':'UTF-8'}'
                                    {/if}

                                    {if isset($field['formula_type']) && $field['formula_type']}
                                        data-formula-type='{$field['formula_type']|escape:'htmlall':'UTF-8'}'
                                    {/if}

                                    {if isset($field['format_as_price']) && $field['format_as_price']}
                                        data-format-as-price='{$field['format_as_price']|escape:'htmlall':'UTF-8'}'
                                    {/if}
                                >
                                    <div class='id_field_column'><span class='id_export_field'
                                                                       data-id='{($k+1)|escape:'htmlall':'UTF-8'}'>{($k+1)|escape:'htmlall':'UTF-8'}</span>
                                    </div>
                                    <div class='move_export_column'><span><i class='mic-arrows-alt-v-solid'></i></span>
                                    </div>
                                    <div class='export_field_name'>
                                        <span class='product_export_field_name'>
                                            {$field['name']|escape:'htmlall':'UTF-8'}
                                            {if !empty($field['gmf_id'])}<a href="{$field['gmf_doc_link']}" class="gmf-label" target="_blank">{$field['gmf_id']|escape:'htmlall':'UTF-8'}<i class="mic-solid_external-link-alt"></i></a>{/if}
                                        </span>
                                        <span class='product_export_field_default_value'>{$field['default_value_label']|escape:'htmlall':'UTF-8'}</span>
                                        <input class='change_name_field'
                                               value='{$field['name']|escape:'htmlall':'UTF-8'}'>
                                    </div>
                                    <div class='edit_export_field'><span class='edit_field'><i
                                                    class='mic-cogs-solid'></i></span><span class='save_field'><i
                                                    class='mic-check-mark'></i></span></div>
                                    <div class='remove_export_field'><span><i class='mic-minus-circle-solid'></i></span>
                                    </div>
                                    <span class='clear_both'></span>
                                </li>
                            {/foreach}
                        {/if}
                    </ul>
                </div>

            </div>
            <div class="no_selected_fields {if !isset($selected_export_fields) || !$selected_export_fields}active{/if}">
                <div class="image_content"><img src="{$img_folder|escape:'htmlall':'UTF-8'}svg/no_data_loup.svg"></div>
                <div class="descr_content">{l s='There is no fields filtered. Add the first one from the list above, or create a static or custom row with custom formulas.' mod='exportproducts'}</div>
            </div>
            <div class="static_custom_row">
                <div class="static_row_button">
                    <a data-name="{l s='Static Extra field' mod='exportproducts'}" data-value="static_field"
                       data-tab="staticTab" class="static_custom static_row"><i
                                class="mic-plus-circle-solid"></i>{l s='Add Static Field' mod='exportproducts'}</a>
                </div>
                <div class="custom_row_button">
                    <a data-name="{l s='Custom Extra field' mod='exportproducts'}" data-value="extra_field"
                       data-tab="staticTab" class="static_custom custom_row"><i
                                class="mic-plus-circle-solid"></i>{l s='Add Custom Field' mod='exportproducts'}</a>
                </div>
                <div class="clear_both"></div>
            </div>
        </div>
    </div>
    {include file="{$path_tpl|escape:'htmlall':'UTF-8'}buttons_block.tpl"}
</div>