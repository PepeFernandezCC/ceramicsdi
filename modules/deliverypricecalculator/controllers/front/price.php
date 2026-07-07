<?php

class DeliverypricecalculatorPriceModuleFrontController extends ModuleFrontController
{
    public $ajax = true;

    public function initContent()
    {
        header('Content-Type: application/json');

        $id_country = (int) Tools::getValue('id_country');
        $id_state = (int) Tools::getValue('id_state');
        $postal = trim((string) Tools::getValue('postal'));
        $id_cart = (int) Tools::getValue('id_cart');
        $showTaxes = Tools::getValue('taxes') == '1';

        if (!$id_country || !$postal || !$id_cart) {
            $this->ajaxDie(json_encode([]));
        }

        $realCart = new Cart($id_cart);

        if (!Validate::isLoadedObject($realCart)) {
            $this->ajaxDie(json_encode(['error' => 'Carrito original no válido']));
        }

        $products = $realCart->getProducts();

        if (empty($products)) {
            $this->ajaxDie(json_encode(['error' => 'El carrito no tiene productos']));
        }

        /*
         * Cliente neutro para cálculo.
         * La dirección temporal usada para el cálculo pertenece a este cliente.
         */
        $idFakeCustomer = 1;
        $fakeCustomer = new Customer($idFakeCustomer);

        if (!Validate::isLoadedObject($fakeCustomer)) {
            $this->ajaxDie(json_encode(['error' => 'Cliente temporal no válido']));
        }

        $address = new Address();
        $address->id_customer = (int) $fakeCustomer->id;
        $address->id_country = $id_country;
        $address->id_state = $id_state;
        $address->alias = 'Temporal shipping calculation';
        $address->firstname = 'Temporal';
        $address->lastname = 'Shipping';
        $address->address1 = 'Temporal address';
        $address->postcode = pSQL($postal);
        $address->city = 'Temporal';
        $address->phone = '000000000';
        $address->active = 1;
        $address->deleted = 0;

        if (!$address->add()) {
            $this->ajaxDie(json_encode(['error' => 'No se pudo crear la dirección temporal']));
        }

        $tmpCart = new Cart();
        $tmpCart->id_shop_group = (int) $realCart->id_shop_group;
        $tmpCart->id_shop = (int) $realCart->id_shop;
        $tmpCart->id_lang = (int) $realCart->id_lang;
        $tmpCart->id_currency = (int) $realCart->id_currency;
        $tmpCart->id_customer = (int) $fakeCustomer->id;
        $tmpCart->id_guest = 0;
        $tmpCart->id_address_delivery = (int) $address->id;
        $tmpCart->id_address_invoice = (int) $address->id;
        $tmpCart->id_carrier = 0;
        $tmpCart->delivery_option = '';
        $tmpCart->secure_key = $fakeCustomer->secure_key;
        $tmpCart->recyclable = 0;
        $tmpCart->gift = 0;
        $tmpCart->allow_seperated_package = 0;

        if (!$tmpCart->add()) {
            $address->delete();

            $this->ajaxDie(json_encode(['error' => 'No se pudo crear el carrito temporal']));
        }

        foreach ($products as $product) {
            $idProduct = (int) $product['id_product'];
            $idProductAttribute = (int) $product['id_product_attribute'];
            $quantity = (int) $product['cart_quantity'];
            $idCustomization = isset($product['id_customization']) ? (int) $product['id_customization'] : 0;

            if ($quantity <= 0) {
                continue;
            }

            $result = $tmpCart->updateQty(
                $quantity,
                $idProduct,
                $idProductAttribute,
                $idCustomization,
                'up',
                (int) $address->id
            );

            if (!$result) {
                $tmpCart->delete();
                $address->delete();

                $this->ajaxDie(json_encode([
                    'error' => 'No se pudo copiar un producto al carrito temporal',
                    'id_product' => $idProduct,
                ]));
            }
        }

        /*
         * Forzar también la dirección en las líneas del carrito.
         */
        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'cart_product`
            SET `id_address_delivery` = ' . (int) $address->id . '
            WHERE `id_cart` = ' . (int) $tmpCart->id
        );

        /*
         * Recargar carrito para limpiar cachés internas del objeto.
         */
        $tmpCart = new Cart((int) $tmpCart->id);

        $context = $this->context;
        $context->cart = $tmpCart;
        $context->customer = $fakeCustomer;
        $context->country = new Country($id_country);
        $context->currency = new Currency((int) $tmpCart->id_currency);

        $tmpCart->id_carrier = 0;
        $tmpCart->delivery_option = '';
        $tmpCart->update();

