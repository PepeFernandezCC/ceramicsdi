<?php
/**
 * Creative Slider - Responsive Slideshow Module
 * https://creativeslider.webshopworks.com
 *
 * @author    WebshopWorks <info@webshopworks.com>
 * @copyright 2015-2025 WebshopWorks
 * @license   One Domain Licence
 *
 * Not allowed to resell or redistribute this software
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class LsSliders
{
    /**
     * @var array Array containing the result of the last DB query
     */
    public static $results = [];

    /**
     * @var int Count of found sliders in the last DB query
     */
    public static $count;

    /**
     * Private constructor to prevent instantiate static class.
     *
     * @since 5.0.0
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Returns the count of found sliders in the last DB query.
     *
     * @since 5.0.0
     *
     * @return int Count of found sliders in the last DB query
     */
    public static function count()
    {
        return self::$count;
    }

    /**
     * Find sliders with the provided filters.
     *
     * @since 5.0.0
     *
     * @param mixed $args Find any slider with the provided filters
     *
     * @return mixed Array on success, false otherwise
     */
    public static function find($args = [])
    {
        if (is_numeric($args) && (int) $args == $args) {
            // Find by slider ID
            return self::_getById((int) $args);
        } elseif ('random' === $args) {
            // Random slider
            return self::_getRandom();
        } elseif (is_string($args)) {
            // Find by slider slug
            return self::_getBySlug($args);
        } elseif (is_array($args) && isset($args[0]) && is_numeric($args[0])) {
            // Find by list of slider IDs
            return self::_getByIds($args);
        } else {
            // Find by query
            $defaults = [
                'term' => '',
                'exclude' => ['removed'],
                'orderby' => 'date_c',
                'order' => 'DESC',
                'limit' => 10,
                'page' => 1,
                'data' => true,
            ];
            $args = array_merge($defaults, $args);

            in_array($args['orderby'], [
                'id',
                'author',
                'name',
                'slug',
                'data',
                'date_c',
                'date_m',
                'flag_hidden',
                'flag_deleted',
                'schedule_start',
                'schedule_end',
            ]) || $args['orderby'] = 'date_c';

            // Build the query
            $db = Db::getInstance();
            $query = (new DbQuery())->select('SQL_CALC_FOUND_ROWS *')->from('layerslider');

            if ($args['exclude']) {
                in_array('hidden', $args['exclude']) && $query->where('`flag_hidden` = 0');
                in_array('removed', $args['exclude']) && $query->where('`flag_deleted` = 0');
            }
            if ($args['term']) {
                $query->where('`name` LIKE "%' . pSQL($args['term']) . '%" OR `slug` LIKE "%' . pSQL($args['term']) . '%"');
            }
            $query->orderBy(bqSQL($args['orderby']) . ('DESC' === $args['order'] ? ' DESC' : ''));
            $query->limit($args['limit'], $args['limit'] * $args['page'] - $args['limit']);
            $sliders = $db->executeS($query);

            // Set counter
            self::$count = (int) $db->getValue('SELECT FOUND_ROWS()');

            // Parse slider data
            if ($args['data'] && $sliders) {
                foreach ($sliders as &$slider) {
                    $slider['data'] = json_decode($slider['data'], true);
                }
            }

            // Return sliders
            return $sliders;
        }
    }

    /**
     * Add slider with the provided name and optional slider data.
     *
     * @since 5.0.0
     *
     * @param string $title The title of the slider to create
     * @param array $data The settings of the slider to create
     *
     * @return int The slider database ID inserted
     */
    public static function add($title = 'Unnamed', $data = [], $slug = '')
    {
        // Slider data
        $data = $data ?: [
            'properties' => [
                'createdWith' => LS_PLUGIN_VERSION,
                'sliderVersion' => LS_PLUGIN_VERSION,
                'title' => $title,
                'new' => true,
            ],
            'layers' => [[]],
        ];

        // Fix issue with longer varchars than the column length
        if (Tools::strlen($title) > 99) {
            $title = Tools::substr($title, 0, 99 - Tools::strlen($title));
        }

        // Insert slider
        $time = time();
        $db = Db::getInstance();
        $db->insert('layerslider', [
            'author' => (int) ls_get_current_user_id(),
            'name' => pSQL($title),
            'slug' => pSQL($slug),
            'data' => pSQL(json_encode($data), true),
            'date_c' => (int) $time,
            'date_m' => (int) $time,
        ]);
        $id_slider = $db->insert_id();
        $db->insert('layerslider_module', ['id_slider' => (int) $id_slider]);

        // Return inserted slider ID
        return $id_slider;
    }

    /**
     * Updates sliders.
     *
     * @since 5.2.0
     *
     * @param int $id The database ID of the slider to be updated
     * @param string $title The new title of the slider
     * @param array $data The new settings of the slider
     *
     * @return bool Returns true on success, false otherwise
     */
    public static function update($id = 0, $title = 'Unnamed', $data = [], $slug = '')
    {
        // Check ID
        if (!is_int($id)) {
            return false;
        }

        // Slider data
        $data = $data ?: [
            'properties' => ['title' => $title],
            'layers' => [[]],
        ];

        // Fix issue with longer varchars than the column length
        if (Tools::strlen($title) > 99) {
            $title = Tools::substr($title, 0, 99 - Tools::strlen($title));
        }

        // Status
        $status = 0;
        if (empty($data['properties']['status']) || 'false' === $data['properties']['status']) {
            $status = 1;
        }

        // Schedule
        $schedule = ['schedule_start' => 0, 'schedule_end' => 0];
        foreach ($schedule as $key => $val) {
            if (!empty($data['properties'][$key])) {
                if (is_numeric($data['properties'][$key])) {
                    $schedule[$key] = (int) $data['properties'][$key];
                } else {
                    $tz = date_default_timezone_get();
                    date_default_timezone_set(ls_get_option('timezone_string'));
                    $schedule[$key] = strtotime($data['properties'][$key]);
                    date_default_timezone_set($tz);
                }
            }
        }

        // Insert slider
        return Db::getInstance()->update('layerslider', [
            'name' => pSQL($title),
            'slug' => pSQL($slug),
            'data' => pSQL(json_encode($data), true),
            'schedule_start' => (int) $schedule['schedule_start'],
            'schedule_end' => (int) $schedule['schedule_end'],
            'date_m' => (int) time(),
            'flag_hidden' => (int) $status,
        ], '`id` = ' . (int) $id, 1);
    }

    /**
     * Marking a slider as removed without deleting it
     * with its database ID.
     *
     * @since 5.0.0
     *
     * @param int $id The database ID if the slider to remove
     *
     * @return bool Returns true on success, false otherwise
     */
    public static function remove($id = null)
    {
        // Check ID
        if (!is_int($id)) {
            return false;
        }

        // Remove
        $db = Db::getInstance();
        $result = $db->update('layerslider', ['flag_deleted' => 1], '`id` = ' . (int) $id, 1);

        // Unregister hook if needed
        $hook = self::_getHookCount($id);
        if ($hook && $hook['name'] && 1 == $hook['count']) {
            LayerSlider::$instance->unregisterHook($hook['name']);
        }
        $db->update('layerslider_module', ['id_shop' => -1], '`id_slider` = ' . (int) $id, 1);

        return $result;
    }

    /**
     * Delete a slider by its database ID.
     *
     * @since 5.0.0
     *
     * @param int $id The database ID if the slider to delete
     *
     * @return bool Returns true on success, false otherwise
     */
    public static function delete($id = null)
    {
        // Check ID
        if (!is_int($id)) {
            return false;
        }

        // Delete
        $db = Db::getInstance();
        $result = $db->delete('layerslider', '`id` = ' . (int) $id, 1);

        // Unregister hook if needed
        $hook = self::_getHookCount($id);
        if ($hook && $hook['name'] && 1 == $hook['count']) {
            LayerSlider::$instance->unregisterHook($hook['name']);
        }
        $db->delete('layerslider_module', '`id_slider` = ' . (int) $id, 1);

        return $result;
    }

    /**
     * Restore a slider marked as removed previously by its database ID.
     *
     * @since 5.0.0
     *
     * @param int $id The database ID if the slider to restore
     *
     * @return bool Returns true on success, false otherwise
     */
    public static function restore($id = null)
    {
        // Check ID
        if (!is_int($id)) {
            return false;
        }

        // Restore
        $db = Db::getInstance();
        $result = $db->update('layerslider', ['flag_deleted' => 0], '`id` = ' . (int) $id, 1);

        // Register hook if needed
        $hook = self::_getHookCount($id);
        if ($hook && $hook['name'] && 0 == $hook['count']) {
            LayerSlider::$instance->registerHook($hook['name']);
        }
        $db->update('layerslider_module', ['id_shop' => 0], '`id_slider` = ' . (int) $id, 1);

        return $result;
    }

    private static function _getHookCount($id)
    {
        return Db::getInstance()->getRow('
            SELECT COUNT(m.`hook`) AS count, s.`hook` AS name
            FROM ' . _DB_PREFIX_ . 'layerslider_module m,
                (SELECT `hook` FROM ' . _DB_PREFIX_ . 'layerslider_module WHERE `id_slider` = ' . (int) $id . ') s
            WHERE m.`id_shop` > -1 AND s.`hook` = m.`hook`
        ');
    }

    private static function _getById($id = null)
    {
        // Check ID
        if (!is_int($id)) {
            return false;
        }

        // Get Slider
        $result = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'layerslider WHERE `id` = ' . (int) $id
        );

        // Decode slider data
        if ($result) {
            $result['data'] = json_decode($result['data'], true);
        }

        return $result;
    }

    private static function _getByIds(array $ids = [])
    {
        // Check IDs
        if (!$ids) {
            return false;
        }

        // Get Sliders
        $limit = count($ids);
        $result = Db::getInstance()->executeS('
            SELECT * FROM ' . _DB_PREFIX_ . 'layerslider
            WHERE `id` IN (' . implode(',', array_map('intval', $ids)) . ')
            ORDER BY `id` DESC LIMIT ' . (int) $limit
        );

        // Decode slider data
        if ($result) {
            foreach ($result as &$slider) {
                $slider['data'] = json_decode($slider['data'], true);
            }
        }

        return $result;
    }

    private static function _getBySlug($slug)
    {
        // Check slug
        if (!$slug) {
            return false;
        }

        // Get Slider
        $result = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'layerslider WHERE `slug` = "' . pSQL($slug) . '"'
        );

        // Decode slider data
        if ($result) {
            $result['data'] = json_decode($result['data'], true);
        }

        return $result;
    }

    private static function _getRandom()
    {
        // Get Slider
        $result = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'layerslider WHERE `flag_hidden` = 0 AND `flag_deleted` = 0 ORDER BY RAND()'
        );

        // Decode slider data
        if ($result) {
            $result['data'] = json_decode($result['data'], true);
        }

        return $result;
    }
}
