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

$slider = &$slider;
$lsDefaults = &$lsDefaults;

$sDefs = &$lsDefaults['slider'];
$sProps = &$slider['properties'];
?>

<!-- Slider title -->
<div class="ls-slider-titlewrap">
    <?php $sliderName = !empty($sProps['title']) ? htmlspecialchars(stripslashes($sProps['title'])) : ''; ?>
    <input type="text" name="title" value="<?php echo $sliderName; ?>" id="title" autocomplete="off" placeholder="<?php ls_e('Type your slider name here'); ?>">
    <div class="ls-slider-slug">
        <?php ls_e('Slider slug'); ?>:<input type="text" name="slug" value="<?php echo !empty($sProps['slug']) ? $sProps['slug'] : ''; ?>" autocomplete="off" placeholder="<?php ls_e('e.g. homepageslider'); ?>" data-help="Set a custom slider identifier to use in shortcodes instead of the database ID. Needs to be unique, and can contain only alphanumeric characters. This setting is optional.">
    </div>
</div>

<!-- Slider settings -->
<div class="ls-box ls-settings">
    <h3 class="header medium">
        <?php ls_e('Slider Settings'); ?>
        <div class="ls-slider-settings-advanced">
            <?php ls_e('Show advanced settings'); ?> <input type="checkbox" data-toggleitems=".ls-settings-contents .ls-advanced">
        </div>
    </h3>
    <div class="inner">
        <ul class="ls-settings-sidebar">
            <li data-deeplink="publish">
                <i class="dashicons dashicons-calendar-alt"></i>
                <strong><?php ls_e('Publish'); ?></strong>
            </li>
            <li data-deeplink="layout" class="active">
                <i class="dashicons dashicons-editor-distractionfree"></i>
                <strong><?php ls_e('Layout'); ?></strong>
            </li>
            <li data-deeplink="mobile">
                <i class="dashicons dashicons-smartphone"></i>
                <strong><?php ls_e('Mobile'); ?></strong>
            </li>
            <li data-deeplink="slideshow">
                <i class="dashicons dashicons-editor-video"></i>
                <strong><?php ls_e('Slideshow'); ?></strong>
            </li>
            <li data-deeplink="appearance">
                <i class="dashicons dashicons-admin-appearance"></i>
                <strong><?php ls_e('Appearance'); ?></strong>
            </li>
            <li data-deeplink="navigation">
                <i class="dashicons dashicons-image-flip-horizontal"></i>
                <strong><?php ls_e('Navigation Area'); ?></strong>
            </li>
            <li data-deeplink="thumbnav">
                <i class="dashicons dashicons-screenoptions"></i>
                <strong><?php ls_e('Thumbnail Navigation'); ?></strong>
            </li>
            <li data-deeplink="videos">
                <i class="dashicons dashicons-video-alt3"></i>
                <strong><?php ls_e('Videos'); ?></strong>
            </li>
            <li data-deeplink="yourlogo">
                <i class="dashicons dashicons-admin-post"></i>
                <strong><?php ls_e('YourLogo'); ?></strong>
            </li>
            <li data-deeplink="transition">
                <i class="dashicons dashicons-admin-settings"></i>
                <strong><?php ls_e('Default Options'); ?></strong>
            </li>
            <li data-deeplink="misc">
                <i class="dashicons dashicons-admin-generic"></i>
                <strong><?php ls_e('Misc'); ?></strong>
            </li>

        </ul>
        <div class="ls-settings-contents">
            <input type="hidden" name="sliderVersion" value="<?php echo LS_PLUGIN_VERSION; ?>">
            <table>
                <!-- Publish -->
                <tbody>
                    <tr><th colspan="2"><?php echo $sDefs['status']['name']; ?></th></tr>
                    <tr>
                        <td colspan="2" class="hero">
                            <p>
                                <?php lsGetCheckbox($sDefs['status'], $sProps, ['class' => 'hero ls-publish-checkbox']); ?>
                                <?php echo $sDefs['status']['desc']; ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th class="half"><?php echo $sDefs['scheduleStart']['name']; ?></th>
                        <th class="half"><?php echo $sDefs['scheduleEnd']['name']; ?></th>
                    </tr>
                    <tr>
                        <td class="half">
                            <div class="ls-datepicker-wrapper">
                                <label><?php ls_e('Interpreted as:'); ?> <span></span></label>
                                <?php lsGetInput($sDefs['scheduleStart'], $sProps, ['class' => 'ls-datepicker-input', 'data-schedule-key' => 'schedule_start']); ?>
                            </div>
                        </td>
                        <td class="half">
                            <div class="ls-datepicker-wrapper">
                                <label><?php ls_e('Interpreted as:'); ?> <span></span></label>
                                <?php lsGetInput($sDefs['scheduleEnd'], $sProps, ['class' => 'ls-datepicker-input', 'data-schedule-key' => 'schedule_end']); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="hero">
                            <div class="ls-schedule-desc"><?php echo $sDefs['scheduleStart']['desc']; ?></div>
                        </td>
                    </tr>
                </tbody>

                <!-- Layout -->
                <tbody class="active">
                    <tr>
                        <td><?php echo $sDefs['hook']['name']; ?></td>
                        <td>
                            <?php lsGetInput($sDefs['hook'], $sProps, []); ?>
                            <a href="#misc"><i class="dashicons dashicons-admin-generic ls-conf"></i><?php ls_e('Configure'); ?></a>
                        </td>
                        <td class="desc"><?php echo $sDefs['hook']['desc']; ?></td>
                    </tr>
                    <tr><th colspan="3"><?php ls_e('Slider type & dimensions'); ?></th></tr>
                    <tr>
                        <td colspan="3" class="ls-slider-dimensions">
                            <div data-type="fixedsize">
                                <img src="<?php echo LS_VIEWS_URL . 'img/admin/layout-fixed.png'; ?>">
                                <span><?php ls_e('Fixed size'); ?></span>
                            </div>

                            <div data-type="responsive">
                                <img src="<?php echo LS_VIEWS_URL . 'img/admin/layout-responsive.png'; ?>">
                                <span><?php ls_e('Responsive'); ?></span>
                            </div>

                            <div data-type="fullwidth">
                                <img src="<?php echo LS_VIEWS_URL . 'img/admin/layout-full-width.png'; ?>">
                                <span><?php ls_e('Full width'); ?></span>
                            </div>

                            <div data-type="fullsize">
                                <img src="<?php echo LS_VIEWS_URL . 'img/admin/layout-full-screen.png'; ?>">
                                <span><?php ls_e('Full size'); ?></span>
                            </div>
                            <?php lsGetInput($sDefs['type'], $sProps); ?>
                        </td>
                    </tr>
                    <?php lsOptionRow('input', $sDefs['width'], $sProps, [], 'ls-popup-hide'); ?>
                    <?php lsOptionRow('input', $sDefs['height'], $sProps, [], 'ls-popup-hide'); ?>
                    <?php lsOptionRow('input', $sDefs['maxWidth'], $sProps, [], 'ls-popup-hide'); ?>
                    <?php lsOptionRow('input', $sDefs['responsiveUnder'], $sProps, [], 'full-width-row ls-popup-hide'); ?>
                    <?php lsOptionRow('select', $sDefs['fullSizeMode'], $sProps, [], 'full-size-row ls-popup-hide'); ?>
                    <?php lsOptionRow('checkbox', $sDefs['fitScreenWidth'], $sProps, [], 'full-width-row full-size-row ls-popup-hide'); ?>
                    <?php lsOptionRow('checkbox', $sDefs['allowFullscreen'], $sProps, [], 'ls-popup-hide'); ?>

                    <tr class="ls-advanced ls-hidden"><th colspan="3"><?php ls_e('Other settings'); ?></th></tr>
                    <?php lsOptionRow('input', $sDefs['maxRatio'], $sProps); ?>
                    <tr class="ls-advanced ls-hidden">
                        <td style="vertical-align: top; padding-top: 10px;">
                            <div>
                                <i class="dashicons dashicons-flag" data-help="Advanced option"></i>
                                <?php echo $sDefs['insertMethod']['name']; ?>
                            </div>
                        </td>
                        <td>
                            <?php lsGetSelect($sDefs['insertMethod'], $sProps); ?>
                            <?php lsGetInput($sDefs['insertSelector'], $sProps); ?>
                        </td>
                        <td class="desc"><?php echo $sDefs['insertMethod']['desc']; ?></td>
                    </tr>
                    <?php lsOptionRow('select', $sDefs['clipSlideTransition'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['preventSliderClip'], $sProps, [], 'full-width-row full-size-row'); ?>
                </tbody>


                <!-- Mobile -->
                <tbody>
                    <?php lsOptionRow('checkbox', $sDefs['slideOnSwipe'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['optimizeForMobile'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Control display by user agent'); ?></th></tr>
                    <?php lsOptionRow('checkbox', $sDefs['disableOnMobile'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['disableOnTablet'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['disableOnDesktop'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Control display by device width'); ?></th></tr>
                    <?php lsOptionRow('input', $sDefs['hideUnder'], $sProps); ?>
                    <?php lsOptionRow('input', $sDefs['hideOver'], $sProps); ?>
                </tbody>

                <!-- Slideshow -->
                <tbody>
                    <tr><th colspan="3"><?php ls_e('Slideshow behavior'); ?></th></tr>
                    <tr>
                        <td><?php echo $sDefs['firstSlide']['name']; ?></td>
                        <td><?php lsGetInput($sDefs['firstSlide'], $sProps); ?></td>
                        <td class="desc"><?php echo $sDefs['firstSlide']['desc']; ?></td>
                    </tr>
                    <?php lsOptionRow('checkbox', $sDefs['autoStart'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['pauseLayers'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['startInViewport'], $sProps); ?>
                    <?php lsOptionRow('select', $sDefs['pauseOnHover'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['hashChange'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Slideshow navigation'); ?></th></tr>
                    <?php lsOptionRow('checkbox', $sDefs['keybNavigation'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['touchNavigation'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Play By Scroll'); ?></th></tr>
                    <?php lsOptionRow('checkbox', $sDefs['playByScroll'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['playByScrollStart'], $sProps); ?>
                    <?php lsOptionRow('input', $sDefs['playByScrollSpeed'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Cycles'); ?></th></tr>
                    <?php lsOptionRow('input', $sDefs['loops'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['forceLoopNumber'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Other settings'); ?></th></tr>
                    <?php lsOptionRow('checkbox', $sDefs['twoWaySlideshow'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['shuffle'], $sProps); ?>
                </tbody>

                <!-- Appearance -->
                <tbody>
                    <tr><th colspan="3"><?php ls_e('Slider appearance'); ?></th></tr>
                    <tr>
                        <td><?php ls_e('Skin'); ?></td>
                        <td>
                            <select name="skin">
                            <?php $sProps['skin'] = empty($sProps['skin']) ? $sDefs['skin']['value'] : $sProps['skin']; ?>
                            <?php foreach (LsSources::getSkins() as $skin) { ?>
                                <option value="<?php echo $skin['handle']; ?>"<?php $skin['handle'] == $sProps['skin'] && print ' selected="selected"'; ?>>
                                    <?php echo $skin['name'] . (!empty($skin['info']['note']) ? " - {$skin['info']['note']}" : ''); ?>
                                </option>
                            <?php } ?>
                            </select>
                        </td>
                        <td class="desc"><?php echo $sDefs['skin']['desc']; ?></td>
                    </tr>
                    <?php lsOptionRow('input', $sDefs['sliderFadeInDuration'], $sProps); ?>
                    <?php lsOptionRow('input', $sDefs['sliderClasses'], $sProps); ?>
                    <tr>
                        <td><?php ls_e('Custom slider CSS'); ?></td>
                        <td colspan="2"><textarea name="sliderstyle" cols="30" rows="10"><?php echo !empty($sProps['sliderstyle']) ? $sProps['sliderstyle'] : $sDefs['sliderStyle']['value']; ?></textarea></td>
                    </tr>

                    <tr><th colspan="3"><?php ls_e('Slider global background'); ?></th></tr>
                    <?php lsOptionRow('input', $sDefs['globalBGColor'], $sProps, ['class' => 'input ls-colorpicker minicolors-input']); ?>
                    <tr>
                        <td><?php ls_e('Background image'); ?></td>
                        <td>
                            <?php $bgImage = !empty($sProps['backgroundimage']) ? $sProps['backgroundimage'] : null; ?>
                            <?php $bgImageId = !empty($sProps['backgroundimageId']) ? $sProps['backgroundimageId'] : null; ?>
                            <input type="hidden" name="backgroundimageId" value="<?php echo !empty($sProps['backgroundimageId']) ? $sProps['backgroundimageId'] : ''; ?>">
                            <input type="hidden" name="backgroundimage" value="<?php echo !empty($sProps['backgroundimage']) ? $sProps['backgroundimage'] : ''; ?>">
                            <div class="ls-image ls-global-background ls-upload" data-l10n-set="<?php ls_e('Click to set'); ?>" data-l10n-change="<?php ls_e('Click to change'); ?>">
                                <div><img src="<?php echo ls_apply_filters('ls_get_thumbnail', $bgImageId, $bgImage); ?>" alt=""></div>
                                <a href="#" class="dashicons dashicons-dismiss"></a>
                            </div>
                        </td>
                        <td class="desc"><?php echo $sDefs['globalBGImage']['desc']; ?></td>
                    </tr>
                    <?php lsOptionRow('select', $sDefs['globalBGRepeat'], $sProps); ?>
                    <?php lsOptionRow('select', $sDefs['globalBGAttachment'], $sProps); ?>
                    <?php lsOptionRow('input', $sDefs['globalBGPosition'], $sProps, ['class' => 'input']); ?>
                    <tr>
                        <td><?php echo $sDefs['globalBGSize']['name']; ?></td>
                        <td><?php lsGetInput($sDefs['globalBGSize'], $sProps, ['class' => 'input']); ?></div>
                        </td>
                        <td class="desc"><?php echo $sDefs['globalBGSize']['desc']; ?></td>
                    </tr>

                </tbody>

                <!-- Navigation Area -->
                <tbody>
                    <tr><th colspan="3"><?php ls_e('Show navigation buttons'); ?></th></tr>
                    <?php lsOptionRow('checkbox', $sDefs['navPrevNextButtons'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['navStartStopButtons'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['navSlideButtons'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Navigation buttons on hover'); ?></th></tr>
                    <?php lsOptionRow('checkbox', $sDefs['hoverPrevNextButtons'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['hoverSlideButtons'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Slideshow timers'); ?></th></tr>
                    <?php lsOptionRow('checkbox', $sDefs['barTimer'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['circleTimer'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['slideBarTimer'], $sProps); ?>
                </tbody>

                <!-- Thumbnail navigation -->
                <tbody>
                    <tr><th colspan="3"><?php ls_e('Appearance'); ?></th></tr>
                    <?php lsOptionRow('select', $sDefs['thumbnailNavigation'], $sProps); ?>
                    <?php lsOptionRow('input', $sDefs['thumbnailAreaWidth'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Thumbnail dimensions'); ?></th></tr>
                    <?php lsOptionRow('input', $sDefs['thumbnailWidth'], $sProps); ?>
                    <?php lsOptionRow('input', $sDefs['thumbnailHeight'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Thumbnail appearance'); ?></th></tr>
                    <?php lsOptionRow('input', $sDefs['thumbnailActiveOpacity'], $sProps); ?>
                    <?php lsOptionRow('input', $sDefs['thumbnailInactiveOpacity'], $sProps); ?>
                </tbody>

                <!-- Videos -->
                <tbody>
                    <?php lsOptionRow('checkbox', $sDefs['autoPlayVideos'], $sProps); ?>
                    <?php lsOptionRow('select', $sDefs['autoPauseSlideshow'], $sProps); ?>
                    <?php lsOptionRow('select', $sDefs['youtubePreviewQuality'], $sProps); ?>
                </tbody>


                <!-- YourLogo -->
                <tbody>
                    <tr>
                        <td><?php echo $sDefs['yourLogoImage']['name']; ?></td>
                        <td>
                            <?php $sProps['yourlogo'] = !empty($sProps['yourlogo']) ? $sProps['yourlogo'] : null; ?>
                            <?php $sProps['yourlogoId'] = !empty($sProps['yourlogoId']) ? $sProps['yourlogoId'] : null; ?>
                            <input type="hidden" name="yourlogoId" value="<?php echo !empty($sProps['yourlogoId']) ? $sProps['yourlogoId'] : ''; ?>">
                            <input type="hidden" name="yourlogo" value="<?php echo !empty($sProps['yourlogo']) ? $sProps['yourlogo'] : ''; ?>">
                            <div class="ls-image ls-upload ls-yourlogo-upload not-set" data-l10n-set="<?php ls_e('Click to set'); ?>" data-l10n-change="<?php ls_e('Click to change'); ?>">
                                <div><img src="<?php echo ls_apply_filters('ls_get_thumbnail', $sProps['yourlogoId'], $sProps['yourlogo']); ?>" alt=""></div>
                                <a href="#" class="dashicons dashicons-dismiss"></a>
                            </div>
                        </td>
                        <td class="desc"><?php echo $sDefs['yourLogoImage']['desc']; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $sDefs['yourLogoStyle']['name']; ?></td>
                        <td colspan="2">
                            <textarea name="yourlogostyle" cols="30" rows="10"><?php echo !empty($sProps['yourlogostyle']) ? $sProps['yourlogostyle'] : $sDefs['yourLogoStyle']['value']; ?></textarea>
                        </td>
                    </tr>
                    <?php lsOptionRow('input', $sDefs['yourLogoLink'], $sProps); ?>
                    <?php lsOptionRow('select', $sDefs['yourLogoTarget'], $sProps); ?>
                </tbody>

                <!-- Transition Defaults -->
                <tbody>
                    <tr><th colspan="3"><?php ls_e('Slide background defaults'); ?></th></tr>
                    <?php lsOptionRow('select', $sDefs['slideBGSize'], $sProps); ?>
                    <?php lsOptionRow('select', $sDefs['slideBGPosition'], $sProps); ?>
                    <tr><th colspan="3"><?php ls_e('Parallax defaults'); ?></th></tr>
                    <?php lsOptionRow('input', $sDefs['parallaxSensitivity'], $sProps); ?>
                    <?php lsOptionRow('select', $sDefs['parallaxCenterLayers'], $sProps); ?>
                    <?php lsOptionRow('input', $sDefs['parallaxCenterDegree'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['parallaxScrollReverse'], $sProps); ?>
                    <tr class="ls-advanced ls-hidden"><th colspan="3"><?php ls_e('Misc'); ?></th></tr>
                    <?php lsOptionRow('input', $sDefs['forceLayersOutDuration'], $sProps); ?>
                </tbody>

                <!-- Misc -->
                <tbody>
                    <?php /* lsOptionRow('checkbox', $sDefs['relativeURLs'], $sProps); */ ?>
                    <?php /* lsOptionRow('checkbox', $sDefs['useSrcset'], $sProps); */ ?>
                    <?php lsOptionRow('checkbox', $sDefs['enhancedLazyLoad'], $sProps); ?>
                    <?php lsOptionRow('checkbox', $sDefs['allowRestartOnResize'], $sProps); ?>
                    <?php lsOptionRow('select', $sDefs['preferBlendMode'], $sProps); ?>
                    <tr>
                        <td><?php ls_e('Slider preview image'); ?></td>
                        <td>
                            <?php $preview = !empty($slider['meta']['preview']) ? $slider['meta']['preview'] : null; ?>
                            <?php $previewId = !empty($slider['meta']['previewId']) ? $slider['meta']['previewId'] : null; ?>
                            <input type="hidden" name="previewId" value="<?php echo !empty($slider['meta']['previewId']) ? $slider['meta']['previewId'] : ''; ?>">
                            <input type="hidden" name="preview" value="<?php echo !empty($slider['meta']['preview']) ? $slider['meta']['preview'] : ''; ?>">
                            <div class="ls-image ls-slider-preview ls-upload" data-l10n-set="<?php ls_e('Click to set'); ?>" data-l10n-change="<?php ls_e('Click to change'); ?>">
                                <div><img src="<?php echo ls_apply_filters('ls_get_thumbnail', $previewId, $preview); ?>" alt=""></div>
                                <a href="#" class="dashicons dashicons-dismiss"></a>
                            </div>
                        </td>
                        <td class="desc"><?php ls_e('The preview image you can see in your list of sliders.'); ?></td>
                    </tr>
                </tbody>

            </table>
        </div>
        <div class="clear"></div>
    </div>
</div>
