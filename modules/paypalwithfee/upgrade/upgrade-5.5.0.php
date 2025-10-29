<?php
/**
 * 2020 4webs
 *
 * DEVELOPED By 4webs.es Prestashop Platinum Partner
 *
 * @author    4webs
 * @copyright 4webs 2019
 * @license   4webs
 * @version 5.5.0
 * @category payment_gateways
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_5_0()
{
    return Db::getInstance()
    ->Execute(
        '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ppwf_hash` (
            `id_ppwf_hash` int(11) NOT NULL AUTO_INCREMENT,
            `id_cart` int(11) NOT NULL,
            `hash_cart` varchar(128) NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_ppwf_hash`),
            UNIQUE KEY `id_cart` (`id_cart`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        '
    );
}
