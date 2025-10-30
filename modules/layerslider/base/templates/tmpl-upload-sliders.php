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
<script type="text/html" id="tmpl-upload-sliders">
    <div id="ls-upload-modal-window">
        <header>
            <h1><?php ls_e('Upload Slider'); ?></h1>
            <b class="dashicons dashicons-no"></b>
        </header>
        <form method="post" enctype="multipart/form-data" class="km-ui-modal-scrollable">
            <p><?php ls_e('Here you can upload your previously exported sliders. To import them to your site, you just need to choose and select the appropriate export file (files with .zip or .json extensions), then press the Import Sliders button.'); ?></p>
            <div class="ls-notification updated info"><div><?php printf(ls__('Looking for the importable demo content? Check out the %sTemplate Store%s.'), '<a href="#" class="ls-open-template-store" data-delay="750"><i class="dashicons dashicons-star-filled"></i>', '</a>'); ?></div></div>
            <p class="small italic dim"><?php ls_e('Notice: In order to import from outdated versions (pre-v3.0.0), you need to create a new file and paste the export code into it. The file needs to have a .json extension, then you will be able to upload it.'); ?></p>
            <input type="hidden" name="ls-import" value="1">
            <p class="centered center file">
                <input type="file" name="import_file">
            </p>
            <button class="button button-primary button-hero"><?php ls_e('Import Sliders'); ?></button><br>
        </form>
    </div>
</script>