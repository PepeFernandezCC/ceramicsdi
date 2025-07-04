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
class customLabelDynamicLastProductOrder extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;
    public $start_date;
    public $end_date;
    public $id_product;
    public static $definition = [
        'table' => 'gmcp_tags_dynamic_last_product_ordered',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'start_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'end_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];
    public static function insertDynamicLastProductOrdered($id_tag, $start_date = null, $end_date = null, $product_id = null)
    {
        $tag = new customLabelDynamicLastProductOrder();
        $tag->id_tag = (int) $id_tag;
        $tag->start_date = \pSQL($start_date);
        $tag->end_date = \pSQL($end_date);
        $tag->id_product = (int)$product_id;
        return $tag->add();
    }
    public static function deleteDynamicLastProductOrdered($id_tag)
    {
        return \Db::getInstance()->delete('gmcp_tags_dynamic_last_product_ordered', 'id_tag=' . (int) $id_tag);
    }
    public static function getDynamicLastProductOrdered($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags_dynamic_last_product_ordered', 'ftdlpo');
        $query->where('ftdlpo.id_tag=' . (int) $id_tag);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
}
