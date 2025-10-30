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
<script type="text/html" id="ls-static-layer-item-template">
    <li>
        <a href="#" class="dashicons dashicons-redo ls-icon-jump" data-help="<?php ls_e('Click this icon to jump to the slide where this layer was added on, so you can quickly edit its settings.'); ?>"></a>
        <div class="ls-sublayer-thumb"></div>
        <span class="ls-sublayer-title"><?php printf(ls__('Layer #%d'), '1'); ?></span>
    </li>
</script>
