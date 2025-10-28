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

namespace Scaledev\Adeo\Core\ObjectModel;

use DateTimeInterface;
use Db;
use DbQuery;
use ObjectModel;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Class AbstractObjectModel
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
abstract class AbstractObjectModel extends ObjectModel implements ObjectModelInterface
{
    const TABLE_PREFIX = 'scaledev_adeo_';
    const TABLE = null;
    const DATE_FORMAT = null;

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
     * Removes the table from database.
     *
     * @return bool
     */
    public static function dropTable()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS `'.static::getCompleteTableName().'`'
        );
    }

    /**
     * Gets the table's name with the prefix.
     *
     * @return string
     */
    public static function getCompleteTableName()
    {
        return _DB_PREFIX_.static::getTableName();
    }

    /**
     * Create the model to database.
     *
     * @param bool $auto_date
     * @param bool $null_values
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($auto_date = true, $null_values = false)
    {
        $this->creation_date = date(static::DATE_FORMAT);
        $this->update_date = date(static::DATE_FORMAT);

        return parent::add($auto_date, $null_values);
    }

    /**
     * Update the model to database.
     *
     * @param bool $null_values
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($null_values = false)
    {
        $this->update_date = date(static::DATE_FORMAT);

        return parent::update($null_values);
    }

    /**
     * Gets all lines from table as an array.
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function findAll()
    {
        $list = Db::getInstance()->executeS((new \DbQuery())
            ->select('*')
            ->from(static::getTableName())
        );

        if (!is_array($list)) {
            $list = array();
        }

        return $list;
    }

    /**
     * Return name of the table without db prefix
     *
     * @return string
     */
    public static function getTableName()
    {
        return self::TABLE_PREFIX . static::TABLE;
    }

    /**
     * Truncate the table of the current object model
     *
     * @return bool
     */
    public static function safeTruncateTable()
    {
        if (Db::getInstance()->executeS(
            'SELECT * FROM `'.static::getCompleteTableName().'` LIMIT 1'
        )) {
            return Db::getInstance()->execute(
                'DELETE FROM `' . static::getCompleteTableName() . '`;
                ALTER TABLE `' . static::getCompleteTableName() . '` AUTO_INCREMENT = 1;'
            );
        }
        return true;
    }

    /**
     * @return DateTimeInterface
     * @throws Exception
     */
    public function getCreationDate()
    {
        return new \DateTime($this->creation_date);
    }

    /**
     * @param DateTimeInterface $creation_date
     * @return $this
     */
    public function setCreationDate(DateTimeInterface $creation_date)
    {
        $this->creation_date = $creation_date->format(self::DATE_FORMAT);

        return $this;
    }

    /**
     * @return DateTimeInterface
     * @throws Exception
     */
    public function getUpdateDate()
    {
        return new \DateTime($this->update_date);
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
}
