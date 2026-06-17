<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminCcDesistimientoController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        $configureUrl = $this->context->link->getAdminLink('AdminModules', true, array(), array(
            'configure' => 'ccdesistimiento',
            'module_name' => 'ccdesistimiento',
        ));

        Tools::redirectAdmin($configureUrl);
    }
}
