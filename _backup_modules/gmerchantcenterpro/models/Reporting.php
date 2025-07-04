<?php
/**
 * Google merchant center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace Gmerchantcenterpro\Models;

if (!defined('_PS_VERSION_')) {
    exit;
}
class Reporting extends \ObjectModel
{
    /** @var int id_reporting * */
    public $id_reporting;

    /** @var string values * */
    public $iso_feed;

    // ** @var string values **/
    public $reporting_content;

    /** @var int id_shop * */
    public $id_shop;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'gmcp_reporting',
        'primary' => 'id_reporting',
        'fields' => [
            'iso_feed' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'reporting_content' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    /**
     *  add the reporing data in table
     *
     * @param string $iso_feed
     * @param string $reporting_content
     * @param int $id_shop
     *
     * @return bool
     */
    public static function addReporting($iso_feed, $reporting_content, $id_shop)
    {
        $reporting = new Reporting();
        $reporting->iso_feed = (string) $iso_feed;
        $reporting->reporting_content = json_encode($reporting_content);
        $reporting->id_shop = (int) $id_shop;

        return $reporting->add();
    }

    /**
     *  clean the table for reporting
     *
     * @param int $iso_feed
     * @param string $id_shop
     *
     * @return bool
     */
    public static function cleanTable($iso_feed, $id_shop)
    {
        return \Db::getInstance()->delete('gmcp_reporting', 'iso_feed = "' . (string) $iso_feed . '" AND id_shop =' . (int) $id_shop);
    }

    /**
     *  get the reporting list generated for a shop
     *
     * @param int $id_shop
     *
     * @return bool
     */
    public static function getReportingList($id_shop)
    {
        $query = new \DbQuery();
        $query->select('iso_feed');
        $query->from('gmcp_reporting', 'fr');
        $query->where('fr.id_shop=' . (int) $id_shop);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     *  get reporting data for an iso_feed for a shop
     *
     * @param string $iso_feed
     * @param int $id_shop
     *
     * @return bool
     */
    public static function getReportingData($iso_feed, $id_shop)
    {
        $query = new \DbQuery();
        $query->select('reporting_content');
        $query->from('gmcp_reporting', 'fr');
        $query->where('fr.iso_feed="' . (string) $iso_feed . '"');
        $query->where('fr.id_shop=' . (int) $id_shop);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
