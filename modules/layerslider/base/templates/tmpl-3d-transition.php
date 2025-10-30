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
<script type="text/html" id="ls-3d-transition-template">
    <div class="ls-transition-item">
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
                                    <td class="right"><?php ls_e('Rows'); ?></td>
                                    <td><input type="text" name="rows" value="1" data-help="<?php ls_e('<i>number</i> or <i>min,max</i> If you specify a value greater than 1, Creative Slider will cut your slide into tiles. You can specify here how many rows of your transition should have. If you specify two numbers separated with a comma, Creative Slider will use that as a range and pick a random number between your values.'); ?>"></td>
                                    <td class="right"><?php ls_e('Cols'); ?></td>
                                    <td><input type="text" name="cols" value="1" data-help="<?php ls_e('<i>number</i> or <i>min,max</i> If you specify a value greater than 1, Creative Slider will cut your slide into tiles. You can specify here how many columns of your transition should have. If you specify two numbers separated with a comma, Creative Slider will use that as a range and pick a random number between your values.'); ?>"></td>
                                </tr>
                            </tbody>
                            <tbody class="tile">
                                <tr>
                                    <td class="right"><?php ls_e('Delay'); ?></td>
                                    <td><input type="text" name="delay" value="75" data-help="<?php ls_e('You can apply a delay between the tiles and postpone their animation relative to each other.'); ?>"></td>
                                    <td class="right"><?php ls_e('Sequence'); ?></td>
                                    <td>
                                        <select name="sequence" data-help="<?php ls_e('You can control the animation order of the tiles here.'); ?>">
                                            <option value="forward"><?php ls_e('Forward'); ?></option>
                                            <option value="reverse"><?php ls_e('Reverse'); ?></option>
                                            <option value="col-forward"><?php ls_e('Col-forward'); ?></option>
                                            <option value="col-reverse"><?php ls_e('Col-reverse'); ?></option>
                                            <option value="random"><?php ls_e('Random'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="right"><?php ls_e('Depth'); ?></td>
                                    <td colspan="3">
                                        <label data-help="<?php ls_e('The script tries to identify the optimal depth for your rotated objects (tiles). With this option you can force your objects to have a large depth when performing 180 degree (and its multiplies) rotation.'); ?>">
                                            <input type="checkbox" class="checkbox" name="depth" value="large">
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
                            <label><input type="checkbox" class="ls-builder-collapse-toggle"> <?php ls_e('Enabled'); ?></label>
                        </p>
                    </td>
                </tr>
            </thead>
            <tbody class="before ls-builder-collapsed">
                <tr>
                    <td class="right"><?php ls_e('Duration'); ?></td>
                    <td><input type="text" name="duration" value="1000" data-help="<?php ls_e('The duration of your animation. This value is in millisecs, so the value 1000 means 1 second.'); ?>"></td>
                    <td class="right"><a href="http://easings.net/" target="_blank"><?php ls_e('Easing'); ?></a></td>
                    <td>
                        <select name="easing" data-help="<?php ls_e('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'); ?>">
                            <option>linear</option>
                            <option>easeInQuad</option>
                            <option>easeOutQuad</option>
                            <option>easeInOutQuad</option>
                            <option>easeInCubic</option>
                            <option>easeOutCubic</option>
                            <option>easeInOutCubic</option>
                            <option>easeInQuart</option>
                            <option>easeOutQuart</option>
                            <option>easeInOutQuart</option>
                            <option>easeInQuint</option>
                            <option>easeOutQuint</option>
                            <option selected="selected">easeInOutQuint</option>
                            <option>easeInSine</option>
                            <option>easeOutSine</option>
                            <option>easeInOutSine</option>
                            <option>easeInExpo</option>
                            <option>easeOutExpo</option>
                            <option>easeInOutExpo</option>
                            <option>easeInCirc</option>
                            <option>easeOutCirc</option>
                            <option>easeInOutCirc</option>
                            <option>easeInBack</option>
                            <option>easeOutBack</option>
                            <option>easeInOutBack</option>
                        </select>
                    </td>
                </tr>
                <tr class="transition">
                    <td colspan="4">
                        <ul class="ls-tr-tags"></ul>
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
                    <td><input type="text" name="duration" value="1000" data-help="<?php ls_e('The duration of your animation. This value is in millisecs, so the value 1000 means 1 second.'); ?>"></td>
                    <td class="right"><a href="http://easings.net/" target="_blank"><?php ls_e('Easing'); ?></a></td>
                    <td>
                        <select name="easing" data-help="<?php ls_e('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'); ?>">
                            <option>linear</option>
                            <option>easeInQuad</option>
                            <option>easeOutQuad</option>
                            <option>easeInOutQuad</option>
                            <option>easeInCubic</option>
                            <option>easeOutCubic</option>
                            <option>easeInOutCubic</option>
                            <option>easeInQuart</option>
                            <option>easeOutQuart</option>
                            <option>easeInOutQuart</option>
                            <option>easeInQuint</option>
                            <option>easeOutQuint</option>
                            <option selected="selected">easeInOutQuint</option>
                            <option>easeInSine</option>
                            <option>easeOutSine</option>
                            <option>easeInOutSine</option>
                            <option>easeInExpo</option>
                            <option>easeOutExpo</option>
                            <option>easeInOutExpo</option>
                            <option>easeInCirc</option>
                            <option>easeOutCirc</option>
                            <option>easeInOutCirc</option>
                            <option>easeInBack</option>
                            <option>easeOutBack</option>
                            <option>easeInOutBack</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="right"><?php ls_e('Direction'); ?></td>
                    <td>
                        <select name="direction" data-help="<?php ls_e('The direction of rotation.'); ?>">
                            <option value="vertical"><?php ls_e('Vertical'); ?></option>
                            <option value="horizontal" selected="selected"><?php ls_e('Horizontal'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="transition">
                    <td colspan="4">
                        <ul class="ls-tr-tags">
                            <li>
                                <p>
                                    <span><?php ls_e('RotateX'); ?></span>
                                    <input type="text" name="rotateY" value="90">
                                </p>
                                <a href="#" class="dashicons dashicons-dismiss"></a>
                            </li>
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
                            <label><input type="checkbox" class="ls-builder-collapse-toggle"> <?php ls_e('Enabled'); ?></label>
                        </p>
                    </td>
                </tr>
            </thead>
            <tbody class="after ls-builder-collapsed">
                <tr>
                    <td class="right"><?php ls_e('Duration'); ?></td>
                    <td><input type="text" name="duration" value="1000" data-help="<?php ls_e('The duration of your animation. This value is in millisecs, so the value 1000 means 1 second.'); ?>"></td>
                    <td class="right"><a href="http://easings.net/" target="_blank"><?php ls_e('Easing'); ?></a></td>
                    <td>
                        <select name="easing" data-help="<?php ls_e('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'); ?>">
                            <option>linear</option>
                            <option>easeInQuad</option>
                            <option>easeOutQuad</option>
                            <option>easeInOutQuad</option>
                            <option>easeInCubic</option>
                            <option>easeOutCubic</option>
                            <option>easeInOutCubic</option>
                            <option>easeInQuart</option>
                            <option>easeOutQuart</option>
                            <option>easeInOutQuart</option>
                            <option>easeInQuint</option>
                            <option>easeOutQuint</option>
                            <option selected="selected">easeInOutQuint</option>
                            <option>easeInSine</option>
                            <option>easeOutSine</option>
                            <option>easeInOutSine</option>
                            <option>easeInExpo</option>
                            <option>easeOutExpo</option>
                            <option>easeInOutExpo</option>
                            <option>easeInCirc</option>
                            <option>easeOutCirc</option>
                            <option>easeInOutCirc</option>
                            <option>easeInBack</option>
                            <option>easeOutBack</option>
                            <option>easeInOutBack</option>
                        </select>
                    </td>
                </tr>
                <tr class="transition">
                    <td colspan="4">
                        <ul class="ls-tr-tags"></ul>
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
</script>