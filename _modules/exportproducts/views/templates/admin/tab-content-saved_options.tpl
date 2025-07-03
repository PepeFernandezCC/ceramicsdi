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
<div class="tab_content tab_content_settings">
    <div class="saved_settings_header">
        <div class="saved_settings_label">{l s='Saved options' mod='exportproducts'}</div>
        <label for="upload_settings" class="import_new_settings"><i
                    class="mic-cloud-upload-alt-solid"></i>{l s='Import Options' mod='exportproducts'} <input
                    type="file" name="upload_settings" id="upload_settings" class="upload_settings"></label>
    </div>
    <div class="saved_settings_content {if isset($settings_list) && $settings_list}active{/if}">
        {include file="{$path_tpl|escape:'htmlall':'UTF-8'}list-saved-configuration.tpl" product_reports=$settings_list setting_url=$setting_url}
    </div>
    <div class="no_saved_settings {if !isset($settings_list) || !$settings_list}active{/if}">
        <div class="no_last_reports">
            <div class="no_last_reports_img">
                <img src="{$img_folder|escape:'htmlall':'UTF-8'}svg/letter_empty.svg">
            </div>
            <div class="no_last_reports_descr">
                <span>{l s='There is no saved options yet. ' mod='exportproducts'}</span>
                <span>{l s='Let’s start a new exporting process or import saved settings from other store.' mod='exportproducts'}</span>
            </div>
        </div>
    </div>
</div>