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

ls_add_action('init', 'ls_register_form_actions');
function ls_register_form_actions()
{
    ls_add_action('save_post', 'layerslider_delete_caches');

    $controller = Tools::getValue('controller');
    $action = Tools::getValue('action');

    if (!empty($GLOBALS['employee']->id)) {
        // Sliders list layout
        if ('AdminLayerSlider' === $controller && 'layout' === $action) {
            $type = 'list' === ${'_GET'}['type'] ? 'list' : 'grid';
            ls_update_user_meta(ls_get_current_user_id(), 'ls-sliders-layout', $type);
            ls_redirect('?controller=AdminLayerSlider');
        }

        // Remove slider
        if ('AdminLayerSlider' === $controller && 'remove' === $action) {
            ls_add_action('admin_init', 'layerslider_removeslider');
        }

        // Restore slider
        if ('AdminLayerSlider' === $controller && 'restore' === $action) {
            ls_add_action('admin_init', 'layerslider_restoreslider');
        }

        // Duplicate slider
        if ('AdminLayerSlider' === $controller && 'duplicate' === $action) {
            ls_add_action('admin_init', 'layerslider_duplicateslider');
        }

        // Export slider
        if ('AdminLayerSlider' === $controller && 'export' === $action) {
            ${'_POST'}['sliders'] = [(int) ${'_GET'}['id']];
            ${'_POST'}['ls-export'] = true;
        }

        // Empty caches
        if ('AdminLayerSlider' === $controller && 'empty_caches' === $action) {
            ls_add_action('admin_init', 'layerslider_empty_caches');
        }
        /*
        // Update Library
        if ('AdminLayerSlider' === $controller && 'update_store' === $action) {
            ls_delete_option('ls-store-last-updated');
            ls_redirect('?controller=AdminLayerSlider&message=updateStore');
        }
        */
        // Slider list bulk actions
        if (isset(${'_POST'}['ls-bulk-action'])) {
            ls_add_action('admin_init', 'ls_sliders_bulk_action');
        }

        // Add new slider
        if (isset(${'_POST'}['ls-add-new-slider'])) {
            ls_add_action('admin_init', 'ls_add_new_slider');
        }

        // Google Fonts
        if (isset(${'_POST'}['ls-save-google-fonts'])) {
            ls_add_action('admin_init', 'ls_save_google_fonts');
        }

        // Advanced settings
        if (isset(${'_POST'}['ls-save-advanced-settings'])) {
            ls_add_action('admin_init', 'ls_save_advanced_settings');
        }

        // Import sliders
        if (isset(${'_POST'}['ls-import'])) {
            ls_add_action('admin_init', 'ls_import_sliders');
        }

        // Export sliders
        if (isset(${'_POST'}['ls-export'])) {
            ls_add_action('admin_init', 'ls_export_sliders');
        }

        // Revisions Options
        if (isset(${'_POST'}['ls-revisions-options'])) {
            ls_add_action('admin_init', 'ls_save_revisions_options');
        }

        // Revert slider
        if (isset(${'_POST'}['ls-revert-slider'])) {
            ls_add_action('admin_init', 'ls_revert_slider');
        }

        // Custom CSS editor
        if (isset(${'_POST'}['ls-user-css'])) {
            ls_add_action('admin_init', 'ls_save_user_css');
        }

        // Skin editor
        if (isset(${'_POST'}['ls-user-skins'])) {
            ls_add_action('admin_init', 'ls_save_user_skin');
        }

        // Transition builder
        if (isset(${'_POST'}['ls-user-transitions'])) {
            ls_add_action('admin_init', 'ls_save_user_transitions');
        }

        // AJAX functions
        ls_add_action('wp_ajax_ls_save_slider', 'ls_save_slider');
        // ls_add_action('wp_ajax_ls_import_bundled', 'ls_import_bundled');
        // ls_add_action('wp_ajax_ls_import_online', 'ls_import_online');
        ls_add_action('wp_ajax_ls_parse_date', 'ls_parse_date');
        ls_add_action('wp_ajax_ls_save_screen_options', 'ls_save_screen_options');
        ls_add_action('wp_ajax_ls_get_mce_sliders', 'ls_get_mce_sliders');
        ls_add_action('wp_ajax_ls_get_mce_slides', 'ls_get_mce_slides');
        ls_add_action('wp_ajax_ls_get_mce_layers', 'ls_get_mce_layers');
        ls_add_action('wp_ajax_ls_get_post_details', 'ls_get_post_details');
        ls_add_action('wp_ajax_ls_get_search_posts', 'ls_get_search_posts');
        // ls_add_action('wp_ajax_ls_get_taxonomies', 'ls_get_taxonomies');
        ls_add_action('wp_ajax_ls_upload_from_url', 'ls_upload_from_url');
        ls_add_action('wp_ajax_ls_store_opened', 'ls_store_opened');
    }
}

