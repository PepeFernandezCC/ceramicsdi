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
<div class="extra_field_form">
    <div class="close_extra_field_form"><i class="mic-times-solid"></i></div>
    <div class="extra_field_form_header">{l s='Add Custom Extra Field' mod='exportproducts'}</div>
    <div class="extra_field_form_descr">{l s='Manage values and extra fields using custom formulas' mod='exportproducts'}</div>
    <div class="extra_field_form_content">
        {if isset($type) && $type == 'extra'}
            <div class="extra_fields_block">
                <div class="extra_fields_row extra_fields_row_first">
                    <div class="extra_field_row_item" id="field_to_which_condition_is_applied_container">
                        <div class="static_field_row_label">{l s='Field:' mod='exportproducts'}</div>
                        <div class="static_field_row_input">
                            <div class="mpm-fpe-select-wrapper search-enabled">
                                <select class="condition_field">
                                    {foreach  $condition_fields as $value}
                                        <option {if isset($condition_field) && $condition_field == $value['id'] } selected {/if}
                                                value="{$value['id']|escape:'htmlall':'UTF-8'}">{$value['name']|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="extra_field_row_item">
                        <div class="static_field_row_label">{l s='Field Name:' mod='exportproducts'}</div>
                        <div class="static_field_row_input"><input type="text" class="static_field_name"
                                                                   value="{if isset($name_field) && $name_field}{$name_field|escape:'htmlall':'UTF-8'}{/if}">
                        </div>
                    </div>

                    {if $gmf_attributes}
                        <div class="extra_field_row_item" id="custom_gmf_attribute_form_group">
                            <div class="static_field_row_label">{l s='Google Merchant Feed attribute:' mod='exportproducts'}</div>
                            <div class="static_field_row_input">
                                <div class="mpm-fpe-select-wrapper search-enabled">
                                    <select class="gmf-attribute">
                                        <option value=""
                                                data-gmf-doc-link="">{l s='None' mod='exportproducts'}</option>
                                        {foreach $gmf_attributes as $gmf_attribute}
                                            <option value="{$gmf_attribute['id']|escape:'htmlall':'UTF-8'}"
                                                    data-gmf-doc-link="{$gmf_attribute['doc_link']|escape:'htmlall':'UTF-8'}"
                                                    {if $selected_gmf_attribute == $gmf_attribute['id']}selected{/if}>{$gmf_attribute['id']|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>


                {if isset($condition) && $condition}
                    {foreach  $condition as $k => $cond}
                        <div data-id="{$k+1|escape:'htmlall':'UTF-8'}"
                             class="extra_fields_row extra_fields_row_{$k+1|escape:'htmlall':'UTF-8'} extra_field_item_condition">
                            <div class="extra_field_row_item condition-input-group">
                                <div class="static_field_row_label">{l s='Condition:' mod='exportproducts'}</div>
                                <div class="static_field_row_input">
                                    <div class="mpm-fpe-select-wrapper">
                                        <select class="condition">
                                            {foreach  $conditions as $val}
                                                <option {if $val['value'] == $cond } selected {/if}
                                                        value="{$val['value']|escape:'htmlall':'UTF-8'}">{$val['name']|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="extra_field_row_item condition-value-input-group {if in_array($cond, [7,8,10])}hidden{/if}">
                                <div class="static_field_row_label">{l s='Condition value:' mod='exportproducts'}</div>
                                <div class="static_field_row_input">
                                    <input type="text" class="condition_value"
                                           value="{if isset($condition_value[$k])}{$condition_value[$k]|escape:'htmlall':'UTF-8'}{/if}">
                                </div>
                            </div>
                            <div class="extra_field_row_item formula-type-input-group">
                                <div class="static_field_row_label">{l s='Formula Type:' mod='exportproducts'}</div>
                                <div class="static_field_row_input">
                                    <div class="mpm-fpe-select-wrapper">
                                        <select class="extra_field_formula_type">
                                            <option value="1"
                                                    {if $formula_type[$k] == 1}selected{/if}>{l s='String' mod='exportproducts'}</option>
                                            <option value="2"
                                                    {if $formula_type[$k] == 2}selected{/if}>{l s='Math Formula' mod='exportproducts'}</option>
                                            <option value="3"
                                                    {if $formula_type[$k] == 3}selected{/if}>{l s='Find And Replace' mod='exportproducts'}</option>
                                            <option value="4"
                                                    {if $formula_type[$k] == 4}selected{/if}>{l s='Truncate' mod='exportproducts'}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="extra_field_row_item formula-input-group">
                                <div class="static_field_row_label">{l s='Formula:' mod='exportproducts'}</div>
                                <div class="static_field_row_input">
                                    <input type="text" class="static_field_value" value="{if isset($value_default_field[$k]) && $value_default_field[$k]}{$value_default_field[$k]|escape:'htmlall':'UTF-8'}{/if}">
                                    <div class="find-and-replace-example">
                                        {l s='Formula should be in the following format:' mod='exportproducts'}<br>
                                        {l s='[product_field_name]=>search_value=>replace_value' mod='exportproducts'}
                                    </div>
                                </div>
                            </div>
                            <div class="extra_field_row_item format-as-price-input-group">
                                <div class="static_field_row_label">{l s='Format As Price:' mod='exportproducts'}</div>
                                <div class="static_field_row_input">
                                    <div class="switch_myprestamodules">
                                        <div class="switch_content">
                                            <input type="radio" class="switch-input" name="format_as_price_{$k+1|escape:'htmlall':'UTF-8'}" value="1"
                                                   id="switch-format-as-price-yes-{$k+1|escape:'htmlall':'UTF-8'}" {if (isset($format_as_price[$k]) && $format_as_price[$k] == 1)} checked{/if}>
                                            <label for="switch-format-as-price-yes-{$k+1|escape:'htmlall':'UTF-8'}"
                                                   class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                                            <input type="radio" class="switch-input" name="format_as_price_{$k+1|escape:'htmlall':'UTF-8'}" value="0"
                                                   id="switch-format-as-price-no-{$k+1|escape:'htmlall':'UTF-8'}" {if (isset($format_as_price[$k]) && $format_as_price[$k] == 0) || !isset($format_as_price[$k])}checked{/if}>
                                            <label for="switch-format-as-price-no-{$k+1|escape:'htmlall':'UTF-8'}"
                                                   class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                                            <span class="switch-selection"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {if $k>0}
                                <div class="close_extra_field_row"><i class="mic-times-solid"></i></div>
                            {/if}

                        </div>
                    {/foreach}

                {else}
                    <div data-id="1" class="extra_fields_row extra_fields_row_1 extra_field_item_condition">
                        <div class="extra_field_row_item condition-input-group">
                            <div class="static_field_row_label">{l s='Condition:' mod='exportproducts'}</div>
                            <div class="static_field_row_input">
                                <div class="mpm-fpe-select-wrapper">
                                    <select class="condition">
                                        {foreach  $conditions as $val}
                                            <option value="{$val['value']|escape:'htmlall':'UTF-8'}">{$val['name']|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="extra_field_row_item condition-value-input-group">
                            <div class="static_field_row_label">{l s='Condition value:' mod='exportproducts'}</div>
                            <div class="static_field_row_input"><input type="text" class="condition_value" value="">
                            </div>
                        </div>
                        <div class="extra_field_row_item formula-type-input-group">
                            <div class="static_field_row_label">{l s='Formula Type:' mod='exportproducts'}</div>
                            <div class="static_field_row_input">
                                <div class="mpm-fpe-select-wrapper">
                                    <select class="extra_field_formula_type">
                                        <option value="1">{l s='String' mod='exportproducts'}</option>
                                        <option value="2">{l s='Math Formula' mod='exportproducts'}</option>
                                        <option value="3">{l s='Find And Replace' mod='exportproducts'}</option>
                                        <option value="4">{l s='Truncate' mod='exportproducts'}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="extra_field_row_item formula-input-group">
                            <div class="static_field_row_label">{l s='Formula:' mod='exportproducts'}</div>
                            <div class="static_field_row_input">
                                <input type="text" class="static_field_value" value="">
                                <div class="find-and-replace-example">
                                    {l s='Formula should be in the following format:' mod='exportproducts'}<br>
                                    {l s='[product_field_name]=>search_value=>replace_value' mod='exportproducts'}
                                </div>
                            </div>
                        </div>
                        <div class="extra_field_row_item format-as-price-input-group">
                            <div class="static_field_row_label">{l s='Format As Price:' mod='exportproducts'}</div>
                            <div class="static_field_row_input">
                                <div class="switch_myprestamodules">
                                    <div class="switch_content">
                                        <input type="radio" class="switch-input" name="format_as_price_1" value="1"
                                               id="switch-format-as-price-yes-1" >
                                        <label for="switch-format-as-price-yes-1"
                                               class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                                        <input type="radio" class="switch-input" name="format_as_price_1" value="0"
                                               id="switch-format-as-price-no-1" checked>
                                        <label for="switch-format-as-price-no-1"
                                               class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                                        <span class="switch-selection"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}


                <div class="add_more_condition_button">
                    <a data-count="{if isset($condition) && $condition}{count($condition)|escape:'htmlall':'UTF-8'}{else}1{/if}"
                       class="add_more_condition">{l s='Add' mod='exportproducts'}</a>
                </div>

                <div class="example_all_fields">
                    <div class="example_all_fields_header">
                        <div class="static_field_row_label">{l s='Available Fields List:' mod='exportproducts'}</div>
                        <div class="search_condition_fields_block">
                            <input type="text" class="search_condition_fields"
                                   placeholder="{l s='Search' mod='exportproducts'}">
                            <i class="mic-search-solid"></i>
                        </div>
                    </div>

                    <div class="example_all_fields_list">
                        {foreach  $condition_fields as $field}
                            <div class="example_one_field">
                                <a class="copy_field_name" data-field="[{$field['id']|escape:'htmlall':'UTF-8'}]"
                                   data-field-name="[{$field['name']|escape:'htmlall':'UTF-8'}]">
                                    [{$field['name']|escape:'htmlall':'UTF-8'}]
                                </a>
                            </div>
                        {/foreach}
                    </div>
                </div>

            </div>
        {else}
            <div class="static_fields_block">
                <div class="static_fields_row">
                    <div class="static_fields_row_left">
                        <div class="static_field_row_label">{l s='Field Name:' mod='exportproducts'}</div>
                        <div class="static_field_row_input"><input type="text" class="static_field_name"
                                                                   value="{if isset($name_field) && $name_field}{$name_field|escape:'htmlall':'UTF-8'}{/if}">
                        </div>
                    </div>
                    <div class="static_fields_row_right">
                        <div class="static_field_row_label">{l s='Field Value:' mod='exportproducts'}</div>
                        <div class="static_field_row_input"><input type="text" class="static_field_value default_static_field_value"
                                                                   value="{if isset($value_default_field) && $value_default_field}{$value_default_field|escape:'htmlall':'UTF-8'}{/if}">
                        </div>
                    </div>

                </div>
            </div>
        {/if}
        <div class="extra_field_form_button_block"><a data-field="{$custom_field|escape:'htmlall':'UTF-8'}"
                                                      data-type="{$type|escape:'htmlall':'UTF-8'}"
                                                      class="extra_field_button">{l s='Create Custom Field' mod='exportproducts'}</a>
        </div>
    </div>
</div>