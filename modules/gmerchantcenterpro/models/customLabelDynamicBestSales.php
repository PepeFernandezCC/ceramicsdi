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

    /** @var int id_feature * */
    public $amount;

    /** @var int unit * */
    public $unit;

    /** @var string unit * */
    public $start_date;

    /** @var string unit * */
    public $end_date;

    /** @var int id_product * */
    public $id_product;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'gmcp_tags_dynamic_best_sale',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'amount' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
            'unit' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
            'start_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'end_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
        ],
    ];

    /**
     * insert dynamic best sales
     *
     * @param int $id_tag
     * @param float $amount
     * @param string $unit
     * @param string $start_date
     * @param string $end_date
     * @param string $id_products
     *
     * @return bool
     */
    public static function insertDynamicBestSales($id_tag, $amount, $unit, $start_date = null, $end_date = null, $id_products = null)
    {
        $tag = new customLabelDynamicBestSales();

        $tag->id_tag = (int) $id_tag;
        $tag->amount = (int) $amount;
        $tag->unit = (int) $unit;
        $tag->start_date = (string) $start_date;
        $tag->end_date = (string) $end_date;
        $tag->id_product = (string) $id_products;

        return $tag->add();
    }

    /**
     * record for best sales from database
     *
     * @param int $id_tag
     *
     * @return bool
     */
    public static function getDynamicBestSales($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags_dynamic_best_sale', 'ftbs');
        $query->where('ftbs.id_tag=' . (int) $id_tag);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    /**
     * clean value for dynamic best sales
     *
     * @param int $id_tag
     *
     * @return bool
     */
    public static function deleteDynamicBestSales($id_tag)
    {
        return \Db::getInstance()->delete('gmcp_tags_dynamic_best_sale', 'id_tag=' . (int) $id_tag);
    }
}
