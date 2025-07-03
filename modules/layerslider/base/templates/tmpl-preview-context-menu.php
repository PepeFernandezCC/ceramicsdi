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
?>
<script type="text/html" id="tmpl-ls-preview-context-menu">
    <div class="ls-preview-context-menu">
        <ul>
            <li class="group">
                <i class="dashicons dashicons-plus"></i>
                <?php ls_e('Add Layer', 'LayerSlider') ?>
                <div>
                    <ul class="ls-context-add-layer">
                        <li data-type="img">
                            <i class="dashicons dashicons-format-image"></i>
                            <?php ls_e('Image', 'LayerSlider') ?>
                        </li>
                        <li data-type="text">
                            <i class="dashicons dashicons-text"></i>
                            <?php ls_e('Text', 'LayerSlider') ?>
                        </li>
                        <li data-type="button">
                            <i class="dashicons dashicons-marker"></i>
                            <?php ls_e('Button', 'LayerSlider') ?>
                        </li>
                        <li data-type="media">
                            <i class="dashicons dashicons-video-alt3"></i>
                            <?php ls_e('Video / Audio', 'LayerSlider') ?>
                        </li>
                        <li data-type="html">
                            <i class="dashicons dashicons-editor-code"></i>
                            <?php ls_e('HTML', 'LayerSlider') ?>
                        </li>
                        <li data-type="post">
                            <i class="dashicons dashicons-admin-post"></i>
                            <?php ls_e('Dynamic Layer', 'LayerSlider') ?>
                        </li>
                        <li data-type="import">
                            <i class="dashicons dashicons-upload"></i>
                            <?php ls_e('Import Layer', 'LayerSlider') ?>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="group ls-context-overlapping-layers">
                <i class="dashicons dashicons-menu"></i>
                <?php ls_e('Overlapping Layers', 'LayerSlider') ?>
                <div>
                    <ul></ul>
                </div>
            </li>
            <li class="ls-context-menu-duplicate">
                <i class="dashicons dashicons-admin-page"></i>
                <?php ls_e('Duplicate Layer', 'LayerSlider') ?>
            </li>
            <li class="ls-context-menu-remove">
                <i class="dashicons dashicons-trash"></i>
                <?php ls_e('Remove Layer', 'LayerSlider') ?>
            </li>
            <li class="divider"></li>
            <li class="ls-context-menu-copy-layer">
                <i class="dashicons dashicons-clipboard"></i>
                <?php ls_e('Copy Layer') ?>
            </li>
            <li class="ls-context-menu-paste-layer">
                <i class="dashicons dashicons-admin-page"></i>
                <?php ls_e('Paste Layer') ?>
            </li>
            <li class="divider"></li>
            <li class="ls-context-menu-hide">
                <i class="dashicons dashicons-visibility"></i>
                <?php ls_e('Toggle Layer Visibility', 'LayerSlider') ?>
            </li>
            <li class="ls-context-menu-lock">
                <i class="dashicons dashicons-lock"></i>
                <?php ls_e('Toggle Layer Locking', 'LayerSlider') ?>
            </li>
            <li class="divider"></li>
            <li class="ls-context-menu-copy-styles">
                <i class="dashicons dashicons-clipboard"></i>
                <?php ls_e('Copy Layer Styles') ?>
            </li>
            <li class="ls-context-menu-paste-styles">
                <i class="dashicons dashicons-admin-page"></i>
                <?php ls_e('Paste Layer Styles') ?>
            </li>
        </ul>
    </div>
</script>
