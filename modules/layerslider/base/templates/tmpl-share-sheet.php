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

$time = time();
$installed = ls_get_option('ls-date-installed', 0);
$level = ls_get_option('ls-share-displayed', 1);

switch ($level) {
    case 1:
        $time = $time-60*60*24*14;
        $odds = 100;
        break;

    case 2:
        $time = $time-60*60*24*30*2;
        $odds = 200;
        break;

    case 3:
        $time = $time-60*60*24*30*6;
        $odds = 300;
        break;

    default:
        $time = $time-60*60*24*30*6;
        $odds = 1000;
        break;
}

if ($installed && $time > $installed) {
    if (mt_rand(1, $odds) == 3) {
        ls_update_option('ls-share-displayed', ++$level);
        ?>
        <div class="ls-overlay" data-manualclose="true"></div>
        <div id="ls-share-template" class="ls-modal ls-box">
            <h3>
                <?php ls_e('Enjoy using Creative Slider?', 'LayerSlider') ?>
                <a href="#" class="dashicons dashicons-no-alt"></a>
            </h3>
            <div class="inner desc">
                <?php ls_e("If so, please consider recommending it to your friends on your favorite social network!", "LayerSlider"); ?>
            </div>
            <div class="inner">
                <a href="https://www.facebook.com/sharer/sharer.php?u=https://addons.prestashop.com/sliders-galleries/19062-creative-slider-responsive-slideshow.html" target="_blank">
                    <i class="dashicons dashicons-facebook-alt"></i> <?php ls_e('Share', 'LayerSlider') ?>
                </a>

                <a href="http://www.twitter.com/share?url=https%3A%2F%2Faddons.prestashop.com%2Fsliders-galleries%2F19062-creative-slider-responsive-slideshow.html&amp;text=Check%20out%20Creative%20Slider%2C%20an%20awesome%20%23slider%20%23module%20for%20%23PrestaShop&amp;via=WebshopWorks" target="_blank">
                    <i class="dashicons dashicons-twitter"></i> <?php ls_e('Tweet', 'LayerSlider') ?>
                </a>
            </div>
        </div>
        <?php
    }
}
