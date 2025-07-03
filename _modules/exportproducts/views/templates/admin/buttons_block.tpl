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

<div class="footer_product_export">
    <div class="button_export_prev">
        <a class="mpm_button_prev"><i class="mic-chevron-left-solid"></i></a>
    </div>
    <div class="button_export">
        <a class="mpm_button_export mpm_button_second">{l s='Export' mod='exportproducts'}</a>
    </div>
    <div class="button_save">
        <a data-id="{if (isset($settings['id_configuration']) && $settings['id_configuration'])}{$settings['id_configuration']|escape:'htmlall':'UTF-8'}{else}0{/if}"
           class="mpm_button_save mpm_button">{l s='Save' mod='exportproducts'}</a>
    </div>
    <div class="button_export_next">
        <a class="mpm_button_next"><i class="mic-chevron-right-solid"></i></a>
    </div>
</div>
<div class="preview_product_export_content">
    <a class="preview_product_export_file"><i
                class="mic-table-solid"></i><span>{l s='PREVIEW TABLE' mod='exportproducts'}</span></a>
</div>