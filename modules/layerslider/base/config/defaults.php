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

require_once _PS_MODULE_DIR_ . 'layerslider/classes/PSOpts.php';

$lsDefaults = [
    'slider' => [
        'createdWith' => [
            'value' => '',
            'keys' => 'createdWith',
        ],

        'sliderVersion' => [
            'value' => '',
            'keys' => 'sliderVersion',
            'props' => [
                'forceoutput' => true,
            ],
        ],

        'status' => [
            'value' => true,
            'name' => ls__('Status'),
            'keys' => 'status',
            'desc' => ls__('Unpublished sliders will not be visible for your visitors until you re-enable this option. This also applies to scheduled sliders, thus leaving this option enabled is recommended in most cases.'),
            'props' => [
                'meta' => true,
            ],
        ],

        'scheduleStart' => [
            'value' => '',
            'name' => ls__('Schedule From'),
            'keys' => 'schedule_start',
            'desc' => '<ul>' .
                '<li>' . ls__('Scheduled sliders will only be visible to your visitors between the time period you set here.') . '</li>' .
                '<li>' . ls__("We're using international date and time format to avoid ambiguity.") . '</li>' .
                '<li>' . ls__('Clear the text field above and left it empty if you want to cancel the schedule.') . '</li>' .
                '</ul><span>' . ls__('IMPORTANT:') . '</span><ul>' .
                '<li>' . ls__('You will still need to set the slider status as published,') . '</li>' .
                '<li>' . ls__('and insert the slider to the target page with one of the methods described in the <a href="http://docs.webshopworks.com/creative-slider/56-place-slider-on-the-site/" target="_blank">documentation</a>.') . '</li>' .
                '</ul>',
            'attrs' => [
                'placeholder' => ls__('No schedule'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'scheduleEnd' => [
            'value' => '',
            'name' => ls__('Schedule Until'),
            'keys' => 'schedule_end',
            'desc' => '',
            'attrs' => [
                'placeholder' => ls__('No schedule'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        // ============= //
        // |   Layout  | //
        // ============= //

        'hook' => [
            'value' => '',
            'name' => ls__('Module Position'),
            'keys' => 'hook',
            'desc' => ls__('Slider will appear on the selected position.'),
            'props' => ['meta' => true],
            'attrs' => [
                'type' => 'text',
                'placeholder' => ls__('- None -'),
                'data-options' => ls_get_hook_list(),
            ],
        ],

        // responsive | fullwidth | fullsize | fixedsize
        'type' => [
            'value' => 'responsive',
            'name' => ls__('Slider type'),
            'keys' => 'type',
            'desc' => '',
            'attrs' => [
                'type' => 'hidden',
            ],
        ],

        // The width of a new slider.
        'width' => [
            'value' => 1280,
            'name' => ls__('Canvas width'),
            'keys' => 'width',
            'desc' => ls__('The width of the slider canvas in pixels.'),
            'attrs' => [
                'type' => 'text',
                'placeholder' => 1280,
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        // The height of a new slider.
        'height' => [
            'value' => 720,
            'name' => ls__('Canvas height'),
            'keys' => 'height',
            'desc' => ls__('The height of the slider canvas in pixels.'),
            'attrs' => [
                'type' => 'text',
                'placeholder' => 720,
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        // The maximum width that the slider can get in responsive mode.
        'maxWidth' => [
            'value' => '',
            'name' => ls__('Max-width'),
            'keys' => 'maxwidth',
            'desc' => ls__('The maximum width your slider can take in pixels when responsive mode is enabled.'),
            'attrs' => [
                'type' => 'number',
                'min' => 0,
                'placeholder' => '100%',
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        // Turn on responsiveness under a given width of the slider.
        // Depends on: enabled fullWidth option. Defaults to: 0
        'responsiveUnder' => [
            'value' => '',
            'name' => ls__('Responsive under'),
            'keys' => ['responsiveunder', 'responsiveUnder'],
            'desc' => ls__('Turns on responsive mode in a full-width slider under the specified value in pixels. Can only be used with full-width mode.'),
            'advanced' => true,
            'attrs' => [
                'type' => 'number',
                'min' => 0,
                'placeholder' => ls__('Canvas width'),
            ],
        ],

        'layersContrainer' => [
            'value' => '',
            'keys' => ['sublayercontainer', 'layersContainer'],
        ],

        'fullSizeMode' => [
            'value' => 'normal',
            'name' => ls__('Mode'),
            'keys' => 'fullSizeMode',
            'desc' => ls__('Select the sizing behavior of your full size sliders (e.g. hero scene).'),
            'options' => [
                'normal' => ls__('Normal'),
                'hero' => ls__('Hero scene'),
                'fitheight' => ls__('Fit to parent height'),
            ],
            'attrs' => [
                'min' => 0,
            ],
        ],

        'allowFullscreen' => [
            'value' => false,
            'name' => ls__('Allow fullscreen mode'),
            'keys' => 'allowFullscreen',
            'desc' => ls__('Visitors can enter OS native full-screen mode when double clicking on the slider.'),
        ],

        'maxRatio' => [
            'value' => '',
            'name' => ls__('Maximum responsive ratio'),
            'keys' => 'maxRatio',
            'desc' => ls__('The slider will not enlarge your layers above the target ratio. The value 1 will keep your layers in their initial size, without any upscaling.'),
            'advanced' => true,
        ],

        'fitScreenWidth' => [
            'value' => true,
            'name' => ls__('Fit to screen width'),
            'keys' => 'fitScreenWidth',
            'desc' => ls__('If enabled, the slider will always have the same width as the viewport, even if a theme uses a boxed layout, unless you choose the "Fit to parent height" full size mode.'),
            'advanced' => true,
        ],

        'preventSliderClip' => [
            'value' => true,
            'name' => ls__('Prevent slider clipping'),
            'keys' => 'preventSliderClip',
            'desc' => ls__('Ensures that the theme cannot clip parts of the slider when used in a boxed layout.'),
            'advanced' => true,
        ],

        'insertMethod' => [
            'value' => 'prependTo',
            'name' => ls__('Move the slider by'),
            'keys' => 'insertMethod',
            'desc' => ls__('Move your slider to a different part of the page by providing a jQuery DOM manipulation method & selector for the target destination.'),
            'options' => [
                'prependTo' => 'prepending to',
                'appendTo' => 'appending to',
                'insertBefore' => 'inserting before',
                'insertAfter' => 'inserting after',
            ],
        ],

        'insertSelector' => [
            'value' => '',
            'keys' => 'insertSelector',
            'attrs' => [
                'placeholder' => '#id, .class',
            ],
        ],

        'clipSlideTransition' => [
            'value' => 'disabled',
            'name' => ls__('Clip slide transition'),
            'keys' => 'clipSlideTransition',
            'desc' => ls__('Choose on which axis (if any) you want to clip the overflowing content (i.e. that breaks outside of the slider bounds).'),
            'advanced' => true,
            'options' => [
                'disabled' => ls__('Do not hide'),
                'enabled' => ls__('Hide on both axis'),
                'x' => ls__('X Axis'),
                'y' => ls__('Y Axis'),
            ],
        ],

        // == COMPATIBILITY ==

        'responsiveness' => [
            'value' => true,
            'keys' => 'responsive',
            'props' => [
                'meta' => true,
                'output' => true,
            ],
        ],
        'fullWidth' => [
            'value' => false,
            'keys' => 'forceresponsive',
            'props' => [
                'meta' => true,
                'output' => true,
            ],
        ],

        // == END OF COMPATIBILITY ==

        'slideBGSize' => [
            'value' => 'cover',
            'name' => ls__('Background size'),
            'keys' => 'slideBGSize',
            'desc' => ls__('This will be used as a default on all slides, unless you choose to explicitly override it on a per slide basis.'),
            'options' => [
                'auto' => ls__('Auto'),
                'cover' => ls__('Cover'),
                'contain' => ls__('Contain'),
                '100% 100%' => ls__('Stretch'),
            ],
        ],

        'slideBGPosition' => [
            'value' => '50% 50%',
            'name' => ls__('Background position'),
            'keys' => 'slideBGPosition',
            'desc' => ls__('This will be used as a default on all slides, unless you choose the explicitly override it on a per slide basis.'),
            'options' => [
                '0% 0%' => ls__('left top'),
                '0% 50%' => ls__('left center'),
                '0% 100%' => ls__('left bottom'),
                '50% 0%' => ls__('center top'),
                '50% 50%' => ls__('center center'),
                '50% 100%' => ls__('center bottom'),
                '100% 0%' => ls__('right top'),
                '100% 50%' => ls__('right center'),
                '100% 100%' => ls__('right bottom'),
            ],
        ],

        'parallaxSensitivity' => [
            'value' => 10,
            'name' => ls__('Parallax sensitivity'),
            'keys' => 'parallaxSensitivity',
            'desc' => ls__('Increase or decrease the sensitivity of parallax content when moving your mouse cursor or tilting your mobile device.'),
        ],

        'parallaxCenterLayers' => [
            'value' => 'center',
            'name' => ls__('Parallax center layers'),
            'keys' => 'parallaxCenterLayers',
            'desc' => ls__('Choose a center point for parallax content where all layers will be aligned perfectly according to their original position.'),
            'options' => [
                'center' => ls__('At center of the viewport'),
                'top' => ls__('At the top of the viewport'),
            ],
        ],

        'parallaxCenterDegree' => [
            'value' => 40,
            'name' => ls__('Parallax center degree'),
            'keys' => 'parallaxCenterDegree',
            'desc' => ls__('Provide a comfortable holding position (in degrees) for mobile devices, which should be the center point for parallax content where all layers should align perfectly.'),
        ],

        'parallaxScrollReverse' => [
            'value' => false,
            'name' => 'Reverse scroll direction',
            'keys' => 'parallaxScrollReverse',
            'desc' => ls__('Your parallax layers will move to the opposite direction when scrolling the page.'),
        ],

        // ================= //
        // |    Mobile    | //
        // ================= //

        'optimizeForMobile' => [
            'value' => true,
            'name' => ls__('Optimize for mobile'),
            'keys' => 'optimizeForMobile',
            'advanced' => true,
            'desc' => ls__('Enable optimizations on mobile devices to avoid performance issues (e.g. fewer tiles in slide transitions, reducing performance-heavy effects with very similar results, etc).'),
        ],

        // Disable the slider on mobile devices.
        // Defaults to: false
        'disableOnMobile' => [
            'value' => false,
            'name' => ls__('Disable on mobile'),
            'keys' => 'disableonmobile',
            'desc' => ls__('Disable the slider on mobile devices.'),
            'props' => ['meta' => true],
        ],

        // Disable the slider on tablet devices.
        // Defaults to: false
        'disableOnTablet' => [
            'value' => false,
            'name' => ls__('Disable on tablet'),
            'keys' => 'disableontablet',
            'desc' => ls__('Disable the slider on tablet devices.'),
            'props' => ['meta' => true],
        ],

        // Disable the slider on desktop devices.
        // Defaults to: false
        'disableOnDesktop' => [
            'value' => false,
            'name' => ls__('Disable on desktop'),
            'keys' => 'disableondesktop',
            'desc' => ls__('Disable the slider on desktop devices.'),
            'props' => ['meta' => true],
        ],

        // Hides the slider under the given value of browser width in pixels.
        // Defaults to: 0
        'hideUnder' => [
            'value' => '',
            'name' => ls__('Hide under'),
            'keys' => ['hideunder', 'hideUnder'],
            'desc' => ls__('Hides the slider when the viewport width goes under the specified value.'),
            'attrs' => [
                'type' => 'number',
                'min' => -1,
            ],
        ],

        // Hides the slider over the given value of browser width in pixel.
        // Defaults to: 100000
        'hideOver' => [
            'value' => '',
            'name' => ls__('Hide over'),
            'keys' => ['hideover', 'hideOver'],
            'desc' => ls__('Hides the slider when the viewport becomes wider than the specified value.'),
            'attrs' => [
                'type' => 'number',
                'min' => -1,
            ],
        ],

        'slideOnSwipe' => [
            'value' => true,
            'name' => ls__('Use slide effect when swiping'),
            'keys' => 'slideOnSwipe',
            'desc' => ls__('Ignore selected slide transitions and use sliding effects only when users are changing slides with a swipe gesture on mobile devices.'),
        ],

        // ================ //
        // |   Slideshow  | //
        // ================ //

        // Automatically start slideshow.
        'autoStart' => [
            'value' => true,
            'name' => ls__('Auto-start slideshow'),
            'keys' => ['autostart', 'autoStart'],
            'desc' => ls__('Slideshow will automatically start after page load.'),
        ],

        'startInViewport' => [
            'value' => true,
            'name' => ls__('Start only in viewport'),
            'keys' => ['startinviewport', 'startInViewport'],
            'desc' => ls__('The slider will not start until it becomes visible.'),
        ],

        'hashChange' => [
            'value' => false,
            'name' => ls__('Change URL hash'),
            'keys' => 'hashChange',
            'desc' => ls__('Updates the hash in the page URL when changing slides based on the deeplinks you’ve set to your slides. This makes it possible to share URLs that will start the slider with the currently visible slide.'),
            'advanced' => true,
        ],

        'pauseLayers' => [
            'value' => false,
            'name' => ls__('Pause layers'),
            'keys' => 'pauseLayers',
            'desc' => ls__('If you enable this option, layer transitions will not start playing as long the slideshow is in a paused state.'),
            'advanced' => true,
        ],

        'pauseOnHover' => [
            'value' => 'enabled',
            'name' => ls__('Pause on hover'),
            'keys' => ['pauseonhover', 'pauseOnHover'],
            'options' => [
                'disabled' => ls__('Do nothing'),
                'enabled' => ls__('Pause slideshow'),
                'layers' => ls__('Pause slideshow and layer transitions'),
                'looplayers' => ls__('Pause slideshow and layer transitions, including loops'),
            ],
            'desc' => ls__('Decide what should happen when you move your mouse cursor over the slider.'),
        ],

        // The starting slide of a slider. Non-index value, starts with 1.
        'firstSlide' => [
            'value' => 1,
            'name' => ls__('Start with slide'),
            'keys' => ['firstlayer', 'firstSlide'],
            'desc' => ls__('The slider will start with the specified slide. You can also use the value "random".'),
            'attrs' => ['type' => 'text', 'data-options' => '["random"]'],
        ],

        // Use global shortcuts to control the slider.
        'keybNavigation' => [
            'value' => true,
            'name' => ls__('Keyboard navigation'),
            'keys' => ['keybnav', 'keybNav'],
            'desc' => ls__('You can navigate through slides with the left and right arrow keys.'),
        ],

        // Accepts touch gestures if enabled.
        'touchNavigation' => [
            'value' => true,
            'name' => ls__('Touch navigation'),
            'keys' => ['touchnav', 'touchNav'],
            'desc' => ls__('Gesture-based navigation when swiping on touch-enabled devices.'),
        ],

        'playByScroll' => [
            'value' => false,
            'name' => ls__('Play By Scroll'),
            'keys' => 'playByScroll',
            'desc' => ls__('Play the slider by scrolling the web page. <a href="https://creativeslider.webshopworks.com/play-by-scroll-26" target="_blank">Click here</a> to see a live example.'),
            'premium' => true,
        ],

        'playByScrollSpeed' => [
            'value' => 1,
            'name' => ls__('Play By Scroll Speed'),
            'keys' => 'playByScrollSpeed',
            'desc' => ls__('Play By Scroll speed multiplier.'),
            'premium' => true,
        ],

        'playByScrollStart' => [
            'value' => false,
            'name' => ls__('Start immediately'),
            'keys' => 'playByScrollStart',
            'desc' => ls__('Instead of freezing the slider until visitors start scrolling, the slider will automatically start playback and will only pause at the first keyframe.'),
            'premium' => true,
        ],

        // Number of loops taking by the slideshow.
        // Depends on: shuffle. Defaults to: 0 => infinite
        'loops' => [
            'value' => 0,
            'name' => ls__('Cycles'),
            'keys' => ['loops', 'cycles'],
            'desc' => ls__('Number of cycles if slideshow is enabled.'),
            'attrs' => [
                'type' => 'number',
                'min' => 0,
            ],
        ],

        // The slideshow will always stop at the given number of
        // loops, even when the user restarts slideshow.
        // Depends on: loop. Defaults to: true
        'forceLoopNumber' => [
            'value' => true,
            'name' => ls__('Force number of cycles'),
            'keys' => ['forceloopnum', 'forceCycles'],
            'advanced' => true,
            'desc' => ls__('The slider will always stop at the given number of cycles, even if the slideshow restarts.'),
        ],

        // The slideshow will change slides in random order.
        'shuffle' => [
            'value' => false,
            'name' => ls__('Shuffle mode'),
            'keys' => ['randomslideshow', 'shuffleSlideshow'],
            'desc' => ls__('Slideshow will proceed in random order. This feature does not work with looping.'),
        ],

        // Whether slideshow should goind backwards or not
        // when you switch to a previous slide.
        'twoWaySlideshow' => [
            'value' => false,
            'name' => ls__('Two way slideshow'),
            'keys' => ['twowayslideshow', 'twoWaySlideshow'],
            'advanced' => true,
            'desc' => ls__('Slideshow can go backwards if someone switches to a previous slide.'),
        ],

        'forceLayersOutDuration' => [
            'value' => 750,
            'name' => ls__('Forced animation duration'),
            'keys' => 'forceLayersOutDuration',
            'advanced' => true,
            'desc' => ls__('The animation speed in milliseconds when the slider forces remaining layers out of scene before swapping slides.'),
            'attrs' => [
                'min' => 0,
            ],
        ],

        // ================= //
        // |   Appearance  | //
        // ================= //

        // The default skin.
        'skin' => [
            'value' => 'v6',
            'name' => ls__('Skin'),
            'keys' => 'skin',
            'desc' => ls__("The skin used for this slider. The 'noskin' skin is a border- and buttonless skin. Your custom skins will appear in the list when you create their folders."),
            'props' => [
                'output' => true,
            ],
        ],

        'sliderFadeInDuration' => [
            'value' => 350,
            'name' => ls__('Initial fade duration'),
            'keys' => ['sliderfadeinduration', 'sliderFadeInDuration'],
            'advanced' => true,
            'desc' => ls__('Change the duration of the initial fade animation when the page loads. Enter 0 to disable fading.'),
            'attrs' => [
                'min' => 0,
            ],
        ],

        'sliderClasses' => [
            'value' => '',
            'name' => ls__('Slider Classes'),
            'keys' => 'sliderclass',
            'desc' => ls__('One or more space-separated class names to be added to the slider container element.'),
            'props' => [
                'meta' => true,
            ],
        ],

        // Some CSS values you can append on each slide individually
        // to make some adjustments if needed.
        'sliderStyle' => [
            'value' => 'margin-bottom: 0px;',
            'name' => ls__('Slider CSS'),
            'keys' => ['sliderstyle', 'sliderStyle'],
            'desc' => ls__('You can enter custom CSS to change some style properties on the slider wrapper element. More complex CSS should be applied with the Custom Styles Editor.'),
            'props' => [
                'meta' => true,
            ],
        ],

        // Global background color on all slides.
        'globalBGColor' => [
            'value' => '',
            'name' => ls__('Background color'),
            'keys' => ['backgroundcolor', 'globalBGColor'],
            'desc' => ls__('Global background color of the slider. Slides with non-transparent background will cover this one. You can use all CSS methods such as HEX or RGB(A) values.'),
        ],

        // Global background image on all slides.
        'globalBGImage' => [
            'value' => '',
            'name' => ls__('Background image'),
            'keys' => ['backgroundimage', 'globalBGImage'],
            'desc' => ls__('Global background image of the slider. Slides with non-transparent backgrounds will cover it. This image will not scale in responsive mode.'),
        ],

        'globalBGImageId' => [
            'value' => '',
            'keys' => ['backgroundimageId', 'globalBGImageId'],
            'props' => [
                'meta' => true,
            ],
        ],

        // Global background image repeat
        'globalBGRepeat' => [
            'value' => 'no-repeat',
            'name' => ls__('Background repeat'),
            'keys' => 'globalBGRepeat',
            'desc' => ls__('Global background image repeat.'),
            'options' => [
                'no-repeat' => ls__('No-repeat'),
                'repeat' => ls__('Repeat'),
                'repeat-x' => ls__('Repeat-x'),
                'repeat-y' => ls__('Repeat-y'),
            ],
        ],

        // Global background image behavior
        'globalBGAttachment' => [
            'value' => 'scroll',
            'name' => ls__('Background behavior'),
            'keys' => 'globalBGAttachment',
            'desc' => ls__('Choose between a scrollable or fixed global background image.'),
            'options' => [
                'scroll' => ls__('Scroll'),
                'fixed' => ls__('Fixed'),
            ],
        ],

        // Global background image position
        'globalBGPosition' => [
            'value' => '50% 50%',
            'name' => ls__('Background position'),
            'keys' => 'globalBGPosition',
            'desc' => ls__('Global background image position of the slider. The first value is the horizontal position and the second value is the vertical.'),
        ],

        // Global background image size
        'globalBGSize' => [
            'value' => 'auto',
            'name' => ls__('Background size'),
            'keys' => 'globalBGSize',
            'desc' => ls__('Global background size of the slider. You can set the size in pixels, percentages, or constants: auto | cover | contain '),
            'attrs' => ['data-options' => '[{
                "name": "auto",
                "value": "auto"
            }, {
                "name": "cover",
                "value": "cover"
            }, {
                "name": "contain",
                "value": "contain"
            }, {
                "name": "stretch",
                "value": "100% 100%"
            }]'],
        ],

        // ================= //
        // |   Navigation  | //
        // ================= //

        // Show the next and previous buttons.
        'navPrevNextButtons' => [
            'value' => true,
            'name' => ls__('Show Prev & Next buttons'),
            'keys' => ['navprevnext', 'navPrevNext'],
            'desc' => ls__('Disabling this option will hide the Prev and Next buttons.'),
        ],

        // Show the next and previous buttons
        // only when hovering over the slider.
        'hoverPrevNextButtons' => [
            'value' => true,
            'name' => ls__('Show Prev & Next buttons on hover'),
            'keys' => ['hoverprevnext', 'hoverPrevNext'],
            'desc' => ls__('Show the buttons only when someone moves the mouse cursor over the slider. This option depends on the previous setting.'),
        ],

        // Show the start and stop buttons
        'navStartStopButtons' => [
            'value' => true,
            'name' => ls__('Show Start & Stop buttons'),
            'keys' => ['navstartstop', 'navStartStop'],
            'desc' => ls__('Disabling this option will hide the Start & Stop buttons.'),
        ],

        // Show the slide buttons or thumbnails.
        'navSlideButtons' => [
            'value' => true,
            'name' => ls__('Show slide navigation buttons'),
            'keys' => ['navbuttons', 'navButtons'],
            'desc' => ls__('Disabling this option will hide slide navigation buttons or thumbnails.'),
        ],

        // Show the slider buttons or thumbnails
        // ony when hovering over the slider.
        'hoverSlideButtons' => [
            'value' => false,
            'name' => ls__('Slide navigation on hover'),
            'keys' => ['hoverbottomnav', 'hoverBottomNav'],
            'desc' => ls__('Slide navigation buttons (including thumbnails) will be shown on mouse hover only.'),
        ],

        // Show bar timer
        'barTimer' => [
            'value' => false,
            'name' => ls__('Show bar timer'),
            'keys' => ['bartimer', 'showBarTimer'],
            'desc' => ls__('Show the bar timer to indicate slideshow progression.'),
        ],

        // Show circle timer. Requires CSS3 capable browser.
        // This setting will overrule the 'barTimer' option.
        'circleTimer' => [
            'value' => true,
            'name' => ls__('Show circle timer'),
            'keys' => ['circletimer', 'showCircleTimer'],
            'desc' => ls__('Use circle timer to indicate slideshow progression.'),
        ],

        'slideBarTimer' => [
            'value' => false,
            'name' => ls__('Show slidebar timer'),
            'keys' => ['slidebartimer', 'showSlideBarTimer'],
            'desc' => ls__('You can grab the slidebar timer playhead and seek the whole slide real-time like a movie.'),
        ],

        // ========================== //
        // |  Thumbnail navigation  | //
        // ========================== //

        // Use thumbnails for slide buttons
        // Depends on: navSlideButtons.
        // Possible values: 'disabled', 'hover', 'always'
        'thumbnailNavigation' => [
            'value' => 'hover',
            'name' => ls__('Thumbnail navigation'),
            'keys' => ['thumb_nav', 'thumbnailNavigation'],
            'desc' => ls__('Use thumbnail navigation instead of slide bullet buttons.'),
            'options' => [
                'disabled' => ls__('Disabled'),
                'hover' => ls__('Hover'),
                'always' => ls__('Always'),
            ],
        ],

        // The width of the thumbnail area in percents.
        'thumbnailAreaWidth' => [
            'value' => '60%',
            'name' => ls__('Thumbnail container width'),
            'keys' => ['thumb_container_width', 'tnContainerWidth'],
            'desc' => ls__('The width of the thumbnail area relative to the slider size.'),
        ],

        // Thumbnails' width in pixels.
        'thumbnailWidth' => [
            'value' => 100,
            'name' => ls__('Thumbnail width'),
            'keys' => ['thumb_width', 'tnWidth'],
            'desc' => ls__('The width of thumbnails in the navigation area.'),
            'attrs' => [
                'min' => 0,
            ],
        ],

        // Thumbnails' height in pixels.
        'thumbnailHeight' => [
            'value' => 60,
            'name' => ls__('Thumbnail height'),
            'keys' => ['thumb_height', 'tnHeight'],
            'desc' => ls__('The height of thumbnails in the navigation area.'),
            'attrs' => [
                'min' => 0,
            ],
        ],

        // The opacity of the active thumbnail in percents.
        'thumbnailActiveOpacity' => [
            'value' => 35,
            'name' => ls__('Active thumbnail opacity'),
            'keys' => ['thumb_active_opacity', 'tnActiveOpacity'],
            'desc' => ls__("Opacity in percentage of the active slide's thumbnail."),
            'attrs' => [
                'min' => 0,
                'max' => 100,
            ],
        ],

        // The opacity of inactive thumbnails in percents.
        'thumbnailInactiveOpacity' => [
            'value' => 100,
            'name' => ls__('Inactive thumbnail opacity'),
            'keys' => ['thumb_inactive_opacity', 'tnInactiveOpacity'],
            'desc' => ls__('Opacity in percentage of inactive slide thumbnails.'),
            'attrs' => [
                'min' => 0,
                'max' => 100,
            ],
        ],

        // ============ //
        // |  Videos  | //
        // ============ //

        // Automatically starts vidoes on the given slide.
        'autoPlayVideos' => [
            'value' => true,
            'name' => ls__('Automatically play videos'),
            'keys' => ['autoplayvideos', 'autoPlayVideos'],
            'desc' => ls__('Videos will be automatically started on the active slide.'),
        ],

        // Automatically pauses the slideshow when a video is playing.
        // Auto means it only pauses the slideshow while the video is playing.
        // Possible values: 'auto', 'enabled', 'disabled'
        'autoPauseSlideshow' => [
            'value' => 'auto',
            'name' => ls__('Pause slideshow'),
            'keys' => ['autopauseslideshow', 'autoPauseSlideshow'],
            'desc' => ls__('The slideshow can temporally be paused while videos are playing. You can choose to permanently stop the pause until manual restarting.'),
            'options' => [
                'auto' => ls__('While playing'),
                'enabled' => ls__('Permanently'),
                'disabled' => ls__('No action'),
            ],
        ],

        // The preview image quality of a YouTube video.
        // Some videos doesn't have HD preview images and
        // you may have to lower the quality settings.
        // Possible values:
        // 'maxresdefault.jpg', 'hqdefault.jpg', 'mqdefault.jpg', 'default.jpg'
        'youtubePreviewQuality' => [
            'value' => 'maxresdefault.jpg',
            'name' => ls__('Youtube preview'),
            'keys' => ['youtubepreview', 'youtubePreview'],
            'desc' => ls__('The automatically fetched preview image quaility for YouTube videos when you do not set your own. Please note, some videos do not have HD previews, and you may need to choose a lower quaility.'),
            'options' => [
                'maxresdefault.jpg' => ls__('Maximum quality'),
                'hqdefault.jpg' => ls__('High quality'),
                'mqdefault.jpg' => ls__('Medium quality'),
                'default.jpg' => ls__('Default quality'),
            ],
        ],

        // ========== //
        // |  Misc  | //
        // ========== //

        // Ignores the host/domain names in URLS by converting the to
        // relative format. Useful when you move your site.
        // Prevents linking content from 3rd party servers.
        'relativeURLs' => [
            'value' => false,
            'name' => ls__('Use relative URLs'),
            'keys' => 'relativeurls',
            'desc' => ls__('Use relative URLs for local images. This setting could be important when moving your PS installation.'),
            'props' => [
                'meta' => true,
            ],
        ],

        'allowRestartOnResize' => [
            'value' => false,
            'name' => ls__('Allow restarting slides on resize'),
            'keys' => 'allowRestartOnResize',
            'desc' => ls__('Certain transformation and transition options cannot be updated on the fly when the browser size or device orientation changes. By enabling this option, the slider will automatically detect such situations and will restart the itself to preserve its appearance.'),
            'advanced' => true,
        ],

        'useSrcset' => [
            'value' => true,
            'name' => ls__('Use srcset attribute'),
            'keys' => 'useSrcset',
            'desc' => ls__('The srcset attribute allows loading dynamically scaled images based on screen resolution. It can save bandwidth and allow using retina-ready images on high resolution devices. In some rare edge cases, this option might cause blurry images.'),
        ],

        'enhancedLazyLoad' => [
            'value' => false,
            'name' => ls__('Enhanced lazy load'),
            'keys' => 'enhancedLazyLoad',
            'desc' => ls__('The default lazy loading behavior makes a compromise to ensure maximum compatibility while offering a solution that works ideally in almost all cases. However, by leaving the image ’src’ attribute untouched, there is a slight chance that the browser might start downloading some assets for a split second before Creative Slider cancels them. Enabling this option will eliminate any chance of generating even a minuscule amount of unwanted traffic, but it can also cause issues for search engine indexing and other themes/modules.'),
            'advanced' => true,
            'props' => [
                'meta' => true,
            ],
        ],

        'preferBlendMode' => [
            'value' => 'disabled',
            'name' => ls__('Prefer Blend Mode'),
            'keys' => 'preferBlendMode',
            'desc' => ls__('Enable this option to avoid blend mode issues with slide transitions. Due to technical limitations, this will also clip your slide transitions regardless of your settings.'),
            'options' => [
                'enabled' => ls__('Enabled'),
                'disabled' => ls__('Disabled'),
            ],
            'advanced' => true,
        ],

        // ============== //
        // |  YourLogo  | //
        // ============== //

        // Places a fixed image on the top of the slider.
        'yourLogoImage' => [
            'value' => '',
            'name' => ls__('YourLogo'),
            'keys' => ['yourlogo', 'yourLogo'],
            'desc' => ls__('A fixed image layer can be shown above the slider that remains still throughout the whole slider. Can be used to display logos or watermarks.'),
        ],

        // Custom CSS style settings for the YourLogo image.
        // Depends on: yourLogoImage
        'yourLogoStyle' => [
            'value' => 'left: -10px; top: -10px;',
            'name' => ls__('YourLogo style'),
            'keys' => ['yourlogostyle', 'yourLogoStyle'],
            'desc' => ls__('CSS properties to control the image placement and appearance.'),
        ],

        // Linking the YourLogo image to a given URL.
        // Depends on: yourLogoImage
        'yourLogoLink' => [
            'value' => '',
            'name' => ls__('YourLogo link'),
            'keys' => ['yourlogolink', 'yourLogoLink'],
            'desc' => ls__('Enter a URL to link the YourLogo image.'),
        ],

        // Link target for yourLogoLink.
        // Depends on: yourLogoLink
        'yourLogoTarget' => [
            'value' => '_self',
            'name' => ls__('Link target'),
            'keys' => ['yourlogotarget', 'yourLogoTarget'],
            'desc' => '',
            'options' => [
                '_self' => ls__('Open on the same page'),
                '_blank' => ls__('Open on new page'),
                '_parent' => ls__('Open in parent frame'),
                '_top' => ls__('Open in main frame'),
            ],
        ],

        // Post options
        'postType' => [
            'value' => '',
            'keys' => 'post_type',
            'props' => [
                'meta' => true,
            ],
        ],

        'postOrderBy' => [
            'value' => 'date',
            'keys' => 'post_orderby',
            'options' => [
                'date' => ls__('Date Created'),
                'modified' => ls__('Last Modified'),
                'ID' => ls__('Post ID'),
                'title' => ls__('Post Title'),
                'comment_count' => ls__('Number of Comments'),
                'rand' => ls__('Random'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'postOrder' => [
            'value' => 'DESC',
            'keys' => 'post_order',
            'options' => [
                'ASC' => ls__('Ascending'),
                'DESC' => ls__('Descending'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'postCategories' => [
            'value' => '',
            'keys' => 'post_categories',
            'props' => [
                'meta' => true,
            ],
        ],

        'postTags' => [
            'value' => '',
            'keys' => 'post_tags',
            'props' => [
                'meta' => true,
            ],
        ],

        'postTaxonomy' => [
            'value' => '',
            'keys' => 'post_taxonomy',
            'props' => [
                'meta' => true,
            ],
        ],

        'postTaxTerms' => [
            'value' => '',
            'keys' => 'post_tax_terms',
            'options' => PSOpts::getProductImgTypes(),
            'props' => [
                'meta' => true,
            ],
        ],

        // Old and obsolete API
        'cbInit' => [
            'value' => "function(element) {\r\n\r\n}",
            'keys' => ['cbinit', 'cbInit'],
            'props' => [
                'meta' => true,
            ],
        ],

        'cbStart' => [
            'value' => "function(data) {\r\n\r\n}",
            'keys' => ['cbstart', 'cbStart'],
            'props' => [
                'meta' => true,
            ],
        ],

        'cbStop' => [
            'value' => "function(data) {\r\n\r\n}",
            'keys' => ['cbstop', 'cbStop'],
            'props' => [
                'meta' => true,
            ],
        ],

        'cbPause' => [
            'value' => "function(data) {\r\n\r\n}",
            'keys' => ['cbpause', 'cbPause'],
            'props' => [
                'meta' => true,
            ],
        ],

        'cbAnimStart' => [
            'value' => "function(data) {\r\n\r\n}",
            'keys' => ['cbanimstart', 'cbAnimStart'],
            'props' => [
                'meta' => true,
            ],
        ],

        'cbAnimStop' => [
            'value' => "function(data) {\r\n\r\n}",
            'keys' => ['cbanimstop', 'cbAnimStop'],
            'props' => [
                'meta' => true,
            ],
        ],

        'cbPrev' => [
            'value' => "function(data) {\r\n\r\n}",
            'keys' => ['cbprev', 'cbPrev'],
            'props' => [
                'meta' => true,
            ],
        ],

        'cbNext' => [
            'value' => "function(data) {\r\n\r\n}",
            'keys' => ['cbnext', 'cbNext'],
            'props' => [
                'meta' => true,
            ],
        ],
    ],

    'slides' => [
        // The background image of slides
        // Defaults to: void
        'image' => [
            'value' => '',
            'name' => ls__('Set a slide image'),
            'keys' => 'background',
            'tooltip' => ls__('The slide image/background. Click on the image to open the Image Manager to choose or upload an image.'),
            'props' => ['meta' => true],
        ],

        'imageId' => [
            'value' => '',
            'keys' => 'backgroundId',
            'props' => ['meta' => true],
        ],

        'imageSize' => [
            'value' => 'inherit',
            'name' => ls__('Size'),
            'keys' => 'bgsize',
            'tooltip' => ls__('The size of the slide background image. Leave this option on inherit if you want to set it globally from Slider Settings.'),
            'options' => [
                'inherit' => ls__('Inherit'),
                'auto' => ls__('Auto'),
                'cover' => ls__('Cover'),
                'contain' => ls__('Contain'),
                '100% 100%' => ls__('Stretch'),
            ],
        ],

        'imagePosition' => [
            'value' => 'inherit',
            'name' => ls__('Position'),
            'keys' => 'bgposition',
            'tooltip' => ls__('The position of the slide background image. Leave this option on inherit if you want to set it globally from Slider Settings.'),
            'options' => [
                'inherit' => ls__('Inherit'),
                '0% 0%' => ls__('left top'),
                '0% 50%' => ls__('left center'),
                '0% 100%' => ls__('left bottom'),
                '50% 0%' => ls__('center top'),
                '50% 50%' => ls__('center center'),
                '50% 100%' => ls__('center bottom'),
                '100% 0%' => ls__('right top'),
                '100% 50%' => ls__('right center'),
                '100% 100%' => ls__('right bottom'),
            ],
        ],

        'imageColor' => [
            'value' => '',
            'name' => ls__('Color'),
            'keys' => 'bgcolor',
            'tooltip' => ls__('The slide background color. You can use color names, hexadecimal, RGB or RGBA values.'),
        ],

        'thumbnail' => [
            'value' => '',
            'name' => ls__('Set a slide thumbnail'),
            'keys' => 'thumbnail',
            'tooltip' => ls__('The thumbnail image of this slide. Click on the image to open the Image Manager to choose or upload an image. If you leave this field empty, the slide image will be used.'),
            'props' => ['meta' => true],
        ],

        'thumbnailId' => [
            'value' => '',
            'keys' => 'thumbnailId',
            'props' => ['meta' => true],
        ],

        // Default slide delay in millisecs.
        // Defaults to: 4000 (ms) => 4secs
        'delay' => [
            'value' => '',
            'name' => ls__('Duration'),
            'keys' => ['slidedelay', 'duration'],
            'tooltip' => ls__("Here you can set the time interval between slide changes, this slide will stay visible for the time specified here. This value is in millisecs, so the value 1000 means 1 second. Please don't use 0 or very low values."),
            'attrs' => [
                'type' => 'number',
                'min' => 0,
                'step' => 500,
                'placeholder' => 'auto',
            ],
        ],

        '2dTransitions' => [
            'value' => '',
            'keys' => ['2d_transitions', 'transition2d'],
        ],

        '3dTransitions' => [
            'value' => '',
            'keys' => ['3d_transitions', 'transition3d'],
        ],

        'custom2dTransitions' => [
            'value' => '',
            'keys' => ['custom_2d_transitions', 'customtransition2d'],
        ],

        'custom3dTransitions' => [
            'value' => '',
            'keys' => ['custom_3d_transitions', 'customtransition3d'],
        ],

        'transitionOrigami' => [
            'value' => false,
            'keys' => 'transitionorigami',
            'premium' => true,
        ],

        'transitionDuration' => [
            'value' => '',
            'name' => ls__('Duration'),
            'keys' => 'transitionduration',
            'tooltip' => ls__("We've made our pre-defined slide transitions with special care to fit in most use cases. However, if you would like to increase or decrease the speed of these transitions, you can override their timing here by providing your own transition length in milliseconds. (1 second = 1000 milliseconds)"),
            'attrs' => [
                'type' => 'number',
                'min' => 0,
                'step' => 500,
                'placeholder' => ls__('custom duration'),
            ],
        ],

        'timeshift' => [
            'value' => 0,
            'name' => ls__('Time Shift'),
            'keys' => 'timeshift',
            'tooltip' => ls__('You can shift the starting point of the slide animation timeline, so layers can animate in an earlier time after a slide change. This value is in milliseconds. A second is 1000 milliseconds. You can only use a negative value.'),
            'attrs' => [
                'step' => 50,
            ],
        ],

        'linkUrl' => [
            'value' => '',
            'name' => ls__('Enter URL'),
            'keys' => ['layer_link', 'linkUrl'],
            'tooltip' => ls__('If you want to link the whole slide, type the URL here. You can choose one of the pre-defined options from the dropdown list when you click into this field. You can also type a hash mark followed by a number to link this layer to another slide. Example: #3 - this will switch to the third slide.'),
            'attrs' => [
                'data-options' => '[{
                    "name": "Switch to the next slide",
                    "value": "#next"
                }, {
                    "name": "Switch to the previous slide",
                    "value": "#prev"
                }, {
                    "name": "Stop the slideshow",
                    "value": "#stop"
                }, {
                    "name": "Resume the slideshow",
                    "value": "#start"
                }, {
                    "name": "Replay the slide from the start",
                    "value": "#replay"
                }, {
                    "name": "Reverse the slide, then pause it",
                    "value": "#reverse"
                }, {
                    "name": "Reverse the slide, then replay it",
                    "value": "#reverse-replay"
                }]',
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'linkId' => [
            'value' => '',
            'keys' => 'linkId',
            'props' => ['meta' => true],
        ],

        'linkTarget' => [
            'value' => '_self',
            'name' => ls__('Link Target'),
            'keys' => ['layer_link_target', 'linkTarget'],
            'options' => [
                '_self' => ls__('Open on the same page'),
                '_blank' => ls__('Open on new page'),
                '_parent' => ls__('Open in parent frame'),
                '_top' => ls__('Open in main frame'),
                'ls-scroll' => ls__('Scroll to element (Enter selector)'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'linkType' => [
            'value' => 'over',
            'keys' => ['layer_link_type', 'linkType'],
            'tooltip' => ls__('Choose whether the slide link should be on top or underneath your layers. The later option makes the link clickable only at empty spaces where the slide background is visible, and enables you to link both slides and layers independently from each other.'),
            'options' => [
                'over' => ls__('On top of layers'),
                'under' => ls__('Underneath layers'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'ID' => [
            'value' => '',
            'name' => ls__('#ID'),
            'keys' => 'id',
            'tooltip' => ls__('You can apply an ID attribute on the HTML element of this slide to work with it in your custom CSS or Javascript code.'),
            'props' => [
                'meta' => true,
            ],
        ],

        'deeplink' => [
            'value' => '',
            'name' => ls__('Deeplink'),
            'keys' => 'deeplink',
            'tooltip' => ls__('You can specify a slide alias name which you can use in your URLs with a hash mark, so Creative Slider will start with the correspondig slide.'),
        ],

        'globalHover' => [
            'value' => false,
            'name' => ls__('Global Hover'),
            'keys' => 'globalhover',
            'tooltip' => ls__('By turning this option on, all layers will trigger their Hover Transitions at the same time when you hover over the slider with your mouse cursor. It’s useful to create spectacular effects that involve multiple layer transitions and activate on hovering over the slider instead of individual layers.'),
            'premium' => true,
        ],

        'postContent' => [
            'value' => null,
            'keys' => 'post_content',
            'props' => [
                'meta' => true,
            ],
        ],

        'postOffset' => [
            'value' => '',
            'keys' => 'post_offset',
            'props' => [
                'meta' => true,
            ],
        ],

        'skipSlide' => [
            'value' => false,
            'name' => ls__('Hidden'),
            'keys' => 'skip',
            'tooltip' => ls__("If you don't want to use this slide in your front-page, but you want to keep it, you can hide it with this switch."),
            'props' => [
                'meta' => true,
            ],
        ],

        'overflow' => [
            'value' => false,
            'name' => ls__('Overflow layers'),
            'keys' => 'overflow',
            'tooltip' => ls__('By default the slider clips the layers outside of its bounds. Enable this option to allow overflowing content.'),
        ],

        'scheduleStart' => [
            'value' => '',
            'name' => ls__('Start on'),
            'keys' => 'schedule_start',
            'desc' => ls__("Scheduled slide will only be visible to your visitors between the time period you set here.<br>We're using international date and time format to avoid ambiguity."),
            'attrs' => [
                'placeholder' => ls__('No schedule'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'scheduleEnd' => [
            'value' => '',
            'name' => ls__('Stop on'),
            'keys' => 'schedule_end',
            'desc' => 'Clear the text field above and left it empty if you want to cancel the schedule.',
            'attrs' => [
                'placeholder' => ls__('No schedule'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'title' => [
            'value' => '',
            'name' => ls__('Title'),
            'keys' => 'title',
            'props' => ['meta' => true],
        ],

        'alt' => [
            'value' => '',
            'name' => ls__('Alt'),
            'keys' => 'alt',
            'tooltip' => ls__('Name or describe your slide image, so search engines and VoiceOver softwares can properly identify it.'),
            'props' => ['meta' => true],
        ],

        // Ken Burns effect
        'kenBurnsZoom' => [
            'value' => 'disabled',
            'name' => ls__('Zoom'),
            'keys' => 'kenburnszoom',
            'options' => [
                'disabled' => ls__('Disabled'),
                'in' => ls__('Zoom In'),
                'out' => ls__('Zoom Out'),
            ],
        ],

        'kenBurnsRotate' => [
            'value' => '',
            'name' => ls__('Rotate'),
            'keys' => 'kenburnsrotate',
            'tooltip' => ls__('The amount of rotation (if any) in degrees used in the Ken Burns effect. Negative values are allowed for counterclockwise rotation.'),
        ],

        'kenBurnsScale' => [
            'value' => 1.2,
            'name' => ls__('Scale'),
            'keys' => 'kenburnsscale',
            'tooltip' => ls__('Increase or decrease the size of the slide background image in the Ken Burns effect. The default value is 1, the value 2 will double the image, while 0.5 results half the size. Negative values will flip the image.'),
            'attrs' => [
                'type' => 'number',
                'step' => 0.1,
            ],
            'props' => [
                'output' => true,
            ],
        ],

        // Parallax
        'parallaxType' => [
            'value' => '2d',
            'name' => ls__('Type'),
            'keys' => 'parallaxtype',
            'tooltip' => ls__('The default value for parallax layers on this slide, which they will inherit, unless you set it otherwise on the affected layers.'),
            'options' => [
                '2d' => ls__('2D'),
                '3d' => ls__('3D'),
            ],
        ],

        'parallaxEvent' => [
            'value' => 'cursor',
            'name' => ls__('Event'),
            'keys' => 'parallaxevent',
            'tooltip' => ls__('You can trigger the parallax effect by either scrolling the page, or by moving your mouse cursor / tilting your mobile device. This is the default value on this slide, which parallax layers will inherit, unless you set it otherwise directly on them.'),
            'options' => [
                'cursor' => ls__('Cursor or Tilt'),
                'scroll' => ls__('Scroll'),
            ],
        ],

        'parallaxAxis' => [
            'value' => 'both',
            'name' => ls__('Axes'),
            'keys' => 'parallaxaxis',
            'tooltip' => ls__('Choose on which axes parallax layers should move. This is the default value on this slide, which parallax layers will inherit, unless you set it otherwise directly on them.'),
            'options' => [
                'none' => ls__('None'),
                'both' => ls__('Both axes'),
                'x' => ls__('Horizontal only'),
                'y' => ls__('Vertical only'),
            ],
        ],

        'parallaxTransformOrigin' => [
            'value' => '50% 50% 0',
            'name' => ls__('Transform Origin'),
            'keys' => 'parallaxtransformorigin',
            'tooltip' => ls__('Sets a point on canvas from which transformations are calculated. For example, a layer may rotate around its center axis or a completely custom point, such as one of its corners. The three values represent the X, Y and Z axes in 3D space. Apart from the pixel and percentage values, you can also use the following constants: top, right, bottom, left, center.'),
        ],

        'parallaxDurationMove' => [
            'value' => 1500,
            'name' => ls__('Move duration'),
            'keys' => 'parallaxdurationmove',
            'tooltip' => ls__('Controls the speed of animating layers when you move your mouse cursor or tilt your mobile device. This is the default value on this slide, which parallax layers will inherit, unless you set it otherwise directly on them.'),
            'attrs' => [
                'type' => 'number',
                'step' => 100,
                'min' => 0,
            ],
        ],

        'parallaxDurationLeave' => [
            'value' => 1200,
            'name' => ls__('Leave duration'),
            'keys' => 'parallaxdurationleave',
            'tooltip' => ls__('Controls how quickly your layers revert to their original position when you move your mouse cursor outside of a parallax slider. This value is in milliseconds. 1 second = 1000 milliseconds. This is the default value on this slide, which parallax layers will inherit, unless you set it otherwise directly on them.'),
            'attrs' => [
                'type' => 'number',
                'step' => 100,
                'min' => 0,
            ],
        ],

        'parallaxDistance' => [
            'value' => 10,
            'name' => ls__('Distance'),
            'keys' => 'parallaxdistance',
            'tooltip' => ls__('Increase or decrease the amount of layer movement when moving your mouse cursor or tilting on a mobile device. This is the default value on this slide, which parallax layers will inherit, unless you set it otherwise directly on them.'),
            'attrs' => [
                'type' => 'number',
                'step' => 1,
            ],
        ],

        'parallaxRotate' => [
            'value' => 10,
            'name' => ls__('Rotation'),
            'keys' => 'parallaxrotate',
            'tooltip' => ls__('Increase or decrease the amount of layer rotation in the 3D space when moving your mouse cursor or tilting on a mobile device. This is the default value on this slide, which parallax layers will inherit, unless you set it otherwise directly on them.'),
            'attrs' => [
                'type' => 'number',
                'step' => 1,
            ],
        ],

        'parallaxPerspective' => [
            'value' => 500,
            'name' => ls__('Perspective'),
            'keys' => 'parallaxtransformperspective',
            'tooltip' => ls__('Changes the perspective of layers in the 3D space. This is the default value on this slide, which parallax layers will inherit, unless you set it otherwise directly on them.'),
            'attrs' => [
                'type' => 'number',
                'step' => 100,
            ],
        ],

        // 'filterFrom' => array(
        //     'value' => '',
        //     'name' => ls__('Filter From'),
        //     'keys' => 'filterfrom',
        //     'tooltip' => ls__('Filters provide effects like blurring or color shifting your layers. Click into the text field to see a selection of filters you can use. Although clicking on the pre-defined options will reset the text field, you can apply multiple filters simply by providing a space separated list of all the filters you would like to use.'),
        //     'advanced' => true,
        //     'attrs' => array(
        //         'data-options' => '[{
        //             "name": "Blur",
        //             "value": "blur(5px)"
        //         }, {
        //             "name": "Brightness",
        //             "value": "brightness(40%)"
        //         }, {
        //             "name": "Contrast",
        //             "value": "contrast(200%)"
        //         }, {
        //             "name": "Grayscale",
        //             "value": "grayscale(50%)"
        //         }, {
        //             "name": "Hue-rotate",
        //             "value": "hue-rotate(90deg)"
        //         }, {
        //             "name": "Invert",
        //             "value": "invert(75%)"
        //         }, {

        //             "name": "Saturate",
        //             "value": "saturate(30%)"
        //         }, {
        //             "name": "Sepia",
        //             "value": "sepia(60%)"
        //         }]'
        //     )
        // ),

        // 'filterTo' => array(
        //     'value' => '',
        //     'name' => ls__('Filter To'),
        //     'keys' => 'filterto',
        //     'tooltip' => ls__('Filters provide effects like blurring or color shifting your layers. Click into the text field to see a selection of filters you can use. Although clicking on the pre-defined options will reset the text field, you can apply multiple filters simply by providing a space separated list of all the filters you would like to use.'),
        //     'advanced' => true,
        //     'attrs' => array(
        //         'data-options' => '[{
        //             "name": "Blur",
        //             "value": "blur(5px)"
        //         }, {
        //             "name": "Brightness",
        //             "value": "brightness(40%)"
        //         }, {
        //             "name": "Contrast",
        //             "value": "contrast(200%)"
        //         }, {
        //             "name": "Grayscale",
        //             "value": "grayscale(50%)"
        //         }, {
        //             "name": "Hue-rotate",
        //             "value": "hue-rotate(90deg)"
        //         }, {
        //             "name": "Invert",
        //             "value": "invert(75%)"
        //         }, {

        //             "name": "Saturate",
        //             "value": "saturate(30%)"
        //         }, {
        //             "name": "Sepia",
        //             "value": "sepia(60%)"
        //         }]'
        //     )
        // )
    ],

    'layers' => [
        // ======================= //
        // |  Content  | //
        // ======================= //

        'uuid' => [
            'value' => '',
            'keys' => 'uuid',
            'props' => [
                'meta' => true,
            ],
        ],

        'type' => [
            'value' => '',
            'keys' => 'type',
            'props' => [
                'meta' => true,
            ],
        ],

        'hide_on_desktop' => [
            'value' => false,
            'keys' => 'hide_on_desktop',
            'props' => [
                'meta' => true,
            ],
        ],

        'hide_on_tablet' => [
            'value' => false,
            'keys' => 'hide_on_tablet',
            'props' => [
                'meta' => true,
            ],
        ],

        'hide_on_phone' => [
            'value' => false,
            'keys' => 'hide_on_phone',
            'props' => [
                'meta' => true,
            ],
        ],

        'media' => [
            'value' => '',
            'keys' => 'media',
            'props' => [
                'meta' => true,
            ],
        ],

        'image' => [
            'value' => '',
            'keys' => 'image',
            'props' => [
                'meta' => true,
            ],
        ],

        'imageId' => [
            'value' => '',
            'keys' => 'imageId',
            'props' => ['meta' => true],
        ],

        'html' => [
            'value' => '',
            'keys' => 'html',
            'props' => [
                'meta' => true,
            ],
        ],

        'mediaAutoPlay' => [
            'value' => 'inherit',
            'name' => ls__('Autoplay'),
            'keys' => 'autoplay',
            'options' => [
                'inherit' => ls__('Inherit'),
                'enabled' => ls__('Enabled'),
                'disabled' => ls__('Disabled'),
            ],
        ],

        'mediaInfo' => [
            'value' => true,
            'name' => ls__('Show Info'),
            'keys' => 'showinfo',
            'options' => [
                'auto' => ls__('Auto'),
                'enabled' => ls__('Enabled'),
                'disabled' => ls__('Disabled'),
            ],
        ],

        'mediaControls' => [
            'value' => true,
            'name' => ls__('Controls'),
            'keys' => 'controls',
            'options' => [
                'auto' => ls__('Auto'),
                'enabled' => ls__('Enabled'),
                'disabled' => ls__('Disabled'),
            ],
        ],

        'mediaPoster' => [
            'value' => '',
            'keys' => 'poster',
        ],

        'mediaFillMode' => [
            'value' => 'cover',
            'name' => ls__('Fill mode'),
            'keys' => 'fillmode',
            'options' => [
                'contain' => ls__('Contain'),
                'cover' => ls__('Cover'),
            ],
        ],

        'mediaVolume' => [
            'value' => '',
            'name' => ls__('Volume'),
            'keys' => 'volume',
            'attrs' => [
                'type' => 'number',
                'min' => 0,
                'max' => 100,
                'placeholder' => 'auto',
            ],
        ],

        'mediaBackgroundVideo' => [
            'value' => false,
            'name' => ls__('Use this video as slide background'),
            'keys' => 'backgroundvideo',
            'tooltip' => ls__('Forces this layer to act like the slide background by covering the whole slider and ignoring some transitions. Please make sure to provide your own poster image with the option above, so the slider can display it immediately on page load.'),
        ],

        'mediaOverlay' => [
            'value' => 'disabled',
            'name' => ls__('Choose an overlay image:'),
            'keys' => 'overlay',
            'tooltip' => ls__('Cover your videos with an overlay image to have dotted or striped effects on them.'),
        ],

        'postTextLength' => [
            'value' => '',
            'keys' => 'post_text_length',
            'props' => [
                'meta' => true,
            ],
        ],

        // ======================= //
        // |  Animation options  | //
        // ======================= //
        'transition' => ['value' => '', 'keys' => 'transition', 'props' => ['meta' => true]],

        'transitionIn' => [
            'value' => true,
            'keys' => 'transitionin',
        ],

        'transitionInOffsetX' => [
            'value' => '0',
            'name' => ls__('OffsetX'),
            'keys' => 'offsetxin',
            'tooltip' => ls__("Shifts the layer starting position from its original on the horizontal axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the width of this layer. The values 'left' or 'right' position the layer out the staging area, so it enters the scene from either side when animating to its destination location."),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Enter the stage from left",
                "value": "left"
            }, {
                "name": "Enter the stage from right",
                "value": "right"
            }, {
                "name": "100% layer width",
                "value": "100lw"
            }, {
                "name": "-100% layer width",
                "value": "-100lw"
            }, {
                "name": "50% slider width",
                "value": "50sw"
            }, {
                "name": "-50% slider width",
                "value": "-50sw"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'transitionInOffsetY' => [
            'value' => '0',
            'name' => ls__('OffsetY'),
            'keys' => 'offsetyin',
            'tooltip' => ls__("Shifts the layer starting position from its original on the vertical axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the height of this layer. The values 'top' or 'bottom' position the layer out the staging area, so it enters the scene from either vertical side when animating to its destination location."),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Enter the stage from top",
                "value": "top"
            }, {
                "name": "Enter the stage from bottom",
                "value": "bottom"
            }, {
                "name": "100% layer height",
                "value": "100lh"
            }, {
                "name": "-100% layer height",
                "value": "-100lh"
            }, {
                "name": "50% slider height",
                "value": "50sh"
            }, {
                "name": "-50% slider height",
                "value": "-50sh"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        // Duration of the transition in millisecs when a layer animates in.
        // Original: durationin
        // Defaults to: 1000 (ms) => 1sec
        'transitionInDuration' => [
            'value' => 1000,
            'name' => ls__('Duration'),
            'keys' => 'durationin',
            'tooltip' => ls__('The length of the transition in milliseconds when the layer enters the scene. A second equals to 1000 milliseconds.'),
            'attrs' => ['min' => 0, 'step' => 50],
        ],

        // Delay before the transition in millisecs when a layer animates in.
        // Original: delayin
        // Defaults to: 0 (ms)
        'transitionInDelay' => [
            'value' => 0,
            'name' => ls__('Start at'),
            'keys' => 'delayin',
            'tooltip' => ls__('Delays the transition with the given amount of milliseconds before the layer enters the scene. A second equals to 1000 milliseconds.'),
            'attrs' => ['min' => 0, 'step' => 50],
        ],

        // Easing of the transition when a layer animates in.
        // Original: easingin
        // Defaults to: 'easeInOutQuint'
        'transitionInEasing' => [
            'value' => 'easeInOutQuint',
            'name' => ls__('Easing'),
            'keys' => 'easingin',
            'tooltip' => ls__('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'),
        ],

        'transitionInFade' => [
            'value' => true,
            'name' => ls__('Fade'),
            'keys' => 'fadein',
            'tooltip' => ls__('Fade the layer during the transition.'),
        ],

        // Initial rotation degrees when a layer animates in.
        // Original: rotatein
        // Defaults to: 0 (deg)
        'transitionInRotate' => [
            'value' => 0,
            'name' => ls__('Rotate'),
            'keys' => 'rotatein',
            'tooltip' => ls__('Rotates the layer by the given number of degrees. Negative values are allowed for counterclockwise rotation.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionInRotateX' => [
            'value' => 0,
            'name' => ls__('RotateX'),
            'keys' => 'rotatexin',
            'tooltip' => ls__('Rotates the layer along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionInRotateY' => [
            'value' => 0,
            'name' => ls__('RotateY'),
            'keys' => 'rotateyin',
            'tooltip' => ls__('Rotates the layer along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionInSkewX' => [
            'value' => 0,
            'name' => ls__('SkewX'),
            'keys' => 'skewxin',
            'tooltip' => ls__('Skews the layer along the X (horizontal) by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionInSkewY' => [
            'value' => 0,
            'name' => ls__('SkewY'),
            'keys' => 'skewyin',
            'tooltip' => ls__('Skews the layer along the Y (vertical) by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionInScaleX' => [
            'value' => 1,
            'name' => ls__('ScaleX'),
            'keys' => 'scalexin',
            'tooltip' => ls__('Scales the layer along the X (horizontal) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks the layer compared to its original size.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'transitionInScaleY' => [
            'value' => 1,
            'name' => ls__('ScaleY'),
            'keys' => 'scaleyin',
            'tooltip' => ls__('Scales the layer along the Y (vertical) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks the layer compared to its original size.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'transitionInTransformOrigin' => [
            'value' => '50% 50% 0',
            'name' => ls__('Transform Origin'),
            'keys' => 'transformoriginin',
            'tooltip' => ls__('Sets a point on canvas from which transformations are calculated. For example, a layer may rotate around its center axis or a completely custom point, such as one of its corners. The three values represent the X, Y and Z axes in 3D space. Apart from the pixel and percentage values, you can also use the following constants: top, right, bottom, left, center, slidercenter, slidermiddle, slidertop, sliderright, sliderbottom, sliderleft.'),
        ],

        'transitionInClip' => [
            'value' => '',
            'name' => ls__('Mask'),
            'keys' => 'clipin',
            'tooltip' => ls__('Clips (cuts off) the sides of the layer by the given amount specified in pixels or percentages. The 4 value in order: top, right, bottom and the left side of the layer.'),
            'attrs' => ['data-options' => '[{
                "name": "From top",
                "value": "0 0 100% 0"
            }, {
                "name": "From right",
                "value": "0 0 0 100%"
            }, {
                "name": "From bottom",
                "value": "100% 0 0 0"
            }, {
                "name": "From left",
                "value": "0 100% 0 0"
            }]'],
        ],

        'transitionInBGColor' => [
            'value' => '',
            'name' => ls__('Background'),
            'keys' => 'bgcolorin',
            'tooltip' => ls__("The background color of your layer. You can use color names, hexadecimal, RGB or RGBA values as well as the 'transparent' keyword. Example: #FFF"),
        ],

        'transitionInColor' => [
            'value' => '',
            'name' => ls__('Color'),
            'keys' => 'colorin',
            'tooltip' => ls__('The color of your text. You can use color names, hexadecimal, RGB or RGBA values. Example: #333'),
        ],

        'transitionInRadius' => [
            'value' => '',
            'name' => ls__('Rounded Corners'),
            'keys' => 'radiusin',
            'tooltip' => ls__('If you want rounded corners, you can set its radius here in pixels. Example: 5px'),
        ],

        'transitionInWidth' => [
            'value' => '',
            'name' => ls__('Width'),
            'keys' => 'widthin',
            'tooltip' => ls__('The initial width of this layer from which it will be animated to its proper size during the transition.'),
        ],

        'transitionInHeight' => [
            'value' => '',
            'name' => ls__('Height'),
            'keys' => 'heightin',
            'tooltip' => ls__('The initial height of this layer from which it will be animated to its proper size during the transition.'),
        ],

        'transitionInFilter' => [
            'value' => '',
            'name' => ls__('Filter'),
            'keys' => 'filterin',
            'tooltip' => ls__('Filters provide effects like blurring or color shifting your layers. Click into the text field to see a selection of filters you can use. Although clicking on the pre-defined options will reset the text field, you can apply multiple filters simply by providing a space separated list of all the filters you would like to use. Click on the "Filter" link for more information.'),
            'premium' => true,
            'attrs' => [
                'data-options' => '[{
                    "name": "Blur",
                    "value": "blur(5px)"
                }, {
                    "name": "Brightness",
                    "value": "brightness(40%)"
                }, {
                    "name": "Contrast",
                    "value": "contrast(200%)"
                }, {
                    "name": "Grayscale",
                    "value": "grayscale(50%)"
                }, {
                    "name": "Hue-rotate",
                    "value": "hue-rotate(90deg)"
                }, {
                    "name": "Invert",
                    "value": "invert(75%)"
                }, {
                    "name": "Saturate",
                    "value": "saturate(30%)"
                }, {
                    "name": "Sepia",
                    "value": "sepia(60%)"
                }]',
            ],
        ],

        'transitionInPerspective' => [
            'value' => '500',
            'name' => ls__('Perspective'),
            'keys' => 'transformperspectivein',
            'tooltip' => ls__('Changes the perspective of this layer in the 3D space.'),
        ],

        // ======

        'transitionOut' => [
            'value' => true,
            'keys' => 'transitionout',
        ],

        'transitionOutOffsetX' => [
            'value' => 0,
            'name' => ls__('OffsetX'),
            'keys' => 'offsetxout',
            'tooltip' => ls__("Shifts the layer from its original position on the horizontal axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the width of this layer. The values 'left' or 'right' animate the layer out the staging area, so it can leave the scene on either side."),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Leave the stage on left",
                "value": "left"
            }, {
                "name": "Leave the stage on right",
                "value": "right"
            }, {
                "name": "100% layer width",
                "value": "100lw"
            }, {
                "name": "-100% layer width",
                "value": "-100lw"
            }, {
                "name": "50% slider width",
                "value": "50sw"
            }, {
                "name": "-50% slider width",
                "value": "-50sw"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'transitionOutOffsetY' => [
            'value' => 0,
            'name' => ls__('OffsetY'),
            'keys' => 'offsetyout',
            'tooltip' => ls__("Shifts the layer from its original position on the vertical axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the height of this layer. The values 'top' or 'bottom' animate the layer out the staging area, so it can leave the scene on either vertical side."),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Leave the stage on top",
                "value": "top"
            }, {
                "name": "Leave the stage on bottom",
                "value": "bottom"
            }, {
                "name": "100% layer height",
                "value": "100lh"
            }, {
                "name": "-100% layer height",
                "value": "-100lh"
            }, {
                "name": "50% slider height",
                "value": "50sh"
            }, {
                "name": "-50% slider height",
                "value": "-50sh"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        // Duration of the transition in millisecs when a layer animates out.
        // Original: durationout
        // Defaults to: 1000 (ms) => 1sec
        'transitionOutDuration' => [
            'value' => 1000,
            'name' => ls__('Duration'),
            'keys' => 'durationout',
            'tooltip' => ls__('The length of the transition in milliseconds when the layer leaves the slide. A second equals to 1000 milliseconds.'),
            'attrs' => ['min' => 0, 'step' => 50],
        ],

        'showUntil' => [
            'value' => '0',
            'keys' => 'showuntil',
        ],

        'transitionOutStartAt' => [
            'value' => 'slidechangeonly',
            'name' => ls__('Start at'),
            'keys' => 'startatout',
            'tooltip' => ls__('You can set the starting time of this transition. Use one of the pre-defined options to use relative timing, which can be shifted with custom operations.'),
            'attrs' => ['type' => 'hidden'],
        ],

        'transitionOutStartAtTiming' => [
            'value' => 'slidechangeonly',
            'keys' => 'startatouttiming',
            'props' => ['meta' => true],
            'options' => [
                'slidechangeonly' => ls__('Slide change starts (ignoring modifier)'),
                'transitioninend' => ls__('Opening Transition completes'),
                'textinstart' => ls__('Opening Text Transition starts'),
                'textinend' => ls__('Opening Text Transition completes'),
                'allinend' => ls__('Opening and Opening Text Transition complete'),
                'loopstart' => ls__('Loop starts'),
                'loopend' => ls__('Loop completes'),
                'transitioninandloopend' => ls__('Opening and Loop Transitions complete'),
                'textinandloopend' => ls__('Opening Text and Loop Transitions complete'),
                'allinandloopend' => ls__('Opening, Opening Text and Loop Transitions complete'),
                'textoutstart' => ls__('Ending Text Transition starts'),
                'textoutend' => ls__('Ending Text Transition completes'),
                'textoutandloopend' => ls__('Ending Text and Loop Transitions complete'),
            ],
        ],

        'transitionOutStartAtOperator' => [
            'value' => '+',
            'keys' => 'startatoutoperator',
            'props' => ['meta' => true],
            'options' => ['+', '-', '/', '*'],
        ],

        'transitionOutStartAtValue' => [
            'value' => 0,
            'keys' => 'startatoutvalue',
            'props' => ['meta' => true],
        ],

        // Easing of the transition when a layer animates out.
        // Original: easingout
        // Defaults to: 'easeInOutQuint'
        'transitionOutEasing' => [
            'value' => 'easeInOutQuint',
            'name' => ls__('Easing'),
            'keys' => 'easingout',
            'tooltip' => ls__('The timing function of the animation. With this function you can manipulate the movement of the animated object. Please click on the link next to this select field to open easings.net for more information and real-time examples.'),
        ],

        'transitionOutFade' => [
            'value' => true,
            'name' => ls__('Fade'),
            'keys' => 'fadeout',
            'tooltip' => ls__('Fade the layer during the transition.'),
        ],

        // Initial rotation degrees when a layer animates out.
        // Original: rotateout
        // Defaults to: 0 (deg)
        'transitionOutRotate' => [
            'value' => 0,
            'name' => ls__('Rotate'),
            'keys' => 'rotateout',
            'tooltip' => ls__('Rotates the layer by the given number of degrees. Negative values are allowed for counterclockwise rotation.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionOutRotateX' => [
            'value' => 0,
            'name' => ls__('RotateX'),
            'keys' => 'rotatexout',
            'tooltip' => ls__('Rotates the layer along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionOutRotateY' => [
            'value' => 0,
            'name' => ls__('RotateY'),
            'keys' => 'rotateyout',
            'tooltip' => ls__('Rotates the layer along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionOutSkewX' => [
            'value' => 0,
            'name' => ls__('SkewX'),
            'keys' => 'skewxout',
            'tooltip' => ls__('Skews the layer along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionOutSkewY' => [
            'value' => 0,
            'name' => ls__('SkewY'),
            'keys' => 'skewyout',
            'tooltip' => ls__('Skews the layer along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'transitionOutScaleX' => [
            'value' => 1,
            'name' => ls__('ScaleX'),
            'keys' => 'scalexout',
            'tooltip' => ls__('Scales the layer along the X (horizontal) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks the layer compared to its original size.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'transitionOutScaleY' => [
            'value' => 1,
            'name' => ls__('ScaleY'),
            'keys' => 'scaleyout',
            'tooltip' => ls__('Scales the layer along the Y (vertical) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks the layer compared to its original size.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'transitionOutTransformOrigin' => [
            'value' => '50% 50% 0',
            'name' => ls__('Transform Origin'),
            'keys' => 'transformoriginout',
            'tooltip' => ls__('Sets a point on canvas from which transformations are calculated. For example, a layer may rotate around its center axis or a completely custom point, such as one of its corners. The three values represent the X, Y and Z axes in 3D space. Apart from the pixel and percentage values, you can also use the following constants: top, right, bottom, left, center, slidercenter, slidermiddle, slidertop, sliderright, sliderbottom, sliderleft.'),
        ],

        'transitionOutClip' => [
            'value' => '',
            'name' => ls__('Mask'),
            'keys' => 'clipout',
            'tooltip' => ls__('Clips (cuts off) the sides of the layer by the given amount specified in pixels or percentages. The 4 value in order: top, right, bottom and the left side of the layer.'),
            'attrs' => ['data-options' => '[{
                "name": "From top",
                "value": "0 0 100% 0"
            }, {
                "name": "From right",
                "value": "0 0 0 100%"
            }, {
                "name": "From bottom",
                "value": "100% 0 0 0"
            }, {
                "name": "From left",
                "value": "0 100% 0 0"
            }]'],
        ],

        'transitionOutFilter' => [
            'value' => '',
            'name' => ls__('Filter'),
            'keys' => 'filterout',
            'tooltip' => ls__('Filters provide effects like blurring or color shifting your layers. Click into the text field to see a selection of filters you can use. Although clicking on the pre-defined options will reset the text field, you can apply multiple filters simply by providing a space separated list of all the filters you would like to use. Click on the "Filter" link for more information.'),
            'premium' => true,
            'attrs' => [
                'data-options' => '[{
                    "name": "Blur",
                    "value": "blur(5px)"
                }, {
                    "name": "Brightness",
                    "value": "brightness(40%)"
                }, {
                    "name": "Contrast",
                    "value": "contrast(200%)"
                }, {
                    "name": "Grayscale",
                    "value": "grayscale(50%)"
                }, {
                    "name": "Hue-rotate",
                    "value": "hue-rotate(90deg)"
                }, {
                    "name": "Invert",
                    "value": "invert(75%)"
                }, {
                    "name": "Saturate",
                    "value": "saturate(30%)"
                }, {
                    "name": "Sepia",
                    "value": "sepia(60%)"
                }]',
            ],
        ],

        'transitionOutPerspective' => [
            'value' => '500',
            'name' => ls__('Perspective'),
            'keys' => 'transformperspectiveout',
            'tooltip' => ls__('Changes the perspective of this layer in the 3D space.'),
        ],

        // -----

        'skipLayer' => [
            'value' => false,
            'name' => ls__('Hidden'),
            'keys' => 'skip',
            'tooltip' => ls__("If you don't want to use this layer, but you want to keep it, you can hide it with this switch."),
            'props' => [
                'meta' => true,
            ],
        ],

        'transitionOutBGColor' => [
            'value' => '',
            'name' => ls__('Background'),
            'keys' => 'bgcolorout',
            'tooltip' => ls__('Animates the background toward the color you specify here when the layer leaves the slider canvas.'),
        ],

        'transitionOutColor' => [
            'value' => '',
            'name' => ls__('Color'),
            'keys' => 'colorout',
            'tooltip' => ls__('Animates the text color toward the color you specify here when the layer leaves the slider canvas.'),
        ],

        'transitionOutRadius' => [
            'value' => '',
            'name' => ls__('Rounded Corners'),
            'keys' => 'radiusout',
            'tooltip' => ls__('Animates rounded corners toward the value you specify here when the layer leaves the slider canvas.'),
        ],

        'transitionOutWidth' => [
            'value' => '',
            'name' => ls__('Width'),
            'keys' => 'widthout',
            'tooltip' => ls__('Animates the layer width toward the value you specify here when the layer leaves the slider canvas.'),
        ],

        'transitionOutHeight' => [
            'value' => '',
            'name' => ls__('Height'),
            'keys' => 'heightout',
            'tooltip' => ls__('Animates the layer height toward the value you specify here when the layer leaves the slider canvas.'),
        ],

        // == Compatibility ==
        'transitionInType' => [
            'value' => 'auto',
            'keys' => 'slidedirection',
        ],
        'transitionOutType' => [
            'value' => 'auto',
            'keys' => 'slideoutdirection',
        ],

        'transitionOutDelay' => [
            'value' => 0,
            'keys' => 'delayout',
        ],

        'transitionInScale' => [
            'value' => '1.0',
            'keys' => 'scalein',
        ],

        'transitionOutScale' => [
            'value' => '1.0',
            'keys' => 'scaleout',
        ],

        // Text Animation IN
        // -----------------

        'textTransitionIn' => [
            'value' => false,
            'keys' => 'texttransitionin',
        ],

        'textTypeIn' => [
            'value' => 'chars_asc',
            'name' => ls__('Text Animation'),
            'keys' => 'texttypein',
            'tooltip' => ls__('Select how your text should be split and animated.'),
            'options' => [
                'chars_asc' => ls__('by chars ascending'),
                'chars_desc' => ls__('by chars descending'),
                'chars_rand' => ls__('by chars random'),
                'chars_center' => ls__('by chars center to edge'),
                'chars_edge' => ls__('by chars edge to center'),
                'words_asc' => ls__('by words ascending'),
                'words_desc' => ls__('by words descending'),
                'words_rand' => ls__('by words random'),
                'words_center' => ls__('by words center to edge'),
                'words_edge' => ls__('by words edge to center'),
                'lines_asc' => ls__('by lines ascending'),
                'lines_desc' => ls__('by lines descending'),
                'lines_rand' => ls__('by lines random'),
                'lines_center' => ls__('by lines center to edge'),
                'lines_edge' => ls__('by lines edge to center'),
            ],
            'props' => [
                'output' => true,
            ],
        ],

        'textShiftIn' => [
            'value' => 50,
            'name' => ls__('Shift In'),
            'tooltip' => ls__('Delays the transition of each text nodes relative to each other. A second equals to 1000 milliseconds.'),
            'keys' => 'textshiftin',
            'attrs' => ['type' => 'number'],
        ],

        'textOffsetXIn' => [
            'value' => 0,
            'name' => ls__('OffsetX'),
            'tooltip' => ls__("Shifts the starting position of text nodes from their original on the horizontal axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the width of this layer. The values 'left' or 'right' position text nodes out the staging area, so they enter the scene from either side when animating to their destination location. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values."),
            'keys' => 'textoffsetxin',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Enter the stage from left",
                "value": "left"
            }, {
                "name": "Enter the stage from right",
                "value": "right"
            }, {
                "name": "100% layer width",
                "value": "100lw"
            }, {
                "name": "-100% layer width",
                "value": "-100lw"
            }, {
                "name": "50% slider width",
                "value": "50sw"
            }, {
                "name": "-50% slider width",
                "value": "-50sw"
            }, {
                "name": "Cycle between values",
                "value": "50|-50"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'textOffsetYIn' => [
            'value' => 0,
            'name' => ls__('OffsetY'),
            'tooltip' => ls__("Shifts the starting position of text nodes from their original on the vertical axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the width of this layer. The values 'top' or 'bottom' position text nodes out the staging area, so they enter the scene from either vertical side when animating to their destination location. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values."),
            'keys' => 'textoffsetyin',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Enter the stage from top",
                "value": "top"
            }, {
                "name": "Enter the stage from bottom",
                "value": "bottom"
            }, {
                "name": "100% layer height",
                "value": "100lh"
            }, {
                "name": "-100% layer height",
                "value": "-100lh"
            }, {
                "name": "50% slider height",
                "value": "50sh"
            }, {
                "name": "-50% slider height",
                "value": "-50sh"
            }, {
                "name": "Cycle between values",
                "value": "50|-50"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'textDurationIn' => [
            'value' => 1000,
            'name' => ls__('Duration'),
            'tooltip' => ls__('The transition length in milliseconds of the individual text fragments. A second equals to 1000 milliseconds.'),
            'keys' => 'textdurationin',
            'attrs' => ['min' => 0, 'step' => 50],
        ],

        'textEasingIn' => [
            'value' => 'easeInOutQuint',
            'name' => ls__('Easing'),
            'tooltip' => ls__('The timing function of the animation. With this function you can manipulate the movement of animated text fragments. Please click on the link next to this select field to open easings.net for more information and real-time examples.'),
            'keys' => 'texteasingin',
        ],

        'textFadeIn' => [
            'value' => true,
            'name' => ls__('Fade'),
            'tooltip' => ls__('Fade the text fragments during their transition.'),
            'keys' => 'textfadein',
        ],

        'textStartAtIn' => [
            'value' => 'transitioninend',
            'name' => ls__('StartAt'),
            'tooltip' => ls__('You can set the starting time of this transition. Use one of the pre-defined options to use relative timing, which can be shifted with custom operations.'),
            'keys' => 'textstartatin',
            'attrs' => ['type' => 'hidden'],
        ],

        'textStartAtInTiming' => [
            'value' => 'transitioninend',
            'keys' => 'textstartatintiming',
            'props' => ['meta' => true],
            'options' => [
                'transitioninstart' => ls__('Opening Transition starts'),
                'transitioninend' => ls__('Opening Transition completes'),
                'loopstart' => ls__('Loop starts'),
                'loopend' => ls__('Loop completes'),
                'transitioninandloopend' => ls__('Opening and Loop Transitions complete'),
            ],
        ],

        'textStartAtInOperator' => [
            'value' => '+',
            'keys' => 'textstartatinoperator',
            'props' => ['meta' => true],
            'options' => ['+', '-', '/', '*'],
        ],

        'textStartAtInValue' => [
            'value' => 0,
            'keys' => 'textstartatinvalue',
            'props' => ['meta' => true],
        ],

        'textRotateIn' => [
            'value' => 0,
            'name' => ls__('Rotate'),
            'tooltip' => ls__('Rotates text fragments clockwise by the given number of degrees. Negative values are allowed for counterclockwise rotation. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textrotatein',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textRotateXIn' => [
            'value' => 0,
            'name' => ls__('RotateX'),
            'tooltip' => ls__('Rotates text fragments along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textrotatexin',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textRotateYIn' => [
            'value' => 0,
            'name' => ls__('RotateY'),
            'tooltip' => ls__('Rotates text fragments along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textrotateyin',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textScaleXIn' => [
            'value' => 1,
            'name' => ls__('ScaleX'),
            'keys' => 'textscalexin',
            'tooltip' => ls__('Scales text fragments along the X (horizontal) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks text fragments compared to their original size. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'textScaleYIn' => [
            'value' => 1,
            'name' => ls__('ScaleY'),
            'keys' => 'textscaleyin',
            'tooltip' => ls__('Scales text fragments along the Y (vertical) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks text fragments compared to their original size. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'textSkewXIn' => [
            'value' => 0,
            'name' => ls__('SkewX'),
            'tooltip' => ls__('Skews text fragments along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textskewxin',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textSkewYIn' => [
            'value' => 0,
            'name' => ls__('SkewY'),
            'tooltip' => ls__('Skews text fragments along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textskewyin',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textTransformOriginIn' => [
            'value' => '50% 50% 0',
            'name' => ls__('Transform Origin'),
            'tooltip' => ls__('Sets a point on canvas from which transformations are calculated. For example, a layer may rotate around its center axis or a completely custom point, such as one of its corners. The three values represent the X, Y and Z axes in 3D space. Apart from the pixel and percentage values, you can also use the following constants: top, right, bottom, left, center, slidercenter, slidermiddle, slidertop, sliderright, sliderbottom, sliderleft.'),
            'keys' => 'texttransformoriginin',
            'attrs' => ['data-options' => '[{
                "name": "Cycle between values",
                "value": "50% 50% 0|100% 100% 0"
            }]'],
        ],

        'textPerspectiveIn' => [
            'value' => '500',
            'name' => ls__('Perspective'),
            'keys' => 'texttransformperspectivein',
            'tooltip' => ls__('Changes the perspective of this layer in the 3D space.'),
        ],

        // Text Animation OUT
        // -----------------

        'textTransitionOut' => [
            'value' => false,
            'keys' => 'texttransitionout',
        ],

        'textTypeOut' => [
            'value' => 'chars_desc',
            'name' => ls__('Text Animation'),
            'keys' => 'texttypeout',
            'tooltip' => ls__('Select how your text should be split and animated.'),
            'options' => [
                'chars_asc' => ls__('by chars ascending'),
                'chars_desc' => ls__('by chars descending'),
                'chars_rand' => ls__('by chars random'),
                'chars_center' => ls__('by chars center to edge'),
                'chars_edge' => ls__('by chars edge to center'),
                'words_asc' => ls__('by words ascending'),
                'words_desc' => ls__('by words descending'),
                'words_rand' => ls__('by words random'),
                'words_center' => ls__('by words center to edge'),
                'words_edge' => ls__('by words edge to center'),
                'lines_asc' => ls__('by lines ascending'),
                'lines_desc' => ls__('by lines descending'),
                'lines_rand' => ls__('by lines random'),
                'lines_center' => ls__('by lines center to edge'),
                'lines_edge' => ls__('by lines edge to center'),
            ],
            'props' => [
                'output' => true,
            ],
        ],

        'textShiftOut' => [
            'value' => '',
            'name' => ls__('Shift Out'),
            'tooltip' => ls__('Delays the transition of each text nodes relative to each other. A second equals to 1000 milliseconds.'),
            'keys' => 'textshiftout',
            'attrs' => ['type' => 'number'],
        ],

        'textOffsetXOut' => [
            'value' => 0,
            'name' => ls__('OffsetX'),
            'tooltip' => ls__("Shifts the ending position of text nodes from their original on the horizontal axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the width of this layer. The values 'left' or 'right' position text nodes out the staging area, so they leave the scene from either side when animating to their destination location. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values."),
            'keys' => 'textoffsetxout',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Leave the stage on left",
                "value": "left"
            }, {
                "name": "Leave the stage on right",
                "value": "right"
            }, {
                "name": "100% layer width",
                "value": "100lw"
            }, {
                "name": "-100% layer width",
                "value": "-100lw"
            }, {
                "name": "50% slider width",
                "value": "50sw"
            }, {
                "name": "-50% slider width",
                "value": "-50sw"
            }, {
                "name": "Cycle between values",
                "value": "50|-50"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'textOffsetYOut' => [
            'value' => 0,
            'name' => ls__('OffsetY'),
            'tooltip' => ls__("Shifts the ending position of text nodes from their original on the vertical axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the width of this layer. The values 'top' or 'bottom' position text nodes out the staging area, so they leave the scene from either vertical side when animating to their destination location. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values."),
            'keys' => 'textoffsetyout',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Leave the stage on top",
                "value": "top"
            }, {
                "name": "Leave the stage on bottom",
                "value": "bottom"
            }, {
                "name": "100% layer height",
                "value": "100lh"
            }, {
                "name": "-100% layer height",
                "value": "-100lh"
            }, {
                "name": "50% slider height",
                "value": "50sh"
            }, {
                "name": "-50% slider height",
                "value": "-50sh"
            }, {
                "name": "Cycle between values",
                "value": "50|-50"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'textDurationOut' => [
            'value' => 1000,
            'name' => ls__('Duration'),
            'tooltip' => ls__('The transition length in milliseconds of the individual text fragments. A second equals to 1000 milliseconds.'),
            'keys' => 'textdurationout',
            'attrs' => ['min' => 0, 'step' => 50],
        ],

        'textEasingOut' => [
            'value' => 'easeInOutQuint',
            'name' => ls__('Easing'),
            'tooltip' => ls__('The timing function of the animation. With this function you can manipulate the movement of animated text fragments. Please click on the link next to this select field to open easings.net for more information and real-time examples.'),
            'keys' => 'texteasingout',
            'attrs' => ['type' => 'hidden'],
        ],

        'textFadeOut' => [
            'value' => true,
            'name' => ls__('Fade'),
            'tooltip' => ls__('Fade the text fragments during their transition.'),
            'keys' => 'textfadeout',
        ],

        'textStartAtOut' => [
            'value' => 'allinandloopend',
            'name' => ls__('StartAt'),
            'tooltip' => ls__('You can set the starting time of this transition. Use one of the pre-defined options to use relative timing, which can be shifted with custom operations.'),
            'keys' => 'textstartatout',
            'attrs' => ['type' => 'hidden'],
        ],

        'textStartAtOutTiming' => [
            'value' => 'allinandloopend',
            'keys' => 'textstartatouttiming',
            'props' => ['meta' => true],
            'options' => [
                'transitioninend' => ls__('Opening Transition completes'),
                'textinstart' => ls__('Opening Text Transition starts'),
                'textinend' => ls__('Opening Text Transition completes'),
                'allinend' => ls__('Opening and Opening Text Transition complete'),
                'loopstart' => ls__('Loop starts'),
                'loopend' => ls__('Loop completes'),
                'transitioninandloopend' => ls__('Opening and Loop Transitions complete'),
                'textinandloopend' => ls__('Opening Text and Loop Transitions complete'),
                'allinandloopend' => ls__('Opening, Opening Text and Loop Transitions complete'),
            ],
        ],

        'textStartAtOutOperator' => [
            'value' => '+',
            'keys' => 'textstartatoutoperator',
            'props' => ['meta' => true],
            'options' => ['+', '-', '/', '*'],
        ],

        'textStartAtOutValue' => [
            'value' => 0,
            'keys' => 'textstartatoutvalue',
            'props' => ['meta' => true],
        ],

        'textRotateOut' => [
            'value' => 0,
            'name' => ls__('Rotate'),
            'tooltip' => ls__('Rotates text fragments clockwise by the given number of degrees. Negative values are allowed for counterclockwise rotation. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textrotateout',
            'attrs' => ['type' => 'text', 'data-options' => '[{
            "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textRotateXOut' => [
            'value' => 0,
            'name' => ls__('RotateX'),
            'tooltip' => ls__('Rotates text fragments along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textrotatexout',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textRotateYOut' => [
            'value' => 0,
            'name' => ls__('RotateY'),
            'tooltip' => ls__('Rotates text fragments along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textrotateyout',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textScaleXOut' => [
            'value' => 1,
            'name' => ls__('ScaleX'),
            'keys' => 'textscalexout',
            'tooltip' => ls__('Scales text fragments along the X (horizontal) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks text fragments compared to their original size. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'textScaleYOut' => [
            'value' => 1,
            'name' => ls__('ScaleY'),
            'keys' => 'textscaleyout',
            'tooltip' => ls__('Scales text fragments along the Y (vertical) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks text fragments compared to their original size. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'textSkewXOut' => [
            'value' => 0,
            'name' => ls__('SkewX'),
            'tooltip' => ls__('Skews text fragments along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textskewxout',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textSkewYOut' => [
            'value' => 0,
            'name' => ls__('SkewY'),
            'tooltip' => ls__('Skews text fragments along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction. By listing multiple values separated with a | character, the slider will use different transition variations on each text node by cycling between the provided values.'),
            'keys' => 'textskewyout',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "30|-30"
            }, {
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'textTransformOriginOut' => [
            'value' => '50% 50% 0',
            'name' => ls__('Transform Origin'),
            'tooltip' => ls__('Sets a point on canvas from which transformations are calculated. For example, a layer may rotate around its center axis or a completely custom point, such as one of its corners. The three values represent the X, Y and Z axes in 3D space. Apart from the pixel and percentage values, you can also use the following constants: top, right, bottom, left, center, slidercenter, slidermiddle, slidertop, sliderright, sliderbottom, sliderleft.'),
            'keys' => 'texttransformoriginout',
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Cycle between values",
                "value": "50% 50% 0|100% 100% 0"
            }]'],
        ],

        'textPerspectiveOut' => [
            'value' => '500',
            'name' => ls__('Perspective'),
            'keys' => 'texttransformperspectiveout',
            'tooltip' => ls__('Changes the perspective of this layer in the 3D space.'),
        ],

        // ======

        // LOOP

        'loop' => [
            'value' => false,
            'keys' => 'loop',
        ],

        'loopOffsetX' => [
            'value' => 0,
            'name' => ls__('OffsetX'),
            'keys' => 'loopoffsetx',
            'tooltip' => ls__("Shifts the layer starting position from its original on the horizontal axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the width of this layer. The values 'left' or 'right' position the layer out the staging area, so it can leave and re-enter the scene from either side during the transition."),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Move out of stage on left",
                "value": "left"
            }, {
                "name": "Move out of stage on right",
                "value": "right"
            }, {
                "name": "100% layer width",
                "value": "100lw"
            }, {
                "name": "-100% layer width",
                "value": "-100lw"
            }, {
                "name": "50% slider width",
                "value": "50sw"
            }, {
                "name": "-50% slider width",
                "value": "-50sw"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'loopOffsetY' => [
            'value' => 0,
            'name' => ls__('OffsetY'),
            'keys' => 'loopoffsety',
            'tooltip' => ls__("Shifts the layer starting position from its original on the vertical axis with the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the height of this layer. The values 'top' or 'bottom' position the layer out the staging area, so it can leave and re-enter the scene from either vertical side during the transition."),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Move out of stage on top",
                "value": "top"
            }, {
                "name": "Move out of stage on bottom",
                "value": "bottom"
            }, {
                "name": "100% layer height",
                "value": "100lh"
            }, {
                "name": "-100% layer height",
                "value": "-100lh"
            }, {
                "name": "50% slider height",
                "value": "50sh"
            }, {
                "name": "-50% slider height",
                "value": "-50sh"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'loopDuration' => [
            'value' => 1000,
            'name' => ls__('Duration'),
            'keys' => 'loopduration',
            'tooltip' => ls__('The length of the transition in milliseconds. A second is equal to 1000 milliseconds.'),
            'attrs' => ['min' => 0, 'step' => 100],
        ],

        'loopStartAt' => [
            'value' => 'allinend',
            'name' => ls__('Start at'),
            'keys' => 'loopstartat',
            'tooltip' => ls__('You can set the starting time of this transition. Use one of the pre-defined options to use relative timing, which can be shifted with custom operations.'),
            'attrs' => ['type' => 'hidden', 'step' => 100],
        ],

        'loopStartAtTiming' => [
            'value' => 'allinend',
            'keys' => 'loopstartattiming',
            'props' => ['meta' => true],
            'options' => [
                'transitioninstart' => ls__('Opening Transition starts'),
                'transitioninend' => ls__('Opening Transition completes'),
                'textinstart' => ls__('Opening Text Transition starts'),
                'textinend' => ls__('Opening Text Transition completes'),
                'allinend' => ls__('Opening and Opening Text Transition complete'),
            ],
        ],

        'loopStartAtOperator' => [
            'value' => '+',
            'keys' => 'loopstartatoperator',
            'props' => ['meta' => true],
            'options' => ['+', '-', '/', '*'],
        ],

        'loopStartAtValue' => [
            'value' => 0,
            'keys' => 'loopstartatvalue',
            'props' => ['meta' => true],
        ],

        'loopEasing' => [
            'value' => 'linear',
            'name' => ls__('Easing'),
            'keys' => 'loopeasing',
            'tooltip' => ls__("The timing function of the animation to manipualte the layer's movement. Click on the link next to this field to open easings.net for examples and more information"),
        ],

        'loopOpacity' => [
            'value' => 1,
            'name' => ls__('Opacity'),
            'keys' => 'loopopacity',
            'tooltip' => ls__('Fades the layer. You can use values between 1 and 0 to set the layer fully opaque or transparent respectively. For example, the value 0.5 will make the layer semi-transparent.'),
            'attrs' => ['min' => 0, 'max' => 1, 'step' => 0.01],
        ],

        'loopRotate' => [
            'value' => 0,
            'name' => ls__('Rotate'),
            'keys' => 'looprotate',
            'tooltip' => ls__('Rotates the layer by the given number of degrees. Negative values are allowed for counterclockwise rotation.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'loopRotateX' => [
            'value' => 0,
            'name' => ls__('RotateX'),
            'keys' => 'looprotatex',
            'tooltip' => ls__('Rotates the layer along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'loopRotateY' => [
            'value' => 0,
            'name' => ls__('RotateY'),
            'keys' => 'looprotatey',
            'tooltip' => ls__('Rotates the layer along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'loopSkewX' => [
            'value' => 0,
            'name' => ls__('SkewX'),
            'keys' => 'loopskewx',
            'tooltip' => ls__('Skews the layer along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'loopSkewY' => [
            'value' => 0,
            'name' => ls__('SkewY'),
            'keys' => 'loopskewy',
            'tooltip' => ls__('Skews the layer along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'loopScaleX' => [
            'value' => 1,
            'name' => ls__('ScaleX'),
            'keys' => 'loopscalex',
            'tooltip' => ls__('Scales the layer along the X (horizontal) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks the layer compared to its original size.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'loopScaleY' => [
            'value' => 1,
            'name' => ls__('ScaleY'),
            'keys' => 'loopscaley',
            'tooltip' => ls__('Scales the layer along the X (horizontal) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks the layer compared to its original size.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'loopTransformOrigin' => [
            'value' => '50% 50% 0',
            'name' => ls__('Transform Origin'),
            'keys' => 'looptransformorigin',
            'tooltip' => ls__('Sets a point on canvas from which transformations are calculated. For example, a layer may rotate around its center axis or a completely custom point, such as one of its corners. The three values represent the X, Y and Z axes in 3D space. Apart from the pixel and percentage values, you can also use the following constants: top, right, bottom, left, center, slidercenter, slidermiddle, slidertop, sliderright, sliderbottom, sliderleft.'),
        ],

        'loopClip' => [
            'value' => '',
            'name' => ls__('Mask'),
            'keys' => 'loopclip',
            'tooltip' => ls__('Clips (cuts off) the sides of the layer by the given amount specified in pixels or percentages. The 4 value in order: top, right, bottom and the left side of the layer.'),
            'attrs' => ['data-options' => '[{
                "name": "From top",
                "value": "0 0 100% 0"
            }, {
                "name": "From right",
                "value": "0 0 0 100%"
            }, {
                "name": "From bottom",
                "value": "100% 0 0 0"
            }, {
                "name": "From left",
                "value": "0 100% 0 0"
            }]'],
        ],

        'loopCount' => [
            'value' => 1,
            'name' => ls__('Count'),
            'keys' => 'loopcount',
            'tooltip' => ls__('The number of times repeating the Loop transition. The count includes the reverse part of the transitions when you use the Yoyo feature. Use the value -1 to repeat infinitely or zero to disable looping.'),
            'attrs' => [
                'step' => 1,
                'data-options' => '[{
                    "name": "Infinite",
                    "value": -1
                }]',
            ],
            'props' => [
                'output' => true,
            ],
        ],

        'loopWait' => [
            'value' => 0,
            'name' => ls__('Wait'),
            'keys' => 'looprepeatdelay',
            'tooltip' => ls__('Waiting time between repeats in milliseconds. A second is 1000 milliseconds.'),
            'attrs' => ['min' => 0, 'step' => 100],
        ],

        'loopYoyo' => [
            'value' => false,
            'name' => ls__('Yoyo'),
            'keys' => 'loopyoyo',
            'tooltip' => ls__('Enable this option to allow reverse transition, so you can loop back and forth seamlessly.'),
        ],

        'loopPerspective' => [
            'value' => '500',
            'name' => ls__('Perspective'),
            'keys' => 'looptransformperspective',
            'tooltip' => ls__('Changes the perspective of this layer in the 3D space.'),
        ],

        'loopFilter' => [
            'value' => '',
            'name' => ls__('Filter'),
            'keys' => 'loopfilter',
            'tooltip' => ls__('Filters provide effects like blurring or color shifting your layers. Click into the text field to see a selection of filters you can use. Although clicking on the pre-defined options will reset the text field, you can apply multiple filters simply by providing a space separated list of all the filters you would like to use. Click on the "Filter" link for more information.'),
            'premium' => true,
            'attrs' => [
                'data-options' => '[{
                    "name": "Blur",
                    "value": "blur(5px)"
                }, {
                    "name": "Brightness",
                    "value": "brightness(40%)"
                }, {
                    "name": "Contrast",
                    "value": "contrast(200%)"
                }, {
                    "name": "Grayscale",
                    "value": "grayscale(50%)"
                }, {
                    "name": "Hue-rotate",
                    "value": "hue-rotate(90deg)"
                }, {
                    "name": "Invert",
                    "value": "invert(75%)"
                }, {
                    "name": "Saturate",
                    "value": "saturate(30%)"
                }, {
                    "name": "Sepia",
                    "value": "sepia(60%)"
                }]',
            ],
        ],

        // HOVER

        'hover' => [
            'value' => false,
            'keys' => 'hover',
        ],

        'hoverOffsetX' => [
            'value' => 0,
            'name' => ls__('OffsetX'),
            'keys' => 'hoveroffsetx',
            'tooltip' => ls__('Moves the layer horizontally by the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the width of this layer. '),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "20% layer width",
                "value": "20lw"
            }, {
                "name": "-20% layer width",
                "value": "-20lw"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'hoverOffsetY' => [
            'value' => 0,
            'name' => ls__('OffsetY'),
            'keys' => 'hoveroffsety',
            'tooltip' => ls__('Moves the layer vertically by the given number of pixels. Use negative values for the opposite direction. Percentage values are relative to the width of this layer. '),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "20% layer height",
                "value": "20lh"
            }, {
                "name": "-20% layer height",
                "value": "-20lh"
            }, {
                "name": "Random",
                "value": "random(-100,100)"
            }]'],
        ],

        'hoverInDuration' => [
            'value' => 500,
            'name' => ls__('Duration'),
            'keys' => 'hoverdurationin',
            'tooltip' => ls__('The length of the transition in milliseconds. A second is equal to 1000 milliseconds.'),
            'attrs' => ['min' => 0, 'step' => 100],
        ],

        'hoverOutDuration' => [
            'value' => '',
            'name' => ls__('Reverse<br>duration'),
            'keys' => 'hoverdurationout',
            'tooltip' => ls__('The duration of the reverse transition in milliseconds. A second is equal to 1000 milliseconds.'),
            'attrs' => ['min' => 0, 'step' => 100, 'placeholder' => 'same'],
        ],

        'hoverInEasing' => [
            'value' => 'easeInOutQuint',
            'name' => ls__('Easing'),
            'keys' => 'hovereasingin',
            'tooltip' => ls__("The timing function of the animation to manipualte the layer's movement. Click on the link next to this field to open easings.net for examples and more information"),
        ],

        'hoverOutEasing' => [
            'value' => '',
            'name' => ls__('Reverse<br>easing'),
            'keys' => 'hovereasingout',
            'tooltip' => ls__("The timing function of the reverse animation to manipualte the layer's movement. Click on the link next to this field to open easings.net for examples and more information"),
            'attrs' => ['placeholder' => 'same'],
        ],

        'hoverOpacity' => [
            'value' => '',
            'name' => ls__('Opacity'),
            'keys' => 'hoveropacity',
            'tooltip' => ls__('Fades the layer. You can use values between 1 and 0 to set the layer fully opaque or transparent respectively. For example, the value 0.5 will make the layer semi-transparent.'),
            'attrs' => [
                'min' => 0,
                'max' => 1,
                'step' => 0.1,
            ],
        ],

        'hoverRotate' => [
            'value' => 0,
            'name' => ls__('Rotate'),
            'keys' => 'hoverrotate',
            'tooltip' => ls__('Rotates the layer clockwise by the given number of degrees. Negative values are allowed for counterclockwise rotation.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'hoverRotateX' => [
            'value' => 0,
            'name' => ls__('RotateX'),
            'keys' => 'hoverrotatex',
            'tooltip' => ls__('Rotates the layer along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'hoverRotateY' => [
            'value' => 0,
            'name' => ls__('RotateY'),
            'keys' => 'hoverrotatey',
            'tooltip' => ls__('Rotates the layer along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'hoverSkewX' => [
            'value' => 0,
            'name' => ls__('SkewX'),
            'keys' => 'hoverskewx',
            'tooltip' => ls__('Skews the layer along the X (horizontal) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'hoverSkewY' => [
            'value' => 0,
            'name' => ls__('SkewY'),
            'keys' => 'hoverskewy',
            'tooltip' => ls__('Skews the layer along the Y (vertical) axis by the given number of degrees. Negative values are allowed for reverse direction.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(-45,45)"
            }]'],
        ],

        'hoverScaleX' => [
            'value' => 1,
            'name' => ls__('ScaleX'),
            'keys' => 'hoverscalex',
            'tooltip' => ls__('Scales the layer along the X (horizontal) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks the layer compared to its original size.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'hoverScaleY' => [
            'value' => 1,
            'name' => ls__('ScaleY'),
            'keys' => 'hoverscaley',
            'tooltip' => ls__('Scales the layer along the Y (vertical) axis by the specified vector. Use the value 1 for the original size. The value 2 will double, while 0.5 shrinks the layer compared to its original size.'),
            'attrs' => ['type' => 'text', 'data-options' => '[{
                "name": "Random",
                "value": "random(2,4)"
            }]'],
        ],

        'hoverTransformOrigin' => [
            'value' => '50% 50% 0',
            'attrs' => ['placeholder' => 'inherit'],
            'name' => ls__('Transform Origin'),
            'keys' => 'hovertransformorigin',
            'tooltip' => ls__('Sets a point on canvas from which transformations are calculated. For example, a layer may rotate around its center axis or a completely custom point, such as one of its corners. The three values represent the X, Y and Z axes in 3D space. Apart from the pixel and percentage values, you can also use the following constants: top, right, bottom, left, center.'),
        ],

        'hoverBGColor' => [
            'value' => '',
            'name' => ls__('Background'),
            'keys' => 'hoverbgcolor',
            'tooltip' => ls__("The background color of this layer. You can use color names, hexadecimal, RGB or RGBA values as well as the 'transparent' keyword. Example: #FFF"),
        ],

        'hoverColor' => [
            'value' => '',
            'name' => ls__('Color'),
            'keys' => 'hovercolor',
            'tooltip' => ls__('The text color of this text. You can use color names, hexadecimal, RGB or RGBA values. Example: #333'),
        ],

        'hoverBorderRadius' => [
            'value' => '',
            'name' => ls__('Rounded corners'),
            'keys' => 'hoverborderradius',
            'tooltip' => ls__('If you want rounded corners, you can set here its radius in pixels. Example: 5px'),
        ],

        'hoverTransformPerspective' => [
            'value' => 500,
            'name' => ls__('Perspective'),
            'keys' => 'hovertransformperspective',
            'tooltip' => ls__('Changes the perspective of layers in the 3D space.'),
        ],

        'hoverTopOn' => [
            'value' => true,
            'name' => ls__('Always on top'),
            'keys' => 'hoveralwaysontop',
            'tooltip' => ls__('Show this layer above every other layer while hovering.'),
        ],

        // Parallax
        'parallax' => [
            'value' => false,
            'keys' => 'parallax',
        ],

        'parallaxLevel' => [
            'value' => 10,
            'name' => ls__('Parallax Level'),
            'tooltip' => ls__('Set the intensity of the parallax effect. Use negative values to shift layers in the opposite direction.'),
            'keys' => 'parallaxlevel',
            'props' => [
                'output' => true,
            ],
        ],

        'parallaxType' => [
            'value' => 'inherit',
            'name' => ls__('Type'),
            'tooltip' => ls__('Choose if you want 2D or 3D parallax layers.'),
            'keys' => 'parallaxtype',
            'options' => [
                'inherit' => ls__('Inherit from Slide Options'),
                '2d' => ls__('2D'),
                '3d' => ls__('3D'),
            ],
        ],

        'parallaxEvent' => [
            'value' => 'inherit',
            'name' => ls__('Event'),
            'tooltip' => ls__('You can trigger the parallax effect by either scrolling the page, or by moving your mouse cursor / tilting your mobile device.'),
            'keys' => 'parallaxevent',
            'options' => [
                'inherit' => ls__('Inherit from Slide Options'),
                'cursor' => ls__('Cursor or Tilt'),
                'scroll' => ls__('Scroll'),
            ],
        ],

        'parallaxAxis' => [
            'value' => 'inherit',
            'name' => ls__('Axes'),
            'tooltip' => ls__('Choose on which axes parallax layers should move.'),
            'keys' => 'parallaxaxis',
            'options' => [
                'inherit' => ls__('Inherit from Slide Options'),
                'none' => ls__('None'),
                'both' => ls__('Both'),
                'x' => ls__('Horizontal only'),
                'y' => ls__('Vertical only'),
            ],
        ],

        'parallaxTransformOrigin' => [
            'value' => '',
            'name' => ls__('Transform Origin'),
            'tooltip' => ls__('Sets a point on canvas from which transformations are calculated. For example, a layer may rotate around its center axis or a completely custom point, such as one of its corners. The three values represent the X, Y and Z axes in 3D space. Apart from the pixel and percentage values, you can also use the following constants: top, right, bottom, left, center.'),
            'keys' => 'parallaxtransformorigin',
            'attrs' => [
                'placeholder' => 'Inherit from Slide Options',
            ],
        ],

        'parallaxDurationMove' => [
            'value' => '',
            'name' => ls__('Move Duration'),
            'tooltip' => ls__('Controls the speed of animating layers when you move your mouse cursor or tilt your mobile device.'),
            'keys' => 'parallaxdurationmove',
            'attrs' => [
                'type' => 'number',
                'step' => 100,
                'min' => 0,
                'placeholder' => 'Inherit from Slide Options',
            ],
        ],

        'parallaxDurationLeave' => [
            'value' => '',
            'name' => ls__('Leave Duration'),
            'tooltip' => ls__('Controls how quickly parallax layers revert to their original position when you move your mouse cursor outside of the slider. This value is in milliseconds. A second equals to 1000 milliseconds.'),
            'keys' => 'parallaxdurationleave',
            'attrs' => [
                'type' => 'number',
                'step' => 100,
                'min' => 0,
                'placeholder' => 'Inherit from Slide Options',
            ],
        ],

        'parallaxRotate' => [
            'value' => '',
            'name' => ls__('Rotation'),
            'tooltip' => ls__('Increase or decrease the amount of layer rotation in the 3D space when moving your mouse cursor or tilting on a mobile device.'),
            'keys' => 'parallaxrotate',
            'attrs' => [
                'type' => 'number',
                'step' => 1,
                'placeholder' => 'Inherit from Slide Options',
            ],
        ],

        'parallaxDistance' => [
            'value' => '',
            'name' => ls__('Distance'),
            'tooltip' => ls__('Increase or decrease the amount of layer movement when moving your mouse cursor or tilting on a mobile device.'),
            'keys' => 'parallaxdistance',
            'attrs' => [
                'type' => 'number',
                'step' => 1,
                'placeholder' => 'Inherit from Slide Options',
            ],
        ],

        'parallaxPerspective' => [
            'value' => '',
            'name' => ls__('Perspective'),
            'tooltip' => ls__('Changes the perspective of layers in the 3D space.'),
            'keys' => 'parallaxtransformperspective',
            'attrs' => [
                'type' => 'number',
                'step' => 100,
                'placeholder' => 'Inherit from Slide Options',
            ],
        ],

        // TRANSITON MISC
        'transitionStatic' => [
            'value' => 'none',
            'name' => ls__('Static layer'),
            'keys' => 'static',
            'tooltip' => ls__('You can keep this layer on top of the slider across multiple slides. Just select the slide on which this layer should animate out. Alternatively, you can make this layer global on all slides after it transitioned in.'),
            'options' => [
                'none' => ls__('Disabled (default)'),
                'forever' => ls__('Enabled (never animate out)'),
            ],
        ],

        'transitionKeyframe' => [
            'value' => false,
            'name' => ls__('Play By Scroll Keyframe'),
            'keys' => 'keyframe',
            'tooltip' => ls__('A Play by Scroll slider will pause when this layer finished its opening transition.'),
        ],

        // Attributes

        'linkURL' => [
            'value' => '',
            'name' => ls__('Enter URL'),
            'keys' => 'url',
            'tooltip' => ls__('If you want to link your layer, type here the URL. You can use a hash mark followed by a number to link this layer to another slide. Example: #3 - this will switch to the third slide.'),
            'attrs' => [
                'data-options' => '[{
                    "name": "Switch to the next slide",
                    "value": "#next"
                }, {
                    "name": "Switch to the previous slide",
                    "value": "#prev"
                }, {
                    "name": "Stop the slideshow",
                    "value": "#stop"
                }, {
                    "name": "Resume the slideshow",
                    "value": "#start"
                }, {
                    "name": "Replay the slide from the start",
                    "value": "#replay"
                }, {
                    "name": "Reverse the slide, then pause it",
                    "value": "#reverse"
                }, {
                    "name": "Reverse the slide, then replay it",
                    "value": "#reverse-replay"
                }]',
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'linkTarget' => [
            'value' => '_self',
            'name' => ls__('URL target'),
            'keys' => 'target',
            'options' => [
                '_self' => ls__('Open on the same page'),
                '_blank' => ls__('Open on new page'),
                '_parent' => ls__('Open in parent frame'),
                '_top' => ls__('Open in main frame'),
                'ls-scroll' => ls__('Scroll to element (Enter selector)'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'innerAttributes' => [
            'value' => '',
            'name' => ls__('Custom Attributes'),
            'keys' => 'innerAttributes',
            'desc' => ls__('Your list of custom attributes. Use this feature if your needs are not covered by the common attributes above or you want to override them. You can use data-* as well as regular attribute names. Empty attributes (without value) are also allowed. For example, to make a FancyBox gallery, you may enter "data-fancybox-group" and "gallery1" for the attribute name and value, respectively.'),
            'props' => [
                'meta' => true,
            ],
        ],

        'outerAttributes' => [
            'value' => '',
            'name' => ls__('Custom Attributes'),
            'keys' => 'outerAttributes',
            'desc' => ls__('Your list of custom attributes. Use this feature if your needs are not covered by the common attributes above or you want to override them. You can use data-* as well as regular attribute names. Empty attributes (without value) are also allowed. For example, to make a FancyBox gallery, you may enter "data-fancybox-group" and "gallery1" for the attribute name and value, respectively.'),
            'props' => [
                'meta' => true,
            ],
        ],

        // Styles

        'width' => [
            'value' => '',
            'name' => ls__('Width'),
            'keys' => 'width',
            'tooltip' => ls__("You can set the width of your layer. You can use pixels, percentage, or the default value 'auto'. Examples: 100px, 50% or auto."),
            'props' => [
                'meta' => true,
            ],
        ],

        'height' => [
            'value' => '',
            'name' => ls__('Height'),
            'keys' => 'height',
            'tooltip' => ls__("You can set the height of your layer. You can use pixels, percentage, or the default value 'auto'. Examples: 100px, 50% or auto"),
            'props' => [
                'meta' => true,
            ],
        ],

        'top' => [
            'value' => '10px',
            'name' => ls__('Top'),
            'keys' => 'top',
            'tooltip' => ls__("The layer position from the top of the slide. You can use pixels and percentage. Examples: 100px or 50%. You can move your layers in the preview above with a drag n' drop, or set the exact values here."),
            'props' => [
                'meta' => true,
            ],
        ],

        'left' => [
            'value' => '10px',
            'name' => ls__('Left'),
            'keys' => 'left',
            'tooltip' => ls__("The layer position from the left side of the slide. You can use pixels and percentage. Examples: 100px or 50%. You can move your layers in the preview above with a drag n' drop, or set the exact values here."),
            'props' => [
                'meta' => true,
            ],
        ],

        'paddingTop' => [
            'value' => '',
            'name' => ls__('Top'),
            'keys' => 'padding-top',
            'tooltip' => ls__('Padding on the top of the layer. Example: 10px'),
            'props' => [
                'meta' => true,
            ],
        ],

        'paddingRight' => [
            'value' => '',
            'name' => ls__('Right'),
            'keys' => 'padding-right',
            'tooltip' => ls__('Padding on the right side of the layer. Example: 10px'),
            'props' => [
                'meta' => true,
            ],
        ],

        'paddingBottom' => [
            'value' => '',
            'name' => ls__('Bottom'),
            'keys' => 'padding-bottom',
            'tooltip' => ls__('Padding on the bottom of the layer. Example: 10px'),
            'props' => [
                'meta' => true,
            ],
        ],

        'paddingLeft' => [
            'value' => '',
            'name' => ls__('Left'),
            'keys' => 'padding-left',
            'tooltip' => ls__('Padding on the left side of the layer. Example: 10px'),
            'props' => [
                'meta' => true,
            ],
        ],

        'borderTop' => [
            'value' => '',
            'name' => ls__('Top'),
            'keys' => 'border-top',
            'tooltip' => ls__('Border on the top of the layer. Example: 5px solid #000'),
            'props' => [
                'meta' => true,
            ],
        ],

        'borderRight' => [
            'value' => '',
            'name' => ls__('Right'),
            'keys' => 'border-right',
            'tooltip' => ls__('Border on the right side of the layer. Example: 5px solid #000'),
            'props' => [
                'meta' => true,
            ],
        ],

        'borderBottom' => [
            'value' => '',
            'name' => ls__('Bottom'),
            'keys' => 'border-bottom',
            'tooltip' => ls__('Border on the bottom of the layer. Example: 5px solid #000'),
            'props' => [
                'meta' => true,
            ],
        ],

        'borderLeft' => [
            'value' => '',
            'name' => ls__('Left'),
            'keys' => 'border-left',
            'tooltip' => ls__('Border on the left side of the layer. Example: 5px solid #000'),
            'props' => [
                'meta' => true,
            ],
        ],

        'fontFamily' => [
            'value' => '',
            'name' => ls__('Family'),
            'keys' => 'font-family',
            'tooltip' => ls__('List of your chosen fonts separated with a comma. Please use apostrophes if your font names contains white spaces. Example: Helvetica, Arial, sans-serif'),
        ],

        'fontSize' => [
            'value' => '',
            'name' => ls__('Font size'),
            'keys' => 'font-size',
            'tooltip' => ls__('The font size in pixels. Example: 16px.'),
            'attrs' => ['data-options' => '["9", "10", "11", "12", "13", "14", "18", "24", "36", "48", "64", "96"]'],
            'props' => [
                'meta' => true,
            ],
        ],

        'lineHeight' => [
            'value' => '',
            'name' => ls__('Line height'),
            'keys' => 'line-height',
            'tooltip' => ls__("The line height of your text. The default setting is 'normal'. Example: 22px"),
            'props' => [
                'meta' => true,
            ],
        ],

        'fontWeight' => [
            'value' => 400,
            'name' => ls__('Font weight'),
            'keys' => 'font-weight',
            'tooltip' => ls__('Sets the font boldness. Please note, not every font supports all the listed variants, thus some settings may have the same result.'),
            'options' => [
                '100' => ls__('100 (UltraLight)'),
                '200' => ls__('200 (Thin)'),
                '300' => ls__('300 (Light)'),
                '400' => ls__('400 (Regular)'),
                '500' => ls__('500 (Medium)'),
                '600' => ls__('600 (Semibold)'),
                '700' => ls__('700 (Bold)'),
                '800' => ls__('800 (Heavy)'),
                '900' => ls__('900 (Black)'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'fontStyle' => [
            'value' => 'normal',
            'name' => ls__('Font style'),
            'keys' => 'font-style',
            'tooltip' => ls__('Oblique is an auto-generated italic version of your chosen font and can force slating even if there is no italic font variant available. However, you should use the regular italic option whenever is possible. Please double check to load italic font variants when using Google Fonts.'),
            'options' => [
                'normal' => ls__('Normal'),
                'italic' => ls__('Italic'),
                'oblique' => ls__('Oblique (Forced slant)'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'textDecoration' => [
            'value' => 'none',
            'name' => ls__('Text decoration'),
            'keys' => 'text-decoration',
            'options' => [
                'none' => 'None',
                'underline' => ls__('Underline'),
                'overline' => ls__('Overline'),
                'line-through' => ls__('Line through'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'letterSpacing' => [
            'value' => '',
            'name' => ls__('Letter spacing'),
            'keys' => 'letter-spacing',
            'tooltip' => ls__('Controls the amount of space between each character. Useful the change letter density in a line or block of text. Negative values and decimals can be used.'),
            'attrs' => [
                'type' => 'number',
                'step' => 0.5,
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'textAlign' => [
            'value' => 'none',
            'name' => ls__('Text align'),
            'keys' => 'text-align',
            'options' => [
                'initial' => ls__('Initial (Language default)'),
                'left' => ls__('Left'),
                'right' => ls__('Right'),
                'center' => ls__('Center'),
                'justify' => ls__('Justify'),
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'opacity' => [
            'value' => 1,
            'name' => ls__('Opacity'),
            'keys' => 'opacity',
            'tooltip' => ls__('Fades the layer. You can use values between 1 and 0 to set the layer fully opaque or transparent respectively. For example, the value 0.5 will make the layer semi-transparent.'),
            'attrs' => [
                'min' => 0,
                'max' => 1,
                'step' => 0.1,
            ],
            'props' => [
                'meta' => true,
            ],
        ],

        'minFontSize' => [
            'value' => '',
            'name' => ls__('Min. font size'),
            'keys' => 'minfontsize',
            'tooltip' => ls__('The minimum font size in a responsive slider. This option allows you to prevent your texts layers becoming too small on smaller screens.'),
        ],

        'minMobileFontSize' => [
            'value' => '',
            'name' => ls__('Min. mobile font size'),
            'keys' => 'minmobilefontsize',
            'tooltip' => ls__('The minimum font size in a responsive slider on mobile devices. This option allows you to prevent your texts layers becoming too small on smaller screens.'),
        ],

        'color' => [
            'value' => '',
            'name' => ls__('Color'),
            'keys' => 'color',
            'tooltip' => ls__('The color of your text. You can use color names, hexadecimal, RGB or RGBA values. Example: #333'),
            'props' => [
                'meta' => true,
            ],
        ],

        'background' => [
            'value' => '',
            'name' => ls__('Background'),
            'keys' => 'background',
            'tooltip' => ls__("The background color of your layer. You can use color names, hexadecimal, RGB or RGBA values as well as the 'transparent' keyword. Example: #FFF"),
            'props' => [
                'meta' => true,
            ],
        ],

        'borderRadius' => [
            'value' => '',
            'name' => ls__('Rounded corners'),
            'keys' => 'border-radius',
            'tooltip' => ls__('If you want rounded corners, you can set its radius here. Example: 5px'),
            'props' => [
                'meta' => true,
            ],
        ],

        'wordWrap' => [
            'value' => false,
            'name' => 'Word-wrap',
            'keys' => 'wordwrap',
            'tooltip' => 'Enable this option to allow line breaking if your text content does not fit into one line. By default, layers have auto sizes based on the text length. If you set custom sizes, it\'s recommended to enable this option in most cases.',
            'props' => [
                'meta' => true,
            ],
        ],

        'style' => [
            'value' => '',
            'name' => ls__('Custom styles'),
            'keys' => 'style',
            'tooltip' => ls__('If you want to set style settings other than above, you can use here any CSS codes. Please make sure to write valid markup.'),
            'props' => [
                'meta' => true,
            ],
        ],

        'styles' => [
            'value' => '',
            'keys' => 'styles',
            'props' => [
                'meta' => true,
                'raw' => true,
            ],
        ],

        'rotate' => [
            'value' => 0,
            'name' => ls__('Rotate'),
            'keys' => 'rotation',
            'tooltip' => ls__('The rotation angle where this layer animates toward when entering into the slider canvas. Negative values are allowed for counterclockwise rotation.'),
        ],

        'rotateX' => [
            'value' => 0,
            'name' => ls__('RotateX'),
            'keys' => 'rotationX',
            'tooltip' => ls__('The rotation angle on the horizontal axis where this animates toward when entering into the slider canvas. Negative values are allowed for reversed direction.'),
        ],

        'rotateY' => [
            'value' => 0,
            'name' => ls__('RotateY'),
            'keys' => 'rotationY',
            'tooltip' => ls__('The rotation angle on the vertical axis where this layer animates toward when entering into the slider canvas. Negative values are allowed for reversed direction.'),
        ],

        'scaleX' => [
            'value' => 1,
            'name' => ls__('ScaleX'),
            'keys' => 'scaleX',
            'tooltip' => ls__('The layer horizontal scale where this layer animates toward when entering into the slider canvas.'),
            'attrs' => [
                'step' => 0.1,
            ],
        ],

        'scaleY' => [
            'value' => 1,
            'name' => ls__('ScaleY'),
            'keys' => 'scaleY',
            'tooltip' => ls__('The layer vertical scale where this layer animates toward when entering into the slider canvas.'),
            'attrs' => [
                'step' => 0.1,
            ],
        ],

        'skewX' => [
            'value' => 0,
            'name' => ls__('SkewX'),
            'keys' => 'skewX',
            'tooltip' => ls__('The layer horizontal skewing angle where this layer animates toward when entering into the slider canvas.'),
        ],

        'skewY' => [
            'value' => 0,
            'name' => ls__('SkewY'),
            'keys' => 'skewY',
            'tooltip' => ls__('The layer vertical skewing angle where this layer animates toward when entering into the slider canvas.'),
        ],

        'position' => [
            'value' => 'relative',
            'name' => ls__('Calculate positions from'),
            'keys' => 'position',
            'tooltip' => ls__('Sets the layer position origin from which top and left values are calculated. The default is the upper left corner of the slider canvas. In a full width and full size slider, your content is centered based on the screen size to achieve the best possible fit. By selecting the "sides of the screen" option in those scenarios, you can allow layers to escape the centered inner area and stick to the sides of the screen.'),
            'options' => [
                'relative' => ls__('sides of the slider'),
                'fixed' => ls__('sides of the screen'),
            ],
        ],

        'zIndex' => [
            'value' => '',
            'name' => ls__('Stacking order'),
            'keys' => 'z-index',
            'tooltip' => ls__("This option controls the vertical stacking order of layers that overlap. In CSS, it's commonly called as z-index. Elements with a higher value are stacked in front of elements with a lower one, effectively covering them. By default, this value is calculated automatically based on the order of your layers, thus simply re-ordering them can fix overlap issues. Use this option only if you want to set your own value manually in special cases like using static layers.<br><br>On each slide, the stacking order starts counting from 100. Providing a number less than 100 will put the layer behind every other layer on all slides. Specifying a much greater number, for example 500, will make the layer to be on top of everything else."),
            'attrs' => [
                'type' => 'number',
                'min' => 1,
                'placeholder' => 'auto',
            ],
        ],

        'blendMode' => [
            'value' => 'normal',
            'name' => ls__('Blend mode'),
            'keys' => 'mix-blend-mode',
            'tooltip' => ls__('Choose how layers and the slide background should blend into each other. Blend modes are an easy way to add eye-catching effects and is one of the most frequently used features in graphic and print design.'),
            'premium' => true,
            'options' => [
                'normal' => 'Normal',
                'multiply' => 'Multiply',
                'screen' => 'Screen',
                'overlay' => 'Overlay',
                'darken' => 'Darken',
                'lighten' => 'Lighten',
                'color-dodge' => 'Color-dodge',
                'color-burn' => 'Color-burn',
                'hard-light' => 'Hard-light',
                'soft-light' => 'Soft-light',
                'difference' => 'Difference',
                'exclusion' => 'Exclusion',
                'hue' => 'Hue',
                'saturation' => 'Saturation',
                'color' => 'Color',
                'luminosity' => 'Luminosity',
            ],
        ],

        'filter' => [
            'value' => '',
            'name' => ls__('Filter'),
            'keys' => 'filter',
            'tooltip' => ls__('Filters provide effects like blurring or color shifting your layers. Click into the text field to see a selection of filters you can use. Although clicking on the pre-defined options will reset the text field, you can apply multiple filters simply by providing a space separated list of all the filters you would like to use. Click on the "Filter" link for more information.'),
            'premium' => true,
            'attrs' => [
                'data-options' => '[{
                    "name": "Blur",
                    "value": "blur(5px)"
                }, {
                    "name": "Brightness",
                    "value": "brightness(40%)"
                }, {
                    "name": "Contrast",
                    "value": "contrast(200%)"
                }, {
                    "name": "Grayscale",
                    "value": "grayscale(50%)"
                }, {
                    "name": "Hue-rotate",
                    "value": "hue-rotate(90deg)"
                }, {
                    "name": "Invert",
                    "value": "invert(75%)"
                }, {
                    "name": "Saturate",
                    "value": "saturate(30%)"
                }, {
                    "name": "Sepia",
                    "value": "sepia(60%)"
                }]',
            ],
        ],

        // Attributes

        'ID' => [
            'value' => '',
            'name' => ls__('ID'),
            'keys' => 'id',
            'tooltip' => ls__('You can apply an ID attribute on the HTML element of this layer to work with it in your custom CSS or Javascript code.'),
            'props' => [
                'meta' => true,
            ],
        ],

        'class' => [
            'value' => '',
            'name' => ls__('Classes'),
            'keys' => 'class',
            'tooltip' => ls__('You can apply classes on the HTML element of this layer to work with it in your custom CSS or Javascript code.'),
            'props' => [
                'meta' => true,
            ],
        ],

        'title' => [
            'value' => '',
            'name' => ls__('Title'),
            'keys' => 'title',
            'tooltip' => ls__('You can add a title to this layer which will display as a tooltip if someone holds his mouse cursor over the layer.'),
            'props' => [
                'meta' => true,
            ],
        ],

        'alt' => [
            'value' => '',
            'name' => ls__('Alt'),
            'keys' => 'alt',
            'tooltip' => ls__('Name or describe your image layer, so search engines and VoiceOver softwares can properly identify it.'),
            'props' => [
                'meta' => true,
            ],
        ],

        'rel' => [
            'value' => '',
            'name' => ls__('Rel'),
            'keys' => 'rel',
            'tooltip' => ls__('Plugins and search engines may use this attribute to get more information about the role and behavior of a link.'),
            'props' => [
                'meta' => true,
            ],
        ],
    ],

    'easings' => [
        'linear',
        'swing',
        'easeInQuad',
        'easeOutQuad',
        'easeInOutQuad',
        'easeInCubic',
        'easeOutCubic',
        'easeInOutCubic',
        'easeInQuart',
        'easeOutQuart',
        'easeInOutQuart',
        'easeInQuint',
        'easeOutQuint',
        'easeInOutQuint',
        'easeInSine',
        'easeOutSine',
        'easeInOutSine',
        'easeInExpo',
        'easeOutExpo',
        'easeInOutExpo',
        'easeInCirc',
        'easeOutCirc',
        'easeInOutCirc',
        'easeInElastic',
        'easeOutElastic',
        'easeInOutElastic',
        'easeInBack',
        'easeOutBack',
        'easeInOutBack',
        'easeInBounce',
        'easeOutBounce',
        'easeInOutBounce',
    ],
];
