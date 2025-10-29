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
    {assign var='sdevadeoAdminPage' value='filter'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-tasks"></i>&nbsp;
    {l s='Products Filter' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
    <div class="col-sm-12" id="filter-panel">
        <div id="filter-notification">
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{l s='Filter type' mod='sdevadeo'}</th>
                    <th>{l s='Filter target' mod='sdevadeo'}</th>
                    <th>{l s='Filter value' mod='sdevadeo'}</th>
                    <th>
                        <button id="add-category-rule-button" class="button btn btn-info alert-category-rule"
                        onclick="SDEVADEO.controller.admin.productFilter.addFilter()">
                            {l s='Add' mod='sdevadeo'}
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
                {if empty($productFilters)}
                    <tr class="no-filters">
                        <td colspan="5" class="text-center">
                            {l s='No filter.' mod='sdevadeo'}
                        </td>
                    </tr>
                {else}
                    {foreach $productFilters as $filter}
                        <tr data-id-filter="{$filter['id']}">
                            <td class="filterId">{$filter['id']}</td>
                            <td class="filterType">{$filter['filterType']}</td>
                            <td class="filterTarget">{$filter['filterTarget']}</td>
                            <td class="filterValue">{$filter['filterValue']}</td>
                            <td>
                                <button class="button btn btn-warning" onclick="SDEVADEO.controller.admin.productFilter.editFilter('{$filter['id']}')">
                                    {l s='Modify' mod='sdevadeo'}
                                </button>
                                <button class="button btn btn-danger" onclick="SDEVADEO.controller.admin.productFilter.deleteFilter({$filter['id']})">
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
                {* FILTER TARGET *}
                <label class="col-sm-2 control-label" for="filter-target">
                    {l s='Filter target' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <select name="filter-target" id="filter-target">
                        <option value="productName"> {l s='Product Name' mod='sdevadeo'}</option>
                        <option value="productEan"> {l s='Product Ean' mod='sdevadeo'}</option>
                        {* <option value="productAttribute"> {l s='Product Attribute' mod='sdevadeo'}</option> *}
                    </select>
                </div>
            </div>

            <div class="form-group">
                {* FILTER TYPE *}
                <label class="col-sm-2 control-label" for="filter-type">
                    {l s='Filter type' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <select name="filter-type" id="filter-type">
                        <option value="equal"> {l s='Equal to' mod='sdevadeo'}</option>
                        <option value="contain"> {l s='Contain' mod='sdevadeo'}</option>
                        <option value="higher"> {l s='Higher Than' mod='sdevadeo'}</option>
                        <option value="lower"> {l s='Lower Than' mod='sdevadeo'}</option>
                        <option value="start"> {l s='Start' mod='sdevadeo'}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                {* FILTER VALUE *}
                <label class="col-sm-2 control-label" for="filter-value">
                    {l s='Filter value' mod='sdevadeo'}
                </label>

                <div class="col-sm-10">
                    <input class="form-control" name="filter-value" id="filter-value">
                </div>
            </div>

            <div class="form-group pull-right">
                <button class="button btn btn-warning cancel-filter"
                    onclick="SDEVADEO.controller.admin.productFilter.closeFilter()">
                    {l s='Cancel' mod='sdevadeo'}
                </button>
                <button class="button btn btn-info hidden add-filter"
                    onclick="SDEVADEO.controller.admin.productFilter.saveFilter()">
                    {l s='Add' mod='sdevadeo'}
                </button>
                <button class="button btn btn-success hidden edit-filter"
                    onclick="SDEVADEO.controller.admin.productFilter.saveFilter()">
                    {l s='Validate' mod='sdevadeo'}
                </button>
            </div>
        </fieldset>
    </div>
{/block}