// Template store last viewed
function ls_store_opened()
{
    ls_update_user_meta(ls_get_current_user_id(), 'ls-store-last-viewed', date('Y-m-d'));
    exit;
}

function layerslider_delete_caches()
{
    try {
        LsCache::getInstance()->delete('ls-slider-data-*');
    } catch (Exception $ex) {
        // TODO
    }
}

function layerslider_empty_caches()
{
    layerslider_delete_caches();
    ls_redirect('?controller=AdminLayerSlider&message=cacheEmpty');
}

function ls_add_new_slider()
{
    $id = LsSliders::add(${'_POST'}['title']);
    ls_redirect('?controller=AdminLayerSlider&action=edit&id=' . $id . '&showsettings=1');
}

function ls_sliders_bulk_action()
{
    if ('export' === ${'_POST'}['action']) {
        // Export
        ls_export_sliders();
    } elseif ('remove' === ${'_POST'}['action']) {
        // Remove
        if (!empty(${'_POST'}['sliders']) && is_array(${'_POST'}['sliders'])) {
            foreach (${'_POST'}['sliders'] as $item) {
                LsSliders::remove((int) $item);
                ls_delete_transient('ls-slider-data-' . (int) $item);
            }
            ls_redirect('?controller=AdminLayerSlider&message=removeSuccess');
        } else {
            ls_redirect('?controller=AdminLayerSlider&message=removeSelectError&error=1');
        }
    } elseif ('delete' === ${'_POST'}['action']) {
        // Delete
        if (!empty(${'_POST'}['sliders']) && is_array(${'_POST'}['sliders'])) {
            foreach (${'_POST'}['sliders'] as $item) {
                LsSliders::delete((int) $item);
                LsRevisions::clear((int) $item);
                ls_delete_transient('ls-slider-data-' . (int) $item);
            }
            ls_redirect('?controller=AdminLayerSlider&message=deleteSuccess');
        } else {
            ls_redirect('?controller=AdminLayerSlider&message=deleteSelectError&error=1');
        }
    } elseif ('restore' === ${'_POST'}['action']) {
        // Restore
        if (!empty(${'_POST'}['sliders']) && is_array(${'_POST'}['sliders'])) {
            foreach (${'_POST'}['sliders'] as $item) {
                LsSliders::restore((int) $item);
            }
            ls_redirect('?controller=AdminLayerSlider&message=restoreSuccess');
        } else {
            ls_redirect('?controller=AdminLayerSlider&message=restoreSelectError&error=1');
        }
    } elseif ('merge' === ${'_POST'}['action']) {
        // Merge
        if (!isset(${'_POST'}['sliders'][1]) || !is_array(${'_POST'}['sliders'])) {
            // Error check
            ls_redirect('?controller=AdminLayerSlider&error=1&message=mergeSelectError');
        }

        if ($sliders = LsSliders::find(${'_POST'}['sliders'])) {
            $ids = [];
            $data = [];
            foreach ($sliders as $key => $item) {
                // Get IDs
                $ids[] = '#' . $item['id'];

                // Merge slides
                if (0 === $key) {
                    $data = $item['data'];
                } else {
                    $data['layers'] = array_merge($data['layers'], $item['data']['layers']);
                }
            }

            // Save as new
            $name = 'Merged sliders of ' . implode(', ', $ids);
            $data['properties']['title'] = $name;
            LsSliders::add($name, $data);
        }

        ls_redirect('?controller=AdminLayerSlider&message=mergeSuccess');
    }
}

