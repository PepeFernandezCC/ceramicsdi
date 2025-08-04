<?php
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');

//$vat_input = 'FR11849695879';
//$customer_id = '7485';

$vat_input = Tools::getValue('vat_number');
$customer_id = Tools::getValue('customer');

$vat_input = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($vat_input));

if (!$vat_input || strlen($vat_input) < 3) {
    echo json_encode(['result' => false, 'userError' => 'Invalid VAT input']);
    exit;
}

// Obtener prefijo y número
$prefix = substr($vat_input, 0, 2);
$number = substr($vat_input, 2);

// Lista de regex para validar formato básico de VAT por país (simplificado)
$vat_formats = [
    'AT' => '/^U\d{8}$/',                   // Austria
    'BE' => '/^\d{10}$/',                   // Bélgica
    'BG' => '/^\d{9,10}$/',                 // Bulgaria
    'CY' => '/^\d{8}[A-Z]$/',               // Chipre
    'CZ' => '/^\d{8,10}$/',                 // Chequia
    'DE' => '/^\d{9}$/',                    // Alemania
    'DK' => '/^\d{8}$/',                    // Dinamarca
    'EE' => '/^\d{9}$/',                    // Estonia
    'EL' => '/^\d{9}$/',                    // Grecia
    'ES' => '/^[A-Z0-9]\d{7}[A-Z0-9]$/',    // España
    'FI' => '/^\d{8}$/',                    // Finlandia
    'FR' => '/^[A-Z0-9]{2}\d{9}$/',         // Francia
    'HR' => '/^\d{11}$/',                   // Croacia
    'HU' => '/^\d{8}$/',                    // Hungría
    'IE' => '/^[0-9A-Z]{7,8}$/',            // Irlanda
    'IT' => '/^\d{11}$/',                   // Italia
    'LT' => '/^\d{9}|\d{12}$/',             // Lituania
    'LU' => '/^\d{8}$/',                    // Luxemburgo
    'LV' => '/^\d{11}$/',                   // Letonia
    'MT' => '/^\d{8}$/',                    // Malta
    'NL' => '/^\d{9}B\d{2}$/',              // Países Bajos
    'PL' => '/^\d{10}$/',                   // Polonia
    'PT' => '/^\d{9}$/',                    // Portugal
    'RO' => '/^\d{2,10}$/',                 // Rumania
    'SE' => '/^\d{12}$/',                   // Suecia
    'SI' => '/^\d{8}$/',                    // Eslovenia
    'SK' => '/^\d{10}$/',                   // Eslovaquia
];

// Verificar si el prefijo está en la lista y si el número cumple el patrón
if (!array_key_exists($prefix, $vat_formats)) {
    echo json_encode(['result' => false, 'userError' => 'Unknown country prefix']);
    exit;
}

// Verificar si el código corresponde con el formato del país
if (!preg_match($vat_formats[$prefix], $number)) {
    echo json_encode(['result' => false, 'userError' => 'Invalid VAT format for country']);
    exit;
}

// Si es español, no lo validamos directamente
if ($prefix === 'ES') {
    echo json_encode(['result' => false, 'userError' => 'Spanish VAT number']);
    exit;
}

customer::assignCustomerGroup($customer_id);

echo json_encode(['result' => true, 'userError' => 'Todo OK']);
exit;
