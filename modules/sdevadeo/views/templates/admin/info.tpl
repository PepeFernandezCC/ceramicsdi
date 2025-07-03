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
    {assign var='sdevadeoAdminPage' value='info'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-info"></i>&nbsp;
    {l s='Information' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
    <h4>
        {l s='Module version' mod='sdevadeo'}
    </h4>

    <div class="alert alert-info">
        <p>
            {$module->version|escape:'htmlall':'UTF-8'}
        </p>
    </div>
    {* /Module version *}


    {if isset($apiState)}
        <hr />

        <h4>
            {l s='API version' mod='sdevadeo'}
        </h4>

        {if $apiState['connected'] == true}
            <div class="conf alert alert-success">
                <p>
                    {$apiState['message']|escape:'htmlall':'UTF-8'}
                </p>
            </div>
        {else}
            <div class="warn alert alert-danger">
                <p>
                    {$apiState['message']|escape:'htmlall':'UTF-8'}
                </p>
            </div>
        {/if}
    {/if}
    {* /API version *}

    <hr />

    <h4>
        {l s='PrestaShop version' mod='sdevadeo'}
    </h4>

    {if version_compare($smarty.const._PS_VERSION_, $module->ps_versions_compliancy['min'], '>=')}
        <div class="conf alert alert-success">
            <p>
                {$smarty.const._PS_VERSION_|escape:'htmlall':'UTF-8'}
                ({l s='compatible PrestaShop version' mod='sdevadeo'})
            </p>
        </div>
    {else}
        <div class="warn alert alert-danger">
            <p>
                {$smarty.const._PS_VERSION_|escape:'htmlall':'UTF-8'}
                ({l s='incompatible PrestaShop version: %s and later is required' sprintf=[$module->ps_versions_compliancy['min']] mod='sdevadeo'})
            </p>
        </div>
    {/if}
    {* /Prestashop version *}

    <hr />

    <h4>
        {l s='PHP version' mod='sdevadeo'}
    </h4>

    {if version_compare(phpversion(), '5.6', '>=')}
        <div class="conf alert alert-success">
            <p>
                {phpversion()|escape:'htmlall':'UTF-8'}
                ({l s='compatible PHP version' mod='sdevadeo'})
            </p>
        </div>
    {else}
        <div class="warn alert alert-danger">
            <p>
                {phpversion()|escape:'htmlall':'UTF-8'}
                ({l s='incompatible PHP version: %s and later is required' sprintf=[SdevAdeo::PHP_MIN_VERSION] mod='sdevadeo'})
            </p>
        </div>
    {/if}
    {* /PHP version *}

    <hr />

    <h4>
        {l s='PHP variable "max_execution_time"' mod='sdevadeo'}
    </h4>

    {assign var="max_execution_time" value=ini_get('max_execution_time')}

    {if $max_execution_time >= SdevAdeo::PHP_MIN_MAX_EXECUTION_TIME || $max_execution_time == '0'}
        <div class="conf alert alert-success">
            <p>
                {ini_get('max_execution_time')|escape:'htmlall':'UTF-8'}
            </p>
        </div>
    {else}
        <div class="warn alert alert-danger">
            <p>
                {ini_get('max_execution_time')|escape:'htmlall':'UTF-8'}
                ({l s='%s and more is required' sprintf=[SdevAdeo::PHP_MIN_MAX_EXECUTION_TIME|escape:'htmlall':'UTF-8'] mod='sdevadeo'})
            </p>
        </div>
    {/if}
    {* /PHP variable "max_execution_time" *}

    <hr />

    <h4>
        {l s='PHP variable "memory_limit"' mod='sdevadeo'}
    </h4>

    {assign var="memory_limit" value=ini_get('memory_limit')}

    {if
        ($memory_limit|strpos:'M' && $memory_limit|replace:'M':'' >= SdevAdeo::PHP_MIN_MEMORY_LIMIT_MB)
        || ($memory_limit|strpos:'G')
        || $memory_limit == '-1'
    }
        <div class="conf alert alert-success">
            <p>
                {ini_get('memory_limit')|escape:'htmlall':'UTF-8'}
            </p>
        </div>
    {else}
        <div class="warn alert alert-danger">
            <p>
                {ini_get('memory_limit')|escape:'htmlall':'UTF-8'}
                ({l s='%sM and more is required' sprintf=[SdevAdeo::PHP_MIN_MEMORY_LIMIT_MB|escape:'htmlall':'UTF-8'] mod='sdevadeo'})
            </p>
        </div>
    {/if}
    {* /PHP variable "memory_limit" *}

    <hr />

    <h4>
        {l s='PHP variable "max_input_vars"' mod='sdevadeo'}
    </h4>

    <div class="conf alert alert-info">
        <p>
            {ini_get('max_input_vars')|escape:'htmlall':'UTF-8'}
        </p>
    </div>
    {* /PHP variable "max_input_vars" *}

    <hr />

    <h4>
        {l s='License' mod='sdevadeo'}
    </h4>

    <div class="warn alert alert-warning">
        <p>
            <strong>
                {l s='This module is under a commerciale license from ScaleDEV society.' mod='sdevadeo'}
                {l s='Any unauthorized use find yourself liable.' mod='sdevadeo'}
            </strong>
        </p>
    </div>
    {* /License *}

    <hr />

    <h4>
        {l s='Overrides' mod='sdevadeo'}
    </h4>

    {if !$hasOverrides}
        <div class="conf alert alert-success">
            <p>
                {l s='No override detected.'}
            </p>
        </div>
    {else}
        <div class="conf alert alert-info">
            <p>
                {l s='This message is not an error. It is a list that you need to provide if you have a problem with our module.' mod='sdevadeo'}
            </p>
        </div>

        <div class="warn alert alert-warning">
            <p>
                {l s='One or many overrides have been detected. The operation of the module can be altered. If you encounter a problem, it is necessary to specify it in the support request.' mod='sdevadeo'}
            </p>

            <!-- CONTROLLERS OVERRIDES -->
            {if $overridesList.controllers && !empty($overridesList.controllers)}
                <p>
                    {if count($overridesList.controllers) > 1}
                        {l s='Controllers:' mod='sdevadeo'}<br/ >
                    {else}
                        {l s='Controller:' mod='sdevadeo'}<br />
                    {/if}

                    {foreach $overridesList.controllers as $controllerOverride}
                        - {$controllerOverride.path|escape:'htmlall':'UTF-8'}<br />
                    {/foreach}
                </p>
            {/if}
            <!-- /CONTROLLERS OVERRIDES -->

            <!-- CLASSES OVERRIDES -->
            {if $overridesList.classes && !empty($overridesList.classes)}
                <p>
                    {if count($overridesList.classes) > 1}
                        {l s='Classes:' mod='sdevadeo'}<br/ >
                    {else}
                        {l s='Class:' mod='sdevadeo'}<br />
                    {/if}

                    {foreach $overridesList.classes as $classOverride}
                        - {$classOverride.path|escape:'htmlall':'UTF-8'}<br />
                    {/foreach}
                </p>
            {/if}
            <!-- /CLASSES OVERRIDES -->
        </div>
    {/if}
{/block}
