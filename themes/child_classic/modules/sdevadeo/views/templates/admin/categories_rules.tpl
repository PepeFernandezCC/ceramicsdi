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
    {assign var='sdevadeoAdminPage' value='categories_rules'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-tags"></i>&nbsp;
    {l s='Categories rules' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
    <div role="tabpanel" class="tab-pane" id="categories">
        <div id="categories-notification">
        </div>
        <div id="attributes-notification">
        </div>
        <fieldset class="form-group">
            <label class="col-sm-2 control-label" for="update_logistic_class">
                {l s='Updade the logistic classes (MANDATORY once) :' mod='sdevadeo'}
            </label>

            <div class="col-sm-10">
                <div class="form-inline">
                    <div class="input-group">
                        <button
                                type="submit"
                                class="btn btn-primary pull-right"
                                title="{l s='Update' mod='sdevadeo'}"
                                onclick="SDEVADEO.controller.admin.categoriesRules.updateLogisticClasses()"
                        >
                            <i class="icon-refresh"></i>&nbsp;
                            {l s='Update' mod='sdevadeo'}
                        </button>
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset class="form-group">
            <label class="col-sm-2 control-label">
                {l s='Category rules :' mod='sdevadeo'}
            </label>

            <div class="col-sm-10" id="category-rules-panel">
                <table class="table table-hover">
                    <thead>
                        <tr class="{if !empty($logisticClass)}hidden {/if}no-logistic-class">
                            <th>
                                <div id="category-rule-add" class="warn alert alert-warning{if $logisticClass} hidden{/if}">
                                    <p>
                                        {l s='Import logistic class first' mod='sdevadeo'}
                                    </p>
                                </div>
                            </th>
                        </tr>
                        <tr class="{if empty($logisticClass)}hidden {/if}logistic-class">
                            <th>#</th>
                            <th>{l s='Rule\'s name' mod='sdevadeo'}</th>
                            <th class="hidden">{l s='Shipping cost adjustment' mod='sdevadeo'}</th>
                            <th>{l s='Shipping delay' mod='sdevadeo'}</th>
                            <th>{l s='Logistic class' mod='sdevadeo'}</th>
                            <th>{l s='Price rule' mod='sdevadeo'}</th>
                            <th>
                                <button id="add-category-rule-button" class="button btn btn-info alert-category-rule" onclick="SDEVADEO.controller.admin.categoriesRules.addCategoryRule()">
                                    {l s='Add' mod='sdevadeo'}
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    {if empty($categoryRules)}
                        <tr class="no-categories">
                            <td colspan="5" class="text-center">
                                {l s='No category rules.' mod='sdevadeo'}
                            </td>
                        </tr>
                    {else}
                        {foreach $categoryRules as $rule}
                            <tr data-id-rule="{$rule['id']}">
                                <td class="categoryRuleId">{$rule['id']}</td>
                                <td class="categoryRuleName">{$rule['name']}</td>
                                <td class="categoryRuleShippingCost hidden">{$rule['shippingCost']}</td>
                                <td class="categoryRuleShippingDelay">{$rule['shippingDelay']}</td>
                                <td class="categoryRuleLogisticClass">{if $rule['logisticClass']}{SdevAdeoLogisticClass::getLabelFromCode($rule['logisticClass'])}{else}{$defaultLogisticClass}{/if}</td>
                                <td class="categoryRulePriceRule">{if empty($rule['pricingRule'])}{l s='No' mod='sdevadeo'}{else}{l s='Yes' mod='sdevadeo'}{/if}</td>
                                <td>
                                    <button class="button btn btn-warning" onclick="SDEVADEO.controller.admin.categoriesRules.editCategoryRule('{$rule['id']}')">
                                        {l s='Modify' mod='sdevadeo'}
                                    </button>
                                    <button class="button btn btn-danger" onclick="SDEVADEO.controller.admin.categoriesRules.deleteCategoryRule({$rule['id']})">
                                        {l s='Delete' mod='sdevadeo'}
                                    </button>
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                    </tbody>
                </table>
                <fieldset class="panel form-horizontal hidden">
                    <div class="form-group">
                        {* RULE'S NAME *}
                        <label class="col-sm-2 control-label" for="category-rule-name">
                            {l s='Rule\'s name' mod='sdevadeo'}
                        </label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="category-rule-name" id="category-rule-name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        {* LOGISTIC CLASS *}
                        <label class="col-sm-2 control-label" for="category-rule-logistic-class">
                            {l s='Select the logistic class' mod='sdevadeo'}
                        </label>

                        <div class="col-sm-10">
                            <select name="category-rule-logistic-class" id="category-rule-logistic-class">
                                {foreach $logisticClass as $class}
                                    <option value="{$class['code']}" {if $class['code'] == 'INIT'} selected>{l s='(default) ' mod='sdevadeo'}{else}>{/if}{$class['label']}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        {* PREPARATION TIME *}
                        <label class="col-sm-2 control-label" for="category-rule-shipping-delay">
                            {l s='Preparation time' mod='sdevadeo'}
                        </label>

                        <div class="col-sm-10">
                            <input class="form-control" type="number" id="category-rule-shipping-delay" name="category-rule-shipping-delay" placeholder="{l s='Please enter a number in days' mod='sdevadeo'}">
                            <p class="profile-help">
                                {l s='Time needed to dispatch the order.' mod='sdevadeo'}
                            </p>
                        </div>
                    </div>
                    <div class="form-group hidden">
                        {* SHIPPING COST *}
                        <label class="col-sm-2 control-label" for="category-rule-shipping-cost">
                            {l s='Shipping cost adjustment' mod='sdevadeo'}
                        </label>

                        <div class="col-sm-10">
                            <input class="form-control" type="number" id="category-rule-shipping-cost" name="category-rule-shipping-cost">
                        </div>
                    </div>
                    <div class="form-group hidden">
                        {* SHIPPING COST *}
                        <label class="col-sm-2 control-label" for="category-rule-free-carriers">
                            {l s='Select the free carriers' mod='sdevadeo'}
                        </label>

                        <div class="col-sm-10">
                            <select name="category-rule-free-carriers" id="category-rule-free-carriers" multiple>
                                {foreach $marketplace_shipping as $name}
                                    <option value="{$name['code']}">{$name['label']}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        {* SHIPPING COST *}
                        <label class="col-sm-2 control-label" for="category-rule-cost-adjustment">
                            {l s='Price adjustement' mod='sdevadeo'}
                        </label>

                        <div class="col-3">
                            <div class="input-group">
                                <input class="form-control col-3" type="number" id="category-rule-cost-adjustment" name="category-rule-cost-adjustment">
                                <span class="input-group-addon">{l s=' % costs calculated in the product price (incl. taxes)' mod='sdevadeo'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group hidden">
                        <label class="col-sm-2 control-label" for="category-rule-cost-applied">
                            {l s='Adjuste the price if the product already has forced price' mod='sdevadeo'}
                        </label>
                        <div class="margin-form">
                            <div class="input-group">
                        <span class="switch prestashop-switch fixed-width-lg" id="category-rule-cost-applied">
                            <input id="disabled-categories-switch_1" name="disabled-categories-switch" value="1" checked type="radio" />
                            <label for="disabled-categories-switch_1" class="label-checkbox">{l s='Yes' mod='sdevadeo'}</label>

                            <input id="disabled-categories-switch_0" name="disabled-categories-switch" value="0" type="radio" />
                            <label for="disabled-categories-switch_0" class="label-checkbox">{l s='No' mod='sdevadeo'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                            </div>
                        </div>
                    </div>
                    {* PRICING RULES CONFIGURATION *}
                    <fieldset class="form-group hidden">
                        <label class="col-sm-2 control-label" for="price-rule-panel">
                            {l s='Price rules (included taxes) :' mod='sdevadeo'}
                        </label>

                        <div class="col-sm-10" id="price-rule-panel">
                            <div id="price-rule-notification">
                            </div>
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>{l s='Min price (included)' mod='sdevadeo'}</th>
                                    <th>{l s='Max price (excluded)' mod='sdevadeo'}</th>
                                    <th>{l s='Value' mod='sdevadeo'}</th>
                                    <th>{l s='Type' mod='sdevadeo'}</th>
                                    <th>
                                        <button class="button btn btn-info pull-right" onclick="SDEVADEO.controller.admin.categoriesRules.addPricingRule()">
                                            {l s='Add' mod='sdevadeo'}
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot id="pricing-rule-foot" class="hidden">
                                <tr>
                                    <td>
                                        <div class="col-sm-10">
                                            <input class="form-control" type="number" id="min-price-rule" name="min-price-rule">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col-sm-10">
                                            <input class="form-control" type="number" id="max-price-rule" name="max-price-rule">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col-sm-10">
                                            <input class="form-control" type="number" id="value-price-rule" name="value-price-rule">
                                        </div>
                                    </td>
                                    <td>
                                        <select id="type-price-rule">
                                            <option>
                                            </option>
                                            <option value="1">
                                                {l s='Percent' mod='sdevadeo'}
                                            </option>
                                            <option value="0">
                                                {l s='Amount' mod='sdevadeo'}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="button btn btn-warning cancel-edit-pricing" onclick="SDEVADEO.controller.admin.categoriesRules.closePricingRule()">
                                            {l s='Cancel' mod='sdesdevadeovcolizey'}
                                        </button>
                                        <button class="button btn btn-info hidden add-pricing-rule" onclick="SDEVADEO.controller.admin.categoriesRules.savePricingRule()">
                                            {l s='Add' mod='sdevadeo'}
                                        </button>
                                        <button class="button btn btn-success hidden edit-pricing-rule" onclick="SDEVADEO.controller.admin.categoriesRules.savePricingRule()">
                                            {l s='Validate' mod='sdevadeo'}
                                        </button>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </fieldset>
                    <div class="form-group pull-right">
                        <button class="button btn btn-warning cancel-edit-category" onclick="SDEVADEO.controller.admin.categoriesRules.closeCategoryRule()">
                            {l s='Cancel' mod='sdesdevadeovcolizey'}
                        </button>
                        <button class="button btn btn-info hidden add-category-rule" onclick="SDEVADEO.controller.admin.categoriesRules.saveCategoryRule()">
                            {l s='Add' mod='sdevadeo'}
                        </button>
                        <button class="button btn btn-success hidden edit-category-rule" onclick="SDEVADEO.controller.admin.categoriesRules.saveCategoryRule()">
                            {l s='Validate' mod='sdevadeo'}
                        </button>
                    </div>
                </fieldset>
            </div>
        </fieldset>
    </div>
{/block}
