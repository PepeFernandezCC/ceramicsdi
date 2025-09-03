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
<div class="tab_content">
    <div class="new_export_tab_content">
        <div data-tab="general_settings" data-prev-tab="general_settings" data-next-tab="filter_product"
             class="new_export_tab general_settings_tab active">
            <div class="new_export_tab_header">
                <div class="new_export_header_title">{l s='General Settings & Features' mod='exportproducts'}</div>
                <div class="new_export_header_descr">{l s='It includes general export settings-such as the type of export, language, file extension, and also allows you to save the template for future use. ' mod='exportproducts'}</div>
                <div class="new_export_header_tabs">
                    {include file="{$path_tpl|escape:'htmlall':'UTF-8'}new_export_tabs.tpl"  new_export_tab='general_settings'}
                </div>
            </div>
            <div class="general_settings_tab_content">
                {include file="{$path_tpl|escape:'htmlall':'UTF-8'}general_settings.tpl"}
            </div>
        </div>
        <div data-tab="filter_product" data-next-tab="filter_product_fields" data-prev-tab="general_settings"
             class="new_export_tab filter_product_tab">
            <div class="new_export_tab_header">
                <div class="new_export_header_title">{l s='Filter by Products' mod='exportproducts'}</div>
                <div class="new_export_header_descr">{l s='Select exporting options and collect data by filtering your store products. Easy to customizing the data needed.' mod='exportproducts'}</div>
                <div class="new_export_header_tabs">
                    {include file="{$path_tpl|escape:'htmlall':'UTF-8'}new_export_tabs.tpl"  new_export_tab='filter_product'}
                </div>
            </div>
            <div class="filter_product_tab_content">
                {include file="{$path_tpl|escape:'htmlall':'UTF-8'}filter_product.tpl"}
            </div>
        </div>
        <div data-tab="filter_product_fields" data-prev-tab="filter_product"
             class="new_export_tab filter_product_fields_tab">
            <div class="new_export_tab_header">
                <div class="new_export_header_title">{l s='Filter by Products Fields' mod='exportproducts'}</div>
                <div class="new_export_header_descr">{l s='It includes general export settings-such as the type of export, language, file extension, and also allows you to save the template for future use. ' mod='exportproducts'}</div>
                <div class="new_export_header_tabs">
                    {include file="{$path_tpl|escape:'htmlall':'UTF-8'}new_export_tabs.tpl"  new_export_tab='filter_product_fields'}
                </div>
            </div>
            <div class="filter_product_fields_tab_content">
                {include file="{$path_tpl|escape:'htmlall':'UTF-8'}filter_product_fields.tpl"}
            </div>
        </div>
    </div>
</div>