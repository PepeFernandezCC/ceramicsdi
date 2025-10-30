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
class AdminManage404ReportController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'Manage404Report';
        $this->table = 'report404';
        $this->identifier = 'id_report';
        $this->lang = false;
        $this->bootstrap = true;
        $this->deleted = false;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        parent::__construct();
        $this->_select = 'a.*';
        $this->_group = ' GROUP BY a.`url_not_found`';
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected'),
                'confirm' => $this->module->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ],
        ];
        $this->fields_list = [
            'id_report' => [
                'title' => $this->module->l('ID'),
                'align' => 'center',
                'width' => 30,
                'orderby' => true,
                'remove_onclick' => true,
            ],
            'url_not_found' => [
                'title' => $this->module->l('404 URL'),
                'orderby' => true,
                'remove_onclick' => true,
            ],
            'id_shop' => [
                'title' => $this->module->l('Shop'),
                'remove_onclick' => true,
                'orderby' => true,
            ],
        ];
        if (Shop::isFeatureActive()) {
            $this->fields_list['id_shop'] = [
                'title' => $this->module->l('Shop'),
                'width' => 25,
                'align' => 'center',
                'class' => 'active',
                'remove_onclick' => true,
            ];
        }
    }

    public function getShopName($id_shop)
    {
        $shop = new Shop($id_shop);

        return $shop->name;
    }

    public function renderList()
    {
        $this->addRowAction('delete');
        $this->context->smarty->assign([
            'href' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=prettyurls&tab_module=redirects&module_name=prettyurls',
        ]);
        $backButton = $this->context->smarty->fetch(dirname(__FILE__) . '/../../views/templates/admin/back_button.tpl');
        return parent::renderList().$backButton;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('info')) {
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
}
