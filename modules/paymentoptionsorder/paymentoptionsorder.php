<?php
/**
 * CERAMIC CONNECTION - Payment options order
 *
 * Lets an employee reorder the checkout payment options (Revolut card,
 * PayPal, PayPal Pay Later, Revolut Pay, Google Pay, Bizum, bank transfer...)
 * from the backoffice, without touching code.
 *
 * The list of options is self-discovering: it is populated by
 * override/classes/checkout/PaymentOptionsFinder.php every time a real
 * customer reaches the payment step, and filtered down to whatever module is
 * currently installed/enabled (see classes/PaymentOptionsOrderModel.php).
 * Nothing about "which payment methods exist" is hardcoded here - install a
 * new payment module and, after it has been seen once at checkout, it shows
 * up in this list on its own; uninstall/disable one and it disappears.
 *
 * This class is intentionally a thin controller: it reads input, delegates
 * persistence/discovery to PaymentOptionsOrderModel, and renders
 * views/templates/admin/configure.tpl (CSS/JS live in their own files under
 * views/css and views/js).
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/PaymentOptionsOrderModel.php';

class PaymentOptionsOrder extends Module
{
    public function __construct()
    {
        $this->name = 'paymentoptionsorder';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'CERAMIC CONNECTION';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CC Payment Options Order');
        $this->description = $this->l('Reordena los metodos de pago mostrados en el checkout.');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        // Nothing to seed: the registry starts empty and fills itself in as
        // real customers go through checkout (see PaymentOptionsFinder override).
        return parent::install();
    }

    public function uninstall()
    {
        return Configuration::deleteByName(PaymentOptionsOrderModel::CONFIG_KEY_ORDER)
            && Configuration::deleteByName(PaymentOptionsOrderModel::CONFIG_KEY_REGISTRY)
            && parent::uninstall();
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitPaymentOptionsOrder')) {
            $output .= $this->processSave();
        }

        return $output . $this->renderConfigureView();
    }

    protected function processSave()
    {
        $submitted = Tools::getValue('payment_options_order', '');
        $submittedKeys = array_filter(array_map('trim', explode(',', $submitted)));

        PaymentOptionsOrderModel::saveOrder($submittedKeys);

        return $this->displayConfirmation($this->l('Orden de metodos de pago guardado correctamente.'));
    }

    protected function renderConfigureView()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');

        $this->context->smarty->assign([
            'poo_items' => PaymentOptionsOrderModel::getOrderedItemsForDisplay(),
            'poo_form_action' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
}
