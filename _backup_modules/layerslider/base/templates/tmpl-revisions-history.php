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

// Attempt to avoid memory limit issues
@ini_set('memory_limit', '256M');

// Get the IF of the slider
$id = (int) ${'_GET'}['id'];

// Get slider
$sliderItem = LsSliders::find($id);
$slider = $sliderItem['data'];

ls_enqueue_script('layerslider-admin');

$revisions = LsRevisions::snapshots($id);
$revisionsCount = count($revisions);

foreach ($revisions as $key => $revision) {
    $revisions[$key]->avatar = ls_get_avatar_url($revisions[$key]->author);
    $revisions[$key]->nickname = ls_get_userdata($revisions[$key]->author)->user_nicename;
    $revisions[$key]->time_diff = sprintf(ls__(' %s ago', 'LayerSlider'), ls_human_time_diff($revision->date_c));
    $revisions[$key]->created = date('M j @ H:i', $revision->date_c);

    $slider = json_decode($revision->data, true);

    // Fixes
    if (!isset($slider['layers'][0]['properties'])) {
        $slider['layers'][0]['properties'] = array();
    }


    // Get yourLogo
    if (! empty($slider['properties']['yourlogoId'])) {
        $slider['properties']['yourlogo'] = ls_apply_filters('ls_get_image', $slider['properties']['yourlogoId'], $slider['properties']['yourlogo']);
        $slider['properties']['yourlogoThumb'] = ls_apply_filters('ls_get_thumbnail', $slider['properties']['yourlogoId'], $slider['properties']['yourlogo']);
    }


    if (! empty($slider['properties']['width'])) {
        if (strpos($slider['properties']['width'], '%') !== false) {
            $slider['properties']['width'] = 1000;
        }
    }

    if (! empty($slider['properties']['width'])) {
        $slider['properties']['width'] = (int) $slider['properties']['width'];
    }

    if (! empty($slider['properties']['width'])) {
        $slider['properties']['height'] = (int) $slider['properties']['height'];
    }

    // Convert old checkbox values
    foreach ($slider['properties'] as $optionKey => $optionValue) {
        switch ($optionValue) {
            case 'on':
                $slider['properties'][$optionKey] = true;
                break;
            case 'off':
                $slider['properties'][$optionKey] = false;
                break;
        }
    }

    foreach ($slider['layers'] as $slideKey => $slideVal) {
        // Get slide background
        if (! empty($slideVal['properties']['backgroundId'])) {
            $slideVal['properties']['background'] = ls_apply_filters('ls_get_image', $slideVal['properties']['backgroundId'], $slideVal['properties']['background']);
            $slideVal['properties']['backgroundThumb'] = ls_apply_filters('ls_get_thumbnail', $slideVal['properties']['backgroundId'], $slideVal['properties']['background']);
        }

        // Get slide thumbnail
        if (! empty($slideVal['properties']['thumbnailId'])) {
            $slideVal['properties']['thumbnail'] = ls_apply_filters('ls_get_image', $slideVal['properties']['thumbnailId'], $slideVal['properties']['thumbnail']);
            $slideVal['properties']['thumbnailThumb'] = ls_apply_filters('ls_get_thumbnail', $slideVal['properties']['thumbnailId'], $slideVal['properties']['thumbnail']);
        }


        // v6.3.0: Improve compatibility with *really* old sliders
        if (! empty($slideVal['sublayers']) && is_array($slideVal['sublayers'])) {
            $slideVal['sublayers'] = array_values($slideVal['sublayers']);
        }


        $slider['layers'][$slideKey] = $slideVal;

        if (!empty($slideVal['sublayers']) && is_array($slideVal['sublayers'])) {
            // v6.0: Reverse layers list
            $slideVal['sublayers'] = array_reverse($slideVal['sublayers']);

            foreach ($slideVal['sublayers'] as $layerKey => $layerVal) {
                if (! empty($layerVal['imageId'])) {
                    $layerVal['image'] = ls_apply_filters('ls_get_image', $layerVal['imageId'], $layerVal['image']);
                    $layerVal['imageThumb'] = ls_apply_filters('ls_get_thumbnail', $layerVal['imageId'], $layerVal['image']);
                }

                if (! empty($layerVal['posterId'])) {
                    $layerVal['poster'] = ls_apply_filters('ls_get_image', $layerVal['posterId'], $layerVal['poster']);
                    $layerVal['posterThumb'] = ls_apply_filters('ls_get_thumbnail', $layerVal['posterId'], $layerVal['poster']);
                }

                // Ensure that magic quotes will not mess with JSON data
                $layerVal['styles'] = Tools::stripslashes($layerVal['styles']);
                $layerVal['transition'] = Tools::stripslashes($layerVal['transition']);

                // Parse embedded JSON data
                $layerVal['styles'] = !empty($layerVal['styles']) ? (object) json_decode(_ss($layerVal['styles']), true) : new stdClass;
                $layerVal['transition'] = !empty($layerVal['transition']) ? (object) json_decode(_ss($layerVal['transition']), true) : new stdClass;
                $layerVal['html'] = !empty($layerVal['html']) ? _ss($layerVal['html']) : '';

                // Custom attributes
                $layerVal['innerAttributes'] = !empty($layerVal['innerAttributes']) ? (object) $layerVal['innerAttributes'] : new stdClass;
                $layerVal['outerAttributes'] = !empty($layerVal['outerAttributes']) ? (object) $layerVal['outerAttributes'] : new stdClass;

                $slider['layers'][$slideKey]['sublayers'][$layerKey] = $layerVal;
            }
        } else {
            $slider['layers'][$slideKey]['sublayers'] = array();
        }
    }

    if (! empty($slider['callbacks'])) {
        foreach ($slider['callbacks'] as $key => $callback) {
            $slider['callbacks'][$key] = _ss($callback);
        }
    }

    $revisions[$key]->data = $slider;
}
?>

