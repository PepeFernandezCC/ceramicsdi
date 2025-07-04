<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../../controllers/admin/AdminHomeSendMailController.php';
require_once dirname(__FILE__).'/../../config.php';

class AdminCorreosOficialHomeController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;

    protected $context;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';

        parent::__construct();

        $this->toolbar_title = $this->l('Home', 'AdminCorreosOficialHomeController');
        $this->meta_title = $this->l('Home Correos Oficial', 'AdminCorreosOficialHomeController');

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        $this->context = Context::getContext();
    }


    public function renderView()
    {
        global $co_module_url_ps;
        $token              = Tools::getAdminTokenLite('AdminCorreosOficialSettings');

        $this->context->smarty->assign('id_language', $this->context->language->id);
        $this->context->smarty->assign('id_employee', $this->context->employee->id);
        $this->context->smarty->assign('cex_token', $token);
        $this->context->smarty->assign('token', $token);
        $this->context->smarty->assign('id_shop', $this->context->shop->id);
        $this->context->smarty->assign('id_shop_group', $this->context->shop->id_shop_group);
        
        // Logos header
        $this->context->smarty->assign('co_base_dir', $co_module_url_ps);

        $this->context->smarty->assign('dispatcher', 'index.php?controller=AdminCorreosOficialSettings&token='.$token);

        //plantilla
        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/home.tpl');

       }
}
?>