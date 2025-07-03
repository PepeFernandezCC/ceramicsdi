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
    {assign var='sdevadeoAdminPage' value='scheduled_tasks'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-tasks"></i>&nbsp;
    {l s='Scheduled tasks' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
    <div class="tab-content sdev-mb">
        <fieldset class="form-horizontal">
            <h4>{l s='Offer flows (by shop)' mod='sdevadeo'}</h4>

            {* Intégration variable pour tâche cron OFFER. *}
            {foreach $shop_list as $shop}
            <div class="form-group">
                <label class="control-label col-lg-3" style="padding-top : 0">
                    {$shop.name|escape:'htmlall':'UTF-8'}
                </label>
                <div class="margin-form col-lg-9">
                    {if $ps_version >= 1.6}
                        <!-- php -->
                        <input type="text" readonly="readonly" value="php {$document_root|escape:'htmlall':'UTF-8'}offer.php {$shop.id_shop|escape:'htmlall':'UTF-8'}">
                    {/if}
                    <!-- wget -->
                    <input type="text" readonly="readonly" value="{$context->link->getModuleLink(
                            'sdevadeo',
                            'SendProductOffersAction',
                            [
                            'shop_id' => $context->shop->id|intval,
                            'token' => $token
                            ]
                        )|escape:'htmlall':'UTF-8'}">
                </div>
            </div>
            {/foreach}
        </fieldset>
        <fieldset class="form-horizontal">
            <h4>{l s='Accept orders (by shop)' mod='sdevadeo'}</h4>

            {foreach $shop_list as $shop}
                <div class="form-group">
                    <label class="control-label col-lg-3" style="padding-top : 0">
                        {$shop.name|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="margin-form col-lg-9">
                        {if $ps_version >= 1.6}
                            <!-- php -->
                            <input type="text" readonly="readonly" value="php {$document_root|escape:'htmlall':'UTF-8'}accept_order.php {$shop.id_shop|escape:'htmlall':'UTF-8'}">
                        {/if}
                        <!-- wget -->
                        <input type="text" readonly="readonly" value="{$context->link->getModuleLink(
                        'sdevadeo',
                        'AcceptOrdersAction',
                        [
                        'shop_id' => $context->shop->id|intval,
                        'token' => $token
                        ]
                        )|escape:'htmlall':'UTF-8'}">
                    </div>
                </div>
            {/foreach}
        </fieldset>
        <fieldset class="form-horizontal">
            <h4>{l s='Ship orders (by shop)' mod='sdevadeo'}</h4>

            {foreach $shop_list as $shop}
                <div class="form-group">
                    <label class="control-label col-lg-3" style="padding-top : 0">
                        {$shop.name|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="margin-form col-lg-9">
                        {if $ps_version >= 1.6}
                            <!-- php -->
                            <input type="text" readonly="readonly" value="php {$document_root|escape:'htmlall':'UTF-8'}shipping.php {$shop.id_shop|escape:'htmlall':'UTF-8'}">
                        {/if}
                        <!-- wget -->
                        <input type="text" readonly="readonly" value="{$context->link->getModuleLink(
                        'sdevadeo',
                        'ShipOrdersAction',
                        [
                        'shop_id' => $context->shop->id|intval,
                        'token' => $token
                        ]
                        )|escape:'htmlall':'UTF-8'}">
                    </div>
                </div>
            {/foreach}
        </fieldset>
    </div>
{/block}
