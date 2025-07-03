<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialSenders.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialConfig.php';

require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Cron/CronCorreosOficial.php';
require_once dirname(__FILE__) . '/../../config.php';

class AdminCorreosOficialUtilitiesController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;

    public $token;

    protected $context;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();

        $this->toolbar_title = $this->l('Utilities', 'AdminCorreosOficialUtilitiesController');
        $this->meta_title = $this->l('Utilities Correos Oficial', 'AdminCorreosOficialUtilitiesController');

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        $this->context = Context::getContext();
    }

    public function renderView()
    {
        // Enviar un header para excluir mod_pagespeed
        header('X-Mod-Pagespeed: off');

        $accepted = Hook::exec('actionCorreosAdminControllers');

        if ($accepted) {
            $template = $accepted;
        } else {
            $template = '/views/templates/admin/utilities.tpl';
            global $co_no_soap_error;
        }

        global $co_module_url_ps;
        $this->token = Tools::getAdminTokenLite('AdminCorreosOficialUtilities');

        $this->context->smarty->assign('id_language', $this->context->language->id);
        $this->context->smarty->assign('id_employee', $this->context->employee->id);

        $this->context->smarty->assign('id_shop', $this->context->shop->id);
        $this->context->smarty->assign('id_shop_group', $this->context->shop->id_shop_group);
        $this->context->smarty->assign('token', $this->token);

        $this->context->smarty->assign("select_label_options", [
            LABEL_TYPE_THERMAL  => 'Térmica',
            LABEL_TYPE_ADHESIVE => 'Adhesiva'
         /* LABEL_TYPE_HALF     => 'Medio folio', */
        ]);
        $defaultLabel = CorreosOficialConfigDao::getConfigValue('DefaultLabel');
        
        // Comprobamos que las dimensiones por defecto están activas
		$activateDimensionsByDefault = CorreosOficialConfig::checkDimensionsByDefaultActivated();

		$this->context->smarty->assign([
			'activateDimensionsByDefault' => $activateDimensionsByDefault,
			'dimensionsByDefaultHeight' => (int)CorreosOficialConfigDao::getConfigValue('DimensionsByDefaultHeight'),
			'dimensionsByDefaultLarge' => (int)CorreosOficialConfigDao::getConfigValue('DimensionsByDefaultLarge'),
			'dimensionsByDefaultWidth' => (int)CorreosOficialConfigDao::getConfigValue('DimensionsByDefaultWidth'),
		]);
        $this->context->smarty->assign('DefaultLabel', $defaultLabel);

        $this->context->smarty->assign('select_label_options_format', [
            LABEL_FORMAT_STANDAR => 'Papel 4 etiquetas',
            LABEL_FORMAT_3A4 => 'Papel 3 etiquetas (Solo CEX)',
            /* LABEL_FORMAT_4A4 => '4/3A' */
        ]);

        /**Se crea el if porque las versiones superiores a 7.4 no soportan que llegue un bool a un array inexistente */
        $sender_hours = CorreosOficialSenders::getDefaultTime();
        if (!empty($sender_hours)) {
            $this->context->smarty->assign('pickup_from', $sender_hours['sender_from_time']);
            $this->context->smarty->assign('pickup_to', $sender_hours['sender_to_time']);
        } else {
            $this->context->smarty->assign('pickup_from', '');
            $this->context->smarty->assign('pickup_to', '');
        }
        $default_sender = CorreosOficialSenders::getDefaultSender();
        $this->context->smarty->assign('default_sender', $default_sender);

        $senders = CorreosOficialSenders::getSenders();
		$select_senders_options = array();
		foreach ($senders as $sender) {
			$select_senders_options[] = array( 'id' => $sender['id'], 'name' => $sender['sender_name'] );
		}
		$this->context->smarty->assign('select_senders_options', $select_senders_options);

        // Logos header
        $this->context->smarty->assign('co_base_dir', $co_module_url_ps);

        if(_PS_VERSION_ < '1.7.6.8') {
            $order_token = Tools::getAdminToken('AdminOrders'.
                (int)Tab::getIdFromClassName('AdminOrders').
                (int)$this->context->employee->id);
        } else {
            // Token pedidos
            $link = $this->context->link->getAdminLink('AdminOrders');
            $pos = strpos($link, '=');
            $order_token = substr($link, $pos + 1);
        }
        $utilitiesToken = Tools::getAdminTokenLite('AdminCorreosOficialUtilitiesAjax');
        
        $this->context->smarty->assign('prestashopVersion', _PS_VERSION_);
        $this->context->smarty->assign('order_token', $order_token);
        $this->context->smarty->assign('utilities_token', $utilitiesToken);

        //plantilla
        return $this->context->smarty->fetch(dirname(__FILE__,3) . $template);
    }
}
