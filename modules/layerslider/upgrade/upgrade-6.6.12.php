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

function upgrade_module_6_6_12($module)
{
    // Remove useless files
    Tools::deleteDirectory(_PS_MODULE_DIR_ . 'layerslider/override');

    array_map('unlink', array_filter([
        _PS_MODULE_DIR_ . 'layerslider/base/templates/tmpl-beta-feedback.php',
        _PS_MODULE_DIR_ . 'layerslider/base/templates/tmpl-import-templates.php',
        _PS_MODULE_DIR_ . 'layerslider/views/js/admin/ls-layer-presets.unpacked.js',
        _PS_MODULE_DIR_ . 'layerslider/views/js/jquery.min.js',
        _PS_MODULE_DIR_ . 'layerslider/views/js/jquery-migrate.min.js',
        _PS_MODULE_DIR_ . 'layerslider/views/js/wp-pointer.min.js',
        _PS_MODULE_DIR_ . 'layerslider/views/css/wp-pointer.min.js',
        _PS_MODULE_DIR_ . 'layerslider/views/css/wp-pointer.js',
        _PS_MODULE_DIR_ . 'layerslider/views/img/admin/cclogo.png',
        _PS_MODULE_DIR_ . 'layerslider/views/img/admin/comingsoon.jpg',
        _PS_MODULE_DIR_ . 'layerslider/views/img/admin/ls_80x80.png',
        _PS_MODULE_DIR_ . 'layerslider/views/img/admin/ls_tb.png',
        _PS_MODULE_DIR_ . 'layerslider/views/img/admin/nocss3d.png',
        _PS_MODULE_DIR_ . 'layerslider/views/img/admin/popup-example-bg.jpg',
        _PS_MODULE_DIR_ . 'layerslider/views/img/admin/popup-example-slidy.png',
        _PS_MODULE_DIR_ . 'layerslider/views/img/admin/slider_preview.jpg',
    ], 'file_exists'));

    return true;
}