function ls_save_google_fonts()
{
    // Build object to save
    $fonts = [];
    if (!empty(${'_POST'}['fontsData']) && is_array(${'_POST'}['fontsData'])) {
        foreach (${'_POST'}['fontsData'] as $key => $val) {
            if (!empty($val['urlParams'])) {
                $fonts[] = [
                    'param' => $val['urlParams'],
                    'admin' => isset($val['onlyOnAdmin']) ? true : false,
                ];
            }
        }
    }

    // Google Fonts character sets
    array_shift(${'_POST'}['scripts']);
    ls_update_option('ls-google-font-scripts', ${'_POST'}['scripts']);

    // Save & redirect back
    ls_update_option('ls-google-fonts', $fonts);
    ls_redirect('?controller=AdminLayerSlider&message=googleFontsUpdated');
}

function ls_save_advanced_settings()
{
    $options = [
        'use_cache',
        'load_unpacked',
        'load_fontawesome',
        'save_history',
        'gsap_sandboxing',
        'force_load_origami',
        'rocketscript_ignore',
    ];
    foreach ($options as $item) {
        ls_update_option('ls_' . $item, (int) array_key_exists($item, ${'_POST'}));
    }

    ls_update_option('ls_scripts_priority', (int) ${'_POST'}['scripts_priority']);

    ls_redirect('?controller=AdminLayerSlider&message=generalUpdated');
}

function ls_save_screen_options()
{
    ${'_POST'}['options'] = !empty(${'_POST'}['options']) ? ${'_POST'}['options'] : [];
    ls_update_option('ls-screen-options', ${'_POST'}['options']);
    exit;
}

function ls_get_mce_sliders()
{
    $sliders = LsSliders::find(['limit' => 100]);
    foreach ($sliders as $key => $item) {
        $sliders[$key]['preview'] = ls_apply_filters('ls_preview_for_slider', $item);
        $sliders[$key]['name'] = !empty($item['name']) ? htmlspecialchars($item['name']) : 'Unnamed';
    }

    exit(json_encode($sliders));
}

function ls_get_mce_slides()
{
    $sliderID = (int) ${'_GET'}['sliderID'];

    $slider = LsSliders::find($sliderID);
    $slider = $slider['data'];
    $slides = [];

    foreach ($slider['layers'] as $key => $slide) {
        if (!empty($slide['properties']['backgroundId'])) {
            $slides[$key]['preview'] = ls_apply_filters('ls_get_image', $slide['properties']['backgroundId'], $slide['properties']['background']);
        }

        $slides[$key]['name'] = !empty($slide['properties']['title']) ? htmlspecialchars(stripslashes($slide['properties']['title'])) : 'Slide #' . ($key + 1);
    }

    exit(json_encode($slides));
}

