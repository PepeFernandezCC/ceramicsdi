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
    {assign var='sdevadeoAdminPage' value='authentication'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-user"></i>&nbsp;
    {l s='Authentication' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
    <div id="save-notification">
        <p></p>
    </div>
    <div id="connection-notification">
    </div>
    <div class="form-horizontal" data-form="config">
        <fieldset class="form-group">
            <label class="col-sm-2 control-label" for="api_key">
                {l s='API key' mod='sdevadeo'}
            </label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="api_key" id="api_key" value="{$api_key}" />
            </div>
        </fieldset>

        <fieldset class="form-group">
            <label class="col-sm-2 control-label" for="test_mode">
                {l s='Test mode enabled' mod='sdevadeo'}
            </label>
            <div class="margin-form">
                <div class="input-group">
                    <span class="switch prestashop-switch fixed-width-lg" id="test-mode-switch">
                        <input id="test_mode_1" name="test_mode" value="1"{if $test_mode == 1} checked{/if} type="radio" />
                        <label for="test_mode_1" class="label-checkbox">{l s='Yes' mod='sdevadeo'}</label>

                        <input id="test_mode_0" name="test_mode" value="0"{if $test_mode == 0} checked{/if} type="radio" />
                        <label for="test_mode_0" class="label-checkbox">{l s='No' mod='sdevadeo'}</label>

                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>
        </fieldset>

        <fieldset class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button
                    type="submit"
                    class="btn btn-primary"
                    title="{l s='Save the module\'s configuration' mod='sdevadeo'}"
                    onclick="SDEVADEO.controller.admin.authentication.update()"
                >
                    <i class="icon-save"></i>&nbsp;
                    {l s='Save' mod='sdevadeo'}
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                    title="{l s='Test the API connection' mod='sdevadeo'}"
                    onclick="SDEVADEO.controller.admin.authentication.test()"
                >
                    <i class="icon-save"></i>&nbsp;
                    {l s='Connection test' mod='sdevadeo'}
                </button>
            </div>
        </fieldset>

        <div class="warn alert alert-warning{if $lastShopInfoUpdate} hidden{/if}">
            {l s='You have to be connected in order to import your shop information.' mod='sdevadeo'}
        </div>
        <fieldset id="shop-information-update" class="form-group{if !$lastShopInfoUpdate} hidden{/if}">
            <div class="col-sm-offset-2 col-sm-10">
                <button
                        type="submit"
                        class="btn btn-primary"
                        title="{l s='Update shop information' mod='sdevadeo'}"
                        onclick="SDEVADEO.controller.admin.authentication.updateShopInformation()"
                >
                    <i class="fa fa-fw fa-refresh"></i>
                    {l s='Update shop information' mod='sdevadeo'}
                </button>
                <p>
                    {l s='Last import\'s date : ' mod='sdevadeo'}{if $lastShopInfoUpdate}{$lastShopInfoUpdate}{/if}
                </p>
            </div>
        </fieldset>
    </div>
{/block}
