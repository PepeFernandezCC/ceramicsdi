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
class customLabelDynamicProducts extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;
    public $id_product;
    public $product_name;
    public static $definition = [
        'table' => 'gmcp_tags_products',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'product_name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
        ],
    ];
    public static function insertProductTag($id_tag, $id_product, $product_name)
    {
        $tag = new customLabelDynamicProducts();
        $tag->id_tag = (int) $id_tag;
        $tag->id_product = (int) $id_product;
        $tag->product_name = \pSQL($product_name);
        return $tag->add();
    }
    public static function deleteTag($id_tag)
    {
        return \Db::getInstance()->delete('gmcp_tags_products', 'id_tag=' . (int) $id_tag);
    }
    public static function getGmcpTagsProduct($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags_products', 'ftp');
        $query->where('ftp.id_tag=' . (int) $id_tag);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }
}
