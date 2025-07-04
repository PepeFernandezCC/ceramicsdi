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

 $query = "SELECT count(name) as c FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_configuration WHERE name='ShowShippingStatusProcess'";
 $record = Db::getInstance()->getRow($query);
 if (isset($record['c']) && $record['c'] == 0) {

    $CREATE = "CREATE TABLE IF NOT EXISTS ";
    $INSERT = "INSERT INTO ";
    $UPDATE = "UPDATE ";
    $DELETE = "DELETE FROM ";

    $sql = array();

    // Actualización para versiones menores que 1.2.0.0, si no existe ShowShippingStatusProcess
    $sql[] = $DELETE . _DB_PREFIX_ . "order_state WHERE id_order_state>800 AND id_order_state<=804;";
    $sql[] = $DELETE . _DB_PREFIX_ . "order_state_lang WHERE id_order_state>800 AND id_order_state<=804;";

    $sql[] = $INSERT . _DB_PREFIX_ . "correos_oficial_configuration (name, value, type) VALUES ('ShowShippingStatusProcess', NULL, 'checkbox');";
    $sql[] = $INSERT . _DB_PREFIX_ . "correos_oficial_configuration (name, value, type) VALUES ('ShipmentPreregistered', '900', 'select');";
    $sql[] = $INSERT . _DB_PREFIX_ . "correos_oficial_configuration (name, value, type) VALUES ('ShipmentInProgress', '904', 'select');";
    $sql[] = $INSERT . _DB_PREFIX_ . "correos_oficial_configuration (name, value, type) VALUES ('ShipmentDelivered', '903', 'select');";
    $sql[] = $INSERT . _DB_PREFIX_ . "correos_oficial_configuration (name, value, type) VALUES ('ShipmentCanceled', '901', 'select');";
    $sql[] = $INSERT . _DB_PREFIX_ . "correos_oficial_configuration (name, value, type) VALUES ('ShipmentReturned', '902', 'select');";

    // Actualizar estados de pedido existentes inferiores a la versión 1.2.0.0
    $sql[] = $UPDATE . _DB_PREFIX_ . "order_state_lang SET name = 'Envío preparado para Correos - CEX' WHERE " . _DB_PREFIX_ . "order_state_lang.id_order_state = 900;";
    $sql[] = $UPDATE . _DB_PREFIX_ . "order_state_lang SET name = 'Envío anulado Correos - CEX' WHERE " . _DB_PREFIX_ . "order_state_lang.id_order_state = 901;";
    $sql[] = $UPDATE . _DB_PREFIX_ . "order_state_lang SET name = 'Envío devuelto Correos - CEX' WHERE " . _DB_PREFIX_ . "order_state_lang.id_order_state = 902;";
    $sql[] = $UPDATE . _DB_PREFIX_ . "order_state_lang SET name = 'Envío entregado Correos - CEX' WHERE " . _DB_PREFIX_ . "order_state_lang.id_order_state = 903;";
    $sql[] = $UPDATE . _DB_PREFIX_ . "order_state_lang SET name = 'Envío en curso Correos - CEX' WHERE " . _DB_PREFIX_ . "order_state_lang.id_order_state = 904;";

    // Para versiones menores o igual que 1.0.2.4
    $query = "SELECT count(name) as c FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_configuration WHERE name='CronLastExecutionTime'";

    // Si no existe ShowShippingStatusProcess
    $record = Db::getInstance()->getRow($query);
    if (isset($record['c']) && $record['c'] == 0) {
        $sql[] = $INSERT . _DB_PREFIX_ . "correos_oficial_configuration (name, value, type) VALUES ('CronLastExecutionTime', '1970-01-01 00:00:00', 'datetime');";
    }

    require_once __DIR__ . "/table_ws_status.php";

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) == false) {
            return false;
        }
    }
 }