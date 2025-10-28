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
    {assign var='sdevadeoAdminPage' value='products_flow'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-cloud-upload"></i>&nbsp;
    {l s='Products flow' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
    <div class="divContentMenu">
        <div id="flux-notification-success" class="hidden conf alert alert-success">
        </div>
        <div id="flux-notification-error" class="hidden warn alert alert-danger">
        </div>
        {* TABS *}
        <ul id="navAdminConfig" class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#product-flow" aria-controls="product-flow" role="tab" data-toggle="tab"><i class="icon-cloud-upload"></i>{l s=' Product flow' mod='sdevadeo'}</a></li>
            <li role="presentation"><a href="#offer-flow" aria-controls="offer-flow" role="tab" data-toggle="tab"><i class="fa fa-refresh"></i>{l s=' Offer flow' mod='sdevadeo'}</a></li>
        </ul>

        <div class="tab-content">
            {* PRODUCT FLOW *}
            <div role="tabpanel" class="tab-pane form-horizontal  active" id="product-flow">
                <div class="form-group">
                    <label class="control-label col-lg-2">
                    </label>
                    <div class="margin-form col-md-7">
                        <div class="input-group">
                            <a title="{l s='Generate the flow' mod='sdevadeo'}" class="input-group-addon" id="btnGenerateFlux" onclick="SDEVADEO.controller.admin.productsFlow.generateProductFlow(
                                '{$context->link->getModuleLink(
                                    'sdevadeo',
                                    'generate',
                                    [
                                    'shop_id' => $context->shop->id|intval,
                                    'token' => $token|escape:'htmlall':'UTF-8',
                                    'flow' => 'products'
                                    ],
                                    true
                                )}'
                            )" rel="FR2">
                                <i class="fa fa-refresh fa-fw ng-scope"></i>
                            </a>
                            <input type="text" readonly="readonly" value="{$module_url|escape:'htmlall':'UTF-8'}fluxs/products/{$shop_name_formatted}/Products.csv">
                        </div>
                        <div class="text-center send-flow-button">
                            <button
                                    type="submit"
                                    class="btn btn-success download-flow-button hidden"
                                    title="{l s='Send the product flow.' mod='sdevadeo'}"
                                    onclick="SDEVADEO.controller.admin.productsFlow.sendProductFlow()"
                            >
                                <i class="icon-external-link-sign pull-left"></i>
                                {l s='Send product flow' mod='sdevadeo'}
                            </button>
                        </div>
                        <div class="text-center product-flow-utils mb-2">
                            <div class="col-md-6 col-lg-6">
                                <a
                                    class="btn btn-warning download-product-flow"
                                    href="{$module_url|escape:'htmlall':'UTF-8'}fluxs/products/{$shop_name_formatted}/Products.csv" download
                                >
                                    <i class="fa fa-download"></i>
                                    {l s='Download product flow' mod='sdevadeo'}
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <a
                                        class="btn btn-warning mirakl-mapping-button"
                                        href={$mapping_wizard_url}
                                >
                                <i class="icon-external-link-sign pull-left"></i>
                                {l s='Mirakl mapping' mod='sdevadeo'}
                                </a>
                            </div>
                        </div>
                        <p class="sdev-mt hidden" id="nbProductsProcessed">
                            <span></span>
                            {l s=' treated products.' mod='sdevadeo'}
                            <span id="percent_value">
                            (<span></span>
                            {l s='%)' mod='sdevadeo'}
                        </span>
                        </p>
                        <progress class="sdev-progress-bar hidden" id="progressBarFlow" value="" max="" data-lang="FR" data-shop="2"></progress>
                        <div class="hidden report-product-flow">
                            <button class="alert alert-success btn-success col-xs-12 text-left collapsed" type="button" data-toggle="collapse" data-target="#flux-prod-success" aria-expanded="false" aria-controls="flux-prod-success">
                                <span></span>
                                {l s=' products exported with success.' mod='sdevadeo'}
                                <div class="pull-right">
                                    Logs
                                    <span class="caret"></span>
                                </div>
                            </button>
                            <div class="collapse" id="flux-prod-success">
                                <div class="well">
                                </div>
                            </div>
                            <button class="alert alert-info btn-info col-xs-12 text-left" type="button" data-toggle="collapse" data-target="#flux-prod-filtered" aria-expanded="false" aria-controls="flux-prod-filtered">
                                <span></span>
                                {l s=' products filtered.' mod='sdevadeo'}
                                <div class="pull-right">
                                    Logs
                                    <span class="caret"></span>
                                </div>
                            </button>
                            <div class="collapse" id="flux-prod-filtered">
                                <div class="well">
                                </div>
                            </div>
                            <button class="alert alert-danger btn-danger col-xs-12 text-left" type="button" data-toggle="collapse" data-target="#flux-prod-error" aria-expanded="false" aria-controls="flux-prod-error">
                                <span></span>
                                {l s=' products in error.' mod='sdevadeo'}
                                <div class="pull-right">
                                    Logs
                                    <span class="caret"></span>
                                </div>
                            </button>
                            <div class="collapse" id="flux-prod-error">
                                <div class="well">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3{if $product_log_exist} hidden{/if}" id="no-product-logs-info">
                        <div class="col-md-4{if $product_log_exist} hidden{/if}">
                            {l s='No flow generated' mod='sdevadeo'}<br>
                            {l s='No logs file available.' mod='sdevadeo'}
                        </div>
                    </div>
                    <div class="col-md-3{if !$product_log_exist} hidden{/if}" id="product-logs-info">
                        <a class="btn btn-default pull-left" href="{$log_file_url}logs/flux/product/product_{$shop_name_formatted}.txt" download title="{l s='Download' mod='sdevadeo'}"><i class="fa fa-download"></i></a>
                        <div class="col-md-4">
                            {if $last_product_flow_date}{$last_product_flow_date}<br>{/if}
                            {l s='Download the logs file' mod='sdevadeo'}
                        </div>
                    </div>
                </div>
                <div class="well sdev-product-reports">
                    <samp>
                        <span class="h3">{l s='Latest reports' mod='sdevadeo'}</span>
                        <hr/>

                        {* LOADING *}
                        <div class="hidden">
                            <em class="text-muted">{l s='Getting last products flow reports' mod='sdevadeo'}...</em>
                        </div>

                        <div>
                            {* LAST PRODUCTS FLOW REPORTS UPDATING *}
                            <div class="hidden">
                                <i class="fa fa-fw fa-refresh"></i>
                                <em class="text-muted">{l s='Last products flow reports loading' mod='sdevadeo'}...</em>
                            </div>

                            <div>
                                {* UPDATE BUTTON *}
                                <button class="btn btn-sm btn-default"
                                        onclick="SDEVADEO.controller.admin.productsFlow.updateProductFlowReports()"
                                >
                                    <i class="fa fa-refresh"></i>
                                    {l s='Update' mod='sdevadeo'}
                                </button>&nbsp;

                                {* LAST UPDATE *}
                                {l s='Last update' mod='sdevadeo'} :
                                <span id="date-update-product-report"><em class="text-muted">{if $last_product_flow_date}{$last_product_flow_date}{else}{l s='Any update.' mod='sdevadeo'}{/if}</em></span>
                                <hr />

                                {* REPORTS *}
                                <table class="table hidden">
                                    <thead>
                                    <tr>
                                        <th>{l s='Date' mod='sdevadeo'}</th>
                                        <th>{l s='ID' mod='sdevadeo'}</th>
                                        <th>{l s='Read' mod='sdevadeo'}</th>
                                        <th>{l s='Treated' mod='sdevadeo'}</th>
                                        <th>{l s='Error' mod='sdevadeo'}</th>
                                        <th>{l s='Rejected' mod='sdevadeo'}</th>
                                        <th>{l s='Accepted' mod='sdevadeo'}</th>
                                        <th>{l s='Status' mod='sdevadeo'}</th>
                                        <th class="sdev-table-th-full-width">{l s='File' mod='sdevadeo'}</th>
                                    </tr>
                                    </thead>

                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </samp>
                </div>
            </div>
            {* END PRODUCT FLOW *}
            {* OFFER FLOW *}
            <div role="tabpanel" class="tab-pane form-horizontal" id="offer-flow">
                <fieldset class="form-group">
                    <label class="col-sm-2 control-label" for="update-type">
                        {l s='Type of update to perform: ' mod='sdevadeo'}
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="update-type">
                            <option selected value="NORMAL">{l s='Normal update' mod='sdevadeo'}</option>
                            <option value="PARTIAL_UPDATE">{l s='Partial update' mod='sdevadeo'}</option>
                            <option value="REPLACE">{l s='Replace offers in place' mod='sdevadeo'}</option>
                        </select>
                    </div>
                </fieldset>
                <div class="form-group">
                    <label class="control-label col-lg-2">
                    </label>
                    <div class="margin-form col-md-7">
                        <div class="input-group">
                            <a title="{l s='Generate the flow' mod='sdevadeo'}" class="input-group-addon" id="btnGenerateFlux" onclick="SDEVADEO.controller.admin.productsFlow.generateOfferFlow(
                                    '{$context->link->getModuleLink(
                            'sdevadeo',
                            'generate',
                            [
                            'shop_id' => $context->shop->id|intval,
                            'token' => $token|escape:'htmlall':'UTF-8',
                            'flow' => 'offers'
                            ],
                            true
                            )}'
                                    )" rel="FR2">
                                <i class="fa fa-refresh fa-fw"></i>
                            </a>
                            <input type="text" readonly="readonly" value="{$module_url|escape:'htmlall':'UTF-8'}fluxs/offers/{$shop_name_formatted|lower|escape:'htmlall':'UTF-8'}/Offers.csv"">
                        </div>
                        <div class="text-center download-flow-button">
                            <button
                                    type="submit"
                                    class="btn btn-success download-flow-button hidden"
                                    title="{l s='Send the offer flow.' mod='sdevadeo'}"
                                    onclick="SDEVADEO.controller.admin.productsFlow.sendOfferFlow()"
                            >
                                <i class="icon-external-link-sign pull-left"></i>
                                {l s='Send offer flow' mod='sdevadeo'}
                            </button>
                        </div>
                        <p class="sdev-mt hidden" id="nbOffersProcessed">
                            <span></span>
                            {l s=' treated offers. (' mod='sdevadeo'}
                            <span id="percent_value">
                        <span></span>
                        {l s='%)' mod='sdevadeo'}
                    </span>
                        </p>
                        <progress class="sdev-progress-bar hidden" id="progressBarFlow" value="" max="" data-lang="FR" data-shop="2"></progress>
                        <div class="hidden report-offer-flow">
                            <button class="alert alert-success btn-success col-xs-12 text-left collapsed" type="button" data-toggle="collapse" data-target="#flux-offer-success" aria-expanded="false" aria-controls="flux-offer-success">
                                <span></span>
                                {l s=' offers exported with success.' mod='sdevadeo'}
                                <div class="pull-right">
                                    Logs
                                    <span class="caret"></span>
                                </div>
                            </button>
                            <div class="collapse" id="flux-offer-success">
                                <div class="well">
                                </div>
                            </div>
                            <button class="alert alert-info btn-info col-xs-12 text-left" type="button" data-toggle="collapse" data-target="#flux-offer-filtered" aria-expanded="false" aria-controls="flux-offer-filtered">
                                <span></span>
                                {l s=' offers filtered.' mod='sdevadeo'}
                                <div class="pull-right">
                                    Logs
                                    <span class="caret"></span>
                                </div>
                            </button>
                            <div class="collapse" id="flux-offer-filtered">
                                <div class="well">
                                </div>
                            </div>
                            <button class="alert alert-danger btn-danger col-xs-12 text-left" type="button" data-toggle="collapse" data-target="#flux-offer-error" aria-expanded="false" aria-controls="flux-offer-error">
                                <span></span>
                                {l s=' offers in error.' mod='sdevadeo'}
                                <div class="pull-right">
                                    Logs
                                    <span class="caret"></span>
                                </div>
                            </button>
                            <div class="collapse" id="flux-offer-error">
                                <div class="well">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3{if $product_log_exist} hidden{/if}" id="offer-logs-info">
                        <div class="col-md-4{if $offer_log_exist} hidden{/if}">
                            {l s='No flow generated' mod='sdevadeo'}<br>
                            {l s='No logs file available.' mod='sdevadeo'}
                        </div>
                    </div>
                    <div class="col-md-3{if !$offer_log_exist} hidden{/if}" id="no-offer-logs-info">
                        <a class="btn btn-default pull-left" href="{$log_file_url}logs/flux/offer/offer_{$shop_name_formatted}.txt" download title="{l s='Download' mod='sdevadeo'}"><i class="fa fa-download"></i></a>
                        <div class="col-md-4">
                            {if $last_offer_flow_date}{$last_offer_flow_date}<br>{/if}
                            {l s='Download the logs file' mod='sdevadeo'}
                        </div>
                    </div>
                </div>
                <div class="well sdev-offer-reports">
                    <samp>
                        <span class="h3">{l s='Latest reports' mod='sdevadeo'}</span>
                        <hr/>
                        {* LOADING *}
                        <div class="hidden">
                            <em class="text-muted">{l s='Getting last offers flow reports' mod='sdevadeo'}...</em>
                        </div>

                        <div>
                            {* LAST PRODUCTS FLOW REPORTS UPDATING *}
                            <div class="hidden">
                                <i class="fa fa-fw fa-refresh"></i>
                                <em class="text-muted">{l s='Last offers flow reports loading' mod='sdevadeo'}...</em>
                            </div>

                            <div>
                                {* ERROR MESSAGE *}
                                <div class="alert alert-danger report-error-notification hidden">
                                    {*{l s='Package ID' mod='sdevadeo'} #[[ key ]] : [[ value ]].*}
                                </div>

                                {* UPDATE BUTTON *}
                                <button class="btn btn-sm btn-default"
                                        onclick="SDEVADEO.controller.admin.productsFlow.updateOfferFlowReports()"
                                >
                                    <i class="fa fa-refresh"></i>
                                    {l s='Update' mod='sdevadeo'}
                                </button>&nbsp;

                                {* LAST UPDATE *}
                                {l s='Last update' mod='sdevadeo'} :
                                <span id="date-update-offer-report"><em class="text-muted">{if $last_offer_flow_date}{$last_offer_flow_date}{else}{l s='Any update.' mod='sdevadeo'}{/if}</em></span>
                                <hr />

                                {* REPORTS *}
                                <table class="table hidden">
                                    <thead>
                                    <tr>
                                        <th>{l s='Date' mod='sdevadeo'}</th>
                                        <th>{l s='ID' mod='sdevadeo'}</th>
                                        <th>{l s='Read' mod='sdevadeo'}</th>
                                        <th>{l s='Pending' mod='sdevadeo'}</th>
                                        <th>{l s='Success' mod='sdevadeo'}</th>
                                        <th>{l s='Error' mod='sdevadeo'}</th>
                                        <th>{l s='Inserted' mod='sdevadeo'}</th>
                                        <th>{l s='Deleted' mod='sdevadeo'}</th>
                                        <th>{l s='Status' mod='sdevadeo'}</th>
                                        <th>{l s='Mode' mod='sdevadeo'}</th>
                                        <th class="sdev-table-th-full-width">{l s='File' mod='sdevadeo'}</th>
                                    </tr>
                                    </thead>

                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </samp>
                </div>
            </div>
            {* END OFFER FLOW *}
        </div>
    </div>
{/block}
