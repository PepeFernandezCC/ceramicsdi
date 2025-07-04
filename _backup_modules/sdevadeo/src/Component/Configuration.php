<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

namespace Scaledev\Adeo\Component;

use Configuration as PsConfiguration;
use Scaledev\Adeo\Exception\TooLongConfigNameException;


/**
 * Class Configuration
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Configuration
{
    const API_KEY = 'API_KEY';
    const API_ENV = 'API_ENV';
    const ENABLED_DISCOUNT = 'ENABLED_DISCOUNT';
    const ENABLED_SALES = 'ENABLED_SALES';
    const USED_DESCRIPTION = 'USED_DESCRIPTION';
    const PRODUCT_BURST = 'PRODUCT_BURST';
    const IMPORTED_STATE = 'IMPORTED_STATE';
    const SHIPPED_STATE = 'SHIPPED_STATE';
    const SHIPPING_CRON = 'SHIPPING_CRON';
    const LAST_SHIPPING = 'LAST_SHIPPING';
    const SHIPPING_COST = 'SHIPPING_COST';
    const API_SHIPPING_METHODS = 'API_METHODS';
    const DATE_UPD_METHOD = 'DATE_UPD_METHOD';
    const EXCL_MANUFACTURER = 'EXCL_MANUFACT';
    const EXCL_SUPPLIER = 'EXCL_SUPPLIER';
    const AUTO_VALIDATE = 'AUTO_VALIDATE';
    const DISABLED_PRODUCT = 'DISABLED_PRODUCT';
    const DISABLED_CAT = 'DISABLED_CAT';
    const MODULE_TOKEN = 'TOKEN';
    const DATE_PRODUCT_FLOW = 'DATE_PROD_FLOW';
    const DATE_OFFER_FLOW = 'DATE_OFFER_FLOW';
    const DATE_PRODUCT_REPORT = 'DATE_PROD_REP';
    const DATE_OFFER_REPORT = 'DATE_OFFER_REP';
    const LAST_SHOP_INFO_IMPORT_DATE = 'LAST_SHOP_INFO';
    const SHOP_CHANNELS = 'SHOP_CHANNELS';
    const PRODUCT_FLOW_IN_PROGRESS = 'PRODUCT_PROGRESS';
    const OFFER_FLOW_IN_PROGRESS = 'OFFER_PROGRESS';
    const DEFAULT_FLOW_TYPE = 'FLOW_TYPE';
    const LAST_ORDER_UPDATE_DATE = 'ORDER_UPDATE';
    const ENABLED_COUNTRIES = 'ENABLED_COUNTRIES';
    const SHIPPING_COUNTRY = 'SHIPPING_COUNTRY';
    const TAX_MAPPING = 'TAX_MAPPING';

    /**
     * Gets the configuration's name prefix.
     *
     * @return string
     */
    public static function getPrefix()
    {
        return 'SDEVADEO_';
    }

    /**
     * Gets a configuration's complete name.
     *
     * @param string $configName
     * @return string
     */
    public static function getCompleteName($configName)
    {
        $configName = strtoupper($configName);

        if (substr($configName, 0, strlen(self::getPrefix())) == self::getPrefix()) {
            return $configName;
        }

        return self::getPrefix().$configName;
    }

    /**
     * Checks the configuration's name length.
     *
     * @param $configName
     * @return void
     * @throws TooLongConfigNameException
     */
    public static function checkKeyLength($configName)
    {
        if (strlen($configName) > 32) {
            throw new TooLongConfigNameException($configName);
        }
    }

    /**
     * Gets the value of a configuration.
     *
     * @param string $configName
     * @param int $languageId
     * @param int $shopGroupId
     * @param int $shopId
     * @return false|string
     * @throws TooLongConfigNameException
     */
    public static function getValue(
        $configName,
        $languageId = null,
        $shopGroupId = null,
        $shopId = null
    ) {
        $configName = self::getCompleteName($configName);

        self::checkKeyLength($configName);

        return PsConfiguration::get(
            $configName,
            $languageId,
            $shopGroupId,
            $shopId
        );
    }

    /**
     * Updates the value of a configuration.
     *
     * @param string $configName
     * @param mixed $values This is an array if the configuration is multilingual, a single string else.
     * @param bool $html Specify if html is authorized in value.
     * @param int $shopGroupId
     * @param int $shopId
     * @return bool
     * @throws TooLongConfigNameException
     */
    public static function updateValue(
        $configName,
        $values,
        $html = false,
        $shopGroupId = null,
        $shopId = null
    ) {
        $configName = self::getCompleteName($configName);

        self::checkKeyLength($configName);

        return PsConfiguration::updateValue(
            $configName,
            $values,
            $html,
            $shopGroupId,
            $shopId
        );
    }

    /**
     * Deletes a configuration from database.
     *
     * @param string $configName
     * @return bool
     * @throws TooLongConfigNameException
     */
    public static function delete($configName)
    {
        $configName = self::getCompleteName($configName);

        self::checkKeyLength($configName);

        return PsConfiguration::deleteByName($configName);
    }

}
