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

class LsRevisions
{
    public static $active = false;
    public static $enabled = true;
    public static $limit = 100;
    public static $interval = 10;

    /**
     * Private constructor to prevent instantiate static class.
     *
     * @since 6.3.0
     *
     * @return void
     */
    private function __construct()
    {
    }

    public static function init()
    {
        if (ls_get_option('ls-revisions-enabled', true)) {
            self::$active = true;
        }

        $option = ls_get_option('ls-revisions-enabled', true);
        self::$enabled = !empty($option);
        self::$limit = ls_get_option('ls-revisions-limit', 100);
        self::$interval = ls_get_option('ls-revisions-interval', 10);
    }

    /**
     * Counts the number of revisions saved for the specified slider.
     *
     * @since 6.3.0
     *
     * @param int $sliderId The slider database ID
     *
     * @return int The number of revisions available for the slider
     */
    public static function count($sliderId)
    {
        if (!$sliderId || !is_numeric($sliderId)) {
            return 0;
        }

        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'layerslider_revisions WHERE `slider_id` = ' . (int) $sliderId
        );
    }

    /**
     * Finds and returns revisions for a specified slider.
     *
     * @since 6.3.0
     *
     * @param int $sliderId The slider database ID
     *
     * @return array Array of found slider revisions
     */
    public static function snapshots($sliderId)
    {
        if (!$sliderId || !is_numeric($sliderId)) {
            return [];
        }

        return array_map(function ($row) {
            return (object) $row;
        }, Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'layerslider_revisions WHERE `slider_id` = ' . (int) $sliderId . ' ORDER BY `id` LIMIT 500'
        ) ?: []);
    }

    /**
     * Retrieve a specific revision by its database ID.
     *
     * @since 6.3.0
     *
     * @param int $revisionId The revision database ID
     *
     * @return object|null The chosen revision data, or null on error
     */
    public static function get($revisionId)
    {
        if (!$revisionId || !is_numeric($revisionId)) {
            return null;
        }

        $row = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'layerslider_revisions WHERE `id` = ' . (int) $revisionId
        );

        return $row ? (object) $row : null;
    }

    /**
     * Retrieve the last revision for a particular slider.
     *
     * @since 6.3.0
     *
     * @param int $sliderId The slider database ID
     *
     * @return object|null The last revision, or null on error
     */
    public static function last($sliderId)
    {
        if (!$sliderId || !is_numeric($sliderId)) {
            return null;
        }

        $row = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'layerslider_revisions WHERE `slider_id` = ' . (int) $sliderId . ' ORDER BY `id` DESC'
        );

        return $row ? (object) $row : null;
    }

    /**
     * Adds a new revision for a specified slider.
     *
     * @since 6.3.0
     *
     * @param int $sliderId The slider database ID
     * @param string $sliderData The serialized data of the slider
     *
     * @return int Inserted revision Id
     */
    public static function add($sliderId, $sliderData)
    {
        if (!$sliderId || !is_numeric($sliderId) || !$sliderData) {
            return 0;
        }

        $db = Db::getInstance();
        $result = $db->insert('layerslider_revisions', [
            'slider_id' => (int) $sliderId,
            'author' => (int) ls_get_current_user_id(),
            'data' => pSQL($sliderData, true),
            'date_c' => (int) time(),
        ]);

        return $result ? $db->insert_id() : 0;
    }

    /**
     * Removes a revision.
     *
     * @since 6.3.0
     *
     * @param int $revisionId The revision database ID
     *
     * @return mixed Returns the number of rows affected, or false on error
     */
    public static function remove($revisionId)
    {
        if (!$revisionId || !is_numeric($revisionId)) {
            return false;
        }

        return Db::getInstance()->delete('layerslider_revisions', '`id` = ' . (int) $revisionId, 1);
    }

    /**
     * Removes the last revision of the specified slider.
     *
     * @since 6.3.0
     *
     * @param int $sliderId The revision database ID
     *
     * @return mixed Returns the number of rows affected, or false on error
     */
    public static function shift($sliderId)
    {
        if (!$sliderId || !is_numeric($sliderId)) {
            return false;
        }

        return (bool) Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'layerslider_revisions WHERE `slider_id` = ' . (int) $sliderId . ' ORDER BY `id` LIMIT 1'
        );
    }

    /**
     * Removes all revisions for a chosen slider.
     *
     * @since 6.3.0
     *
     * @param int $sliderId The slider database ID
     *
     * @return mixed Returns the number of rows affected, or false on error
     */
    public static function clear($sliderId)
    {
        if (!$sliderId || !is_numeric($sliderId)) {
            return false;
        }

        return Db::getInstance()->delete('layerslider_revisions', '`slider_id` = ' . (int) $sliderId);
    }

    /**
     * Truncates the entire database table.
     *
     * @since 6.3.0
     *
     * @return bool
     */
    public static function truncate()
    {
        return (bool) Db::getInstance()->execute('TRUNCATE ' . _DB_PREFIX_ . 'layerslider_revisions');
    }

    /**
     * Reverts the specified slider to a chosen revision.
     *
     * @since 6.3.0
     *
     * @param int $sliderId The slider database ID
     * @param int $revisionId The revision database ID
     *
     * @return bool True on success, false on error
     */
    public static function revert($sliderId, $revisionId)
    {
        if (!$sliderId || !is_numeric($sliderId) || !$revisionId || !is_numeric($revisionId)) {
            return false;
        }

        $slider = LsSliders::find($sliderId);
        $revision = self::get($revisionId);

        if ($revision && $data = $revision->data) {
            self::add($sliderId, $data);
            LsSliders::update($sliderId, $slider['name'], json_decode($data, true), $slider['slug']);
        }

        return true;
    }
}
