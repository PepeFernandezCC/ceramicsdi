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
class exportBrands extends \ObjectModel
{
    /** @var int id_brands * */
    public $id_brands;

    /** @var int id of the shop * */
    public $id_shop;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'gmcp_brands',
        'primary' => 'id_brands',
        'fields' => [
            'id_brands' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];

    /**
     *  clean the table for
     *
     * @param int $id_shop
     *
     * @return bool
     */
    public static function cleanTable($id_shop)
    {
        return \Db::getInstance()->delete('gmcp_brands', 'id_shop=' . (int) $id_shop);
    }

    /**
     * method returns categories to export
     *
     * @param int $id_shop
     *
     * @return array
     */
    public static function getBrands($id_shop)
    {
        // set
        $brands = [];
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_brands', 'gb');
        $query->where('gb.id_shop=' . (int) $id_shop);

        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (!empty($result)) {
            foreach ($result as $brand) {
                $brands[] = $brand['id_brands'];
            }
        }

        return $brands;
    }
}
