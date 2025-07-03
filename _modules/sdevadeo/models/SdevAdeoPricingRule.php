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
 * Class SdevAdeoPricingRule
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class SdevAdeoPricingRule extends AbstractObjectModel
{
    const TABLE = 'pricing_rule';

    const COLUMN_ID = 'id';
    const COLUMN_MIN_AMOUNT = 'minAmount';
    const COLUMN_MAX_AMOUNT = 'maxAmount';
    const COLUMN_VALUE = 'value';
    const COLUMN_TYPE_PERCENT = 'typePercent';
    const COLUMN_CATEGORY_RULE = 'categoryRule';
    const COLUMN_CREATION_DATE = 'creation_date';
    const COLUMN_UPDATE_DATE = 'update_date';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Pricing rule identifier
     *
     * @var int
     */
    public $id;

    /**
     * Minimum amount to apply the rule
     *
     * @var float
     */
    public $minAmount;

    /**
     * Maximum amount to apply the rule
     *
     * @var float
     */
    public $maxAmount;

    /**
     * Value of the rule
     *
     * @var float
     */
    public $value;

    /**
     * Type to apply to reduced price (percent = 1 / fixed = 0)
     *
     * @var bool
     */
    public $typePercent;

    /**
     * Category that deal with the product rule
     *
     * @var float
     */
    public $categoryRule;

    /**
     * The table's definition.
     *
     * @var array
     */
    public static $definition = array(
        'table' => self::TABLE_PREFIX . self::TABLE,
        'primary' => self::COLUMN_ID,
        'fields' => array(
            self::COLUMN_MIN_AMOUNT => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => true,
            ),
            self::COLUMN_MAX_AMOUNT => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => true,
            ),
            self::COLUMN_VALUE => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => true,
            ),
            self::COLUMN_TYPE_PERCENT => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            self::COLUMN_CATEGORY_RULE => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
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
        )
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
                `'.self::COLUMN_MIN_AMOUNT.'` FLOAT NOT NULL,
                `'.self::COLUMN_MAX_AMOUNT.'` FLOAT NOT NULL,
                `'.self::COLUMN_VALUE.'` FLOAT NOT NULL,
                `'.self::COLUMN_TYPE_PERCENT.'` TINYINT(1) NOT NULL,
                `'.self::COLUMN_CATEGORY_RULE.'` INT(11) UNSIGNED NOT NULL,
                `'.self::COLUMN_CREATION_DATE.'` TIMESTAMP DEFAULT \'0000-00-00 00:00:00\',
                `'.self::COLUMN_UPDATE_DATE.'` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`'.self::COLUMN_ID.'`),
                FOREIGN KEY (`'.self::COLUMN_CATEGORY_RULE.'`)
                    REFERENCES `'.SdevAdeoCategoryRule::getCompleteTableName().'`(`id`) ON DELETE CASCADE,
                KEY (`'.self::COLUMN_MIN_AMOUNT.'`),
                KEY (`'.self::COLUMN_MAX_AMOUNT.'`),
                KEY (`'.self::COLUMN_VALUE.'`),
                KEY (`'.self::COLUMN_TYPE_PERCENT.'`),
                KEY (`'.self::COLUMN_CATEGORY_RULE.'`)
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
     * @return float
     */
    public function getMinAmount()
    {
        return $this->minAmount;
    }

    /**
     * @param float $minAmount
     * @return $this
     */
    public function setMinAmount($minAmount)
    {
        $this->minAmount = $minAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getMaxAmount()
    {
        return $this->maxAmount;
    }

    /**
     * @param float $maxAmount
     * @return $this
     */
    public function setMaxAmount($maxAmount)
    {
        $this->maxAmount = $maxAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTypePercent()
    {
        return $this->typePercent;
    }

    /**
     * @param bool $typePercent
     * @return $this
     */
    public function setTypePercent($typePercent)
    {
        $this->typePercent = $typePercent;
        return $this;
    }

    /**
     * @return float
     */
    public function getCategoryRule()
    {
        return $this->categoryRule;
    }

    /**
     * @param float $categoryRule
     * @return $this
     */
    public function setCategoryRule($categoryRule)
    {
        $this->categoryRule = $categoryRule;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * @param string $creation_date
     * @return $this
     */
    public function setCreationDate(DateTimeInterface $creation_date)
    {
        $this->creation_date = $creation_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * @param string $update_date
     * @return $this
     */
    public function setUpdateDate(DateTimeInterface $update_date)
    {
        $this->update_date = $update_date;
        return $this;
    }
}
