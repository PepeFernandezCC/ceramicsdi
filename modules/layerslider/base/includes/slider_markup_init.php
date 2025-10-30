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

/** @var int $sliderID */
/** @var array $slides */
/** @var array $init */
/** @var array $lsInit */
/** @var array $lsPlugins */

// Preload Skin
$skin = empty($slides['properties']['attrs']['skin']) ? 'v6' : $slides['properties']['attrs']['skin'];
if (empty($GLOBALS['ls_style']['skin-' . $skin])) {
    $GLOBALS['ls_style']['skin-' . $skin] = true;

    $lsInit[] = '<link id="ls-skin-' . $skin . '" rel="stylesheet" href="' . LS_VIEWS_URL . "css/layerslider/skins/$skin/skin.css?v=" . LS_PLUGIN_VERSION . '">' . PHP_EOL;
}

// Get init code
foreach ($slides['properties']['attrs'] as $key => $val) {
    if (is_bool($val)) {
        $val = $val ? 'true' : 'false';
        $init[] = $key . ': ' . $val;
    } elseif (is_numeric($val)) {
        $init[] = $key . ': ' . $val;
    } else {
        $init[] = "$key: '$val'";
    }
}

// Full-size sliders
if ((!empty($slides['properties']['attrs']['type']) && 'fullsize' === $slides['properties']['attrs']['type']) && (empty($slides['properties']['attrs']['fullSizeMode']) || 'fitheight' !== $slides['properties']['attrs']['fullSizeMode'])) {
    $init[] = 'height: ' . $slides['properties']['props']['height'] . '';
}

if (!empty($lsPlugins)) {
    $init[] = 'plugins: ' . json_encode(array_unique($lsPlugins));
}

if (!LS_UNPACKED) {
    $init[] = 'hideWelcomeMessage: true';
}

$attr = ls_get_option('ls_rocketscript_ignore', false) ? ' data-cfasync="false"' : '';

$lsInit[] = "<script$attr>" . PHP_EOL;
$lsInit[] = 'document.addEventListener("DOMContentLoaded", function() {' . PHP_EOL;
$lsInit[] = '  if (typeof jQuery.fn.layerSlider == "undefined") {' . PHP_EOL;
$lsInit[] = '    if (window._layerSlider && window._layerSlider.showNotice) { ' . PHP_EOL;
$lsInit[] = '      window._layerSlider.showNotice(\'' . $sliderID . '\',\'jquery\');' . PHP_EOL;
$lsInit[] = '    }' . PHP_EOL;
$lsInit[] = '  } else {' . PHP_EOL;
$lsInit[] = '    jQuery("#' . $sliderID . ' .fancybox > img").unwrap();' . PHP_EOL;
$lsInit[] = '    jQuery("#' . $sliderID . '")';
if (!empty($slides['callbacks']) && is_array($slides['callbacks'])) {
    foreach ($slides['callbacks'] as $event => $function) {
        $lsInit[] = '      .on("' . $event . '", ' . stripslashes($function) . ')';
    }
}
$lsInit[] = '      .layerSlider({' . implode(', ', $init) . '});' . PHP_EOL;
$lsInit[] = '  }' . PHP_EOL;
$lsInit[] = '});' . PHP_EOL;
$lsInit[] = '</script>';
