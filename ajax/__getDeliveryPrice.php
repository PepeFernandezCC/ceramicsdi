<?php
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');

$id_country = (int) Tools::getValue('id_country');
$id_state   = (int) Tools::getValue('id_state');
$postal     = trim((string) Tools::getValue('postal'));
$id_cart    = (int) Tools::getValue('id_cart');
$showTaxes  = Tools::getValue('taxes') == "1" ? true : false;

if (!$id_country || !$id_state || !$postal || !$id_cart) {
    echo json_encode([]);
    exit;
}

// actualizar la dirección falsa
$sql = 'UPDATE `' . _DB_PREFIX_ . 'address`
SET `id_country` = ' . (int)$id_country . ',
    `id_state` = ' . (int)$id_state . ',
    `postcode` = "' . pSQL($postal) . '"
WHERE `id_address` = 1';

Db::getInstance()->execute($sql);

// falsear carrito
$cart = new Cart((int)$id_cart);

if (!Validate::isLoadedObject($cart)) {
    echo json_encode([
        'error' => 'Carrito no válido'
    ]);
    exit;
}

$cart->id_address_delivery = 1;
$cart->id_address_invoice = 1;
$cart->id_customer = 2;

// importante: guardar para que Prestashop recalcule correctamente
$cart->update();

// sacar opción más barata exactamente igual que en checkout
$bestOption = Carrier::getCheapestDeliveryOptionByCart($cart, $showTaxes);

if (!$bestOption || empty($bestOption['id_carrier'])) {
    echo json_encode([
        'error' => 'No hay transportistas disponibles'
    ]);
    exit;
}

$id_carrier = (int)$bestOption['id_carrier'];
$result = $showTaxes
    ? (float)$bestOption['price_with_tax']
    : (float)$bestOption['price_without_tax'];

// opcional: dejar también marcado el carrier en el carrito
$cart->id_carrier = $id_carrier;
$cart->delivery_option = json_encode([
    (string)$cart->id_address_delivery => (string)$bestOption['option_key']
]);
$cart->update();

$result = (float) Tools::ps_round($result, 2);

echo json_encode([
    'show_taxes' => $showTaxes,
    'id_carrier' => $id_carrier,
    'shipping_cost' => $result
]);
exit;