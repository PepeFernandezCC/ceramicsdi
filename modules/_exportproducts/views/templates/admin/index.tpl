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
<div class="exportproducts_form" data-is-active-export-tab="" data-img-folder="{$img_folder|escape:'htmlall':'UTF-8'}">
    <div class="exportproducts_status_export">
        <div class="exportproducts_status_export_form">
            <i class="mic-times-solid close-status-form"></i>
            <div class="header_status_export_form">{l s='Export is running' mod='exportproducts'}</div>
            <div class="descriptions_status_export_form">
                <span>{l s='Exporting may take some time.' mod='exportproducts'}</span>
                <span>{l s='You can close this window and download' mod='exportproducts'}</span>
                <span>{l s='the exported file later.' mod='exportproducts'}</span>
            </div>
            <div class="progress_product_export">
                <div class="progress_bar_product_export"></div>
            </div>
            <div class="label_progress_product_export">{l s='EXPORT PROGRESS:' mod='exportproducts'}</div>
            <div class="count_progress_product_export">
                <div data-label="{l s='Status Pending ...' mod='exportproducts'}" class="export_product_notification">{l s='Status Pending ...' mod='exportproducts'}</div>
                <div class="export_product_time_start"></div>
                <div class="export_product_time_now"></div>
            </div>
            <div class="stop-current-export-process" data-id-export-process="">
                {l s='STOP' mod='exportproducts'}
            </div>
        </div>
    </div>

    <div class="exportproducts_overflow"></div>
    <div class="exportproducts_loader ">
        <div class="productexport_loader_overflow"></div>
        <div class="productexport_loader"><img src="{$img_folder|escape:'htmlall':'UTF-8'}loader.gif"></div>
    </div>
    <div class="hidden_block hidden">
        <input type="hidden" class="token_product_export" name="token_product_export"
               value="{$token_product_export|escape:'htmlall':'UTF-8'}">
        <input type="hidden" class="id_configuration" name="id_configuration" value="{$id_configuration|escape:'htmlall':'UTF-8'}">
    </div>
    <div class="exportproducts_header">

        <div class="exportproducts_title">
            <img class="logo_myprestamodules" src="../modules/exportproducts/views/img/svg/logo.svg"/>
            <span>{l s='Products Catalog (CSV, Excel, Xml) Export' mod='exportproducts'}</span>
        </div>
        <div class="exportproducts_title">
            <a class="link_to_faqs" href="https://faq.myprestamodules.com/products-catalog-csv-excel-xml-export.html" target="_blank" >
                <i class="mic-life-ring-regular"></i>
                {l s='Can not configure? Visit our FAQs' mod='ordersexport'}
            </a>
        </div>

    </div>
    <div class="exportproducts_left_column">
        <div class="exportproducts_row">
            {$left_column|escape:'htmlall':'UTF-8'|unescape}
        </div>
    </div>
    <div class="exportproducts_right_column">
        <div class="exportproducts_row">
            {$right_column|escape:'htmlall':'UTF-8'|unescape}
        </div>

        <div class="exportproducts_row debug-mode-switch-container" data-is-enabled="{$debug_mode|escape:'htmlall':'UTF-8'}">
            <div class="export_field_label">{l s='DEBUG MODE:' mod='exportproducts'}</div>
            <div class="">
                <div class="switch_myprestamodules">
                    <div class="switch_content">
                        <input type="radio" class="switch-input" name="mpm_pe_debug_mode" value="1" id="mpm_pe_debug_mode_yes"
                        {if isset($debug_mode) && $debug_mode}checked{/if}>
                        <label for="mpm_pe_debug_mode_yes" class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                        <input type="radio" class="switch-input" name="mpm_pe_debug_mode" value="0" id="mpm_pe_debug_mode_no"
                         {if !isset($debug_mode) || (isset($debug_mode) && !$debug_mode)}checked{/if}>
                        <label for="mpm_pe_debug_mode_no" class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                        <span class="switch-selection"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear_both"></div>
</div>