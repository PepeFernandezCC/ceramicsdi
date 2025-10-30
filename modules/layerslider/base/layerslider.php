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

/* ---------------------- Actions --------------------- */

// Basic configuration
define('LS_PLUGIN_VERSION', '6.6.12');

// Path info
define('LS_ROOT_PATH', __DIR__);

// Shared
include LS_ROOT_PATH . '/wp/scripts.php';
include LS_ROOT_PATH . '/wp/menus.php';
include LS_ROOT_PATH . '/wp/hooks.php';
include LS_ROOT_PATH . '/wp/shortcodes.php';
// include LS_ROOT_PATH . '/wp/compatibility.php';
include LS_ROOT_PATH . '/includes/slider_utils.php';
include LS_ROOT_PATH . '/classes/class.ls.posts.php';
include LS_ROOT_PATH . '/classes/class.ls.sliders.php';
include LS_ROOT_PATH . '/classes/class.ls.sources.php';

// Back-end only
if (ls_is_admin()) {
    include LS_ROOT_PATH . '/wp/actions.php';
    // include LS_ROOT_PATH . '/wp/activation.php';
    // include LS_ROOT_PATH . '/wp/tinymce.php';
    include LS_ROOT_PATH . '/wp/notices.php';
    include LS_ROOT_PATH . '/classes/class.ls.revisions.php';

    LsRevisions::init();
}

// Register [layerslider] shortcode
LsShortcode::registerShortcode();

// Add default skins.
// Reads all sub-directories (individual skins) from the given path.
LsSources::addSkins(_PS_MODULE_DIR_ . 'layerslider/views/css/layerslider/skins/');
