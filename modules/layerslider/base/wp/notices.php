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

// Update notice
if (false !== strpos($_SERVER['REQUEST_URI'], '?controller=AdminLayerSlider')) {
    ls_add_action('admin_notices', 'layerslider_dependency_notice');
}

function layerslider_dependency_notice()
{
    if (version_compare(PHP_VERSION, '5.4', '<') || !class_exists('DOMDocument')) {
        ?>
        <div class="layerslider_notice">
            <img src="<?php echo LS_VIEWS_URL . 'logo.png'; ?>" alt="Creative Slider icon">
            <h1><?php ls_e('Server configuration issues detected!'); ?></h1>
            <p>
                <?php ls_e('Module requires PHP 5.4+ with the following extensions installed: PHP DOM extension, PHP Multibyte String extension. Please contact with your hosting provider to resolve these dependencies, as it will likely prevent Creative Slider from functioning properly.'); ?>
                <strong><?php ls_e('This issue could result a blank page in slider builder.'); ?></strong>
            </p>
            <div class="clear"></div>
        </div>
        <?php
    }
}
