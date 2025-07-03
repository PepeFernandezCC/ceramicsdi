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
class exclusionProduct extends \ObjectModel
{
    /** @var int id_rule * */
    public $id_rule;
    public $id_product;
    public $id_product_attribute;
    public static $definition = [
        'table' => 'gmcp_product_excluded',
        'primary' => 'id_rule',
        'fields' => [
            'id_rule' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];
    public static function addRule($id_rule, $id_product, $id_product_attribute = null)
    {
        $rule = new exclusionProduct();
        $rule->id_rule = (int) $id_rule;
        $rule->id_product = (int) $id_product;
        $rule->id_product_attribute = !empty($id_product_attribute) ? (int) $id_product_attribute : 0;
        $rule->add();
    }
    public static function deleteRule($id_rule)
    {
        return \Db::getInstance()->delete('gmcp_product_excluded', 'id_rule=' . (int) $id_rule);
    }
    public static function getExcludedProduct()
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_product_excluded', 'gpe');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
    public static function getExcludedProductById($id_rule)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_product_excluded', 'gpe');
        $query->where('gpe.`id_rule` = ' . (int) $id_rule);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
    public static function isExcludedProduct()
    {
        $query = new \DbQuery();
        $query->select('COUNT(*) as nb');
        $query->from('gmcp_product_excluded', 'gpe');
        $query->leftJoin('gmcp_advanced_exclusion', 'gae', 'gae.id = gpe.id_rule');
        $query->where('gae.status = 1');
        $data = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        return !empty($data[0]['nb']) ? 1 : 0;
    }
    public static function isIdProductExcluded($id_product, $id_product_attribute = 0)
    {
        $query = new \DbQuery();
        $query->select('id_product');
        $query->from('gmcp_product_excluded', 'gpe');
        $query->leftJoin('gmcp_advanced_exclusion', 'gae', 'gae.id = gpe.id_rule');
        $query->where('gpe.`id_product` = ' . (int) $id_product);
        $query->where('gae.status = 1');
        if (!empty($id_product_attribute) && !empty(\GMerchantCenterPro::$conf['GMCP_P_COMBOS'])) {
            $query->where('gpe.`id_product_attribute` = ' . (int) $id_product_attribute);
        }
        return !empty(\Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query)) ? true : false;
    }
}
