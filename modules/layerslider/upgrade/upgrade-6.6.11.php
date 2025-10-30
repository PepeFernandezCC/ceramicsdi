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

function upgrade_module_6_6_11($module)
{
    return Configuration::deleteByName('LS_DB_VERSION')
        && Configuration::deleteByName('LS_PLUGIN_VERSION')
        && Configuration::deleteByName('LS_INSTALL')
        && Configuration::deleteByName('LS_INSTALLED')
        && Configuration::deleteByName('LS_STORE_DATA')
        && Configuration::deleteByName('LS_STORE_LAST_UPDATED');
}
