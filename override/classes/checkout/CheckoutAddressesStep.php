<?php

class CheckoutAddressesStep extends CheckoutAddressesStepCore
{
    public function handleRequest(array $requestParams = [])
    {
        $result = parent::handleRequest($requestParams);

        // Sólo al confirmar direcciones
        if (isset($requestParams['confirm-addresses'])) {
            $process = $this->getCheckoutProcess();
            $session = $this->getCheckoutSession();
            $ctx     = Context::getContext();
            $cart    = $ctx->cart;

            $idD = (int)$session->getIdAddressDelivery();
            $idI = (int)($session->getIdAddressInvoice() ?: $idD);

            // 1) Log de errores del step
            PrestaShopLogger::addLog(sprintf(
                'ADDR STEP: hasErrors=%s | delivery=%d invoice=%d | cartD=%d cartI=%d',
                $process->hasErrors() ? '1' : '0',
                $idD, $idI, (int)$cart->id_address_delivery, (int)$cart->id_address_invoice
            ), 1);

            // 2) Valida direcciones con el validador del core y loguea por qué falla
            try {
                $validator = new AddressValidator();
                $invalidForCart = $validator->validateCartAddresses($cart); // IDs inválidos aplicados al carrito
                $invalidForCustomer = $validator->validateCustomerAddresses($ctx->customer, $ctx->language);

                if (!empty($invalidForCart)) {
                    PrestaShopLogger::addLog('ADDR INVALID (CART): '.implode(',', $invalidForCart), 2);
                }
                if (!empty($invalidForCustomer)) {
                    PrestaShopLogger::addLog('ADDR INVALID (CUSTOMER): '.implode(',', $invalidForCustomer), 2);
                }
            } catch (Exception $e) {
                PrestaShopLogger::addLog('ADDR VALIDATOR EXC: '.$e->getMessage(), 3);
            }

            // 3) Calcula opciones de envío reales para esa dirección y anota cuántas hay
            try {
                $finder = new DeliveryOptionsFinder($this->context, $this->getTranslator(), $this->objectPresenter, new \PrestaShop\PrestaShop\Adapter\Product\PriceFormatter());
                $options = $finder->getDeliveryOptions($cart);
                $countOptions = 0;
                foreach ($options as $k => $opt) {
                    // $opt es un array de carriers posibles en esa combinación
                    $countOptions += count($opt);
                }
                PrestaShopLogger::addLog('CARRIERS: total_options='.$countOptions, 1);

                if ($countOptions === 0) {
                    PrestaShopLogger::addLog('NO HAY TRANSPORTISTAS DISPONIBLES PARA ESA DIRECCIÓN', 2);
                }
            } catch (Exception $e) {
                PrestaShopLogger::addLog('DELIVERY FINDER EXC: '.$e->getMessage(), 3);
            }
        }

        return $result;
    }
}

