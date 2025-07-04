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

$GLOBALS['lsLoadPlugins'] = array();

class LsShortcode
{

    // List of already included sliders on page.
    // Using to identify duplicates and give them
    // a unique slider ID to avoid issues with caching.
    public static $slidersOnPage = array();

    private function __construct()
    {
    }


    /**
     * Registers the LayerSlider shortcode.
     *
     * @since 5.3.3
     * @access public
     * @return void
     */

    public static function registerShortcode()
    {
        if (!ls_shortcode_exists('layerslider')) {
            ls_add_shortcode('layerslider', array(__CLASS__, 'handleShortcode'));
        }
    }


    /**
     * Handles the shortcode workflow to display the
     * appropriate content.
     *
     * @since 5.3.3
     * @access public
     * @param array $atts Shortcode attributes
     * @return bool True on successful validation, false otherwise
     */

    public static function handleShortcode($atts = array())
    {
        if (self::validateFilters($atts)) {
            $output = '';
            $item = self::validateShortcode($atts);

            // Show error messages (if any)
            if (! empty($item['error'])) {
                // Bail out early if the visitor has no permission to see error messages
                if (! ls_current_user_can(ls_get_option('layerslider_custom_capability', 'manage_options'))) {
                    return '';
                }

                $output .= $item['error'];
            }

            if ($item['data']) {
                $output .= self::processShortcode($item['data'], $atts);
            }

            return $output;
        }
    }


    /**
     * Validates the provided shortcode filters (if any).
     *
     * @since 5.3.3
     * @access public
     * @param array $atts Shortcode attributes
     * @return bool True on successful validation, false otherwise
     */

    public static function validateFilters($atts = array())
    {
        // Bail out early and pass the validation
        // if there aren't filters provided
        if (empty($atts['filters'])) {
            return true;
        }

        // Gather data needed for filters
        // $pages = explode(',', $atts['filters']);
        // $currSlug = basename(ls_get_permalink());
        // $currPageID = (string) get_the_ID();

        // foreach ($pages as $page) {
        //     if (($page == 'homepage' && is_front_page())
        //         || $currPageID == $page
        //         || $currSlug == $page
        //         || in_category($page)
        //     ) {
        //         return true;
        //     }
        // }

        // No filters matched,
        // return false
        return false;
    }


    /**
     * Validates the shortcode parameters and checks
     * the references slider.
     *
     * @since 5.3.3
     * @access public
     * @param array $atts Shortcode attributes
     * @return bool True on successful validation, false otherwise
     */

    public static function validateShortcode($atts = array())
    {
        $error = false;
        $slider = false;

        // Has ID attribute
        if (!empty($atts['id'])) {
            $sliderID = $atts['id'];

            // Attempt to retrieve the pre-generated markup
            // set via the Transients API
            if (ls_get_option('ls_use_cache', true)) {
                if ($slider = ls_get_transient('ls-slider-data-'.$sliderID)) {
                    $slider['id'] = $sliderID;
                    $slider['_cached'] = true;
                }
            }

            // Slider exists and isn't deleted
            if (empty($slider)) {
                $slider = LsSliders::find($sliderID);
            }

            // ERROR: No slider with ID was found
            if (empty($slider)) {
                $error = self::generateErrorMarkup(
                    ls__('The slider cannot be found', 'LayerSlider'),
                    null
                );

            // ERROR: The slider is not published
            } elseif ((int)$slider['flag_hidden']) {
                $error = self::generateErrorMarkup(
                    ls__('Unpublished slider', 'LayerSlider'),
                    sprintf(ls__("The slider you've inserted here is yet to be published, thus it won't be displayed to your visitors. You can publish it by enabling the appropriate option in %sSlider Settings -> Publish%s. ", 'LayerSlider'), '<a href="'.ls_admin_url('admin.php?page=layerslider&action=edit&id='.(int)$slider['id'].'&showsettings=1#publish').'" target="_blank">', '</a>.'),
                    'dashicons-hidden'
                );

            // ERROR: The slider was removed
            } elseif ((int)$slider['flag_deleted']) {
                $error = self::generateErrorMarkup(
                    ls__('Removed slider', 'LayerSlider'),
                    sprintf(ls__("The slider you've inserted here was removed in the meantime, thus it won't be displayed to your visitors. This slider is still recoverable on the admin interface. You can enable listing removed sliders with the Screen Options -> Removed sliders option, then choose the Restore option for the corresponding item to reinstate this slider, or just click %shere%s.", 'LayerSlider'), '<a href="'.ls_nonce_url('?page=layerslider&action=restore&id='.$slider['id'].'&ref='.urlencode(ls_get_permalink()), 'restore_'.$slider['id']).'">', '</a>'),
                    'dashicons-trash'
                );

            // ERROR: Scheduled/disabled sliders
            } else {
                $tz = date_default_timezone_get();
                $siteTz = ls_get_option('timezone_string', 'UTC');
                $siteTz = $siteTz ? $siteTz : 'UTC';
                date_default_timezone_set($siteTz);

                if (! empty($slider['schedule_start']) && (int) $slider['schedule_start'] > time()) {
                    $error = self::generateErrorMarkup(
                        sprintf(ls__('This slider is scheduled to display on %s', 'LayerSlider'), date('Y-m-d H:i:s', (int) $slider['schedule_start'])),
                        '',
                        'dashicons-calendar-alt',
                        'scheduled'
                    );
                } elseif (! empty($slider['schedule_end']) && (int) $slider['schedule_end'] < time()) {
                    $error = self::generateErrorMarkup(
                        sprintf(ls__('This slider was scheduled to hide on %s ', 'LayerSlider'), date('Y-m-d H:i:s', (int) $slider['schedule_end'])),
                        sprintf(ls__('Due to scheduling, this slider is no longer visible to your visitors. If you wish to reinstate this slider, just remove the schedule in %sSlider Settings -> Publish%s.', 'LayerSlider'), '<a href="'.ls_admin_url('admin.php?page=layerslider&action=edit&id='.(int) $slider['id'].'&showsettings=1#publish').'" target="_blank">', '</a>'),
                        'dashicons-no-alt',
                        'dead'
                    );
                }

                date_default_timezone_set($tz);

                $context = Context::getContext();
                if (method_exists($context, 'getMobileDetect')) {
                    $context->getMobileDetect();
                } elseif (!$context->mobile_detect) {
                    require_once(_PS_TOOL_DIR_.'mobile_Detect/Mobile_Detect.php');
                    $context->mobile_detect = new Mobile_Detect();
                }
                $device = $context->mobile_detect;
                if (!empty($slider['data']['properties']['disableonmobile']) && $device->isMobile() && !$device->isTablet() ||
                    !empty($slider['data']['properties']['disableontablet']) && $device->isTablet() ||
                    !empty($slider['data']['properties']['disableondesktop']) && !$device->isMobile()) {
                    $error = true;
                }
            }

        // ERROR: No slider ID was provided
        } else {
            $error = self::generateErrorMarkup();
        }

        return array(
            'error' => $error,
            'data' => $slider
        );
    }


