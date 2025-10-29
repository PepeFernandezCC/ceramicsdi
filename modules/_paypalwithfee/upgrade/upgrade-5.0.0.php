<?php
/**
 * 2020 4webs
 *
 * DEVELOPED By 4webs.es Prestashop Platinum Partner
 *
 * @author    4webs
 * @copyright 4webs 2019
 * @license   4webs
 * @version 5.1.4
 * @category payment_gateways
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_0_0()
{
    return Db::getInstance()
    ->Execute(
        '
        ALTER TABLE `' . _DB_PREFIX_ . 'ppwf_order`
            ADD payer_id varchar(128) NULL
                AFTER transaction_id;

        ALTER TABLE `' . _DB_PREFIX_ . 'ppwf_order`
            ADD seller_protection int(1) NULL
                AFTER payer_id;

        ALTER TABLE `' . _DB_PREFIX_ . 'ppwf_order`
            ADD customer_data LONGTEXT NULL
                AFTER id_shop;
        '
    );
}
