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

// Get screen options
$lsScreenOptions = ls_get_option('ls-screen-options', []);

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
$urlParamOrder = 'date_c';
$urlParamTerm = '';

$filters = [
    'orderby' => 'date_c',
    'order' => 'DESC',
    'page' => $curPage,
    'limit' => (int) $lsScreenOptions['numberOfSliders'],
];

if (!empty(${'_GET'}['filter']) && 'all' === ${'_GET'}['filter']) {
    $userFilters = true;
    $showAllSlider = true;
    $urlParamFilter = htmlentities(${'_GET'}['filter']);
    $filters['exclude'] = [];
}

if (!empty(${'_GET'}['order'])) {
    $userFilters = true;
    $urlParamOrder = ${'_GET'}['order'];
    $filters['orderby'] = htmlentities(${'_GET'}['order']);

    if ('name' === ${'_GET'}['order']) {
        $filters['order'] = 'ASC';
    }
}

if (!empty(${'_GET'}['term'])) {
    $userFilters = true;
    $urlParamTerm = htmlentities(${'_GET'}['term']);
    $filters['term'] = Tools::getValue('term');
}

// Find sliders
$sliders = LsSliders::find($filters);

// Pager
$maxItem = LsSliders::$count;
$maxPage = ceil($maxItem / (int) $lsScreenOptions['numberOfSliders']);
$maxPage = $maxPage ? $maxPage : 1;

$layout = ls_get_user_meta(ls_get_current_user_id(), 'ls-sliders-layout', true);

// Google Fonts
$googleFonts = ls_get_option('ls-google-fonts', []);
$googleFontScripts = ls_get_option('ls-google-font-scripts', ['latin', 'latin-ext']);

$importSliderCount = !empty(${'_GET'}['sliderCount']) ? (int) ${'_GET'}['sliderCount'] : 0;

// Notification messages
$notifications = [
    'cacheEmpty' => ls__('Successfully emptied Creative Slider caches.'),
    'updateStore' => ls__('Successfully updated the Template Store library.'),

    'removeSelectError' => ls__('No sliders were selected to remove.'),
    'removeSuccess' => ls__('The selected sliders were removed.'),

    'duplicateSuccess' => ls__('The selected sliders were duplicated.'),

    'deleteSelectError' => ls__('No sliders were selected.'),
    'deleteSuccess' => ls__('The selected sliders were permanently deleted.'),
    'mergeSelectError' => ls__('You need to select at least 2 sliders to merge them.'),
    'mergeSuccess' => ls__('The selected items were merged together as a new slider.'),
    'restoreSelectError' => ls__('No sliders were selected.'),
    'restoreSuccess' => ls__('The selected sliders were restored.'),

    'exportNotFound' => ls__('No sliders were found to export.'),
    'exportSelectError' => ls__('No sliders were selected to export.'),
    'exportZipError' => ls__('The PHP ZipArchive extension is required to export .zip files.'),

    'importSelectError' => ls__('Choose a file to import sliders.'),
    'importFailed' => ls__('The import file seems to be invalid or corrupted.'),
    'importSuccess' => sprintf(ls_n('%d slider has been successfully imported.', '%d sliders has been successfully imported.', $importSliderCount), $importSliderCount),

    'permissionError' => ls__('Your account does not have the necessary permission you have chosen, and your settings have not been saved in order to prevent locking yourself out of the plugin.'),
    'permissionSuccess' => ls__('Permission changes has been updated.'),
    'googleFontsUpdated' => ls__('Your Google Fonts library has been updated.'),
    'generalUpdated' => ls__('Your settings has been updated.'),
];
?>

<div id="ls-screen-options" class="metabox-prefs hidden">
    <div id="screen-options-wrap" class="hidden">
        <form id="ls-screen-options-form" method="post" novalidate>
            <h5><?php ls_e('Show on screen'); ?></h5>
            <label><input type="checkbox" name="showTooltips"<?php echo 'true' == $lsScreenOptions['showTooltips'] ? ' checked="checked"' : ''; ?>> <?php ls_e('Tooltips'); ?></label><br><br>

            <?php ls_e('Show me'); ?> <input type="number" name="numberOfSliders" min="8" step="4" value="<?php echo $lsScreenOptions['numberOfSliders']; ?>"> <?php ls_e('sliders per page'); ?>
            <button class="button"><?php ls_e('Apply'); ?></button>
        </form>
    </div>
    <div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
        <button type="button" id="show-settings-link" class="button show-settings" aria-controls="screen-options-wrap" aria-expanded="false"><?php ls_e('Screen Options'); ?></button>
    </div>
