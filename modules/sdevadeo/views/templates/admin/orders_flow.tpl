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
    {assign var='sdevadeoAdminPage' value='orders_flow'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-cloud-download"></i>&nbsp;
    {l s='Orders flow' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
<div class="tab-content">
    <div id="success-notification" class="alert alert-success hidden"></div>
    <div id="error-notification" class="alert alert-danger hidden"></div>
    <div class="row">
        <div class="col-md-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>{l s='Last import' mod='sdevadeo'}</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{if $date_last_flow}{$date_last_flow}{else}{l s='No import' mod='sdevadeo'}{/if}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4 pull-right">
            <h4>{l s='Legend' mod='sdevadeo'}</h4>
            <hr>
            <span class="label label-success">{l s='Order waiting to be accepted' mod='sdevadeo'}</span><br><br>
            <span class="label label-info">{l s='Order waiting for debit payment' mod='sdevadeo'}</span><br><br>
            <span class="label label-warning">{l s='Order ready to be imported' mod='sdevadeo'}</span>
        </div>
    </div>
    <br/>
    {* ACCEPT ORDERS *}
    <div id="tab-com-accept" role="tabpanel" class="tab-pane tabPaneAdminFluxCom active form-horizontal">
        <div class="row">
            <div class="col-lg-6">
                <a class="btn btn-primary" onclick="SDEVADEO.controller.admin.ordersFlow.getOrders()">
                    {l s='See orders to be accepted' mod='sdevadeo'}
                </a>
            </div>
        </div>
        <br>
        <table class="table table-hover" id="display-orders">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox"
                               class="sdev-checkbox-accept-order"
                        >
                    </th>
                    <th>{l s='Order number' mod='sdevadeo'}</th>
                    <th>{l s='Date' mod='sdevadeo'}</th>
                    <th>{l s='Client\'s name' mod='sdevadeo'}</th>
                    <th>{l s='Products' mod='sdevadeo'}</th>
                    <th>{l s='Total incl. VAT' mod='sdevadeo'}</th>
                    <th>{l s='State' mod='sdevadeo'}</th>
                </tr>
            </thead>
            <tbody>
                <tr id="getting-orders-loading" class="hidden">
                    <td colspan="6">
                        <i class="fa fa-refresh fa-fw"></i><em class="text-muted">{l s='Loading...' mod='sdevadeo'}</em>
                    </td>
                </tr>
                <tr id="no-order-retrieve" class="hidden">
                    <td colspan="6">
                        <em class="text-muted">{l s='No orders' mod='sdevadeo'}</em>
                    </td>
                </tr>
            </tbody>
        </table>

        <p>
            <a class="btn btn-success" onclick="SDEVADEO.controller.admin.ordersFlow.importOrders()">
                {l s='Accept' mod='sdevadeo'}
            </a>

            <a class="btn btn-danger" onclick="SDEVADEO.controller.admin.ordersFlow.deleteOrders('{l s='Do you really want to refuse the selected orders ?' mod='sdevadeo'}')">
                {l s='Refuse' mod='sdevadeo'}
            </a>
        </p>
    </div>
{/block}
