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
$postTypes = &$postTypes;
$postCategories = &$postCategories;
$postTags = &$postTags;
$postTaxonomies = &$postTaxonomies;
$lsDefaults = &$lsDefaults;

$queryArgs = [
    'post_status' => 'publish',
    'limit' => 50,
    'img_size' => null,
];

if (!empty($slider['properties']['post_orderby'])) {
    $queryArgs['orderby'] = $slider['properties']['post_orderby'];
}
if (!empty($slider['properties']['post_order'])) {
    $queryArgs['order'] = $slider['properties']['post_order'];
}
if (!empty($slider['properties']['post_type'][0])) {
    $queryArgs['post_type'] = $slider['properties']['post_type'];
}
if (!empty($slider['properties']['post_categories'][0])) {
    $queryArgs['category__in'] = $slider['properties']['post_categories'];
}
if (!empty($slider['properties'][0])) {
    $queryArgs['tag__in'] = $slider['properties']['post_tags'];
}
if (!empty($slider['properties']['post_tax_terms'])) {
    $queryArgs['img_size'] = $slider['properties']['post_tax_terms'];
}

$posts = LsPosts::find($queryArgs)->getParsedObject();
?>
<script type="text/javascript" class="ls-hidden" id="ls-posts-json">window.lsPostsJSON = <?php echo $posts ? json_encode($posts) : '[]'; ?>;</script>
<div id="ls-post-options">
    <div class="ls-box ls-modal ls-configure-posts-modal">
        <h2 class="header">
            <?php ls_e('Find products with the filters below'); ?>
            <a href="#" class="dashicons dashicons-no"></a>
        </h2>
        <div style="text-align: right; padding: 5px;">
            <label><?php ls_e('Advanced'); ?></label><input type="checkbox" id="ls-post-settings-adv">
        </div>
        <div class="ls-post-basic" style="width: 140px; margin: 0 auto 10px;">
            <label><input type="radio" name="post_basic" value="date_add"> <?php ls_e('New Arrivals'); ?></label><br>
            <label><input type="radio" name="post_basic" value="position"> <?php ls_e('Popular'); ?></label><br>
            <label><input type="radio" name="post_basic" value="quantity"> <?php ls_e('Best Sellers'); ?></label><br>
            <label><input type="radio" name="post_basic" value="reduction"> <?php ls_e('Special'); ?></label>
        </div>
        <div class="ls-post-advanced">
            <div class="inner clearfix">
                <div class="ls-post-filters clearfix">

                    <!-- Post types -->
                    <select data-param="post_type" name="post_type" class="multiple" multiple="multiple">
                    <?php foreach ($postTypes as $item) { ?>
                        <?php if (isset($slider['properties']['post_type']) && in_array($item['slug'], $slider['properties']['post_type'])) { ?>
                            <option value="<?php echo $item['slug']; ?>" selected="selected"><?php echo Tools::ucfirst($item['name']); ?></option>
                        <?php } else { ?>
                            <option value="<?php echo $item['slug']; ?>"><?php echo Tools::ucfirst($item['name']); ?></option>
                        <?php } ?>
                    <?php } ?>
                    </select>

                    <!-- Post categories -->
                    <select data-param="post_categories" name="post_categories" class="multiple" multiple="multiple">
                        <option value="0"><?php ls_e("Don't filter categories"); ?></option>
                    <?php foreach ($postCategories as $item) { ?>
                        <?php if (isset($slider['properties']['post_categories']) && in_array($item->term_id, $slider['properties']['post_categories'])) { ?>
                            <option value="<?php echo $item->term_id; ?>" selected="selected"><?php echo $item->name; ?></option>
                        <?php } else { ?>
                            <option value="<?php echo $item->term_id; ?>"><?php echo $item->name; ?></option>
                        <?php } ?>
                    <?php } ?>
                    </select>

                    <!-- Post tags -->
                    <select data-param="post_tags" name="post_tags" class="multiple" multiple="multiple">
                        <option value="0"><?php ls_e("Don't filter tags"); ?></option>
                    <?php foreach ($postTags as $item) { ?>
                        <?php if (isset($slider['properties']['post_tags']) && in_array($item->term_id, $slider['properties']['post_tags'])) { ?>
                            <option value="<?php echo $item->term_id; ?>" selected="selected"><?php echo Tools::ucfirst($item->name); ?></option>
                        <?php } else { ?>
                            <option value="<?php echo $item->term_id; ?>"><?php echo Tools::ucfirst($item->name); ?></option>
                        <?php } ?>
                    <?php } ?>
                    </select>

                    <!-- Post taxonomies -->
                    <select data-param="post_taxonomy" name="post_taxonomy" class="ls-post-taxonomy">
                        <option value="0"><?php ls_e("Don't filter taxonomies"); ?></option>
                    <?php foreach ($postTaxonomies as $key => $item) { ?>
                        <?php if (isset($slider['properties']['post_taxonomy']) && $slider['properties']['post_taxonomy'] == $key) { ?>
                            <option value="<?php echo $item->name; ?>" selected="selected"><?php echo $item->labels->name; ?></option>
                        <?php } else { ?>
                            <option value="<?php echo $item->name; ?>"><?php echo $item->labels->name; ?></option>
                        <?php } ?>
                    <?php } ?>
                    </select>
                </div>
            </div>
            <h3 class="subheader clearfix">
                <div class="half"><?php ls_e('Order results by'); ?></div>
                <div class="half">
                    <div class="half"><?php ls_e('Image size'); ?></div>
                    <div class="half"><?php ls_e('On this slide'); ?></div>
                </div>
            </h3>
            <div class="ls-post-adv-settings clearfix">

                <!-- Order  -->
                <div class="half">
                    <?php lsGetSelect($lsDefaults['slider']['postOrderBy'], $slider['properties'], ['data-param' => $lsDefaults['slider']['postOrderBy']['keys']]); ?>
                    <?php lsGetSelect($lsDefaults['slider']['postOrder'], $slider['properties'], ['data-param' => $lsDefaults['slider']['postOrder']['keys']]); ?>
                </div>

                <div class="half" style="padding:0">
                    <div class="half">
                        <!-- Taxonomy terms -->
                        <?php lsGetSelect($lsDefaults['slider']['postTaxTerms'], $slider['properties'], ['data-param' => $lsDefaults['slider']['postTaxTerms']['keys']]); ?>
                    </div>
                    <div class="half">
                        <!-- Post offset -->
                        <?php ls_e('Get the '); ?>
                        <select data-param="post_offset" name="post_offset" class="offset">
                            <option value="-1"><?php ls_e('following'); ?></option>
                        <?php for ($c = 0; $c < 50; ++$c) { ?>
                            <option value="<?php echo $c; ?>"><?php echo ls_ordinal_number($c + 1); ?></option>
                        <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="subheader preview-subheader"><?php ls_e('Preview from currenty matched elements'); ?></h3>
        <div class="ls-post-previews"><ul></ul></div>
    </div>
</div>
