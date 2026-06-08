<?php
class CcDesistimientoRequestModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        $idOrder = (int) Tools::getValue('id_order');
        $order = new Order($idOrder);
        if (!Validate::isLoadedObject($order) || (int) $order->id_customer !== (int) $this->context->customer->id) {
            Tools::redirect('index.php?controller=history');
        }
        if (!$this->module->canRequestWithdrawal($order)) {
            $this->errors[] = $this->module->l('Este pedido no esta disponible para solicitar desistimiento.');
            $this->setTemplate('module:ccdesistimiento/views/templates/front/request.tpl');
            return;
        }
        $existing = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'cc_desistimiento` WHERE id_order=' . (int) $order->id);
        if ($existing > 0) {
            $this->errors[] = $this->module->l('Ya existe una solicitud de desistimiento para este pedido.');
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

    protected function processRequest(Order $order, array $products)
    {
        $token = Tools::getValue('token');
        if (!$token || $token !== Tools::getToken(false)) {
            $this->errors[] = $this->module->l('Token de seguridad no valido.');
            return;
        }
        $selected = Tools::getValue('products');
        if (!is_array($selected) || empty($selected)) {
            $this->errors[] = $this->module->l('Selecciona al menos un producto.');
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
            $this->errors[] = $this->module->l('Los productos seleccionados no son validos.');
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
            $this->errors[] = $this->module->l('No se ha podido registrar la solicitud.');
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
        $message->message = "Solicitud de desistimiento recibida el " . $date . "\nProductos:\n" . implode("\n", $productLabels) . "\nComentario:\n" . $comment;
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
        Mail::Send((int) $order->id_lang, 'withdrawal_received', 'Solicitud de desistimiento recibida', $vars, $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null, _PS_MODULE_DIR_ . 'ccdesistimiento/mails/');
        $adminEmail = Configuration::get('CC_DESISTIMIENTO_EMAIL');
        if ($adminEmail) {
            Mail::Send((int) $order->id_lang, 'withdrawal_admin', 'Nueva solicitud de desistimiento', $vars, $adminEmail, 'Ceramic Connection', null, null, null, null, _PS_MODULE_DIR_ . 'ccdesistimiento/mails/');
        }
    }
}
