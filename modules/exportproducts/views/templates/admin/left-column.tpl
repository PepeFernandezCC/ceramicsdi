{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="product_export_tabs">
    <ul class="export_tabs_list">
        <li onclick="" data-tab="overview"
            class="export_tab_item tab_overview {if $tab == 'overview'}active{/if}">{l s='Overview' mod='exportproducts'}
            <span></span></li>
        <li data-tab="new_export"
            class="export_tab_item tab_new_export {if $tab == 'new_export'}active{/if}">{l s='New Export' mod='exportproducts'}
            <span></span></li>
        <li data-tab="scheduled_tasks"
            class="export_tab_item tab_scheduled_tasks {if $tab == 'scheduled_tasks'}active{/if}">{l s='Scheduled Tasks' mod='exportproducts'}
            <span>{$count_scheduled_tasks|escape:'htmlall':'UTF-8'}</span></li>
        <li data-tab="saved_options"
            class="export_tab_item tab_saved_options {if $tab == 'saved_options'}active{/if}">{l s='Saved Options' mod='exportproducts'}
            <span>{$count_saved_options|escape:'htmlall':'UTF-8'}</span></li>
        <li data-tab="history"
            class="export_tab_item tab_history {if $tab == 'history'}active{/if}">{l s='History' mod='exportproducts'}
            <span>{$count_history|escape:'htmlall':'UTF-8'}</span></li>
        <li data-tab="history"
            class="export_tab_item tab_history">
            <a target="_blank" href="https://addons.prestashop.com/en/data-import-export/19091-product-catalog-csv-excel-import.html">{l s='Product Catalog (CSV, Excel) Import' mod='exportproducts'}</a>
            </li>
        <div class="clear_both"></div>
    </ul>
</div>
<div class="product_export_tab_content">
    {$tab_content|escape:'htmlall':'UTF-8'|unescape}
</div>