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

function upgrade_module_6_6_4($module)
{
    $db = Db::getInstance();

    try {
        $db->execute('
            ALTER TABLE ' . _DB_PREFIX_ . 'layerslider
            ADD `schedule_start` INT(11) NOT NULL DEFAULT 0,
            ADD `schedule_end` INT(11) NOT NULL DEFAULT 0
        ');
    } catch (Exception $ex) {
        // Do nothing
    }

    $db->execute('
        CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'layerslider_revisions (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `slider_id` int(11) NOT NULL,
            `author` int(11) NOT NULL DEFAULT 0,
            `data` mediumtext NOT NULL,
            `date_c` int(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_general_ci
    ');

    return $db->update('tab_lang', ['name' => 'Creative Slider'], "`name` = 'Layer Slider'");
}
