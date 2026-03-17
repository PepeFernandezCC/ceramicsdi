<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Revolut
 * @copyright Since 2020 Revolut
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

trait RevolutDatabaseHelper
{
    public function getRevolutOrder($id_order)
    {
        $result = Db::getInstance()->getRow(
            'SELECT id as record_id, UNHEX(`id_revolut_order`) as id_revolut_order, id_order, UNHEX(`public_id`) as public_id, shipments_synced FROM `' . _DB_PREFIX_ . 'revolut_payment_orders`'
            . ' WHERE `id_order` = ' . (int) $id_order
        );

        if (!empty(Db::getInstance()->getMsgError())) {
            PrestaShopLogger::addLog('getRevolutOrder SQL_ERROR: ' . Db::getInstance()->getMsgError(), 3);
        }

        return $result;
    }

    public function getRevolutOrderByPublicId($public_id)
    {
        $result = Db::getInstance()->getRow(
            'SELECT id as record_id, UNHEX(`id_revolut_order`) as id_revolut_order, id_cart, id_order'
            . ' FROM `' . _DB_PREFIX_ . 'revolut_payment_orders`'
            . ' WHERE UNHEX(`public_id`) LIKE "' . pSQL($public_id) . '"'
        );

        if (!empty(Db::getInstance()->getMsgError())) {
            PrestaShopLogger::addLog('getRevolutOrderByPublicId SQL_ERROR: ' . Db::getInstance()->getMsgError(), 3);
        }

        return $result;
    }

    public function getRevolutOrderByIdCart($id_cart, $payment_method = '')
    {
        $sql = 'SELECT id as record_id, UNHEX(`id_revolut_order`) as id_revolut_order, id_order, UNHEX(`public_id`) as public_id 
            FROM `' . _DB_PREFIX_ . 'revolut_payment_orders`
            WHERE `id_cart` = ' . (int) $id_cart . " AND `payment_method` = '" . pSQL($payment_method) . "'";

        $result = Db::getInstance()->getRow($sql);

        if (!empty(Db::getInstance()->getMsgError())) {
            PrestaShopLogger::addLog('getRevolutOrderByIdCart SQL_ERROR: ' . Db::getInstance()->getMsgError(), 3);
        }

        return $result;
    }

    public function removeRevolutOrderByIdCart($id_cart, $payment_method = '')
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'revolut_payment_orders` 
            WHERE `id_cart` = ' . (int) $id_cart . " AND `payment_method` = '" . pSQL($payment_method) . "'";

        $result = Db::getInstance()->execute($sql);

        return $result;
    }

    public function updatePsOrderIdRecord($id_new_order, $payment_method, $revolut_order_db_record_id)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'revolut_payment_orders`
        SET `id_order` = %d, `save_card` = 0, `payment_method` = \'%s\'
        WHERE `id` = %d';

        $result = Db::getInstance()->execute(sprintf(
            $sql,
            (int) $id_new_order,
            pSQL($payment_method),
            (int) $revolut_order_db_record_id
        ));

        if (!empty(Db::getInstance()->getMsgError())) {
            PrestaShopLogger::addLog('updatePsOrderIdRecord SQL_ERROR: ' . Db::getInstance()->getMsgError(), 3);
        }

        return $result;
    }

    public function setShipmentsSynced($id_order)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'revolut_payment_orders` SET `shipments_synced` = 1 WHERE `id_order` = %d';

        $result = Db::getInstance()->execute(sprintf(
            $sql,
            (int) $id_order
        ));

        if (!empty(Db::getInstance()->getMsgError())) {
            PrestaShopLogger::addLog('setShipmentsSynced SQL_ERROR: ' . Db::getInstance()->getMsgError(), 3);
        }

        return $result;
    }

    public function getOrderCurrentState($id_order)
    {
        $result = Db::getInstance()->getValue('SELECT current_state FROM `' . _DB_PREFIX_ . 'orders` 
            WHERE id_order=' . (int) $id_order);
        if (!empty(Db::getInstance()->getMsgError())) {
            PrestaShopLogger::addLog('getOrderCurrentState SQL_ERROR: ' . Db::getInstance()->getMsgError(), 3);
        }

        return $result;
    }

    public function createRevolutPaymentRecord($id_revolut_order, $public_id, $id_cart, $payment_method = '')
    {
        $result = Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'revolut_payment_orders` (`id_revolut_order`, `public_id`, `id_cart`, `payment_method`, `shipments_synced`)'
            . ' VALUES (HEX("' . pSQL($id_revolut_order) . '"), HEX("' . pSQL($public_id) . '"), "' . (int) $id_cart . '", "' . pSQL($payment_method) . '", "0")'
        );

        if (!empty(Db::getInstance()->getMsgError())) {
            PrestaShopLogger::addLog('createRevolutPaymentRecord SQL_ERROR: ' . Db::getInstance()->getMsgError(), 3);
        }

        return $result;
    }
}
