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
class AdminManageRedirectController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'RedirectUrl';
        $this->table = 'redirecturl';
        $this->identifier = 'id_redirecturl';
        $this->lang = false;
        $this->bootstrap = true;
        $this->deleted = false;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        parent::__construct();
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected'),
                'confirm' => $this->module->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'id_redirecturl' => ['title' => $this->module->l('ID'), 'align' => 'center', 'width' => 30],
            'old_uri' => ['title' => $this->module->l('Old URI')],
            'new_url' => ['title' => $this->module->l('New URL')],
            'redirect_type' => ['title' => $this->module->l('Redirect Type'), 'align' => 'center'],
        ];
    }

    public function init()
    {
        parent::init();
        Shop::addTableAssociation($this->table, ['type' => 'shop']);
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ .
            'redirecturl_shop` sa ON (a.`id_redirecturl` = sa.`id_redirecturl` AND sa.id_shop = ' .
            (int) $this->context->shop->id . ') ';
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND sa.`id_shop` = ' . (int) Context::getContext()->shop->id;
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->context->smarty->assign([
            'href' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=prettyurls&tab_module=redirects&module_name=prettyurls',
        ]);
        $backButton = $this->context->smarty->fetch(dirname(__FILE__) . '/../../views/templates/admin/back_button.tpl');
        
        return parent::renderList().$backButton;
    }

    public function initToolbar()
    {
        $module_index = $this->context->link->getAdminLink('AdminModules', false);
        $token = Tools::getAdminTokenLite('AdminModules');
        $url = $module_index . '&configure=prettyurls&token=' . $token . '&tab_module=seo&module_name=prettyurls';

        parent::initToolbar();
    }

    public function renderForm()
    {
        $redirect_types = [
            ['value' => 301, 'name' => $this->module->l('301 Moved Permanently')],
            ['value' => 302, 'name' => $this->module->l('302 Moved Temporarily')],
            ['value' => 303, 'name' => $this->module->l('303 Other Source')],
        ];

        $this->fields_form = [
            'tinymce' => true,
            'legend' => [
                'title' => $this->module->l('Manage Redirects'),
                'icon' => 'icon-cog',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->module->l('Redirect Type'),
                    'name' => 'redirect_type',
                    'options' => [
                        'query' => $redirect_types,
                        'id' => 'value',
                        'name' => 'name',
                    ],
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Old URI'),
                    'name' => 'old_uri',
                    'required' => true,
                    'desc' => $this->module->l(
                        'Do not use http, www and domain for old URI.
                        Your old URI must start with "/" (i.e /old-uri.html)'
                    ),
                    'hint' => $this->module->l('Invalid characters:') . ' <>;=#{}',
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('New URL'),
                    'name' => 'new_url',
                    'desc' => $this->module->l(
                        'Use full URL with http/https, www and domain
                        (i.e http://www.example.com/en/new-uri.html)'
                    ),
                    'required' => true,
                    'hint' => $this->module->l('Invalid characters:') . ' <>;=#{}',
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
                'name' => 'submitRedirects',
            ],
        ];
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->module->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        return parent::renderForm();
    }

    public function postProcess()
    {
        $obj = new RedirectUrl();
        $path = _PS_ROOT_DIR_ . '/.htaccess';
        if (Tools::isSubmit('submitRedirects')
            || Tools::isSubmit('deleteredirecturl')
            || Tools::isSubmit('submitOptionsredirects')) {
            $old_uri = pSQL(Tools::getValue('old_uri'));
            $id_redirecturl = (int) Tools::getValue('id_redirecturl');
            $new_url = pSQL(Tools::getValue('new_url'));
            if (!empty($old_uri) && $obj->isExists($old_uri, $id_redirecturl)) {
                return $this->context->controller->warnings[] = $this->module->l(
                    'You can not add duplicate old urls'
                );
            } elseif (!empty($new_url) && !Validate::isAbsoluteUrl($new_url)) {
                $this->context->controller->errors[] = $this->module->l(
                    'Please enter valid new url.'
                );
            } else {
                parent::postProcess();
                $redirects_collection = $obj->getAllRedirects();
                $old_htaccess_content = Tools::file_get_contents($path);
                $trigger = (int) Configuration::get('REDIRECT_TRIGGER');
                if ($trigger <= 0) {
                    if (file_exists($path)) {
                        $content = Tools::file_get_contents($path);
                        if (preg_match('#^(.*)\#begin_redirecturl.*\#end_redirecturl[^\n]*(.*)$#s', $content, $m)) {
                            if (!$write_fd = @fopen($path, 'w')) {
                                $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                                return false;
                            } elseif ($write_fd = @fopen($path, 'w')) {
                                fwrite($write_fd, $m[1]);
                                fwrite($write_fd, "\n#begin_redirecturl\n");

                                // RedirectMatch or Redirect
                                foreach ($redirects_collection as $redirect) {
                                    fwrite(
                                        $write_fd,
                                        'RedirectMatch ' . $redirect['redirect_type'] . ' ' .
                                        $redirect['old_uri'] . ' ' . $redirect['new_url'] . "\n"
                                    );
                                }

                                fwrite($write_fd, "#end_redirecturl\n");
                                fclose($write_fd);
                            }
                        } else {
                            if (!$write_fd = @fopen($path, 'w')) {
                                $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                                return false;
                            } elseif ($write_fd = @fopen($path, 'w')) {
                                fwrite($write_fd, $old_htaccess_content);
                                fwrite($write_fd, "\n#begin_redirecturl\n");
                                foreach ($redirects_collection as $redirect) {
                                    fwrite(
                                        $write_fd,
                                        'RedirectMatch ' . $redirect['redirect_type'] . ' ' .
                                        $redirect['old_uri'] . ' ' . $redirect['new_url'] . "\n"
                                    );
                                }

                                fwrite($write_fd, "#end_redirecturl\n");
                                fclose($write_fd);
                            }
                        }
                    } else {
                        $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                        return false;
                    }
                }
            }
        } elseif (Tools::isSubmit('submitBulkdeleteredirecturl')) {
            $redirects_box = Tools::getValue('redirecturlBox');
            if (isset($redirects_box)) {
                $obj->dropAll($redirects_box);
                $this->clearHtaccessFile();
            }
        } elseif (Tools::isSubmit('exportredirecturl')) {
            $this->processExport();
        } elseif (Tools::isSubmit('info')) {
            $inserted = (int) Tools::getValue('inserted');
            $invalid = (int) Tools::getValue('invalid');
            $duplicate = (int) Tools::getValue('duplicate');

            if ($inserted > 0) {
                $this->confirmations[] = $this->module->l('CSV data imported.');
            }

            $this->informations[] = $this->module->l('Duplicate and invalid records will be skipped.');
            $this->informations[] = $this->module->l('Inserted records : ') . $inserted;
            $this->informations[] = $this->module->l('Invalid records : ') . $invalid;
            $this->informations[] = $this->module->l('Duplicate records : ') . $duplicate;
        } elseif (Tools::isSubmit('required_fields_error')) {
            $this->errors[] = Tools::displayError('Import unsuccessfull...missing required fields.');
        }
    }

    public function clearHtaccessFile()
    {
        $path = _PS_ROOT_DIR_ . '/.htaccess';
        $content = Tools::file_get_contents($path);
        if (preg_match('#^(.*)\#begin_redirecturl.*\#end_redirecturl[^\n]*(.*)$#s', $content, $m)) {
            if ($write_fd = @fopen($path, 'w')) {
                fwrite($write_fd, $m[1]);
                fclose($write_fd);
            }
        }

        return true;
    }

    protected function addUrlToHtaccess()
    {
        $obj = new RedirectUrl();
        $path = _PS_ROOT_DIR_ . '/.htaccess';
        $trigger = (int) Configuration::get('REDIRECT_TRIGGER');
        $old_htaccess_content = Tools::file_get_contents($path);
        $redirects_collection = $obj->getAllRedirects();
        if ($trigger <= 0) {
            if (file_exists($path)) {
                $content = Tools::file_get_contents($path);
                if (preg_match('#^(.*)\#begin_redirecturl.*\#end_redirecturl[^\n]*(.*)$#s', $content, $m)) {
                    if (!$write_fd = @fopen($path, 'w')) {
                        $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                        return false;
                    } elseif ($write_fd = @fopen($path, 'w')) {
                        fwrite($write_fd, $m[1]);
                        fwrite($write_fd, "\n#begin_redirecturl\n");

                        // RedirectMatch or Redirect
                        foreach ($redirects_collection as $redirect) {
                            fwrite(
                                $write_fd,
                                'RedirectMatch ' . $redirect['redirect_type'] . ' ' .
                                $redirect['old_uri'] . ' ' . $redirect['new_url'] . "\n"
                            );
                        }

                        fwrite($write_fd, "#end_redirecturl\n");
                        fclose($write_fd);
                    }
                } else {
                    if (!$write_fd = @fopen($path, 'w')) {
                        $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                        return false;
                    } elseif ($write_fd = @fopen($path, 'w')) {
                        fwrite($write_fd, $old_htaccess_content);
                        fwrite($write_fd, "\n#begin_redirecturl\n");
                        foreach ($redirects_collection as $redirect) {
                            fwrite(
                                $write_fd,
                                'RedirectMatch ' . $redirect['redirect_type'] . ' ' .
                                $redirect['old_uri'] . ' ' . $redirect['new_url'] . "\n"
                            );
                        }

                        fwrite($write_fd, "#end_redirecturl\n");
                        fclose($write_fd);
                    }
                }
            }
        }
    }
}
