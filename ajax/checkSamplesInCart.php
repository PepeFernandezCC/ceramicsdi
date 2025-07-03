<?php
// Incluye el entorno de Prestashop
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');
$canAddSample = false;

//Obtener carrito
$id_cart = Tools::getValue('id_cart');
$cart = new Cart((int) $id_cart);

//obtener numero de muestras en el carrito
$elements = $cart->getSamplesNumberInCart();

if ($elements <= 7) {
    $canAddSample = true;
}

echo json_encode([
    'can_add_sample' => $canAddSample,
    'elements' =>  $elements
]);
exit;


