<?php
/**
* 2007-2017 PrestaShop
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
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_0_1($module)
{

    Configuration::updateValue('VALIDATEVATNUMBER_DEFAULT_GROUP', 3);
    Configuration::updateValue('VALIDATEVATNUMBER_COUNTRY_ID', 0);
    Configuration::updateValue('VALIDATEVATNUMBER_ACCEPTED_GROUP', '');
    Configuration::updateValue('VALIDATEVATNUMBER_ADMINNOTIFY', 0);
    Configuration::updateValue('VALIDATEVATNUMBER_ADMINMAILS', '');
    Configuration::updateValue('VALIDATEVATNUMBER_USERSNOTIFY', 0);

    try {
        $module->installOverrides();
    } catch (Exception $e) {
        //$module->uninstallOverrides();
        //return false;
        // If this failed, most certainly the overrides are already installed, so we still return true;
    }
    $return = '';
    $return &= $module->addBackOfficeControllers() &&
        $module->registerHook('actionObjectCustomerAddAfter') &&
        $module->unregisterHook('validateCustomerFormFields') &&
        $module->unregisterHook('displayCustomerAccount');

    return $return;
}
