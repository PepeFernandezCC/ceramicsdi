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

ls_enqueue_style('wp-specs', LS_VIEWS_URL . 'css/wp-specs.css', false, ${'this'}->module->version);

ls_enqueue_script('jquery-ui', LS_VIEWS_URL . 'js/jquery-ui.min.js', false, '1.12.1');
ls_enqueue_script('wp-specs', LS_VIEWS_URL . 'js/wp-specs.js', false, ${'this'}->module->version);

ls_magic_quotes(); // magic quotes fix

function ls_init_screen_meta()
{
    ?>
    <div id="screen-meta" class="metabox-prefs">
        <div id="contextual-help-wrap" class="hidden no-sidebar" tabindex="-1" aria-label="Contextual Help Tab">
            <div id="contextual-help-back"></div>
            <div id="contextual-help-columns">
                <div class="contextual-help-tabs"><ul></ul></div>
                <div class="contextual-help-tabs-wrap"></div>
            </div>
        </div>
    </div>
    <div id="screen-meta-links"></div>
    <?php
}
ls_add_action(ls_get_current_screen()->id, 'ls_init_screen_meta');

function init_admin_scripts()
{
    $link = $GLOBALS['context']->link;
    $mediamanagerurl = preg_replace('/^https?:/', '', $link->getAdminLink('AdminLayerSliderMedia'));
    $adminmodulesurl = preg_replace('/^https?:/', '', $link->getAdminLink('AdminModules') . '&configure=layerslider&module_name=layerslider');
    $ajaxurl = 'index.php?controller=AdminLayerSlider&ajax=1&token=' . $GLOBALS['ls_token'];
    $lsVersion = LS_PLUGIN_VERSION;
    $lsSaveHistory = ls_get_option('ls_save_history', false) ? 1 : 0;
    $userSettings = json_encode([
        'time' => time(),
        'uid' => $GLOBALS['employee']->id,
        'url' => __PS_BASE_URI__,
    ]);
    $_wpPluploadSettings = json_encode([
        'defaults' => [
            'multipart_params' => [
                '_wpnonce' => $GLOBALS['ls_token'],
            ],
        ],
    ]);
    echo "<script>
        mediamanagerurl = '$mediamanagerurl';
        admin_modules_link = '$adminmodulesurl';
        ajaxurl = '$ajaxurl';
        lsVersion = '$lsVersion';
        lsSaveHistory = $lsSaveHistory;
        userSettings = $userSettings;
        _wpPluploadSettings = $_wpPluploadSettings;
    </script>";
}
ls_add_action('admin_enqueue_scripts', 'init_admin_scripts');

ob_start();
require_once _PS_MODULE_DIR_ . 'layerslider/base/layerslider.php';

ls_do_action('init');

if (isset(${'_GET'}['ajax']) && isset($_REQUEST['action'])) {
    // handle AJAX requests
    if ('upload-attachment' == $_REQUEST['action']) {
        // handle AJAX image upload
        if (isset($_FILES['async-upload'])) {
            $name = $_FILES['async-upload']['name'];
            $destination = _PS_IMG_DIR_ . $name;
            $reldestination = _PS_IMG_ . $name;
            if (move_uploaded_file($_FILES['async-upload']['tmp_name'], $destination)) {
                $res = [
                    'data' => ['id' => '', 'sizes' => [], 'url' => $reldestination],
                    'success' => true,
                ];
                exit(json_encode($res));
            }
        }
        exit(json_encode(['success' => false]));
    }
    ls_do_action('wp_ajax_' . $_REQUEST['action']);
    exit(ob_get_clean());
}
ls_do_action('admin_menu');
ls_do_action('admin_init');

if ((int) _PS_VERSION_ >= 9) {
    echo "<script>document.getElementById('content').className = 'nobootstrap';</script>";
}
ls_do_action('admin_enqueue_scripts');
ls_do_action('ls_enqueue_scripts');
ls_do_action('admin_notices');
ls_do_action(ls_get_current_screen()->id);

${'this'}->content = ob_get_clean();
