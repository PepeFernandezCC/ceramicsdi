<?php

// override/classes/checkout/CheckoutAddressesStep.php

class CheckoutAddressesStep extends CheckoutAddressesStepCore
{
    public function handleRequest(array $requestParams = [])
    {
        // Deja al core procesar primero (selección/edición de direcciones, etc.)
        parent::handleRequest($requestParams);

        // Solo intervenimos al confirmar direcciones y si no hay errores previos
        if (!isset($requestParams['confirm-addresses']) || $this->getCheckoutProcess()->hasErrors()) {
            return $this;
        }

        $session = $this->getCheckoutSession();

        // 1) Normaliza "use_same_address" e invoice si no viene
        $idDelivery = (int) $session->getIdAddressDelivery();
        $idInvoice  = (int) $session->getIdAddressInvoice();

        $useSame = array_key_exists('use_same_address', $requestParams)
            ? (bool) $requestParams['use_same_address']
            : (!isset($requestParams['id_address_invoice']) || !$idInvoice);

        if ($useSame) {
            $idInvoice = $idDelivery;
            $session->setIdAddressInvoice($idInvoice);
        }

        if (!$idDelivery || !$idInvoice) {
            $this->getCheckoutProcess()->setHasErrors(true);
            $this->context->controller->errors[] = $this->getTranslator()->trans(
                'Missing delivery or invoice address.',
                [],
                'Shop.Notifications.Error'
            );
            $this->setCurrent(true);
            return $this;
        }

        // 3) Lógica intracomunitaria del AJAX (en servidor)
        //    address::getVatApiData($address) debe existir como en tu AJAX
        $data = Address::getVatApiData($idInvoice);

        $validate     = isset($data['validate']) ? (bool)$data['validate'] : false;
        $vat_input    = isset($data['vat_number']) ? (string)$data['vat_number'] : '';
        $customer_id  = isset($data['customer']) ? (int)$data['customer'] : (int)$this->context->customer->id;
        $idCountry    = isset($data['country']) ? (int)$data['country'] : 0;

        $FRANCE_ID = 8;

        $country = new Country($idCountry);

        $vat_input = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($vat_input));


        // Si quieres forzar VIES siempre que haya VAT, dejamos la validación como abajo.

        if ($vat_input !== '') {

            if (!$validate) {
                // Tu AJAX: "Cliente no apto para intracomunitario"
                Customer::insertIntracomunitaryLog(false, 'DIRECCIONES: Cliente no apto', $vat_input, $customer_id, $idCountry);
                $errors_msg = 'Cliente no apto para intracomunitario';
            }

            if (strlen($vat_input) < 3) {
                Customer::insertIntracomunitaryLog(false, 'DIRECCIONES: VAT CON FORMATO INCORRECTO', $vat_input, $customer_id, $idCountry);
                $error_msg = 'Invalid VAT input';
            }

            // Prefijo y número
            $prefix = substr($vat_input, 0, 2);

            if ($prefix === $country->iso_code) {
                $vatNumber = $vat_input;
            } else {
                // Caso FR: si mandan SIREN de 9 dígitos, calcula clave
                if ($idCountry === $FRANCE_ID) {
                    if (strlen($vat_input) != 9 && strlen($vat_input) != 11) {
                        Customer::insertIntracomunitaryLog(false, 'DIRECCIONES: VAT NO VALIDO', $vat_input, $customer_id, $idCountry);
                        $error_msg = 'Numero Vat no válido';
                    }
                    if (strlen($vat_input) == 9) {
                        $siren = (int) $vat_input;
                        $clave = (12 + 3 * ($siren % 97)) % 97;
                        $vat_input = str_pad((string)$clave, 2, '0', STR_PAD_LEFT) . $vat_input;
                    }
                }
                $vatNumber = $country->iso_code . $vat_input;
            }

            // Llamada a VIES (igual que tu AJAX)
            $apiUrl   = 'https://viesapi.eu/api/get/vies/euvat/' . urlencode($vatNumber);
            $apiKeyId = 'qhzV8CuqUKqa';
            $apiKey   = 'NCUE2Ghk0GaI';
            $auth     = base64_encode($apiKeyId . ':' . $apiKey);

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . $auth,
                'Accept: text/xml',
                'User-Agent: VIESAPIClient/1.0 PHP/8.1',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $ok  = false;
            $msg = '';

            if ($httpCode === 200) {
                $xml = @simplexml_load_string($response);
                if ($xml) {
                    if (isset($xml->error)) {
                        $code = (string)$xml->error->code;
                        $desc = (string)$xml->error->description;
                        $ok   = false;
                        $msg  = "Error en VIES (código $code): $desc";
                    } elseif (isset($xml->vies->valid)) {
                        $ok  = ((string)$xml->vies->valid === 'true');
                        $msg = $ok ? 'VAT válido' : 'VAT NO válido';
                    } else {
                        $ok  = false;
                        $msg = 'Error en la API desconocido.';
                    }
                } else {
                    $ok  = false;
                    $msg = 'Respuesta XML mal formada.';
                }
            } else {
                $ok  = false;
                $msg = "Error en petición HTTP ($httpCode)";
            }

            if ($ok) {
                Address::updateIntracomunitaryAddress($idInvoice, $vatNumber);
                Customer::updateCustomerSiret($customer_id, $vatNumber);
                Customer::assignIntracomunitaryGroup($customer_id);
            }

            Customer::insertIntracomunitaryLog($ok, 'DIRECCIONES: ' . $msg, $vatNumber, $customer_id, $idCountry);
        }

        // Si hemos llegado aquí, no hay errores: asegúrate de avanzar a envío
        if (!$this->getCheckoutProcess()->hasErrors()) {
            $session = $this->getCheckoutSession();

            // por si acaso, garantiza invoice cuando use_same o no vino
            if (!(int)$session->getIdAddressInvoice()) {
                $session->setIdAddressInvoice((int)$session->getIdAddressDelivery());
            }

            // Marca siguiente paso y completa este
            $this->setNextStepAsCurrent();
            $this->setComplete(
                (bool)$session->getIdAddressInvoice() &&
                (bool)$session->getIdAddressDelivery()
            );
        }

        // Nada de recalcular totales/aquí.
        return $this;
    }
}


