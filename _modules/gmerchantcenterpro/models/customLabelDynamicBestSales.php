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
class customLabelDynamicBestSales extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;
    public $amount;
    public $unit;
    public $start_date;
    public $end_date;
    public $id_product;
    public static $definition = [
        'table' => 'gmcp_tags_dynamic_best_sale',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'amount' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
            'unit' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'start_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'end_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
        ],
    ];
    public static function insertDynamicBestSales($id_tag, $amount, $unit, $start_date = null, $end_date = null, $id_products = null)
    {
        $tag = new customLabelDynamicBestSales();
        $tag->id_tag = (int) $id_tag;
        $tag->amount = (int) $amount;
        $tag->unit = \pSQL($unit);
        if (!empty($start_date)) {
            $tag->start_date = \pSQL($start_date);
        }
        if (!empty($end_date)) {
            $tag->end_date = \pSQL($end_date);
        }
        $tag->id_product = (int)$id_products;
        return $tag->add();
    }
    public static function getDynamicBestSales($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags_dynamic_best_sale', 'ftbs');
        $query->where('ftbs.id_tag=' . (int) $id_tag);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
    public static function deleteDynamicBestSales($id_tag)
    {
        return \Db::getInstance()->delete('gmcp_tags_dynamic_best_sale', 'id_tag=' . (int) $id_tag);
    }
}
