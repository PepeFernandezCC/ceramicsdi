<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminManoManoOrdersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'manomano_orders';
        parent::__construct();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJS($this->module->getPathUri().'views/js/manomano_admin.js');
    }

    public function initContent()
    {
        parent::initContent();

        $orders = $this->getManoManoOrders();
        $template = 'module:' . $this->module->name . '/views/templates/admin/orders.tpl';
        $this->context->smarty->assign([
            'orders' => $orders,
            'module_dir' => $this->module->getPathUri(),
        ]);

        $this->context->smarty->assign([
            'content' => $this->content . $this->module->fetch($template)
        ]);
    }

    private function getManoManoOrders()
    {
        $SELLER_ID_ES = '40134643';
        $SELLER_ID_DE = '42401412';
        $SELLER_ID_FR = '42401388';
       
        $apiKey = Configuration::get('MM_API_KEY');
        $sellerIds = [
            'es' => $SELLER_ID_ES,
            'de' => $SELLER_ID_DE,
            'fr' => $SELLER_ID_FR
        ];  
        
        if (empty($apiKey) || empty($sellerIds)) {
            return ['error' => 'Configura API Key y Seller ID en la configuración del módulo.'];
        }

        $allOrders = [];

        foreach($sellerIds as $country=>$sellerId) {
            if (empty($sellerId)) {
                continue; 
            }
            
            $url = 'https://partnersapi.manomano.com/orders/v1/orders?seller_contract_id=' . urlencode($sellerId);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'x-api-key: ' . $apiKey,
                'x-thirdparty-name: Prestashop_1.7.11'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                return ['error' => 'Error CURL: ' . $err];
            }
            $data = json_decode($response, true);
            if (!$data) {
                return ['error' => 'Respuesta no válida de ManoMano: ' . htmlspecialchars($response)];
            }

            $orders = $data['content'] ?? [];

            // Filtrar pedidos ya importados
            $importedRefs = Db::getInstance()->executeS('SELECT order_reference FROM `'._DB_PREFIX_.'mm_orders_imported`');
            $importedRefs = array_column($importedRefs, 'order_reference');

            $orders = array_filter($orders, function($o) use ($importedRefs) {
                return !in_array($o['order_reference'], $importedRefs);
            });

            // Acumular todos los pedidos
            $allOrders = array_merge($allOrders, $orders);
            
        }

        return $allOrders;
    }

    public function postProcess()
    {
        parent::postProcess();

        // Importar pedido individual
        if (Tools::isSubmit('importOrder')) {
            $payload = Tools::getValue('order_payload');
            if (!$payload) {
                $this->errors[] = 'No se recibió payload.';
                return;
            }
            $order = json_decode(htmlspecialchars_decode($payload), true);
            if (!$order) {
                $this->errors[] = 'Payload JSON inválido.';
                return;
            }

            $result = $this->importOrderToPrestashop($order);

            if ($result['success']) {
                // Guardar en tabla de pedidos importados
                Db::getInstance()->insert('mm_orders_imported', [
                    'order_reference' => pSQL($order['order_reference']),
                    'date_add' => date('Y-m-d H:i:s')
                ]);

                // Cambiar estado del pedido
                $id_order = $result['id_order'];
                $stateMap = [
                    'WAITING_PAYMENT' => 15,
                    'PENDING'         => 906,
                    'REFUSED'         => 6,
                    'PREPARATION'     => 3,
                    'SHIPPED'         => 4,
                    'REFUNDED'        => 7,
                    'REFUNDING'       => 7
                ];
                $orderState = $stateMap[$order['status']] ?? (int)Configuration::get('PS_OS_PAYMENT');

                Db::getInstance()->update('orders', [
                    'current_state' => (int)$orderState
                ], 'id_order = '.(int)$id_order);

                Db::getInstance()->insert('order_history', [
                    'id_employee'     => '0',
                    'id_order'        => (int)$id_order,
                    'id_order_state'  => (int)$orderState,
                    'date_add'        => date('Y-m-d H:i:s')
                ]);

                $this->confirmations[] = $result['message'];
            } else {
                $this->errors[] = $result['message'];
            }
        }

        // Importar todos los pedidos
        if (Tools::isSubmit('importAllOrders')) {
            $orders = $this->getManoManoOrders();
            $count = 0;
            foreach ($orders as $order) {
                $result = $this->importOrderToPrestashop($order);
                if ($result['success']) {
                    Db::getInstance()->insert('mm_orders_imported', [
                        'order_reference' => pSQL($order['order_reference']),
                        'date_add' => date('Y-m-d H:i:s')
                    ]);

                    // Cambiar estado del pedido
                    $id_order = $result['id_order'];
                    $stateMap = [
                        'WAITING_PAYMENT' => 15,
                        'PENDING'         => 906,
                        'REFUSED'         => 6,
                        'PREPARATION'     => 3,
                        'SHIPPED'         => 4,
                        'REFUNDED'        => 7,
                        'REFUNDING'       => 7
                    ];
                    $orderState = $stateMap[$order['status']] ?? (int)Configuration::get('PS_OS_PAYMENT');

                    Db::getInstance()->update('orders', [
                        'current_state' => (int)$orderState
                    ], 'id_order = '.(int)$id_order);

                    Db::getInstance()->insert('order_history', [
                        'id_employee'     => '0',
                        'id_order'        => (int)$id_order,
                        'id_order_state'  => (int)$orderState,
                        'date_add'        => date('Y-m-d H:i:s')
                    ]);
                    $count++;
                }

            }
            $this->confirmations[] = $this->l('Pedidos importados correctamente: '.$count);
        }
    }
    private function importOrderToPrestashop(array $mmOrder)
    {
        try {
            $context = Context::getContext();

            // 1) Customer
            $email = $mmOrder['addresses']['billing']['email'] ?? ('mm_'.$mmOrder['order_reference'].'@manomano.local');
            $firstname = $mmOrder['addresses']['billing']['firstname'] ?? 'Cliente';
            $lastname = $mmOrder['addresses']['billing']['lastname'] ?? 'ManoMano';

            $customer = new Customer();
            $existing = Customer::getCustomersByEmail($email);
            if (!empty($existing) && isset($existing[0]['id_customer'])) {
                $customer = new Customer($existing[0]['id_customer']);
            } else {
                $customer->email = $email;
                $customer->firstname = $firstname;
                $customer->lastname = $lastname;
                $customer->passwd = Tools::encrypt(Tools::passwdGen());
                $customer->add();
            }

            // 2) Address
            $addr = $mmOrder['addresses']['shipping'] ?? $mmOrder['addresses']['billing'];
            $address = new Address();
            $address->id_customer = $customer->id;
            $address->alias = 'ManoMano '.$mmOrder['order_reference'];
            $address->lastname = $addr['lastname'] ?? $lastname;
            $address->firstname = $addr['firstname'] ?? $firstname;
            $address->address1 = $addr['address_line1'] ?? '';
            $address->address2 = $addr['address_line2'] ?? '';
            $address->postcode = $addr['zipcode'] ?? '';
            $address->city = $addr['city'] ?? '';
            $countryIso = $addr['country_iso'] ?? null;
            $id_country = 0;
            if ($countryIso) {
                $country = Country::getByIso($countryIso);
                if ($country) {
                    $id_country = $country;
                }
            }
            $address->id_country = $id_country ?: 0;
            $address->id_state = 0;
            $address->phone = $addr['phone'] ?? '';
            $address->add();

            // 3) Cart temporal
            $cart = new Cart();
            $cart->id_customer = $customer->id;
            $cart->id_address_delivery = $address->id;
            $cart->id_address_invoice = $address->id;
            $cart->id_currency = (int)$context->currency->id;
            $cart->id_lang = (int)$context->language->id;
            $cart->id_carrier = '251'; // TRANSAHER
            $cart->add();

            foreach ($mmOrder['products'] as $p) {
                $id_product = Product::getIdByReference($p['seller_sku'] ?? '');
                if (!$id_product) continue;
                $qty = (int)($p['quantity'] ?? 1);
                $cart->updateQty($qty, $id_product, null, false);
            }
            $cart->update();

            // 4) Validar pedido con validateOrder (Pago aceptado)
            $paymentModule = new ManoManoImportPayment();
            $order_status = (int)Configuration::get('PS_OS_PAYMENT');
            $total = (float)$cart->getOrderTotal(true, Cart::BOTH);

            $paymentModule->validateOrder(
                $cart->id,
                $order_status,
                $total,
                $paymentModule->displayName,
                'Imported from ManoMano: '.$mmOrder['order_reference'],
                [],
                (int)$cart->id_currency,
                false,
                $customer->secure_key
            );

            // 5) Sobrescribir totales exactos de ManoMano
            $id_order = Order::getIdByCartId($cart->id);
            if (!$id_order) {
                return ['success' => false, 'message' => 'Error al obtener ID de pedido tras validateOrder.'];
            }

            // Actualizar ps_orders
            Db::getInstance()->update('orders', [
                'total_paid' => (float)$mmOrder['total_price']['amount'],
                'id_carrier' => '251', // TRANSAHER
                'total_paid_tax_incl' => (float)$mmOrder['total_price']['amount'],
                'total_paid_tax_excl' => (float)$mmOrder['total_price_excluding_vat']['amount'],
                'total_paid_real' => (float)$mmOrder['total_price']['amount'],
                'total_products' => (float)$mmOrder['products_price_excluding_vat']['amount'],
                'total_products_wt' => (float)$mmOrder['products_price']['amount'],
                'total_shipping' => (float)$mmOrder['shipping_price']['amount'],
                'total_shipping_tax_incl' => (float)$mmOrder['shipping_price']['amount'],
                'total_shipping_tax_excl' => (float)$mmOrder['shipping_price_excluding_vat']['amount'],
                'total_discounts' => (float)($mmOrder['total_discount']['amount'] ?? 0),
                'total_discounts_tax_incl' => (float)($mmOrder['total_discount']['amount'] ?? 0),
                'total_discounts_tax_excl' => (float)($mmOrder['total_discount']['amount'] ?? 0)
            ], 'id_order = '.(int)$id_order);

            // Actualizar order_detail
            $orderObj = new Order($id_order);
            foreach ($orderObj->getOrderDetailList() as $detail) {
                $prodRef = $detail['product_reference'];
                foreach ($mmOrder['products'] as $p) {
                    if (($p['seller_sku'] ?? '') === $prodRef) {
                        $unit_excl = (float)($p['product_price_excluding_vat']['amount'] ?? 0);
                        $unit_incl = (float)($p['product_price']['amount'] ?? 0);
                        Db::getInstance()->update('order_detail', [
                            'product_price' => $unit_excl,
                            'unit_price_tax_incl' => $unit_incl,
                            'total_price_tax_incl' => $unit_incl * (int)$p['quantity'],
                            'total_price_tax_excl' => $unit_excl * (int)$p['quantity'],
                        ], 'id_order_detail = '.(int)$detail['id_order_detail']);
                    }
                }
            }

            // Actualizar order_carrier
            $orderCarrier = new OrderCarrier($orderObj->getIdOrderCarrier());
            Db::getInstance()->update('order_carrier', [
                'id_carrier' => '251', // TRANSAHER
                'shipping_cost_tax_excl' => (float)$mmOrder['shipping_price_excluding_vat']['amount'],
                'shipping_cost_tax_incl' => (float)$mmOrder['shipping_price']['amount']
            ], 'id_order_carrier = '.(int)$orderCarrier->id);

            // Actualizar order_payment
            Db::getInstance()->update('order_payment', [
                'amount' => (float)$mmOrder['total_price']['amount']
            ], 'order_reference = "'.pSQL($orderObj->reference).'"');

            return ['success' => true, 'message' => 'Pedido importado correctamente: '.$mmOrder['order_reference'], 'id_order' => $id_order];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Excepción: '.$e->getMessage()];
        }
    }
}
