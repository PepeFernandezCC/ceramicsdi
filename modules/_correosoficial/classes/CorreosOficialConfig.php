<?php
if (!defined('_PS_VERSION_')) {
  exit;
}
require_once dirname(__FILE__).'/../vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';

/**
 * Clase genérica para lo relacionado con Senders
 */
class CorreosOficialConfig extends CorreosOficialConfigDao {

    public static function getConfig($id_shop = null)
    {
        $id_shop = ($id_shop === null) ? Context::getContext()->shop->id : $id_shop;

        return (new CorreosOficialConfigDao)->readRecord('correos_oficial_configuration', 'WHERE id_shop = ' . $id_shop, null, true);
    }

    public static function checkDimensionsByDefaultActivated($id_shop = null)
    {
        $id_shop = ($id_shop === null) ? Context::getContext()->shop->id : $id_shop;

		return (self::getConfigValue('ActivateDimensionsByDefault', $id_shop) == 'on' ||
            ((int)self::getConfigValue('DimensionsByDefaultHeight', $id_shop) > 0 &&
            (int)self::getConfigValue('DimensionsByDefaultLarge', $id_shop)  > 0 &&
            (int)self::getConfigValue('DimensionsByDefaultWidth', $id_shop)  > 0)) 
        ? true : false;
    }

}