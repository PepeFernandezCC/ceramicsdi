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

{extends file=$smarty.const._PS_MODULE_DIR_|cat:$module->name|cat:'/views/templates/extends/admin/index.tpl'}

{block 'SdevAdeoAdminPageIndexModuleLogo'}
    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/adeo-logo.png"
         alt="{l s='Adeo module logo' mod='sdevadeo'}"
         style="max-height: 400px; max-width: 100%;"
    />
{/block}

{block 'SdevAdeoAdminPageIndexModuleDescription'}
    <p class="text-center">
        {l s='The Adeo Marketplace module is the best solution to exports your products and manage all orders from our DIY Marketplaces (Leroy Merlin, Bricoman...). Can\'t wait to see your products online !' mod='sdevadeo'}
    </p>
{/block}
