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
    {assign var='sdevadeoAdminPage' value='index'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-home"></i>&nbsp;
    {l s='Home' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
    <header>
        <div class="row text-center" id="adeo-logo-block">
            <div>
                <figure>
                    <picture>
                        {block name='SdevAdeoAdminPageIndexModuleLogo'}{/block}
                    </picture>
                </figure>
            </div>
        </div>
    </header>

    <hr />

    <p class="text-center">
        {l s='Welcome to our Adeo module!' mod='sdevadeo'}
    </p>

    <hr />

    {* MODULE'S DESCRIPTION *}
    <section class="sdevadeo-module-description">
        {block name='SdevAdeoAdminPageIndexModuleDescription'}{/block}
    </section><!-- /.sdevadeo-module-description -->

    <hr />

    <div id="scaledev-block" class="text-center">
        <p>
            {l s='This module has been developed by ScaleDEV.' mod='sdevadeo'}
        </p>
        <figure>
            <picture>
                <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/scaledev-logo.svg"
                     alt="{l s='ScaleDEV logo' mod='sdevadeo'}"
                     style="max-height: 64px; max-width: 100%;"
                />
            </picture>
        </figure>
    </div>
{/block}
