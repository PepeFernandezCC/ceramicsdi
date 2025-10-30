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

class AdminLayerSliderTransitionController extends ModuleAdminController
{
    public function postProcess()
    {
        parent::postProcess();
        if (isset($this->context->cookie->ls_error)) {
            $this->errors[] = $this->context->cookie->ls_error;
            unset($this->context->cookie->ls_error);
        }
    }

    public function initPageHeaderToolbar()
    {
        // hide header toolbar
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $GLOBALS['ls_token'] = $this->token;
        $GLOBALS['ls_screen'] = (object) [
            'id' => 'layerslider_page_ls-transition-builder',
            'base' => 'layerslider_page_ls-transition-builder',
        ];

        require_once _PS_MODULE_DIR_ . $this->module->name . '/helper.php';
        require_once _PS_MODULE_DIR_ . 'layerslider/views/default.php';
    }

    public function display()
    {
        $this->context->smarty->assign(['content' => $this->content]);
        $this->display_footer = false;

        parent::display();
    }
}
