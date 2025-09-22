<?php
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');

//$vat_input = 'FR11849695879';
//$customer_id = '7485';

$vat_input = Tools::getValue('vat_number');
$customer_id = Tools::getValue('customer');
$idCountry = Tools::getValue('country');
$france = '8';
$spain = '6';

$country = new Country($idCountry);

$vat_input = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($vat_input));

if (!$vat_input || strlen($vat_input) < 3) {
    echo json_encode(['result' => false, 'userError' => 'Invalid VAT input']);
    exit;
}

// Obtener prefijo y número
$prefix = substr($vat_input, 0, 2);
$number = substr($vat_input, 2);

/* CONTROLAR VAT ESPAÑOL */
if ($idCountry == $spain) {
    echo json_encode(['result' => false, 'userError' => 'No aplica a España']);
    exit;
}

if($prefix == $country->iso_code) {
    $vatNumber = $vat_input;

}else{
    
    /* CONTROLAR VAT FRANCÉS */
    if ($idCountry == $france) {
        if (strlen($vat_input) != 9 && strlen($vat_input) != 11) {
            echo json_encode(['result' => false, 'userError' => 'Numero Vat no válido']);
            exit;
        }
        if (strlen($vat_input) == 9) {
            // Cálculo de la clave de control
            $siren = intval($vat_input);
            $clave = (12 + 3 * ($siren % 97)) % 97;

            // Formato con dos dígitos (relleno con 0 si es necesario)
            $clave_str = str_pad($clave, 2, '0', STR_PAD_LEFT);

            // Devolver el VAT completo
            $vat_input = $clave_str . $vat_input;
        }

    }
    
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

// Reintentos automáticos
$maxAttempts = 5;
$attempt = 0;
$response = null;
$httpCode = 0;

do {
    $attempt++;
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo json_encode(['result' => null, 'userError' => curl_error($ch), 'fullVat' => $vatNumber]);
        curl_close($ch);
        exit;
    }

    // Si es error de concurrencia (código 58 en la respuesta XML), reintentamos
    if ($httpCode === 200 && strpos($response, '<code>58</code>') !== false) {
        sleep($attempt * 2); // backoff exponencial
        continue;
    }

    break;
} while ($attempt < $maxAttempts);

curl_close($ch);

// Procesar respuesta
$result = false;
$err    = '';

if ($httpCode === 200) {
    $xml = simplexml_load_string($response);

    if ($xml) {
        if (isset($xml->error)) {
            $errorCode = (string)$xml->error->code;
            $errorMsg  = (string)$xml->error->description;

            $err = "Error en VIES (código $errorCode): $errorMsg";
            $result = false; // indeterminado
        }
        elseif (isset($xml->vies->valid)) {
            $isValid = (string)$xml->vies->valid;
            $result = $isValid === 'true';
            $err = $result ? "VAT válido" : "VAT NO válido";
        } else {
            $err = "Error en la API desconocido.";
            $result = false;
        }
    } else {
        $err = "Respuesta XML mal formada.";
        $result = false;
    }
} else {
    $err = "Error en petición HTTP ($httpCode)";
    $result = false;
}

if ($result) {

    customer::assignIntracomunitaryGroup($customer_id);

}

echo json_encode(['result' => $result, 'userError' => $err, 'fullVat' => $vatNumber]);
exit;
