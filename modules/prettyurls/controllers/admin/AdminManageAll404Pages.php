<?php
/**
 * UrlRedirects
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2023 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminManageAll404PagesController extends ModuleAdminController
{
    protected $check_pnf = false;

    public function __construct()
    {
        $this->className = 'AllBrokenLinks';
        $this->table = 'fmm_404_pages';
        $this->identifier = 'id_fmm_404_pages';
        $this->lang = false;
        $this->bootstrap = true;
        $this->deleted = false;
        parent::__construct();
        $this->context = Context::getContext();
        $this->check_pnf = Module::isInstalled('pagesnotfound') && Module::isEnabled('pagesnotfound');
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected'),
                'confirm' => $this->module->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ],
        ];
        $this->fields_list = [
            'id_fmm_404_pages' => [
                'title' => $this->module->l('ID'),
                'width' => 'auto',
                'orderby' => true,
                'filter_key' => 'id_fmm_404_pages',
            ],
            'redirect_type' => [
                'title' => $this->module->l('Redirection Type'),
                'maxlength' => 30,
                'filter_key' => 'redirect_type',
            ],
            'broken_url' => [
                'title' => $this->module->l('404 Url'),
                'maxlength' => 200,
                'orderby' => false,
                'filter_key' => 'broken_url',
            ],
            'redirection_url' => [
                'title' => $this->module->l('New Url'),
                'maxlength' => 200,
                'orderby' => false,
                'filter_key' => 'redirection_url',
            ],
        ];
    }

    public function init()
    {
        parent::init();
        if (Tools::getValue('action') == 'openFancyBoxForAllUrls') {
            $action_url = $this->context->link->getAdminLink('AdminManageAll404Pages', true);
            $this->context->smarty->assign('action_url', $action_url);
            $res = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ .
                'prettyurls/views/templates/admin/all_urls/add_new_urls.tpl'
            );
            exit(json_encode($res));
        }
        if (Tools::getValue('action') == 'addNewUrlForAllPnf') {
            $new_url = pSQL(Tools::getValue('new_url'));
            $redirection_type = pSQL(Tools::getValue('redirection_type'));
            if (!empty($new_url) && !Validate::isUrl($new_url)) {
                $result = false;
            } else {
                $result = AllBrokenLinks::updateAllPnfUrls($redirection_type, $new_url);
                $result = true;
            }
            if ($result == true) {
                $this->addUrlsToHtaccessFile();
                exit(json_encode(['success' => true, 'msg' => $this->module->l('Setting successfully updated')]));
            } else {
                exit(json_encode([
                    'success' => false,
                    'msg' => $this->module->l(
                        'Please enter valid url for redirection e.g https: //www.google.com'
                    ),
                ]));
            }
        }
    }

    public function renderList()
    {
        if ($this->check_pnf) {
            $this->addAllPnfInDb();
        }
        $obj = new AllBrokenLinks();
        $pnf = $obj->getAllPnfRedirects();
        $res = '';
        if (!empty($pnf)) {
            $res = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ .
                'prettyurls/views/templates/admin/all_urls/all_new_urls.tpl'
            );
        }
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->context->smarty->assign([
            'href' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=prettyurls&tab_module=redirects&module_name=prettyurls',
        ]);
        $backButton = $this->context->smarty->fetch(dirname(__FILE__) . '/../../views/templates/admin/back_button.tpl');
        
        return $res . parent::renderList().$backButton;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function addAllPnfInDb()
    {
        $obj = new AllBrokenLinks();
        $pnf = $obj->getPagesNotFound();
        $result = [];
        if (!empty($pnf)) {
            foreach ($pnf as $pnfs) {
                array_push($result, $pnfs['request_uri']);
            }
            $unique = array_map('unserialize', array_unique(array_map('serialize', $result)));
            $obj->isExists($unique);
        }
    }

    public function renderForm()
    {
        $options = [
            [
                'id_option' => '301',
                'name' => '301 Moved Permanently',
            ],
            [
                'id_option' => '302',
                'name' => '302 Moved Permanently',
            ],
            [
                'id_option' => '303',
                'name' => '303 Other source',
            ],
        ];
        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Manage All 404 pages'),
                'icon' => 'icon-globe',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->module->l('404 Uri'),
                    'name' => 'broken_url',
                    'disabled' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Redirection Type'),
                    'name' => 'redirect_type',
                    'options' => [
                        'query' => $options,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('404 Uri'),
                    'name' => 'redirection_url',
                    'required' => true,
                    'desc' => 'https://www.google.com/',
                ],
            ],
        ];

        $this->fields_form['submit'] = [
            'title' => $this->module->l('Save'),
            'class' => 'button pull-right',
        ];

        return parent::renderForm();
    }

    public function postProcess()
    {
        $obj = new AllBrokenLinks();
        if (Tools::isSubmit('submitAddfmm_404_pages') || Tools::isSubmit('submitBulkdeletefmm_404_pages')) {
            parent::postProcess();
            $this->addUrlsToHtaccessFile();
        } elseif (Tools::isSubmit('submitBulkdeletefmm_404_pages')) {
            $redirects_box = Tools::getValue('fmm_404_pagesBox');
            if (isset($redirects_box)) {
                $obj->dropAll($redirects_box);
                $obj->clearPnfHtaccessFile();
            }
        } elseif (Tools::isSubmit('deletefmm_404_pages')) {
            $id = Tools::getValue('id_fmm_404_pages');
            $obj->deleteSelectedRecord((int) $id);
            $this->addUrlsToHtaccessFile();
            $this->confirmations[] = $this->module->l('Redirected url deleted Successfully');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminManageAll404Pages') . '&conf=2');
        }
    }

    public function addUrlsToHtaccessFile()
    {
        $obj = new AllBrokenLinks();
        $path = _PS_ROOT_DIR_ . '/.htaccess';
        $trigger = (int) Configuration::get('REDIRECT_TRIGGER');
        if ($trigger <= 0) {
            if (file_exists($path)) {
                $old_htaccess_content = Tools::file_get_contents($path);
                $redirects_collection = $obj->getAllPnfRedirects();
                $content = Tools::file_get_contents($path);
                if (preg_match('#^(.*)\#begin_404url.*\#end_404url[^\n]*(.*)$#s', $content, $m)) {
                    if (!$write_fd = @fopen($path, 'w')) {
                        $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                        return false;
                    } elseif ($write_fd = @fopen($path, 'w')) {
                        fwrite($write_fd, $m[1]);
                        fwrite($write_fd, "\n#begin_404url\n");

                        // RedirectMatch or Redirect
                        foreach ($redirects_collection as $redirect) {
                            if (!empty($redirect['redirection_url'])) {
                                fwrite(
                                    $write_fd,
                                    'RedirectMatch ' . $redirect['redirect_type'] .
                                    ' ' . $redirect['broken_url'] . ' ' . $redirect['redirection_url'] . "\n"
                                );
                            }
                        }

                        fwrite($write_fd, "#end_404url\n");
                        fclose($write_fd);
                    }
                } else {
                    if (!$write_fd = @fopen($path, 'w')) {
                        $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                        return false;
                    } elseif ($write_fd = @fopen($path, 'w')) {
                        fwrite($write_fd, $old_htaccess_content);
                        fwrite($write_fd, "\n#begin_404url\n");
                        foreach ($redirects_collection as $redirect) {
                            if (!empty($redirect['redirection_url'])) {
                                fwrite(
                                    $write_fd,
                                    'RedirectMatch ' . $redirect['redirect_type'] .
                                    ' ' . $redirect['broken_url'] . ' ' . $redirect['redirection_url'] . "\n"
                                );
                            }
                        }
                        fwrite($write_fd, "#end_404url\n");
                        fclose($write_fd);
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                return false;
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme = false);
        $url = $this->context->link->getAdminLink('AdminManageAll404Pages', true);
        if (Tools::getValue('controller') == 'AdminManageAll404Pages') {
            $this->addjQueryPlugin([
                'fancybox',
            ]);
            Media::addJsDef([
                'controller_url' => $url,
            ]);
            $this->context->controller->
                addJS(_PS_MODULE_DIR_ . 'prettyurls/views/js/urlredirects.js');
        }
    }
}
