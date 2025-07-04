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
class advancedExclusion extends \ObjectModel
{
    /** @var int id * */
    public $id;
    public $status;
    public $id_shop;
    public $name;
    public $type;
    public $exclusion_value;
    public static $definition = [
        'table' => 'gmcp_advanced_exclusion',
        'primary' => 'id',
        'fields' => [
            'status' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'exclusion_value' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
        ],
    ];
    public static function addRule($status, $id_shop, $rule_name, $type, $data)
    {
        $rule = new advancedExclusion();
        $rule->status = (int) $status;
        $rule->id_shop = (int) $id_shop;
        $rule->name = \pSQL($rule_name);
        $rule->type = \pSQL($type);
        $rule->exclusion_value = $data;
        return $rule->add();
    }
    public static function updateRule($status, $id_shop, $rule_name, $type, $data, $id)
    {
        $rule = new advancedExclusion($id);
        $rule->status = (int) $status;
        $rule->id_shop = (int) $id_shop;
        $rule->name = \pSQL($rule_name);
        $rule->type = \pSQL($type);
        $rule->exclusion_value = $data;
        $rule->update();
    }
    public static function updateRuleStatus($id, $type, $status)
    {
        if ($type == 'bulk') {
            $rules = explode(',', $id);
            if (is_array($rules)) {
                foreach ($rules as $id_rule) {
                    $rule = new advancedExclusion((int)$id_rule);
                    $rule->status = (int) $status;
                    $rule->update();
                }
            }
        } else {
            $rule = new advancedExclusion((int)$id);
            $rule->status = (int) $status;
            $rule->update();
        }
    }
    public static function getRules()
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_advanced_exclusion', 'fae');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }
    public static function getRulesById($id_rule)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_advanced_exclusion', 'fae');
        $query->where('fae.`id` = ' . (int) $id_rule);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }
    public static function deleteExclusionRule($id, $type)
    {
        if ($type == 'bulk') {
            $rules = explode(',', $id);
            if (is_array($rules)) {
                foreach ($rules as $id_rule) {
                    \Db::getInstance()->delete('gmcp_advanced_exclusion', 'id=' . (int) $id_rule);
                }
            }
        } else {
            \Db::getInstance()->delete('gmcp_advanced_exclusion', 'id=' . (int) $id);
        }
    }
    public static function getLastRuleId()
    {
        $query = new \DbQuery();
        $query->select('MAX(id) as last_id');
        $query->from('gmcp_advanced_exclusion', 'fae');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
}
