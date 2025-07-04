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
<div class="tab_content tab_content_overview">
    <div class="welcome_block">
        <div class="welcome_block_center">
            <div class="welcome_block_img">
                <img src="{$img_folder|escape:'htmlall':'UTF-8'}intro_banner.png">
            </div>
            <div class="welcome_block_content">
                <div class="welcome_text">
                    <div class="welcome_title">{l s='Welcome to the Product Catalog Export Module!' mod='exportproducts'}</div>
                    <div class="welcome_descr">{l s='An updated and augmented Product Catalog Export module will even help you easily export your products in a few steps. Just try it - you will be satisfied!' mod='exportproducts'}</div>
                </div>
            </div>
            <div class="clear_both"></div>
        </div>
    </div>
    <div class="additional_modules_block">
        <div class="title_overview title_overview_modules">
            {l s='Try Additional Modules' mod='exportproducts'}
        </div>
        <div class="additional_modules">
            <div class="additional_module">
                <div><a target="_blank" href="https://addons.prestashop.com/en/data-import-export/19091-product-catalog-csv-excel-import.html"><img src="{$img_folder|escape:'htmlall':'UTF-8'}import_banner.png"></a></div>
            </div>
            <div class="additional_module">
                <div><a target="_blank"  href="https://addons.prestashop.com/en/fast-mass-updates/17438-mass-product-quantity-price-update.html"><img src="{$img_folder|escape:'htmlall':'UTF-8'}mass_banner.png"></a></div>
            </div>
            <div class="additional_module">
                <div><a target="_blank"  href="https://addons.prestashop.com/en/data-import-export/86690-orders-csv-excel-import.html"><img src="{$img_folder|escape:'htmlall':'UTF-8'}ordersimport_banner.png"></a></div>
            </div>
            <div class="clear_both"></div>
        </div>
    </div>
    <div class="last_executed_reports_block">
        <div class="title_overview title_overview_reports">{l s='Last Executed Reports' mod='exportproducts'}</div>
        <div class="last_executed_reports">
            {include file="{$path_tpl|escape:'htmlall':'UTF-8'}list-reports.tpl" product_reports=$product_reports setting_url=$setting_url}
            <div class="no_last_executed_reports {if !isset($product_reports) || !$product_reports}active{/if}">
                <div class="no_last_reports">
                    <div class="no_last_reports_img">
                        <img src="{$img_folder|escape:'htmlall':'UTF-8'}svg/no_data_loup.svg">
                    </div>
                    <div class="no_last_reports_descr">
                        {l s='There is no data information yet. Looks like you have not export products at this time.' mod='exportproducts'}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="demo_files_block">
        <div class="title_overview title_overview_demo_files">
            {l s=' Quick Start & Demo Files' mod='exportproducts'}
        </div>
        <div class="demo_files_links">
            <div class="demo_file">
                <a target="_blank"
                   href="http://faq.myprestamodules.com/product-catalog-csv-excel-xml-export-pro/automatic-products-export-configuration-guide.html"
                   class="demo_file_url">
                    <div class="demo_file_left_column"><i class="mic-life-ring-regular"></i></div>
                    <div class="demo_file_center_column">
                        <div class="demo_file_descr">{l s='Read Instructions' mod='exportproducts'}</div>
                        <div class="demo_file_title">{l s='Automatic Products Export' mod='exportproducts'}</div>
                    </div>
                    <div class="demo_file_right_column"><i class="mic-arrow-right-solid"></i></div>
                    <div class="clear_both"></div>
                </a>
            </div>
            <div class="demo_file">
                <a target="_blank"
                   href="http://faq.myprestamodules.com/product-catalog-csv-excel-xml-export-pro/how-to-use-custom-formulas.html"
                   class="demo_file_url">
                    <div class="demo_file_left_column"><i class="mic-life-ring-regular"></i></div>
                    <div class="demo_file_center_column">
                        <div class="demo_file_descr">{l s='Read Instructions' mod='exportproducts'}</div>
                        <div class="demo_file_title">{l s='Using Custom Formulas' mod='exportproducts'}</div>
                    </div>
                    <div class="demo_file_right_column"><i class="mic-arrow-right-solid"></i></div>
                    <div class="clear_both"></div>
                </a>
            </div>
            <div class="demo_file">
                <a target="_blank"
                   href="http://faq.myprestamodules.com/product-catalog-csv-excel-xml-export-pro/export-only-not-exported-products-.html"
                   class="demo_file_url">
                    <div class="demo_file_left_column"><i class="mic-life-ring-regular"></i></div>
                    <div class="demo_file_center_column">
                        <div class="demo_file_descr">{l s='Read Instructions' mod='exportproducts'}</div>
                        <div class="demo_file_title">{l s='Export not exported products' mod='exportproducts'}</div>
                    </div>
                    <div class="demo_file_right_column"><i class="mic-arrow-right-solid"></i></div>
                    <div class="clear_both"></div>
                </a>
            </div>
            <div class="clear_both"></div>
        </div>
    </div>
</div>