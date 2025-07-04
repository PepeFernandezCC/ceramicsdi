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
class customLabelTags extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;
    public $id_shop;
    public $name;
    public $type;
    public $active;
    public $position;
    public $end_date;
    public $custom_label_set_postion;
    public static $definition = [
        'table' => 'gmcp_tags',
        'primary' => 'id_tag',
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'active' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
            'end_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'custom_label_set_postion' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
        ],
    ];
    public static function addTag($id_shop, $name, $type, $custom_label_set_postion, $active, $position = null, $end_date = null)
    {
        $tag = new customLabelTags();
        $tag->id_shop = (int) $id_shop;
        $tag->name = \pSQL($name);
        $tag->type = \pSQL($type);
        $tag->active = (int) $active;
        $tag->custom_label_set_postion = \pSQL($custom_label_set_postion);
        if (!empty($position)) {
            $tag->position = (int) $position;
        }
        if (!empty($end_date)) {
            $tag->end_date = \pSQL($end_date);
        }
        if ($tag->add()) {
            return (int) $tag->id;
        } else {
            return false;
        }
    }
    public static function getTags($id_shop = null, $id_tag = null, $table_type = null, $field = null)
    {
        $query = new \DbQuery();
        $query->select('*');
        $output_data = [];
        if (empty($table_type)) {
            $query->from('gmcp_tags');
        } else {
            $query->from('gmcp_tags_' . $table_type);
        }
        if (!empty($id_shop)) {
            $query->where('id_shop=' . (int) $id_shop);
        }
        if (!empty($id_tag)) {
            $query->where('id_tag=' . (int) $id_tag);
        }
        if (empty($id_tag)) {
            $query->orderBy('position ASC');
        }
        $data = \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
        if (!empty($data) && $field !== null) {
            foreach ($data as $aCat) {
                $output_data[] = $aCat['id_' . $field];
            }
        } else {
            $output_data = $data;
        }
        return $output_data;
    }
    public static function getTagDate($id_shop)
    {
        $query = new \DbQuery();
        $query->select('id_tag, end_date');
        $query->from('gmcp_tags', 'ft');
        $query->where('ft.id_shop=' . (int) $id_shop);
        $query->where('end_date != "00-00-0000"');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }
    public static function getActive($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('gmcp_tags', 'ft');
        $query->where('id_shop=' . (int) $id_shop);
        $query->where('active = 1');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }
    public static function updateTag($id_tag, $name, $type, $custom_label_set_postion, $active, $position = null, $date_end = null)
    {
        $tag = new customLabelTags($id_tag);
        $tag->name = \pSQL($name);
        $tag->type = \pSQL($type);
        $tag->active = (int)$active;
        $tag->custom_label_set_postion = \pSQL($custom_label_set_postion);
        if (!empty($position)) {
            $tag->position = (int) $position;
        }
        if (!empty($date_end)) {
            $tag->end_date = \pSQL($date_end);
        }
        return $tag->update();
    }
    public static function updateTagStatus($id_tag, $status)
    {
        $tag = new customLabelTags($id_tag);
        $tag->active = $status;
        return $tag->update();
    }
    public static function updateProcessDate($id_tag, $status, $position)
    {
        $tag = new customLabelTags($id_tag);
        $tag->active = $status;
        $tag->position = $position;
        return $tag->update();
    }
    public static function getTagPosition($id_tag)
    {
        $query = new \DbQuery();
        $query->select('position');
        $query->from('gmcp_tags', 'gt');
        $query->where('id_tag=' . (int) $id_tag);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
    public static function inserCatTag($id_tag, $id_category, $table_name, $field)
    {
        \Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'gmcp_tags_' . $table_name . '` (`id_tag`, `id_' . pSQL($field) . '`) VALUES (' . (int) $id_tag . ', ' . (int) $id_category . ')');
    }
    public static function deleteTag($id_tag, array $label_list = null, $custom_label_type = null)
    {
        try {
            if (\Db::getInstance()->delete('gmcp_tags', 'id_tag=' . (int) $id_tag)) {
                if (!empty($label_list)) {
                    foreach ($label_list as $table_name => $type) {
                        \Db::getInstance()->delete('gmcp_tags_' . $table_name, 'id_tag=' . (int) $id_tag);
                    }
                }
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 1, $e->getCode(), null, null, true);
        }
    }
    public static function deleteCatTag($id_tag, $table_type)
    {
        return \Db::getInstance()->delete('gmcp_tags_' . $table_type, 'id_tag=' . (int) $id_tag);
    }
    public static function updatePositionTag($id_tag, $position, $id_shop)
    {
        $tag = new customLabelTags($id_tag);
        $tag->position = (int)$position;
        $tag->id_shop = (int)$id_shop;
        return $tag->update();
    }
    public static function getLastId()
    {
        $query = new \DbQuery();
        $query->select('position');
        $query->from('gmcp_tags', 'ft');
        $query->orderBy('position DESC');
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
