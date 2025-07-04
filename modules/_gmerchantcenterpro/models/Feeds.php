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
    public $iso_lang;
    public $iso_country;
    public $iso_currency;
    public $taxonomy;
    public $id_shop;
    public $date_add;
    public $date_upd;
    public $feed_is_default;
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
    public static function hasSavedData($id_shop)
    {
        $query = new \DbQuery();
        $query->select('id_feed');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.id_shop=' . (int) $id_shop);
        return !empty(\Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query)) ? true : false;
    }
    public static function getAvailableFeeds($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.id_shop=' . (int) $id_shop);
        $query->orderBy('ff.feed_is_default ASC');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
    public static function getFeedLangData($iso_lang, $id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.iso_lang="' . \pSQL($iso_lang ). '"');
        $query->where('ff.id_shop=' . (int) $id_shop);
        $query->orderBy('ff.feed_is_default ASC');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
    public static function feedExist($iso_lang, $iso_country, $iso_currency, $taxonomy, $id_shop)
    {
        $query = new \DbQuery();
        $query->select('ff.id_feed');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.iso_lang="' . \Tools::strtoupper(\pSQL($iso_lang)) . '"');
        $query->where('ff.iso_country="' . \Tools::strtolower(\pSQL($iso_country)) . '"');
        $query->where('ff.iso_currency="' . \pSQL($iso_currency) . '"');
        $query->where('ff.taxonomy="' . \pSQL($taxonomy). '"');
        $query->where('ff.id_shop=' . (int) $id_shop);
        return !empty(\Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query)) ? true : false;
    }
    public static function getFeedTaxonomy($iso_lang, $iso_country, $iso_currency, $id_shop)
    {
        $query = new \DbQuery();
        $query->select('ff.taxonomy');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.iso_lang="' . \Tools::strtoupper(\pSQL($iso_lang)) . '"');
        $query->where('ff.iso_country="' . \Tools::strtolower(\pSQL($iso_country)) . '"');
        $query->where('ff.iso_currency="' . \pSQL($iso_currency) . '"');
        $query->where('ff.id_shop=' . (int) $id_shop);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
    public static function getSavedTaxonomies($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_feeds', 'ff');
        $query->where('ff.id_shop=' . (int) $id_shop);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
    public static function deleteFeed($id_feed)
    {
        return \Db::getInstance()->delete('gmcp_feeds', 'id_feed=' . (int) $id_feed);
    }
}
