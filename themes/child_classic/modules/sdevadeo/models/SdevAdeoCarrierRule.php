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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../autoload.php');

/**
 * Class SdevAdeoCarrierRule
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class SdevAdeoCarrierRule extends AbstractObjectModel
{

    const TABLE = 'carrier_rule';

    const COLUMN_ID = 'id';
    const COLUMN_INTERNAL_CARRIER_ID = 'internalCarrierId';
    const COLUMN_MARKETPLACE_SHIPMENT = 'marketplaceShippingCode';
    const COLUMN_CREATION_DATE = 'creation_date';
    const COLUMN_UPDATE_DATE = 'update_date';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Carrier rule's ID
     *
     * @var int
     */
    public $id;

    /**
     * ID of the prestashop carrier linked
     *
     * @var int
     */
    public $internalCarrierId;

    /**
     * Code of the marketplace's carrier
     *
     * @var string
     */
    public $marketplaceShippingCode;

    /**
     * The table's definition.
     *
     * @var array
     */
    public static $definition = array(
        'table' => self::TABLE_PREFIX . self::TABLE,
        'primary' => self::COLUMN_ID,
        'fields' => array(
            self::COLUMN_INTERNAL_CARRIER_ID => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ),
            self::COLUMN_MARKETPLACE_SHIPMENT => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCarrierName',
                'required' => true,
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
     * Creates the table to database.
     *
     * @return bool
     */
    public static function createTable()
    {
        return Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `'.self::getCompleteTableName().'` (
                `'.self::COLUMN_ID.'` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `'.self::COLUMN_INTERNAL_CARRIER_ID.'` INT(10) UNSIGNED NOT NULL,
                `'.self::COLUMN_MARKETPLACE_SHIPMENT.'` VARCHAR(32) NOT NULL,
                `'.self::COLUMN_CREATION_DATE.'` TIMESTAMP DEFAULT \'0000-00-00 00:00:00\',
                `'.self::COLUMN_UPDATE_DATE.'` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`'.self::COLUMN_ID.'`),
                FOREIGN KEY (`'.self::COLUMN_INTERNAL_CARRIER_ID.'`) REFERENCES `' ._DB_PREFIX_. 'carrier`(`id_carrier`) ON DELETE CASCADE,
                KEY (`'.self::COLUMN_MARKETPLACE_SHIPMENT.'`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
        );
    }

    /**
     * Removes the table from database and re-create it.
     *
     * @return bool
     */
    public static function resetTable()
    {
        return
            self::dropTable()
            && self::createTable()
            ;
    }

    /**
     * Gets all lines from table as an array.
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function findAll()
    {
        $list = Db::getInstance()->executeS((new DbQuery)
            ->select('sdev.*, c.name')
            ->from(self::getTableName(), 'sdev')
            ->innerJoin('carrier', 'c', 'sdev.'.self::COLUMN_INTERNAL_CARRIER_ID.' = c.id_carrier')
        );

        if (!is_array($list)) {
            $list = array();
        }

        return $list;
    }

    /**
     * Retrieve the CMS id carrier depending on the marketplace carrier reference
     *
     * @param $reference
     * @return false|string
     */
    public static function findIdCarrierByMpReference($reference)
    {
        $reference = Db::getInstance()->getValue((new \DbQuery())
            ->select(self::COLUMN_INTERNAL_CARRIER_ID)
            ->from(self::getTableName())
            ->where(self::COLUMN_MARKETPLACE_SHIPMENT.' = \''.pSQL($reference).'\'')
        );
        return \Carrier::getCarrierByReference($reference)->id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getInternalCarrierId()
    {
        return $this->internalCarrierId;
    }

    /**
     * @param int $internalCarrierId
     * @return $this
     */
    public function setInternalCarrierId($internalCarrierId)
    {
        $this->internalCarrierId = $internalCarrierId;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketplaceShippingCode()
    {
        return $this->marketplaceShippingCode;
    }

    /**
     * @param string $marketplaceShippingCode
     * @return $this
     */
    public function setMarketplaceShippingCode($marketplaceShippingCode)
    {
        $this->marketplaceShippingCode = $marketplaceShippingCode;
        return $this;
    }
}
