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

// Custom transitions file
$custom_trs = _PS_MODULE_DIR_ . 'layerslider/views/js/custom.transitions.js';
$sample_trs = _PS_MODULE_DIR_ . 'layerslider/views/js/demos/transitions.js';

// Get transition file
if (file_exists($custom_trs)) {
    $data = call_user_func('file_get_contents', $custom_trs);
} else {
    $data = call_user_func('file_get_contents', $sample_trs);
}

// Get JSON data
if (!empty($data)) {
    $data = Tools::substr($data, 35);
    $data = Tools::substr($data, 0, -1);
    $data = json_decode($data, true);
}

// Get screen options
$lsScreenOptions = ls_get_option('ls-screen-options', []);

// Defaults
if (!isset($lsScreenOptions['showTooltips'])) {
    $lsScreenOptions['showTooltips'] = 'true';
}

// Function to convert array keys to property names
function lsTrGetProperty($key)
{
    switch ($key) {
        case 'scale3d':
            return 'Scale3D';
        case 'rotateX':
            return 'RotateX';
        case 'rotateY':
            return 'RotateY';
        case 'x':
            return 'MoveX';
        case 'y':
            return 'MoveY';
        case 'delay':
            return 'Delay';
        default:
            return $key;
    }
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


<!-- Import sample markup of transitions -->
<?php include LS_ROOT_PATH . '/templates/tmpl-2d-transition.php'; ?>
<?php include LS_ROOT_PATH . '/templates/tmpl-3d-transition.php'; ?>

<!-- Import Transition Gallery markup -->
<?php include LS_ROOT_PATH . '/templates/tmpl-transition-gallery.php'; ?>

<div class="wrap">

    <!-- Page title -->
    <h2>Creative Slider -
        <?php ls_e('Transition Builder'); ?>
        <a href="<?php echo $GLOBALS['context']->link->getAdminLink('AdminLayerSlider'); ?>" class="add-new-h2"><?php ls_e('Back to the list'); ?></a>
    </h2>

    <?php if (isset(${'_GET'}['edited'])) { ?>
        <div class="updated"><?php ls_e('Your changes has been saved!'); ?></div>
    <?php } ?>

    <!-- Editor box -->
    <form method="post" id="ls-tr-builder-form">
        <input type="hidden" name="ls-user-transitions" value="1">
        <input type="hidden" name="ls-transitions">

        <div class="ls-slider-settings ls-transition-settings">
            <div class="ls-box ls-settings">
                <div class="inner">
                    <div class="ls-settings-sidebar ls-transitions-sidebar">
                        <h3 class="subheader">
                            <?php ls_e('2D Transitions'); ?>
                            <a href="#" class="ls-import-transition">
                                <span class="dashicons dashicons-update"></span>
                                <?php ls_e('Import'); ?>
                            </a>
                            <a href="#" class="ls-add-transition">
                                <span class="dashicons dashicons-plus"></span>
                                <?php ls_e('Add New'); ?>
                            </a>
                        </h3>
                        <ul class="2d" data-type="2d">
                        <?php $hidenClass = ''; ?>
                        <?php if (!empty($data['t2d']) && is_array($data['t2d'])) { ?>
                            <?php $hidenClass = 'ls-hidden'; ?>
                            <?php foreach ($data['t2d'] as $tr) { ?>
                                <li>
                                    <span class="dashicons dashicons-menu"></span>
                                    <input type="text" value="<?php echo htmlspecialchars(html_entity_decode($tr['name'])); ?>" placeholder="<?php ls_e('Type transition name'); ?>">
                                    <a href="#" title="<?php ls_e('Remove transition'); ?>" class="dashicons dashicons-trash remove"></a>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        </ul>
                        <p class="ls-no-transition <?php echo $hidenClass; ?>"><?php ls_e('No 2D transitions yet.'); ?></p>
                        <h3 class="subheader">
                            <?php ls_e('3D Transitions'); ?>
                            <a href="#" class="ls-import-transition">
                                <span class="dashicons dashicons-update"></span>
                                <?php ls_e('Import'); ?>
                            </a>
                            <a href="#" class="ls-add-transition">
                                <span class="dashicons dashicons-plus"></span>
                                <?php ls_e('Add New'); ?>
                            </a>
                        </h3>
                        <ul class="3d" data-type="3d">
                        <?php $hidenClass = ''; ?>
                        <?php if (!empty($data['t3d']) && is_array($data['t3d'])) { ?>
                            <?php $hidenClass = 'ls-hidden'; ?>
                            <?php foreach ($data['t3d'] as $tr) { ?>
                                <li>
                                    <span class="dashicons dashicons-menu"></span>
                                    <input type="text" value="<?php echo htmlspecialchars(html_entity_decode($tr['name'])); ?>" placeholder="<?php ls_e('Type transition name'); ?>">
                                    <a href="#" title="<?php ls_e('Remove transition'); ?>" class="dashicons dashicons-trash remove"></a>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        </ul>
                        <p class="ls-no-transition <?php echo $hidenClass; ?>"><?php ls_e('No 3D transitions yet.'); ?></p>
                    </div>
                    <div class="ls-settings-contents ls-transition-contents">
                        <div class="ls-box ls-tr-builder">

                            <div class="ls-tr-options clearfix">
                                <div class="ls-builder-left ls-tr-list-3d">
                                <?php if (!empty($data['t3d']) && is_array($data['t3d'])) { ?>
                                    <?php foreach ($data['t3d'] as $key => $tr) { ?>
                                        <?php $activeClass = (0 == $key) ? ' active' : ''; ?>
                                        <div class="ls-transition-item<?php echo $activeClass; ?>">
                                            <table class="ls-box ls-tr-settings">
                                                <thead>
                                                    <tr>
                                                        <td colspan="2"><?php ls_e('Preview'); ?></td>
                                                        <td colspan="2"><?php ls_e('Tiles'); ?></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="ls-builder-preview ls-transition-preview">
                                                                <img src="<?php echo LS_VIEWS_URL; ?>img/admin/sample_slide_1.png" alt="preview image">
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <table class="tiles">
                                                                <tbody>
                                                                    <tr>
                                                                        <?php $tr['rows'] = is_array($tr['rows']) ? implode(',', $tr['rows']) : $tr['rows']; ?>
                                                                        <?php $tr['cols'] = is_array($tr['cols']) ? implode(',', $tr['cols']) : $tr['cols']; ?>
                                                                        <td class="right"><?php ls_e('Rows'); ?></td>
                                                                        <td><input type="text" name="rows" value="<?php echo $tr['rows']; ?>" data-help="<?php ls_e('<i>number</i> or <i>min,max</i> If you specify a value greater than 1, Creative Slider will cut your slide into tiles. You can specify here how many rows of your transition should have. If you specify two numbers separated with a comma, Creative Slider will use that as a range and pick a random number between your values.'); ?>"></td>
                                                                        <td class="right"><?php ls_e('Cols'); ?></td>
                                                                        <td><input type="text" name="cols" value="<?php echo $tr['cols']; ?>" data-help="<?php ls_e('<i>number</i> or <i>min,max</i> If you specify a value greater than 1, Creative Slider will cut your slide into tiles. You can specify here how many columns of your transition should have. If you specify two numbers separated with a comma, Creative Slider will use that as a range and pick a random number between your values.'); ?>"></td>
                                                                    </tr>
                                                                </tbody>
                                                                <tbody class="tile">
                                                                    <tr>
                                                                        <td class="right"><?php ls_e('Delay'); ?></td>
                                                                        <td><input type="text" name="delay" value="<?php echo $tr['tile']['delay']; ?>" data-help="<?php ls_e('You can apply a delay between the tiles and postpone their animation relative to each other.'); ?>"></td>
                                                                        <td class="right"><?php ls_e('Sequence'); ?></td>
                                                                        <td>
                                                                            <select name="sequence" data-help="<?php ls_e('You can control the animation order of the tiles here.'); ?>">
                                                                                <option value="forward"<?php echo ('forward' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Forward'); ?></option>
                                                                                <option value="reverse"<?php echo ('reverse' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Reverse'); ?></option>
                                                                                <option value="col-forward"<?php echo ('col-forward' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Col-forward'); ?></option>
                                                                                <option value="col-reverse"<?php echo ('col-reverse' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Col-reverse'); ?></option>
                                                                                <option value="random"<?php echo ('random' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Random'); ?></option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="right"><?php ls_e('Depth'); ?></td>
                                                                        <td colspan="3">
                                                                            <label data-help="<?php ls_e('The script tries to identify the optimal depth for your rotated objects (tiles). With this option you can force your objects to have a large depth when performing 180 degree (and its multiplies) rotation.'); ?>">
                                                                                <input type="checkbox" class="checkbox" name="depth" value="large"<?php echo isset($tr['tile']['depth']) ? ' checked="checked"' : ''; ?>>
                                                                                <?php ls_e('Large depth'); ?>
                                                                            </label>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <thead>
                                                    <tr>
                                                        <td colspan="4">
                                                            <?php ls_e('Before animation'); ?>
                                                            <p class="ls-builder-checkbox">
                                                                <label>
                                                                    <input type="checkbox"<?php isset($tr['before']) && print ' checked="checked"'; ?> class="ls-builder-collapse-toggle">
                                                                    <?php ls_e('Enabled'); ?>
                                                                </label>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody class="before<?php isset($tr['before']) || print ' ls-builder-collapsed'; ?>">
                                                    <tr>
                                                        <td class="right"><?php ls_e('Duration'); ?></td>
                                                        <td><input type="text" name="duration" value="<?php echo isset($tr['before']['duration']) ? $tr['before']['duration'] : '1000'; ?>" data-help="<?php ls_e('The duration of your animation. This value is in millisecs, so the value 1000 means 1 second.'); ?>"></td>
                                                        <td class="right"><a href="http://easings.net/" target="_blank"><?php ls_e('Easing'); ?></a></td>
                                                        <td>
                                                            <?php $tr['before']['easing'] = isset($tr['before']['easing']) ? $tr['before']['easing'] : 'easeInOutBack'; ?>
                                                            <select name="easing" data-help="<?php ls_e('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'); ?>">
                                                                <option<?php echo ('linear' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>linear</option>
                                                                <option<?php echo ('easeInQuad' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuad</option>
                                                                <option<?php echo ('easeOutQuad' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuad</option>
                                                                <option<?php echo ('easeInOutQuad' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuad</option>
                                                                <option<?php echo ('easeInCubic' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInCubic</option>
                                                                <option<?php echo ('easeOutCubic' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeOutCubic</option>
                                                                <option<?php echo ('easeInOutCubic' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutCubic</option>
                                                                <option<?php echo ('easeInQuart' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuart</option>
                                                                <option<?php echo ('easeOutQuart' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuart</option>
                                                                <option<?php echo ('easeInOutQuart' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuart</option>
                                                                <option<?php echo ('easeInQuint' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuint</option>
                                                                <option<?php echo ('easeOutQuint' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuint</option>
                                                                <option<?php echo ('easeInOutQuint' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuint</option>
                                                                <option<?php echo ('easeInSine' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInSine</option>
                                                                <option<?php echo ('easeOutSine' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeOutSine</option>
                                                                <option<?php echo ('easeInOutSine' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutSine</option>
                                                                <option<?php echo ('easeInExpo' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInExpo</option>
                                                                <option<?php echo ('easeOutExpo' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeOutExpo</option>
                                                                <option<?php echo ('easeInOutExpo' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutExpo</option>
                                                                <option<?php echo ('easeInCirc' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInCirc</option>
                                                                <option<?php echo ('easeOutCirc' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeOutCirc</option>
                                                                <option<?php echo ('easeInOutCirc' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutCirc</option>
                                                                <option<?php echo ('easeInBack' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInBack</option>
                                                                <option<?php echo ('easeOutBack' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeOutBack</option>
                                                                <option<?php echo ('easeInOutBack' == $tr['before']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutBack</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr class="transition">
                                                        <td colspan="4">
                                                            <ul class="ls-tr-tags">
                                                            <?php if (isset($tr['before']['transition']) && !empty($tr['before']['transition'])) { ?>
                                                                <?php foreach ($tr['before']['transition'] as $pkey => $prop) { ?>
                                                                    <li>
                                                                        <p>
                                                                            <span><?php echo lsTrGetProperty($pkey); ?></span>
                                                                            <input type="text" name="<?php echo $pkey; ?>" value="<?php echo $prop; ?>">
                                                                        </p>
                                                                        <a href="#" class="dashicons dashicons-dismiss"></a>
                                                                    </li>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            </ul>
                                                            <p class="ls-tr-add-property">
                                                                <a href="#" class="ls-icon-tr-add"><i class="dashicons dashicons-plus"></i><?php ls_e('Add new'); ?></a>
                                                                <select>
                                                                    <option value="scale3d,0.8"><?php ls_e('Scale3D'); ?></option>
                                                                    <option value="rotateX,90"><?php ls_e('RotateX'); ?></option>
                                                                    <option value="rotateY,90"><?php ls_e('RotateY'); ?></option>
                                                                    <option value="delay,200"><?php ls_e('Delay'); ?></option>
                                                                </select>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <thead>
                                                    <tr>
                                                        <td colspan="4">
                                                            <?php ls_e('Animation'); ?>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody class="animation">
                                                    <tr>
                                                        <td class="right"><?php ls_e('Duration'); ?></td>
                                                        <td><input type="text" name="duration" value="<?php echo $tr['animation']['duration']; ?>" data-help="<?php ls_e('The duration of your animation. This value is in millisecs, so the value 1000 means 1 second.'); ?>"></td>
                                                        <td class="right"><a href="http://easings.net/" target="_blank"><?php ls_e('Easing'); ?></a></td>
                                                        <td>
                                                            <select name="easing" data-help="<?php ls_e('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'); ?>">
                                                                <option<?php echo ('linear' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>linear</option>
                                                                <option<?php echo ('easeInQuad' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuad</option>
                                                                <option<?php echo ('easeOutQuad' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuad</option>
                                                                <option<?php echo ('easeInOutQuad' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuad</option>
                                                                <option<?php echo ('easeInCubic' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInCubic</option>
                                                                <option<?php echo ('easeOutCubic' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeOutCubic</option>
                                                                <option<?php echo ('easeInOutCubic' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutCubic</option>
                                                                <option<?php echo ('easeInQuart' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuart</option>
                                                                <option<?php echo ('easeOutQuart' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuart</option>
                                                                <option<?php echo ('easeInOutQuart' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuart</option>
                                                                <option<?php echo ('easeInQuint' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuint</option>
                                                                <option<?php echo ('easeOutQuint' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuint</option>
                                                                <option<?php echo ('easeInOutQuint' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuint</option>
                                                                <option<?php echo ('easeInSine' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInSine</option>
                                                                <option<?php echo ('easeOutSine' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeOutSine</option>
                                                                <option<?php echo ('easeInOutSine' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutSine</option>
                                                                <option<?php echo ('easeInExpo' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInExpo</option>
                                                                <option<?php echo ('easeOutExpo' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeOutExpo</option>
                                                                <option<?php echo ('easeInOutExpo' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutExpo</option>
                                                                <option<?php echo ('easeInCirc' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInCirc</option>
                                                                <option<?php echo ('easeOutCirc' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeOutCirc</option>
                                                                <option<?php echo ('easeInOutCirc' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutCirc</option>
                                                                <option<?php echo ('easeInBack' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInBack</option>
                                                                <option<?php echo ('easeOutBack' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeOutBack</option>
                                                                <option<?php echo ('easeInOutBack' == $tr['animation']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutBack</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="right"><?php ls_e('Direction'); ?></td>
                                                        <td>
                                                            <select name="direction" data-help="<?php ls_e('The direction of rotation.'); ?>">
                                                                <option value="vertical"<?php echo ('vertical' == $tr['animation']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Vertical'); ?></option>
                                                                <option value="horizontal"<?php echo ('horizontal' == $tr['animation']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Horizontal'); ?></option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr class="transition">
                                                        <td colspan="4">

                                                            <ul class="ls-tr-tags">
                                                            <?php if (isset($tr['animation']['transition']) && !empty($tr['animation']['transition'])) { ?>
                                                                <?php foreach ($tr['animation']['transition'] as $pkey => $prop) { ?>
                                                                    <li>
                                                                        <p>
                                                                            <span><?php echo lsTrGetProperty($pkey); ?></span>
                                                                            <input type="text" name="<?php echo $pkey; ?>" value="<?php echo $prop; ?>">
                                                                        </p>
                                                                        <a href="#" class="dashicons dashicons-dismiss"></a>
                                                                    </li>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            </ul>
                                                            <p class="ls-tr-add-property">
                                                                <a href="#" class="ls-icon-tr-add"><i class="dashicons dashicons-plus"></i><?php ls_e('Add new'); ?></a>
                                                                <select>
                                                                    <option value="scale3d,0.8"><?php ls_e('Scale3D'); ?></option>
                                                                    <option value="rotateX,90"><?php ls_e('RotateX'); ?></option>
                                                                    <option value="rotateY,90"><?php ls_e('RotateY'); ?></option>
                                                                    <option value="delay,200"><?php ls_e('Delay'); ?></option>
                                                                </select>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <thead>
                                                    <tr>
                                                        <td colspan="4">
                                                            <?php ls_e('After animation'); ?>
                                                            <p class="ls-builder-checkbox">
                                                                <label>
                                                                    <input type="checkbox"<?php isset($tr['after']) && print ' checked="checked"'; ?> class="ls-builder-collapse-toggle">
                                                                    <?php ls_e('Enabled'); ?>
                                                                </label>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody class="after<?php isset($tr['after']) || print ' ls-builder-collapsed'; ?>">
                                                    <tr>
                                                        <td class="right"><?php ls_e('Duration'); ?></td>
                                                        <td><input type="text" name="duration" value="<?php echo isset($tr['after']['duration']) ? $tr['after']['duration'] : '1000'; ?>" data-help="<?php ls_e('The duration of your animation. This value is in millisecs, so the value 1000 means 1 second.'); ?>"></td>
                                                        <td class="right"><a href="http://easings.net/" target="_blank"><?php ls_e('Easing'); ?></a></td>
                                                        <td>
                                                            <?php $tr['after']['easing'] = isset($tr['after']['easing']) ? $tr['after']['easing'] : 'easeInOutBack'; ?>
                                                            <select name="easing" data-help="<?php ls_e('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'); ?>">
                                                                <option<?php echo ('linear' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>linear</option>
                                                                <option<?php echo ('easeInQuad' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuad</option>
                                                                <option<?php echo ('easeOutQuad' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuad</option>
                                                                <option<?php echo ('easeInOutQuad' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuad</option>
                                                                <option<?php echo ('easeInCubic' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInCubic</option>
                                                                <option<?php echo ('easeOutCubic' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeOutCubic</option>
                                                                <option<?php echo ('easeInOutCubic' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutCubic</option>
                                                                <option<?php echo ('easeInQuart' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuart</option>
                                                                <option<?php echo ('easeOutQuart' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuart</option>
                                                                <option<?php echo ('easeInOutQuart' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuart</option>
                                                                <option<?php echo ('easeInQuint' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuint</option>
                                                                <option<?php echo ('easeOutQuint' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuint</option>
                                                                <option<?php echo ('easeInOutQuint' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuint</option>
                                                                <option<?php echo ('easeInSine' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInSine</option>
                                                                <option<?php echo ('easeOutSine' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeOutSine</option>
                                                                <option<?php echo ('easeInOutSine' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutSine</option>
                                                                <option<?php echo ('easeInExpo' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInExpo</option>
                                                                <option<?php echo ('easeOutExpo' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeOutExpo</option>
                                                                <option<?php echo ('easeInOutExpo' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutExpo</option>
                                                                <option<?php echo ('easeInCirc' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInCirc</option>
                                                                <option<?php echo ('easeOutCirc' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeOutCirc</option>
                                                                <option<?php echo ('easeInOutCirc' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutCirc</option>
                                                                <option<?php echo ('easeInBack' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInBack</option>
                                                                <option<?php echo ('easeOutBack' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeOutBack</option>
                                                                <option<?php echo ('easeInOutBack' == $tr['after']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutBack</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr class="transition">
                                                        <td colspan="4">
                                                            <ul class="ls-tr-tags">
                                                            <?php if (isset($tr['after']['transition']) && !empty($tr['after']['transition'])) { ?>
                                                                <?php foreach ($tr['after']['transition'] as $pkey => $prop) { ?>
                                                                    <li>
                                                                        <p>
                                                                            <span><?php echo lsTrGetProperty($pkey); ?></span>
                                                                            <input type="text" name="<?php echo $pkey; ?>" value="<?php echo $prop; ?>">
                                                                        </p>
                                                                        <a href="#" class="dashicons dashicons-dismiss"></a>
                                                                    </li>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            </ul>
                                                            <p class="ls-tr-add-property">
                                                                <a href="#" class="ls-icon-tr-add"><i class="dashicons dashicons-plus"></i><?php ls_e('Add new'); ?></a>
                                                                <select>
                                                                    <option value="scale3d,0.8"><?php ls_e('Scale3D'); ?></option>
                                                                    <option value="rotateX,90"><?php ls_e('RotateX'); ?></option>
                                                                    <option value="rotateY,90"><?php ls_e('RotateY'); ?></option>
                                                                    <option value="delay,200"><?php ls_e('Delay'); ?></option>
                                                                </select>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                </div>
                                <div class="ls-builder-right ls-tr-list-2d">

                                <?php if (!empty($data['t2d']) && is_array($data['t2d'])) { ?>
                                    <?php foreach ($data['t2d'] as $key => $tr) { ?>
                                        <?php $activeClass = (0 == $key) ? ' active' : ''; ?>
                                        <div class="ls-transition-item<?php echo $activeClass; ?>">
                                            <table class="ls-box ls-tr-settings bottomborder">
                                                <thead>
                                                    <tr>
                                                        <td colspan="2"><?php ls_e('Preview'); ?></td>
                                                        <td colspan="2"><?php ls_e('Tiles'); ?></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="ls-builder-preview ls-transition-preview">
                                                                <img src="<?php echo LS_VIEWS_URL; ?>img/admin/sample_slide_1.png" alt="preview image">
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <table class="tiles">
                                                                <tbody>
                                                                    <tr>
                                                                        <?php $tr['rows'] = is_array($tr['rows']) ? implode(',', $tr['rows']) : $tr['rows']; ?>
                                                                        <?php $tr['cols'] = is_array($tr['cols']) ? implode(',', $tr['cols']) : $tr['cols']; ?>
                                                                        <td class="right"><?php ls_e('Rows'); ?></td>
                                                                        <td><input type="text" name="rows" value="<?php echo $tr['rows']; ?>" data-help="<?php ls_e('<i>number</i> or <i>min,max</i> If you specify a value greater than 1, Creative Slider will cut your slide into tiles. You can specify here how many rows of your transition should have. If you specify two numbers separated with a comma, Creative Slider will use that as a range and pick a random number between your values.'); ?>"></td>
                                                                        <td class="right"><?php ls_e('Cols'); ?></td>
                                                                        <td><input type="text" name="cols" value="<?php echo $tr['cols']; ?>" data-help="<?php ls_e('<i>number</i> or <i>min,max</i> If you specify a value greater than 1, Creative Slider will cut your slide into tiles. You can specify here how many columns of your transition should have. If you specify two numbers separated with a comma, Creative Slider will use that as a range and pick a random number between your values.'); ?>"></td>
                                                                    </tr>
                                                                </tbody>
                                                                <tbody class="tile">
                                                                    <tr>
                                                                        <td class="right"><?php ls_e('Delay'); ?></td>
                                                                        <td><input type="text" name="delay" value="<?php echo $tr['tile']['delay']; ?>" data-help="<?php ls_e('You can apply a delay between the tiles and postpone their animation relative to each other.'); ?>"></td>
                                                                        <td class="right"><?php ls_e('Sequence'); ?></td>
                                                                        <td>
                                                                            <select name="sequence" data-help="<?php ls_e('You can control the animation order of the tiles here.'); ?>">
                                                                                <option value="forward"<?php echo ('forward' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Forward'); ?></option>
                                                                                <option value="reverse"<?php echo ('reverse' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Reverse'); ?></option>
                                                                                <option value="col-forward"<?php echo ('col-forward' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Col-forward'); ?></option>
                                                                                <option value="col-reverse"<?php echo ('col-reverse' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Col-reverse'); ?></option>
                                                                                <option value="random"<?php echo ('random' == $tr['tile']['sequence']) ? ' selected="selected"' : ''; ?>><?php ls_e('Random'); ?></option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <thead>
                                                    <tr>
                                                        <td colspan="4"><?php ls_e('Transition'); ?></td>
                                                    </tr>
                                                </thead>
                                                <tbody class="transition">
                                                    <tr>
                                                        <td class="right"><?php ls_e('Duration'); ?></td>
                                                        <td><input type="text" name="duration" value="<?php echo $tr['transition']['duration']; ?>" data-help="<?php ls_e('The duration of the animation. This value is in millisecs, so the value 1000 measn 1 second.'); ?>"></td>
                                                        <td class="right"><a href="http://easings.net/" target="_blank"><?php ls_e('Easing'); ?></a></td>
                                                        <td>
                                                            <select name="easing" data-help="<?php ls_e('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'); ?>">
                                                                <option<?php echo ('linear' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>linear</option>
                                                                <option<?php echo ('swing' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>swing</option>
                                                                <option<?php echo ('easeInQuad' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuad</option>
                                                                <option<?php echo ('easeOutQuad' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuad</option>
                                                                <option<?php echo ('easeInOutQuad' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuad</option>
                                                                <option<?php echo ('easeInCubic' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInCubic</option>
                                                                <option<?php echo ('easeOutCubic' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutCubic</option>
                                                                <option<?php echo ('easeInOutCubic' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutCubic</option>
                                                                <option<?php echo ('easeInQuart' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuart</option>
                                                                <option<?php echo ('easeOutQuart' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuart</option>
                                                                <option<?php echo ('easeInOutQuart' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuart</option>
                                                                <option<?php echo ('easeInQuint' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInQuint</option>
                                                                <option<?php echo ('easeOutQuint' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutQuint</option>
                                                                <option<?php echo ('easeInOutQuint' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutQuint</option>
                                                                <option<?php echo ('easeInSine' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInSine</option>
                                                                <option<?php echo ('easeOutSine' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutSine</option>
                                                                <option<?php echo ('easeInOutSine' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutSine</option>
                                                                <option<?php echo ('easeInExpo' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInExpo</option>
                                                                <option<?php echo ('easeOutExpo' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutExpo</option>
                                                                <option<?php echo ('easeInOutExpo' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutExpo</option>
                                                                <option<?php echo ('easeInCirc' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInCirc</option>
                                                                <option<?php echo ('easeOutCirc' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutCirc</option>
                                                                <option<?php echo ('easeInOutCirc' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutCirc</option>
                                                                <option<?php echo ('easeInElastic' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInElastic</option>
                                                                <option<?php echo ('easeOutElastic' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutElastic</option>
                                                                <option<?php echo ('easeInOutElastic' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutElastic</option>
                                                                <option<?php echo ('easeInBack' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInBack</option>
                                                                <option<?php echo ('easeOutBack' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutBack</option>
                                                                <option<?php echo ('easeInOutBack' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInOutBack</option>
                                                                <option<?php echo ('easeInBounce' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeInBounce</option>
                                                                <option<?php echo ('easeOutBounce' == $tr['transition']['easing']) ? ' selected="selected"' : ''; ?>>easeOutBounce</option>
                                                                <option<?php echo ('easeInOutBounce' == $tr['transition']['easing']) ? 'selected="selected"' : ''; ?>>easeInOutBounce</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="right"><?php ls_e('Type'); ?></td>
                                                        <td>
                                                            <select name="type" data-help="<?php ls_e('The type of the animation, either slide, fade or both (mixed).'); ?>">
                                                                <option value="slide"<?php echo ('slide' == $tr['transition']['type']) ? ' selected="selected"' : ''; ?>><?php ls_e('Slide'); ?></option>
                                                                <option value="fade"<?php echo ('fade' == $tr['transition']['type']) ? ' selected="selected"' : ''; ?>><?php ls_e('Fade'); ?></option>
                                                                <option value="mixed"<?php echo ('mixed' == $tr['transition']['type']) ? ' selected="selected"' : ''; ?>><?php ls_e('Mixed'); ?></option>
                                                            </select>
                                                        </td>
                                                        <td class="right"><?php ls_e('Direction'); ?></td>
                                                        <td>
                                                            <select name="direction" data-help="<?php ls_e("The direction of the slide or mixed animation if you've chosen this type in the previous settings."); ?>">
                                                                <option value="top"<?php echo ('top' == $tr['transition']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Top'); ?></option>
                                                                <option value="right"<?php echo ('right' == $tr['transition']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Right'); ?></option>
                                                                <option value="bottom"<?php echo ('bottom' == $tr['transition']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Bottom'); ?></option>
                                                                <option value="left"<?php echo ('left' == $tr['transition']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Left'); ?></option>
                                                                <option value="random"<?php echo ('random' == $tr['transition']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Random'); ?></option>
                                                                <option value="topleft"<?php echo ('topleft' == $tr['transition']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Top left'); ?></option>
                                                                <option value="topright"<?php echo ('topright' == $tr['transition']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Top right'); ?></option>
                                                                <option value="bottomleft"<?php echo ('bottomleft' == $tr['transition']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Bottom left'); ?></option>
                                                                <option value="bottomright"<?php echo ('bottomright' == $tr['transition']['direction']) ? ' selected="selected"' : ''; ?>><?php ls_e('Bottom right'); ?></option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="right"><?php ls_e('RotateX'); ?></td>
                                                        <td><input type="text" name="rotateX" value="<?php echo !empty($tr['transition']['rotateX']) ? $tr['transition']['rotateX'] : '0'; ?>" data-help="The initial rotation of the individual tiles which will be animated to the default (0deg) value around the X axis. You can use negatuve values."></td>
                                                        <td class="right"><?php ls_e('RotateY'); ?></td>
                                                        <td><input type="text" name="rotateY" value="<?php echo !empty($tr['transition']['rotateY']) ? $tr['transition']['rotateY'] : '0'; ?>" data-help="The initial rotation of the individual tiles which will be animated to the default (0deg) value around the Y axis. You can use negatuve values."></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="right"><?php ls_e('RotateZ'); ?></td>
                                                        <td><input type="text" name="rotate" value="<?php echo !empty($tr['transition']['rotate']) ? $tr['transition']['rotate'] : '0'; ?>" data-help="The initial rotation of the individual tiles which will be animated to the default (0deg) value around the Z axis. You can use negatuve values."></td>
                                                        <td class="right"><?php ls_e('Scale'); ?></td>
                                                        <td><input type="text" name="scale" value="<?php echo !empty($tr['transition']['scale']) ? $tr['transition']['scale'] : '1.0'; ?>" data-help="The initial scale of the individual tiles which will be animated to the default (1.0) value."></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

        <div class="ls-publish">
            <?php if (is_writable(dirname($custom_trs))) { ?>
                <button class="button button-primary button-hero"><?php ls_e('Save changes'); ?></button>
            <?php } else { ?>
                <?php printf(ls__('Before you can save your changes, you need to make your "%s" folder writable.'), ls_upload_dir()['basedir']); ?>
            <?php } ?>
        </div>
    </form>
</div>
<script type="text/javascript">
    var pluginPath = '<?php echo LS_VIEWS_URL; ?>';
    var lsTrImgPath = '<?php echo LS_VIEWS_URL; ?>img/admin/';
    var lsScreenOptions = <?php echo json_encode($lsScreenOptions); ?>;
</script>
