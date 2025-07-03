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
class exportCategories extends \ObjectModel
{
    /** @var int id_category * */
    public $id_category;
    public $id_shop;
    public static $definition = [
        'table' => 'gmcp_categories',
        'primary' => 'id_category',
        'fields' => [
            'id_category' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];
    public static function cleanTable($id_shop)
    {
        return \Db::getInstance()->delete('gmcp_categories', 'id_shop=' . (int) $id_shop);
    }
    public static function getCategories($id_shop)
    {
        $categories = [];
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_categories', 'gc');
        $query->where('gc.id_shop=' . (int) $id_shop);
        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if (!empty($result)) {
            foreach ($result as $category) {
                $categories[] = $category['id_category'];
            }
        }
        return $categories;
    }
}