        $bestOption = Carrier::getCheapestDeliveryOptionByCart($tmpCart, $showTaxes);

        if (!$bestOption || empty($bestOption['id_carrier'])) {
            $tmpCart->delete();
            $address->delete();

            $this->ajaxDie(json_encode(['error' => 'No hay transportistas disponibles']));
        }

        $idCarrier = (int) $bestOption['id_carrier'];

        $tmpCart->id_carrier = $idCarrier;
        $tmpCart->delivery_option = json_encode([
            (int) $address->id => $bestOption['option_key'],
        ]);
        $tmpCart->update();

        $id_zone = Address::getZoneById((int) $address->id);

        $total = 0;
        $return = false;

        /*
         * El cálculo base se hace sin impuestos, y después se añaden
         * manualmente si procede (ver $showTaxes más abajo).
         */
        $use_tax = false;

        $default_country = new Country($id_country);
        $keepOrderPrices = false;
        $product_list = null;

        Hook::exec('actionCartGetPackageShippingCost', [
            'object' => &$tmpCart,
            'id_carrier' => &$idCarrier,
            'use_tax' => &$use_tax,
            'default_country' => &$default_country,
            'product_list' => &$product_list,
            'id_zone' => &$id_zone,
            'keepOrderPrices' => &$keepOrderPrices,
            'total' => &$total,
            'return' => &$return,
            'custom' => true,
        ]);

        if ($return) {
            $result = ($total !== false)
                ? (float) Tools::ps_round((float) $total, 2)
                : false;
        } else {
            if (method_exists($tmpCart, 'getParentPackageShippingCost')) {
                $shipping_cost = $tmpCart->getParentPackageShippingCost(
                    $idCarrier,
                    $use_tax,
                    $default_country,
                    $product_list,
                    $id_zone,
                    $keepOrderPrices
                );
            } else {
                $shipping_cost = $tmpCart->getPackageShippingCost(
                    $idCarrier,
                    $use_tax,
                    $default_country,
                    $product_list,
                    $id_zone,
                    $keepOrderPrices
                );
            }

            if ($shipping_cost === false) {
                $tmpCart->delete();
                $address->delete();

                $this->ajaxDie(json_encode([
                    'error' => 'No se pudo calcular el coste de envío personalizado',
                    'id_carrier' => $idCarrier,
                ]));
            }

            $result = $shipping_cost + (float) Tools::ps_round((float) $total, 2);
        }

        if ($result === false || $result === null) {
            $tmpCart->delete();
            $address->delete();

            $this->ajaxDie(json_encode([
                'error' => 'No se pudo calcular el coste de envío',
                'id_carrier' => $idCarrier,
            ]));
        }

        if ($showTaxes) {
            $rate = Tax::getStandardTaxByCountryId($id_country);
            $result = $result * (1 + ($rate / 100));
        }

        $result = (float) Tools::ps_round((float) $result, 2);

        $carrier = new Carrier($idCarrier);

        $shippingEstimateHtml = '';
        $shippingCalculatorModule = Module::getInstanceByName('shippingcalculator');

        if ($shippingCalculatorModule && Validate::isLoadedObject($shippingCalculatorModule) && method_exists($shippingCalculatorModule, 'calculateEstimatedDelivery')) {
            $estimatedDelivery = $shippingCalculatorModule->calculateEstimatedDelivery($tmpCart);

            if (is_array($estimatedDelivery)) {
                // Usamos el coste real calculado por transportistas en vez del que calcula shippingcalculator
                $estimatedDelivery['shipping_cost'] = $result;
                $estimatedDelivery['shipping_cost_formatted'] = Tools::displayPrice($result);
            }

            $this->context->smarty->assign([
                'has_delivery_info' => (bool) $estimatedDelivery,
                'estimated_delivery' => $estimatedDelivery,
            ]);

            $shippingEstimateHtml = $shippingCalculatorModule->display(
                _PS_MODULE_DIR_ . 'shippingcalculator/shippingcalculator.php',
                'views/templates/hook/shopping_cart_delivery.tpl'
            );
        }

        $tmpCart->delete();
        $address->delete();

        $this->ajaxDie(json_encode([
            'show_taxes' => $showTaxes,
            'id_carrier' => $idCarrier,
            'carrier_name' => Validate::isLoadedObject($carrier) ? $carrier->name : null,
            'shipping_cost' => $result,
            'shipping_cost_formatted' => Tools::displayPrice($result),
            'shipping_estimate_html' => $shippingEstimateHtml,
        ]));
    }
}
