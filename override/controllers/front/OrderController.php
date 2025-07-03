<?php
class OrderController extends OrderControllerCore
{
    /*
    * module: eicaptcha
    * date: 2025-07-01 11:48:14
    * version: 2.5.1
    */
    public function postProcess()
    {
        if (
            Tools::isSubmit('submitCreate')
            && version_compare(_PS_VERSION_, '8.2.1', '<')
            && Module::isInstalled('eicaptcha')
            && Module::isEnabled('eicaptcha')
            && false === Module::getInstanceByName('eicaptcha')->hookActionCustomerRegisterSubmitCaptcha([])
            && !empty($this->errors)
        ) {
            unset($_POST['submitCreate']);
        }
        parent::postProcess();
    }
}
