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
<script type="text/html" id="ls-layer-item-template">
    <li>
        <span class="ls-sublayer-sortable-handle dashicons dashicons-menu"></span>
        <span class="ls-sublayer-controls">
            <span class="ls-icon-eye dashicons dashicons-visibility" data-help="<?php ls_e('Toggle layer visibility.'); ?>"></span>
            <span class="ls-icon-lock dashicons dashicons-lock disabled" data-help="<?php ls_e('Prevent layer dragging in the editor.'); ?>"></span>
        </span>
        <div class="ls-sublayer-thumb"></div>
        <input type="text" name="subtitle" class="ls-sublayer-title" value="<?php printf(ls__('Layer #%d'), '1'); ?>">
        <a href="#" title="<?php ls_e('Duplicate this layer'); ?>" class="dashicons dashicons-admin-page duplicate"></a>
        <a href="#" title="<?php ls_e('Remove this layer'); ?>" class="dashicons dashicons-trash remove"></a>
    </li>
</script>
