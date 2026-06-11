<?php
class CcDesistimientoRequestModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        $this->assignTemplateTranslations();
        $idOrder = (int) Tools::getValue('id_order');
        $order = new Order($idOrder);
        if (!Validate::isLoadedObject($order) || (int) $order->id_customer !== (int) $this->context->customer->id) {
            Tools::redirect('index.php?controller=history');
        }
        if (!$this->module->canRequestWithdrawal($order)) {
            $this->errors[] = $this->module->ccL('not_available');
            $this->setTemplate('module:ccdesistimiento/views/templates/front/request.tpl');
            return;
        }
        $existing = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'cc_desistimiento` WHERE id_order=' . (int) $order->id);
        if ($existing > 0) {
            $this->errors[] = $this->module->ccL('already_exists');
            $this->setTemplate('module:ccdesistimiento/views/templates/front/request.tpl');
            return;
        }

        $products = $order->getProducts();
        if (Tools::isSubmit('submitCcDesistimiento')) {
            $this->processRequest($order, $products);
        }

        $this->context->smarty->assign(array(
            'cc_order' => $order,
            'cc_products' => $products,
            'cc_customer' => $this->context->customer,
            'cc_return_address' => Configuration::get('CC_DESISTIMIENTO_RETURN_ADDRESS'),
            'cc_contact_email' => Configuration::get('CC_DESISTIMIENTO_EMAIL'),
            'cc_phone' => Configuration::get('CC_DESISTIMIENTO_PHONE'),
            'cc_action' => $this->context->link->getModuleLink('ccdesistimiento', 'request', array('id_order' => (int) $order->id), true),
        ));
        $this->setTemplate('module:ccdesistimiento/views/templates/front/request.tpl');
    }


    protected function assignTemplateTranslations()
    {
        $this->context->smarty->assign(array(
            'cc_t' => array(
                'request_title' => $this->module->ccL('request_title'),
                'order_label' => $this->module->ccL('order_label'),
                'customer_label' => $this->module->ccL('customer_label'),
                'request_info' => $this->module->ccL('request_info'),
                'affected_products' => $this->module->ccL('affected_products'),
                'optional_comments' => $this->module->ccL('optional_comments'),
                'return_warning' => sprintf($this->module->ccL('return_warning'), Configuration::get('CC_DESISTIMIENTO_RETURN_ADDRESS')),
                'confirm_withdrawal' => $this->module->ccL('confirm_withdrawal'),
                'back_to_orders' => $this->module->ccL('back_to_orders'),
            ),
        ));
    }

    protected function processRequest(Order $order, array $products)
    {
        $token = Tools::getValue('token');
        if (!$token || $token !== Tools::getToken(false)) {
            $this->errors[] = $this->module->ccL('invalid_token');
            return;
        }
        $selected = Tools::getValue('products');
        if (!is_array($selected) || empty($selected)) {
            $this->errors[] = $this->module->ccL('select_product');
            return;
        }
        $productLabels = array();
        foreach ($products as $product) {
            $key = (int) $product['product_id'] . '-' . (int) $product['product_attribute_id'];
            if (in_array($key, $selected, true)) {
                $productLabels[] = $product['product_name'] . ' x ' . (int) $product['product_quantity'];
            }
        }
        if (!$productLabels) {
            $this->errors[] = $this->module->ccL('invalid_products');
            return;
        }
        $customer = $this->context->customer;
        $now = date('Y-m-d H:i:s');
        $comment = trim((string) Tools::getValue('comment'));
        $ok = Db::getInstance()->insert('cc_desistimiento', array(
            'id_order' => (int) $order->id,
            'id_customer' => (int) $customer->id,
            'customer_name' => pSQL($customer->firstname . ' ' . $customer->lastname),
            'customer_email' => pSQL($customer->email),
            'products' => pSQL(implode("\n", $productLabels)),
            'comment' => pSQL($comment, true),
            'status' => 'pendiente',
            'date_add' => pSQL($now),
            'date_upd' => pSQL($now),
        ));
        if (!$ok) {
            $this->errors[] = $this->module->ccL('save_error');
            return;
        }
        $this->addOrderMessage($order, $productLabels, $comment, $now);
        $this->sendEmails($order, $productLabels, $comment, $now);
        Tools::redirect($this->context->link->getModuleLink('ccdesistimiento', 'success', array('id_order' => (int) $order->id), true));
    }

    protected function addOrderMessage(Order $order, array $productLabels, $comment, $date)
    {
        $message = new Message();
        $message->id_order = (int) $order->id;
        $message->id_customer = (int) $order->id_customer;
        $message->message = sprintf($this->module->ccL('order_message'), $date, implode("\n", $productLabels), $comment);
        $message->private = 1;
        $message->add();
    }

    protected function sendEmails(Order $order, array $productLabels, $comment, $date)
    {
        $customer = $this->context->customer;
        $vars = array(
            '{order_reference}' => $order->reference,
            '{order_id}' => (int) $order->id,
            '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
            '{customer_email}' => $customer->email,
            '{request_date}' => $date,
            '{products}' => nl2br(Tools::safeOutput(implode("\n", $productLabels))),
            '{products_txt}' => implode("\n", $productLabels),
            '{comment}' => Tools::safeOutput($comment),
            '{return_address}' => Configuration::get('CC_DESISTIMIENTO_RETURN_ADDRESS'),
            '{contact_email}' => Configuration::get('CC_DESISTIMIENTO_EMAIL'),
            '{phone}' => Configuration::get('CC_DESISTIMIENTO_PHONE'),
        );

        Mail::Send(
            (int) $order->id_lang, 
            'withdrawal_received', 
            $this->module->ccL('customer_email_subject'), 
            $vars, 
            $customer->email, 
            $customer->firstname . ' ' . $customer->lastname, 
            null, null, null, null, 
            _PS_MODULE_DIR_ . 'ccdesistimiento/mails/');
        $adminEmail = Configuration::get('CC_DESISTIMIENTO_EMAIL');

        if ($adminEmail) {
            Mail::Send((int) $order->id_lang, 'withdrawal_admin', $this->module->ccL('admin_email_subject'), $vars, $adminEmail, 'Ceramic Connection', null, null, null, null, _PS_MODULE_DIR_ . 'ccdesistimiento/mails/');
        }
    }
}
