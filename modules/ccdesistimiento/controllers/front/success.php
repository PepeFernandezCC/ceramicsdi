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
            'cc_t' => array(
                'success_title' => $this->module->ccL('success_title'),
                'success_message' => $this->module->ccL('success_message'),
                'back_to_orders' => $this->module->ccL('back_to_orders'),
            ),
        ));
        $this->setTemplate('module:ccdesistimiento/views/templates/front/success.tpl');
    }
}
