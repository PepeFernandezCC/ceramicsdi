<?php

include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');

$id_country = (int) Tools::getValue('id_country');
$id_state   = (int) Tools::getValue('id_state');
$postal     = trim((string) Tools::getValue('postal'));
$id_cart    = (int) Tools::getValue('id_cart');
$showTaxes  = Tools::getValue('taxes') == "1";

if (!$id_country || !$postal || !$id_cart) {
    echo json_encode([]);
    exit;
}

$realCart = new Cart((int)$id_cart);

if (!Validate::isLoadedObject($realCart)) {
    echo json_encode([
        'error' => 'Carrito original no válido',
    ]);
    exit;
}

$products = $realCart->getProducts();

if (empty($products)) {
    echo json_encode([
        'error' => 'El carrito no tiene productos',
    ]);
    exit;
}

/**
 * Cliente neutro para cálculo.
 * Puedes usar 1, 2, o un cliente específico creado para simulaciones.
 * Lo importante es que la dirección temporal pertenezca a este cliente.
 */
$idFakeCustomer = 1;
$fakeCustomer = new Customer((int)$idFakeCustomer);

if (!Validate::isLoadedObject($fakeCustomer)) {
    echo json_encode([
        'error' => 'Cliente temporal no válido',
    ]);
    exit;
}

/**
 * Crear dirección temporal.
 * Mejor crear una nueva que reutilizar id_address = 1.
 */
$address = new Address();
$address->id_customer = (int)$fakeCustomer->id;
$address->id_country = (int)$id_country;
$address->id_state = (int)$id_state;
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
    echo json_encode([
        'error' => 'No se pudo crear la dirección temporal',
    ]);
    exit;
}

/**
 * Crear carrito temporal.
 */
$tmpCart = new Cart();
$tmpCart->id_shop_group = (int)$realCart->id_shop_group;
$tmpCart->id_shop = (int)$realCart->id_shop;
$tmpCart->id_lang = (int)$realCart->id_lang;
$tmpCart->id_currency = (int)$realCart->id_currency;
$tmpCart->id_customer = (int)$fakeCustomer->id;
$tmpCart->id_guest = 0;
$tmpCart->id_address_delivery = (int)$address->id;
$tmpCart->id_address_invoice = (int)$address->id;
$tmpCart->id_carrier = 0;
$tmpCart->delivery_option = '';
$tmpCart->secure_key = $fakeCustomer->secure_key;
$tmpCart->recyclable = 0;
$tmpCart->gift = 0;
$tmpCart->allow_seperated_package = 0;

if (!$tmpCart->add()) {
    $address->delete();

    echo json_encode([
        'error' => 'No se pudo crear el carrito temporal',
    ]);
    exit;
}

/**
 * Copiar productos del carrito real al temporal.
 */
foreach ($products as $product) {
    $idProduct = (int)$product['id_product'];
    $idProductAttribute = (int)$product['id_product_attribute'];
    $quantity = (int)$product['cart_quantity'];
    $idCustomization = isset($product['id_customization']) ? (int)$product['id_customization'] : 0;

    if ($quantity <= 0) {
        continue;
    }

    $result = $tmpCart->updateQty(
        $quantity,
        $idProduct,
        $idProductAttribute,
        $idCustomization,
        'up',
        (int)$address->id
    );

    if (!$result) {
        $tmpCart->delete();
        $address->delete();

        echo json_encode([
            'error' => 'No se pudo copiar un producto al carrito temporal',
            'id_product' => $idProduct,
        ]);
        exit;
    }
}

/**
 * Muy importante:
 * Forzar también la dirección en las líneas del carrito.
 */
Db::getInstance()->execute('
    UPDATE `' . _DB_PREFIX_ . 'cart_product`
    SET `id_address_delivery` = ' . (int)$address->id . '
    WHERE `id_cart` = ' . (int)$tmpCart->id
);

/**
 * Recargar carrito para limpiar cachés internas del objeto.
 */
$tmpCart = new Cart((int)$tmpCart->id);

$context = Context::getContext();
$context->cart = $tmpCart;
$context->customer = $fakeCustomer;
$context->country = new Country((int)$id_country);
$context->currency = new Currency((int)$tmpCart->id_currency);

$tmpCart->id_carrier = 0;
$tmpCart->delivery_option = '';
$tmpCart->update();

$bestOption = Carrier::getCheapestDeliveryOptionByCart($tmpCart, $showTaxes);

if (!$bestOption || empty($bestOption['id_carrier'])) {
    $tmpCart->delete();
    $address->delete();

    echo json_encode([
        'error' => 'No hay transportistas disponibles',
    ]);
    exit;
}

$idCarrier = (int)$bestOption['id_carrier'];

$result = $showTaxes
    ? (float)$bestOption['price_with_tax']
    : (float)$bestOption['price_without_tax'];

$result = (float) Tools::ps_round($result, 2);

$carrier = new Carrier((int)$idCarrier);

/**
 * Limpiar carrito y dirección temporal.
 */
$tmpCart->delete();
$address->delete();

echo json_encode([
    'show_taxes' => $showTaxes,
    'id_carrier' => $idCarrier,
    'carrier_name' => Validate::isLoadedObject($carrier) ? $carrier->name : null,
    'shipping_cost' => $result,
]);

exit;