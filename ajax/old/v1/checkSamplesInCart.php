<?php
// Incluye el entorno de Prestashop
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');
$canAddSample = false;

//Obtener carrito
$id_cart = Tools::getValue('id_cart');
$id_product = Tools::getValue('id_product');
$cart = new Cart((int) $id_cart);
$product = new Product((int)$id_product);

//obtener numero de muestras en el carrito
$elements = $cart->getSamplesNumberInCart();
$idSample = $product->checkSampleVinculation($product->id);

if ($elements <= 7) {
    $canAddSample = true;
}

echo json_encode([
    'can_add_sample' => $canAddSample,
    'elements' =>  $elements,
    'id_sample' => $idSample
]);
exit;


