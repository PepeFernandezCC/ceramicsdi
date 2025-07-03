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
class customLabelDynamicLastProductNotOrder extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;

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
        'table' => 'gmcp_tags_dynamic_last_product_not_ordered',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'start_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'end_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];

    /**
     * insert dynamic last product ordered
     *
     * @param int $id_tag
     * @param string $start_date
     * @param string $end_date
     * @param string $product_ids = null
     *
     * @return int
     */
    public static function insert($id_tag, $start_date = null, $end_date = null, $product_id = null)
    {
        $tag = new customLabelDynamicLastProductNotOrder();

        $tag->id_tag = (int) $id_tag;
        $tag->start_date = (string) $start_date;
        $tag->end_date = (string) $end_date;
        $tag->id_product = (string) $product_id;

        return $tag->add();
    }

    /**
     * clean value for dynamic last ordered
     *
     * @param int $id_tag
     *
     * @return bool
     */
    public static function deleteTag($id_tag)
    {
        return \Db::getInstance()->delete('gmcp_tags_dynamic_last_product_not_ordered', 'id_tag=' . (int) $id_tag);
    }

    /**
     * record for last sales from database
     *
     * @param int $id_tag
     *
     * @return bool
     */
    public static function getDynamicLastProductNotOrdered($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags_dynamic_last_product_not_ordered', 'ftdlpo');
        $query->where('ftdlpo.id_tag=' . (int) $id_tag);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
}
