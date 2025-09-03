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
<div class="errors_form">
    <div class="animateErrorIcon">
        <div class="animateXMark">
            <span class="icon_left"></span>
            <span class="icon_right"></span>
        </div>
    </div>
    <div class="header_error_form">
        {l s='Error' mod='exportproducts'}
    </div>
    <div class="errors_messages">
        {if $errors}
            {foreach  $errors as $error}
                <span>{$error|escape:'htmlall':'UTF-8'|unescape}</span>
            {/foreach}
        {/if}
        {if isset($file_error_log) && $file_error_log}
            <span> <a download class="download_file_link download_file_error"
                      href="{$file_error_log|escape:'htmlall':'UTF-8'}"> <i
                            class="mic-download-solid"></i> {l s='Download Error File' mod='exportproducts'} </a> </span>
        {/if}

    </div>
    <button>{l s='Ok' mod='exportproducts'}</button>
</div>