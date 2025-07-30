<?php
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');

$valid_vat_number = false;
$vat_result = null;
$attempts = 0;
$max_attempts = 3;
$timeout = 10;

// Obtener y limpiar el VAT
$vat_input = Tools::getValue('vat_number');
$vat_input = preg_replace('/[^A-Za-z0-9]/', '', $vat_input);

if (!$vat_input || strlen($vat_input) < 3) {
    echo json_encode(['result' => false, 'error' => 'Invalid VAT input']);
    exit;
}

$prefix = strtoupper(substr($vat_input, 0, 2));
$number = substr($vat_input, 2);

$url = "https://ec.europa.eu/taxation_customs/vies/rest-api/ms/{$prefix}/vat/{$number}";

do {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $vat_result = json_decode($response);

    $attempts++;

    $should_retry = (
        !$vat_result ||
        !isset($vat_result->userError) ||
        !in_array($vat_result->userError, ['VALID', 'INVALID', 'INVALID_INPUT']) // los que consideramos "respuesta definitiva"
    );

    if ($should_retry) {
        sleep(1); // pequeña pausa entre intentos
    }

} while ($should_retry && $attempts < $max_attempts);

if (
    isset($vat_result->isValid) &&
    $vat_result->userError === 'VALID' &&
    $vat_result->isValid === true
) {
    $valid_vat_number = true;
}

echo json_encode([
    'result' => $valid_vat_number,
    'userError' => $vat_result->userError ?? 'NO_RESPONSE',
    'attempts' => $attempts
]);
exit;
