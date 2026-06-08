<?php
class CcDesistimientoEligibleModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $ssl = true;
    public $ajax = true;

    public function initContent()
    {
        parent::initContent();

        header('Content-Type: application/json; charset=utf-8');

        if (!$this->context->customer || !$this->context->customer->isLogged()) {
            die(json_encode(array('success' => false, 'orders' => array())));
        }

        $orders = $this->module->getEligibleOrdersForCustomer((int) $this->context->customer->id);
        die(json_encode(array('success' => true, 'orders' => $orders)));
    }
}
