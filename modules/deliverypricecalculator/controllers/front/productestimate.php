<?php

class DeliverypricecalculatorProductestimateModuleFrontController extends ModuleFrontController
{
    public $ajax = true;

    public function initContent()
    {
        header('Content-Type: application/json');

        $id_product = (int) Tools::getValue('id_product');
        $boxes = (int) Tools::getValue('boxes');
        $id_country = (int) Tools::getValue('id_country');
        $id_state = (int) Tools::getValue('id_state');
        $postal = trim((string) Tools::getValue('postal'));

        if (!$id_product || !$boxes || !$id_country || !$postal) {
            $this->ajaxDie(json_encode(['error' => $this->translateError('missing_fields')]));
        }

        $product = new Product($id_product, false, $this->context->language->id);

        if (!Validate::isLoadedObject($product)) {
            $this->ajaxDie(json_encode(['error' => $this->translateError('invalid_product')]));
        }

        $result = [
            'product' => $this->estimateForProduct($id_product, $boxes, $id_country, $id_state, $postal),
        ];

        $this->ajaxDie(json_encode($result));
    }

    /**
     * Traduce un mensaje de error según el idioma actual del contexto.
     */
    private function translateError($key)
    {
        $translations = [
            'missing_fields' => [
                1 => 'Faltan datos obligatorios',
                2 => 'Des champs obligatoires sont manquants',
                3 => 'Required fields are missing',
                4 => 'Pflichtfelder fehlen',
                5 => 'Faltam dados obrigatórios',
                6 => 'Verplichte velden ontbreken',
            ],
            'invalid_product' => [
                1 => 'Producto no válido',
                2 => 'Produit non valide',
                3 => 'Invalid product',
                4 => 'Ungültiges Produkt',
                5 => 'Produto inválido',
                6 => 'Ongeldig product',
            ],
            'customer_error' => [
                1 => 'Cliente temporal no válido',
                2 => 'Client temporaire non valide',
                3 => 'Invalid temporary customer',
                4 => 'Ungültiger temporärer Kunde',
                5 => 'Cliente temporário inválido',
                6 => 'Ongeldige tijdelijke klant',
            ],
            'address_error' => [
                1 => 'No se pudo crear la dirección temporal',
                2 => "Impossible de créer l'adresse temporaire",
                3 => 'Could not create the temporary address',
                4 => 'Die temporäre Adresse konnte nicht erstellt werden',
                5 => 'Não foi possível criar o endereço temporário',
                6 => 'Het tijdelijke adres kon niet worden aangemaakt',
            ],
            'cart_error' => [
                1 => 'No se pudo crear el carrito temporal',
                2 => 'Impossible de créer le panier temporaire',
                3 => 'Could not create the temporary cart',
                4 => 'Der temporäre Warenkorb konnte nicht erstellt werden',
                5 => 'Não foi possível criar o carrinho temporário',
                6 => 'De tijdelijke winkelwagen kon niet worden aangemaakt',
            ],
            'add_product_error' => [
                1 => 'No se pudo añadir el producto al carrito temporal',
                2 => 'Impossible d\'ajouter le produit au panier temporaire',
                3 => 'Could not add the product to the temporary cart',
                4 => 'Das Produkt konnte nicht zum temporären Warenkorb hinzugefügt werden',
                5 => 'Não foi possível adicionar o produto ao carrinho temporário',
                6 => 'Het product kon niet aan de tijdelijke winkelwagen worden toegevoegd',
            ],
            'no_carrier_error' => [
                1 => 'No hay transportistas disponibles para este destino',
                2 => "Aucun transporteur disponible pour cette destination",
                3 => 'No carriers available for this destination',
                4 => 'Für dieses Ziel sind keine Transportunternehmen verfügbar',
                5 => 'Não há transportadoras disponíveis para este destino',
                6 => 'Er zijn geen vervoerders beschikbaar voor deze bestemming',
            ],
        ];

        $idLang = (int) $this->context->language->id;

        if (isset($translations[$key][$idLang])) {
            return $translations[$key][$idLang];
        }

        return $translations[$key][3];
    }

