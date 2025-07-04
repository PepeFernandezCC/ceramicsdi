<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_'))
    exit;

class EtsAbancartUnsubscribers extends ObjectModel
{
    public $id_customer;
    public static $definition = array(
        'table' => 'ets_abancart_unsubscribers',
        'primary' => 'id_customer',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public static function isUnsubscribe($id_customer)
    {
        return (bool)Db::getInstance()->getValue('SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` WHERE id_customer = ' . (int)$id_customer);
    }

    public static function setCustomerUnsubscribe($id_customer)
    {
        return Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_abancart_unsubscribers` VALUES (' . (int)$id_customer . ', \'' . pSQL(date('Y-m-d H:i:s')) . '\') ON DUPLICATE KEY UPDATE `date_add`=\'' . pSQL(date('Y-m-d H:i:s')) . '\'');
    }

    public static function isSubscribeByEmail($email)
    {
        if (trim($email) == '' || !Validate::isEmail($email))
            return false;
        return Db::getInstance()->getValue('SELECT `email` FROM `' . _DB_PREFIX_ . (version_compare(_PS_VERSION_, '1.7', '>=') ? 'emailsubscription' : 'newsletter') . '` WHERE `email`=\'' . pSQL(trim($email)) . '\'');
    }
}