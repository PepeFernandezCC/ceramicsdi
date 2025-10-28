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
 * Class SdevAdeoCategoryRule
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class SdevAdeoCategoryRule extends AbstractObjectModel
{
    const TABLE = 'category_rule';

    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_SHIPPING_DELAY = 'shippingDelay';
    const COLUMN_SHIPPING_COST = 'shippingCost';
    const COLUMN_FREE_CARRIER_LIST = 'freeCarrierList';
    const COLUMN_ADDITIONAL_PRICE = 'additionalPrice';
    const COLUMN_ADD_IF_FORCED_PRICE = 'addIfForcedPrice';
    const COLUMN_LOGISTIC_CLASS = 'logisticClass';
    const COLUMN_CREATION_DATE = 'creation_date';
    const COLUMN_UPDATE_DATE = 'update_date';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Category rule's ID
     *
     * @var int
     */
    public $id;

    /**
     * Category rule's name
     *
     * @var string
     */
    public $name;

    /**
     * Category rule's shipping delay modification
     *
     * @var integer
     */
    public $shippingDelay;

    /**
     * Category rule's shipping cost adjustement
     *
     * @var float
     */
    public $shippingCost;

    /**
     * Specify the logistic class to take account
     *
     * @var string
     */
    public $logisticClass;

    /**
     * Defines the creation date of the registry.
     *
     * @var string $creation_date
     */
    public $creation_date;

    /**
     * Defines the update date of the registry.
     *
     * @var string $creation_date
     */
    public $update_date;

    /**
     * Defines the impact on price as percent
     *
     * @var float
     */
    public $additionalPrice;

    /**
     * Defines if the additional price is applied on forced price
     *
     * @var boolean
     */
    public $addIfForcedPrice;

    /**
     * Defines the carriers that are free
     *
     * @var string
     */
    public $freeCarrierList;

    /**
     * The table's definition.
     *
     * @var array
     */
    public static $definition = array(
        'table' => self::TABLE_PREFIX . self::TABLE,
        'primary' => self::COLUMN_ID,
        'fields' => array(
            self::COLUMN_NAME => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ),
            self::COLUMN_SHIPPING_DELAY => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false,
            ),
            self::COLUMN_SHIPPING_COST => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => false,
            ),
            self::COLUMN_ADDITIONAL_PRICE => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => false,
            ),
            self::COLUMN_ADD_IF_FORCED_PRICE => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ),
            self::COLUMN_FREE_CARRIER_LIST => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
            ),
            self::COLUMN_LOGISTIC_CLASS => array(
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
     * Creates the table to database.
     *
     * @return bool
     */
    public static function createTable()
    {
        return Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `'.self::getCompleteTableName().'` (
                `'.self::COLUMN_ID.'` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `'.self::COLUMN_NAME.'` VARCHAR(255) NOT NULL,
                `'.self::COLUMN_SHIPPING_DELAY.'` INT(11) NULL,
                `'.self::COLUMN_SHIPPING_COST.'` FLOAT NULL,
                `'.self::COLUMN_ADDITIONAL_PRICE.'` FLOAT NULL,
                `'.self::COLUMN_ADD_IF_FORCED_PRICE.'` TINYINT(1) NULL,
                `'.self::COLUMN_FREE_CARRIER_LIST.'` VARCHAR(255) NULL,
                `'.self::COLUMN_LOGISTIC_CLASS.'` VARCHAR(255) NULL,
                `'.self::COLUMN_CREATION_DATE.'` TIMESTAMP DEFAULT \'0000-00-00 00:00:00\',
                `'.self::COLUMN_UPDATE_DATE.'` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`'.self::COLUMN_ID.'`),
                KEY (`'.self::COLUMN_NAME.'`),
                KEY (`'.self::COLUMN_SHIPPING_DELAY.'`),
                KEY (`'.self::COLUMN_SHIPPING_COST.'`),
                KEY (`'.self::COLUMN_ADDITIONAL_PRICE.'`),
                KEY (`'.self::COLUMN_ADD_IF_FORCED_PRICE.'`),
                KEY (`'.self::COLUMN_FREE_CARRIER_LIST.'`),
                KEY (`'.self::COLUMN_LOGISTIC_CLASS.'`)
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
        $list = Db::getInstance()->executeS(sprintf(
            'SELECT * FROM `%s`;',
            self::getCompleteTableName()
        ));

        foreach ($list as $key => $categoryRule) {
            $list[$key]['freeCarrierList'] = json_decode($categoryRule['freeCarrierList']);
            $list[$key]['pricingRule'] = array();
            foreach (SdevAdeoPricingRule::findAll() as $pricingRule) {
                if ($pricingRule['categoryRule'] == $list[$key]['id']) {
                    $list[$key]['pricingRule'][] = $pricingRule;
                }
            }
        }

        if (!is_array($list)) {
            $list = array();
        }

        //$this->hydrate();

        return $list;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getShippingDelay()
    {
        return $this->shippingDelay;
    }

    /**
     * @param int $shippingDelay
     * @return $this
     */
    public function setShippingDelay($shippingDelay)
    {
        $this->shippingDelay = $shippingDelay;
        return $this;
    }

    /**
     * @return number
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * @param number $shippingCost
     * @return $this
     */
    public function setShippingCost($shippingCost)
    {
        $this->shippingCost = $shippingCost;
        return $this;
    }

    /**
     * @return int
     */
    public function getAdditionalPrice()
    {
        return $this->additionalPrice;
    }

    /**
     * @param int $additionalPrice
     * @return $this
     */
    public function setAdditionalPrice($additionalPrice)
    {
        $this->additionalPrice = $additionalPrice;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAddIfForcedPrice()
    {
        return $this->addIfForcedPrice;
    }

    /**
     * @param bool $addIfForcedPrice
     * @return $this
     */
    public function setAddIfForcedPrice($addIfForcedPrice)
    {
        $this->addIfForcedPrice = $addIfForcedPrice;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogisticClass()
    {
        return $this->logisticClass;
    }

    /**
     * @param string $logisticClass
     * @return $this
     */
    public function setLogisticClass($logisticClass)
    {
        $this->logisticClass = $logisticClass;
        return $this;
    }

    /**
     * @return DateTimeInterface
     * @throws Exception
     */
    public function getCreationDate()
    {
        return new DateTime($this->creation_date);
    }

    /**
     * @param DateTimeInterface $creation_date
     * @return $this
     */
    public function setCreationDate(DateTimeInterface $creationDate)
    {
        $this->creation_date = $creationDate->format(self::DATE_FORMAT);

        return $this;
    }

    /**
     * @return DateTimeInterface
     * @throws Exception
     */
    public function getUpdateDate()
    {
        return new DateTime($this->update_date);
    }

    /**
     * @param DateTimeInterface $update_date
     * @return $this
     */
    public function setUpdateDate(DateTimeInterface $update_date)
    {
        $this->update_date = $update_date->format(self::DATE_FORMAT);

        return $this;
    }

    /**
     * @return string
     */
    public function getFreeCarrierList()
    {
        return $this->freeCarrierList;
    }

    /**
     * @param string $freeCarrierList
     * @return $this
     */
    public function setFreeCarrierList($freeCarrierList)
    {
        $this->freeCarrierList = $freeCarrierList;
        return $this;
    }
}