function ls_get_mce_layers()
{
    $sliderID = (int) ${'_GET'}['sliderID'];
    $slideIndex = (int) ${'_GET'}['slideIndex'];
    $layers = [];

    $slider = LsSliders::find($sliderID);
    $slider = $slider['data'];

    foreach ($slider['layers'][$slideIndex]['sublayers'] as $key => $layer) {
        // Parse embedded JSON data
        $layer['styles'] = !empty($layer['styles']) ? (object) json_decode(stripslashes($layer['styles']), true) : new stdClass();
        $layer['transition'] = !empty($layer['transition']) ? (object) json_decode(stripslashes($layer['transition']), true) : new stdClass();
        $layer['html'] = !empty($layer['html']) ? stripslashes($layer['html']) : '';

        // Add 'top', 'left' and 'wordwrap' to the styles object
        if (isset($layer['top'])) {
            $layer['styles']->top = $layer['top'];
            unset($layer['top']);
        }
        if (isset($layer['left'])) {
            $layer['styles']->left = $layer['left'];
            unset($layer['left']);
        }
        if (isset($layer['wordwrap'])) {
            $layer['styles']->wordwrap = $layer['wordwrap'];
            unset($layer['wordwrap']);
        }

        if (!empty($layer['transition']->showuntil)) {
            $layer['transition']->startatout = 'transitioninend + ' . $layer['transition']->showuntil; // @phpstan-ignore-line
            $layer['transition']->startatouttiming = 'transitioninend'; // @phpstan-ignore-line
            $layer['transition']->startatoutvalue = $layer['transition']->showuntil; // @phpstan-ignore-line
            unset($layer['transition']->showuntil);
        }

        if (!empty($layer['transition']->parallaxlevel)) {
            $layer['transition']->parallax = true; // @phpstan-ignore-line
        }

        // Custom attributes
        $layer['innerAttributes'] = !empty($layer['innerAttributes']) ? (object) $layer['innerAttributes'] : new stdClass();
        $layer['outerAttributes'] = !empty($layer['outerAttributes']) ? (object) $layer['outerAttributes'] : new stdClass();

        // v6.5.6: Convert old checkbox media settings to the new
        // select based options.
        if (isset($layer['transition']->controls)) {
            if (true === $layer['transition']->controls) {
                $layer['transition']->controls = 'auto';
            } elseif (false === $layer['transition']->controls) {
                $layer['transition']->controls = 'disabled';
            }
        }

        $layers[$key] = $layer;

        if (!empty($layer['imageId'])) {
            $layers[$key]['image'] = ls_apply_filters('ls_get_image', $layer['imageId'], $layer['image']);
        }

        if (!empty($layer['posterId'])) {
            $layers[$key]['poster'] = ls_apply_filters('ls_get_image', $layer['posterId'], $layer['poster']);
        }

        $layers[$key]['name'] = !empty($layer['subtitle']) ? Tools::substr(htmlspecialchars(stripslashes($layer['subtitle'])), 0, 32) : 'Layer #' . ($key + 1);
    }

    exit(json_encode($layers));
}

function ls_save_slider()
{
    // Vars
    $id = (int) ${'_POST'}['id'];
    $data = ${'_POST'}['sliderData'];

    // Parse slider settings
    $data['properties'] = json_decode(stripslashes(html_entity_decode($data['properties'])), true);

    // Parse slide data
    if (!empty($data['layers']) && is_array($data['layers'])) {
        foreach ($data['layers'] as $slideKey => $slideData) {
            $slideData = json_decode(stripslashes($slideData), true);

            if (!empty($slideData['sublayers'])) {
                foreach ($slideData['sublayers'] as $layerKey => $layerData) {
                    if (!empty($layerData['transition'])) {
                        $slideData['sublayers'][$layerKey]['transition'] = addslashes($layerData['transition']);
                    }

                    if (!empty($layerData['styles'])) {
                        $slideData['sublayers'][$layerKey]['styles'] = addslashes($layerData['styles']);
                    }
                }
            }

            $data['layers'][$slideKey] = $slideData;
        }
    }

    $title = $data['properties']['title'];
    $slug = !empty($data['properties']['slug']) ? $data['properties']['slug'] : '';

    // Relative URL
    if (isset($data['properties']['relativeurls'])) {
        $data = layerslider_convert_urls($data);
    }

    // WPML
    // if (function_exists('icl_register_string')) {
    //     layerslider_register_wpml_strings($id, $data);
    // }

    // Delete transient (if any) to
    // invalidate outdated data
    ls_delete_transient('ls-slider-data-' . $id);

    // Update the slider
    if (empty($id)) {
        $id = LsSliders::add($title, $data, $slug);
    } else {
        LsSliders::update($id, $title, $data, $slug);
    }

    // Revisions handling
    if (LsRevisions::$active) {
        $lastRevision = LsRevisions::last($id);

        if (empty($lastRevision->date_c) || $lastRevision->date_c < time() - 60 * LsRevisions::$interval) {
            LsRevisions::add($id, json_encode($data));

            if (LsRevisions::count($id) > LsRevisions::$limit) {
                LsRevisions::shift($id);
            }
        }
    }

    exit(json_encode(['status' => 'ok']));
}

