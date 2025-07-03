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
use Gmerchantcenterpro\ModuleLib\moduleTools;
if (!defined('_PS_VERSION_')) {
    exit;
}
class featureCategoryTag extends \ObjectModel
{
    public $id_cat;
    public $values;
    public $id_shop;
    public static $definition = [
        'table' => 'gmcp_features_by_cat',
        'primary' => 'id_cat',
        'fields' => [
            'id_cat' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'values' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];
    public static function cleanTable($id_shop)
    {
        return \Db::getInstance()->delete('gmcp_features_by_cat', 'id_shop=' . (int) $id_shop);
    }
    public static function getFeaturesByCategory($id_category, $id_shop)
    {
        $result = [];
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_features_by_cat', 'gfbc');
        $query->where('gfbc.id_cat=' . (int) $id_category);
        $query->where('gfbc.id_shop=' . (int) $id_shop);
        $data = \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        if (!empty($data) && is_array($data)) {
            $result = moduleTools::handleGetConfigurationData($data['values']);
        }
        unset($data);
        return $result;
    }
}
