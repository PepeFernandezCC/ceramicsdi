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

class AdminLayerSliderController extends ModuleAdminController
{
    const ACTIVATE_URL = 'https://webshopworks.com/activate/';

    public function postProcess()
    {
        parent::postProcess();

        if (isset($this->context->cookie->ls_error)) {
            $this->errors[] = $this->context->cookie->__get('ls_error');
            unset($this->context->cookie->ls_error);
        }
    }

    protected function processActivate()
    {
        $url = $this->context->link->getAdminLink('AdminLayerSlider');

        if (0 === strpos($url, 'index.php')) {
            $url = Tools::getShopProtocol() . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/$url";
        }

        if (Tools::getIsset('license')) {
            Configuration::updateGlobalValue('LS_LICENSE', Tools::getValue('license'));
            $url .= '#license';
        } else {
            list($p, $r) = explode('://', ls_referer());
            $encode = 'base64_encode';
            $url = self::ACTIVATE_URL . '?' . http_build_query([
                'response_type' => 'code',
                'client_id' => substr($encode(_COOKIE_KEY_), 0, 32),
                'auth_secret' => rtrim($encode("$r?" . Tools::passwdGen(23 - strlen($r))), '='),
                'state' => substr($encode($this->module->module_key), 0, 12),
                'redirect_uri' => urlencode($url),
            ]);
        }
        Tools::redirectAdmin($url);
    }

    protected function processDownloadSlider()
    {
        $id = Tools::getValue('id');
        $source = 'https://offlajn.com/index2.php?option=com_ls_import&task=getTemplate&id=' . $id;
        $destination = _PS_UPLOAD_DIR_ . 'lsimport.zip';
        $data = ls_remote_get($source, [
            'body' => ls_apply_filters('ls_download_slider/body_args', ['id' => $id]),
            'timeout' => 300,
        ]);
        if (isset($data[0]) && '{' === $data[0] && $error = json_decode($data, true)) {
            $this->context->cookie->__set('ls_error', $error['error']);

            if (419 == $error['code']) {
                Configuration::updateGlobalValue('LS_LICENSE', '');
            }
        } elseif ($data) {
            if (call_user_func('file_put_contents', $destination, $data)) {
                // import file
                require_once _PS_MODULE_DIR_ . 'layerslider/base/layerslider.php';
                require_once _PS_MODULE_DIR_ . 'layerslider/base/classes/class.ls.importutil.php';

                $import = new LsImportUtil($destination);

                @call_user_func('unlink', $destination);

                // Db::getInstance()->update('layerslider', array('name' => 'Unnamed'), 'id = ' . (int) $import->lastImportId);

                // redirect after import
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminLayerSlider') . "&action=edit&id={$import->lastImportId}#appearance"
                );
            } else {
                $this->context->cookie->__set('ls_error', ls__('Unable to write file: ') . $destination);
            }
        } else {
            $this->context->cookie->__set('ls_error', ls__('Import returned without data!'));
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminLayerSlider'));
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
            'id' => 'toplevel_page_layerslider',
            'base' => 'toplevel_page_layerslider',
        ];

        require_once _PS_MODULE_DIR_ . $this->module->name . '/helper.php';
        if (isset(${'_COOKIE'}['ls-login'])) {
            $this->content = '<script>
                var doc = window.parent.document, $ = window.parent.jQuery;
                doc.cookie = "ls-login=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
                $(".ls-publish button").click();
                $(doc.getElementById("main")).css({ opacity: "", pointerEvents: "" });
                $(doc.getElementById("ls-login")).remove();
            </script>';
        } else {
            require_once _PS_MODULE_DIR_ . 'layerslider/views/default.php';
        }

        $this->context->smarty->unregisterFilter('output', 'smartyPackJSinHTML');
    }

    public function display()
    {
        $this->context->smarty->assign('content', $this->content);
        $this->display_footer = false;

        parent::display();
    }
}
