<?php
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');

//$vat_input = 'FR11849695879';
//$customer_id = '7485';

$vat_input = Tools::getValue('vat_number');
$customer_id = Tools::getValue('customer');
$idCountry = Tools::getValue('country');

$country = new Country($idCountry);

$vat_input = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($vat_input));

if (!$vat_input || strlen($vat_input) < 3) {
    echo json_encode(['result' => false, 'userError' => 'Invalid VAT input']);
    exit;
}

// Obtener prefijo y número
$prefix = substr($vat_input, 0, 2);
$number = substr($vat_input, 2);

if($prefix == $country->iso_code) {
    $vatNumber = $vat_input;

}else{
    $vatNumber = $country->iso_code . $vat_input;

}

$apiUrl = "https://viesapi.eu/api/get/vies/euvat/" . urlencode($vatNumber);

// Tus credenciales de producción
$apiKeyId = "qhzV8CuqUKqa";
$apiKey = "NCUE2Ghk0GaI";

// Autenticación básica
$authHeader = base64_encode("$apiKeyId:$apiKey");

$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic $authHeader",
    "Accept: text/xml",  // <-- Asegura que la respuesta sea XML
    "User-Agent: VIESAPIClient/1.0 PHP/8.1"
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Ejecutar solicitud
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['result' => true, 'userError' => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

if ($httpCode === 200) {
    // Parsear XML
    $xml = simplexml_load_string($response);

    if ($xml && isset($xml->vies->valid)) {
        $isValid = (string)$xml->vies->valid;
        $err =  $isValid === 'true' ? "VAT válido" : "VAT NO válido";
        $result = true;
    } else {
        $err = "No se pudo interpretar la respuesta XML.";
        $result = false;
    }
} else {
    //$err =  "Error HTTP $httpCode<br>Respuesta: $response";
    $err = "Error en petición HTTP";
    $result = false;
}

if ($result) {

    customer::assignCustomerGroup($customer_id);

}

echo json_encode(['result' => $result, 'userError' => $err]);
exit;
