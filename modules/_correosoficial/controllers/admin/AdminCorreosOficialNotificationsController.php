<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminCorreosOficialNotificationsController extends ModuleAdminController
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

        $this->toolbar_title = $this->l('Notifications', 'AdminCorreosOficialNotificationsController');
        $this->meta_title = $this->l('Notifications Correos Oficial', 'AdminCorreosOficialNotificationsController');

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        $this->context = Context::getContext();
    }

    public function init()
    {
        if (Tools::getIsset('notificationId') && !empty(Tools::getValue('notificationId'))) {
            if (Tools::getIsset('token') && Tools::getValue('token') === $this->token) {
                (new Analitica())->checkNotifications((int) Tools::getValue('notificationId'));
            }
        }
        parent::init();
    }

    public function renderView()
    {
        // Enviar un header para excluir mod_pagespeed
        header('X-Mod-Pagespeed: off');

        $accepted = Hook::exec('actionCorreosAdminControllers');

        if ($accepted) {
            $template = $accepted;
        } else {
            $template = '/views/templates/admin/notifications.tpl';
            global $co_no_soap_error;
        }

        global $co_module_url_ps;
        $this->token = Tools::getAdminTokenLite('AdminCorreosOficialNotifications');
        $notifications = (new Analitica())->getNotifications();

        if (!is_array($notifications['output'])) {
            $notifications['output'] = false;
        }

        Media::addJsDef(['correos_inView_check' => $this->l('Mark as ready and discart', 'AdminCorreosOficialNotificationsController')]);

        $this->context->smarty->assign([
            'co_base_dir' => $co_module_url_ps,
            'notifications' => $notifications['output'],
        ]);

        return $this->context->smarty->fetch(dirname(__FILE__, 3) . $template);
        
    }
}
