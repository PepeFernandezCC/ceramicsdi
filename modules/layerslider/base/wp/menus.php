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

// Register sidebar menu
ls_add_action('admin_menu', 'layerslider_settings_menu');
function layerslider_settings_menu()
{
    $capability = 'manage_options';
    $icon = LS_VIEWS_URL . 'img/admin/icon_16x16.png';

    // Add main page
    ls_add_menu_page('LayerSlider', 'LayerSlider', $capability, 'layerslider', 'layerslider_router', $icon);

    // Add "All Sliders" submenu
    ls_add_submenu_page('layerslider', 'LayerSlider', ls__('All Sliders'), $capability, 'layerslider', 'layerslider_router');

    // Add "Revisions" submenu
    ls_add_submenu_page('layerslider', 'LayerSlider Revisions', ls__('Revisions'), $capability, 'ls-revisions', 'layerslider_router');

    // Add "Skin Editor" submenu
    ls_add_submenu_page('layerslider', 'LayerSlider Skin Editor', ls__('Skin Editor'), $capability, 'ls-skin-editor', 'layerslider_router');

    // Add "CSS Editor submenu"
    ls_add_submenu_page('layerslider', 'LayerSlider CSS Editor', ls__('CSS Editor'), $capability, 'ls-style-editor', 'layerslider_router');

    // Add "Transition Builder" submenu
    ls_add_submenu_page('layerslider', 'LayerSlider Transition Builder', ls__('Transition Builder'), $capability, 'ls-transition-builder', 'layerslider_router');
}

function layerslider_router()
{
    // Get current screen details
    $screen = ls_get_current_screen();

    if (false !== strpos($screen->base, 'ls-skin-editor')) {
        include LS_ROOT_PATH . '/views/skin_editor.php';
    } elseif (false !== strpos($screen->base, 'ls-transition-builder')) {
        include LS_ROOT_PATH . '/views/transition_builder.php';
    } elseif (false !== strpos($screen->base, 'ls-revisions')) {
        include LS_ROOT_PATH . '/views/revisions.php';
    } elseif (false !== strpos($screen->base, 'ls-style-editor')) {
        include LS_ROOT_PATH . '/views/style_editor.php';
    } elseif (isset(${'_GET'}['action']) && 'edit' === ${'_GET'}['action']) {
        include LS_ROOT_PATH . '/views/slider_edit.php';
    } else {
        include LS_ROOT_PATH . '/views/slider_list.php';
    }
}
