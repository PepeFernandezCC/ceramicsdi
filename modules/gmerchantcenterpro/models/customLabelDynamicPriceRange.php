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
class customLabelDynamicPriceRange extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;

    /** @var int price_min * */
    public $price_min;

    /** @var int price_max * */
    public $price_max;

    /** @var int id_product * */
    public $id_product;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'gmcp_tags_price_range',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
            'price_min' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
            'price_max' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
        ],
    ];

    /**
     * insert dynamic price range
     *
     * @param int $id_tag
     * @param float $price_min
     * @param float $price_max
     * @param int $id_product
     *
     * @return int
     */
    public static function insertDynamicPriceRange($id_tag, $price_min, $price_max, $id_product)
    {
        $tag = new customLabelDynamicPriceRange();

        $tag->id_tag = (int) $id_tag;
        $tag->price_min = (string) $price_min;
        $tag->price_max = (string) $price_max;
        $tag->id_product = (string) $id_product;

        return $tag->add();
    }

    /**
     * return id_product for the tag
     *
     * @param int $id_tag
     *
     * @return bool
     */
    public static function getDynamicPriceRange($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags_price_range', 'ftpr');
        $query->where('ftpr.id_tag=' . (int) $id_tag);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    /**
     * clean value for dynamic best sales
     *
     * @param int $id_tag
     *
     * @return bool
     */
    public static function deleteDynamicPriceRange($id_tag)
    {
        return \Db::getInstance()->delete('gmcp_tags_price_range', 'id_tag=' . (int) $id_tag);
    }
}