<!-- Get slider data from DB -->
<script type="text/javascript">

    // Revisions
    window.lsRevisions = <?php echo json_encode($revisions); ?>;

    // Slider data
    window.lsSliderData = <?php echo json_encode($slider) ?>;

    window.lsPostsJSON = [];

    // Plugin path
    var pluginPath = '<?php echo LS_VIEWS_URL ?>';
    var lsTrImgPath = '<?php echo LS_VIEWS_URL ?>img/admin/';

</script>


<div id="ls-revisions">

    <div class="wrap">
        <h2>
            <?php ls_e('Revisions for Slider:', 'LayerSlider') ?>
            <?php $sliderName = !empty($slider['properties']['title']) ? $slider['properties']['title'] : 'Unnamed'; ?>
            <?php echo ls_apply_filters('ls_slider_title', $sliderName, 30) ?>
            <a href="<?php echo ls_admin_url('admin.php?page=layerslider&action=edit&id='.$id) ?>" class="add-new-h2"><?php ls_e('Back to Slider', 'LayerSlider') ?></a>
        </h2>
        <form method="post" id="ls-revisions-form">
            <?php ls_nonce_field('ls-revert-slider-' . $id); ?>
            <input type="hidden" name="ls-revert-slider" value="1">
            <input type="hidden" name="slider-id" value="<?php echo $id ?>">
            <input type="hidden" id="revision-id" name="revision-id" value="<?php echo $revision->id ?>">
            <span class="ls-revisions-oldest"><?php echo date('M j, Y', $revisions[0]->date_c) ?></span>
            <span class="ls-revisions-now"><?php ls_e('Now') ?></span>

            <div id="ls-revisions-selected">
                <table>
                    <tr>
                        <td class="avatar" rowspan="2">
                            <img src="<?php echo $revision->avatar ?>">
                        </td>
                        <td>
                            <?php echo sprintf(ls__('Selected Revision by %s', 'LayerSlider'), '<strong class="author">'.$revision->nickname.'</strong>')  ?>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="time-diff"><?php echo $revision->time_diff ?></span>
                            (<span class="date"><?php echo $revision->created ?></span>)
                        </td>
                    </tr>
                </table>
                <button class="button button-primary button-hero"><?php ls_e('Revert to This Revision', 'LayerSlider') ?></button>
            </div>

            <input type="range" id="ls-revisions-range" min="1" max="<?php echo $revisionsCount ?>" value="<?php echo $revisionsCount ?>" name="revision" list="ls-revisions-timeline">
            <datalist id="ls-revisions-timeline">
                <?php for ($c = 1; $c <= $revisionsCount; $c++) : ?>
                <option><?php echo $c ?></option>
                <?php endfor ?>
            </datalist>
        </form>

        <div class="ls-notification-info">
            <i class="dashicons dashicons-info"></i>
            <?php ls_e('Reverting a slider to an earlier version adds another snapshot to Revisions, which can also be reverted if you change your mind and would rather return to the original copy.', 'LayerSlider') ?>
            <?php ls_e('Slider Revisions also saves the undo/redo controls. Even if there is no perfect snapshot, you will be able to undo the changes in-between to find what you are looking for.', 'LayerSlider') ?>
        </div>

        <h2 class="ls-revisions-h2"><?php ls_e('Preview for Selected Revision', 'LayerSlider') ?></h2>
        <div id="ls-slider-form">
            <div id="ls-layer-tabs">
                <?php
                foreach ($slider['layers'] as $key => $layer) :
                    $active = empty($key) ? 'active' : '';
                    $name = !empty($layer['properties']['title']) ? $layer['properties']['title'] : sprintf(ls__('Slide #%d', 'LayerSlider'), ($key+1));
                    $bgImage = !empty($layer['properties']['background']) ? $layer['properties']['background'] : null;
                    $bgImageId = !empty($layer['properties']['backgroundId']) ? $layer['properties']['backgroundId'] : null;
                    $image = ls_apply_filters('ls_get_image', $bgImageId, $bgImage, true);
                    ?>
                    <a href="#" class="<?php echo $active ?>" data-help="<div style='background-image: url(<?php echo $image?>);'></div>" data-help-class="ls-slide-preview-tooltip popover-light km-ui-popup" data-help-delay="1" data-help-transition="false">
                        <span><?php echo $name ?></span>
                        <span class="dashicons dashicons-dismiss"></span>
                    </a>
                <?php
                endforeach; ?>
                <div class="unsortable clear"></div>
            </div>

            <!-- Slides -->
            <div id="ls-layers" class="clearfix">
                <div class="ls-box ls-layer-box active">
                    <table id="ls-preview-table">
                        <tbody>
                            <tr id="slider-editor-toolbar">
                                <td >
                                    <div class="ls-editor-zoom">
                                        <!-- <span class="dashicons dashicons-editor-expand ls-layers-icon"></span> -->
                                        <div class="ls-editor-slider" ></div>
                                        <span class="ls-editor-slider-val">100%</span>
                                        |
                                        <?php ls_e('Auto-Fit', 'LayerSlider') ?>
                                        <input id="zoom-fit" class="ls-checkbox checkbox small" type="checkbox" checked>
                                    </div>


                                    <div class="ls-editor-preview">
                                        <button type="button" class="button ls-preview-button"><?php ls_e('Preview Slide', 'LayerSlider') ?></button>
                                    </div>


                                    <div class="ls-editor-layouts">
                                        <button data-type="desktop" class="button dashicons dashicons-desktop playing" data-help="<?php ls_e('Show layers that are visible on desktop.', 'LayerSlider') ?>"></button><!--
                                    --><button data-type="tablet" class="button dashicons dashicons-tablet" data-help="<?php ls_e('Show layers that are visible on tablets.', 'LayerSlider') ?>"></button><!--
                                    --><button data-type="phone"  class="button dashicons dashicons-smartphone" data-help="<?php ls_e('Show layers that are visible on mobile phones.', 'LayerSlider') ?>"></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="ls-preview-td">
                        <div class="ls-preview-wrapper ls-preview-size" data-dragover="<?php ls_e('Drop image(s) here', 'LayerSlider') ?>">
                            <div class="ls-preview ls-preview-size">
                                <div id="ls-preview-layers" class="draggable ls-layer ls-preview-transform">
                                    <div id="ls-static-preview" class="disabled"></div>
                                </div>
                            </div>
                            <div class="ls-real-time-preview ls-preview-size"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
