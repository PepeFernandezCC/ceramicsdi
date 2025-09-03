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

<div data-id="{$count_conditions|escape:'htmlall':'UTF-8'}" class="extra_fields_row extra_fields_row_{$count_conditions|escape:'htmlall':'UTF-8'} extra_field_item_condition">
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
                    <input type="radio" class="switch-input" name="format_as_price_{$count_conditions|escape:'htmlall':'UTF-8'}" value="1"
                           id="switch-format-as-price-yes-{$count_conditions|escape:'htmlall':'UTF-8'}" >
                    <label for="switch-format-as-price-yes-{$count_conditions|escape:'htmlall':'UTF-8'}"
                           class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                    <input type="radio" class="switch-input" name="format_as_price_{$count_conditions|escape:'htmlall':'UTF-8'}" value="0"
                           id="switch-format-as-price-no-{$count_conditions|escape:'htmlall':'UTF-8'}"  checked>
                    <label for="switch-format-as-price-no-{$count_conditions|escape:'htmlall':'UTF-8'}"
                           class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                    <span class="switch-selection"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="close_extra_field_row"><i class="mic-times-solid"></i></div>
</div>