function ls_save_revisions_options()
{
    ls_update_option('ls-revisions-enabled', (int) isset(${'_POST'}['ls-revisions-enabled']));
    ls_update_option('ls-revisions-limit', ${'_POST'}['ls-revisions-limit']);
    ls_update_option('ls-revisions-interval', ${'_POST'}['ls-revisions-interval']);

    if (empty(${'_POST'}['ls-revisions-enabled'])) {
        LsRevisions::truncate();
    }

    ls_redirect('?controller=AdminLayerSliderRevisions');
}

function ls_revert_slider()
{
    $sliderId = (int) ${'_POST'}['slider-id'];
    $revisionId = (int) ${'_POST'}['revision-id'];

    LsRevisions::revert($sliderId, $revisionId);

    ls_redirect('?controller=AdminLayerSlider&action=edit&id=' . $sliderId);
}

function ls_parse_date()
{
    exit(json_encode(['errorCount' => 1, 'dateStr' => '']));
}

/* ------------- Action to duplicate slider ----------- */

function layerslider_duplicateslider()
{
    // Check and get the ID
    $id = (int) ${'_GET'}['id'];
    if (!isset(${'_GET'}['id'])) {
        return;
    }

    // Get the original slider
    $slider = LsSliders::find((int) ${'_GET'}['id']);
    $data = $slider['data'];

    // Name check
    if (empty($data['properties']['title'])) {
        $data['properties']['title'] = 'Unnamed';
    }

    // Insert the duplicate
    $data['properties']['title'] .= ' copy';
    unset($data['properties']['hook']);
    unset($data['properties']['shop']);
    unset($data['properties']['lang']);
    unset($data['properties']['cats']);
    unset($data['properties']['pages']);
    unset($data['properties']['position']);
    LsSliders::add($data['properties']['title'], $data);

    // Success
    ls_redirect('?controller=AdminLayerSlider&message=duplicateSuccess');
}

/* -------------- Action to remove slider ------------- */

function layerslider_removeslider()
{
    // Check received data
    if (empty(${'_GET'}['id'])) {
        return false;
    }

    // Remove the slider
    LsSliders::remove((int) ${'_GET'}['id']);

    // Delete transient cache
    ls_delete_transient('ls-slider-data-' . (int) ${'_GET'}['id']);

    // Reload page
    ls_redirect('?controller=AdminLayerSlider&message=removeSuccess');
}

/* -------------- Action to restore slider ------------ */

function layerslider_restoreslider()
{
    // Check received data
    if (empty(${'_GET'}['id'])) {
        return false;
    }

    // Remove the slider
    LsSliders::restore((int) ${'_GET'}['id']);

    // Delete transient cache
    ls_delete_transient('ls-slider-data-' . (int) ${'_GET'}['id']);

    // Reload page
    if (!empty(${'_GET'}['ref'])) {
        ls_redirect(urldecode(${'_GET'}['ref']));
    } else {
        ls_redirect('?controller=AdminLayerSlider&message=restoreSuccess');
    }

    exit;
}

