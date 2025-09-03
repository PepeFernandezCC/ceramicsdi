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
<div class="new_export_tabs">
    <div class="new_export_tab_item new_export_tab_item_first">
        <a data-tab="general_settings"
           class="new_export_tab_button selected {if $new_export_tab == 'general_settings'}selected{else}active{/if}">
            <span><i class="mic-check-mark"></i></span>
            <span class="tab_point"></span>
            <span class="new_export_tab_label">{l s='General Settings' mod='exportproducts'}</span>
        </a>
    </div>
    <div class="new_export_tab_item">
        <a data-tab="filter_product"
           class="new_export_tab_button {if $new_export_tab == 'filter_product'}selected{elseif $new_export_tab == 'general_settings'}{else}active{/if}">
            <span><i class="mic-check-mark"></i></span>
            <span class="tab_point"></span>
            <span class="new_export_tab_label">{l s='Filter Products' mod='exportproducts'}</span>
        </a>
    </div>
    <div class="new_export_tab_item">
        <a data-tab="filter_product_fields"
           class="new_export_tab_button {if $new_export_tab == 'filter_product_fields'}selected{elseif $new_export_tab == 'general_settings' || $new_export_tab == 'filter_product'}{else}active{/if}">
            <span><i class="mic-check-mark"></i></span>
            <span class="tab_point"></span>
            <span class="new_export_tab_label">{l s='Filter Fields' mod='exportproducts'}</span>
        </a>
    </div>
    <div class="clear_both"></div>
</div>