</div>


<div id="ls-guides" class="metabox-prefs">
    <div id="ls-guides-wrap" class="hidden">
        <h5><?php ls_e('Interactive guides coming soon!'); ?></h5>
        <p><?php ls_e('Interactive step-by-step tutorial guides will shortly arrive to help you get started using Creative Slider.'); ?></p>
    </div>
    <div id="show-guides-link-wrap" class="hide-if-no-js screen-meta-toggle">
        <button type="button" id="show-guides-link" class="button show-settings" aria-controls="ls-guides-wrap" aria-expanded="false"><?php ls_e('Guides'); ?></button>
    </div>
</div>

<!-- WP hack to place notification at the top of page -->
<div class="wrap ls-wp-hack">
    <h2></h2>

    <!-- Error messages -->
    <?php if (isset(${'_GET'}['message'])) { ?>
        <div class="ls-notification large <?php echo isset(${'_GET'}['error']) ? 'error' : 'updated'; ?>">
            <div><?php echo $notifications[${'_GET'}['message']]; ?></div>
        </div>
    <?php } ?>
    <!-- End of error messages -->
</div>

<div class="wrap" id="ls-list-page">
    <h2>Creative Slider - <?php ls_e('Your sliders'); ?></h2>

    <!-- Add slider template -->
    <?php include LS_ROOT_PATH . '/templates/tmpl-add-slider-list.php'; ?>
    <?php include LS_ROOT_PATH . '/templates/tmpl-add-slider-grid.php'; ?>

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
            <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=layout&type=list'); ?>" data-help="<?php ls_e('List View'); ?>" class="dashicons dashicons-list-view"></a>
            <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=layout&type=grid'); ?>" data-help="<?php ls_e('Grid View'); ?>" class="dashicons dashicons-grid-view"></a>
        </div>
        <div class="filter">
            <?php ls_e('Show'); ?>
            <select name="filter">
                <option value="published"><?php ls_e('published'); ?></option>
                <option value="all" <?php echo $showAllSlider ? 'selected' : ''; ?>><?php ls_e('all'); ?></option>
            </select>
            <?php ls_e('sliders'); ?>
        </div>
        <div class="sort">
            <?php ls_e('Sort by'); ?>
            <select name="order">
                <option value="name" <?php echo ('name' === $filters['orderby']) ? 'selected' : ''; ?>><?php ls_e('name'); ?></option>
                <option value="date_c" <?php echo ('date_c' === $filters['orderby']) ? 'selected' : ''; ?>><?php ls_e('date created'); ?></option>
                <option value="date_m" <?php echo ('date_m' === $filters['orderby']) ? 'selected' : ''; ?>><?php ls_e('date modified'); ?></option>
                <option value="schedule_start" <?php echo ('schedule_start' === $filters['orderby']) ? 'selected' : ''; ?>><?php ls_e('date scheduled'); ?></option>
            </select>
        </div>

        <div class="right">
            <input type="search" name="term" placeholder="<?php ls_e('Filter by name'); ?>" value="<?php echo !empty(${'_GET'}['term']) ? htmlentities(${'_GET'}['term']) : ''; ?>">
            <button class="button"><?php ls_e('Search'); ?></button>
        </div>
    </form>

    <form method="post" class="ls-slider-list-form">
        <input type="hidden" name="ls-bulk-action" value="1">

        <div>

        <!-- List View -->
        <?php if ('list' === $layout) { ?>
            <div class="ls-sliders-list">

                <a class="button import-templates" href="#" id="ls-import-samples-button">
                    <i class="import dashicons dashicons-star-filled"></i>
                    <span><?php ls_e('Template Store'); ?></span>
                </a>

                <a class="button" href="#" id="ls-import-button">
                    <i class="import dashicons dashicons-upload"></i>
                    <span><?php ls_e('Import Sliders'); ?></span>
                </a>

                <a class="button" href="#" id="ls-add-slider-button">
                    <i class="add dashicons dashicons-plus"></i>
                    <span><?php ls_e('Add New Slider'); ?></span>
                </a>

                <?php if (!empty($sliders)) { ?>
                    <?php $hooks = ls_get_hook_list(); ?>
                    <div class="ls-box">
                        <table>
                            <thead class="header">
                                <tr>
                                    <td></td>
                                    <td><?php ls_e('ID'); ?></td>
                                    <td class="preview-td"><?php ls_e('Slider preview'); ?></td>
                                    <td><?php ls_e('Name'); ?></td>
                                    <td><?php ls_e('Module Position'); ?></td>
                                    <td class="center"><?php ls_e('Shortcode'); ?></td>
                                    <td><?php ls_e('Slides'); ?></td>
                                    <td><?php ls_e('Created'); ?></td>
                                    <td><?php ls_e('Modified'); ?></td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($sliders as $key => $item) {
                                $class = ('1' == $item['flag_deleted']) ? ' dimmed' : '';
                                $preview = ls_apply_filters('ls_preview_for_slider', $item);
                                ?>
                                <tr class="slider-item<?php echo $class; ?>" data-id="<?php echo $item['id']; ?>" data-slug="<?php echo htmlentities($item['slug']); ?>">
                                    <td><input type="checkbox" name="sliders[]" value="<?php echo $item['id']; ?>"></td>
                                    <td><span><?php echo $item['id']; ?></span></td>
                                    <td class="preview-td">
                                        <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=edit&id=' . $item['id']); ?>" class="preview" style="background-image: url(<?php echo !empty($preview) ? $preview : LS_VIEWS_URL . 'img/admin/blank.gif'; ?>);">
                                        </a>
                                    </td>
                                    <td class="name">
                                        <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=edit&id=' . $item['id']); ?>">
                                            <?php echo ls_apply_filters('ls_slider_title', stripslashes($item['name']), 40); ?>
                                        </a>
                                    </td>
                                    <td class="hook">
                                        <?php $hook = isset($item['data']['properties']['hook']) ? $item['data']['properties']['hook'] : ''; ?>
                                        <input type="text" placeholder="<?php ls_e('- None -'); ?>" class="km-combo-input" value="<?php echo $hook; ?>" data-value="<?php echo $hook; ?>" data-options="<?php echo htmlspecialchars($hooks, ENT_QUOTES); ?>" data-hook="<?php echo $hook; ?>" />
                                        <i class="dashicons dashicons-update ls-hook-update"></i>
                                    </td>
                                    <td class="center"><input type="text" class="ls-shortcode" value='[creativeslider id="<?php echo !empty($item['slug']) ? $item['slug'] : $item['id']; ?>"]' readonly></td>
                                    <td><span><?php echo isset($item['data']['layers']) ? count($item['data']['layers']) : 0; ?></span></td>
                                    <td><span><?php echo date('d/m/y', $item['date_c']); ?></span></td>
                                    <td><span><?php echo ls_human_time_diff($item['date_m']); ?> <?php ls_e('ago'); ?></span></td>
                                    <td class="center">
                                    <?php if (!$item['flag_deleted']) { ?>
                                        <span class="slider-actions dashicons dashicons-arrow-down-alt2"
                                            data-id="<?php echo $item['id']; ?>"
                                            data-slug="<?php echo htmlentities($item['slug']); ?>"
                                            data-export-url="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=export&id=' . $item['id']); ?>"
                                            data-duplicate-url="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=duplicate&id=' . $item['id']); ?>"
                                            data-revisions-url="<?php echo ls_admin_url('?controller=AdminLayerSliderRevisions&id=' . $item['id']); ?>"
                                            data-remove-url="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=remove&id=' . $item['id']); ?>">
                                        </span>
                                    <?php } else { ?>
                                        <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=restore&id=' . $item['id']); ?>">
                                            <span class="dashicons dashicons-backup" data-help="<?php ls_e('Restore removed slider'); ?>"></span>
                                        </a>
                                    <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <!-- Slider actions template -->
                        <div id="ls-slider-actions-template" class="ls-pointer ls-box ls-hidden">
                            <span class="ls-mce-arrow"></span>
                            <ul class="inner">
                                <li>
                                    <a href="#" class="embed">
                                        <i class="dashicons dashicons-plus"></i>
                                        <?php ls_e('Embed Slider'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="dashicons dashicons-share-alt2"></i>
                                        <?php ls_e('Export'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="dashicons dashicons-admin-page"></i>
                                        <?php ls_e('Duplicate'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="dashicons dashicons-backup"></i>
                                        <?php ls_e('Revisions'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="remove">
                                        <i class="dashicons dashicons-trash"></i>
                                        <?php ls_e('Remove'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- End of Slider actions template -->
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <!-- Slider List -->
            <div class="ls-sliders-grid clearfix">

                <div class="slider-item hero import-templates">
                    <div class="slider-item-wrapper">
                        <a href="#" id="ls-import-samples-button" class="preview import-templates">
                            <i class="import dashicons dashicons-star-filled"></i>
                            <span><?php ls_e('Template Store'); ?></span>
                        </a>
                    </div>
                </div>
                <div class="slider-item hero">
                    <div class="slider-item-wrapper">
                        <a href="#" id="ls-import-button" class="preview">
                            <i class="import dashicons dashicons-upload"></i>
                            <span><?php ls_e('Import Sliders'); ?></span>
                        </a>
                    </div>
                </div>
                <div class="slider-item hero">
                    <div class="slider-item-wrapper">
                        <a href="#" id="ls-add-slider-button" class="preview">
                            <i class="add dashicons dashicons-plus"></i>
                            <span><?php ls_e('Add New Slider'); ?></span>
                        </a>
                    </div>
                </div>
                <?php if (!empty($sliders)) { ?>
                    <?php foreach ($sliders as $key => $item) {
                        $class = ('1' == $item['flag_deleted']) ? 'dimmed' : '';
                        $preview = ls_apply_filters('ls_preview_for_slider', $item);
                        ?>
                        <div class="slider-item <?php echo $class; ?>">
                            <div class="slider-item-wrapper">
                                <input type="checkbox" name="sliders[]" class="checkbox ls-hover" value="<?php echo $item['id']; ?>">
                                <?php if (!$item['flag_deleted']) { ?>
                                    <span class="ls-hover slider-actions dashicons dashicons-arrow-down-alt2"></span>
                                <?php } else { ?>
                                    <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=restore&id=' . $item['id']); ?>">
                                        <span class="ls-hover dashicons dashicons-backup" data-help="<?php ls_e('Restore removed slider'); ?>"></span>
                                    </a>
                                <?php } ?>
                                <a class="preview" style="background-image: url(<?php echo !empty($preview) ? $preview : LS_VIEWS_URL . 'img/admin/blank.gif'; ?>);" href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=edit&id=' . $item['id']); ?>">
                                <?php if (empty($preview)) { ?>
                                    <div class="no-preview">
                                        <h5><?php ls_e('No Preview'); ?></h5>
                                        <small><?php ls_e('Previews are automatically generated from slide images in sliders.'); ?></small>
                                    </div>
                                <?php } ?>
                                </a>
                                <div class="info">
                                    <div class="name">
                                        <?php echo ls_apply_filters('ls_slider_title', stripslashes($item['name']), 40); ?>
                                    </div>
                                </div>

                                <ul class="slider-actions-sheet ls-hidden">
                                    <li>
                                        <a href="#" class="embed" data-id="<?php echo $item['id']; ?>" data-slug="<?php echo htmlentities($item['slug']); ?>">
                                            <i class="dashicons dashicons-plus" data-hook="<?php echo isset($item['data']['properties']['hook']) ? $item['data']['properties']['hook'] : ''; ?>"></i>
                                            <?php ls_e('Embed Slider'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=export&id=' . $item['id']); ?>">
                                            <i class="dashicons dashicons-share-alt2"></i>
                                            <?php ls_e('Export'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=duplicate&id=' . $item['id']); ?>">
                                            <i class="dashicons dashicons-admin-page"></i>
                                            <?php ls_e('Duplicate'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo ls_admin_url('?controller=AdminLayerSliderRevisions&id=' . $item['id']); ?>">
                                            <i class="dashicons dashicons-backup"></i>
                                            <?php ls_e('Revisions'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=remove&id=' . $item['id']); ?>" class="remove">
                                            <i class="dashicons dashicons-trash"></i>
                                            <?php ls_e('Remove'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>

        <!-- No Slider Notification -->
        <?php if (empty($sliders)) { ?>
            <div id="ls-no-sliders">
                <div class="ls-notification-info">
                    <i class="dashicons dashicons-info"></i>
                    <?php if ($userFilters) { ?>
                        <span><?php printf(ls__('No sliders found with the current filters set. %sClick here%s to reset filters.'), '<a href="' . ls_admin_url('?controller=AdminLayerSlider') . '">', '</a>'); ?></span>
                    <?php } else { ?>
                        <span><?php printf(ls__('Add a new slider or check out the %sTemplate Store%s to get started using Creative Slider.'), '<a href="#" class="ls-open-template-store"><i class="dashicons dashicons-star-filled"></i>', '</a>'); ?></span>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        </div>



        <?php if (!empty($sliders)) { ?>
            <div>
                <div class="ls-bulk-actions">
                    <select name="action">
                        <option value="0"><?php ls_e('Bulk Actions'); ?></option>
                        <option value="export"><?php ls_e('Export selected'); ?></option>
                        <option value="remove"><?php ls_e('Remove selected'); ?></option>
                        <option value="delete"><?php ls_e('Delete permanently'); ?></option>
                        <?php if ($showAllSlider) { ?>
                            <option value="restore"><?php ls_e('Restore selected'); ?></option>
                        <?php } ?>
                        <option value="merge"><?php ls_e('Merge selected as new'); ?></option>
                    </select>
                    <button class="button"><?php ls_e('Apply'); ?></button>
                </div>
                <div class="ls-pagination bottom">
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php printf(ls_n('%d slider', '%d sliders', $maxItem), $maxItem); ?></span>
                        <span class="pagination-links">
                            <a class="button first-page<?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="Go to the first page" href="<?php echo ls_admin_url('?controller=AdminLayerSlider&filter=' . $urlParamFilter . '&term=' . $urlParamTerm . '&order=' . $urlParamOrder); ?>">«</a>
                            <a class="button prev-page <?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="Go to the previous page" href="<?php echo ls_admin_url('?controller=AdminLayerSlider&paged=' . ($curPage - 1) . '&filter=' . $urlParamFilter . '&term=' . $urlParamTerm . '&order=' . $urlParamOrder); ?>">‹</a>

                            <span class="total-pages"><?php printf(ls__('%1$d of %2$d'), $curPage, $maxPage); ?> </span>

                            <a class="button next-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="Go to the next page" href="<?php echo ls_admin_url('?controller=AdminLayerSlider&paged=' . ($curPage + 1) . '&filter=' . $urlParamFilter . '&term=' . $urlParamTerm . '&order=' . $urlParamOrder); ?>">›</a>
                            <a class="button last-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="Go to the last page" href="<?php echo ls_admin_url('?controller=AdminLayerSlider&paged=' . $maxPage . '&filter=' . $urlParamFilter . '&term=' . $urlParamTerm . '&order=' . $urlParamOrder); ?>">»</a>
                        </span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </form>


    <div class="km-tabs ls-plugin-settings-tabs">
        <a href="#" class="active"><?php ls_e('Google Fonts'); ?></a>
        <a href="#"><?php ls_e('Advanced'); ?></a>
        <a href="#" id="license"><?php ls_e('License'); ?></a>
    </div>
    <div class="km-tabs-content ls-plugin-settings">

        <!-- Google Fonts -->
        <div class="active">
            <figure><?php ls_e('Choose from hundreds of custom fonts faces provided by Google Fonts'); ?></figure>
            <form method="post" class="ls-box km-tabs-inner ls-google-fonts">
                <input type="hidden" name="ls-save-google-fonts" value="1">

                <!-- Google Fonts list -->
                <div class="inner">
                    <ul class="ls-font-list">
                        <li class="ls-hidden">
                            <a href="#" class="remove dashicons dashicons-dismiss" title="Remove this font"></a>
                            <input type="text" data-name="urlParams" readonly>
                            <input type="checkbox" data-name="onlyOnAdmin">
                            <?php ls_e('Load only on admin interface'); ?>
                        </li>
                        <?php if (is_array($googleFonts) && !empty($googleFonts)) { ?>
                            <?php foreach ($googleFonts as $item) { ?>
                                <li>
                                    <a href="#" class="remove dashicons dashicons-dismiss" title="Remove this font"></a>
                                    <input type="text" data-name="urlParams" value="<?php echo htmlspecialchars($item['param']); ?>" readonly>
                                    <input type="checkbox" data-name="onlyOnAdmin" <?php echo $item['admin'] ? ' checked="checked"' : ''; ?>>
                                    <?php ls_e('Load only on admin interface'); ?>
                                </li>
                            <?php } ?>
                        <?php } else { ?>
                            <li class="ls-notice"><?php ls_e("You haven't added any Google font to your library yet."); ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="inner ls-font-search">

                    <input type="text" placeholder="<?php ls_e('Enter a font name to add to your collection'); ?>">
                    <button class="button"><?php ls_e('Search'); ?></button>

                    <!-- Google Fonts search pointer -->
                    <div class="ls-box ls-pointer">
                        <h3 class="header"><?php ls_e('Choose a font family'); ?></h3>
                        <div class="fonts">
                            <ul class="inner"></ul>
                        </div>
                        <div class="variants">
                            <ul class="inner"></ul>
                            <div class="inner">
                                <button class="button add-font"><?php ls_e('Add font'); ?></button>
                                <button class="button right"><?php ls_e('Back to results'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Fonts search bar -->
                <div class="inner footer">
                    <button type="submit" class="button"><?php ls_e('Save changes'); ?></button>
                    <?php $scripts = [
                        'arabic' => ls__('Arabic'),
                        'bengali' => ls__('Bengali'),
                        'cyrillic' => ls__('Cyrillic'),
                        'cyrillic-ext' => ls__('Cyrillic Extended'),
                        'devanagari' => ls__('Devanagari'),
                        'greek' => ls__('Greek'),
                        'greek-ext' => ls__('Greek Extended'),
                        'gujarati' => ls__('Gujarati'),
                        'gurmukhi' => ls__('Gurmukhi'),
                        'hebrew' => ls__('Hebrew'),
                        'kannada' => ls__('Kannada'),
                        'khmer' => ls__('Khmer'),
                        'latin' => ls__('Latin'),
                        'latin-ext' => ls__('Latin Extended'),
                        'malayalam' => ls__('Malayalam'),
                        'myanmar' => ls__('Myanmar'),
                        'oriya' => ls__('Oriya'),
                        'sinhala' => ls__('Sinhala'),
                        'tamil' => ls__('Tamil'),
                        'telugu' => ls__('Telugu'),
                        'thai' => ls__('Thai'),
                        'vietnamese' => ls__('Vietnamese'),
                    ]; ?>
                    <div class="right">
                        <div>
                            <select>
                                <option><?php ls_e('Select new'); ?></option>
                                <?php foreach ($scripts as $key => $val) { ?>
                                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <ul class="ls-google-font-scripts">
                            <li class="ls-hidden">
                                <span></span>
                                <a href="#" class="dashicons dashicons-dismiss" title="<?php ls_e('Remove character set'); ?>"></a>
                                <input type="hidden" name="scripts[]" value="">
                            </li>
                            <?php if (!empty($googleFontScripts) && is_array($googleFontScripts)) { ?>
                                <?php foreach ($googleFontScripts as $item) { ?>
                                    <li>
                                        <span><?php echo $scripts[$item]; ?></span>
                                        <a href="#" class="dashicons dashicons-dismiss" title="<?php ls_e('Remove character set'); ?>"></a>
                                        <input type="hidden" name="scripts[]" value="<?php echo $item; ?>">
                                    </li>
                                <?php } ?>
                            <?php } else { ?>
                                <li>
                                    <span>Latin</span>
                                    <a href="#" class="dashicons dashicons-dismiss" title="<?php ls_e('Remove character set'); ?>"></a>
                                    <input type="hidden" name="scripts[]" value="latin">
                                </li>
                            <?php } ?>
                        </ul>
                        <div><?php ls_e('Use character sets:'); ?></div>
                    </div>
                </div>

            </form>
        </div>

        <!-- Advanced -->
        <div class="ls-global-settings">
            <figure>
                <?php ls_e('Troubleshooting &amp; Advanced Settings'); ?>
                <span class="warning"><?php ls_e("Don't change these options without experience, incorrect settings might break your site."); ?></span>
            </figure>
            <form method="post" class="ls-box km-tabs-inner">
                <input type="hidden" name="ls-save-advanced-settings">

                <table>
                    <tr class="ls-cache-options">
                        <td><?php ls_e('Use slider markup caching'); ?></td>
                        <td><input type="checkbox" name="use_cache" <?php echo ls_get_option('ls_use_cache', true) ? 'checked="checked"' : ''; ?>></td>
                        <td class="desc">
                            <?php ls_e('Enabled caching can drastically increase the plugin performance and spare your server from unnecessary load.'); ?>
                            <a href="<?php echo ls_admin_url('?controller=AdminLayerSlider&action=empty_caches'); ?>" class="button button-small"><?php ls_e('Empty caches'); ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Save slide histories'); ?></td>
                        <td><input type="checkbox" name="save_history" <?php echo ls_get_option('ls_save_history', false) ? 'checked="checked"' : ''; ?>></td>
                        <td class="desc"><?php ls_e("Save slide histories (undo, redo) with slider data. Isn't recommanded when post_max_size is small."); ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Load uncompressed JS files'); ?></td>
                        <td><input type="checkbox" name="load_unpacked" <?php echo ls_get_option('ls_load_unpacked', false) ? 'checked="checked"' : ''; ?>></td>
                        <td class="desc"><?php ls_e('Enable this option if you want to debug the code.'); ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Load FontAwesome library'); ?></td>
                        <td><input type="checkbox" name="load_fontawesome" <?php echo ls_get_option('ls_load_fontawesome', true) ? 'checked="checked"' : ''; ?>></td>
                        <td class="desc"><?php ls_e('Disable this option if the FontAwesome library is already loaded by another addon, to prevent duplicated loading.'); ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Use multiple GreenSock (GSAP) compatibility mode'); ?></td>
                        <td><input type="checkbox" name="gsap_sandboxing" <?php echo ls_get_option('ls_gsap_sandboxing', false) ? 'checked="checked"' : ''; ?>></td>
                        <td class="desc"><?php ls_e('Enabling multiple GreenSock compatibility mode can solve issues when other modules/theme are using another/outdated versions of this library.'); ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('RocketScript compatibility'); ?></td>
                        <td><input type="checkbox" name="rocketscript_ignore" <?php echo ls_get_option('ls_rocketscript_ignore', false) ? 'checked="checked"' : ''; ?>></td>
                        <td class="desc"><?php ls_e('Enable this option to ignore CreativeSlider files by CloudFront’s Rocket Loader, which can help overcoming potential issues.'); ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Force load Origami plugin'); ?></td>
                        <td><input type="checkbox" name="force_load_origami" <?php echo ls_get_option('ls_force_load_origami', false) ? 'checked="checked"' : ''; ?>></td>
                        <td class="desc"><?php ls_e('Enable this option if your theme does not load the Origami effect.'); ?></td>
                    </tr>
                    <tr>
                        <td><?php ls_e('Scripts priority'); ?></td>
                        <td><input name="scripts_priority" value="<?php echo ls_get_option('ls_scripts_priority', 50); ?>" placeholder="3"></td>
                        <td class="desc"><?php ls_e('Used to specify the order in which scripts are loaded. Lower numbers correspond with earlier execution.'); ?></td>
                    </tr>
                </table>
                <div class="footer">
                    <button type="submit" class="button"><?php ls_e('Save changes'); ?></button>
                </div>
            </form>
        </div>

        <!-- License -->
        <div>
            <form method="post" class="ls-box km-tabs-inner ls-license">
            <?php if (!Configuration::getGlobalValue('LS_LICENSE')) { ?>
                <figure>
                    <h3><?php ls_e('Activate License'); ?></h3>
                    <p><?php ls_e('Please activate your license to get unlimited access to the template library.'); ?></p>
                </figure>
                <div class="footer">
                    <button class="button" type="submit" name="action" value="activate"><i class="icon-file-text"></i>&ensp;<?php ls_e('Activate'); ?></button>
                </div>
            <?php } else { ?>
                <figure>
                    <h3><?php ls_e('License Status:'); ?> <span style="color: #249533"><?php ls_e('Active'); ?></span></h3>
                    <p><?php ls_e('Your website is activated. Want to activate this website by a different license?'); ?></p>
                </figure>
                <div class="footer">
                    <button class="button" type="submit" name="action" value="activate"><i class="icon-file-text"></i>&ensp;<?php ls_e('Switch License'); ?></button>
                </div>
            <?php } ?>
            </form>
        </div>

    </div>

    <div class="columns clearfix">
        <!-- Suggested Modules -->
        <div class="third">
            <h2>
                <?php ls_e('Suggested modules for your store'); ?>
                <a class="button dashicons-arrow-right"></a>
                <a class="button dashicons-arrow-left"></a>
            </h2>
            <div class="ls-box ls-product-banner ls-suggested-modules">
                <div class="inner active no-offer" style="display:none">
                    <img src="../modules/layerslider/views/img/admin/handshake.png" alt="Icon">
                    <h3><?php ls_e('Congratulations!'); ?></h3>
                    <span class="dev"><?php ls_e('You have all of our suggested modules!'); ?></span>
                </div>
            </div>
        </div>
        <!-- Kreatura Newsletter -->
        <div class="third">
            <h2><?php ls_e('Subscribe to our newsletter'); ?></h2>
            <div class="ls-box ls-product-banner ls-newsletter">
                <div class="inner">
                    <ul>
                        <li>
                            <i class="dashicons dashicons-megaphone"></i>
                            <strong><?php ls_e('Stay Updated'); ?></strong>
                            <small><?php ls_e('News about the latest features and other product info.'); ?></small>
                        </li>
                        <li>
                            <i class="dashicons dashicons-heart"></i>
                            <strong><?php ls_e('Sneak Peak on Product Updates'); ?></strong>
                            <small><?php ls_e('Access to all the cool new features before anyone else.'); ?></small>
                        </li>
                        <li>
                            <i class="dashicons dashicons-smiley"></i>
                            <strong><?php ls_e('Provide Feedback'); ?></strong>
                            <small><?php ls_e('Participate in various programs and help us improving Creative Slider.'); ?></small>
                        </li>
                    </ul>
                    <form method="post" action="https://creativeslider.webshopworks.com/#footer" target="_blank">
                        <input type="hidden" name="submitNewsletter" value="Subscribe">
                        <div class="email">
                            <input type="text" name="email" placeholder="<?php ls_e('Enter your email address'); ?>">
                            <button class="button"><?php ls_e('Subscribe'); ?></button>
                        </div>
                        <input type="hidden" name="action" value="0">
                    </form>
                </div>
            </div>
        </div>
        <!-- Product Support  -->
        <div class="third">
            <h2><?php ls_e('Product Support'); ?></h2>
            <div class="ls-box ls-product-banner ls-product-support">
                <div class="inner">
                    <ul>
                        <li>
                            <i class="dashicons dashicons-book"></i>
                            <strong><?php ls_e('Read the documentation'); ?></strong>
                            <small><?php ls_e('Get started with using Creative Slider.'); ?></small>
                        </li>
                        <li>
                            <i class="dashicons dashicons-sos"></i>
                            <strong><?php ls_e('Browse the FAQs'); ?></strong>
                            <small><?php ls_e('Find answers for common questions.'); ?></small>
                        </li>
                        <li>
                            <i class="dashicons dashicons-groups"></i>
                            <strong><?php ls_e('Direct Support'); ?></strong>
                            <small><?php ls_e('Get in touch with our Support Team.'); ?></small>
                        </li>
                    </ul>
                    <a href="https://addons.prestashop.com/en/contact-us?id_product=19062" target="_blank" class="button"><?php ls_e('Contact the developer'); ?></a>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    var lsScreenOptions = <?php echo json_encode($lsScreenOptions); ?>;
</script>
