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

if (defined('LS_INCLUDE')) {
    $notification = '';
}
?>
<div id="ls-revisions-welcome">

    <div class="wrap">

        <?php if (!empty($notification)) { ?>
        <div class="ls-notification-info">
            <i class="dashicons dashicons-info"></i>
            <?php echo $notification; ?>
        </div>
        <?php } ?>

        <h1><?php ls_e('You Can Now Rewind Time'); ?></h1>
        <p class="center">
            <?php ls_e('Have a peace of mind knowing that your slider edits are always safe and you can revert back unwanted changes or faulty saves at any time. This feature serves not just as a backup solution, but a complete version control system where you can visually compare the changes you have made along the way.'); ?>
            <br><br>
            <a href="#" class="ls-revisions-options"><?php ls_e('Customize Revisions Preferences'); ?></a>
            <a target="_blank" href="https://creativeslider.webshopworks.com/revisions-63" class="ls-revisions-more-info"><?php ls_e('More Information'); ?></a>
        </p>
        <div class="center">
            <video autoplay loop muted>
                <source src="https://layerslider.com/wp-content/uploads/2017/04/revisions.mp4" type="video/mp4">
            </video>
        </div>
    </div>


    <script type="text/html" id="tmpl-revisions-options">
        <div id="ls-revisions-modal-window">
            <header>
                <h1><?php ls_e('Revisions Preferences'); ?></h1>
                <b class="dashicons dashicons-no"></b>
            </header>
            <form method="post" class="km-ui-modal-scrollable">
                <input type="hidden" name="ls-revisions-options" value="1">
                <table>
                    <tr>
                        <td><input type="checkbox" name="ls-revisions-enabled" class="hero" data-warning="<?php ls_e('Disabling Slider Revisions will also remove all revisions saved so far. Are you sure you want to continue?'); ?>" <?php echo LsRevisions::$enabled ? 'checked' : ''; ?>></td>
                        <td><?php ls_e('Enable Revisions'); ?></td>
                    </tr>
                </table>


                <div>
                    <h2 class="ls-revisions-h2"><?php ls_e('Update Frequency'); ?></h2>
                    <?php printf(ls__('Limit the total number of revisions per slider to %s.'), '<input type="number" name="ls-revisions-limit" min="2" max="500" value="' . LsRevisions::$limit . '">'); ?> <br>
                    <?php printf(ls__('Wait at least %s minutes between edits before adding a new revision.'), '<input type="number" name="ls-revisions-interval" min="0" max="500" value="' . LsRevisions::$interval . '">'); ?>
                </div>

                <div class="ls-notification-info">
                    <i class="dashicons dashicons-info"></i>
                    <?php ls_e('Slider Revisions also stores the undo/redo controls. There is no reason using very frequent saves since you will be able to undo the changes in-between.'); ?>
                </div>

                <button class="button button-primary button-hero"><?php ls_e('Update Revisions Preferences'); ?></button>
            </form>
        </div>
    </script>
</div>
