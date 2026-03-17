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
function upgrade_module_2_9_1(RevolutPayment $module)
{
    $apiKeyLive = Configuration::get('REVOLUT_P_APIKEY_LIVE');
    $apiKeyPublicLive = Configuration::get('REVOLUT_MERCHANT_PUBLIC_TOKEN_live');

    $apiKeySandbox = Configuration::get('REVOLUT_P_APIKEY');
    $apiKeyPublicSandbox = Configuration::get('REVOLUT_MERCHANT_PUBLIC_TOKEN_sandbox');

    $isSandBoxMode = Configuration::get('REVOLUT_P_SANDBOX');

    if ($isSandBoxMode) {
        Configuration::updateValue($module::$CONFIGURATION_API_MODE_IDENTIFIER, 'sandbox');
    } else {
        Configuration::updateValue($module::$CONFIGURATION_API_MODE_IDENTIFIER, 'live');
    }

    // insert new keys
    Configuration::updateValue($module::$CONFIGURATION_SECRET_API_KEY_IDENTIFIER . 'live', $apiKeyLive);
    Configuration::updateValue($module::$CONFIGURATION_PUBLIC_API_KEY_IDENTIFIER . 'live', $apiKeyPublicLive);

    Configuration::updateValue($module::$CONFIGURATION_SECRET_API_KEY_IDENTIFIER . 'sandbox', $apiKeySandbox);
    Configuration::updateValue($module::$CONFIGURATION_PUBLIC_API_KEY_IDENTIFIER . 'sandbox', $apiKeyPublicSandbox);

    // delete old keys
    Configuration::deleteByName('REVOLUT_P_APIKEY_LIVE');
    Configuration::deleteByName('REVOLUT_P_APIKEY');
    Configuration::deleteByName('REVOLUT_P_SANDBOX');
    Configuration::deleteByName('REVOLUT_MERCHANT_PUBLIC_TOKEN_live');
    Configuration::deleteByName('REVOLUT_MERCHANT_PUBLIC_TOKEN_sandbox');

    return true;
}
