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

if (!defined('_PS_VERSION_')) {
    exit;
}

use Scaledev\Adeo\Core\ObjectModel\AbstractObjectModel;
use Scaledev\Adeo\Component\Configuration as ScaledevConfiguration;

/**
 * @param $object
 * @return bool
 */
function upgrade_module_1_1_0($object)
{
    return removeMarketplaceCategoryColumn()
        && removeUnusedTables()
        && deleteConfigurationEntries()
        && uninstallTabs()
    ;
}

/**
 * @return bool
 */
function removeMarketplaceCategoryColumn()
{
    return
        Db::getInstance()->execute("ALTER TABLE ".SdevAdeoCategoryRule::getCompleteTableName()." DROP COLUMN marketplaceCategory")
        && Db::getInstance()->execute("ALTER TABLE ".SdevAdeoCarrierRule::getCompleteTableName()." DROP COLUMN shippingDelay")
    ;
}

/**
 * @return bool
 */
function removeUnusedTables()
{
    return Db::getInstance()->execute(
        "DROP TABLE IF EXISTS ".
        _DB_PREFIX_."scaledev_adeo_product_state, ".
        _DB_PREFIX_."scaledev_adeo_value_parameter, ".
        _DB_PREFIX_."scaledev_adeo_value_mapping, ".
        _DB_PREFIX_."scaledev_adeo_marketplace_value_lang, ".
        _DB_PREFIX_."scaledev_adeo_marketplace_value, ".
        _DB_PREFIX_."scaledev_adeo_attribute_mapping, ".
        _DB_PREFIX_."scaledev_adeo_category_attribute, ".
        _DB_PREFIX_."scaledev_adeo_product_attribute_lang, ".
        _DB_PREFIX_."scaledev_adeo_product_attribute, ".
        _DB_PREFIX_."scaledev_adeo_category_lang, ".
        _DB_PREFIX_."scaledev_adeo_category"
    );
}

/**
 * @return bool
 */
function deleteConfigurationEntries()
{
    $prefix = ScaledevConfiguration::getPrefix();
    return
        Configuration::deleteByName($prefix.'API_CATEGORIES')
        && Configuration::deleteByName($prefix.'TO_BE_SENT')
    ;
}

/**
 * @return bool
 */
function uninstallTabs()
{
    $success = true;
    foreach (
        array(
             'attributesMapping',
             'valuesMapping',
             'manufacturerMapping'
         ) as $tab
    ) {
        $success &= Tab::getInstanceFromClassName($tab)->delete();
    }
    return $success;
}
