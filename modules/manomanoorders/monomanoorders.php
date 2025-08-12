<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class ManomanoOrders extends Module
{
    private $apiUrl = 'https://partnersapi.manomano.com'; // Endpoint ficticio para ejemplo
    private $clientId = 'prestashop';
    private $clientSecret = 'hblp7UMma3Ph1qfDGiY7qsT3FfBkbsbk';
    private $accessToken = 'FICTICIO_ACCESS_TOKEN';

    public function __construct()
    {
        $this->name = 'manomanoorders';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'José Fernández';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ManoMano Pedidos');
        $this->description = $this->l('Importa pedidos de ManoMano a PrestaShop con un botón manual.');

        $this->confirmUninstall = $this->l('¿Estás seguro que deseas desinstalar?');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayBackOfficeHeader');
    }

    public function getContent()
    {
        $output = '<h2>'.$this->displayName.'</h2>';
        $output .= '<p>'.$this->l('Pulsa el botón para sincronizar pedidos de ManoMano').'</p>';
        $output .= '<form method="post">';
        $output .= '<input type="submit" name="sync_manomano" class="btn btn-primary" value="'.$this->l('Sincronizar Pedidos').'" />';
        $output .= '</form>';

        if (Tools::isSubmit('sync_manomano')) {
            $output .= '<br>'.$this->syncOrders();
        }

        return $output;
    }

    // En la clase ManomanoOrders, reemplaza el método syncOrders() por:

    private function syncOrders()
    {
        $apiBase = 'https://partnersapi.manomano.com';
        $url = $apiBase . '/orders/v1/orders';

        // Parámetros de ejemplo, ajustables:
        $params = http_build_query([
            'seller_contract_id'     => 'FICTICIO_CONTRACT_ID',
            'status'                 => 'PENDING',
            'created_at_start'       => '2025-08-01T00:00:00Z',
            'created_at_end'         => '2025-08-11T23:59:59Z',
            'limit'                  => 50,
            'page'                   => 1,
        ]);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->accessToken,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode == 429) {
            return $this->l('Límite de peticiones alcanzado (429). Intenta más tarde.');
        }

        if ($httpCode != 200) {
            return $this->l('Error al solicitar los pedidos: HTTP ' . $httpCode);
        }

        $data = json_decode($response, true);
        if (empty($data['orders']) || !is_array($data['orders'])) {
            return $this->l('No se encontraron pedidos.');
        }

        $imported = 0;
        foreach ($data['orders'] as $orderData) {
            if ($this->createPrestashopOrder($orderData)) {
                $imported++;
            }
        }

        return $this->l('Pedidos importados: ') . $imported;
    }


    private function createPrestashopOrder($orderData)
    {
        // Buscar si el pedido ya existe para evitar duplicados
        $existingOrder = Db::getInstance()->getValue('SELECT id_order FROM '._DB_PREFIX_.'orders WHERE reference = "'.pSQL($orderData['order_id']).'"');
        if ($existingOrder) {
            return false; // Pedido ya existe
        }

        // Crear cliente o usar uno existente
        $customer = new Customer();
        $customer->firstname = $orderData['customer']['firstname'];
        $customer->lastname = $orderData['customer']['lastname'];
        $customer->email = $orderData['customer']['email'];
        $customer->id_gender = 1; // Opcional, por ejemplo
        $customer->passwd = Tools::encrypt('manomano'); // Contraseña dummy
        $customer->active = 1;

        // Buscar cliente por email
        $existingCustomerId = Customer::customerExists($orderData['customer']['email'], true);
        if ($existingCustomerId) {
            $customer->id = (int)$existingCustomerId;
            $customer->update();
        } else {
            $customer->add();
        }

        // Crear dirección
        $address = new Address();
        $address->id_customer = $customer->id;
        $address->alias = 'Dirección ManoMano';
        $address->lastname = $customer->lastname;
        $address->firstname = $customer->firstname;
        $address->address1 = $orderData['customer']['address'];
        $address->postcode = $orderData['customer']['postcode'];
        $address->city = $orderData['customer']['city'];
        $address->id_country = Country::getByIso($orderData['customer']['country']);
        $address->phone = '000000000';
        $address->add();

        // Crear carrito
        $cart = new Cart();
        $cart->id_customer = $customer->id;
        $cart->id_address_delivery = $address->id;
        $cart->id_address_invoice = $address->id;
        $cart->id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        $cart->id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $cart->add();

        // Añadir productos al carrito
        foreach ($orderData['products'] as $product) {
            $idProduct = (int)Product::getIdByReference($product['reference']);
            if ($idProduct) {
                $cart->updateQty($product['quantity'], $idProduct);
            }
        }

        // Crear pedido con estado 2 (Pago aceptado)
        $paymentModule = 'manomano_payment'; // Módulo de pago ficticio
        $orderStatus = 2;

        $orderId = Order::getIdByCartId($cart->id);
        if (!$orderId) {
            $order = Order::createOrder(
                $cart->id,
                $orderStatus,
                $orderData['total'],
                $paymentModule,
                null,
                [],
                null,
                false,
                $customer->secure_key
            );

            if ($order) {
                // Guardar referencia ManoMano en tabla orders (campo reference)
                Db::getInstance()->update(
                    'orders',
                    ['reference' => pSQL($orderData['order_id'])],
                    'id_order = '.(int)$order->id
                );

                return true;
            }
        }
        return false;
    }
}
