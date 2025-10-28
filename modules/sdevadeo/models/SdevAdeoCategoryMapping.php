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
 * Class ScaledevdevAdeoCategoryMapping
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class SdevAdeoCategoryMapping extends AbstractObjectModel
{
    const TABLE = 'category_mapping';

    const COLUMN_ID = 'id';
    const COLUMN_PRESTASHOP_CATEGORY = 'prestashop_category';
    const COLUMN_CATEGORY_RULE = 'category_rule';
    const COLUMN_ACTIVE_CATEGORY = 'active';
    const COLUMN_ID_SHOP = 'id_shop';
    const COLUMN_CREATION_DATE = 'creation_date';
    const COLUMN_UPDATE_DATE = 'update_date';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $prestashop_category;

    /**
     * @var int|null
     */
    public $category_rule;

    /**
     * @var bool
     */
    public $active;

    /**
     * @var int
     */
    public $id_shop;

    /**
     * The table's definition.
     *
     * @var array
     */
    public static $definition = array(
        'table' => self::TABLE_PREFIX . self::TABLE,
        'primary' => self::COLUMN_ID,
        'fields' => array(
            self::COLUMN_PRESTASHOP_CATEGORY => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => true
            ),
            self::COLUMN_CATEGORY_RULE => array(
                'allow_null' => true,
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => false
            ),
            self::COLUMN_ACTIVE_CATEGORY => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true
            ),
            self::COLUMN_ID_SHOP => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => true
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
            `'.self::COLUMN_PRESTASHOP_CATEGORY.'` INT(10) UNSIGNED NOT NULL,
            `'.self::COLUMN_CATEGORY_RULE.'` INT(11) UNSIGNED NULL,
            `'.self::COLUMN_ACTIVE_CATEGORY.'` TINYINT(1) NOT NULL,
            `'.self::COLUMN_ID_SHOP.'` INT(11) NOT NULL,
            `'.self::COLUMN_CREATION_DATE.'` TIMESTAMP DEFAULT \'0000-00-00 00:00:00\',
            `'.self::COLUMN_UPDATE_DATE.'` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`'.self::COLUMN_ID.'`),
                KEY (`'.self::COLUMN_PRESTASHOP_CATEGORY.'`),
                KEY (`'.self::COLUMN_CATEGORY_RULE.'`),
                KEY (`'.self::COLUMN_ACTIVE_CATEGORY.'`),
                KEY (`'.self::COLUMN_ID_SHOP.'`)
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
     * @inheritdoc
     */
    public static function findAll()
    {
        $list = Db::getInstance()->executeS((new \DbQuery())
            ->select('*')
            ->from(static::getTableName())
            ->where(self::COLUMN_ID_SHOP.'='.Context::getContext()->shop->id)
        );

        if (!is_array($list)) {
            $list = array();
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function add($auto_date = true, $null_values = false)
    {
        $this->creation_date = date(static::DATE_FORMAT);
        $this->update_date = date(static::DATE_FORMAT);
        $this->id_shop = Context::getContext()->shop->id;

        return parent::add($auto_date, $null_values);
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
    public function getPrestashopCategory()
    {
        return $this->prestashop_category;
    }

    /**
     * @param int $prestashop_category
     * @return $this
     */
    public function setPrestashopCategory($prestashop_category)
    {
        $this->prestashop_category = $prestashop_category;
        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryRule()
    {
        return $this->category_rule;
    }

    /**
     * @param int $category_rule
     * @return $this
     */
    public function setCategoryRule($category_rule)
    {
        $this->category_rule = $category_rule;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdShop()
    {
        return $this->id_shop;
    }

    /**
     * @param int $id_shop
     * @return $this
     */
    public function setIdShop($id_shop)
    {
        $this->id_shop = $id_shop;
        return $this;
    }
}
