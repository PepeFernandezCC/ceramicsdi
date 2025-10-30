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

$lsDefaults = &$lsDefaults;
$googleFonts = &$googleFonts;
?>
<script type="text/html" id="ls-layer-template">
    <div class="ls-sublayer-page ls-sublayer-basic active">


        <div class="ls-set-screen-types">
            <?php ls_e('Show this layer on the following devices:'); ?>

                <button data-type="desktop" class="button dashicons dashicons-desktop playing" data-help="Show this layer on desktop."></button><!--
            --><button data-type="tablet" class="button dashicons dashicons-tablet" data-help="Show this layer on tablets."></button><!--
            --><button data-type="phone" class="button dashicons dashicons-smartphone" data-help="Show this layer on mobile phones."></button>

        </div>


        <input type="hidden" name="media" value="img">
        <div class="ls-layer-kind">
            <ul>
                <li data-section="img" class="active"><span class="dashicons dashicons-format-image"></span><?php ls_e('Image'); ?></li>
                <li data-section="text" data-placeholder="<?php ls_e('Enter text only content here ...'); ?>"><span class="dashicons dashicons-text"></span><?php ls_e('Text'); ?></li>
                <li data-section="button" data-placeholder="<?php ls_e('Enter the label of your button'); ?>"><span class="dashicons dashicons-marker"></span><?php ls_e('Button'); ?></li>
                <li data-section="media" data-placeholder="<?php ls_e('Paste embed code here   or   add self-hosted media ...'); ?>">
                    <span class="dashicons dashicons-video-alt3"></span><?php ls_e('Video / Audio'); ?>
                </li>
                <li data-section="html" data-placeholder="<?php ls_e('Enter custom HTML code, which will appear on your front-office pages ...'); ?>"><span class="dashicons dashicons-editor-code "></span><?php ls_e('HTML'); ?></li>
                <li data-section="post" data-placeholder="<?php ls_e('You can enter both post placeholders and custom content here (including HTML) ...'); ?>"><span class="dashicons dashicons-admin-post"></span><?php ls_e('Dynamic content'); ?></li>
            </ul>
        </div>
        <!-- End of Layer Media Type -->

        <!-- Layer Element Type -->
        <input type="hidden" name="type" value="p">
        <ul class="ls-sublayer-element ls-hidden">
            <li class="ls-type active" data-element="p"><?php ls_e('Paragraph'); ?></li>
            <li class="ls-type" data-element="h1"><?php ls_e('H1'); ?></li>
            <li class="ls-type" data-element="h2"><?php ls_e('H2'); ?></li>
            <li class="ls-type" data-element="h3"><?php ls_e('H3'); ?></li>
            <li class="ls-type" data-element="h4"><?php ls_e('H4'); ?></li>
            <li class="ls-type" data-element="h5"><?php ls_e('H5'); ?></li>
            <li class="ls-type" data-element="h6"><?php ls_e('H6'); ?></li>
        </ul>
        <!-- End of Layer Element Type -->

        <div class="ls-layer-sections">

            <!-- Image Layer -->
            <div class="ls-image-uploader slide-image clearfix">
                <input type="hidden" name="imageId">
                <input type="hidden" name="image">
                <div class="ls-image ls-upload ls-bulk-upload ls-layer-image not-set" data-l10n-set="<?php ls_e('Click to set'); ?>" data-l10n-change="<?php ls_e('Click to change'); ?>">
                    <div><img src="<?php echo LS_VIEWS_URL . 'img/admin/blank.gif'; ?>" alt=""></div>
                    <a href="#" class="dashicons dashicons-dismiss"></a>
                </div>
                <p>
                    <?php ls_e('Click on the image preview to open Image Manager or'); ?>
                    <a href="#" class="ls-url-prompt"><?php ls_e('insert from URL'); ?></a> or
                    <a href="#" class="ls-post-image"><?php ls_e('use product img'); ?></a>.
                </p>
            </div>

            <!-- Text/HTML/Video Layer -->
            <div class="ls-html-code ls-hidden">
                <div class="ls-html-textarea">
                    <textarea name="html" cols="50" rows="5" placeholder="Enter layer content here"></textarea>
                    <button type="button" class="button ls-insert-icon">
                        <span class="fa fa-star"></span>
                        <?php ls_e('Add Icon'); ?>
                    </button>
                    <button type="button" class="button ls-upload ls-bulk-upload ls-insert-media">
                        <span class="dashicons dashicons-admin-media"></span>
                        <?php ls_e('Add Media'); ?>
                    </button>
                </div>
                <div class="ls-options">

                    <div class="ls-image-uploader slide-image clearfix">
                        <table>
                            <tr>
                                <td>
                                    <input type="hidden" name="posterId">
                                    <input type="hidden" name="poster">
                                    <div class="ls-image ls-upload ls-bulk-upload ls-media-image not-set" data-l10n-set="<?php ls_e('Click to set'); ?>" data-l10n-change="<?php ls_e('Click to change'); ?>">
                                        <div><img src="<?php echo LS_VIEWS_URL . 'img/admin/blank.gif'; ?>" alt=""></div>
                                        <a href="#" class="dashicons dashicons-dismiss"></a>
                                    </div>
                                </td>
                                <td>
                                    <p>
                                        <?php ls_e('Insert a video poster image from your Image Manager or '); ?>
                                        <a href="#" class="ls-url-prompt"><?php ls_e('insert from URL'); ?></a>.
                                    </p>
                                </td>
                                <td>
                                    <?php lsGetCheckbox($lsDefaults['layers']['mediaBackgroundVideo'], null, ['class' => 'sublayerprop hero bgvideo']); ?>
                                    <?php echo $lsDefaults['layers']['mediaBackgroundVideo']['name']; ?>
                                </td>
                            </tr>
                        </table>

                        <div class="ls-bgvideo-options ls-notification-info">
                            <i class="dashicons dashicons-info"></i>
                            <?php ls_e('Please note, the slide background image (if any) will cover the video.'); ?>
                        </div>
                    </div>

                    <div class="ls-separator"><span><?php ls_e('options'); ?></span></div>
                    <table class="ls-media-options">
                        <tr>
                            <td>
                                <?php echo $lsDefaults['layers']['mediaAutoPlay']['name']; ?> <br>
                                <?php lsGetSelect($lsDefaults['layers']['mediaAutoPlay'], null, ['class' => 'sublayerprop']); ?>
                            </td>
                            <td>
                                <?php echo $lsDefaults['layers']['mediaFillMode']['name']; ?> <br>
                                <?php lsGetSelect($lsDefaults['layers']['mediaFillMode'], null, ['class' => 'sublayerprop']); ?>
                            </td>
                            <td>
                                <?php echo $lsDefaults['layers']['mediaControls']['name']; ?> <br>
                                <?php lsGetSelect($lsDefaults['layers']['mediaControls'], null, ['class' => 'sublayerprop']); ?>
                            </td>
                            <td>
                                <?php echo $lsDefaults['layers']['mediaInfo']['name']; ?> <br>
                                <?php lsGetSelect($lsDefaults['layers']['mediaInfo'], null, ['class' => 'sublayerprop']); ?>
                            </td>
                            <td class="volume">
                                <?php echo $lsDefaults['layers']['mediaVolume']['name']; ?> <br>
                                <?php lsGetInput($lsDefaults['layers']['mediaVolume'], null, ['class' => 'sublayerprop']); ?>
                            </td>
                            <td class="overlay">
                                <?php echo $lsDefaults['layers']['mediaOverlay']['name']; ?> <br>
                                <?php lsGetSelect($lsDefaults['layers']['mediaOverlay'], null, ['class' => 'sublayerprop', 'options' => ['disabled' => 'No overlay image'] + array_combine(array_map(function ($basename) {
                                    return LS_VIEWS_URL . 'img/layerslider/overlays/' . $basename;
                                }, $basenames = array_map('basename', glob(_PS_MODULE_DIR_ . 'layerslider/views/img/layerslider/overlays/*.png'))), $basenames)]); ?>
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
                        <?php ls_e("Click on one or more post placeholders to insert them into your layer's content. Post placeholders act like shortcodes, and they will be filled with the actual content from your dynamic content."); ?><br>
                        <?php ls_e('Limit text length (if any)'); ?>
                        <input type="number" name="post_text_length">
                        <button type="button" class="button ls-configure-posts"><span class="dashicons dashicons-admin-post"></span><?php ls_e('Configure dynamic content'); ?></button>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="ls-sublayer-page ls-sublayer-options">
        <div class="ls-easy">
            <ul class="layer-properties-box clearfix">
                <li class="ls-easy-tr" data-prop="transitionin">
                    <h6><?php ls_e('Add opening transition'); ?></h6>
                    <h5><span><?php ls_e('Opening transition'); ?></span></h5>
                    <button type="button" class="button ls-blue-button ls-preset">
                        <i class="dashicons dashicons-admin-settings"></i>
                        <?php ls_e('Choose preset'); ?>
                    </button>
                    <ul></ul>
                    <select class="ls-add-tr-prop"><option value="">&#10010;&nbsp;&nbsp;&nbsp;<?php ls_e('Add property'); ?></option></select>
                </li>
                <li class="ls-easy-tr" data-prop="transitionout">
                    <h6><?php ls_e('Add ending transition'); ?></h6>
                    <h5><span><?php ls_e('Ending transition'); ?></span><i class="dashicons dashicons-no"></i></h5>
                    <button type="button" class="button ls-blue-button ls-preset">
                        <i class="dashicons dashicons-admin-settings"></i>
                        <?php ls_e('Choose preset'); ?>
                    </button>
                    <ul></ul>
                    <select class="ls-add-tr-prop"><option value="">&#10010;&nbsp;&nbsp;&nbsp;<?php ls_e('Add property'); ?></option></select>
                </li>
                <li class="ls-easy-tr" data-prop="loop">
                    <h6><?php ls_e('Add loop transition'); ?></h6>
                    <h5><span><?php ls_e('Loop transition'); ?></span><i class="dashicons dashicons-no"></i></h5>
                    <button type="button" class="button ls-blue-button ls-preset">
                        <i class="dashicons dashicons-admin-settings"></i>
                        <?php ls_e('Choose preset'); ?>
                    </button>
                    <ul></ul>
                    <select class="ls-add-tr-prop"><option value="">&#10010;&nbsp;&nbsp;&nbsp;<?php ls_e('Add property'); ?></option></select>
                </li>
                <li class="ls-easy-tr" data-prop="hover">
                    <h6><?php ls_e('Add hover transition'); ?></h6>
                    <h5><span><?php ls_e('Hover transition'); ?></span><i class="dashicons dashicons-no"></i></h5>
                    <button type="button" class="button ls-blue-button ls-preset">
                        <i class="dashicons dashicons-admin-settings"></i>
                        <?php ls_e('Choose preset'); ?>
                    </button>
                    <ul></ul>
                    <select class="ls-add-tr-prop"><option value="">&#10010;&nbsp;&nbsp;&nbsp;<?php ls_e('Add property'); ?></option></select>
                </li>
            </ul>
        </div>
        <div class="ls-adv">
            <select id="ls-transition-selector">
                <option value="0"><?php ls_e('Opening Transition properties'); ?></option>
                <option value="1"><?php ls_e('Opening Text Transition properties'); ?></option>
                <option value="2"><?php ls_e('Loop or Middle Transition properties'); ?></option>
                <option value="3"><?php ls_e('Ending Text Transition properties'); ?></option>
                <option value="4"><?php ls_e('Ending Transition properties'); ?></option>
                <option value="5"><?php ls_e('Hover Transition properties'); ?></option>
                <option value="6"><?php ls_e('Parallax Transition properties'); ?></option>
            </select>

            <table id="ls-transition-selector-table">
                <tr>
                    <td class="ls-padding"></td>
                    <td class="ls-opening-transition">
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-in">
                                <div class="ls-preview-layer"></div>
                            </div>
                            <span><?php ls_e('Opening<br>Transition'); ?></span>
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
                            <span><?php ls_e('Opening Text<br>Transition'); ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                    <td>
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-loop">
                                <div class="ls-preview-layer"></div>
                            </div>
                            <span><?php ls_e('Loop or Middle<br>Transition'); ?></span>
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
                            <span><?php ls_e('Ending Text<br>Transition'); ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                    <td>
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-out">
                                <div class="ls-preview-layer"></div>
                            </div>
                            <span><?php ls_e('Ending<br>Transition'); ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                    <td>
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-hover">
                                <div class="ls-preview-layer"></div>
                            </div>
                            <span><?php ls_e('Hover<br>Transition'); ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                    <td>
                        <div>
                            <div class="ls-tpreview-wrapper" id="ls-tpreview-parallax">
                                <div class="ls-preview-layer"></div>
                                <div class="ls-preview-layer ls-preview-layer_b"></div>
                            </div>
                            <span><?php ls_e('Parallax<br>Transition'); ?></span>
                        </div>
                    </td>
                    <td class="ls-padding"></td>
                </tr>
            </table>

            <div id="ls-transition-warning">
                <div class="ls-notification-info">
                    <i class="dashicons dashicons-info"></i>
                    <?php ls_e('Layers require an opening transition in order to become visible during the slideshow. Enable either <mark>Opening Transition</mark> or <mark>Opening Text Transition</mark> to make this layer visible again.'); ?>
                </div>
            </div>

            <div id="ls-layer-transitions">

                <!-- Opening Transition -->
                <section data-storage="ls-tr-in">
                    <div>
                        <div class="ls-separator"><span><?php ls_e('Opening Transition properties'); ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED'); ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['transitionIn'], null, ['class' => 'sublayerprop large toggle']); ?></div>
                            <div class="ls-h-description"><?php ls_e('The following are the initial options from which this layer animates toward the appropriate values set under the Styles tab when it enters into the slider canvas.'); ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties'); ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties'); ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Position &amp; Dimensions'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInOffsetX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInOffsetX'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInOffsetY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInOffsetY'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInScaleX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInScaleX'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInScaleY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInScaleY'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInWidth']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInWidth'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInHeight']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInHeight'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation, Skew &amp; Mask'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInRotate']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInRotate'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInRotateX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInRotateX'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInRotateY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInRotateY'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInSkewX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInSkewX'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInSkewY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInSkewY'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInClip']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInClip'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInDelay']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInDelay'], null, ['class' => 'sublayerprop']); ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInDuration']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInDuration'], null, ['class' => 'sublayerprop']); ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['transitionInEasing']['name']; ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['transitionInEasing'], null, ['class' => 'sublayerprop', 'options' => $lsDefaults['easings']]); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInTransformOrigin']['name']; ?>
                                        </div>
                                        <div>
                                            <i class="dashicons dashicons-search"></i><?php lsGetInput($lsDefaults['layers']['transitionInTransformOrigin'], null, ['class' => 'sublayerprop', 'style' => 'width:130px']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInPerspective']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInPerspective'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <!-- <li>
                                        <div>
                                            Perspective
                                        </div>
                                        <div>
                                            <?php /* lsGetInput($lsDefaults['layers']['transitionInTransformPerspective'], null, ['class' => 'sublayerprop', 'style' => 'width:130px']); */ ?>
                                        </div>
                                    </li> -->
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInFade']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetCheckbox($lsDefaults['layers']['transitionInFade'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInColor']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInColor'], null, ['class' => 'sublayerprop ls-colorpicker']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInBGColor']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInBGColor'], null, ['class' => 'sublayerprop ls-colorpicker']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionInRadius']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInRadius'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                     <li>
                                        <div>
                                            <a href="https://developer.mozilla.org/en/docs/Web/CSS/filter#Functions" target="_blank"><?php echo $lsDefaults['layers']['transitionInFilter']['name']; ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionInFilter'], null, ['class' => 'sublayerprop']); ?>
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
                        <div class="ls-separator"><span><?php ls_e('Opening Text Transition properties'); ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED'); ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['textTransitionIn'], null, ['class' => 'sublayerprop large toggle']); ?></div>
                            <div class="ls-h-description"><?php ls_e('The following options specify the initial state of each text fragments before they start animating toward the joint whole word.'); ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties'); ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties'); ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Type, Position &amp; Dimensions'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textTypeIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textTypeIn'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textOffsetXIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textOffsetXIn'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textOffsetYIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textOffsetYIn'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textScaleXIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textScaleXIn'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textScaleYIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textScaleYIn'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation &amp; Skew'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateIn'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateXIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateXIn'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateYIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateYIn'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textSkewXIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textSkewXIn'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textSkewYIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textSkewYIn'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform'); ?></span></h5>
                                <ul>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Sets the starting time for this transition. Select one of the pre-defined options from this list to control timing in relation with other transition types. Additionally, you can shift starting time with the modifier controls below.'); ?>">
                                        <div><?php ls_e('Start when'); ?></div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textStartAtIn'], null, ['class' => 'sublayerprop start-at-calc undomanager-merge']); ?>
                                            <?php lsGetSelect($lsDefaults['layers']['textStartAtInTiming'], null, ['class' => 'sublayerprop start-at-timing']); ?>
                                        </div>
                                    </li>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Shifts the above selected starting time by performing a custom operation. For example, &quot;- 1000&quot; will advance the animation by playing it 1 second (1000 milliseconds) earlier.'); ?>">
                                        <div><?php ls_e('with modifier'); ?></div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textStartAtInOperator'], null, ['class' => 'sublayerprop start-at-operator']); ?>
                                            <?php lsGetInput($lsDefaults['layers']['textStartAtInValue'], null, ['class' => 'sublayerprop start-at-value']); ?>  ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textDurationIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textDurationIn'], null, ['class' => 'sublayerprop']); ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textShiftIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textShiftIn'], null, ['class' => 'sublayerprop']); ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['textEasingIn']['name']; ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textEasingIn'], null, ['class' => 'sublayerprop', 'options' => $lsDefaults['easings']]); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textTransformOriginIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textTransformOriginIn'], null, ['class' => 'sublayerprop', 'style' => 'width:130px']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textPerspectiveIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textPerspectiveIn'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textFadeIn']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetCheckbox($lsDefaults['layers']['textFadeIn'], null, ['class' => 'sublayerprop']); ?>
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
                        <div class="ls-separator"><span><?php ls_e('Loop / Middle Transition properties'); ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED'); ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['loop'], null, ['class' => 'sublayerprop large toggle']); ?></div>
                            <div class="ls-h-description"><?php ls_e('Repeats a transition based on the options below. If you set the Loop Count to 1, it can also act as a middle transition in the chain of animation lifecycles.'); ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties'); ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties'); ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Position &amp; Dimensions'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopOffsetX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopOffsetX'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopOffsetY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopOffsetY'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopScaleX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopScaleX'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopScaleY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopScaleY'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation, Skew &amp; Mask'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopRotate']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopRotate'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopRotateX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopRotateX'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopRotateY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopRotateY'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopSkewX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopSkewX'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopSkewY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopSkewY'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopClip']['name']; ?><br>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopClip'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform'); ?></span></h5>
                                <ul>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Sets the starting time for this transition. Select one of the pre-defined options from this list to control timing in relation with other transition types. Additionally, you can shift starting time with the modifier controls below.'); ?>">
                                        <div><?php ls_e('Start when'); ?></div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopStartAt'], null, ['class' => 'sublayerprop start-at-calc undomanager-merge']); ?>
                                            <?php lsGetSelect($lsDefaults['layers']['loopStartAtTiming'], null, ['class' => 'sublayerprop start-at-timing']); ?>
                                        </div>
                                    </li>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Shifts the above selected starting time by performing a custom operation. For example, &quot;- 1000&quot; will advance the animation by playing it 1 second (1000 milliseconds) earlier.'); ?>">
                                        <div><?php ls_e('with modifier'); ?></div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['loopStartAtOperator'], null, ['class' => 'sublayerprop start-at-operator']); ?>
                                            <?php lsGetInput($lsDefaults['layers']['loopStartAtValue'], null, ['class' => 'sublayerprop start-at-value']); ?>  ms
                                        </div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['loopDuration']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['loopDuration'], null, ['class' => 'sublayerprop']); ?> ms</div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['loopEasing']['name']; ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['loopEasing'], null, ['class' => 'sublayerprop', 'options' => $lsDefaults['easings']]); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopCount']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopCount'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopWait']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopWait'], null, ['class' => 'sublayerprop']); ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopYoyo']['name']; ?>
                                        </div>
                                        <div>
                                            <label style="display:inline" data-help="<?php echo $lsDefaults['layers']['loopYoyo']['tooltip']; ?>"><?php lsGetCheckbox($lsDefaults['layers']['loopYoyo'], null, ['class' => 'sublayerprop']); ?></label>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopTransformOrigin']['name']; ?>
                                        </div>
                                        <div>
                                            <i class="dashicons dashicons-search"></i><?php lsGetInput($lsDefaults['layers']['loopTransformOrigin'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopPerspective']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopPerspective'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>


                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['loopOpacity']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopOpacity'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                     <li>
                                        <div>
                                            <a href="https://developer.mozilla.org/en/docs/Web/CSS/filter#Functions" target="_blank"><?php echo $lsDefaults['layers']['loopFilter']['name']; ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['loopFilter'], null, ['class' => 'sublayerprop']); ?>
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
                        <div class="ls-separator"><span><?php ls_e('Ending Text Transition properties'); ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED'); ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['textTransitionOut'], null, ['class' => 'sublayerprop large toggle']); ?></div>
                            <div class="ls-h-description"><?php ls_e('Each text fragment will animate from the joint whole word to the options you specify here.'); ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties'); ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties'); ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Type, Position &amp; Dimensions'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textTypeOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textTypeOut'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textOffsetXOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textOffsetXOut'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textOffsetYOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textOffsetYOut'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textScaleXOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textScaleXOut'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textScaleYOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textScaleYOut'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation &amp; Skew'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateOut'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateXOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateXOut'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textRotateYOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textRotateYOut'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textSkewXOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textSkewXOut'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textSkewYOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textSkewYOut'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform'); ?></span></h5>
                                <ul>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Sets the starting time for this transition. Select one of the pre-defined options from this list to control timing in relation with other transition types. Additionally, you can shift starting time with the modifier controls below.'); ?>">
                                        <div><?php ls_e('Start when'); ?></div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textStartAtOut'], null, ['class' => 'sublayerprop start-at-calc undomanager-merge']); ?>
                                            <?php lsGetSelect($lsDefaults['layers']['textStartAtOutTiming'], null, ['class' => 'sublayerprop start-at-timing']); ?>
                                        </div>
                                    </li>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Shifts the above selected starting time by performing a custom operation. For example, &quot;- 1000&quot; will advance the animation by playing it 1 second (1000 milliseconds) earlier.'); ?>">
                                        <div><?php ls_e('with modifier'); ?></div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textStartAtOutOperator'], null, ['class' => 'sublayerprop start-at-operator']); ?>
                                            <?php lsGetInput($lsDefaults['layers']['textStartAtOutValue'], null, ['class' => 'sublayerprop start-at-value']); ?>  ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textDurationOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textDurationOut'], null, ['class' => 'sublayerprop']); ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textShiftOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textShiftOut'], null, ['class' => 'sublayerprop']); ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['textEasingOut']['name']; ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['textEasingOut'], null, ['class' => 'sublayerprop', 'options' => $lsDefaults['easings']]); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textTransformOriginOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textTransformOriginOut'], null, ['class' => 'sublayerprop', 'style' => 'width:130px']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textPerspectiveOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['textPerspectiveOut'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['textFadeOut']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetCheckbox($lsDefaults['layers']['textFadeOut'], null, ['class' => 'sublayerprop']); ?>
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
                        <div class="ls-separator"><span><?php ls_e('Ending Transition properties'); ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED'); ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['transitionOut'], null, ['class' => 'sublayerprop large toggle']); ?></div>
                            <div class="ls-h-description"><?php ls_e('The following options will be the end values where this layer animates toward when it leaves the slider canvas.'); ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties'); ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties'); ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Position &amp; Dimensions'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutOffsetX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutOffsetX'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutOffsetY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutOffsetY'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutScaleX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutScaleX'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutScaleY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutScaleY'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutWidth']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutWidth'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutHeight']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutHeight'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation, Skew &amp; Mask'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutRotate']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutRotate'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutRotateX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutRotateX'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutRotateY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutRotateY'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutSkewX']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutSkewX'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutSkewY']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutSkewY'], null, ['class' => 'sublayerprop']); ?> &deg;
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutClip']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutClip'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform'); ?></span></h5>
                                <ul>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Sets the starting time for this transition. Select one of the pre-defined options from this list to control timing in relation with other transition types. Additionally, you can shift starting time with the modifier controls below.'); ?>">
                                        <div><?php ls_e('Start when'); ?></div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutStartAt'], null, ['class' => 'sublayerprop start-at-calc undomanager-merge']); ?>
                                            <?php lsGetSelect($lsDefaults['layers']['transitionOutStartAtTiming'], null, ['class' => 'sublayerprop start-at-timing']); ?>
                                        </div>
                                    </li>
                                    <li class="start-at-wrapper" data-help="<?php ls_e('Shifts the above selected starting time by performing a custom operation. For example, &quot;- 1000&quot; will advance the animation by playing it 1 second (1000 milliseconds) earlier.'); ?>">
                                        <div><?php ls_e('with modifier'); ?></div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['transitionOutStartAtOperator'], null, ['class' => 'sublayerprop start-at-operator']); ?>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutStartAtValue'], null, ['class' => 'sublayerprop start-at-value']); ?>  ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutDuration']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutDuration'], null, ['class' => 'sublayerprop']); ?> ms
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['transitionOutEasing']['name']; ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetSelect($lsDefaults['layers']['transitionOutEasing'], null, ['class' => 'sublayerprop', 'options' => $lsDefaults['easings']]); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutTransformOrigin']['name']; ?>
                                        </div>
                                        <div>
                                            <i class="dashicons dashicons-search"></i><?php lsGetInput($lsDefaults['layers']['transitionOutTransformOrigin'], null, ['class' => 'sublayerprop', 'style' => 'width:130px']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutPerspective']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutPerspective'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <!-- <li>
                                        <div>
                                            Perspective
                                        </div>
                                        <div>
                                            <?php /* lsGetInput($lsDefaults['layers']['transitionOutTransformPerspective'], null, ['class' => 'sublayerprop', 'style' => 'width:130px']); */ ?>
                                        </div>
                                    </li> -->
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutFade']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetCheckbox($lsDefaults['layers']['transitionOutFade'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutColor']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutColor'], null, ['class' => 'sublayerprop ls-colorpicker']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutBGColor']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutBGColor'], null, ['class' => 'sublayerprop ls-colorpicker']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <?php echo $lsDefaults['layers']['transitionOutRadius']['name']; ?>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutRadius'], null, ['class' => 'sublayerprop']); ?>
                                        </div>
                                    </li>
                                     <li>
                                        <div>
                                            <a href="https://developer.mozilla.org/en/docs/Web/CSS/filter#Functions" target="_blank"><?php echo $lsDefaults['layers']['transitionOutFilter']['name']; ?></a>
                                        </div>
                                        <div>
                                            <?php lsGetInput($lsDefaults['layers']['transitionOutFilter'], null, ['class' => 'sublayerprop']); ?>
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
                        <div class="ls-separator"><span><?php ls_e('Hover Transition properties'); ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED'); ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['hover'], null, ['class' => 'sublayerprop large toggle']); ?></div>
                            <div class="ls-h-description"><?php ls_e('Plays a transition based on the options below when the user moves the mouse cursor over this layer.'); ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties'); ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties'); ?></a>
                            </div>
                        </header>
                    </div>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix">
                            <li>
                                <h5><span><?php ls_e('Position &amp; Dimensions'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverOffsetX']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverOffsetX'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverOffsetY']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverOffsetY'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverScaleX']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverScaleX'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverScaleY']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverScaleY'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Rotation, Skew &amp; Mask'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverRotate']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverRotate'], null, ['class' => 'sublayerprop']); ?> &deg;</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverRotateX']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverRotateX'], null, ['class' => 'sublayerprop']); ?> &deg;</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverRotateY']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverRotateY'], null, ['class' => 'sublayerprop']); ?> &deg;</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverSkewX']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverSkewX'], null, ['class' => 'sublayerprop']); ?> &deg;</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverSkewY']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverSkewY'], null, ['class' => 'sublayerprop']); ?> &deg;</div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverInDuration']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverInDuration'], null, ['class' => 'sublayerprop']); ?> ms</div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverOutDuration']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverOutDuration'], null, ['class' => 'sublayerprop']); ?> ms</div>
                                    </li>
                                    <li>
                                        <div><a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['hoverInEasing']['name']; ?></a></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['hoverInEasing'], null, ['class' => 'sublayerprop', 'options' => $lsDefaults['easings']]); ?></div>
                                    </li>
                                    <li>
                                        <div><a href="http://easings.net/" target="_blank"><?php echo $lsDefaults['layers']['hoverOutEasing']['name']; ?></a></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['hoverOutEasing'], null, ['class' => 'sublayerprop', 'options' => array_merge(['' => '- Same easing -'], $lsDefaults['easings'])]); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverTransformOrigin']['name']; ?></div>
                                        <div><i class="dashicons dashicons-search"></i><?php lsGetInput($lsDefaults['layers']['hoverTransformOrigin'], null, ['class' => 'sublayerprop', 'style' => 'width:130px']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverTransformPerspective']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverTransformPerspective'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Style properties'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverOpacity']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverOpacity'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverColor']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverColor'], null, ['class' => 'sublayerprop ls-colorpicker']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverBGColor']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverBGColor'], null, ['class' => 'sublayerprop ls-colorpicker']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverBorderRadius']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['hoverBorderRadius'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['hoverTopOn']['name']; ?></div>
                                        <div><?php lsGetCheckbox($lsDefaults['layers']['hoverTopOn'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </section>




                <!-- Parallax Transition -->
                <section data-storage="ls-tr-parallax">
                    <div>
                        <div class="ls-separator"><span><?php ls_e('Parallax Transition properties'); ?></span></div>
                        <header>
                            <div class="ls-h-enabled"><?php ls_e('ENABLED'); ?></div>
                            <div class="ls-h-button"><?php lsGetCheckbox($lsDefaults['layers']['parallax'], null, ['class' => 'sublayerprop large toggle']); ?></div>
                            <div class="ls-h-description"><?php ls_e('Select a parallax type and event, then set the Parallax Level option to enable parallax layers.'); ?></div>
                            <div class="ls-h-actions">
                                <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy transition properties'); ?></a>
                                <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste transition properties'); ?></a>
                            </div>
                        </header>
                    <div class="overlay">
                        <ul class="layer-properties-box clearfix col-3">
                            <li>
                                <h5><span><?php ls_e('Basic Settings'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxLevel']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxLevel'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxType']['name']; ?></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['parallaxType'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxEvent']['name']; ?></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['parallaxEvent'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxAxis']['name']; ?></div>
                                        <div><?php lsGetSelect($lsDefaults['layers']['parallaxAxis'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Distance &amp; Rotation'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxDistance']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxDistance'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxRotate']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxRotate'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h5><span><?php ls_e('Timing &amp; Transform'); ?></span></h5>
                                <ul>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxDurationMove']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxDurationMove'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxDurationLeave']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxDurationLeave'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxTransformOrigin']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxTransformOrigin'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                    <li>
                                        <div><?php echo $lsDefaults['layers']['parallaxPerspective']['name']; ?></div>
                                        <div><?php lsGetInput($lsDefaults['layers']['parallaxPerspective'], null, ['class' => 'sublayerprop']); ?></div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </section>
            </div>

            <div class="ls-separator"><span><?php ls_e('Other settings'); ?></span></div>

            <div class="ls-layer-other-settings clearfix">
                <div>
                    <div>
                        <?php echo $lsDefaults['layers']['transitionStatic']['name']; ?>
                    </div>
                    <div>
                        <?php lsGetSelect($lsDefaults['layers']['transitionStatic'], null, ['class' => 'sublayerprop']); ?>
                    </div>
                </div>


                <div class="clearfix">
                     <div>
                        <?php echo $lsDefaults['layers']['transitionKeyframe']['name']; ?>
                    </div>
                    <div>
                        <?php lsGetCheckbox($lsDefaults['layers']['transitionKeyframe'], null, ['class' => 'sublayerprop']); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="ls-sublayer-page ls-sublayer-link">
        <h3 class="subheader"><?php ls_e('Linking'); ?></h3>
        <div class="ls-slide-link clearfix">
            <div>
                <?php lsGetInput($lsDefaults['layers']['linkURL'], null, ['class' => 'url', 'placeholder' => $lsDefaults['layers']['linkURL']['name']]); ?>
                <input type="hidden" name="linkId">
                <input type="hidden" name="linkName">
                <input type="hidden" name="linkType">
                <a href="#" class="change">
                    <span class="dashicons dashicons-editor-unlink"></span>
                    <?php ls_e('Change Link'); ?>
                </a>
                <span><a href="#" class="dyn"><?php ls_e('Use Dynamic post URL'); ?></a></span>
            </div>
            <?php lsGetSelect($lsDefaults['layers']['linkTarget'], null); ?>
        </div>

        <h3 class="subheader"><?php ls_e('Common Attributes'); ?></h3>
        <div class="ls-sublayer-attributes">
            <table class="ls-sublayer-common-attributes">
                <tbody>
                    <tr data-help="<?php echo $lsDefaults['layers']['ID']['tooltip']; ?>">
                        <td class="first"><input type="text" value="id" disabled></td>
                        <td class="second"><input type="text" name="id"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it."); ?>">
                            <?php ls_e('On layer'); ?>
                        </td>
                    </tr>
                    <tr data-help="<?php echo $lsDefaults['layers']['class']['tooltip']; ?>">
                        <td class="first"><input type="text" value="class" disabled></td>
                        <td class="second"><input type="text" name="class"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it."); ?>">
                            <?php ls_e('On layer'); ?>
                        </td>
                    </tr>
                    <tr data-help="<?php echo $lsDefaults['layers']['title']['tooltip']; ?>">
                        <td class="first"><input type="text" value="title" disabled></td>
                        <td class="second"><input type="text" name="title"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it."); ?>">
                            <?php ls_e('On parent'); ?>
                        </td>
                    </tr>
                    <tr data-help="<?php echo $lsDefaults['layers']['alt']['tooltip']; ?>">
                        <td class="first"><input type="text" value="alt" disabled></td>
                        <td class="second"><input type="text" name="alt"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it."); ?>">
                            <?php ls_e('On layer'); ?>
                        </td>
                    </tr>
                    <tr class="ls-adv" data-help="<?php echo $lsDefaults['layers']['rel']['tooltip']; ?>">
                        <td class="first"><input type="text" value="rel" disabled></td>
                        <td class="second"><input type="text" name="rel"></td>
                        <td class="third" data-help="<?php ls_e("In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. Some attributes will be applied on the wrapper (if any), which is desirable in many cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. If the pre-defined option doesn't fit your needs, use custom attributes below to override it."); ?>">
                            <?php ls_e('On parent'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3 class="subheader ls-adv"><?php ls_e('Custom Attributes'); ?></h3>
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
                            <input type="text" placeholder="<?php ls_e('Attribute name'); ?>">
                        </td>
                        <td class="second">
                            <input type="text" placeholder="<?php ls_e('Attribute value'); ?>">
                        </td>
                        <td class="third" data-help="<?php ls_e('In some cases your layers may be wrapped by another element. For example, an ＜A＞ tag when you use layer linking. By default, new attributes will be applied on the wrapper (if any), which is desirable in most cases (e.g. lightbox plugins). If there is no wrapper element, attributes will be automatically applied on the layer itself. Uncheck this option when you need to apply this attribute on the layer element in all cases.'); ?>">
                            <input type="checkbox" class="small noreset" checked> <?php ls_e('On parent'); ?>
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
                    <h5><?php ls_e('Layout'); ?> <span>| <?php ls_e('sizing & position'); ?></span></h5>
                    <div class="ls-layer-visual-box">
                        <div class="ls-layer-position">
                            <div>
                                <?php lsGetInput($lsDefaults['layers']['top'], null, ['class' => 'auto ls-layer-top']); ?>
                                <?php lsGetInput($lsDefaults['layers']['left'], null, ['class' => 'auto ls-layer-left']); ?>
                                <span class="ls-layer-top"><?php echo $lsDefaults['layers']['top']['name']; ?></span>
                                <span class="ls-layer-left"><?php echo $lsDefaults['layers']['left']['name']; ?></span>
                            </div>
                            <div class="ls-layer-border">
                                <?php ls_e('border'); ?>
                                <b class="ls-border-top-value">–</b>
                                <b class="ls-border-right-value">–</b>
                                <b class="ls-border-bottom-value">–</b>
                                <b class="ls-border-left-value">–</b>
                                <div class="ls-layer-padding">
                                    <?php ls_e('padding'); ?>
                                    <b class="ls-padding-top-value">–</b>
                                    <b class="ls-padding-right-value">–</b>
                                    <b class="ls-padding-bottom-value">–</b>
                                    <b class="ls-padding-left-value">–</b>
                                    <div class="ls-layer-size">
                                        <?php lsGetInput($lsDefaults['layers']['width'], null, ['class' => 'auto', 'placeholder' => 'auto']); ?><span class="ls-x">x</span><?php lsGetInput($lsDefaults['layers']['height'], null, ['class' => 'auto', 'placeholder' => 'auto']); ?>
                                        <br>
                                        <span class="ls-wh"><?php echo $lsDefaults['layers']['width']['name']; ?></span><span class="ls-wh"><?php echo $lsDefaults['layers']['height']['name']; ?></span>
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
                                        <?php echo $lsDefaults['layers']['position']['name']; ?>
                                    </td>
                                    <td>
                                        <?php lsGetSelect($lsDefaults['layers']['position'], null, ['class' => 'sublayerprop']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $lsDefaults['layers']['zIndex']['name']; ?>
                                    </td>
                                    <td>
                                        <?php lsGetInput($lsDefaults['layers']['zIndex'], null, ['class' => 'auto']); ?>
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
                                    <td class="ls-bptable-2"><?php ls_e('Border'); ?></td>
                                    <td class="ls-bptable-3"><?php ls_e('Padding'); ?></td>
                                </tr>
                                <tr data-edge="top">
                                    <td><?php ls_e('Top'); ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderTop'], null, ['class' => 'auto']); ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['paddingTop'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr data-edge="right">
                                    <td><?php ls_e('Right'); ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderRight'], null, ['class' => 'auto']); ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['paddingRight'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr data-edge="bottom">
                                    <td><?php ls_e('Bottom'); ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderBottom'], null, ['class' => 'auto']); ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['paddingBottom'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr data-edge="left">
                                    <td><?php ls_e('Left'); ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderLeft'], null, ['class' => 'auto']); ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['paddingLeft'], null, ['class' => 'auto']); ?></td>
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
                    <h5><?php ls_e('Actions'); ?></h5>
                    <div class="table-holder">
                        <a href="#" class="copy"><i class="dashicons dashicons-clipboard"></i> <?php ls_e('Copy layer styles'); ?></a>
                        <a href="#" class="paste"><i class="dashicons dashicons-admin-page"></i> <?php ls_e('Paste layer styles'); ?></a>
                    </div>
                </div>
            </div>

            <div>
                <div>
                    <h5><?php ls_e('Text'); ?> <span>| <?php ls_e('font &amp; style'); ?></span></h5>
                    <div class="table-holder">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="right"><?php echo $lsDefaults['layers']['fontFamily']['name']; ?></td>
                                    <td>
                                        <?php lsGetInput($lsDefaults['layers']['fontFamily'], null, ['class' => 'auto', 'data-options' => json_encode(array_merge([
                                            ['name' => 'Arial', 'font' => true],
                                            ['name' => 'Helvetica', 'font' => true],
                                            ['name' => 'Georgia', 'font' => true],
                                            ['name' => 'Comic Sans MS', 'value' => "'Comic Sans MS'", 'font' => true],
                                            ['name' => 'Impact', 'font' => true],
                                            ['name' => 'Tahoma', 'font' => true],
                                            ['name' => 'Verdana', 'font' => true],
                                        ], array_map(function ($font) {
                                            $item = ['font' => true];
                                            $family = explode(':', $font['param'])[0];

                                            if (strpos($family, '+') !== false) {
                                                $family = str_replace('+', ' ', $family);
                                                $item['value'] = "'{$family}'";
                                            }
                                            $item['name'] = $family;

                                            return $item;
                                        }, $googleFonts)))]); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['fontSize']['name']; ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['fontSize'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['lineHeight']['name']; ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['lineHeight'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['textAlign']['name']; ?></td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['textAlign'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['fontWeight']['name']; ?></td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['fontWeight'], null, ['class' => 'auto'], true); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['fontStyle']['name']; ?></td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['fontStyle'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['textDecoration']['name']; ?></td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['textDecoration'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['letterSpacing']['name']; ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['letterSpacing'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['color']['name']; ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['color'], null, ['class' => 'auto ls-colorpicker']); ?></td>
                                </tr>
                                <tr class="ls-adv">
                                    <td><?php echo $lsDefaults['layers']['minFontSize']['name']; ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['minFontSize'], null, ['class' => 'sublayerprop']); ?></td>
                                </tr>
                                <tr class="ls-adv">
                                    <td><?php echo $lsDefaults['layers']['minMobileFontSize']['name']; ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['minMobileFontSize'], null, ['class' => 'sublayerprop']); ?></td>
                                </tr>
                                <tr>
                                    <td><?php ls_e('Word-wrap'); ?></td>
                                    <td><?php lsGetCheckbox($lsDefaults['layers']['wordWrap'], null, ['class' => 'auto']); ?></td>
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
                    <h5><?php ls_e('Misc'); ?> <span>| <?php ls_e('other settings'); ?></span></h5>
                    <div class="table-holder">
                        <table>
                            <tbody>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['background']['name']; ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['background'], null, ['class' => 'auto ls-colorpicker']); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['opacity']['name']; ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['opacity'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lsDefaults['layers']['borderRadius']['name']; ?></td>
                                    <td><?php lsGetInput($lsDefaults['layers']['borderRadius'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr class="ls-adv">
                                    <td>
                                        <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/blend-mode" target="_blank">
                                            <?php echo $lsDefaults['layers']['blendMode']['name']; ?>
                                        </a>
                                    </td>
                                    <td><?php lsGetSelect($lsDefaults['layers']['blendMode'], null, ['class' => 'auto']); ?></td>
                                </tr>
                                <tr class="ls-adv">
                                    <td>
                                        <a href="https://developer.mozilla.org/en/docs/Web/CSS/filter#Functions" target="_blank">
                                            <?php echo $lsDefaults['layers']['filter']['name']; ?>
                                        </a>
                                    </td>
                                    <td><?php lsGetInput($lsDefaults['layers']['filter'], null, ['class' => 'auto']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="ls-adv">
                <div>
                    <h5><?php ls_e('Transforms'); ?> <span>| <?php ls_e('between transitions'); ?></span></h5>
                    <div class="textarea-helper">
                        <table>
                            <tr>
                                <td class="right"><?php echo $lsDefaults['layers']['rotate']['name']; ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['rotate'], null, ['class' => 'sublayerprop transforms']); ?> &deg;</td>
                                <td class="right"><?php echo $lsDefaults['layers']['scaleX']['name']; ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['scaleX'], null, ['class' => 'sublayerprop transforms']); ?></td>
                            </tr>
                            <tr>
                                <td class="right"><?php echo $lsDefaults['layers']['rotateX']['name']; ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['rotateX'], null, ['class' => 'sublayerprop transforms']); ?> &deg;</td>
                                <td class="right"><?php echo $lsDefaults['layers']['scaleY']['name']; ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['scaleY'], null, ['class' => 'sublayerprop transforms']); ?></td>
                            </tr>
                            <tr>
                                <td class="right"><?php echo $lsDefaults['layers']['rotateY']['name']; ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['rotateY'], null, ['class' => 'sublayerprop transforms']); ?> &deg;</td>
                                <td class="right"><?php echo $lsDefaults['layers']['skewX']['name']; ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['skewX'], null, ['class' => 'sublayerprop transforms']); ?> &deg;</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="right"><?php echo $lsDefaults['layers']['skewY']['name']; ?></td>
                                <td><?php lsGetInput($lsDefaults['layers']['skewY'], null, ['class' => 'sublayerprop transforms']); ?> &deg;</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="ls-adv">
                <h5><?php ls_e('Custom CSS'); ?> <span>| <?php ls_e('write your own code'); ?></span></h5>
                <div class="textarea-helper">
                    <textarea rows="5" cols="50" name="style" class="style" data-help="<?php ls_e('If you want to set style settings other then above, you can use here any CSS codes. Please make sure to write valid markup.'); ?>"></textarea>
                </div>
            </div>
        </div>

    </div>
</script>
