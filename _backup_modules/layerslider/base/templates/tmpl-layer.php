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

if (defined('LS_INCLUDE')) {
    $lsDefaults = null;
    $googleFonts = null;
}
?>
<script type="text/html" id="ls-layer-template">
    <div class="ls-sublayer-page ls-sublayer-basic active">


        <div class="ls-set-screen-types">
            <?php ls_e('Show this layer on the following devices:', 'LayerSlider') ?>

                <button data-type="desktop" class="button dashicons dashicons-desktop playing" data-help="Show this layer on desktop."></button><!--
            --><button data-type="tablet" class="button dashicons dashicons-tablet" data-help="Show this layer on tablets."></button><!--
            --><button data-type="phone" class="button dashicons dashicons-smartphone" data-help="Show this layer on mobile phones."></button>

        </div>


        <input type="hidden" name="media" value="img">
        <div class="ls-layer-kind">
            <ul>
                <li data-section="img" class="active"><span class="dashicons dashicons-format-image"></span><?php ls_e('Image', 'LayerSlider') ?></li>
                <li data-section="text" data-placeholder="<?php ls_e('Enter text only content here ...', 'LayerSlider') ?>"><span class="dashicons dashicons-text"></span><?php ls_e('Text', 'LayerSlider') ?></li>
                <li data-section="button" data-placeholder="<?php ls_e('Enter the label of your button', 'LayerSlider') ?>"><span class="dashicons dashicons-marker"></span><?php ls_e('Button', 'LayerSlider') ?></li>
                <li data-section="media" data-placeholder="<?php ls_e('Paste embed code here   or   add self-hosted media ...', 'LayerSlider') ?>">
                    <span class="dashicons dashicons-video-alt3"></span><?php ls_e('Video / Audio', 'LayerSlider') ?>
                </li>
                <li data-section="html" data-placeholder="<?php ls_e('Enter custom HTML code, which will appear on your front-office pages ...', 'LayerSlider') ?>"><span class="dashicons dashicons-editor-code "></span><?php ls_e('HTML', 'LayerSlider') ?></li>
                <li data-section="post" data-placeholder="<?php ls_e('You can enter both post placeholders and custom content here (including HTML) ...', 'LayerSlider') ?>"><span class="dashicons dashicons-admin-post"></span><?php ls_e('Dynamic content', 'LayerSlider') ?></li>
            </ul>
        </div>
        <!-- End of Layer Media Type -->

        <!-- Layer Element Type -->
        <input type="hidden" name="type" value="p">
        <ul class="ls-sublayer-element ls-hidden">
            <li class="ls-type active" data-element="p"><?php ls_e('Paragraph', 'LayerSlider') ?></li>
            <li class="ls-type" data-element="h1"><?php ls_e('H1', 'LayerSlider') ?></li>
            <li class="ls-type" data-element="h2"><?php ls_e('H2', 'LayerSlider') ?></li>
            <li class="ls-type" data-element="h3"><?php ls_e('H3', 'LayerSlider') ?></li>
            <li class="ls-type" data-element="h4"><?php ls_e('H4', 'LayerSlider') ?></li>
            <li class="ls-type" data-element="h5"><?php ls_e('H5', 'LayerSlider') ?></li>
            <li class="ls-type" data-element="h6"><?php ls_e('H6', 'LayerSlider') ?></li>
        </ul>
        <!-- End of Layer Element Type -->

        <div class="ls-layer-sections">

            <!-- Image Layer -->
            <div class="ls-image-uploader slide-image clearfix">
                <input type="hidden" name="imageId">
                <input type="hidden" name="image">
                <div class="ls-image ls-upload ls-bulk-upload ls-layer-image not-set" data-l10n-set="<?php ls_e('Click to set', 'LayerSlider') ?>" data-l10n-change="<?php ls_e('Click to change', 'LayerSlider') ?>">
                    <div><img src="<?php echo LS_VIEWS_URL.'img/admin/blank.gif' ?>" alt=""></div>
                    <a href="#" class="aviary dashicons dashicons-image-crop"></a>
                    <a href="#" class="dashicons dashicons-dismiss"></a>
                </div>
                <p>
                    <?php ls_e('Click on the image preview to open Image Manager or', 'LayerSlider') ?>
                    <a href="#" class="ls-url-prompt"><?php ls_e('insert from URL', 'LayerSlider') ?></a> or
                    <a href="#" class="ls-post-image"><?php ls_e('use product img', 'LayerSlider') ?></a>.
                </p>
            </div>

            <!-- Text/HTML/Video Layer -->
            <div class="ls-html-code ls-hidden">
                <div class="ls-html-textarea">
                    <textarea name="html" cols="50" rows="5" placeholder="Enter layer content here"></textarea>
                    <button type="button" class="button ls-insert-icon">
                        <span class="fa fa-star"></span>
                        <?php ls_e('Add Icon', 'LayerSlider') ?>
                    </button>
                    <button type="button" class="button ls-upload ls-bulk-upload ls-insert-media">
                        <span class="dashicons dashicons-admin-media"></span>
                        <?php ls_e('Add Media', 'LayerSlider') ?>
                    </button>
                </div>
                <div class="ls-options">

                    <div class="ls-image-uploader slide-image clearfix">
                        <table>
                            <tr>
                                <td>
                                    <input type="hidden" name="posterId">
                                    <input type="hidden" name="poster">
                                    <div class="ls-image ls-upload ls-bulk-upload ls-media-image not-set" data-l10n-set="<?php ls_e('Click to set', 'LayerSlider') ?>" data-l10n-change="<?php ls_e('Click to change', 'LayerSlider') ?>">
                                        <div><img src="<?php echo LS_VIEWS_URL.'img/admin/blank.gif' ?>" alt=""></div>
                                        <a href="#" class="aviary dashicons dashicons-image-crop"></a>
                                        <a href="#" class="dashicons dashicons-dismiss"></a>
                                    </div>
                                </td>
                                <td>
                                    <p>
                                        <?php ls_e('Insert a video poster image from your Image Manager or ', 'LayerSlider') ?>
                                        <a href="#" class="ls-url-prompt"><?php ls_e('insert from URL', 'LayerSlider') ?></a>.
                                    </p>
                                </td>
                                <td>
                                    <?php lsGetCheckbox($lsDefaults['layers']['mediaBackgroundVideo'], null, array('class' => 'sublayerprop hero bgvideo')) ?>
                                    <?php echo $lsDefaults['layers']['mediaBackgroundVideo']['name'] ?>
                                </td>
                            </tr>
                        </table>

                        <div class="ls-bgvideo-options ls-notification-info">
                            <i class="dashicons dashicons-info"></i>
                            <?php ls_e('Please note, the slide background image (if any) will cover the video.', 'LayerSlider') ?>
                        </div>
                    </div>

                    <div class="ls-separator"><span><?php ls_e('options', 'LayerSlider') ?></span></div>
                    <table class="ls-media-options">
                        <tr>
                            <td>
                                <?php echo $lsDefaults['layers']['mediaAutoPlay']['name'] ?> <br>
                                <?php lsGetSelect($lsDefaults['layers']['mediaAutoPlay'], null, array('class' => 'sublayerprop')) ?>
                            </td>
                            <td>
                                <?php echo $lsDefaults['layers']['mediaFillMode']['name'] ?> <br>
                                <?php lsGetSelect($lsDefaults['layers']['mediaFillMode'], null, array('class' => 'sublayerprop')) ?>
                            </td>
                            <td>
                                <?php echo $lsDefaults['layers']['mediaControls']['name'] ?> <br>
                                <?php lsGetSelect($lsDefaults['layers']['mediaControls'], null, array('class' => 'sublayerprop')) ?>
                            </td>
                            <td>
                                <?php echo $lsDefaults['layers']['mediaInfo']['name'] ?> <br>
                                <?php lsGetSelect($lsDefaults['layers']['mediaInfo'], null, array('class' => 'sublayerprop')) ?>
                            </td>
                            <td class="volume">
                                <?php echo $lsDefaults['layers']['mediaVolume']['name'] ?> <br>
                                <?php lsGetInput($lsDefaults['layers']['mediaVolume'], null, array('class' => 'sublayerprop')) ?>
                            </td>
                            <td class="overlay">
                                <?php echo $lsDefaults['layers']['mediaOverlay']['name']; ?><br>
                                <?php

                                $location = LS_VIEWS_URL.'img/layerslider/overlays/*';
                                $overlays = array('disabled' => 'No overlay image');

                                foreach (glob($location) as $file) {
                                    $basename = basename($file);
                                    $url = LS_VIEWS_URL.'img/layerslider/overlays/'.$basename;

                                    $overlays[$url] = $basename;
                                }

                                lsGetSelect($lsDefaults['layers']['mediaOverlay'], null, array('class' => 'sublayerprop', 'options' => $overlays ));
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Dynamic Layer -->
            <div class="ls-post-section ls-hidden">
                <div class="ls-posts-configured">
                    <ul class="ls-post-placeholders clearfix">
                        <li><span>[id]</span></li>
                        <li><span>[url]</span></li>
                        <li><span>[date-published]</span></li>
                        <li><span>[date-modified]</span></li>
                        <li><span>[image]</span></li>
                        <li><span>[image-url]</span></li>
                        <li><span>[name]</span></li>
                        <li><span>[price]</span></li>
                        <li><span>[old-price]</span></li>
                        <li><span>[description]</span></li>
                        <li><span>[description-short]</span></li>
                        <!--li data-placeholder="<a href=&quot;[url]&quot;>Read more</a>"><span>[link]</span></li-->
                        <li><span>[manufacturer]</span></li>
                        <li><span>[category]</span></li>
                        <li><span>[breadcrumbs]</span></li>
                    </ul>
                    <p>
                        <?php ls_e("Click on one or more post placeholders to insert them into your layer's content. Post placeholders act like shortcodes, and they will be filled with the actual content from your dynamic content.", "LayerSlider") ?><br>
                        <?php ls_e('Limit text length (if any)', 'LayerSlider') ?>
                        <input type="number" name="post_text_length">
                        <button type="button" class="button ls-configure-posts"><span class="dashicons dashicons-admin-post"></span><?php ls_e('Configure dynamic content', 'LayerSlider') ?></button>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="ls-sublayer-page ls-sublayer-options">
        <div class="ls-easy">
            <ul class="layer-properties-box clearfix">
                <li class="ls-easy-tr" data-prop="transitionin">
                    <h6><?php ls_e('Add opening transition') ?></h6>
                    <h5><span><?php ls_e('Opening transition') ?></span></h5>
                    <button type="button" class="button ls-blue-button ls-preset">
                        <i class="dashicons dashicons-admin-settings"></i>
                        <?php ls_e('Choose preset') ?>
                    </button>
                    <ul></ul>
                    <select class="ls-add-tr-prop"><option value="">&#10010;&nbsp;&nbsp;&nbsp;<?php ls_e('Add property') ?></option></select>
                </li>
                <li class="ls-easy-tr" data-prop="transitionout">
                    <h6><?php ls_e('Add ending transition') ?></h6>
                    <h5><span><?php ls_e('Ending transition') ?></span><i class="dashicons dashicons-no"></i></h5>
                    <button type="button" class="button ls-blue-button ls-preset">
                        <i class="dashicons dashicons-admin-settings"></i>
                        <?php ls_e('Choose preset') ?>
                    </button>
                    <ul></ul>
                    <select class="ls-add-tr-prop"><option value="">&#10010;&nbsp;&nbsp;&nbsp;<?php ls_e('Add property') ?></option></select>
                </li>
                <li class="ls-easy-tr" data-prop="loop">
                    <h6><?php ls_e('Add loop transition') ?></h6>
                    <h5><span><?php ls_e('Loop transition') ?></span><i class="dashicons dashicons-no"></i></h5>
                    <button type="button" class="button ls-blue-button ls-preset">
                        <i class="dashicons dashicons-admin-settings"></i>
                        <?php ls_e('Choose preset') ?>
                    </button>
                    <ul></ul>
                    <select class="ls-add-tr-prop"><option value="">&#10010;&nbsp;&nbsp;&nbsp;<?php ls_e('Add property') ?></option></select>
                </li>
                <li class="ls-easy-tr" data-prop="hover">
                    <h6><?php ls_e('Add hover transition') ?></h6>
                    <h5><span><?php ls_e('Hover transition') ?></span><i class="dashicons dashicons-no"></i></h5>
                    <button type="button" class="button ls-blue-button ls-preset">
                        <i class="dashicons dashicons-admin-settings"></i>
                        <?php ls_e('Choose preset') ?>
                    </button>
                    <ul></ul>
                    <select class="ls-add-tr-prop"><option value="">&#10010;&nbsp;&nbsp;&nbsp;<?php ls_e('Add property') ?></option></select>
                </li>
            </ul>
        </div>
        <div class="ls-adv">
            <select id="ls-transition-selector">
                <option value="0"><?php ls_e('Opening Transition properties', 'LayerSlider') ?></option>
                <option value="1"><?php ls_e('Opening Text Transition properties', 'LayerSlider') ?></option>
                <option value="2"><?php ls_e('Loop or Middle Transition properties', 'LayerSlider') ?></option>
                <option value="3"><?php ls_e('Ending Text Transition properties', 'LayerSlider') ?></option>
                <option value="4"><?php ls_e('Ending Transition properties', 'LayerSlider') ?></option>
                <option value="5"><?php ls_e('Hover Transition properties', 'LayerSlider') ?></option>
                <option value="6"><?php ls_e('Parallax Transition properties', 'LayerSlider') ?></option>
            </select>

            <table id="ls-transition-selector-table">
                <tr>
                    <td class="ls-padding"></td>
                    <td class="ls-opening-transition">
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-in">
                                <div class="ls-preview-layer"></div>
                            </div>
                            <span><?php ls_e('Opening<br>Transition', 'LayerSlider') ?></span>
                        </div>
                    </td>
                    <td class="ls-padding ls-only-with-text-layers"></td>
                    <td class="ls-opening-transition ls-only-with-text-layers">
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-textin">
                                <span class="ls-preview-layer_t ls-preview-layer_t4">t</span>
                                <span class="ls-preview-layer_t ls-preview-layer_t3">x</span>
                                <span class="ls-preview-layer_t ls-preview-layer_t2">e</span>
                                <span class="ls-preview-layer_t ls-preview-layer_t1">t</span>
                            </div>
                            <span><?php ls_e('Opening Text<br>Transition', 'LayerSlider') ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                    <td>
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-loop">
                                <div class="ls-preview-layer"></div>
                            </div>
                            <span><?php ls_e('Loop or Middle<br>Transition', 'LayerSlider') ?></span>
                        </div>
                    </td>
                    <td class="ls-padding ls-only-with-text-layers"></td>
                    <td class="ls-only-with-text-layers">
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-textout">
                                <span class="ls-preview-layer_t ls-preview-layer_t4">t</span>
                                <span class="ls-preview-layer_t ls-preview-layer_t3">x</span>
                                <span class="ls-preview-layer_t ls-preview-layer_t2">e</span>
                                <span class="ls-preview-layer_t ls-preview-layer_t1">t</span>
                            </div>
                            <span><?php ls_e('Ending Text<br>Transition', 'LayerSlider') ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                    <td>
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-out">
                                <div class="ls-preview-layer"></div>
                            </div>
                            <span><?php ls_e('Ending<br>Transition', 'LayerSlider') ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                    <td>
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-hover">
                                <div class="ls-preview-layer"></div>
                            </div>
                            <span><?php ls_e('Hover<br>Transition', 'LayerSlider') ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                    <td>
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-parallax">
                                <div class="ls-preview-layer"></div>
                                <div class="ls-preview-layer ls-preview-layer_b"></div>
                            </div>
                            <span><?php ls_e('Parallax<br>Transition', 'LayerSlider') ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                </tr>
            </table>

            <div id="ls-transition-warning">
                <div class="ls-notification-info">
                    <i class="dashicons dashicons-info"></i>
                    <?php ls_e('Layers require an opening transition in order to become visible during the slideshow. Enable either <mark>Opening Transition</mark> or <mark>Opening Text Transition</mark> to make this layer visible again.', 'LayerSlider') ?>
                </div>
            </div>

            <div id="ls-layer-transitions">

                <!-- Opening Transition -->
                <section data-storage="ls-tr-in">
                    <div>
                        <div class="ls-separator"><span><?php ls_e('Opening Transition properties', 'LayerSlider') ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED', 'LayerSlider') ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['transitionIn'], null, array('class' => 'sublayerprop large toggle')) ?></div>
                            <div class="ls-h-description"><?php ls_e('The following are the initial options from which this layer animates toward the appropriate values set under the Styles tab when it enters into the slider canvas.', 'LayerSlider') ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties', 'LayerSlider') ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties', 'LayerSlider') ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Position &amp; Dimensions', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInOffsetX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInOffsetX'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInOffsetY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInOffsetY'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInScaleX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInScaleX'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInScaleY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInScaleY'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInWidth']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInWidth'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInHeight']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInHeight'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation, Skew &amp; Mask', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInRotate']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInRotate'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInRotateX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInRotateX'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInRotateY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInRotateY'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInSkewX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInSkewX'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInSkewY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInSkewY'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInClip']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInClip'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInDelay']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInDelay'], null, array('class' => 'sublayerprop')) ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInDuration']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInDuration'], null, array('class' => 'sublayerprop')) ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['transitionInEasing']['name'] ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['transitionInEasing'], null, array('class' => 'sublayerprop', 'options' => $lsDefaults['easings'])) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInTransformOrigin']['name'] ?>
                                        </div>
                                        <div>
                                            <i class="dashicons dashicons-search"></i><?php lsGetInput($lsDefaults['layers']['transitionInTransformOrigin'], null, array('class' => 'sublayerprop', 'style' => 'width:130px')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInPerspective']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInPerspective'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <!-- <li>
                                        <div>
                                            Perspective
                                        </div>
                                        <div>
                                            <?php //lsGetInput($lsDefaults['layers']['transitionInTransformPerspective'], null, array('class' => 'sublayerprop', 'style' => 'width:130px')) ?>
                                        </div>
                                    </li> -->
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInFade']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetCheckbox($lsDefaults['layers']['transitionInFade'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInColor']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInColor'], null, array('class' => 'sublayerprop ls-colorpicker')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInBGColor']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInBGColor'], null, array('class' => 'sublayerprop ls-colorpicker')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInRadius']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInRadius'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                     <li>
                                        <div>
                                            <a href="https://developer.mozilla.org/en/docs/Web/CSS/filter#Functions" target="_blank"><?php echo $lsDefaults['layers']['transitionInFilter']['name'] ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInFilter'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                 </ul>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Opening Text Transition -->
                <section class="ls-text-transition" data-storage="ls-tr-text-in">
                    <div>
                        <div class="ls-separator"><span><?php ls_e('Opening Text Transition properties', 'LayerSlider') ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED', 'LayerSlider') ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['textTransitionIn'], null, array('class' => 'sublayerprop large toggle')) ?></div>
                            <div class="ls-h-description"><?php ls_e('The following options specify the initial state of each text fragments before they start animating toward the joint whole word.', 'LayerSlider') ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties', 'LayerSlider') ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties', 'LayerSlider') ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Type, Position &amp; Dimensions', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textTypeIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textTypeIn'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textOffsetXIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textOffsetXIn'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textOffsetYIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textOffsetYIn'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textScaleXIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textScaleXIn'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textScaleYIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textScaleYIn'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation &amp; Skew', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateIn'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateXIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateXIn'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateYIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateYIn'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textSkewXIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textSkewXIn'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textSkewYIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textSkewYIn'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Sets the starting time for this transition. Select one of the pre-defined options from this list to control timing in relation with other transition types. Additionally, you can shift starting time with the modifier controls below.', 'LayerSlider') ?>">
                                        <div><?php ls_e('Start when', 'LayerSlider') ?></div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textStartAtIn'], null, array('class' => 'sublayerprop start-at-calc undomanager-merge')) ?>
                                            <?php lsGetSelect($lsDefaults['layers']['textStartAtInTiming'], null, array('class' => 'sublayerprop start-at-timing')) ?>
                                        </div>
                                    </li>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Shifts the above selected starting time by performing a custom operation. For example, &quot;- 1000&quot; will advance the animation by playing it 1 second (1000 milliseconds) earlier.', 'LayerSlider') ?>">
                                        <div><?php ls_e('with modifier', 'LayerSlider') ?></div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textStartAtInOperator'], null, array('class' => 'sublayerprop start-at-operator')) ?>
                                            <?php lsGetInput($lsDefaults['layers']['textStartAtInValue'], null, array('class' => 'sublayerprop start-at-value')) ?>  ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textDurationIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textDurationIn'], null, array('class' => 'sublayerprop')) ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textShiftIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textShiftIn'], null, array('class' => 'sublayerprop')) ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['textEasingIn']['name'] ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textEasingIn'], null, array('class' => 'sublayerprop', 'options' => $lsDefaults['easings'])) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textTransformOriginIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textTransformOriginIn'], null, array('class' => 'sublayerprop', 'style' => 'width:130px')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textPerspectiveIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textPerspectiveIn'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textFadeIn']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetCheckbox($lsDefaults['layers']['textFadeIn'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Loop or Middle Transition -->
                <section data-storage="ls-tr-loop">
                    <div>
                        <div class="ls-separator"><span><?php ls_e('Loop / Middle Transition properties', 'LayerSlider') ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED', 'LayerSlider') ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['loop'], null, array('class' => 'sublayerprop large toggle')) ?></div>
                            <div class="ls-h-description"><?php ls_e('Repeats a transition based on the options below. If you set the Loop Count to 1, it can also act as a middle transition in the chain of animation lifecycles.', 'LayerSlider') ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties', 'LayerSlider') ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties', 'LayerSlider') ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Position &amp; Dimensions', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopOffsetX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopOffsetX'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopOffsetY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopOffsetY'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopScaleX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopScaleX'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopScaleY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopScaleY'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation, Skew &amp; Mask', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopRotate']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopRotate'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopRotateX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopRotateX'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopRotateY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopRotateY'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopSkewX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopSkewX'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopSkewY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopSkewY'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopClip']['name'] ?><br>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopClip'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Sets the starting time for this transition. Select one of the pre-defined options from this list to control timing in relation with other transition types. Additionally, you can shift starting time with the modifier controls below.', 'LayerSlider'); ?>">
                                        <div><?php ls_e('Start when', 'LayerSlider') ?></div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopStartAt'], null, array('class' => 'sublayerprop start-at-calc undomanager-merge')) ?>
                                            <?php lsGetSelect($lsDefaults['layers']['loopStartAtTiming'], null, array('class' => 'sublayerprop start-at-timing')) ?>
                                        </div>
                                    </li>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Shifts the above selected starting time by performing a custom operation. For example, &quot;- 1000&quot; will advance the animation by playing it 1 second (1000 milliseconds) earlier.', 'LayerSlider'); ?>">
                                        <div><?php ls_e('with modifier', 'LayerSlider') ?></div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['loopStartAtOperator'], null, array('class' => 'sublayerprop start-at-operator')) ?>
                                            <?php lsGetInput($lsDefaults['layers']['loopStartAtValue'], null, array('class' => 'sublayerprop start-at-value')) ?>  ms
                                        </div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['loopDuration']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['loopDuration'], null, array('class' => 'sublayerprop')) ?> ms</div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['loopEasing']['name'] ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['loopEasing'], null, array('class' => 'sublayerprop', 'options' => $lsDefaults['easings'])) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopCount']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopCount'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopWait']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopWait'], null, array('class' => 'sublayerprop')) ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopYoyo']['name'] ?>
                                        </div>
                                        <div>
                                            <label style="display:inline" data-help="<?php echo $lsDefaults['layers']['loopYoyo']['tooltip'] ?>"><?php lsGetCheckbox($lsDefaults['layers']['loopYoyo'], null, array('class' => 'sublayerprop')) ?></label>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopTransformOrigin']['name'] ?>
                                        </div>
                                        <div>
                                            <i class="dashicons dashicons-search"></i><?php lsGetInput($lsDefaults['layers']['loopTransformOrigin'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopPerspective']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopPerspective'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>


                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopOpacity']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopOpacity'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                     <li>
                                        <div>
                                            <a href="https://developer.mozilla.org/en/docs/Web/CSS/filter#Functions" target="_blank"><?php echo $lsDefaults['layers']['loopFilter']['name'] ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopFilter'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Ending Text Transition -->
                <section class="ls-text-transition" data-storage="ls-tr-text-out">
                    <div>
                        <div class="ls-separator"><span><?php ls_e('Ending Text Transition properties', 'LayerSlider') ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED', 'LayerSlider') ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['textTransitionOut'], null, array('class' => 'sublayerprop large toggle')) ?></div>
                            <div class="ls-h-description"><?php ls_e('Each text fragment will animate from the joint whole word to the options you specify here.', 'LayerSlider') ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties', 'LayerSlider') ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties', 'LayerSlider') ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Type, Position &amp; Dimensions', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textTypeOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textTypeOut'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textOffsetXOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textOffsetXOut'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textOffsetYOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textOffsetYOut'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textScaleXOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textScaleXOut'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textScaleYOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textScaleYOut'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation &amp; Skew', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateOut'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateXOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateXOut'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateYOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateYOut'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textSkewXOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textSkewXOut'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textSkewYOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textSkewYOut'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Sets the starting time for this transition. Select one of the pre-defined options from this list to control timing in relation with other transition types. Additionally, you can shift starting time with the modifier controls below.', 'LayerSlider'); ?>">
                                        <div><?php ls_e('Start when', 'LayerSlider') ?></div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textStartAtOut'], null, array('class' => 'sublayerprop start-at-calc undomanager-merge')) ?>
                                            <?php lsGetSelect($lsDefaults['layers']['textStartAtOutTiming'], null, array('class' => 'sublayerprop start-at-timing')) ?>
                                        </div>
                                    </li>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Shifts the above selected starting time by performing a custom operation. For example, &quot;- 1000&quot; will advance the animation by playing it 1 second (1000 milliseconds) earlier.', 'LayerSlider'); ?>">
                                        <div><?php ls_e('with modifier', 'LayerSlider') ?></div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textStartAtOutOperator'], null, array('class' => 'sublayerprop start-at-operator')) ?>
                                            <?php lsGetInput($lsDefaults['layers']['textStartAtOutValue'], null, array('class' => 'sublayerprop start-at-value')) ?>  ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textDurationOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textDurationOut'], null, array('class' => 'sublayerprop')) ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textShiftOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textShiftOut'], null, array('class' => 'sublayerprop')) ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['textEasingOut']['name'] ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textEasingOut'], null, array('class' => 'sublayerprop', 'options' => $lsDefaults['easings'])) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textTransformOriginOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textTransformOriginOut'], null, array('class' => 'sublayerprop', 'style' => 'width:130px')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textPerspectiveOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textPerspectiveOut'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textFadeOut']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetCheckbox($lsDefaults['layers']['textFadeOut'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Ending Transition -->
                <section data-storage="ls-tr-out">
                    <div>
                        <div class="ls-separator"><span><?php ls_e('Ending Transition properties', 'LayerSlider') ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED', 'LayerSlider') ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['transitionOut'], null, array('class' => 'sublayerprop large toggle')) ?></div>
                            <div class="ls-h-description"><?php ls_e('The following options will be the end values where this layer animates toward when it leaves the slider canvas.', 'LayerSlider') ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties', 'LayerSlider') ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties', 'LayerSlider') ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Position &amp; Dimensions', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutOffsetX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutOffsetX'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutOffsetY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutOffsetY'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutScaleX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutScaleX'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutScaleY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutScaleY'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutWidth']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutWidth'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutHeight']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutHeight'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation, Skew &amp; Mask', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutRotate']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutRotate'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutRotateX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutRotateX'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutRotateY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutRotateY'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutSkewX']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutSkewX'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutSkewY']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutSkewY'], null, array('class' => 'sublayerprop')) ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutClip']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutClip'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Sets the starting time for this transition. Select one of the pre-defined options from this list to control timing in relation with other transition types. Additionally, you can shift starting time with the modifier controls below.', 'LayerSlider'); ?>">
                                        <div><?php ls_e('Start when', 'LayerSlider') ?></div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutStartAt'], null, array('class' => 'sublayerprop start-at-calc undomanager-merge')) ?>
                                            <?php lsGetSelect($lsDefaults['layers']['transitionOutStartAtTiming'], null, array('class' => 'sublayerprop start-at-timing')) ?>
                                        </div>
                                    </li>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Shifts the above selected starting time by performing a custom operation. For example, &quot;- 1000&quot; will advance the animation by playing it 1 second (1000 milliseconds) earlier.', 'LayerSlider'); ?>">
                                        <div><?php ls_e('with modifier', 'LayerSlider') ?></div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['transitionOutStartAtOperator'], null, array('class' => 'sublayerprop start-at-operator')) ?>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutStartAtValue'], null, array('class' => 'sublayerprop start-at-value')) ?>  ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutDuration']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutDuration'], null, array('class' => 'sublayerprop')) ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['transitionOutEasing']['name'] ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['transitionOutEasing'], null, array('class' => 'sublayerprop', 'options' => $lsDefaults['easings'])) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutTransformOrigin']['name'] ?>
                                        </div>
                                        <div>
                                            <i class="dashicons dashicons-search"></i><?php lsGetInput($lsDefaults['layers']['transitionOutTransformOrigin'], null, array('class' => 'sublayerprop', 'style' => 'width:130px')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutPerspective']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutPerspective'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <!-- <li>
                                        <div>
                                            Perspective
                                        </div>
                                        <div>
                                            <?php //lsGetInput($lsDefaults['layers']['transitionOutTransformPerspective'], null, array('class' => 'sublayerprop', 'style' => 'width:130px')) ?>
                                        </div>
                                    </li> -->
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutFade']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetCheckbox($lsDefaults['layers']['transitionOutFade'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutColor']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutColor'], null, array('class' => 'sublayerprop ls-colorpicker')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutBGColor']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutBGColor'], null, array('class' => 'sublayerprop ls-colorpicker')) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutRadius']['name'] ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutRadius'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                     <li>
                                        <div>
                                            <a href="https://developer.mozilla.org/en/docs/Web/CSS/filter#Functions" target="_blank"><?php echo $lsDefaults['layers']['transitionOutFilter']['name'] ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutFilter'], null, array('class' => 'sublayerprop')) ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </section>


                <!-- Hover Transition -->
                <section data-storage="ls-tr-hover">
                    <div>
                        <div class="ls-separator"><span><?php ls_e('Hover Transition properties', 'LayerSlider') ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED', 'LayerSlider') ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['hover'], null, array('class' => 'sublayerprop large toggle')) ?></div>
                            <div class="ls-h-description"><?php ls_e('Plays a transition based on the options below when the user moves the mouse cursor over this layer.', 'LayerSlider') ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties', 'LayerSlider') ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties', 'LayerSlider') ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Position &amp; Dimensions', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverOffsetX']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverOffsetX'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverOffsetY']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverOffsetY'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverScaleX']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverScaleX'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverScaleY']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverScaleY'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation, Skew &amp; Mask', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverRotate']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverRotate'], null, array('class' => 'sublayerprop')) ?> &deg;</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverRotateX']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverRotateX'], null, array('class' => 'sublayerprop')) ?> &deg;</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverRotateY']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverRotateY'], null, array('class' => 'sublayerprop')) ?> &deg;</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverSkewX']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverSkewX'], null, array('class' => 'sublayerprop')) ?> &deg;</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverSkewY']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverSkewY'], null, array('class' => 'sublayerprop')) ?> &deg;</div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverInDuration']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverInDuration'], null, array('class' => 'sublayerprop')) ?> ms</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverOutDuration']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverOutDuration'], null, array('class' => 'sublayerprop')) ?> ms</div>
                                    </li>
                                    <li>
                                        <div><a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['hoverInEasing']['name'] ?></a></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['hoverInEasing'], null, array('class' => 'sublayerprop', 'options' => $lsDefaults['easings'])) ?></div>
                                    </li>
                                    <li>
                                        <div><a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['hoverOutEasing']['name'] ?></a></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['hoverOutEasing'], null, array('class' => 'sublayerprop', 'options' => array_merge(array('' => '- Same easing -'), $lsDefaults['easings']))) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverTransformOrigin']['name'] ?></div>
                                        <div><i class="dashicons dashicons-search"></i><?php lsGetInput($lsDefaults['layers']['hoverTransformOrigin'], null, array('class' => 'sublayerprop', 'style' => 'width:130px')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverTransformPerspective']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverTransformPerspective'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverOpacity']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverOpacity'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverColor']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverColor'], null, array('class' => 'sublayerprop ls-colorpicker')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverBGColor']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverBGColor'], null, array('class' => 'sublayerprop ls-colorpicker')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverBorderRadius']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverBorderRadius'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverTopOn']['name'] ?></div>
                                        <div><?php lsGetCheckbox($lsDefaults['layers']['hoverTopOn'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </section>




                <!-- Parallax Transition -->
                <section data-storage="ls-tr-parallax">
                    <div>
                        <div class="ls-separator"><span><?php ls_e('Parallax Transition properties', 'LayerSlider') ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED', 'LayerSlider') ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['parallax'], null, array('class' => 'sublayerprop large toggle')) ?></div>
                            <div class="ls-h-description"><?php ls_e('Select a parallax type and event, then set the Parallax Level option to enable parallax layers.', 'LayerSlider') ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties', 'LayerSlider') ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties', 'LayerSlider') ?></a>
                            </div>
                        </header>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix col-3">
                            <li>
                                <h5><span><?php ls_e('Basic Settings', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxLevel']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxLevel'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxType']['name'] ?></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['parallaxType'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxEvent']['name'] ?></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['parallaxEvent'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxAxis']['name'] ?></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['parallaxAxis'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Distance &amp; Rotation', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxDistance']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxDistance'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxRotate']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxRotate'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform', 'LayerSlider') ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxDurationMove']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxDurationMove'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxDurationLeave']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxDurationLeave'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxTransformOrigin']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxTransformOrigin'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxPerspective']['name'] ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxPerspective'], null, array('class' => 'sublayerprop')) ?></div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </section>
            </div>

            <div class="ls-separator"><span><?php ls_e('Other settings', 'LayerSlider') ?></span></div>

            <div class="ls-layer-other-settings clearfix">
                <div>
                    <div>
                        <?php echo $lsDefaults['layers']['transitionStatic']['name'] ?>
                    </div>
                    <div>
                        <?php lsGetSelect($lsDefaults['layers']['transitionStatic'], null, array('class' => 'sublayerprop')) ?>
                    </div>
                </div>


                <div class="clearfix">
                     <div>
                        <?php echo $lsDefaults['layers']['transitionKeyframe']['name'] ?>
                    </div>
                    <div>
                        <?php lsGetCheckbox($lsDefaults['layers']['transitionKeyframe'], null, array('class' => 'sublayerprop')) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="ls-sublayer-page ls-sublayer-link">
        <h3 class="subheader"><?php ls_e('Linking', 'LayerSlider') ?></h3>
        <div class="ls-slide-link clearfix">
            <div>
                <?php lsGetInput($lsDefaults['layers']['linkURL'], null, array('class' => 'url', 'placeholder' => $lsDefaults['layers']['linkURL']['name'])) ?>
                <input type="hidden" name="linkId">
                <input type="hidden" name="linkName">
                <input type="hidden" name="linkType">
                <a href="#" class="change">
                    <span class="dashicons dashicons-editor-unlink"></span>
                    <?php ls_e('Change Link', 'LayerSlider') ?>
                </a>
                <span><a href="#" class="dyn"><?php ls_e('Use Dynamic post URL', 'LayerSlider') ?></a></span>
            </div>
            <?php lsGetSelect($lsDefaults['layers']['linkTarget'], null) ?>
        </div>

        <h3 class="subheader"><?php ls_e('Common Attributes', 'LayerSlider') ?></h3>
        <div class="ls-sublayer-attributes">
            <table class="ls-sublayer-common-attributes">
                <tbody>
                    <tr data-help="<?php echo $lsDefaults['layers']['ID']['tooltip'] ?>">
                        <td class="first"><input type="text" value="id" disabled></td>
                        <td class="second"><input type="text" name="id"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it.", 'LayerSlider') ?>">
                            <?php ls_e('On layer', 'LayerSlider') ?>
                        </td>
                    </tr>
                    <tr data-help="<?php echo $lsDefaults['layers']['class']['tooltip'] ?>">
                        <td class="first"><input type="text" value="class" disabled></td>
                        <td class="second"><input type="text" name="class"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it.", 'LayerSlider') ?>">
                            <?php ls_e('On layer', 'LayerSlider') ?>
                        </td>
                    </tr>
                    <tr data-help="<?php echo $lsDefaults['layers']['title']['tooltip'] ?>">
                        <td class="first"><input type="text" value="title" disabled></td>
                        <td class="second"><input type="text" name="title"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it.", 'LayerSlider') ?>">
                            <?php ls_e('On parent', 'LayerSlider') ?>
                        </td>
                    </tr>
                    <tr data-help="<?php echo $lsDefaults['layers']['alt']['tooltip'] ?>">
                        <td class="first"><input type="text" value="alt" disabled></td>
                        <td class="second"><input type="text" name="alt"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it.", 'LayerSlider') ?>">
                            <?php ls_e('On layer', 'LayerSlider') ?>
                        </td>
                    </tr>
                    <tr class="ls-adv" data-help="<?php echo $lsDefaults['layers']['rel']['tooltip'] ?>">
                        <td class="first"><input type="text" value="rel" disabled></td>
                        <td class="second"><input type="text" name="rel"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it.", 'LayerSlider') ?>">
                            <?php ls_e('On parent', 'LayerSlider') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3 class="subheader ls-adv"><?php ls_e('Custom Attributes', 'LayerSlider') ?></h3>
        <div class="ls-sublayer-attributes ls-adv">
            <table class="ls-sublayer-custom-attributes">
                <tbody>
                    <tr>
                        <td colspan="3">
                            <p><?php echo $lsDefaults['layers']['innerAttributes']['desc']; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="first">
                            <input type="text" placeholder="<?php ls_e('Attribute name', 'LayerSlider') ?>">
                        </td>
                        <td class="second">
                            <input type="text" placeholder="<?php ls_e('Attribute value', 'LayerSlider') ?>">
                        </td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. By default, new attributes will be applied on the wrapper (if any), which is desirable in most cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. Uncheck this option when you need to apply this attribute on the layer element in all cases.", 'LayerSlider') ?>">
                            <input type="checkbox" class="small noreset" checked> <?php ls_e('On parent', 'LayerSlider') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ls-sublayer-page ls-sublayer-style clearfix" data-storage="ls-styles">

        <div>

            <div>
                <div>
                    <h5><?php ls_e('Layout', 'LayerSlider') ?> <span>| <?php ls_e('sizing & position', 'LayerSlider') ?></span></h5>
                    <div class="ls-layer-visual-box">
                        <div class="ls-layer-position">
                            <div>
                                <?php lsGetInput($lsDefaults['layers']['top'], null, array('class' => 'auto ls-layer-top')) ?>
                                <?php lsGetInput($lsDefaults['layers']['left'], null, array('class' => 'auto ls-layer-left')) ?>
                                <span class="ls-layer-top"><?php echo $lsDefaults['layers']['top']['name'] ?></span>
                                <span class="ls-layer-left"><?php echo $lsDefaults['layers']['left']['name'] ?></span>
                            </div>
                            <div class="ls-layer-border">
                                <?php ls_e('border', 'LayerSlider') ?>
                                <b class="ls-border-top-value">–</b>
                                <b class="ls-border-right-value">–</b>
                                <b class="ls-border-bottom-value">–</b>
                                <b class="ls-border-left-value">–</b>
                                <div class="ls-layer-padding">
                                    <?php ls_e('padding', 'LayerSlider') ?>
                                    <b class="ls-padding-top-value">–</b>
                                    <b class="ls-padding-right-value">–</b>
                                    <b class="ls-padding-bottom-value">–</b>
                                    <b class="ls-padding-left-value">–</b>
                                    <div class="ls-layer-size">
                                        <?php lsGetInput($lsDefaults['layers']['width'], null, array('class' => 'auto', 'placeholder' => 'auto')) ?><span class="ls-x">x</span><?php lsGetInput($lsDefaults['layers']['height'], null, array('class' => 'auto', 'placeholder' => 'auto')) ?>
                                        <br>
                                        <span class="ls-wh"><?php echo $lsDefaults['layers']['width']['name'] ?></span><span class="ls-wh"><?php echo $lsDefaults['layers']['height']['name'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-holder ls-position ls-adv">
                        <table>
                            <tbody>
                                <tr>
                                    <td>
                                        <?php echo $lsDefaults['layers']['position']['name'] ?>
                                    </td>
                                    <td>
                                        <?php lsGetSelect($lsDefaults['layers']['position'], null, array('class' => 'sublayerprop')) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $lsDefaults['layers']['zIndex']['name'] ?>
                                    </td>
                                    <td>
                                        <?php lsGetInput($lsDefaults['layers']['zIndex'], null, array('class' => 'auto')) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-holder ls-border-padding">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="ls-bptable-1"></td>
                                    <td class="ls-bptable-2"><?php ls_e('Border', 'LayerSlider') ?></td>
                                    <td class="ls-bptable-3"><?php ls_e('Padding', 'LayerSlider') ?></td>
                                </tr>
                                <tr data-edge="top">
                                    <td><?php ls_e('Top', 'LayerSlider') ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderTop'], null, array('class' => 'auto')) ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['paddingTop'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr data-edge="right">
                                    <td><?php ls_e('Right', 'LayerSlider') ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderRight'], null, array('class' => 'auto')) ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['paddingRight'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr data-edge="bottom">
                                    <td><?php ls_e('Bottom', 'LayerSlider') ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderBottom'], null, array('class' => 'auto')) ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['paddingBottom'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr data-edge="left">
                                    <td><?php ls_e('Left', 'LayerSlider') ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderLeft'], null, array('class' => 'auto')) ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['paddingLeft'], null, array('class' => 'auto')) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div>

            <div class="ls-h-actions">
                <div>
                    <h5><?php ls_e('Actions', 'LayerSlider') ?></h5>
                    <div class="table-holder">
                        <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy layer styles', 'LayerSlider') ?></a>
                        <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste layer styles', 'LayerSlider') ?></a>
                    </div>
                </div>
            </div>

            <div>
                <div>
                    <h5><?php ls_e('Text', 'LayerSlider') ?> <span>| <?php ls_e('font &amp; style', 'LayerSlider') ?></span></h5>
                    <div class="table-holder">
                        <table>
                            <tbody>

                                <?php
                                $fontList = array(
                                    array('name' => 'Arial', 'font' => true),
                                    array('name' => 'Helvetica', 'font' => true),
                                    array('name' => 'Georgia', 'font' => true),
                                    array('name' => 'Comic Sans MS', 'value' => "'Comic Sans MS'", 'font' => true),
                                    array('name' => 'Impact', 'font' => true),
                                    array('name' => 'Tahoma', 'font' => true),
                                    array('name' => 'Verdana', 'font' => true),
                                );

                                foreach ($googleFonts as $font) : ?>
                                    <?php list($family) = explode(':', $font['param']); ?>
                                    <?php $item = array('font' => true); ?>
                                    <?php
                                    if (strpos($family, '+')) : ?>
                                        <?php $family = str_replace('+', ' ', $family); ?>
                                        <?php $item['value'] = "'{$family}'"; ?>
                                    <?php
                                    endif; ?>

                                    <?php $item['name'] = $family; ?>
                                    <?php $fontList[] = $item; ?>
                                <?php
                                endforeach; ?>
                                <tr>
                                    <td class="right"><?php echo $lsDefaults['layers']['fontFamily']['name'] ?></td>
                                    <td>
                                            <?php lsGetInput($lsDefaults['layers']['fontFamily'], null, array(
                                                'class' => 'auto',
                                                'data-options' => Tools::jsonEncode($fontList)
                                            )) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['fontSize']['name'] ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['fontSize'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['lineHeight']['name'] ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['lineHeight'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['textAlign']['name'] ?></td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['textAlign'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['fontWeight']['name'] ?></td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['fontWeight'], null, array('class' => 'auto'), true) ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['fontStyle']['name'] ?></td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['fontStyle'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['textDecoration']['name'] ?></td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['textDecoration'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['letterSpacing']['name'] ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['letterSpacing'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['color']['name'] ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['color'], null, array('class' => 'auto ls-colorpicker')) ?></td>
                                </tr>
                                <tr class="ls-adv">
                                    <td><?php echo $lsDefaults['layers']['minFontSize']['name'] ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['minFontSize'], null, array('class' => 'sublayerprop')) ?></td>
                                </tr>
                                <tr class="ls-adv">
                                    <td><?php echo $lsDefaults['layers']['minMobileFontSize']['name'] ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['minMobileFontSize'], null, array('class' => 'sublayerprop')) ?></td>
                                </tr>
                                <tr>
                                    <td><?php ls_e('Word-wrap', 'LayerSlider') ?></td>
                                    <td><?php lsGetCheckbox($lsDefaults['layers']['wordWrap'], null, array('class' => 'auto')) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div>
                <div>
                    <h5><?php ls_e('Misc', 'LayerSlider') ?> <span>| <?php ls_e('other settings', 'LayerSlider') ?></span></h5>
                    <div class="table-holder">
                        <table>
                            <tbody>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['background']['name'] ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['background'], null, array('class' => 'auto ls-colorpicker')) ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['opacity']['name'] ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['opacity'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['borderRadius']['name'] ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderRadius'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr class="ls-adv">
                                    <td>
                                        <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/blend-mode" target="_blank">
                                            <?php echo $lsDefaults['layers']['blendMode']['name'] ?>
                                        </a>
                                    </td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['blendMode'], null, array('class' => 'auto')) ?></td>
                                </tr>
                                <tr class="ls-adv">
                                    <td>
                                        <a href="https://developer.mozilla.org/en/docs/Web/CSS/filter#Functions" target="_blank">
                                            <?php echo $lsDefaults['layers']['filter']['name'] ?>
                                        </a>
                                    </td>
                                    <td><?php lsGetInput($lsDefaults['layers']['filter'], null, array('class' => 'auto')) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="ls-adv">
                <div>
                    <h5><?php ls_e('Transforms', 'LayerSlider') ?> <span>| <?php ls_e('between transitions', 'LayerSlider') ?></span></h5>
                    <div class="textarea-helper">
                        <table>
                            <tr>
                                <td class="right"><?php echo $lsDefaults['layers']['rotate']['name'] ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['rotate'], null, array('class' => 'sublayerprop transforms')) ?> &deg;</td>
                                <td class="right"><?php echo $lsDefaults['layers']['scaleX']['name'] ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['scaleX'], null, array('class' => 'sublayerprop transforms')) ?></td>
                            </tr>
                            <tr>
                                <td class="right"><?php echo $lsDefaults['layers']['rotateX']['name'] ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['rotateX'], null, array('class' => 'sublayerprop transforms')) ?> &deg;</td>
                                <td class="right"><?php echo $lsDefaults['layers']['scaleY']['name'] ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['scaleY'], null, array('class' => 'sublayerprop transforms')) ?></td>
                            </tr>
                            <tr>
                                <td class="right"><?php echo $lsDefaults['layers']['rotateY']['name'] ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['rotateY'], null, array('class' => 'sublayerprop transforms')) ?> &deg;</td>
                                <td class="right"><?php echo $lsDefaults['layers']['skewX']['name'] ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['skewX'], null, array('class' => 'sublayerprop transforms')) ?> &deg;</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="right"><?php echo $lsDefaults['layers']['skewY']['name'] ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['skewY'], null, array('class' => 'sublayerprop transforms')) ?> &deg;</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="ls-adv">
                <h5><?php ls_e('Custom CSS', 'LayerSlider') ?> <span>| <?php ls_e('write your own code', 'LayerSlider') ?></span></h5>
                <div class="textarea-helper">
                    <textarea rows="5" cols="50" name="style" class="style" data-help="<?php ls_e('If you want to set style settings other then above, you can use here any CSS codes. Please make sure to write valid markup.', 'LayerSlider') ?>"></textarea>
                </div>
            </div>
        </div>

    </div>
</script>
