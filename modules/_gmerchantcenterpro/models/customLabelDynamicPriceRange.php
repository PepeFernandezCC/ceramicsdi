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
    public $price_min;
    public $price_max;
    public $id_product;
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
    public static function insertDynamicPriceRange($id_tag, $price_min, $price_max, $id_product)
    {
        $tag = new customLabelDynamicPriceRange();
        $tag->id_tag = (int) $id_tag;
        $tag->price_min = (int)$price_min;
        $tag->price_max = (int)$price_max;
        $tag->id_product = (int)$id_product;
        return $tag->add();
    }
    public static function getDynamicPriceRange($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags_price_range', 'ftpr');
        $query->where('ftpr.id_tag=' . (int) $id_tag);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
    public static function deleteDynamicPriceRange($id_tag)
    {
        return \Db::getInstance()->delete('gmcp_tags_price_range', 'id_tag=' . (int) $id_tag);
    }
}
