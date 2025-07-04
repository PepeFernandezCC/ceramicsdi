<?php
/**
 * Creative Slider - Responsive Slideshow Module
 * https://creativeslider.webshopworks.com
 *
 * @author    WebshopWorks <info@webshopworks.com>
 * @copyright 2015-2020 WebshopWorks
 * @license   One Domain Licence
 *
 * Not allowed to resell or redistribute this software
 */

defined('_PS_VERSION_') or exit;

if (defined('LS_INCLUDE')) {
    $slides = null;
    $init = null;
    $lsInit = null;
    $lsPlugins = null;
    $sliderID = 0;
}

// Preload Skin
$skin = empty($slides['properties']['attrs']['skin']) ? 'v6' : $slides['properties']['attrs']['skin'];
if (empty($GLOBALS['ls_style']['skin-'.$skin])) {
    $GLOBALS['ls_style']['skin-'.$skin] = true;

    $lsInit[] = '<link id="ls-skin-'.$skin.'" rel="stylesheet" href="'.LS_VIEWS_URL."css/layerslider/skins/$skin/skin.css?v=".LS_PLUGIN_VERSION.'">' . NL;
}

// Get init code
foreach ($slides['properties']['attrs'] as $key => $val) {
    if (is_bool($val)) {
        $val = $val ? 'true' : 'false';
        $init[] = $key.': '.$val;
    } elseif (is_numeric($val)) {
        $init[] = $key.': '.$val;
    } else {
        $init[] = "$key: '$val'";
    }
}

// Full-size sliders
if ((!empty($slides['properties']['attrs']['type']) && $slides['properties']['attrs']['type'] === 'fullsize') && (empty($slides['properties']['attrs']['fullSizeMode']) || $slides['properties']['attrs']['fullSizeMode'] !== 'fitheight')) {
    $init[] = 'height: '.$slides['properties']['props']['height'].'';
}

if (! empty($lsPlugins)) {
    $init[] = 'plugins: ' . Tools::jsonEncode(array_unique($lsPlugins));
}

if (!LS_UNPACKED) {
    $init[] = "hideWelcomeMessage: true";
}

$init = implode(', ', $init);
$attr = ls_get_option('ls_rocketscript_ignore', false) ? ' data-cfasync="false"' : '';

$lsInit[] = "<script$attr>" . NL;
    $lsInit[] = 'document.addEventListener("DOMContentLoaded", function() {' . NL;
        $lsInit[] = 'if (typeof jQuery.fn.layerSlider == "undefined") {' . NL;
            $lsInit[] = 'if (window._layerSlider && window._layerSlider.showNotice) { ' . NL;
                $lsInit[] = 'window._layerSlider.showNotice(\''.$sliderID.'\',\'jquery\');' . NL;
            $lsInit[] = '}' . NL;
        $lsInit[] = '} else {' . NL;
            $lsInit[] = 'jQuery("#'.$sliderID.' .fancybox > img").unwrap();' . NL;
            $lsInit[] = 'jQuery("#'.$sliderID.'")';
if (!empty($slides['callbacks']) && is_array($slides['callbacks'])) {
    foreach ($slides['callbacks'] as $event => $function) {
        $lsInit[] = '.on("'.$event.'", '._ss($function).')';
    }
}
            $lsInit[] = '.layerSlider({'.$init.'});' . NL;
        $lsInit[] = '}' . NL;
    $lsInit[] = '});' . NL;
$lsInit[] = '</script>';
