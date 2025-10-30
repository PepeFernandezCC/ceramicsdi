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

$l10n_ls = [
    // General
    'save' => ls__('Save changes'),
    'saving' => ls__('Saving ...'),
    'saved' => ls__('Saved'),
    'error' => ls__('ERROR'),
    'untitled' => ls__('Untitled'),
    'stop' => ls__('Stop'),
    'copied' => ls__('Copied to clipboard!'),

    'slide' => ls__('Slide'),
    'layer' => ls__('Layer'),

    'selectAll' => ls__('Select all'),
    'deselectAll' => ls__('Deselect all'),

    'noPreview' => ls__('No Preview'),

    // Notify OSD
    'notifySliderSaved' => ls__('Slider saved successfully'),
    'notifyCaptureSlide' => ls__('Capturing page. This might take a moment ...'),

    // Sliders list
    'SLRemoveSlider' => ls__('Are you sure you want to remove this slider?'),
    'SLUploadSlider' => ls__('Uploading, please wait ...'),
    'SLEnterCode' => ls__('Please enter a valid Item Purchase Code. For more information, please click on the “Where’s my purchase code?” button.'),
    'SLDeactivate' => ls__('Are you sure you want to deactivate this site?'),
    'SLPermissions' => ls__('WARNING: This option controls who can access to this plugin, you can easily lock out yourself by accident. Please, make sure that you have entered a valid capability without whitespaces or other invalid characters. Do you want to proceed?'),
    'SLJQueryConfirm' => ls__("Do not enable this option unless you're  experiencing issues with jQuery on your site. This option can easily cause unexpected issues when used incorrectly. Do you want to proceed?"),
    'SLJQueryReminder' => ls__('Do not forget to disable this option later on if it does not help, or if you experience unexpected issues. This includes your entire site, not just Creative Slider.'),

    'SLImporting' => ls__('Importing, please wait...'),
    'SLImportError' => ls__('It seems there is a server issue that prevented Creative Slider from importing your selected slider. Please check Creative Slider -> System Status for potential errors, try to temporarily disable themes/plugins to rule out incompatibility issues or contact your hosting provider to resolve server configuration problems. In many cases retrying to import the same slider can help.'),
    'SLImportHTTPError' => ls__('It seems there is a server issue that prevented Creative Slider from importing your selected slider. Please check Creative Slider -> System Status for potential errors, try to temporarily disable themes/plugins to rule out incompatibility issues or contact your hosting provider to resolve server configuration problems. In many cases retrying to import the same slider can help. Your HTTP server thrown the following error: \n\n %s'),

    // Template Store
    'TSImportWarningTitle' => ls__('Activate your site to access premium templates.'),
    'TSImportWarningContent' => sprintf(ls__('This template is only available for activated sites. Please review the PRODUCT ACTIVATION section on the main Creative Slider screen or %sclick here%s for more information.'), '<a href=\"https://support.kreaturamedia.com/docs/layersliderwp/documentation.html#activation\" target=\"_blank\">', '</a>'),
    'TSVersionWarningTitle' => ls__('Plugin update required'),
    'TSVersionWarningContent' => sprintf(ls__('This slider template requires a newer version of Creative Slider in order to work properly. This is due to additional features introduced in a later version than you have. For updating instructions, please refer to our %sonline documentation%s.'), '<a href="https://support.kreaturamedia.com/docs/layersliderwp/documentation.html#updating" target="_blank">', '</a>'),

    // Google Fonts
    'GFEmptyList' => ls__("You haven't added any Google Font to your collection yet."),
    'GFEmptyCharset' => ls__('You need to have at least one character set added. Please select another item before removing this one.'),
    'GFFontFamily' => ls__('Choose a font family'),
    'GFFontVariant' => ls__('Select %s font variants'),

    // Slider Builder
    'SBSlideTitle' => ls__('Slide #%d'),
    'SBSlideCopyTitle' => ls__('Slide #%d copy'),
    'SBLayerTitle' => ls__('Layer #%d'),
    'SBLayerCopyTitle' => ls__('Layer #%d copy'),
    'SBUndoLayer' => ls__('Layer settings'),
    'SBUndoSlide' => ls__('Slide settings'),
    'SBUndoNewLayer' => ls__('New layer'),
    'SBUndoNewLayers' => ls__('New layers'),
    'SBUndoVideoPoster' => ls__('Video poster'),
    'SBUndoRemoveVideoPoster' => ls__('Remove video poster'),
    'SBUndoLayerPosition' => ls__('Layer position'),
    'SBUndoRemoveLayer' => ls__('Remove layer(s)'),
    'SBUndoHideLayer' => ls__('Hide layer'),
    'SBUndoLockLayer' => ls__('Lock layer'),
    'SBUndoPasteSettings' => ls__('Paste layer settings'),
    'SBUndoSlideImage' => ls__('Slide image'),
    'SBUndoLayerImage' => ls__('Layer image'),
    'SBUndoSortLayers' => ls__('Sort layers'),
    'SBUndoLayerType' => ls__('Layer type'),
    'SBUndoLayerMedia' => ls__('Layer media'),
    'SBUndoLayerResize' => ls__('Layer resize'),
    'SBUndoAlignLayer' => ls__('Align layer(s)'),
    'SBUndoRemoveSlideImage' => ls__('Remove slide image'),
    'SBUndoRemoveLayerImage' => ls__('Remove layer image'),
    'SBDragMe' => ls__('Drag me :)'),
    'SBPreviewImagePlaceholder' => ls__('Double click to<br> set image'),
    'SBPreviewMediaPlaceholder' => ls__('Add media or paste embed code'),
    'SBPreviewTextPlaceholder' => ls__('Text Layer'),
    'SBPreviewHTMLPlaceholder' => ls__('HTML Layer'),
    'SBPreviewButtonPlaceholder' => ls__('Button Label'),
    'SBPreviewSlide' => ls__('Preview Slide'),
    'SBLayerPreviewMultiSelect' => ls__('Layer Preview is not available in Multiple Selection Mode. Select only one layer to use this feature. '),
    'SBStaticUntil' => ls__('Until the end of Slide #%d'),
    'SBPasteLayerError' => ls__("There's nothing to paste. Copy a layer first!"),
    'SBPasteError' => ls__('There is nothing to paste!'),
    'SBRemoveSlide' => ls__('Are you sure you want to remove this slide?'),
    'SBRemoveLayer' => ls__('Are you sure you want to remove this layer?'),
    'SBMediaLibraryImage' => ls__('Pick an image to use it in Creative Slider WP'),
    'SBUploadError' => ls__('Upload error'),
    'SBUploadErrorMessage' => ls__('Upload error: %s'),
    'SBInvalidFormat' => ls__('Invalid format'),
    'SBEnterImageURL' => ls__('Enter an image URL'),
    'SBTransitionApplyOthers' => ls__('Are you sure you want to apply the currently selected transitions and effects on the other slides?'),
    'SBPostFilterWarning' => ls__('No posts were found with the current filters.'),
    'SBSaveError' => ls__('It seems there is a server issue that prevented Creative Slider from saving your work. Please check Creative Slider -> System Status for potential errors, try to temporarily disable themes/plugins to rule out incompatibility issues or contact your hosting provider to resolve server configuration problems. Your HTTP server thrown the following error: \n\n %s'),
    'SBUnsavedChanges' => ls__('You have unsaved changes on this page. Do you want to leave and discard the changes made since your last save?'),
    'SBLinkPostDynURL' => ls__('Linked to: Post URL from Dynamic content'),
    'SBImportLayerNoSlider' => ls__('No sliders found.'),
    'SBImportLayerNoSlide' => ls__('No slides found.'),
    'SBImportLayerNoLayer' => ls__('No layers found.'),

    'SBImportLayerSelectSlide' => ls__('Select a slide first.'),

    'SBLayerTypeImg' => ls__('Image'),
    'SBLayerTypeIcon' => ls__('Icon'),
    'SBLayerTypeText' => ls__('Text'),
    'SBLayerTypeButton' => ls__('Button'),
    'SBLayerTypeMedia' => ls__('Audio / Video'),
    'SBLayerTypeHTML' => ls__('HTML'),
    'SBLayerTypePost' => ls__('Dynamic'),

    // Transition Builder
    'TBTransitionName' => ls__('Type transition name'),
    'TBRemoveTransition' => ls__('Remove transition'),
    'TBRemoveConfirmation' => ls__('Are you sure you want to remove this transition?'),
];