// IMPORT SLIDERS
// ------------------------------------------------------
function ls_import_sliders()
{
    // Check export file if any
    if (!is_uploaded_file($_FILES['import_file']['tmp_name'])) {
        ls_redirect('?controller=AdminLayerSlider&error=1&message=importSelectError');
    }

    include LS_ROOT_PATH . '/classes/class.ls.importutil.php';
    $import = new LsImportUtil($_FILES['import_file']['tmp_name'], $_FILES['import_file']['name']);

    if (!empty($import->lastImportId) && 1 === (int) $import->sliderCount) {
        // One slider, redirect to editor
        ls_redirect('?controller=AdminLayerSlider&action=edit&id=' . $import->lastImportId);
    } else {
        // Multiple sliders, redirect to slider list
        ls_redirect('?controller=AdminLayerSlider&message=importSuccess&sliderCount=' . $import->sliderCount);
    }
}

// EXPORT SLIDERS
// ------------------------------------------------------
function ls_export_sliders($sliderId = 0)
{
    // Get sliders
    if (!empty($sliderId)) {
        $sliders = LsSliders::find($sliderId);
    } elseif (isset(${'_POST'}['sliders'][0]) && -1 == ${'_POST'}['sliders'][0]) {
        $sliders = LsSliders::find(['limit' => 500]);
    } elseif (!empty(${'_POST'}['sliders'])) {
        $sliders = LsSliders::find(${'_POST'}['sliders']);
    } else {
        ls_redirect('?controller=AdminLayerSlider&error=1&message=exportSelectError');
    }

    // Check results
    if (empty($sliders)) {
        ls_redirect('?controller=AdminLayerSlider&error=1&message=exportNotFound');
    }

    // Check ZipArchive extension
    if (!class_exists('ZipArchive')) {
        ls_redirect('?controller=AdminLayerSlider&error=1&message=exportZipError');
    }

    include LS_ROOT_PATH . '/classes/class.ls.exportutil.php';
    $zip = new LsExportUtil();

    // Gather slider data
    $data = [];
    foreach ($sliders as $item) {
        // init PS specific props
        unset($item['data']['properties']['hook']);
        unset($item['data']['properties']['shop']);
        unset($item['data']['properties']['lang']);
        unset($item['data']['properties']['cats']);
        unset($item['data']['properties']['pages']);
        unset($item['data']['properties']['position']);

        // Gather Google Fonts used in slider
        $item['data']['googlefonts'] = $zip->fontsForSlider($item['data']);

        // Slider settings array for fallback mode
        $data[] = $item['data'];

        // If ZipArchive is available
        if (class_exists('ZipArchive')) {
            // Add slider folder and settings.json
            $name = empty($item['name']) ? 'slider_' . $item['id'] : $item['name'];
            $name = ls_sanitize_file_name($name);
            $zip->addSettings(json_encode($item['data']), $name);

            // Add images?
            if (!isset(${'_POST'}['skip_images'])) {
                $images = $zip->getImagesForSlider($item['data']);
                $images = $zip->getFSPaths($images);
                $zip->addImage($images, $name);
            }
        }
    }

    if (class_exists('ZipArchive')) {
        $zip->download();
    } else {
        $name = 'Creative Slider Export ' . date('Y-m-d') . ' at ' . date('H.i.s') . '.json';
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $name) . '"');
        exit(base64_encode(json_encode($data)));
    }
}

// TRANSITION BUILDER
// ------------------------------------------------------
function ls_save_user_css()
{
    // Get target file and content
    $file = _PS_MODULE_DIR_ . 'layerslider/views/css/custom.css';

    // Attempt to save changes
    if (is_writable(dirname($file))) {
        call_user_func('file_put_contents', $file, stripslashes(${'_POST'}['contents']));
        ls_redirect('?controller=AdminLayerSliderStyle&edited=1');

    // File isn't writable
    } else {
        ls_die(ls__("It looks like your files isn't writable, so PHP couldn't make any changes (CHMOD)."), ls__('Cannot write to file'), ['back_link' => true]);
    }
}

