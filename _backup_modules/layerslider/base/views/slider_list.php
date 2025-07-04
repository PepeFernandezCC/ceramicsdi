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

// Get screen options
$lsScreenOptions = ls_get_option('ls-screen-options', '0');
$lsScreenOptions = ($lsScreenOptions == 0) ? array() : $lsScreenOptions;
$lsScreenOptions = is_array($lsScreenOptions) ? $lsScreenOptions : unserialize($lsScreenOptions);

// Defaults
if (!isset($lsScreenOptions['showTooltips'])) {
    $lsScreenOptions['showTooltips'] = 'true';
}
if (!isset($lsScreenOptions['numberOfSliders'])) {
    $lsScreenOptions['numberOfSliders'] = '25';
}

// Get current page
$curPage = (!empty(${'_GET'}['paged']) && is_numeric(${'_GET'}['paged'])) ? (int) ${'_GET'}['paged'] : 1;
// $curPage = ($curPage >= $maxPage) ? $maxPage : $curPage;

// Set filters
$userFilters = false;
$showAllSlider = false;

$urlParamFilter = 'published';
$urlParamOrder     = 'date_c';
$urlParamTerm     = '';

$filters = array(
    'orderby' => 'date_c',
    'order' => 'DESC',
    'page' => $curPage,
    'limit' => (int) $lsScreenOptions['numberOfSliders']
);

if (! empty(${'_GET'}['filter']) && ${'_GET'}['filter'] === 'all') {
    $userFilters = true;
    $showAllSlider = true;
    $urlParamFilter = htmlentities(${'_GET'}['filter']);
    $filters['exclude'] = array();
}

if (! empty(${'_GET'}['order'])) {
    $userFilters = true;
    $urlParamOrder = ${'_GET'}['order'];
    $filters['orderby'] = htmlentities(${'_GET'}['order']);

    if (${'_GET'}['order'] === 'name') {
        $filters['order'] = 'ASC';
    }
}

if (! empty(${'_GET'}['term'])) {
    $userFilters = true;
    $urlParamTerm = htmlentities(${'_GET'}['term']);
    $filters['where'] = "name LIKE '%".ls_esc_sql(${'_GET'}['term'])."%' OR slug LIKE '%".ls_esc_sql(${'_GET'}['term'])."%'";
}

// Find sliders
$sliders = LsSliders::find($filters);

// Pager
$maxItem = LsSliders::$count;
$maxPage = ceil($maxItem / (int) $lsScreenOptions['numberOfSliders']);
$maxPage = $maxPage ? $maxPage : 1;

$layout = ls_get_user_meta(ls_get_current_user_id(), 'ls-sliders-layout', true);

// Custom capability
$custom_capability = $custom_role = ls_get_option('layerslider_custom_capability', 'manage_options');
$default_capabilities = array('manage_network', 'manage_options', 'publish_pages', 'publish_posts', 'edit_posts');

if (in_array($custom_capability, $default_capabilities)) {
    $custom_capability = '';
} else {
    $custom_role = 'custom';
}


// Site activation
$code         = ls_get_option('layerslider-purchase-code', '');
$validity     = ls_get_option('layerslider-authorized-site', false);
$channel     = ls_get_option('layerslider-release-channel', 'stable');

// Purchase code
$codeFormatted = '';
if (!empty($code)) {
    $start = Tools::substr($code, 0, -6);
    $end = Tools::substr($code, -6);
    $codeFormatted = preg_replace("/[a-zA-Z0-9]/", '●', $start) . $end;
    $codeFormatted = str_replace('-', ' ', $codeFormatted);
}

// Google Fonts
$googleFonts         = ls_get_option('ls-google-fonts', array());
$googleFontScripts     = ls_get_option('ls-google-font-scripts', array('latin', 'latin-ext'));


// Template store data
$lsStoreUpdate         = ls_get_option('ls-store-last-updated', 0);
$lsStoreData         = ls_get_option('ls-store-data', false);
$lsStoreInterval     = ! empty($lsStoreData) ? DAY_IN_SECONDS : HOUR_IN_SECONDS;
$lsStoreLastViewed     = ls_get_user_meta(ls_get_current_user_id(), 'ls-store-last-viewed', true);

// Update last visited date
if (empty($lsStoreLastViewed)) {
    $lsStoreLastViewed = time();
    ls_update_user_meta(ls_get_current_user_id(), 'ls-store-last-viewed', date('Y-m-d'));
}

// Update store data
if ($lsStoreUpdate < time() - $lsStoreInterval) {
    // Refresh update time
    ls_update_option('ls-store-last-updated', time());
    $lsStoreUpdate = time();

    // Set update data
    $data = ls_remote_retrieve_body(ls_remote_get(sprintf('%ssliders/', LS_REPO_BASE_URL, LS_MARKETPLACE_ID)));
    $lsStoreData = ! empty($data) ? Tools::jsonDecode($data, true) : array();
    ls_update_option('ls-store-data', $lsStoreData, false);
}

$lsStoreHasUpdate = !empty($lsStoreData['last_updated']) && $lsStoreLastViewed < $lsStoreData['last_updated'];

$importSliderCount = !empty(${'_GET'}['sliderCount']) ? (int)${'_GET'}['sliderCount'] : 0;

