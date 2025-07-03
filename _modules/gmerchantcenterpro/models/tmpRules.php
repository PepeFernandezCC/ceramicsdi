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
class tmpRules extends \ObjectModel
{
    /** @var int id_brands * */
    public $id_shop;
    public $type;
    public $exclusion_values;
    public static $definition = [
        'table' => 'gmcp_tmp_rules',
        'primary' => 'id_cat',
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'exclusion_values' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
        ],
    ];
    public static function addTmpRules($id_shop, $type, $value)
    {
        $tmp_rule = new tmpRules();
        $tmp_rule->id_shop = (int) $id_shop;
        $tmp_rule->type = \pSQL($type);
        $tmp_rule->exclusion_values = \pSQL($value);
        $tmp_rule->add();
    }
    public static function getTmpRules()
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tmp_rules', 'ftp');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }
    public static function cleanTmpRules($id_shop)
    {
        return \Db::getInstance()->delete('gmcp_tmp_rules', 'id_shop=' . (int) $id_shop);
    }
    public static function deleteTmpRules($id_rule)
    {
        return \Db::getInstance()->delete('gmcp_tmp_rules', 'id=' . (int) $id_rule . ' AND `id_shop` = ' . (int) (int)\Context::getContext()->shop->id);
    }
    public static function resetIncrement()
    {
        $sQuery = 'ALTER TABLE `' . _DB_PREFIX_ . 'gmcp_tmp_rules` AUTO_INCREMENT = 1';
        return \Db::getInstance()->Execute($sQuery);
    }
}
