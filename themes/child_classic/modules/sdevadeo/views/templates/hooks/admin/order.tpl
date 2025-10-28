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

{if $ps_version < 1.6}
    <br/>
    <fieldset>
        <legend>{l s='Adeo' mod='sdevadeo'}</legend>
        <p style="text-align: center;">
            <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/logos/adeo-logo.png" alt="" width="200" height="auto">
        </p>
        {l s='Order reference :' mod='sdevadeo'} <strong>{$mp_order_id|escape:'htmlall':'UTF-8'}</strong>
    </fieldset>
{else}
    <div class="col-md-6">
        <div class="panel">
            <div class="panel-heading">
                {l s='Adeo' mod='sdevadeo'}
            </div>
            <div class="panel-body">
                <p style="text-align: center">
                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/logos/adeo-logo.png" alt="" width="200" height="auto">
                </p>
                <p>
                    <strong>{l s='Order reference :' mod='sdevadeo'}</strong> {$mp_order_id|escape:'htmlall':'UTF-8'}
                </p>
            </div>
        </div>
    </div>
{/if}