<?php

class CheckoutAddressesStep extends CheckoutAddressesStepCore
{
    public function handleRequest(array $requestParams = [])
    {
        // Ejecuta la lógica original
        $result = parent::handleRequest($requestParams);

        if (isset($requestParams['confirm-addresses']) && !$this->getCheckoutProcess()->hasErrors()) {

            // Aquí tu lógica personalizada
            $spain = '6';
            $checkAddress = new Address($this->getCheckoutSession()->getIdAddressInvoice());
            if ($checkAddress->country == $spain || $checkAddress->company == '') {

                customer::removeIntracomunitaryGroup($checkAddress->id_customer);

            }else{
                //comprobar si es intracomunitario
                $intraComunitary = self::checkIntraComunitary($checkAddress);
                
                if ($intraComunitary) {
                    customer::assignIntracomunitaryGroup($checkAddress->id_customer);
                }else{
                    customer::removeIntracomunitaryGroup($checkAddress->id_customer);
                }
            } 
            
        }

        return $result;
    }

    // Puedes añadir métodos privados para centralizar tu lógica si quieres
    
    private function checkIntraComunitary($address)
    {

        
        $france = '8';

        $country = new Country($address->id_country);

        $vat_input = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($address->dni));

        if (!$vat_input || strlen($vat_input) < 3) {
            return false;
        }

        // Obtener prefijo y número
        $prefix = substr($vat_input, 0, 2);
        
        if($prefix == $country->iso_code) {
            $vatNumber = $vat_input;

        }else{
            
            /* CONTROLAR VAT FRANCÉS */
            if ($address->id_country == $france) {
                if (strlen($vat_input) != 9 && strlen($vat_input) != 11) {
                    return false;
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

        // Ejecutar solicitud
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            echo json_encode(['result' => true, 'userError' => curl_error($ch), 'fullVat' => $vatNumber]);
            curl_close($ch);
            exit;
        }

        curl_close($ch);

        if ($httpCode === 200) {
            // Parsear XML
            $xml = simplexml_load_string($response);

            if ($xml && isset($xml->vies->valid)) {
                $isValid = (string)$xml->vies->valid;
                return ($isValid === 'true') ? true : false;

            } else {
                return false;
            }
        } else {
            return false;
        }

        return false;
    }
    
    /*
    private function miFuncionDespuesDeConfirmar($delivery, $invoice)
    {
        // Tu código después de confirmar direcciones
    }
    */
}