// SKIN EDITOR
// ------------------------------------------------------
function ls_save_user_skin()
{
    // Error checking
    if (empty(${'_POST'}['skin']) || false !== strpos(${'_POST'}['skin'], '..')) {
        ls_die(ls__("It looks like you haven't selected any skin to edit."), ls__('No skin selected.'), ['back_link' => true]);
    }

    // Get skin file and contents
    $skin = LsSources::getSkin(${'_POST'}['skin']);
    $file = $skin['file'];

    // Attempt to write the file
    if (is_writable($file)) {
        call_user_func('file_put_contents', $file, stripslashes(${'_POST'}['contents']));
        ls_redirect('?controller=AdminLayerSliderSkin&skin=' . $skin['handle'] . '&edited=1');
    } else {
        ls_die(ls__("It looks like your files isn't writable, so PHP couldn't make any changes (CHMOD)."), ls__('Cannot write to file'), ['back_link' => true]);
    }
}

// TRANSITION BUILDER
// ------------------------------------------------------
function ls_save_user_transitions()
{
    $custom_trs = _PS_MODULE_DIR_ . 'layerslider/views/js/custom.transitions.js';
    $data = 'var layerSliderCustomTransitions = ' . stripslashes(${'_POST'}['ls-transitions']) . ';';
    call_user_func('file_put_contents', $custom_trs, $data);
    exit('SUCCESS');
}

// --
function ls_get_post_details()
{
    $params = ${'_POST'}['params'];

    $queryArgs = [
        'post_status' => 'publish',
        'limit' => 50,
        'img_size' => null,
    ];

    if (!empty($params['post_orderby'])) {
        $queryArgs['orderby'] = $params['post_orderby'];
    }

    if (!empty($params['post_order'])) {
        $queryArgs['order'] = $params['post_order'];
    }

    if (!empty($params['post_type'][0])) {
        $queryArgs['post_type'] = $params['post_type'];
    }

    if (!empty($params['post_categories'][0])) {
        $queryArgs['category__in'] = $params['post_categories'];
    }

    if (!empty($params['post_tags'][0])) {
        $queryArgs['tag__in'] = $params['post_tags'];
    }

    if (!empty($params['post_tax_terms'])) {
        $queryArgs['img_size'] = $params['post_tax_terms'];
    }

    $posts = LsPosts::find($queryArgs)->getParsedObject();

    exit(json_encode($posts));
}

function layerslider_convert_urls($arr)
{
    // Global BG
    if (!empty($arr['properties']['backgroundimage']) && false !== strpos($arr['properties']['backgroundimage'], '://')) {
        $arr['properties']['backgroundimage'] = parse_url($arr['properties']['backgroundimage'], PHP_URL_PATH);
    }

    // YourLogo img
    if (!empty($arr['properties']['yourlogo']) && false !== strpos($arr['properties']['yourlogo'], '://')) {
        $arr['properties']['yourlogo'] = parse_url($arr['properties']['yourlogo'], PHP_URL_PATH);
    }

    if (!empty($arr['layers'])) {
        foreach ($arr['layers'] as $key => $slide) {
            // Layer BG
            if (false !== strpos($slide['properties']['background'], '://')) {
                $arr['layers'][$key]['properties']['background'] = parse_url($slide['properties']['background'], PHP_URL_PATH);
            }

            // Layer Thumb
            if (false !== strpos($slide['properties']['thumbnail'], '://')) {
                $arr['layers'][$key]['properties']['thumbnail'] = parse_url($slide['properties']['thumbnail'], PHP_URL_PATH);
            }

            // Image sublayers
            if (!empty($slide['sublayers'])) {
                foreach ($slide['sublayers'] as $subkey => $layer) {
                    if ('img' === $layer['media'] && false !== strpos($layer['image'], '://')) {
                        $arr['layers'][$key]['sublayers'][$subkey]['image'] = parse_url($layer['image'], PHP_URL_PATH);
                    }
                }
            }
        }
    }

    return $arr;
}
