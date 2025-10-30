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
<script type="text/html" id="ls-2d-transition-template">
    <div class="ls-transition-item">
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
                    <td><input type="text" name="duration" value="1000" data-help="<?php ls_e('The duration of the animation. This value is in millisecs, so the value 1000 measn 1 second.'); ?>"></td>
                    <td class="right"><a href="http://easings.net/" target="_blank"><?php ls_e('Easing'); ?></a></td>
                    <td>
                        <select name="easing" data-help="<?php ls_e('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'); ?>">
                            <option>linear</option>
                            <option>swing</option>
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
                            <option>easeInElastic</option>
                            <option>easeOutElastic</option>
                            <option>easeInOutElastic</option>
                            <option>easeInBack</option>
                            <option>easeOutBack</option>
                            <option>easeInOutBack</option>
                            <option>easeInBounce</option>
                            <option>easeOutBounce</option>
                            <option>easeInOutBounce</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="right"><?php ls_e('Type'); ?></td>
                    <td>
                        <select name="type" data-help="<?php ls_e('The type of the animation, either slide, fade or both (mixed).'); ?>">
                            <option value="slide"><?php ls_e('Slide'); ?></option>
                            <option value="fade"><?php ls_e('Fade'); ?></option>
                            <option value="mixed"><?php ls_e('Mixed'); ?></option>
                        </select>
                    </td>
                    <td class="right"><?php ls_e('Direction'); ?></td>
                    <td>
                        <select name="direction" data-help="<?php ls_e("The direction of the slide or mixed animation if you've chosen this type in the previous settings."); ?>">
                            <option value="top"><?php ls_e('Top'); ?></option>
                            <option value="right"><?php ls_e('Right'); ?></option>
                            <option value="bottom"><?php ls_e('Bottom'); ?></option>
                            <option value="left"><?php ls_e('Left'); ?></option>
                            <option value="random"><?php ls_e('Random'); ?></option>
                            <option value="topleft"><?php ls_e('Top left'); ?></option>
                            <option value="topright"><?php ls_e('Top right'); ?></option>
                            <option value="bottomleft"><?php ls_e('Bottom left'); ?></option>
                            <option value="bottomright"><?php ls_e('Bottom right'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="right"><?php ls_e('RotateX'); ?></td>
                    <td><input type="text" name="rotateX" value="0" data-help="<?php ls_e('The initial rotation of the individual tiles which will be animated to the default (0deg) value around the X axis. You can use negatuve values.'); ?>"></td>
                    <td class="right"><?php ls_e('RotateY'); ?></td>
                    <td><input type="text" name="rotateY" value="0" data-help="<?php ls_e('The initial rotation of the individual tiles which will be animated to the default (0deg) value around the Y axis. You can use negatuve values.'); ?>"></td>
                </tr>
                <tr>
                    <td class="right"><?php ls_e('RotateZ'); ?></td>
                    <td><input type="text" name="rotate" value="0" data-help="<?php ls_e('The initial rotation of the individual tiles which will be animated to the default (0deg) value around the Z axis. You can use negatuve values.'); ?>"></td>
                    <td class="right"><?php ls_e('Scale'); ?></td>
                    <td><input type="text" name="scale" value="1.0" data-help="<?php ls_e('The initial scale of the individual tiles which will be animated to the default (1.0) value.'); ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
</script>