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
class googleTaxonomy extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;

    /** @var string value * */
    public $value;

    /** @var string lang * */
    public $lang;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'gmcp_taxonomy',
        'primary' => 'id_taxonomy',
        'fields' => [
            'value' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'lang' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
        ],
    ];

    /**
     * clean value for taxonomies
     *
     * @param string $iso_lang
     *
     * @return bool
     */
    public static function clean($iso_lang)
    {
        return \Db::getInstance()->delete('gmcp_taxonomy', 'lang="' . (string) $iso_lang . '"');
    }

    /**
     * add the taxonomies values
     *
     * @param string $iso_lang
     * @param string $value
     *
     * @return bool
     */
    public static function addTaxonomy($value, $iso_lang)
    {
        $taxonomy = new googleTaxonomy();
        $taxonomy->value = (string) $value;
        $taxonomy->lang = (string) $iso_lang;

        return $taxonomy->add();
    }

    /**
     * method checks if the current country has already been updated
     *
     * @param string $iso_code
     *
     * @return bool
     */
    public static function checkTaxonomyUpdate($iso_code)
    {
        $query = new \DbQuery();
        $query->select('COUNT(`id_taxonomy`) as count');
        $query->from('gmcp_taxonomy', 'gt');
        $query->where('gt.lang="' . \pSQL($iso_code) . '"');

        $data = \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

        return $data['count'] > 1 ? true : false;
    }

    /**
     * method delete taxonomy
     *
     * @param string $iso_code
     * @param array $words
     *
     * @return array
     */
    public static function autocompleteSearch($iso_code, array $words)
    {
        $query = new \DbQuery();
        $query->select('value');
        $query->from('gmcp_taxonomy', 'gt');
        $query->where('gt.lang="' . \pSQL($iso_code) . '"');

        foreach ($words as $string) {
            $query->where('gt.value LIKE "%' . \pSQL($string) . '%"');
        }

        return \Db::getInstance()->ExecuteS($query);
    }
}
