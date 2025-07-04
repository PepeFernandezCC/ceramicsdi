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
function upgrade_module_3_0_4()
{
    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_abancart_reminder_lang` ADD `discount_name` VARCHAR(255) NOT NULL AFTER `content`');
    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_abancart_reminder` CHANGE `day` `day` FLOAT NULL DEFAULT NULL, CHANGE `hour` `hour` FLOAT NULL DEFAULT NULL');
    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_abancart_campaign`
          DROP `is_future_cart`,
          DROP `cart_days_old`
    ');

    return true;
}