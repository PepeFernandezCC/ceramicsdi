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
$weight = Tools::getValue('weight');
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
$id_zone = State::getIdZone((int)$id_state);
$international=false;


// IDs de transportistas (Cambiar a buscar id por búsqueda de nombre)
$correos = 320;
$correos_internacional = 327;
$camion = 287;
$camion_internacional = 322;
$seur = 311;
$gc_international = 325;
$gc_spain = 319;
$transaher = 294;

// Excepciones por zona
$transaher_zones = ['65', '66', '67', '68'];
$gc_spain_states = ['357', '365', '375', '372', '384', '376'];

if ($id_country != 6) {
    $international = true;
}

        
if ($international) {

    // LOGICA TRANSPORTISTAS INTERNACIONAL

    $id_carrier = $correos_internacional;

    if($id_country == 13) { //Envíos a Paises Bajos
        $id_carrier = $seur;
    }

    if($weight > 8) {
        $id_carrier = $gc_international;
    }

    if($weight > 60) {
        $id_carrier = $camion_internacional;
    }

}else{

    // LOGICA TRANSPORTISTAS ESPAÑA

    $id_carrier = $correos;

    if($weight > 8) {

        $id_carrier = $camion;

        if (in_array($id_state, $gc_spain_states)) {
            $id_carrier = $gc_spain;
        }               

    }

} 

//EXCEPCIÓN TRANSAHER
        
if (in_array($id_zone, $transaher_zones) && $weight > 8) {
    $id_carrier = $transaher;
}

//falsear Carrito
$cart->id_carrier = ''.$id_carrier.'';
$cart->id_address_delivery = '1';
$cart->id_address_invoice = '1';
$cart->id_customer = '2';
$cart->delivery_option = '{"1":"'.$id_carrier.'"}';

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