    public static function processShortcode($slider, $embed = array())
    {

        // Slider ID
        $sID = 'layerslider_'.$slider['id'];

        // Include init code in the footer?
        $condsc = ls_get_option('ls_conditional_script_loading', false) ? true : false;
        $footer = ls_get_option('ls_include_at_footer', false) ? true : false;
        $footer = $condsc ? true : $footer;

        // Check for the '_cached' key in data,
        // indicating that it's a pre-generated
        // slider markup retrieved via Transients
        if (!empty($slider['_cached'])) {
            $output = $slider;

        // No cached copy, generate new markup.
        // Make sure to include some database related
        // data, since we rely on those to display
        // notifications for admins.
        } else {
            $output = self::generateSliderMarkup($slider, $embed);

            $output['id'] = $slider['id'];
            $output['schedule_start'] = $slider['schedule_start'];
            $output['schedule_end'] = $slider['schedule_end'];
            $output['flag_hidden'] = $slider['flag_hidden'];
            $output['flag_deleted'] = $slider['flag_deleted'];
            $output['data'] = array(
                'properties' => array(
                    'disableonmobile' => !empty($slider['data']['properties']['disableonmobile']),
                    'disableontablet' => !empty($slider['data']['properties']['disableontablet']),
                    'disableondesktop' => !empty($slider['data']['properties']['disableondesktop']),
                ),
            );
            ls_set_transient('ls-slider-data-'.$slider['id'], $output, HOUR_IN_SECONDS * 6);
        }

        // Replace slider ID to avoid issues with enabled caching when
        // adding the same slider to a page in multiple times
        if (array_key_exists($slider['id'], self::$slidersOnPage)) {
            $sliderCount = ++self::$slidersOnPage[ $slider['id'] ];
            $output['init'] = str_replace($sID, $sID.'_'.$sliderCount, $output['init']);
            $output['container'] = str_replace($sID, $sID.'_'.$sliderCount, $output['container']);

            $sID = $sID.'_'.$sliderCount;
        } else {
            // Add current slider ID to identify duplicates later on
            // and give them a unique slider ID to avoid issues with caching.
            self::$slidersOnPage[ $slider['id'] ] = 1;
        }

        // Override firstSlide if it is specified in embed params
        if (! empty($embed['firstslide'])) {
            $output['init'] = str_replace('[firstSlide]', $embed['firstslide'], $output['init']);
        }

        // Filter to override the printed JavaScript init code
        if (ls_has_filter('layerslider_slider_init')) {
            $output['init'] = ls_apply_filters('layerslider_slider_init', $output['init'], $slider, $sID);
        }

        // Unify the whole markup after any potential string replacement
        $output['markup'] = $output['container'].$output['markup'];

        // Filter to override the printed HTML markup
        if (ls_has_filter('layerslider_slider_markup')) {
            $output['markup'] = ls_apply_filters('layerslider_slider_markup', $output['markup'], $slider, $sID);
        }

        // Origami
        if (!empty($output['plugins'])) {
            $GLOBALS['lsLoadPlugins'] = array_merge($GLOBALS['lsLoadPlugins'], $output['plugins']);
            foreach ($output['plugins'] as $plg) {
                ls_enqueue_script('layerslider-'.$plg);
                ls_enqueue_style('layerslider-'.$plg);
            }
        }

        if ($footer) {
            $GLOBALS['lsSliderInit'][] = $output['init'];
            return $output['markup'];
        } else {
            return $output['init'].$output['markup'];
        }
    }


