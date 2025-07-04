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
class Feeds extends \ObjectModel
{
    /** @var int id_feed * */
    public $id_feed;

    /** @var string values * */
    public $iso_lang;

    // ** @var string values **/
    public $iso_country;

    // ** @var string values **/
    public $iso_currency;

    // ** @var string values **/
    public $taxonomy;

    /** @var int id_shop * */
    public $id_shop;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var int feed_is_default * */
    public $feed_is_default;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'gmcp_feeds',
        'primary' => 'id_feed',
        'fields' => [
            'iso_lang' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'iso_country' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'iso_currency' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'taxonomy' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'feed_is_default' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];

    /**
     * check if data has aleady saved, to handle the new system on module update if needed
     *
     * @param int $id_shop
     *
     * @return bool
     */
    public static function hasSavedData($id_shop)
    {
        $query = new \DbQuery();
        $query->select('id_feed');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.id_shop=' . (int) $id_shop);

        return !empty(\Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query)) ? true : false;
    }

    /**
     * get all the data from the taxonomies saved on database
     *
     * @param int $id_shop
     *
     * @return array
     */
    public static function getAvailableFeeds($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.id_shop=' . (int) $id_shop);
        $query->orderBy('ff.feed_is_default ASC');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * get the data feed for an iso lang
     *
     * @param string $iso_lang
     * @param int $id_shop
     *
     * @return array
     */
    public static function getFeedLangData($iso_lang, $id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.iso_lang="' . (string) $iso_lang . '"');
        $query->where('ff.id_shop=' . (int) $id_shop);
        $query->orderBy('ff.feed_is_default ASC');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * get the data feed for an iso lang
     *
     * @param string $iso_lang
     * @param string $iso_country
     * @param string $iso_currency
     * @param string $taxonomy
     * @param int $id_shop
     *
     * @return bool
     */
    public static function feedExist($iso_lang, $iso_country, $iso_currency, $taxonomy, $id_shop)
    {
        $query = new \DbQuery();
        $query->select('ff.id_feed');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.iso_lang="' . \Tools::strtoupper((string) $iso_lang) . '"');
        $query->where('ff.iso_country="' . \Tools::strtolower((string) $iso_country) . '"');
        $query->where('ff.iso_currency="' . (string) $iso_currency . '"');
        $query->where('ff.taxonomy="' . (string) $taxonomy . '"');
        $query->where('ff.id_shop=' . (int) $id_shop);

        return !empty(\Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query)) ? true : false;
    }

    /**
     * get the data feed taxonomy
     *
     * @param string $iso_lang
     * @param string $iso_country
     * @param string $iso_currency
     * @param int $id_shop
     *
     * @return bool
     */
    public static function getFeedTaxonomy($iso_lang, $iso_country, $iso_currency, $id_shop)
    {
        $query = new \DbQuery();
        $query->select('ff.taxonomy');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.iso_lang="' . \Tools::strtoupper((string) $iso_lang) . '"');
        $query->where('ff.iso_country="' . \Tools::strtolower((string) $iso_country) . '"');
        $query->where('ff.iso_currency="' . (string) $iso_currency . '"');
        $query->where('ff.id_shop=' . (int) $id_shop);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * get all saved taxonomies for a shop
     *
     * @param int $id_shop
     *
     * @return array
     */
    public static function getSavedTaxonomies($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.id_shop=' . (int) $id_shop);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * clean value for feed data
     *
     * @param int $id_feed
     *
     * @return bool
     */
    public static function deleteFeed($id_feed)
    {
        return \Db::getInstance()->delete('gmcp_feeds', 'id_feed=' . (int) $id_feed);
    }
}
