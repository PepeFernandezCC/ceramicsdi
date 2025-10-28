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

use Scaledev\Adeo\Core\ObjectModel\AbstractObjectModel;

/**
 * Class SdevAdeoProductLogs
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class SdevAdeoImportLogs extends AbstractObjectModel
{
    const TABLE = 'import_logs';

    const COLUMN_ID = 'id';
    const COLUMN_ID_IMPORT = 'id_import';
    const COLUMN_SHOP_ID = 'shop_id';
    const COLUMN_IS_PRODUCT_IMPORT = 'is_product_import';
    const COLUMN_FLOW_TYPE = 'flow_type';
    const COLUMN_CREATION_DATE = 'creation_date';
    const COLUMN_UPDATE_DATE = 'update_date';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Import log identifier
     *
     * @var int
     */
    public $id;

    /**
     * Define the type of update performed on the flow
     *
     * @var string
     */
    public $flow_type;

    /**
     * Marketplace internal reference
     *
     * @var int
     */
    public $id_import;

    /**
     * Identifier of the context's shop
     *
     * @var int
     */
    public $shop_id;

    /**
     * The import correspond to product import
     *
     * @var bool
     */
    public $is_product_import;

    /**
     * The table's definition.
     *
     * @var array
     */
    public static $definition = array(
        'table' => self::TABLE_PREFIX . self::TABLE,
        'primary' => self::COLUMN_ID,
        'fields' => array(
            self::COLUMN_ID_IMPORT => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ),
            self::COLUMN_SHOP_ID => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ),
            self::COLUMN_IS_PRODUCT_IMPORT => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            self::COLUMN_FLOW_TYPE => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
            ),
            self::COLUMN_CREATION_DATE => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => true,
            ),
            self::COLUMN_UPDATE_DATE => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => true,
            ),
        ),
    );

    /**
     * @inheritDoc
     */
    public static function createTable()
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'.self::getCompleteTableName().'` (
                `'.self::COLUMN_ID.'` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `'.self::COLUMN_ID_IMPORT.'` INT(10) UNSIGNED NOT NULL,
                `'.self::COLUMN_SHOP_ID.'` INT(10) UNSIGNED NOT NULL,
                `'.self::COLUMN_IS_PRODUCT_IMPORT.'` TINYINT(1) UNSIGNED NOT NULL,
                `'.self::COLUMN_FLOW_TYPE.'` VARCHAR(255) NULL,
                `'.self::COLUMN_CREATION_DATE.'` TIMESTAMP DEFAULT \'0000-00-00 00:00:00\',
                `'.self::COLUMN_UPDATE_DATE.'` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`'.self::COLUMN_ID.'`),
                KEY (`'.self::COLUMN_ID_IMPORT.'`),
                KEY (`'.self::COLUMN_SHOP_ID.'`),
                KEY (`'.self::COLUMN_IS_PRODUCT_IMPORT.'`),
                KEY (`'.self::COLUMN_FLOW_TYPE.'`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
    }

    /**
     * @inheritDoc
     */
    public static function resetTable()
    {
        return
            self::dropTable()
            && self::createTable()
        ;
    }

    /**
     * @return int
     */
    public function getIdImport()
    {
        return $this->id_import;
    }

    /**
     * @param int $id_import
     * @return $this
     */
    public function setIdImport($id_import)
    {
        $this->id_import = $id_import;
        return $this;
    }

    /**
     * @param int $shop_id
     * @return $this
     */
    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIsProductImport()
    {
        return $this->is_product_import;
    }

    /**
     * @param bool $is_product_import
     * @return $this
     */
    public function setIsProductImport($is_product_import)
    {
        $this->is_product_import = $is_product_import;
        return $this;
    }

    /**
     * @return string
     */
    public function getFlowType()
    {
        return $this->flow_type;
    }

    /**
     * @param string $flow_type
     * @return $this
     */
    public function setFlowType($flow_type)
    {
        $this->flow_type = $flow_type;
        return $this;
    }
}