// Notification messages
$notifications = array(

    'cacheEmpty' => ls__('Successfully emptied Creative Slider caches.', 'LayerSlider'),
    'updateStore' => ls__('Successfully updated the Template Store library.', 'LayerSlider'),

    'removeSelectError' => ls__('No sliders were selected to remove.', 'LayerSlider'),
    'removeSuccess' => ls__('The selected sliders were removed.', 'LayerSlider'),

    'duplicateSuccess' => ls__('The selected sliders were duplicated.', 'LayerSlider'),

    'deleteSelectError' => ls__('No sliders were selected.', 'LayerSlider'),
    'deleteSuccess' => ls__('The selected sliders were permanently deleted.', 'LayerSlider'),
    'mergeSelectError' => ls__('You need to select at least 2 sliders to merge them.', 'LayerSlider'),
    'mergeSuccess' => ls__('The selected items were merged together as a new slider.', 'LayerSlider'),
    'restoreSelectError' => ls__('No sliders were selected.', 'LayerSlider'),
    'restoreSuccess' => ls__('The selected sliders were restored.', 'LayerSlider'),

    'exportNotFound' => ls__('No sliders were found to export.', 'LayerSlider'),
    'exportSelectError' => ls__('No sliders were selected to export.', 'LayerSlider'),
    'exportZipError' => ls__('The PHP ZipArchive extension is required to import .zip files.', 'LayerSlider'),

    'importSelectError' => ls__('Choose a file to import sliders.', 'LayerSlider'),
    'importFailed' => ls__('The import file seems to be invalid or corrupted.', 'LayerSlider'),
    'importSuccess' => sprintf(ls_n('%d slider has been successfully imported.', '%d sliders has been successfully imported.', $importSliderCount, 'LayerSlider'), $importSliderCount),

    'permissionError' => ls__('Your account does not have the necessary permission you have chosen, and your settings have not been saved in order to prevent locking yourself out of the plugin.', 'LayerSlider'),
    'permissionSuccess' => ls__('Permission changes has been updated.', 'LayerSlider'),
    'googleFontsUpdated' => ls__('Your Google Fonts library has been updated.', 'LayerSlider'),
    'generalUpdated' => ls__('Your settings has been updated.', 'LayerSlider')
);
?>

<script type="text/javascript">
    window.lsSiteActivation = <?php echo ! empty($validity) ? 'true' : 'false' ?>;
</script>

<div id="ls-screen-options" class="metabox-prefs hidden">
    <div id="screen-options-wrap" class="hidden">
        <form id="ls-screen-options-form" method="post" novalidate>
            <?php ls_nonce_field('ls-save-screen-options'); ?>
            <h5><?php ls_e('Show on screen', 'LayerSlider') ?></h5>
            <label><input type="checkbox" name="showTooltips"<?php echo $lsScreenOptions['showTooltips'] == 'true' ? ' checked="checked"' : ''?>> <?php ls_e('Tooltips', 'LayerSlider') ?></label><br><br>

            <?php ls_e('Show me', 'LayerSlider') ?> <input type="number" name="numberOfSliders" min="8" step="4" value="<?php echo $lsScreenOptions['numberOfSliders'] ?>"> <?php ls_e('sliders per page', 'LayerSlider') ?>
            <button class="button"><?php ls_e('Apply', 'LayerSlider') ?></button>
        </form>
    </div>
    <div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
        <button type="button" id="show-settings-link" class="button show-settings" aria-controls="screen-options-wrap" aria-expanded="false"><?php ls_e('Screen Options', 'LayerSlider') ?></button>
    </div>
</div>


<div id="ls-guides" class="metabox-prefs">
    <div id="ls-guides-wrap" class="hidden">
        <h5><?php ls_e('Interactive guides coming soon!', 'LayerSlider') ?></h5>
        <p><?php ls_e("Interactive step-by-step tutorial guides will shortly arrive to help you get started using LayerSlider.", 'LayerSlider') ?></p>
    </div>
    <div id="show-guides-link-wrap" class="hide-if-no-js screen-meta-toggle">
        <button type="button" id="show-guides-link" class="button show-settings" aria-controls="ls-guides-wrap" aria-expanded="false"><?php ls_e('Guides', 'LayerSlider') ?></button>
    </div>
</div>

<!-- WP hack to place notification at the top of page -->
<div class="wrap ls-wp-hack">
    <h2></h2>

    <!-- Error messages -->
    <?php if (isset(${'_GET'}['message'])) : ?>
        <div class="ls-notification large <?php echo isset(${'_GET'}['error']) ? 'error' : 'updated' ?>">
            <div><?php echo $notifications[ ${'_GET'}['message'] ] ?></div>
        </div>
    <?php endif; ?>
    <!-- End of error messages -->
</div>

