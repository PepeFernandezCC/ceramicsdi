<?php
class CcDesistimientoSuccessModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(array(
            'cc_history_link' => $this->context->link->getPageLink('history', true),
        ));
        $this->setTemplate('module:ccdesistimiento/views/templates/front/success.tpl');
    }
}
