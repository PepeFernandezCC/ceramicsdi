<?php

// override/classes/checkout/CheckoutAddressesStep.php

class CheckoutAddressesStep extends CheckoutAddressesStepCore
{
       public function handleRequest(array $requestParams = [])
    {
        // 1) Deja que el core haga TODO su trabajo primero
        parent::handleRequest($requestParams);

        // --- Detectar si estamos en flujo de edición / creación de dirección ---
        $editAddress = Tools::getValue('editAddress');
        $newAddress  = Tools::getValue('newAddress');
        $isEditOrNew = !empty($editAddress) || !empty($newAddress);

        if ($isEditOrNew) {
            $this->setCurrent(true);
            $this->setComplete(false);
        }


        // 2) Solo intervenimos cuando el usuario ha pulsado "Continuar"
        //    y NO hay errores previos (del core o de otros módulos)
        if (empty($requestParams['confirm-addresses']) || $this->getCheckoutProcess()->hasErrors()) {
            return $this;
        }

        $session   = $this->getCheckoutSession();
        $idDelivery = (int) $session->getIdAddressDelivery();
        $idInvoice  = (int) $session->getIdAddressInvoice();

        // 3) Asegurar que existe dirección de facturación.
        //    Si el core no ha puesto ninguna, copiamos la de envío.
        if (!$idInvoice && $idDelivery) {
            $session->setIdAddressInvoice($idDelivery);
            $idInvoice = $idDelivery;
        }

        // 4) Si falta alguna de las dos, bloqueamos y nos quedamos en direcciones
        if (!$idDelivery || !$idInvoice) {
            $this->getCheckoutProcess()->setHasErrors(true);
            $this->context->controller->errors[] = $this->getTranslator()->trans(
                'Missing delivery or invoice address.',
                [],
                'Shop.Notifications.Error'
            );
            $this->setCurrent(true);
            $this->setComplete(false);

            return $this;
        }

        // 5) Lógica intracomunitaria
        //    getVatApiData($idDelivery, $idInvoice) -> ya tienes esa función
        $data = Address::getVatApiData($idDelivery, $idInvoice);

        $validate    = !empty($data['validate']);
        $vat_input   = isset($data['vat_number']) ? (string)$data['vat_number'] : '';
        $customer_id = isset($data['customer']) ? (int)$data['customer'] : (int)$this->context->customer->id;
        $idCountry   = isset($data['country']) ? (int)$data['country'] : 0;

        $vat_input = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($vat_input));

        // Si no hay VAT, no hacemos nada y dejamos que el core avance normal
        if ($vat_input === '') {
            return $this;
        }

        $country   = new Country($idCountry);
        $FRANCE_ID = 8;

        // Si ya desde Address::getVatApiData nos dicen que NO es apto, lo tratamos
        // como "no intracomunitario" pero NO bloqueamos el checkout

        if (!$validate || strlen($vat_input) < 3) {
            Customer::insertIntracomunitaryLog(false, 'DIRECCIONES: Cliente no apto o VAT inválido', $vat_input, $customer_id, $idCountry);

            // SOLO avanzamos a envío si NO estamos en edit/new
            if (!$this->getCheckoutProcess()->hasErrors() && !$isEditOrNew) {
                    $session = $this->getCheckoutSession();

                if (!(int)$session->getIdAddressInvoice()) {
                    $session->setIdAddressInvoice((int)$session->getIdAddressDelivery());
                }

                $this->setNextStepAsCurrent();
                $this->setComplete(
                    (bool)$session->getIdAddressInvoice() &&
                    (bool)$session->getIdAddressDelivery()
                );
            }

            return $this;
        }

        // 6) Normalizar VAT (caso especial Francia incluido)
        $prefix = substr($vat_input, 0, 2);

        if ($prefix === $country->iso_code) {
            $vatNumber = $vat_input;
        } else {
            if ($idCountry === $FRANCE_ID && strlen($vat_input) == 9) {
                $siren = (int) $vat_input;
                $clave = (12 + 3 * ($siren % 97)) % 97;
                $vat_input = str_pad((string)$clave, 2, '0', STR_PAD_LEFT) . $vat_input;
            }

            $vatNumber = $country->iso_code . $vat_input;
        }

        // 7) Llamada a VIES (via viesapi.eu)
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
                    $code = (string) $xml->error->code;
                    $desc = (string) $xml->error->description;
                    $ok   = false;
                    $msg  = "Error en VIES (código $code): $desc";

                    // Caso especial: MS_MAX_CONCURRENT_REQ (código 58)
                    if ($code === '58') {
                        $this->getCheckoutProcess()->setHasErrors(true);
                        $this->context->controller->errors[] = $this->getTranslator()->trans(
                            'We could not validate your VAT number due to a temporary issue with the VIES service. Please try again later.',
                            [],
                            'Shop.Notifications.Error'
                        );
                        $this->setCurrent(true);
                        $this->setComplete(false);

                        Customer::insertIntracomunitaryLog(false, 'DIRECCIONES: ' . $msg, $vatNumber, $customer_id, $idCountry);
                        return $this;
                    }

                    // Otros errores de VIES → solo log, NO bloqueamos
                    Customer::insertIntracomunitaryLog(false, 'DIRECCIONES: ' . $msg, $vatNumber, $customer_id, $idCountry);
                    return $this;
                } elseif (isset($xml->vies->valid)) {
                    $ok  = ((string) $xml->vies->valid === 'true');
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

        // 8) Aplicamos cambios solo si VIES confirma OK
        if ($ok) {
            Address::updateIntracomunitaryAddress($idInvoice, $vatNumber);
            Customer::updateCustomerSiret($customer_id, $vatNumber);
            Customer::assignIntracomunitaryGroup($customer_id);
            $this->context->controller->success[] = $this->getTranslator()->trans(
                'Your VAT number has been validated. Prices have been updated for intra-community supply. You can continue.',
                [],
                'Shop.Notifications.Success'
            );
        }

        Customer::insertIntracomunitaryLog($ok, 'DIRECCIONES: ' . $msg, $vatNumber, $customer_id, $idCountry);

        return $this;
    }

}


