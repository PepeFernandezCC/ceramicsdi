<?php
/**
* WhatsApp Chat
*
* ISC License
*
* Copyright (c) 2023 idnovate.com
* idnovate is a Registered Trademark & Property of idnovate.com, innovación y desarrollo SCP
*
* Permission to use, copy, modify, and/or distribute this software for any
* purpose with or without fee is hereby granted, provided that the above
* copyright notice and this permission notice appear in all copies.
*
* THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
* REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
* AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
* INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
* LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
* OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
* PERFORMANCE OF THIS SOFTWARE.
*
* @author    idnovate
* @copyright 2024 idnovate
* @license   https://www.isc.org/licenses/ https://opensource.org/licenses/ISC ISC License
*/

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_1_0_2($module)
{
    $result = true;

    $result &= Db::getInstance()->execute(
        'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'whatsappchatblock` (
          `id_whatsappchatblock` int(11) NOT NULL AUTO_INCREMENT,
          `id_shop` int(10) NOT NULL,
          `id_hook` varchar(150) NOT NULL,
          `open_chat` tinyint(1) NOT NULL,
          `position` varchar(150) NOT NULL,
          PRIMARY KEY (`id_whatsappchatblock`),
          KEY `id_shop_id_hook` (`id_shop`,`id_hook`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
    );

    $result &= Db::getInstance()->execute(
        'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'whatsappchatblock_lang` (
          `id_whatsappchatblock` int(11) NOT NULL,
          `id_lang` int(11) NOT NULL,
          `message` text,
          PRIMARY KEY (`id_whatsappchatblock`,`id_lang`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
    );

     $result &= $module->registerHook('header')
        && $module->registerHook('footer')
        && $module->registerHook('leftColumn')
        && $module->registerHook('rightColumn')
        && $module->registerHook('top')
        && $module->registerHook('home')
        && $module->registerHook('shoppingCart')
        && $module->registerHook('shoppingCartExtra')
        && $module->registerHook('paymentTop')
        && $module->registerHook('beforeCarrier')
        && $module->registerHook('customerAccount')
        && $module->registerHook('myAccountBlock')
        && $module->registerHook('orderConfirmation')
        && $module->registerHook('orderDetail');

    if (version_compare(_PS_VERSION_, '1.5', '>=')) {
        $result &= $module->registerHook('displayBanner')
            && $module->registerHook('displayTopColumn')
            && $module->registerHook('displayNav')
            && $module->registerHook('displayproductButtons')
            && $module->registerHook('displayLeftColumnProduct')
            && $module->registerHook('displayRightColumnProduct')
            && $module->registerHook('displayFooterProduct')
            && $module->registerHook('displayShoppingCartFooter')
            && $module->registerHook('displayCustomerAccountForm')
            && $module->registerHook('displayCustomerAccountFormTop')
            && $module->registerHook('displayCustomerIdentityForm')
            && $module->registerHook('displayMyAccountBlockfooter')
            && $module->registerHook('displayMaintenance');
    } else {
        $result &= $module->registerHook('extraLeft')
            && $module->registerHook('extraRight')
            && $module->registerHook('productActions')
            && $module->registerHook('productfooter');
    }

    $result &= Db::getInstance()->execute(
        "INSERT INTO `"._DB_PREFIX_."whatsappchatblock` (`id_whatsappchatblock`, `id_shop`, `id_hook`, `open_chat`, `position`)
        VALUES (1, 1, 'badge', 1, 'bottom-right')"
    );

    $result &= $module->installTabs();

    return $result;
}
