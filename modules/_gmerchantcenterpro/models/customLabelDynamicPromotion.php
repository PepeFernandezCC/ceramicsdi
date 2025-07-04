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
class customLabelDynamicPromotion extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;
    public $start_date;
    public $end_date;
    public $id_product;
    public static $definition = [
        'table' => 'gmcp_tags_dynamic_promotion',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'start_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'end_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];
    public static function insertDynamicPromotion($id_tag, $start_date = null, $end_date = null, $id_product = null)
    {
        $tag = new customLabelDynamicLastProductOrder();
        $tag->id_tag = (int) $id_tag;
        $tag->start_date = \pSQL($start_date);
        $tag->end_date = \pSQL($end_date);
        $tag->id_product = (int)$id_product;
        return $tag->add();
    }
    public static function deleteDynamicPromotion($id_tag)
    {
        return \Db::getInstance()->delete('gmcp_tags_dynamic_promotion', 'id_tag=' . (int) $id_tag);
    }
    public static function getDynamicLastDynamicPromotion($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags_dynamic_promotion', 'ftdp');
        $query->where('ftdp.id_tag=' . (int) $id_tag);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
}
