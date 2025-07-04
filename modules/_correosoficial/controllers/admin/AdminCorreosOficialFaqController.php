<?php
if (!defined('_PS_VERSION_')) {
    exit;
}



require_once dirname(__FILE__).'/../../config.php';

class AdminCorreosOficialFaqController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;


    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';

        parent::__construct();
        $this->toolbar_title = $this->l('FAQ', 'AdminCorreosOficialFaqController');
        $this->meta_title = $this->l('FAQ Correos Oficial', 'AdminCorreosOficialFaqController');

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        $this->renderView();
    }


    public function renderView()
    {
        global $co_module_url_ps;
        $token              = Tools::getAdminToken('AdminInicio');

        $this->context->controller->addCss( _PS_MODULE_DIR_.'correosoficial/views/css/back.css?v=20211201');
        $this->context->controller->addCss( _PS_MODULE_DIR_.'correosoficial/views/css/bootstrap.min.css?v=20211201');

        $this->context->controller->addJS(_PS_MODULE_DIR_.'correosoficial/views/js/bootstrap.bundle.min.js?v=20211201');

        $this->context->smarty->assign('id_language', $this->context->language->id);
        $this->context->smarty->assign('id_employee', $this->context->employee->id);
        $this->context->smarty->assign('cex_token', $token);
        $this->context->smarty->assign('id_shop', $this->context->shop->id);
        $this->context->smarty->assign('id_shop_group', $this->context->shop->id_shop_group);

        // Logos header
        $this->context->smarty->assign('co_base_dir', $co_module_url_ps);

        //plantilla
        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/faq.tpl');
    }

}