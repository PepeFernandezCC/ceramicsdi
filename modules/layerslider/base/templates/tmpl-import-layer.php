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
?>
<script type="text/html" id="tmpl-import-layer">
    <div id="tmpl-import-layer-modal-window">
        <header>
            <h1><?php ls_e('Import Layer'); ?></h1>
            <b class="dashicons dashicons-no"></b>
        </header>
        <div class="km-ui-modal-scrollable">
            <div class="columns clearfix">
                    <div class="third third-1">
                        <h3><?php ls_e('Select slider'); ?></h3>
                    </div>
                    <div class="third third-2">
                        <h3><?php ls_e('Choose a Slide'); ?></h3>
                    </div>
                    <div class="third third-3">
                        <h3><?php ls_e('Click to import'); ?></h3>
                    </div>
            </div>
            <div class="columns clearfix">
                <div class="third third-1 ls-import-layer-sliders">
                    <?php ls_e('Loading ...'); ?>
                </div>
                <div class="third third-2 ls-import-layer-slides">
                    <?php ls_e('Select a slider first.'); ?>
                </div>
                <div class="third third-3 ls-import-layer-layers">
                    <?php ls_e('Select a slide first.'); ?>
                </div>
            </div>
        </div>
    </div>
</script>
