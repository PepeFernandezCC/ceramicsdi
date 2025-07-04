<?php
/**
 * 2007-2021 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * In some cases you should not drop the tables.
 * Maybe the merchant will just try to reset the module
 * but does not want to loose all of the data associated to the module.
 */
$sql = array();

$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_configuration';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_senders';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_codes';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_codes_actives';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_orders';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_returns';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_products';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_products_shop';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_carriers_products';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_history';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_saved_orders';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_saved_returns';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_pickups_returns';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_customs_description';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_requests';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'correos_oficial_ws_status';

// Se eliminan estados de pedidos
// $sql[] = "DELETE FROM "._DB_PREFIX_."order_state WHERE id_order_state >=800 AND id_order_state <=904;";
// $sql[] = "DELETE FROM "._DB_PREFIX_."order_state_lang WHERE id_order_state >=800 AND id_order_state <=904;";

// Se eliminan estados de devolución
// $sql[] = "DELETE FROM "._DB_PREFIX_."order_return_state WHERE id_order_return_state=800 OR id_order_return_state=900;";
// $sql[] = "DELETE FROM "._DB_PREFIX_."order_return_state_lang WHERE id_order_return_state=800 OR id_order_return_state=900;";

// Se eliminan transportistas del módulo
$sql[] = 'UPDATE ' . _DB_PREFIX_ . 'carrier SET deleted=1, active=0 WHERE external_module_name="correosoficial"';

foreach ($sql as $query) {
    if (!Db::getInstance()->execute($query)) {
        return false;
    }
}
