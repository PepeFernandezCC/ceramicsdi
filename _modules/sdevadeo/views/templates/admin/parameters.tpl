{**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 *}

{extends file=$smarty.const._PS_MODULE_DIR_|cat:$module->name|cat:'/views/templates/extends/admin/base.tpl'}

{block 'SdevAdeoVarsAssignments'}
    {assign var='sdevadeoAdminPage' value='parameters'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-cogs"></i>&nbsp;
    {l s='Parameters' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
<div class="divContenuMenu">
    <div class="conf alert alert-success hidden" id="params_ok">
        {l s='Parameters saved' mod='sdevadeo'}
    </div>
    <div class="conf alert alert-danger hidden" id="params_ko">
        {l s='Impossible to save parameters' mod='sdevadeo'}
    </div>

    {* TABS *}
    <ul id="navAdminConfig" class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#generals" aria-controls="generals" role="tab" data-toggle="tab">{l s='Generals' mod='sdevadeo'}</a></li>
        <li role="presentation"><a href="#products" aria-controls="products" role="tab" data-toggle="tab">{l s='Products' mod='sdevadeo'}</a></li>
        <li role="presentation"><a href="#orders" aria-controls="orders" role="tab" data-toggle="tab">{l s='Orders' mod='sdevadeo'}</a></li>
        <li role="presentation"><a href="#carriers" aria-controls="carriers" role="tab" data-toggle="tab">{l s='Carriers' mod='sdevadeo'}</a></li>
        <li role="presentation"><a href="#filters" aria-controls="filters" role="tab" data-toggle="tab">{l s='Filters' mod='sdevadeo'}</a></li>
    </ul>

    <div class="tab-content">
        {* GENERALS CONFIGURATION *}
        <div role="tabpanel" class="tab-pane active" id="generals">
            <div id="generals-notification">
            </div>
            {* DISCOUNT CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="test_mode">
                    {l s='Use of discounted prices' mod='sdevadeo'}
                </label>
                <div class="margin-form">
                    <div class="input-group">
                        <span class="switch prestashop-switch fixed-width-lg" id="discount-switch">
                            <input id="discount-switch_1" name="discount-switch" value="1"{if $discount == 1} checked{/if} type="radio" />
                            <label for="discount-switch_1" class="label-checkbox">{l s='Yes' mod='sdevadeo'}</label>

                            <input id="discount-switch_0" name="discount-switch" value="0"{if $discount == 0} checked{/if} type="radio" />
                            <label for="discount-switch_0" class="label-checkbox">{l s='No' mod='sdevadeo'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </fieldset>

            {* SALES CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="sales-switch">
                    {l s='Enable sales' mod='sdevadeo'}
                </label>
                <div class="margin-form">
                    <div class="input-group">
                        <span class="switch prestashop-switch fixed-width-lg" id="sales-switch">
                            <input id="sales-switch_1" name="sales-switch" value="1"{if $sales == 1} checked{/if} type="radio" />
                            <label for="sales-switch_1" class="label-checkbox">{l s='Yes' mod='sdevadeo'}</label>

                            <input id="sales-switch_0" name="sales-switch" value="0"{if $sales == 0} checked{/if} type="radio" />
                            <label for="sales-switch_0" class="label-checkbox">{l s='No' mod='sdevadeo'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </fieldset>

            {* DESCRIPTION CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="description_select">
                    {l s='Description field to use' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <select class="form-control" name="description_select" id="description_select">
                        {foreach $description_options as $option}
                            <option{if isset($description_value) && $description_value == $option['value']} selected="selected"{/if} value="{$option['value']}">{$option['text']}</option>
                        {/foreach}
                    </select>
                </div>
            </fieldset>

            {* AUTOMATIC ORDER VALIDATION CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="automatic-validation-switch">
                    {l s='Disable the automatic order validation' mod='sdevadeo'}
                </label>
                <div class="margin-form">
                    <div class="input-group">
                        <span class="switch prestashop-switch fixed-width-lg" id="automatic-validation-switch">
                            <input id="automatic-validation-switch_1" name="automatic-validation-switch" value="1"{if $automatic_validation == 1} checked{/if} type="radio" />
                            <label for="automatic-validation-switch_1" class="label-checkbox">{l s='Yes' mod='sdevadeo'}</label>

                            <input id="automatic-validation-switch_0" name="automatic-validation-switch" value="0"{if $automatic_validation == 0} checked{/if} type="radio" />
                            <label for="automatic-validation-switch_0" class="label-checkbox">{l s='No' mod='sdevadeo'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </fieldset>

            {* DISABLED CATEGORIES CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="disabled-categories-switch">
                    {l s='Display the disable categories' mod='sdevadeo'}
                </label>
                <div class="margin-form">
                    <div class="input-group">
                        <span class="switch prestashop-switch fixed-width-lg" id="disabled-categories-switch">
                            <input id="disabled-categories-switch_1" name="disabled-categories-switch" value="1"{if $disabled_categories == 1} checked{/if} type="radio" />
                            <label for="disabled-categories-switch_1" class="label-checkbox">{l s='Yes' mod='sdevadeo'}</label>

                            <input id="disabled-categories-switch_0" name="disabled-categories-switch" value="0"{if $disabled_categories == 0} checked{/if} type="radio" />
                            <label for="disabled-categories-switch_0" class="label-checkbox">{l s='No' mod='sdevadeo'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </fieldset>

            {* DISABLED PRODUCTS CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="disabled-products-switch">
                    {l s='Export the disable products' mod='sdevadeo'}
                </label>
                <div class="margin-form">
                    <div class="input-group">
                        <span class="switch prestashop-switch fixed-width-lg" id="disabled-products-switch">
                            <input id="disabled-products-switch_1" name="disabled-products-switch" value="1"{if $disabled_products == 1} checked{/if} type="radio" />
                            <label for="disabled-products-switch_1" class="label-checkbox">{l s='Yes' mod='sdevadeo'}</label>

                            <input id="disabled-products-switch_0" name="disabled-products-switch" value="0"{if $disabled_products == 0} checked{/if} type="radio" />
                            <label for="disabled-products-switch_0" class="label-checkbox">{l s='No' mod='sdevadeo'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </fieldset>

            {* COUNTRY OF SALES CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="enabled_countries">
                    {l s='Countries to enable :' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <select class="form-control" name="enabled_countries" id="enabled_countries" multiple>
                        {foreach ['FR', 'IT', 'ES', 'PT'] as $country}
                            <option{if isset($enabled_countries) && in_array($country, $enabled_countries)} selected="selected"{/if} value="{$country}">{$country}</option>
                        {/foreach}
                    </select>
                </div>
            </fieldset>

            {* SHIPPING COUNTRY *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="shipping_country">
                    {l s='Country of shipping :' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <select class="form-control" name="shipping_country" id="shipping_country">
                        {foreach $shipping_countries as $country}
                            <option{if isset($shipping_country) && $shipping_country == $country} selected="selected"{/if} value="{$country}">{$country}</option>
                        {/foreach}
                    </select>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button
                            type="submit"
                            class="btn btn-primary"
                            title="{l s='Save the general parameters.' mod='sdevadeo'}"
                            onclick="SDEVADEO.controller.admin.parameters.saveGenerals()"
                    >
                        <i class="icon-save"></i>&nbsp;
                        {l s='Save' mod='sdevadeo'}
                    </button>
                </div>
            </fieldset>
        </div>

        {* PRODUCTS CONFIGURATION *}
        <div role="tabpanel" class="tab-pane" id="products">
            <div id="products-notification">
            </div>
            {* NUMBER PER BURST CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="number_per_burst">
                    {l s='Catalog generation :' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <div class="input-group">
                        <input type="number" class="form-control" name="number_per_burst" id="number_per_burst" value="{$products}">
                        <div class="input-group-addon">{l s='products per burst' mod='sdevadeo'}</div>
                    </div>
                </div>
            </fieldset>

            {* NUMBER PER BURST CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="flow_type">
                    {l s='Type of flow generation used for CRON :' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <select class="form-control" name="flow_type" id="flow_type">
                        {foreach $flow_type['list'] as $type => $text}
                            <option{if $flow_type['selected'] == $type} selected{/if} value="{$type}">{$text}</option>
                        {/foreach}
                    </select>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="tax_mapping">
                    {l s='Taxes mapping' mod='sdevadeo'}
                </label>
                {foreach $mp_taxes as $tax}
                    <fieldset class="form-group col-sm-10 pull-right">
                        <label class="col-sm-2 control-label" for="tax_mapping_{$tax}">
                            {$tax}
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control" name="tax_mapping_{$tax}" id="tax_mapping_{$tax}">
                                <option selected value=''>{l s='Select the equivalent tax' mod='sdevadeo'}</option>
                                {foreach $cms_taxes as $cms_tax}
                                    <option {if array_key_exists($tax, $mapped_taxes) && $mapped_taxes[$tax] == $cms_tax['id_tax']}selected {/if}value="{$cms_tax['id_tax']}">{$cms_tax['name']}</option>
                                {/foreach}
                            </select>
                        </div>
                    </fieldset>
                {/foreach}
            </fieldset>

            <fieldset class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button
                            type="submit"
                            class="btn btn-primary"
                            title="{l s='Save the general parameters.' mod='sdevadeo'}"
                            onclick="SDEVADEO.controller.admin.parameters.saveProducts()"
                    >
                        <i class="icon-save"></i>&nbsp;
                        {l s='Save' mod='sdevadeo'}
                    </button>
                </div>
            </fieldset>
        </div>
        {* ORDER CONFIGURATION *}
        <div role="tabpanel" class="tab-pane" id="orders">
            <div id="orders-notification">
            </div>
            {* STATE FOR IMPORTED ORDERS CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="imported_state">
                    {l s='Imported orders state :' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <select class="form-control" name="imported_state" id="imported_state">
                        {foreach $order_states as $key => $option}
                            <option{if isset($imported_state) && $imported_state == $key} selected="selected"{/if} value="{$key}">{$option}</option>
                        {/foreach}
                    </select>
                </div>
            </fieldset>

            {* STATE OF SHIPPED ORDERS CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="shipped_state">
                    {l s='Shipped orders state :' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <select class="form-control" name="shipped_state" id="shipped_state" multiple>
                        {foreach $order_states as $key => $option}
                            <option{if isset($shipped_state) && in_array($key, $shipped_state)} selected="selected"{/if} value="{$key}">{$option}</option>
                        {/foreach}
                    </select>
                </div>
            </fieldset>

            {* SHIPMENT CRON CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="shipment-switch">
                    {l s='Synchronize shipments by CRON task :' mod='sdevadeo'}
                </label>
                <div class="margin-form">
                    <div class="input-group">
                        <span class="switch prestashop-switch fixed-width-lg" id="shipment-switch">
                            <input id="shipment-switch_1" name="shipment-switch" value="1"{if $cron_shipment == 1} checked{/if} type="radio" />
                            <label for="shipment-switch_1" class="label-checkbox">{l s='Yes' mod='sdevadeo'}</label>

                            <input id="shipment-switch_0" name="shipment-switch" value="0"{if $cron_shipment == 0} checked{/if} type="radio" />
                            <label for="shipment-switch_0" class="label-checkbox">{l s='No' mod='sdevadeo'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </fieldset>

            {* NUMBER PER BURST CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="last_shipment_cron">
                    {l s='Last execution of the cron task for shipping orders :' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <div class="input-group">
                        <input type="text" class="form-control" name="last_shipment_cron" id="last_shipment_cron" value="{$last_shipment_cron}">
                        <p class="profile-help">
                            {l s='Format : Y-m-d H:i:s' mod='sdevadeo'}
                        </p>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button
                            type="submit"
                            class="btn btn-primary"
                            title="{l s='Save the general parameters.' mod='sdevadeo'}"
                            onclick="SDEVADEO.controller.admin.parameters.saveOrders()"
                    >
                        <i class="icon-save"></i>&nbsp;
                        {l s='Save' mod='sdevadeo'}
                    </button>
                </div>
            </fieldset>
        </div>

        {* CARRIERS CONFIGURATION *}
        <div role="tabpanel" class="tab-pane" id="carriers">
            <div id="carriers-notification">
            </div>
            {* ADDITIONAL COST CONFIGURATION *}
            <fieldset class="form-group hidden">
                <label class="col-sm-2 control-label" for="additional_shipping">
                    {l s='Additional shipping cost :' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <div class="form-inline">
                        <input type="number" class="form-control" name="additional_shipping" id="additional_shipping" value="{$shipping_additional}">
                        <div class="input-group">
                            <button
                                    type="submit"
                                    class="btn btn-primary pull-right"
                                    title="{l s='Save the general parameters.' mod='sdevadeo'}"
                                    onclick="SDEVADEO.controller.admin.parameters.saveCarriers()"
                            >
                                <i class="icon-save"></i>&nbsp;
                                {l s='Save' mod='sdevadeo'}
                            </button>
                        </div>
                    </div>
                </div>
            </fieldset>

            {* CARRIER RULES CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="additional_shipping">
                    {l s='Carrier rules :' mod='sdevadeo'}
                </label>

                <div class="col-sm-10" id="connection-panel">
                    {if $apiShippingUpdateDate == null}
                        <button class="button btn btn-info" onclick="SDEVADEO.controller.admin.parameters.updateCarrierList()">
                            {l s='Update marketplace\'s carrier list' mod='sdevadeo'}
                        </button>
                    {/if}
                    <table id="carrier-rule-table" class="table table-hover{if $apiShippingUpdateDate == null} hidden{/if}">
                        <thead>
                            <tr>
                                <th>{l s='Marketplace\'s shipment mode' mod='sdevadeo'}</th>
                                <th>{l s='Internal\'s carrier' mod='sdevadeo'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $marketplace_shipping as $shipping_method}
                                <tr data-code-method="{$shipping_method['code']}">
                                    <td>
                                        <p class="form-control">{$shipping_method['label']}</p>
                                    </td>
                                    <td>
                                        <select id="cms_carrier" class="form-control">
                                            <option selected>
                                                -
                                            </option>
                                            {foreach $internal_carriers as $cms_carrier}
                                                <option value="{$cms_carrier['id_reference']}"{if array_key_exists($shipping_method['code'], $carrier_rules) && $carrier_rules[$shipping_method['code']] == $cms_carrier['id_reference']} selected{/if}>
                                                    {$cms_carrier['name']}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                    <button
                            type="submit"
                            class="btn btn-primary pull-right"
                            title="{l s='Save the general parameters.' mod='sdevadeo'}"
                            onclick="SDEVADEO.controller.admin.parameters.saveCarrierRule()"
                    >
                        <i class="icon-save"></i>&nbsp;
                        {l s='Save' mod='sdevadeo'}
                    </button>
                </div>
            </fieldset>

            <fieldset class="form-group">
                {if $apiShippingUpdateDate != null}
                    <div id="button-form-group button-form-group">
                        <button class="button btn btn-info" id="refresh-button" onclick="SDEVADEO.controller.admin.parameters.updateCarrierList()">
                            <i class="icon-refresh"></i>
                            {l s='Update marketplace\'s carrier list' mod='sdevadeo'}
                        </button>
                        <label class="col-sm-2 control-label" for="refresh-button">
                            {l s='Date of the last update: ' mod='sdevadeo'}{$apiShippingUpdateDate}
                        </label>
                    </div>
                {/if}
            </fieldset>
        </div>

        {* FILTER CONFIGURATION *}
        <div role="tabpanel" class="tab-pane" id="filters">
            <div id="filters-notification">
            </div>
            {* MANUFACTURER FILTER CONFIGURATION *}
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="filter_manufacturer">
                    {l s='Manufacturers to disable :' mod='sdevadeo'}
                </label>

                <div class="form-group">
                    <select class="col-lg-4" name="filter_manufacturer" id="filter_manufacturer" multiple>
                        <option value="false" disabled style="color: green;">{l s='Manufacturers enabled' mod='sdevadeo'}</option>
                        {foreach $manufacturer_list as $key => $name}
                            {if !is_array($manufacturers_excluded) || !in_array($key, $manufacturers_excluded)}
                                <option value="{$key}">{$name}</option>
                            {/if}
                        {/foreach}
                    </select>
                    <div class="col-lg-1 text-center">
                        <a class="btn btn-primary" onclick="SDEVADEO.controller.admin.parameters.removeFiltered('manufacturer')">
                            <i class="icon-chevron-left"></i>
                        </a>
                        <a class="btn btn-primary" onclick="SDEVADEO.controller.admin.parameters.addFiltered('manufacturer')">
                            <i class="icon-chevron-right"></i>
                        </a>
                    </div>
                    <select class="col-lg-4" id="disabled_manufacturer" multiple>
                        <option value="false" disabled style="color: red;">{l s='Manufacturers disabled' mod='sdevadeo'}</option>
                        {if is_array($manufacturers_excluded)}
                            {foreach $manufacturers_excluded as $disabled}
                                <option value="{$disabled}">{$manufacturer_list[$disabled]}</option>
                            {/foreach}
                        {/if}
                    </select>
                </div>
            </fieldset>
            <fieldset class="form-group">
                <label class="col-sm-2 control-label" for="filter_manufacturer">
                    {l s='Suppliers to disable :' mod='sdevadeo'}
                </label>
                <div class="form-group">
                    <select class="col-lg-4" name="filter_supplier" id="filter_supplier" multiple>
                        <option value="false" disabled style="color: green;">{l s='Suppliers enabled' mod='sdevadeo'}</option>
                        {foreach $supplier_list as $key => $name}
                            {if !is_array($suppliers_excluded) || !in_array($key, $suppliers_excluded)}
                                <option value="{$key}">{$name}</option>
                            {/if}
                        {/foreach}
                    </select>
                    <div class="col-lg-1 text-center">
                        <a class="btn btn-primary" onclick="SDEVADEO.controller.admin.parameters.removeFiltered('supplier')">
                            <i class="icon-chevron-left"></i>
                        </a>
                        <a class="btn btn-primary" onclick="SDEVADEO.controller.admin.parameters.addFiltered('supplier')">
                            <i class="icon-chevron-right"></i>
                        </a>
                    </div>
                    <select class="col-lg-4" id="disabled_supplier" multiple>
                        <option value="false" disabled style="color: red;">{l s='Suppliers disabled' mod='sdevadeo'}</option>
                        {if is_array($suppliers_excluded)}
                            {foreach $suppliers_excluded as $disabled}
                                <option value="{$disabled}">{$supplier_list[$disabled]}</option>
                            {/foreach}
                        {/if}
                    </select>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button
                            type="submit"
                            class="btn btn-primary"
                            title="{l s='Save the general parameters.' mod='sdevadeo'}"
                            onclick="SDEVADEO.controller.admin.parameters.saveFilters()"
                    >
                        <i class="icon-save"></i>&nbsp;
                        {l s='Save' mod='sdevadeo'}
                    </button>
                </div>
            </fieldset>
        </div>
    </div>
</div>
{/block}
