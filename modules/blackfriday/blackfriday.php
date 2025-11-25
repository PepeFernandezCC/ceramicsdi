<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class BlackFriday extends Module
{
    // Intervalo de fechas en que aplica la promo
    const START_DATE = '2025-11-24';
    const END_DATE   = '2025-11-30';

    // Condiciones
    const CATEGORY_ID  = 1751;   // categoría "muestra"
    const COUNTRY_ISO  = 'ES';   // España

    public $bootstrap = true;

    public function __construct()
    {
        $this->name = 'blackfriday';
        $this->tab = 'pricing_promotion';
        $this->version = '4.0.0';
        $this->author = 'jose Fernandez';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->l('Black Friday - 50% envío muestras');
        $this->description = $this->l('Reduce a la mitad los gastos de envío de pedidos de muestras a España y muestra el descuento en checkout y factura.');
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('actionCartGetPackageShippingCost')
            && $this->registerHook('actionValidateOrder')
            && $this->registerCustomHook(
                'displayBlackFridayShippingDiscount',
                'Black Friday shipping discount',
                'Muestra el descuento de envío en el resumen del carrito'
            )
            && $this->registerHook('displayPDFInvoice');
    }

    public function uninstall()
    {
        // Si quieres, puedes comentar esto para mantener el histórico:
        // Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'blackfriday_shipping`');
        return parent::uninstall();
    }

    protected function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blackfriday_shipping` (
            `id_blackfriday_shipping` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_cart` INT UNSIGNED NOT NULL,
            `id_order` INT UNSIGNED DEFAULT NULL,
            `original_shipping` DECIMAL(20,6) NOT NULL,
            `discounted_shipping` DECIMAL(20,6) NOT NULL,
            `discount_amount` DECIMAL(20,6) NOT NULL,
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_blackfriday_shipping`),
            KEY `idx_bf_cart` (`id_cart`),
            KEY `idx_bf_order` (`id_order`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        return Db::getInstance()->execute($sql);
    }

    protected function registerCustomHook($name, $title, $description = '')
    {
        $idHook = (int)Hook::getIdByName($name);
        if ($idHook <= 0) {
            $hook = new Hook();
            $hook->name = pSQL($name);
            $hook->title = pSQL($title);
            $hook->description = pSQL($description);
            $hook->position = 1;
            $hook->live_edit = 0;
            if (!$hook->add()) {
                return false;
            }
        }

        return $this->registerHook($name);
    }

    /* ================== BACK-OFFICE: listado ================== */

    public function getContent()
    {
        $html = '<div class="panel">';
        $html .= '<h3><i class="icon icon-tags"></i> '.$this->displayName.'</h3>';
        $html .= '<p>'.$this->l('Listado de descuentos de envío aplicados (50% muestras a España).').'</p>';
        $html .= $this->renderAdminList();
        $html .= '</div>';

        return $html;
    }

    protected function renderAdminList()
    {
        $sql = 'SELECT bfs.*, o.reference
                FROM `'._DB_PREFIX_.'blackfriday_shipping` bfs
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON (bfs.id_order = o.id_order)
                ORDER BY bfs.date_add DESC
                LIMIT 100';

        $rows = Db::getInstance()->executeS($sql);

        if (!$rows) {
            return '<p>'.$this->l('Todavía no se ha aplicado ningún descuento.').'</p>';
        }

        $currency = $this->context->currency;

        $html = '<div class="table-responsive">';
        $html .= '<table class="table">';
        $html .= '<thead>
            <tr>
                <th>'.$this->l('Fecha').'</th>
                <th>'.$this->l('ID Carrito').'</th>
                <th>'.$this->l('ID Pedido').'</th>
                <th>'.$this->l('Referencia').'</th>
                <th class="text-right">'.$this->l('Envío original').'</th>
                <th class="text-right">'.$this->l('Envío con descuento').'</th>
                <th class="text-right">'.$this->l('Descuento aplicado').'</th>
            </tr>
        </thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            $html .= '<td>'.htmlspecialchars($row['date_add']).'</td>';
            $html .= '<td>'.(int)$row['id_cart'].'</td>';
            $html .= '<td>'.((int)$row['id_order'] ?: '-').'</td>';
            $html .= '<td>'.($row['reference'] ? htmlspecialchars($row['reference']) : '-').'</td>';
            $html .= '<td class="text-right">'.Tools::displayPrice($row['original_shipping'], $currency).'</td>';
            $html .= '<td class="text-right">'.Tools::displayPrice($row['discounted_shipping'], $currency).'</td>';
            $html .= '<td class="text-right">-'.Tools::displayPrice($row['discount_amount'], $currency).'</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    /* ================== LÓGICA DE DESCUENTO ================== */

    /**
     * Aquí rebajamos el coste de envío del carrito y guardamos el registro.
     */
    public function hookActionCartGetPackageShippingCost($params)
    {
        /** @var Cart $cart */
        $cart = isset($params['cart']) ? $params['cart'] : (isset($params['object']) ? $params['object'] : null);
        if (!$cart instanceof Cart || !Validate::isLoadedObject($cart)) {
            return;
        }

        $idCart = (int)$cart->id;

        // Si no estamos en fechas o no se cumplen condiciones, no tocamos nada ni guardamos
        if (!$this->isInPeriod() || !$this->conditionsMet($cart)) {
            return;
        }

        // Coste base del envío SIN este hook
        $shipping_cost = $cart->getParentPackageShippingCost(
            $params['id_carrier'],
            $params['use_tax'],
            $params['default_country'],
            $params['product_list'],
            $params['id_zone'],
            $params['keepOrderPrices']
        );

        if ($shipping_cost === false || $shipping_cost <= 0) {
            return;
        }

        // Mitad del envío
        $discounted = Tools::ps_round($shipping_cost / 2, 2);
        $discount   = $shipping_cost - $discounted;

        // Aplicamos el nuevo coste al carrito actual
        $params['total']  = $discounted;
        $params['return'] = true;

        // Guardamos / actualizamos registro SIEMPRE que aplicamos descuento
        $this->saveCartRow($idCart, $shipping_cost, $discounted, $discount);
    }

    /**
     * Cuando se valida el pedido, enlazamos el carrito a la orden.
     */
    public function hookActionValidateOrder($params)
    {
        /** @var Order $order */
        $order = $params['order'];
        /** @var Cart $cart */
        $cart = $params['cart'];

        if (!Validate::isLoadedObject($order) || !Validate::isLoadedObject($cart)) {
            return;
        }

        Db::getInstance()->update(
            _DB_PREFIX_.'blackfriday_shipping',
            ['id_order' => (int)$order->id],
            'id_cart='.(int)$cart->id
        );
    }

    /* =============== HOOKS DE VISUALIZACIÓN =============== */

    public function hookDisplayBlackFridayShippingDiscount($params)
    {
        $cart = $this->context->cart;
        if (!$cart instanceof Cart || !Validate::isLoadedObject($cart)) {
            return '';
        }

        // Solo mostramos si AHORA mismo se cumplen las condiciones
        if (!$this->isInPeriod() || !$this->conditionsMet($cart)) {
            return '';
        }

        // Usamos el envío actual para calcular original y descuento,
        // así no dependemos de que la tabla se haya llenado o no.
        $shipping = (float)$cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
        if ($shipping <= 0) {
            return '';
        }

        $original = Tools::ps_round($shipping * 2, 2);
        $discount = $original - $shipping;

        $this->context->smarty->assign([
            'bf_original_shipping'   => $original,
            'bf_discounted_shipping' => $shipping,
            'bf_discount_amount'     => $discount,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/checkout_discount.tpl');
    }

    public function hookDisplayPDFInvoice($params)
    {
        $object = isset($params['object']) ? $params['object'] : null;

        if ($object instanceof OrderInvoice) {
            $idOrder = (int)$object->id_order;
        } elseif ($object instanceof Order) {
            $idOrder = (int)$object->id;
        } else {
            return '';
        }

        $row = $this->getByOrder($idOrder);
        if (!$row || (float)$row['discount_amount'] <= 0) {
            return '';
        }

        $this->context->smarty->assign([
            'bf_discount_amount' => (float)$row['discount_amount'],
        ]);

        return $this->display(__FILE__, 'views/templates/hook/pdf_discount.tpl');
    }

    /* ================== FUNCIONES DE APOYO ================== */

    protected function isInPeriod()
    {
        $today = date('Y-m-d');
        return ($today >= static::START_DATE && $today <= static::END_DATE);
    }

    protected function conditionsMet(Cart $cart)
    {
        // 1) Dirección de entrega a España
        $idAddress = (int)$cart->id_address_delivery;
        if ($idAddress <= 0) {
            return false;
        }

        $address = new Address($idAddress);
        if (!Validate::isLoadedObject($address)) {
            return false;
        }

        $country = new Country((int)$address->id_country);
        if (!Validate::isLoadedObject($country)) {
            return false;
        }

        if (strtoupper($country->iso_code) !== static::COUNTRY_ISO) {
            return false;
        }

        // 2) Todos los productos de la categoría 1751
        $products = $cart->getProducts();
        if (empty($products)) {
            return false;
        }

        $context = Context::getContext();
        $idLang = (int)$context->language->id;

        foreach ($products as $row) {
            $product = new Product((int)$row['id_product'], false, $idLang);
            if (!Validate::isLoadedObject($product)) {
                return false;
            }
            $categories = $product->getCategories();
            if (empty($categories) || !in_array(static::CATEGORY_ID, array_map('intval', $categories), true)) {
                return false;
            }
        }

        return true;
    }

    protected function getByOrder($idOrder)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'blackfriday_shipping`
                WHERE id_order='.(int)$idOrder.'
                ORDER BY id_blackfriday_shipping DESC';
        return Db::getInstance()->getRow($sql);
    }

    protected function getByCart($idCart)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'blackfriday_shipping`
                WHERE id_cart='.(int)$idCart.'
                ORDER BY id_blackfriday_shipping DESC';
        return Db::getInstance()->getRow($sql);
    }

    protected function saveCartRow($idCart, $original, $discounted, $discount)
    {
        $row = $this->getByCart($idCart);

        $data = [
            'id_cart'             => (int)$idCart,
            'original_shipping'   => (float)$original,
            'discounted_shipping' => (float)$discounted,
            'discount_amount'     => (float)$discount,
            'date_add'            => date('Y-m-d H:i:s'),
        ];

        if ($row) {
            return Db::getInstance()->update(
               'blackfriday_shipping',
                $data,
                'id_blackfriday_shipping='.(int)$row['id_blackfriday_shipping']
            );
        } else {
            return Db::getInstance()->insert('blackfriday_shipping', $data);
        }
    }
}
