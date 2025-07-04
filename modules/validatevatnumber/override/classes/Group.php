<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 *  @author    Active Design <office@activedesign.ro>
 *  @copyright 2018 Active Design
 *  @license   LICENSE.txt
 */

class Group extends GroupCore
{
    /**
     * Returns price display method for a group (i.e. price should be including tax or not).
     *
     * @param int $id_group
     *
     * @return int Returns 1 if the our module verified the VAT number.
     */
    public static function getPriceDisplayMethod($id_group)
    {
        if (!isset(Group::$group_price_display_method[$id_group])) {
            self::$group_price_display_method[$id_group] = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                '
                SELECT `price_display_method`
                FROM `' . _DB_PREFIX_ . 'group`
                WHERE `id_group` = ' . (int) $id_group
            );
        }

        if (Tools::getValue('controller') == 'AdminOrders' && Tools::getValue('id_order')) {
            $id_address_invoice = Db::getInstance()->getValue('SELECT `id_address_invoice` FROM `'._DB_PREFIX_.'orders` WHERE `id_order` = "'.Tools::getValue('id_order').'"');
            $check = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                '
                SELECT `vat_number_status`
                FROM `' . _DB_PREFIX_ . 'validatevatnumber`
                WHERE `id_address` = ' . $id_address_invoice
            );
            if ($check) {
                return self::$group_price_display_method[$id_group] = 1;
            }
        }

        return self::$group_price_display_method[$id_group];
    }
}
