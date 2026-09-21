<?php

// override/classes/checkout/CheckoutAddressesStep.php

class CheckoutAddressesStep extends CheckoutAddressesStepCore
{
       public function handleRequest(array $requestParams = [])
    {
         $this->debugAddressStepRequest($requestParams, 'BEFORE_PARENT');

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

        // Comprobamos que la dirección de facturación esté realmente marcada como tal
        $invoiceAddressIsValid = false;
        if ($idInvoice) {
            $invoiceAddress = new Address($idInvoice);
            $invoiceAddressIsValid = (int) $invoiceAddress->is_invoice !== 0;
        }

        // 4) Si falta alguna de las dos, o la de facturación no es válida, bloqueamos y nos quedamos en direcciones
        if (!$idDelivery || !$idInvoice || !$invoiceAddressIsValid) {
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

        // 4bis) Bloquear el avance a transportista si la dirección de facturación
        // es extranjera, el importe de productos alcanza el umbral, y no tiene DNI/CIF.
        if ($this->invoiceAddressNeedsForeignIdMissing($invoiceAddress)) {
            $this->getCheckoutProcess()->setHasErrors(true);
            $this->context->controller->errors[] = $this->getIncompleteAddressDniError();
            $this->setCurrent(true);
            $this->setComplete(false);

            return $this;
        }

        // 5) Lógica intracomunitaria
        $data = Address::getVatApiData($idDelivery, $idInvoice);

        $validate    = !empty($data['validate']);
        $vat_input   = isset($data['vat_number']) ? (string)$data['vat_number'] : '';
        $customer_id = isset($data['customer']) ? (int)$data['customer'] : (int)$this->context->customer->id;
        $idCountry   = isset($data['country']) ? (int)$data['country'] : 0;

        $vat_input = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($vat_input));

        $country   = new Country($idCountry);
        $FRANCE_ID = 8;

        // Si no hay VAT/DNI, o Address::getVatApiData nos dice que NO es apto, o el VAT es demasiado corto,
        // tratamos al cliente como "no intracomunitario": lo sacamos del grupo y le borramos el siret
        // guardado de una validación anterior, para que no se quede "pegado" como intracomunitario
        // con direcciones que ya no tienen VAT/DNI válido. NO bloqueamos el checkout.

        if ($vat_input === '' || !$validate || strlen($vat_input) < 3) {
            Customer::removeIntracomunitaryGroup($customer_id);
            Customer::updateCustomerSiret($customer_id, '');
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
        $inputPrefix = $prefix;
        
        // Lista de prefijos válidos VIES (ISO VAT). Ojo: Grecia usa EL (no GR).
        $validVatPrefixes = [
            'AT','BE','BG','CY','CZ','DE','DK','EE','EL','FI','FR','HR','HU',
            'IE','IT','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK'
        ];

        // ¿Parece que el cliente ha metido ya un VAT completo? (2 letras + algo detrás)
        $hasVatLikePrefix = strlen($vat_input) >= 4 && in_array($inputPrefix, $validVatPrefixes, true);

        if ($prefix === $country->iso_code || $hasVatLikePrefix) {
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
                    Customer::removeIntracomunitaryGroup($customer_id);
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

    /**
     * @param Address $invoiceAddress
     */
    private function invoiceAddressNeedsForeignIdMissing(Address $invoiceAddress)
    {
        $isInvoice = (int) $invoiceAddress->is_invoice !== 0;
        $isSpanish = (int) $invoiceAddress->id_country === 6;

        if (!$isInvoice || $isSpanish) {
            return false;
        }

        $cartProductsTotal = (float) $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);

        if ($cartProductsTotal < Address::FOREIGN_ID_MIN_AMOUNT) {
            return false;
        }

        $hasDni = trim((string) $invoiceAddress->dni) !== '';

        return !$hasDni;
    }

    private function getIncompleteAddressDniError()
    {
        $messages = [
            1 => 'Dirección incompleta: necesita una dirección con DNI para este importe.',
            2 => "Adresse incomplète : une adresse avec un numéro d'identification (DNI/CIF) est nécessaire pour ce montant.",
            3 => 'Incomplete address: an address with an ID/Tax number is required for this order amount.',
            4 => 'Unvollständige Adresse: Für diesen Betrag wird eine Adresse mit Ausweis-/Steuernummer benötigt.',
            5 => 'Endereço incompleto: é necessário um endereço com número de identificação fiscal para este valor.',
            6 => 'Onvolledig adres: voor dit bedrag is een adres met een identificatienummer (ID/BTW) vereist.',
        ];

        $idLang = (int) $this->context->language->id;

        return $messages[$idLang] ?? $messages[3];
    }

    private function debugAddressStepRequest(array $requestParams, $stage)
    {
        $logFile = _PS_ROOT_DIR_ . '/var/logs/checkout_address_step_debug.log';

        $data = [
            'date' => date('Y-m-d H:i:s'),
            'stage' => $stage,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? null,
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? null,

            'requestParams_is_invoice' => isset($requestParams['is_invoice'])
                ? $requestParams['is_invoice']
                : 'NO_REQUEST_PARAM',

            'post_is_invoice' => isset($_POST['is_invoice'])
                ? $_POST['is_invoice']
                : 'NO_POST',

            'tools_is_invoice' => Tools::getValue('is_invoice', 'NO_TOOLS'),

            'requestParams' => $requestParams,
            'POST' => $_POST,
            'GET' => $_GET,

            // Ojo: php://input normalmente solo se puede leer una vez.
            // En x-www-form-urlencoded suele seguir disponible, pero no dependas de él.
            'raw_input' => file_get_contents('php://input'),
        ];

        file_put_contents(
            $logFile,
            print_r($data, true) . "\n-----------------------------\n\n",
            FILE_APPEND
        );
    }

}


