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
class categoryTaxonomy extends \ObjectModel
{
    /** @var int id_category * */
    public $id_category;
    public $id_shop;
    public $txt_taxonomy;
    public $lang;
    public static $definition = [
        'table' => 'gmcp_taxonomy_categories',
        'primary' => 'id_category',
        'fields' => [
            'id_category' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'txt_taxonomy' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'lang' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
        ],
    ];
    public static function getGoogleCategories($id_shop, $id_cat, $iso_lang)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_taxonomy_categories', 'gtc');
        $query->where('gtc.id_shop=' . (int) $id_shop);
        $query->where('gtc.id_category=' . (int) $id_cat);
        $query->where('gtc.lang="' . \pSQL($iso_lang) . '"');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
    public static function deleteGoogleCategory($id_shop, $iso_lang)
    {
        return \Db::getInstance()->delete('gmcp_taxonomy_categories', 'lang="' . \pSQL($iso_lang) . '" AND id_shop =' . (int) $id_shop);
    }
    public static function deleteSpecificGoogleCategory($id_shop, $iso_lang, $id_category)
    {
        return \Db::getInstance()->delete('gmcp_taxonomy_categories', 'lang="' . \pSQL($iso_lang) . '" AND id_shop =' . (int) $id_shop . ' AND id_category =' . (int) $id_category);
    }
    public static function insertGoogleCategory($id_shop, $id_category, $google_category, $iso_lang)
    {
        $taxonomy = new categoryTaxonomy();
        $taxonomy->id_shop = (int) $id_shop;
        $taxonomy->id_category = (int) $id_category;
        $taxonomy->txt_taxonomy = json_encode($google_category, JSON_UNESCAPED_UNICODE);
        $taxonomy->lang = \pSQL($iso_lang);
        return $taxonomy->add();
    }
    public static function hasTaxonomies($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_taxonomy_categories', 'gtc');
        $query->where('gtc.id_shop=' . (int) $id_shop);
        $data = \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
        return empty($data) ? false : true;
    }
    public static function getAllTaxonomies($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_taxonomy_categories', 'gtc');
        $query->where('gtc.id_shop=' . (int) $id_shop);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }
}
