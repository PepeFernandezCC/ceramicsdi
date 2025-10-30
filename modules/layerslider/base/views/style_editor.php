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

// Get uploads dir
$file = _PS_MODULE_DIR_ . 'layerslider/views/css/custom.css';

// Get contents
if (file_exists($file)) {
    $contents = call_user_func('file_get_contents', $file);
} else {
    $contents = '';
}

// Get screen options
$lsScreenOptions = ls_get_option('ls-screen-options', []);

// Defaults
if (!isset($lsScreenOptions['showTooltips'])) {
    $lsScreenOptions['showTooltips'] = 'true';
}
?>

<div id="ls-screen-options" class="metabox-prefs hidden">
    <div id="screen-options-wrap" class="hidden">
        <form id="ls-screen-options-form" method="post">
            <h5><?php ls_e('Show on screen'); ?></h5>
            <label>
                <input type="checkbox" name="showTooltips"<?php echo 'true' == $lsScreenOptions['showTooltips'] ? ' checked="checked"' : ''; ?>> <?php ls_e('Tooltips'); ?>
            </label>
        </form>
    </div>
    <div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
        <button type="button" id="show-settings-link" class="button show-settings" aria-controls="screen-options-wrap" aria-expanded="false"><?php ls_e('Screen Options'); ?></button>
    </div>
</div>
<div class="wrap">

    <!-- Page title -->
    <h2>Creative Slider -
        <?php ls_e('CSS Editor'); ?>
        <a href="<?php echo $GLOBALS['context']->link->getAdminLink('AdminLayerSlider'); ?>" class="add-new-h2"><?php ls_e('Back to the list'); ?></a>
    </h2>

    <!-- Error messages -->
    <?php if (isset(${'_GET'}['edited'])) { ?>
        <div class="ls-notification updated">
            <div><?php ls_e('Your changes has been saved!'); ?></div>
        </div>
    <?php } ?>
    <!-- End of error messages -->

    <!-- Editor box -->
    <div class="ls-box ls-skin-editor-box">
        <h3 class="header medium">
            <?php ls_e('Contents of your custom CSS file'); ?>
            <figure><span>|</span><?php ls_e('Ctrl+Q to fold/unfold a block'); ?></figure>
        </h3>
        <form method="post" class="inner">
            <input type="hidden" name="ls-user-css" value="1">
            <textarea rows="10" cols="50" name="contents" class="ls-codemirror"
           ><?php
            if (!empty($contents)) {
                echo htmlentities($contents);
            } else {
                echo "/*\n" . ls__('You can type here custom CSS code, which will be loaded both on your admin and front-end pages. Please make sure to not override layout properties (positions and sizes), as they can interfere with the sliders built-in responsive functionality. Here are few example targets to help you get started:');
                echo "\n*/\n\n";
                echo ".ls-container { /* Slider container */\n\n}\n\n";
                echo ".ls-layers { /* Layers wrapper */\n\n}\n\n";
                echo ".ls-3d-box div { /* Sides of 3D transition objects */\n\n}";
            } ?></textarea>
            <p class="footer">
            <?php if (!is_writable(dirname($file))) { ?>
                <?php ls_e('You need to make your uploads folder writable in order to save your changes.'); ?>
            <?php } else { ?>
                <button class="button-primary"><?php ls_e('Save changes'); ?></button>
                <?php ls_e('Using invalid CSS code could break the appearance of your site or your sliders. Changes cannot be reverted after saving.'); ?>
            <?php } ?>
            </p>
        </form>
    </div>
</div>
<script type="text/javascript">
    // Screen options
    var lsScreenOptions = <?php echo json_encode($lsScreenOptions); ?>;
</script>
