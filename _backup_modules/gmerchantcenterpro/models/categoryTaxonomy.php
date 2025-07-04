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

    /** @var int id_shop * */
    public $id_shop;

    /** @var string value * */
    public $txt_taxonomy;

    /** @var string lang * */
    public $lang;

    /**
     * @see ObjectModel::$definition
     */
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

    /**
     * method returns google's categories
     *
     * @param int $id_shop
     * @param int $id_cat
     * @param string $iso_lang
     *
     * @return array
     */
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

    /**
     * method delete google categories
     *
     * @param int $id_shop
     * @param string $iso_lang
     *
     * @return bool
     */
    public static function deleteGoogleCategory($id_shop, $iso_lang)
    {
        return \Db::getInstance()->delete('gmcp_taxonomy_categories', 'lang="' . \pSQL($iso_lang) . '" AND id_shop =' . (int) $id_shop);
    }

    /**
     * method add google categories
     *
     * @param int $id_shop
     * @param int $id_category
     * @param string $google_category
     * @param string $iso_lang
     *
     * @return bool
     */
    public static function insertGoogleCategory($id_shop, $id_category, $google_category, $iso_lang)
    {
        $taxonomy = new categoryTaxonomy();
        $taxonomy->id_shop = (int) $id_shop;
        $taxonomy->id_category = (int) $id_category;
        $taxonomy->txt_taxonomy = (string) $google_category;
        $taxonomy->lang = (string) $iso_lang;

        return $taxonomy->add();
    }
}
