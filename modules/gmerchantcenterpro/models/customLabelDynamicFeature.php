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
class customLabelDynamicFeature extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;

    /** @var int id_feature * */
    public $id_feature;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'gmcp_tags_dynamic_features',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_feature' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];

    /**
     * insert dynamic feature for a specific tag
     *
     * @param int $id_tag
     * @param int $id_feature
     *
     * @return int
     */
    public static function addTag($id_tag, $id_feature)
    {
        $tag = new customLabelDynamicFeature();
        $tag->id_tag = (int) $id_tag;
        $tag->id_feature = (int) $id_feature;

        return $tag->add();
    }

    /**
     * feature save for one custom_label_id
     *
     * @param int $id_tag
     *
     * @return bool
     */
    public static function getFeatureSave($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags_dynamic_features', 'tdf');
        $query->where('id_tag=' . (int) $id_tag);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow($query);
    }

    /**
     * delete one feature
     *
     * @param int $id_tag
     *
     * @return bool
     */
    public static function deleteFeatureSave($id_tag)
    {
        return \Db::getInstance()->delete('gmcp_tags_dynamic_features', 'id_tag=' . (int) $id_tag);
    }
}