    /**
     * Crea un carrito temporal con el producto indicado y calcula el
     * plazo estimado de entrega y el coste de envío para un destino dado.
     */
    private function estimateForProduct($idProduct, $quantity, $idCountry, $idState, $postal)
    {
        $idFakeCustomer = 1;
        $fakeCustomer = new Customer($idFakeCustomer);

        if (!Validate::isLoadedObject($fakeCustomer)) {
            return ['error' => $this->translateError('customer_error')];
        }

        $address = new Address();
        $address->id_customer = (int) $fakeCustomer->id;
        $address->id_country = $idCountry;
        $address->id_state = $idState;
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
            return ['error' => $this->translateError('address_error')];
        }

        $tmpCart = new Cart();
        $tmpCart->id_shop_group = (int) $this->context->shop->id_shop_group;
        $tmpCart->id_shop = (int) $this->context->shop->id;
        $tmpCart->id_lang = (int) $this->context->language->id;
        $tmpCart->id_currency = (int) $this->context->currency->id;
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

            return ['error' => $this->translateError('cart_error')];
        }

        $added = $tmpCart->updateQty($quantity, $idProduct, 0, 0, 'up', (int) $address->id);

        if (!$added) {
            $tmpCart->delete();
            $address->delete();

            return ['error' => $this->translateError('add_product_error')];
        }

        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'cart_product`
            SET `id_address_delivery` = ' . (int) $address->id . '
            WHERE `id_cart` = ' . (int) $tmpCart->id
        );

        $tmpCart = new Cart((int) $tmpCart->id);

        $context = $this->context;
        $context->cart = $tmpCart;
        $context->customer = $fakeCustomer;
        $context->country = new Country($idCountry);
        $context->currency = new Currency((int) $tmpCart->id_currency);

        $tmpCart->id_carrier = 0;
        $tmpCart->delivery_option = '';
        $tmpCart->update();

        $bestOption = Carrier::getCheapestDeliveryOptionByCart($tmpCart, true);

        if (!$bestOption || empty($bestOption['id_carrier'])) {
            $tmpCart->delete();
            $address->delete();

            return ['error' => $this->translateError('no_carrier_error')];
        }

        $idCarrier = (int) $bestOption['id_carrier'];

        $tmpCart->id_carrier = $idCarrier;
        $tmpCart->delivery_option = json_encode([
            (int) $address->id => $bestOption['option_key'],
        ]);
        $tmpCart->update();

        $estimatedDelivery = null;
        $estimatedDeliveryHtml = '';
        $shippingCalculatorModule = Module::getInstanceByName('shippingcalculator');

        if ($shippingCalculatorModule && Validate::isLoadedObject($shippingCalculatorModule) && method_exists($shippingCalculatorModule, 'calculateEstimatedDelivery')) {
            $estimatedDelivery = $shippingCalculatorModule->calculateEstimatedDelivery($tmpCart);

            if (is_array($estimatedDelivery)) {
                $this->context->smarty->assign([
                    'has_delivery_info' => true,
                    'estimated_delivery' => $estimatedDelivery,
                    'module_dir' => _MODULE_DIR_ . 'shippingcalculator/',
                ]);

                $estimatedDeliveryHtml = $shippingCalculatorModule->display(
                    _PS_MODULE_DIR_ . 'shippingcalculator/shippingcalculator.php',
                    'views/templates/hook/shopping_cart_delivery.tpl'
                );
            }
        }

        $productObj = new Product($idProduct, false, $this->context->language->id);

        $response = [
            'id_product' => $idProduct,
            'name' => $productObj->name,
            'quantity' => $quantity,
            'has_delivery_info' => (bool) $estimatedDelivery,
            'estimated_delivery' => $estimatedDelivery,
            'estimated_delivery_html' => $estimatedDeliveryHtml,
        ];

        $tmpCart->delete();
        $address->delete();

        return $response;
    }
}
