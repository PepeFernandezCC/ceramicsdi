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
require_once(dirname(__FILE__).'/../classes/MM_Config.php');
function upgrade_module_2_1_9($object)
{
    $res = ets_menu_update_configuration_haimotchin($object);
    return $res;
}

if ( ! function_exists('ets_menu_update_configuration_haimotchin') ){
    function ets_menu_update_configuration_haimotchin($object){
        $config = new MM_Config();
        return is_object($object) && $config->installConfigs(true);
    }
}