    public static function generateSliderMarkup($slider = null, $embed = array())
    {
        // Bail out early if no params received
        if (!$slider) {
            return array('init' => '', 'container' => '', 'markup' => '');
        }

        // Slider and markup data
        $id = $slider['id'];
        $sliderID = 'layerslider_'.$id;
        $slides = $slider['data'];

        // Store generated output
        $lsInit = array();
        $lsContainer = array();
        $lsMarkup = array();
        $lsPlugins = array();

        // Include slider file
        if (is_array($slides)) {
            // Get phpQuery
            if (! defined('LS_PHPQUERY')) {
                libxml_use_internal_errors(true);
                include LS_ROOT_PATH.'/helpers/phpQuery.php';
            }

            $GLOBALS['lsPremiumNotice'] = array();

            include LS_ROOT_PATH.'/config/defaults.php';
            include LS_ROOT_PATH.'/includes/slider_markup_setup.php';
            include LS_ROOT_PATH.'/includes/slider_markup_html.php';
            include LS_ROOT_PATH.'/includes/slider_markup_init.php';

            // Admin notice when using premium features on non-activated sites
            if (! empty($GLOBALS['lsPremiumNotice'])) {
                array_unshift($lsContainer, self::generateErrorMarkup(
                    ls__('Premium features is available for preview purposes only.', 'LayerSlider'),
                    sprintf(ls__("We've detected that you're using premium features in this slider, but you have not yet activated your copy of LayerSlider. Premium features in your sliders will not be available for your visitors without activation. %sClick here to learn more%s. Detected features: %s", 'LayerSlider'), '<a href="https://support.kreaturamedia.com/docs/layersliderwp/documentation.html#activation" target="_blank">', '</a>', implode(', ', $GLOBALS['lsPremiumNotice'])),
                    'dashicons-star-filled',
                    'info'
                ));
            }



            $lsInit = implode('', $lsInit);
            $lsContainer = implode('', $lsContainer);
            $lsMarkup = implode('', $lsMarkup);
        }

        // Concatenate output
        if (ls_get_option('ls_concatenate_output', false)) {
            $lsInit = trim(preg_replace('/\s+/u', ' ', $lsInit));
            $lsContainer = trim(preg_replace('/\s+/u', ' ', $lsContainer));
            $lsMarkup = trim(preg_replace('/\s+/u', ' ', $lsMarkup));
        }

        // Bug fix in v5.4.0: Use self closing tag for <source>
        $lsMarkup = str_replace('></source>', ' />', $lsMarkup);

        // Return formatted data
        return array(
            'init' => $lsInit,
            'container' => $lsContainer,
            'markup' => $lsMarkup,
            'plugins' => array_unique($lsPlugins)
        );
    }


    public static function generateErrorMarkup($title = null, $description = null, $logo = 'dashicons-warning', $customClass = '')
    {

        if (! $title) {
            $title = ls__('LayerSlider encountered a problem while it tried to show your slider.', 'LayerSlider');
        }

        if (is_null($description)) {
            $description = ls__("Please make sure that you've used the right shortcode or method to insert the slider, and check if the corresponding slider exists and it wasn't deleted previously.", "LayerSlider");
        }

        if ($description) {
            $description .= '<br><br>';
        }

        $logo = $logo ? '<i class="lswp-notification-logo dashicons '.$logo.'"></i>' : '';
        $notice = ls__('Only you and other administrators can see this to take appropriate actions if necessary.', 'LayerSlider');

        $classes = array('error', 'info', 'scheduled', 'dead');
        if (! empty($customClass) && ! in_array($customClass, $classes)) {
            $customClass = '';
        }


        return '<div class="clearfix lswp-notification '.$customClass.'">
                    '.$logo.'
                    <strong>'.$title.'</strong>
                    <span>'.$description.'</span>
                    <small>
                        <i class="dashicons dashicons-lock"></i>
                        '.$notice.'
                    </small>
                </div>';
    }
}
