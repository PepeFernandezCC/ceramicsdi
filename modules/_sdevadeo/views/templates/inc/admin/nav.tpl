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

<nav class="list-group">
    <a class="list-group-item{if $sdevadeoAdminPage == 'index'} active{/if}"
        href="{Context::getContext()->link->getAdminLink('AdminSdevAdeoIndex')}"
    >
        <i class="icon-home"></i>&nbsp;
        {l s='Home' mod='sdevadeo'}
    </a><!-- /.list-group-item -->

    <a class="list-group-item{if $sdevadeoAdminPage == 'info'} active{/if}"
        href="{Context::getContext()->link->getAdminLink('AdminSdevAdeoInfo')}"
    >
        <i class="icon-info"></i>&nbsp;
        {l s='Information' mod='sdevadeo'}
    </a><!-- /.list-group-item -->

    <a class="list-group-item{if $sdevadeoAdminPage == 'authentication'} active{/if}"
        href="{Context::getContext()->link->getAdminLink('AdminSdevAdeoAuthentication')}"
    >
        <i class="icon-user"></i>&nbsp;
        {l s='Authentication' mod='sdevadeo'}
    </a><!-- /.list-group-item -->

    <a class="list-group-item{if $sdevadeoAdminPage == 'parameters'} active{/if}"
        href="{Context::getContext()->link->getAdminLink('AdminSdevAdeoParameters')}"
    >
        <i class="icon-cogs"></i>&nbsp;
        {l s='Parameters' mod='sdevadeo'}
    </a><!-- /.list-group-item -->

    <a class="list-group-item{if $sdevadeoAdminPage == 'categories_rules'} active{/if}"
        href="{Context::getContext()->link->getAdminLink('AdminSdevAdeoCategoriesRules')}"
    >
        <i class="icon-tags"></i>&nbsp;
        {l s='Categories rules' mod='sdevadeo'}
    </a><!-- /.list-group-item -->

    <a class="list-group-item{if $sdevadeoAdminPage == 'categories_mapping'} active{/if}"
        href="{Context::getContext()->link->getAdminLink('AdminSdevAdeoCategoriesMapping')}"
    >
        <i class="icon-sitemap"></i>&nbsp;
        {l s='Categories mapping' mod='sdevadeo'}
    </a><!-- /.list-group-item -->

    <a class="list-group-item{if $sdevadeoAdminPage == 'products_flow'} active{/if}"
        href="{Context::getContext()->link->getAdminLink('AdminSdevAdeoProductsFlow')}"
    >
        <i class="icon-cloud-upload"></i>&nbsp;
        {l s='Products flow' mod='sdevadeo'}
    </a><!-- /.list-group-item -->

    <a class="list-group-item{if $sdevadeoAdminPage == 'orders_flow'} active{/if}"
        href="{Context::getContext()->link->getAdminLink('AdminSdevAdeoOrdersFlow')}"
    >
        <i class="icon-cloud-download"></i>&nbsp;
        {l s='Orders flow' mod='sdevadeo'}
    </a><!-- /.list-group-item -->

    <a class="list-group-item{if $sdevadeoAdminPage == 'scheduled_tasks'} active{/if}"
        href="{Context::getContext()->link->getAdminLink('AdminSdevAdeoScheduledTasks')}"
    >
        <i class="icon-tasks"></i>&nbsp;
        {l s='Scheduled tasks' mod='sdevadeo'}
    </a><!-- /.list-group-item -->
</nav><!-- /.list-group -->
