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
<script type="text/html" id="tmpl-ls-add-slider-grid">
    <form method="post" id="ls-add-slider-template" class="preview">
        <input type="hidden" name="ls-add-new-slider" value="1">
        <h3><?php ls_e('Name your new slider'); ?></h3>
        <div class="inner">
            <input type="text" name="title" placeholder="<?php ls_e('e.g. Homepage slider'); ?>">
            <button class="button button-primary">
                <?php ls_e('Add slider'); ?>
                <i class="dashicons dashicons-arrow-right-alt"></i>
            </button>
        </div>
    </form>
</script>