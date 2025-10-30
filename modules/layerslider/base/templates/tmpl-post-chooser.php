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
?>
<script type="text/html" id="tmpl-post-chooser">
    <div id="ls-post-chooser-modal-window">
        <header>
            <h1><?php ls_e('Select the Post, Page or Attachment you want to use'); ?></h1>
            <b class="dashicons dashicons-no"></b>
        </header>
        <div class="km-ui-modal-scrollable">
            <form method="post">
                <input type="hidden" name="action" value="ls_get_search_posts">
                <div class="search-holder">
                    <input type="search" name="s" placeholder="<?php ls_e('Type here to search ...'); ?>">
                </div>
                <select name="post_type">
                    <option value="page"><?php ls_e('Pages'); ?></option>
                    <option value="post"><?php ls_e('Posts'); ?></option>
                    <option value="attachment"><?php ls_e('Attachments'); ?></option>
                </select>
            </form>

            <div class="results ls-post-previews">
                <ul>

                </ul>
            </div>

        </div>
    </div>
</script>