<div class="wrap" id="ls-list-page">
    <h2>Creative Slider - <?php ls_e('Your sliders', 'LayerSlider') ?></h2>

    <!-- Beta version -->
    <?php include LS_ROOT_PATH . '/templates/tmpl-beta-feedback.php'; ?>

    <!-- Add slider template -->
    <?php include LS_ROOT_PATH . '/templates/tmpl-add-slider-list.php'; ?>
    <?php include LS_ROOT_PATH . '/templates/tmpl-add-slider-grid.php'; ?>

    <!-- Import sample sliders template -->
    <?php include LS_ROOT_PATH . '/templates/tmpl-import-templates.php'; ?>

    <!-- Importing template -->
    <?php include LS_ROOT_PATH . '/templates/tmpl-importing.php'; ?>

    <!-- Import sample sliders template -->
    <?php include LS_ROOT_PATH . '/templates/tmpl-upload-sliders.php'; ?>

    <!-- Embed slider template -->
    <?php include LS_ROOT_PATH . '/templates/tmpl-embed-slider.php'; ?>

    <!-- Share sheet template -->
    <?php include LS_ROOT_PATH . '/templates/tmpl-share-sheet.php'; ?>



    <!-- Slider Filters -->
    <form method="get" id="ls-slider-filters">
        <input type="hidden" name="page" value="layerslider">
        <div class="layout">
            <a href="?page=layerslider&amp;action=layout&amp;type=list" data-help="<?php ls_e('List View', 'LayerSlider') ?>" class="dashicons dashicons-list-view"></a>
            <a href="?page=layerslider&amp;action=layout&amp;type=grid" data-help="<?php ls_e('Grid View', 'LayerSlider') ?>" class="dashicons dashicons-grid-view"></a>
        </div>
        <div class="filter">
            <?php ls_e('Show', 'LayerSlider') ?>
            <select name="filter">
                <option value="published"><?php ls_e('published', 'LayerSlider') ?></option>
                <option value="all" <?php echo $showAllSlider ? 'selected' : '' ?>><?php ls_e('all', 'LayerSlider') ?></option>
            </select>
            <?php ls_e('sliders', 'LayerSlider') ?>
        </div>
        <div class="sort">
            <?php ls_e('Sort by', 'LayerSlider') ?>
            <select name="order">
                <option value="name" <?php echo ($filters['orderby'] === 'name') ? 'selected' : '' ?>><?php ls_e('name', 'LayerSlider') ?></option>
                <option value="date_c" <?php echo ($filters['orderby'] === 'date_c') ? 'selected' : '' ?>><?php ls_e('date created', 'LayerSlider') ?></option>
                <option value="date_m" <?php echo ($filters['orderby'] === 'date_m') ? 'selected' : '' ?>><?php ls_e('date modified', 'LayerSlider') ?></option>
                <option value="schedule_start" <?php echo ($filters['orderby'] === 'schedule_start') ? 'selected' : '' ?>><?php ls_e('date scheduled', 'LayerSlider') ?></option>
            </select>
        </div>

        <div class="right">
            <input type="search" name="term" placeholder="<?php ls_e('Filter by name', 'LayerSlider') ?>" value="<?php echo ! empty(${'_GET'}['term']) ? htmlentities(${'_GET'}['term']) : '' ?>">
            <button class="button"><?php ls_e('Search', 'LayerSlider') ?></button>
        </div>
    </form>

    <form method="post" class="ls-slider-list-form">
        <input type="hidden" name="ls-bulk-action" value="1">
        <?php ls_nonce_field('bulk-action'); ?>

        <div>

        <!-- List View -->
        <?php if ($layout === 'list') : ?>
            <div class="ls-sliders-list">

                <a class="button import-templates <?php echo $lsStoreHasUpdate ? 'has-updates' : '' ?>" href="#" id="ls-import-samples-button">
                    <i class="import dashicons dashicons-star-filled"></i>
                    <span><?php ls_e('Template Store', 'LayerSlider') ?></span>
                </a>

                <a class="button" href="#" id="ls-import-button">
                    <i class="import dashicons dashicons-upload"></i>
                    <span><?php ls_e('Import Sliders', 'LayerSlider') ?></span>
                </a>

                <a class="button" href="#" id="ls-add-slider-button">
                    <i class="add dashicons dashicons-plus"></i>
                    <span><?php ls_e('Add New Slider', 'LayerSlider') ?></span>
                </a>

                <?php if (! empty($sliders)) : ?>
                    <?php $hooks = ls_get_hook_list(); ?>
                    <div class="ls-box">
                        <table>
                            <thead class="header">
                                <tr>
                                    <td></td>
                                    <td><?php ls_e('ID', 'LayerSlider') ?></td>
                                    <td class="preview-td"><?php ls_e('Slider preview', 'LayerSlider') ?></td>
                                    <td><?php ls_e('Name', 'LayerSlider') ?></td>
                                    <td><?php ls_e('Module Position', 'LayerSlider') ?></td>
                                    <td class="center"><?php ls_e('Shortcode', 'LayerSlider') ?></td>
                                    <td><?php ls_e('Slides', 'LayerSlider') ?></td>
                                    <td><?php ls_e('Created', 'LayerSlider') ?></td>
                                    <td><?php ls_e('Modified', 'LayerSlider') ?></td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($sliders as $key => $item) :
                                $class = ($item['flag_deleted'] == '1') ? ' dimmed' : '';
                                $preview = ls_apply_filters('ls_preview_for_slider', $item); ?>
                                <tr class="slider-item<?php echo $class ?>" data-id="<?php echo $item['id'] ?>" data-slug="<?php echo htmlentities($item['slug']) ?>">
                                    <td><input type="checkbox" name="sliders[]" value="<?php echo $item['id'] ?>"></td>
                                    <td><span><?php echo $item['id'] ?></span></td>
                                    <td class="preview-td">
                                        <a class="preview" style="background-image: url(<?php echo  ! empty($preview) ? $preview : LS_VIEWS_URL . 'img/admin/blank.gif' ?>);" href="?page=layerslider&action=edit&id=<?php echo $item['id'] ?>">

                                        </a>
                                    </td>
                                    <td class="name">
                                        <a href="?page=layerslider&action=edit&id=<?php echo $item['id'] ?>">
                                            <?php echo ls_apply_filters('ls_slider_title', _ss($item['name']), 40) ?>
                                        </a>
                                    </td>
                                    <td class="hook">
                                        <?php $hook = isset($item['data']['properties']['hook']) ? $item['data']['properties']['hook'] : '' ?>
                                        <input type="text" placeholder="<?php ls_e('- None -') ?>" class="km-combo-input" value="<?php echo $hook ?>" data-value="<?php echo $hook ?>" data-options='<?php echo $hooks ?>' data-hook="<?php echo $hook ?>" />
                                        <i class="dashicons dashicons-update ls-hook-update"></i>
                                    </td>
                                    <td class="center"><input type="text" class="ls-shortcode" value="[layerslider id=&quot;<?php echo !empty($item['slug']) ? $item['slug'] : $item['id'] ?>&quot;]" readonly></td>
                                    <td><span><?php echo isset($item['data']['layers']) ? count($item['data']['layers']) : 0 ?></span></td>
                                    <td><span><?php echo date('d/m/y', $item['date_c']) ?></span></td>
                                    <td><span><?php echo ls_human_time_diff($item['date_m']) ?> <?php ls_e('ago', 'LayerSlider') ?></span></td>
                                    <td class="center">
                                    <?php if (!$item['flag_deleted']) : ?>
                                        <span class="slider-actions dashicons dashicons-arrow-down-alt2"
                                            data-id="<?php echo $item['id'] ?>"
                                            data-slug="<?php echo htmlentities($item['slug']) ?>"
                                            data-export-url="<?php echo ls_nonce_url('?page=layerslider&action=export&id='.$item['id'], 'export-sliders') ?>"
                                            data-duplicate-url="<?php echo ls_nonce_url('?page=layerslider&action=duplicate&id='.$item['id'], 'duplicate_'.$item['id']) ?>"
                                            data-revisions-url="<?php echo ls_admin_url('admin.php?page=ls-revisions&id='.$item['id']) ?>"
                                            data-remove-url="<?php echo ls_nonce_url('?page=layerslider&action=remove&id='.$item['id'], 'remove_'.$item['id']) ?>">
                                        </span>
                                    <?php else : ?>
                                        <a href="<?php echo ls_nonce_url('?page=layerslider&action=restore&id='.$item['id'], 'restore_'.$item['id']) ?>">
                                            <span class="dashicons dashicons-backup" data-help="<?php ls_e('Restore removed slider', 'LayerSlider') ?>"></span>
                                        </a>
                                    <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Slider actions template -->
                        <div id="ls-slider-actions-template" class="ls-pointer ls-box ls-hidden">
                            <span class="ls-mce-arrow"></span>
                            <ul class="inner">
                                <li>
                                    <a href="#" class="embed">
                                        <i class="dashicons dashicons-plus"></i>
                                        <?php ls_e('Embed Slider', 'LayerSlider') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="dashicons dashicons-share-alt2"></i>
                                        <?php ls_e('Export', 'LayerSlider') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="dashicons dashicons-admin-page"></i>
                                        <?php ls_e('Duplicate', 'LayerSlider') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="dashicons dashicons-backup"></i>
                                        <?php ls_e('Revisions', 'LayerSlider') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="remove">
                                        <i class="dashicons dashicons-trash"></i>
                                        <?php ls_e('Remove', 'LayerSlider') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- End of Slider actions template -->
                    </div>
                <?php endif ?>
            </div>
        <?php else : ?>
            <!-- Slider List -->
            <div class="ls-sliders-grid clearfix">

                <div class="slider-item hero import-templates <?php echo $lsStoreHasUpdate ? 'has-updates' : '' ?>">
                    <div class="slider-item-wrapper">
                        <a href="#" id="ls-import-samples-button" class="preview import-templates <?php echo $lsStoreHasUpdate ? 'has-updates' : '' ?>">
                            <i class="import dashicons dashicons-star-filled"></i>
                            <span><?php ls_e('Template Store', 'LayerSlider') ?></span>
                        </a>
                    </div>
                </div>
                <div class="slider-item hero">
                    <div class="slider-item-wrapper">
                        <a href="#" id="ls-import-button" class="preview">
                            <i class="import dashicons dashicons-upload"></i>
                            <span><?php ls_e('Import Sliders', 'LayerSlider') ?></span>
                        </a>
                    </div>
                </div>
                <div class="slider-item hero">
                    <div class="slider-item-wrapper">
                        <a href="#" id="ls-add-slider-button" class="preview">
                            <i class="add dashicons dashicons-plus"></i>
                            <span><?php ls_e('Add New Slider', 'LayerSlider') ?></span>
                        </a>
                    </div>
                </div>
                <?php if (! empty($sliders)) : ?>
                    <?php foreach ($sliders as $key => $item) :
                        $class = ($item['flag_deleted'] == '1') ? 'dimmed' : '';
                        $preview = ls_apply_filters('ls_preview_for_slider', $item); ?>
                        <div class="slider-item <?php echo $class ?>">
                            <div class="slider-item-wrapper">
                                <input type="checkbox" name="sliders[]" class="checkbox ls-hover" value="<?php echo $item['id'] ?>">
                                <?php if (!$item['flag_deleted']) : ?>
                                    <span class="ls-hover slider-actions dashicons dashicons-arrow-down-alt2"></span>
                                <?php else : ?>
                                    <a href="<?php echo ls_nonce_url('?page=layerslider&action=restore&id='.$item['id'], 'restore_'.$item['id']) ?>">
                                        <span class="ls-hover dashicons dashicons-backup" data-help="<?php ls_e('Restore removed slider', 'LayerSlider') ?>"></span>
                                    </a>
                                <?php endif; ?>
                                <a class="preview" style="background-image: url(<?php echo  ! empty($preview) ? $preview : LS_VIEWS_URL . 'img/admin/blank.gif' ?>);" href="<?php echo ls_admin_url('?page=layerslider&action=edit&id='.$item['id']) ?>">
                                <?php if (empty($preview)) : ?>
                                    <div class="no-preview">
                                        <h5><?php ls_e('No Preview', 'LayerSlider') ?></h5>
                                        <small><?php ls_e('Previews are automatically generated from slide images in sliders.', 'LayerSlider') ?></small>
                                    </div>
                                <?php endif ?>
                                </a>
                                <div class="info">
                                    <div class="name">
                                        <?php echo ls_apply_filters('ls_slider_title', _ss($item['name']), 40) ?>
                                    </div>
                                </div>

                                <ul class="slider-actions-sheet ls-hidden">
                                    <li>
                                        <a href="#" class="embed" data-id="<?php echo $item['id'] ?>" data-slug="<?php echo htmlentities($item['slug']) ?>">
                                            <i class="dashicons dashicons-plus" data-hook="<?php echo isset($item['data']['properties']['hook']) ? $item['data']['properties']['hook'] : '' ?>"></i>
                                            <?php ls_e('Embed Slider', 'LayerSlider') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo ls_nonce_url('?page=layerslider&action=export&id='.$item['id'], 'export-sliders') ?>">
                                            <i class="dashicons dashicons-share-alt2"></i>
                                            <?php ls_e('Export', 'LayerSlider') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo ls_nonce_url('?page=layerslider&action=duplicate&id='.$item['id'], 'duplicate_'.$item['id']) ?>">
                                            <i class="dashicons dashicons-admin-page"></i>
                                            <?php ls_e('Duplicate', 'LayerSlider') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo ls_admin_url('admin.php?page=ls-revisions&id='.$item['id']) ?>">
                                            <i class="dashicons dashicons-backup"></i>
                                            <?php ls_e('Revisions', 'LayerSlider') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo ls_nonce_url('?page=layerslider&action=remove&id='.$item['id'], 'remove_'.$item['id']) ?>" class="remove">
                                            <i class="dashicons dashicons-trash"></i>
                                            <?php ls_e('Remove', 'LayerSlider') ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif ?>
            </div>
        <?php endif ?>

        <!-- No Slider Notification -->
        <?php if (empty($sliders)) : ?>
            <div id="ls-no-sliders">
                <div class="ls-notification-info">
                    <i class="dashicons dashicons-info"></i>
                    <?php if ($userFilters) : ?>
                        <span><?php echo sprintf(ls__('No sliders found with the current filters set. %sClick here%s to reset filters.', 'LayerSlider'), '<a href="?page=layerslider">', '</a>') ?></span>
                    <?php else : ?>
                        <span><?php echo sprintf(ls__('Add a new slider or check out the %sTemplate Store%s to get started using LayerSlider.', 'LayerSlider'), '<a href="#" class="ls-open-template-store"><i class="dashicons dashicons-star-filled"></i>', '</a>') ?></span>
                    <?php endif ?>
                </div>
            </div>
        <?php endif ?>
        </div>



        <?php if (! empty($sliders)) : ?>
            <div>
                <div class="ls-bulk-actions">
                    <select name="action">
                        <option value="0"><?php ls_e('Bulk Actions', 'LayerSlider') ?></option>
                        <option value="export"><?php ls_e('Export selected', 'LayerSlider') ?></option>
                        <option value="remove"><?php ls_e('Remove selected', 'LayerSlider') ?></option>
                        <option value="delete"><?php ls_e('Delete permanently', 'LayerSlider') ?></option>
                        <?php if ($showAllSlider) : ?>
                            <option value="restore"><?php ls_e('Restore selected', 'LayerSlider') ?></option>
                        <?php endif; ?>
                        <option value="merge"><?php ls_e('Merge selected as new', 'LayerSlider') ?></option>
                    </select>
                    <button class="button"><?php ls_e('Apply', 'LayerSlider') ?></button>
                </div>
                <div class="ls-pagination bottom">
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo sprintf(ls_n('%d slider', '%d sliders', $maxItem, 'LayerSlider'), $maxItem) ?></span>
                        <span class="pagination-links">
                            <a class="button first-page<?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="Go to the first page" href="admin.php?page=layerslider&amp;filter=<?php echo $urlParamFilter ?>&amp;term=<?php echo $urlParamTerm ?>&amp;order=<?php echo $urlParamOrder ?>">«</a>
                            <a class="button prev-page <?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="Go to the previous page" href="admin.php?page=layerslider&amp;paged=<?php echo ($curPage-1) ?>&amp;filter=<?php echo $urlParamFilter ?>&amp;term=<?php echo $urlParamTerm ?>&amp;order=<?php echo $urlParamOrder ?>">‹</a>

                            <span class="total-pages"><?php echo sprintf(ls__('%1$d of %2$d', 'LayerSlider'), $curPage, $maxPage) ?> </span>

                            <a class="button next-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="Go to the next page" href="admin.php?page=layerslider&amp;paged=<?php echo ($curPage+1) ?>&amp;filter=<?php echo $urlParamFilter ?>&amp;term=<?php echo $urlParamTerm ?>&amp;order=<?php echo $urlParamOrder ?>">›</a>
                            <a class="button last-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="Go to the last page" href="admin.php?page=layerslider&amp;paged=<?php echo $maxPage ?>&amp;filter=<?php echo $urlParamFilter ?>&amp;term=<?php echo $urlParamTerm ?>&amp;order=<?php echo $urlParamOrder ?>">»</a>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </form>


    <div class="km-tabs ls-plugin-settings-tabs">
        <a href="#" class="active"><?php ls_e('Google Fonts', 'LayerSlider') ?></a>
        <a href="#"><?php ls_e('Advanced', 'LayerSlider') ?></a>
    </div>
    <div class="km-tabs-content ls-plugin-settings">

        <!-- Google Fonts -->
        <div class="active">
            <figure><?php ls_e('Choose from hundreds of custom fonts faces provided by Google Fonts', 'LayerSlider') ?></figure>
            <form method="post" class="ls-box km-tabs-inner ls-google-fonts">
                <?php ls_nonce_field('save-google-fonts'); ?>
                <input type="hidden" name="ls-save-google-fonts" value="1">

                <!-- Google Fonts list -->
                <div class="inner">
                    <ul class="ls-font-list">
                        <li class="ls-hidden">
                            <a href="#" class="remove dashicons dashicons-dismiss" title="Remove this font"></a>
                            <input type="text" data-name="urlParams" readonly>
                            <input type="checkbox" data-name="onlyOnAdmin">
                            <?php ls_e('Load only on admin interface', 'LayerSlider') ?>
                        </li>
                        <?php if (is_array($googleFonts) && !empty($googleFonts)) : ?>
                            <?php foreach ($googleFonts as $item) : ?>
                                <li>
                                    <a href="#" class="remove dashicons dashicons-dismiss" title="Remove this font"></a>
                                    <input type="text" data-name="urlParams" value="<?php echo htmlspecialchars($item['param']) ?>" readonly>
                                    <input type="checkbox" data-name="onlyOnAdmin" <?php echo $item['admin'] ? ' checked="checked"' : '' ?>>
                                    <?php ls_e('Load only on admin interface', 'LayerSlider') ?>
                                </li>
                            <?php endforeach ?>
                        <?php else : ?>
                            <li class="ls-notice"><?php ls_e("You haven't added any Google font to your library yet.", "LayerSlider") ?></li>
                        <?php endif ?>
                    </ul>
                </div>
                <div class="inner ls-font-search">

                    <input type="text" placeholder="<?php ls_e('Enter a font name to add to your collection', 'LayerSlider') ?>">
                    <button class="button"><?php ls_e('Search', 'LayerSlider') ?></button>

                    <!-- Google Fonts search pointer -->
                    <div class="ls-box ls-pointer">
                        <h3 class="header"><?php ls_e('Choose a font family', 'LayerSlider') ?></h3>
                        <div class="fonts">
                            <ul class="inner"></ul>
                        </div>
                        <div class="variants">
                            <ul class="inner"></ul>
                            <div class="inner">
                                <button class="button add-font"><?php ls_e('Add font', 'LayerSlider') ?></button>
                                <button class="button right"><?php ls_e('Back to results', 'LayerSlider') ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Fonts search bar -->
                <div class="inner footer">
                    <button type="submit" class="button"><?php ls_e('Save changes', 'LayerSlider') ?></button>
                    <?php
                    $scripts = array(
                        'arabic' => ls__('Arabic', 'LayerSlider'),
                        'bengali' => ls__('Bengali', 'LayerSlider'),
                        'cyrillic' => ls__('Cyrillic', 'LayerSlider'),
                        'cyrillic-ext' => ls__('Cyrillic Extended', 'LayerSlider'),
                        'devanagari' => ls__('Devanagari', 'LayerSlider'),
                        'greek' => ls__('Greek', 'LayerSlider'),
                        'greek-ext' => ls__('Greek Extended', 'LayerSlider'),
                        'gujarati' => ls__('Gujarati', 'LayerSlider'),
                        'gurmukhi' => ls__('Gurmukhi', 'LayerSlider'),
                        'hebrew' => ls__('Hebrew', 'LayerSlider'),
                        'kannada' => ls__('Kannada', 'LayerSlider'),
                        'khmer' => ls__('Khmer', 'LayerSlider'),
                        'latin' => ls__('Latin', 'LayerSlider'),
                        'latin-ext' => ls__('Latin Extended', 'LayerSlider'),
                        'malayalam' => ls__('Malayalam', 'LayerSlider'),
                        'myanmar' => ls__('Myanmar', 'LayerSlider'),
                        'oriya' => ls__('Oriya', 'LayerSlider'),
                        'sinhala' => ls__('Sinhala', 'LayerSlider'),
                        'tamil' => ls__('Tamil', 'LayerSlider'),
                        'telugu' => ls__('Telugu', 'LayerSlider'),
                        'thai' => ls__('Thai', 'LayerSlider'),
                        'vietnamese' => ls__('Vietnamese', 'LayerSlider')
                    );
                    ?>
                    <div class="right">
                        <div>
                            <select>
                                <option><?php ls_e('Select new', 'LayerSlider') ?></option>
                                <?php foreach ($scripts as $key => $val) : ?>
                                    <option value="<?php echo $key ?>"><?php echo $val ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <ul class="ls-google-font-scripts">
                            <li class="ls-hidden">
                                <span></span>
                                <a href="#" class="dashicons dashicons-dismiss" title="<?php ls_e('Remove character set', 'LayerSlider') ?>"></a>
                                <input type="hidden" name="scripts[]" value="">
                            </li>
                            <?php if (!empty($googleFontScripts) && is_array($googleFontScripts)) : ?>
                                <?php foreach ($googleFontScripts as $item) : ?>
                                    <li>
                                        <span><?php echo $scripts[$item] ?></span>
                                        <a href="#" class="dashicons dashicons-dismiss" title="<?php ls_e('Remove character set', 'LayerSlider') ?>"></a>
                                        <input type="hidden" name="scripts[]" value="<?php echo $item ?>">
                                    </li>
                                <?php endforeach ?>
                            <?php else : ?>
                                <li>
                                    <span>Latin</span>
                                    <a href="#" class="dashicons dashicons-dismiss" title="<?php ls_e('Remove character set', 'LayerSlider') ?>"></a>
                                    <input type="hidden" name="scripts[]" value="latin">
                                </li>
                            <?php endif ?>
                        </ul>
                        <div><?php ls_e('Use character sets:', 'LayerSlider') ?></div>
                    </div>
                </div>

            </form>
        </div>

        <!-- Advanced -->
        <div class="ls-global-settings">
            <figure>
                <?php ls_e('Troubleshooting &amp; Advanced Settings', 'LayerSlider') ?>
                <span class="warning"><?php ls_e("Don't change these options without experience, incorrect settings might break your site.", "LayerSlider") ?></span>
            </figure>
            <form method="post" class="ls-box km-tabs-inner">
                <?php ls_nonce_field('save-advanced-settings'); ?>
                <input type="hidden" name="ls-save-advanced-settings">

                <table>
                    <tr class="ls-cache-options">
                        <td><?php ls_e('Use slider markup caching', 'LayerSlider') ?></td>
                        <td><input type="checkbox" name="use_cache" <?php echo ls_get_option('ls_use_cache', true) ? 'checked="checked"' : '' ?>></td>
                        <td class="desc">
                            <?php ls_e('Enabled caching can drastically increase the plugin performance and spare your server from unnecessary load.', 'LayerSlider') ?>
                            <a href="<?php echo ls_nonce_url('?page=layerslider&action=empty_caches', 'empty_caches') ?>" class="button button-small"><?php ls_e('Empty caches', 'LayerSlider') ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Save slide histories', 'LayerSlider') ?></td>
                        <td><input type="checkbox" name="save_history" <?php echo ls_get_option('ls_save_history', false) ? 'checked="checked"' : '' ?>></td>
                        <td class="desc"><?php ls_e("Save slide histories (undo, redo) with slider data. Isn't recommanded when post_max_size is small.", 'LayerSlider') ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Load uncompressed JS files', 'LayerSlider') ?></td>
                        <td><input type="checkbox" name="load_unpacked" <?php echo ls_get_option('ls_load_unpacked', false) ? 'checked="checked"' : '' ?>></td>
                        <td class="desc"><?php ls_e('Enable this option if you want to debug the code.', 'LayerSlider') ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Load FontAwesome library', 'LayerSlider') ?></td>
                        <td><input type="checkbox" name="load_fontawesome" <?php echo ls_get_option('ls_load_fontawesome', true) ? 'checked="checked"' : '' ?>></td>
                        <td class="desc"><?php ls_e('Disable this option if the FontAwesome library is already loaded by another addon, to prevent duplicated loading.', 'LayerSlider') ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Use multiple GreenSock (GSAP) compatibility mode', 'LayerSlider') ?></td>
                        <td><input type="checkbox" name="gsap_sandboxing" <?php echo ls_get_option('ls_gsap_sandboxing', false) ? 'checked="checked"' : '' ?>></td>
                        <td class="desc"><?php ls_e('Enabling multiple GreenSock compatibility mode can solve issues when other modules/theme are using another/outdated versions of this library.', 'LayerSlider') ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('RocketScript compatibility', 'LayerSlider') ?></td>
                        <td><input type="checkbox" name="rocketscript_ignore" <?php echo ls_get_option('ls_rocketscript_ignore', false) ? 'checked="checked"' : '' ?>></td>
                        <td class="desc"><?php ls_e('Enable this option to ignore CreativeSlider files by CloudFront’s Rocket Loader, which can help overcoming potential issues.', 'LayerSlider') ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Force load Origami plugin', 'LayerSlider') ?></td>
                        <td><input type="checkbox" name="force_load_origami" <?php echo ls_get_option('ls_force_load_origami', false) ? 'checked="checked"' : '' ?>></td>
                        <td class="desc"><?php ls_e('Enable this option if your theme does not load the Origami effect.', 'LayerSlider') ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Scripts priority', 'LayerSlider') ?></td>
                        <td><input name="scripts_priority" value="<?php echo ls_get_option('ls_scripts_priority', 50) ?>" placeholder="3"></td>
                        <td class="desc"><?php ls_e('Used to specify the order in which scripts are loaded. Lower numbers correspond with earlier execution.', 'LayerSlider') ?></td>
                    </tr>
                </table>
                <div class="footer">
                    <button type="submit" class="button"><?php ls_e('Save changes', 'LayerSlider') ?></button>
                </div>
            </form>
        </div>

    </div>

    <div class="columns clearfix">
        <!-- Suggested Modules -->
        <div class="third">
            <h2>
                <?php ls_e('Suggested modules for your store') ?>
                <a class="button dashicons-arrow-right"></a>
                <a class="button dashicons-arrow-left"></a>
            </h2>
            <div class="ls-box ls-product-banner ls-suggested-modules">
                <div class="inner active no-offer" style="display:none">
                    <img src="../modules/layerslider/views/img/admin/handshake.png" alt="Icon">
                    <h3><?php ls_e('Congratulations!') ?></h3>
                    <span class="dev"><?php ls_e('You have all of our suggested modules!') ?></span>
                </div>
            </div>
        </div>
        <!-- Kreatura Newsletter -->
        <div class="third">
            <h2><?php ls_e('Subscribe to our newsletter', 'LayerSlider') ?></h2>
            <div class="ls-box ls-product-banner ls-newsletter">
                <div class="inner">
                    <ul>
                        <li>
                            <i class="dashicons dashicons-megaphone"></i>
                            <strong><?php ls_e('Stay Updated', 'LayerSlider') ?></strong>
                            <small><?php ls_e('News about the latest features and other product info.', 'LayerSlider') ?></small>
                        </li>
                        <li>
                            <i class="dashicons dashicons-heart"></i>
                            <strong><?php ls_e('Sneak Peak on Product Updates', 'LayerSlider') ?></strong>
                            <small><?php ls_e('Access to all the cool new features before anyone else.', 'LayerSlider') ?></small>
                        </li>
                        <li>
                            <i class="dashicons dashicons-smiley"></i>
                            <strong><?php ls_e('Provide Feedback', 'LayerSlider') ?></strong>
                            <small><?php ls_e('Participate in various programs and help us improving LayerSlider.', 'LayerSlider') ?></small>
                        </li>
                    </ul>
                    <form method="post" action="https://creativeslider.webshopworks.com/#footer" target="_blank">
                        <input type="hidden" name="submitNewsletter" value="Subscribe">
                        <div class="email">
                            <input type="text" name="email" placeholder="<?php ls_e('Enter your email address', 'LayerSlider') ?>">
                            <button class="button"><?php ls_e('Subscribe', 'LayerSlider') ?></button>
                        </div>
                        <input type="hidden" name="action" value="0">
                    </form>
                </div>
            </div>
        </div>
        <!-- Product Support  -->
        <div class="third">
            <h2><?php ls_e('Product Support', 'LayerSlider') ?></h2>
            <div class="ls-box ls-product-banner ls-product-support">
                <div class="inner">
                    <ul>
                        <li>
                            <i class="dashicons dashicons-book"></i>
                            <strong><?php ls_e('Read the documentation', 'LayerSlider') ?></strong>
                            <small><?php ls_e('Get started with using LayerSlider.', 'LayerSlider') ?></small>
                        </li>
                        <li>
                            <i class="dashicons dashicons-sos"></i>
                            <strong><?php ls_e('Browse the FAQs', 'LayerSlider') ?></strong>
                            <small><?php ls_e('Find answers for common questions.', 'LayerSlider') ?></small>
                        </li>
                        <li>
                            <i class="dashicons <?php echo $validity ? 'dashicons-groups' : 'dashicons-lock' ?>"></i>
                            <strong><?php ls_e('Direct Support', 'LayerSlider') ?></strong>
                            <small><?php ls_e('Get in touch with our Support Team.', 'LayerSlider') ?></small>
                        </li>
                    </ul>
                    <a href="https://addons.prestashop.com/en/contact-us?id_product=19062" target="_blank" class="button"><?php ls_e('Contact the developer', 'LayerSlider') ?></a>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Help menu WP Pointer -->
<?php
if (ls_get_user_meta(ls_get_current_user_id(), 'layerslider_help_wp_pointer', true) != '1') {
    ls_add_user_meta(ls_get_current_user_id(), 'layerslider_help_wp_pointer', '1'); ?>
    <script type="text/javascript">

        // Help
        jQuery(document).ready(function() {
            jQuery('#contextual-help-link-wrap').pointer({
                pointerClass : 'ls-help-pointer',
                pointerWidth : 320,
                content: '<h3><?php ls_e('The documentation is here', 'LayerSlider') ?></h3><div class="inner"><?php ls_e('Open this help menu to quickly access to our online documentation.', 'LayerSlider') ?></div>',
                position: {
                    edge : 'top',
                    align : 'right'
                }
            }).pointer('open');
        });
    </script>
    <?php
} ?>
<script type="text/javascript">
    var lsScreenOptions = <?php echo Tools::jsonEncode($lsScreenOptions) ?>;
</script>
