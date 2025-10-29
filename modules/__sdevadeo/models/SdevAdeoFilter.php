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
 * Class SdevAdeoFilter
 *
 * @package Scaledev\Adeo
 * @author Fabien Sigrand <contact@scaledev.fr>
 */
final class SdevAdeoFilter extends AbstractObjectModel
{

    const TABLE = 'product_filter';

    const COLUMN_ID = 'id';
    const COLUMN_FILTER_TYPE = 'filterType';
    const COLUMN_FILTER_TARGET = 'filterTarget';
    const COLUMN_FILTER_VALUE = 'filterValue';
    const COLUMN_CREATION_DATE = 'creation_date';
    const COLUMN_UPDATE_DATE = 'update_date';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    const ENUM_TYPE_CONTAIN = 'contain';
    const ENUM_TYPE_EQUAL = 'equal';
    const ENUM_TYPE_START = 'start';
    const ENUM_TYPE_HIGHER = 'high';
    const ENUM_TYPE_LOWER = 'lower';

    const ENUM_TARGET_PRODUCT_NAME = 'productName';
    const ENUM_TARGET_PRODUCT_EAN = 'productEan';
    const ENUM_TARGET_PRODUCT_ATTRIBUTE = 'productAttribute';

    /**
     * Carrier rule's ID
     *
     * @var int
     */
    public $id;

    /**
     * A string value of which type of filter apply
     * Enum('contains', 'equals', 'start', 'high', 'lower')
     *
     * @var string
     */
    public $filterType;

    /**
     * On which item we should apply the filter
     * Enum('productName', 'productEan', 'attribute')
     *
     * @var string
     */
    public $filterTarget;

     /**
     * The value on which apply the filter
     *
     * @var string
     */
    public $filterValue;

    /**
     * The table's definition.
     *
     * @var array
     */
    public static $definition = array(
        'table' => self::TABLE_PREFIX . self::TABLE,
        'primary' => self::COLUMN_ID,
        'fields' => array(
            self::COLUMN_FILTER_TYPE => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ),
            self::COLUMN_FILTER_TARGET => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ),
            self::COLUMN_FILTER_VALUE => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
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
                `'.self::COLUMN_FILTER_TYPE.'` ENUM(\''.self::ENUM_TYPE_CONTAIN.'\',\''.self::ENUM_TYPE_EQUAL.'\',\''.self::ENUM_TYPE_START.'\',\''.self::ENUM_TYPE_HIGHER.'\', \''.self::ENUM_TYPE_LOWER.'\') NOT NULL,
                `'.self::COLUMN_FILTER_TARGET.'` ENUM(\''.self::ENUM_TARGET_PRODUCT_NAME.'\',\''.self::ENUM_TARGET_PRODUCT_ATTRIBUTE.'\',\''.self::ENUM_TARGET_PRODUCT_EAN.'\') NOT NULL,
                `'.self::COLUMN_FILTER_VALUE.'` VARCHAR(255) NOT NULL,
                `'.self::COLUMN_CREATION_DATE.'` TIMESTAMP DEFAULT \'0000-00-00 00:00:00\',
                `'.self::COLUMN_UPDATE_DATE.'` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`'.self::COLUMN_ID.'`)
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
     * Get the value on which apply the filter
     *
     * @return string
     */ 
    public function getFilterValue()
    {
        return $this->filterValue;
    }

    /**
     * Set the value on which apply the filter
     *
     * @param string $filterValue  The value on which apply the filter
     *
     * @return self
     */ 
    public function setFilterValue(string $filterValue)
    {
        $this->filterValue = $filterValue;

        return $this;
    }

    /**
     * Get enum('productName', 'productEan', 'attribute')
     *
     * @return string
     */ 
    public function getFilterTarget()
    {
        return $this->filterTarget;
    }

    /**
     * Set enum('productName', 'productEan', 'attribute')
     *
     * @param string $filterTarget  Enum('productName', 'productEan', 'attribute')
     *
     * @return self
     */ 
    public function setFilterTarget(string $filterTarget)
    {
        $this->filterTarget = $filterTarget;

        return $this;
    }

    /**
     * Get enum('contains', 'equals', 'start', 'high', 'lower')
     *
     * @return string
     */ 
    public function getFilterType()
    {
        return $this->filterType;
    }

    /**
     * Set enum('contains', 'equals', 'start', 'high', 'lower')
     *
     * @param string $filterType  Enum('contains', 'equals', 'start', 'high', 'lower')
     *
     * @return self
     */ 
    public function setFilterType(string $filterType)
    {
        $this->filterType = $filterType;

        return $this;
    }

    /**
     * Get carrier rule's ID
     *
     * @return int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set carrier rule's ID
     *
     * @param int $id  Carrier rule's ID
     *
     * @return self
     */ 
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }
}
