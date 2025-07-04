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
<div class="success_form">
    <div class="animateErrorIcon"><i class="mic-check-mark"></i></div>
    <div class="header_success_form">{l s='Perfectly!' mod='exportproducts'}</div>
    <div class="success_messages">
        {foreach  $messages as $message}
            <span>{$message|escape:'htmlall':'UTF-8'|unescape}</span>
        {/foreach}
        {if isset($file_for_download) && $file_for_download}
            <span> <a download class="download_file_link" href="{$file_for_download|escape:'htmlall':'UTF-8'}"> <i
                            class="mic-download-solid"></i> {l s='Download Exported File' mod='exportproducts'} </a> </span>
        {/if}
    </div>
    <a class="button_ok"
       {if isset($link) && $link}href="{$link|escape:'htmlall':'UTF-8'}" {/if}>{l s='Ok' mod='exportproducts'}</a>
</div>