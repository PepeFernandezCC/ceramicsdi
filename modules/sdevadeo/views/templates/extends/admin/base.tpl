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

{block name='SdevAdeoVarsAssignments'}{/block}

<section data-module="sdevadeo">
    <div class="row">
        <div class="col-lg-3">
            {include file=$smarty.const._PS_MODULE_DIR_|cat:$module->name|cat:'/views/templates/inc/admin/nav.tpl'}

            <figure class="sdevadeo-logo">
                <picture>
                    <img alt="{l s='ScaleDEV logo' mod='sdevadeo'}"
                        src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/scaledev-logo.svg"
                    />
                </picture>
            </figure><!-- /.sdevadeo-logo -->

            <p class="sdevadeo-version">
                {l s='Version %s' sprintf=[$module->version|escape:'htmlall':'UTF-8'] mod='sdevadeo'}
            </p><!-- /.sdevadeo-version -->
        </div><!-- /.col-lg-3 -->

        <div class="col-lg-9">
            <div class="panel panel--full-height">
                <header class="panel-heading">
                    <span class="panel-title">
                        {block name='SdevAdeoAdminPageTitle'}{/block}
                    </span><!-- /.panel-title -->
                </header><!-- /.panel-heading -->

                <main class="panel-body">
                    {block name='SdevAdeoAdminPageContent'}{/block}
                </main><!-- /.panel-body -->

                <footer class="panel-footer text-center">
                    <p>
                        {l s='Documentations' mod='sdevadeo'}
                    </p>

                    {if empty($documentationsList)}
                        <div class="warn alert alert-danger">
                            <p>
                                {l s='No documentation available yet' mod='sdevadeo'}.
                            </p>
                        </div>
                    {else}
                        <p>
                            {foreach name='documentationsListLoop' from=$documentationsList key=isoCode item=url}
                                {if $smarty.foreach.documentationsListLoop.index > 0}
                                    &nbsp;
                                {/if}

                                <a href="{$url|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">
                                    <i class="icon-book"></i>
                                    {$isoCode|escape:'htmlall':'UTF-8'|upper}
                                </a>
                            {/foreach}{* /foreach documentationsListLoop *}
                        </p>
                    {/if}

                    <hr />

                    <p>
                        {l s='Copyright' mod='sdevadeo'} &copy; ScaleDEV
                    </p>

                    <p>
                        <a href="{SdevAdeo::PS_SUPPORT_URL|escape:'htmlall':'UTF-8'}"
                           target="_blank"
                           class="btn btn-default"
                        >
                            <i class="icon-envelope"></i>&nbsp;
                            {l s='Click here to contact us' mod='sdevadeo'}
                        </a>
                    </p>
                </footer><!-- /.panel-footer -->
            </div><!-- /.panel -->
        </div><!-- /.col-lg-9 -->
    </div><!-- /.row -->
</section><!-- /Module SdevAdeo -->
