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

<div class="google-category-assoc-container" data-id-category="{$shop_category_id|escape:'htmlall':'UTF-8'}">
    <div class="shop-category">{$shop_category_name|escape:'htmlall':'UTF-8'}</div>
    <div class="mpm-fpe-select-wrapper fixed-search-enabled">
        <img src="{$img_folder|escape:'htmlall':'UTF-8'}google_logo.png">
        <select class="google-category">
            {if !empty($google_category_options)}
                {foreach $google_category_options as $google_category_option}
                    <option value="{$google_category_option['id']|escape:'htmlall':'UTF-8'}">{$google_category_option['title']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            {/if}
        </select>
    </div>
</div>