<?php
// Incluye el entorno de Prestashop
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');

// Obtener valores

$id_country = Tools::getValue('id_country');
$id_state = Tools::getValue('id_state');
$postal = Tools::getValue('postal');
$id_cart = Tools::getValue('id_cart');
$showTaxes = Tools::getValue('taxes') == "1"? true : false;
$delivery_by_truck = Tools::getValue('weight') >= 13 ? true : false ;

if (!$id_country || !$id_state || !$postal || !$id_cart) {
    echo json_encode([]);
    exit;
}

//actualizar la dirección falsa
$sql = 'UPDATE `' . _DB_PREFIX_ . 'address`
SET `id_country` = ' . $id_country . ',
`id_state` = '. $id_state .',
`postcode` = '. $postal .'
WHERE  `id_address` = 1';

Db::getInstance()->execute($sql);


//Obtener carrito
$cart = new Cart((int) $id_cart);


//Obtener carrier local

/*LOCAL*/

$correos = 167;
$correos_internacional = 158;
$camion = 168;
$camion_internacional = 169;

/*SERVIDOR*/
/*
$correos = 173;
$correos_internacional = 172;
$camion = 184;
$camion_internacional = 182;
*/

$delivery_by_truck = Tools::getValue('weight') >= 13 ? true : false ;
$id_carrier = $delivery_by_truck ? $camion_internacional : $correos_internacional;

if ($id_country == 6) {//Envío en españa
    $id_carrier = $delivery_by_truck ? $camion : $correos;
}

//falsear Carrito
$cart->id_carrier = ''.$id_carrier.'';
$cart->id_address_delivery = '1';
$cart->id_address_invoice = '1';
$cart->id_customer = '2';
$cart->delivery_option = '{"1":"'.$id_carrier.'"}';


//calcular id_zone

$id_zone = State::getIdZone((int)$id_state);

/* CONSTRUIR PARAMETROS */
static $cache = [];
$total = 0;
$return = false;
$use_tax = false;
$default_country = new Country((int) $id_country);
$keepOrderPrices = false;
$product_list = null;
Hook::exec('actionCartGetPackageShippingCost', [
    'object' => &$cart,
    'id_carrier' => &$id_carrier,
    'use_tax' => &$use_tax,
    'default_country' => &$default_country,
    'product_list' => &$product_list,
    'id_zone' => &$id_zone,
    'keepOrderPrices' => &$keepOrderPrices,
    'total' => &$total,
    'return' => &$return,
    'custom' => true
]);

$check_shipping = false; 
if ($return) {
    $result = ($total !== false ? (float) Tools::ps_round((float) $total, 2) : false);
   
} else {
    $shipping_cost = $cart->getParentPackageShippingCost(
        $id_carrier,
        $use_tax,
        $default_country,
        $product_list,
        $id_zone,
        $keepOrderPrices
    );
    if ($shipping_cost !== false) {
        $result = $shipping_cost + (float) Tools::ps_round((float) $total, 2);
    }

    $check_shipping = $shipping_cost;

}


// Si no hay resultado, maneja el caso
if ($result === null ) {
    $result = ['error' => 'No se pudo calcular el costo de envío'];
}

//obtener impuesto

if($showTaxes) {
    $rate = Tax::getStandardTaxByCountryId((int)$id_country);
    $result = $result * (1+($rate/100));
}
$result = (float) Tools::ps_round((float) $result, 2);

// Devuelve el resultado como JSON
echo json_encode([
    'show_taxes' => $showTaxes,
    'shipping_cost' => $result
]);
exit;



