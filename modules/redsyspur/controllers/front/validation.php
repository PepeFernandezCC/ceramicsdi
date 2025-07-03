<?php
/**
 * NOTA SOBRE LA LICENCIA DE USO DEL SOFTWARE
 *
 * El uso de este software está sujeto a las Condiciones de uso de software que
 * se incluyen en el paquete en el documento "Aviso Legal.pdf". También puede
 * obtener una copia en la siguiente url:
 * http://www.redsys.es/wps/portal/redsys/publica/areadeserviciosweb/descargaDeDocumentacionYEjecutables
 *
 * Redsys es titular de todos los derechos de propiedad intelectual e industrial
 * del software.
 *
 * Quedan expresamente prohibidas la reproducción, la distribución y la
 * comunicación pública, incluida su modalidad de puesta a disposición con fines
 * distintos a los descritos en las Condiciones de uso.
 *
 * Redsys se reserva la posibilidad de ejercer las acciones legales que le
 * correspondan para hacer valer sus derechos frente a cualquier infracción de
 * los derechos de propiedad intelectual y/o industrial.
 *
 * Redsys Servicios de Procesamiento, S.L., CIF B85955367
 */

if(!class_exists("Redsys_Refund")) {
	require_once('redsys_refund.php');
}

class RedsyspurValidationModuleFrontController extends ModuleFrontController  {
    public function postProcess() {
        try{
            /** Log de Errores **/
            $logLevel  = Configuration::get('REDSYS_LOG');
            $logString = Configuration::get( 'REDSYS_LOG_STRING' );
            
            /** Control de navegación en caso de que el cliente sea redirigido al validation. */
            if (!empty($_GET) && isset($_GET["Ds_MerchantParameters"])) {
                
                if (isset($_GET["Ds_MerchantParameters"]))
                    $datos = $_GET['Ds_MerchantParameters'];
                else
                    die ("La URL del retorno de navegación no contiene parámetros válidos, por lo que no se puede redireccionar de nuevo a la tienda. Revisa tu historial de pedidos accediendo a la tienda de nuevo y en caso de duda contacta con el comercio.");
                
                /** Se decodifican los datos enviados y se carga el array de datos **/
                $miObj = new RedsysAPI;
                $miObj->decodeMerchantParameters($datos);
                
                /** Declaramos Log */
                $pedido = $miObj->getParameter('Ds_Order');
                $idLog = generateIdLog($logLevel, $logString, $pedido);
                
                escribirLog("DEBUG", $idLog, "Procesando pretición vía GET para el pedido " . $pedido);

                $Linkobj = new Link(); //if no link object declared before

                $merchantData = b64url_decode($miObj->getParameter('Ds_MerchantData'));
                $merchantData = json_decode( $merchantData );

                $cart = new Cart($merchantData->idCart);
                $orderId = Order::getOrderByCartId($merchantData->idCart);

                if (($cart->id_customer == 0))
                    $customer = new Guest((int)$cart->id_guest);
                else
                    $customer = new Customer((int)$cart->id_customer);

                $urlRedirect = $Linkobj->getPageLink('order-confirmation') . '?id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key;
                escribirLog("DEBUG", $idLog, "Redireccionando cliente a: " . $urlRedirect);
                Tools::redirect($urlRedirect);

                exit();
            }
            
            /** Recoger datos de respuesta **/
            $version      = Tools::getValue('Ds_SignatureVersion');
            $datos        = Tools::getValue('Ds_MerchantParameters');
            $firma_remota = Tools::getValue('Ds_Signature');
            
            // Se crea Objeto
            $miObj = new RedsysAPI;
            
            /** Se decodifican los datos enviados y se carga el array de datos **/
            $miObj->decodeMerchantParameters($datos);

            /** Declaramos Log */
            $pedido = $miObj->getParameter('Ds_Order');
            $idLog = generateIdLog($logLevel, $logString, $pedido);

            escribirLog("INFO ", $idLog, "***** VALIDACIÓN DE LA NOTIFICACIÓN  ──  PEDIDO " . $pedido . " *****");
            
            escribirLog("DEBUG", $idLog, "Parámetros de la notificación: " . $datos);
            escribirLog("DEBUG", $idLog, "Firma recibida del SIS       : " . $firma_remota);

            /** Clave y método de pago **/
            if ($miObj->getParameter('Ds_ProcessedPayMethod') == 68) {
                $kc = Configuration::get('REDSYS_CLAVE256_BIZUM'); //68 -> Bizum
                $codigoOrig = Configuration::get('REDSYS_FUC_BIZUM');
                $metodo = "Redsys - Bizum";
                $order_method = "bizum";
            } else {
                $kc = Configuration::get('REDSYS_CLAVE256_TARJETA');
                $codigoOrig = Configuration::get('REDSYS_FUC_TARJETA');
                $metodo = "Redsys - Tarjeta";
                $order_method = "redireccion";
            }

            /** Se calcula la firma **/
            $firma_local = $miObj->createMerchantSignatureNotif($kc,$datos);
            escribirLog("DEBUG", $idLog, "Firma calculada notificación : " . $firma_local);

            $merchantData = b64url_decode($miObj->getParameter('Ds_MerchantData'));
            $merchantData = json_decode( $merchantData );
            
            /** Extraer datos de la notificación **/
            $total     = $miObj->getParameter('Ds_Amount');  
            //$pedido extraido en el Log, arriba.
            $pedidoSecuencial = $merchantData->idCart;
            $codigo    = $miObj->getParameter('Ds_MerchantCode');
            $terminal  = $miObj->getParameter('Ds_Terminal');
            $moneda    = $miObj->getParameter('Ds_Currency');
            $respuesta = $miObj->getParameter('Ds_Response');
            $id_trans  = $miObj->getParameter('Ds_AuthorisationCode');
            $tipoTransaccion = $miObj->getParameter('Ds_TransactionType');

            $metodoOrder = "N/A";

            if ($respuesta < 101)
                $metodoOrder = "Autorizada " . $id_trans;    
            else if ($respuesta >= 101)
                $metodoOrder = "Denegada " . $respuesta;

            escribirLog("DEBUG", $idLog, "ID del Carrito: " . $pedidoSecuencial);
            escribirLog("DEBUG", $idLog, "Codigo Comercio FUC: " . $codigo);
            escribirLog("DEBUG", $idLog, "Terminal: " . $terminal);
            escribirLog("DEBUG", $idLog, "Moneda: " . $moneda);
            escribirLog("DEBUG", $idLog, "Codigo de respuesta del SIS: " . $respuesta);
            escribirLog("DEBUG", $idLog, "Método de Pago: " . $metodo);
            escribirLog("DEBUG", $idLog, "Información adicional del módulo: " . $merchantData->moduleComent);

            /** Análisis de respuesta del SIS. */
            $erroresSIS = array();
            $errorBackofficeSIS = "";

            include 'erroresSIS.php';

            if (array_key_exists($respuesta, $erroresSIS)) {
                
                $errorBackofficeSIS  = $respuesta;
                $errorBackofficeSIS .= ' - '.$erroresSIS[$respuesta].'.';
            
            } else {

                $errorBackofficeSIS = "La operación ha finalizado con errores. Consulte el módulo de administración del TPV Virtual.";
            }
            
            escribirLog("DEBUG", $idLog, "Código de Autorización: " . $id_trans);
            $id_trans = str_replace("+", "", $id_trans);
            
            /** VALIDACIONES DE LIBRERÍA **/
            if (checkFirma($firma_local, $firma_remota)
                && checkImporte($total)
                && checkPedidoAlfaNum($pedido, Configuration::get('REDSYS_PEDIDO_EXTENDIDO') == 1)
                && checkFuc($codigo)
                && checkMoneda($moneda)
                && checkRespuesta($respuesta)) 
            {

                escribirLog("DEBUG", $idLog, "Validaciones del pedido, firmas correctas.");
                    
                /** Creamos los objetos para confirmar el pedido **/
                $cart = new Cart($pedidoSecuencial);
                $redsys = new Redsyspur();

                (empty($cart)) ? ($cartInfo = "El objeto del carrito está vacío") : ($cartInfo = serialize($cart));
                if(Configuration::get('REDSYS_LOG_CART'))
                    escribirLog("INFO ", $idLog, "POST ─ CARRITO SERIALIZADO: " . $cartInfo);
                
                $carrito_valido = true;
                $cliente = true;
                $mensajeError = "Errores validando el carrito en POST: ";
                /** Validamos Objeto carrito **/
                if ($cart->id_customer == 0) {
                    escribirLog("ERROR", $idLog, "Excepción validando el carrito. Cliente vacío. Puede no estar logueado, cargamos el guest.");

                    if ($cart->id_guest == 0) {
                        escribirLog("ERROR", $idLog, "Error validando el carrito. Cliente vacío y Guest vacío.");
                        $mensajeError += "Cliente vacío | ";
                        $carrito_valido = false;
                    }
                    else {
                        $cliente = false;
                        escribirLog("ERROR", $idLog, "Excepción validando el carrito CONTROLADA. Cliente vacío pero GUEST con datos.");
                        $id_customer = $cart->id_guest;
                    }

                } else {

                    $id_customer = $cart->id_customer;
                }

                /** Validamos Objeto cliente **/
                $customer = $cliente ? new Customer((int)$cart->id_customer) : new Guest((int)$cart->id_guest);
                $address = new Address((int)$cart->id_address_invoice);

                /** Generamos los contextos necesarios */
                Context::getContext()->customer = $customer;
                Context::getContext()->country = new Country((int)$address->id_country);
                Context::getContext()->language = new Language((int)$cart->id_lang);
                Context::getContext()->currency = new Currency((int)$cart->id_currency);                         
                
                if ($cart->id_address_delivery == 0) {
                    escribirLog("ERROR", $idLog, "Error validando el carrito. Dirección de envío vacía.");
                    $mensajeError += "Dirección de envío vacía | ";
                    $carrito_valido = false;
                }
                if ($cart->id_address_invoice == 0){
                    escribirLog("ERROR", $idLog, "Error validando el carrito. Dirección de facturación vacía.");
                    $mensajeError += "Dirección de facturación vacía | ";
                    $carrito_valido = false;
                }
                if (!$redsys->active) {
                    escribirLog("ERROR", $idLog, "Error. Módulo desactivado.");
                    $mensajeError += "Módulo desactivado | ";
                    $carrito_valido = false;
                }

                $totalCarrito = $cart->getOrderTotal(true, Cart::BOTH);
                $estadoFinal = Configuration::get("REDSYS_ESTADO_PEDIDO");

                switch((int)Configuration::get('REDSYS_COMPROBACION_TOTAL_NOTIF')) {
                    case 0:
                        if ($total/100 != $totalCarrito) {
                            escribirLog("INFO ", $idLog, "No coincide el total con el del carrito, REDSYS_COMPROBACION_TOTAL_NOTIF con valor 0 - REDSYS: $total | CARRITO: $totalCarrito.");      
                        }
                        break;
                    case 1:
                        if ($total/100 != $totalCarrito) {
                            escribirLog("INFO ", $idLog, "No coincide el total con el del carrito, REDSYS_COMPROBACION_TOTAL_NOTIF con valor 1 - REDSYS: $total | CARRITO: $totalCarrito.");
                            $estadoFinal = _PS_OS_ERROR_;
                        }
                        break;
                    case 2:
                        if ($total/100 != $totalCarrito) {
                            escribirLog("ERROR", $idLog, "Error. No coincide el total con el del carrito, no se creará pedido. - REDSYS: $total | CARRITO: $totalCarrito.");
                            $mensajeError += "No coincide el total con el del carrito | ";
                            $carrito_valido = false;
                        }
                        break;
                    default:
                        escribirLog("ERROR", $idLog, "Flujo Default en la sentencia de evaluación de REDSYS_COMPROBACION_TOTAL_NOTIF, se coloca estadoFinal a ERROR y se invalida el carrito por seguridad.");
                        $estadoFinal = _PS_OS_ERROR_;
                        $carrito_valido = false;
                        break;

                }
                
                if (!$carrito_valido){
                    escribirLog("INFO ", $idLog, "Ha ocurrido un error al procesar el carrito y el pedido " . $pedidoSecuencial . " (" . $pedido . ") no se ha validado correctamente. Acceda al Portal de Administración del TPV Virtual para comprobar el estado del pago.");
                    
                    if(Configuration::get('REDSYS_LOG_CART'))
                        escribirLog("DEBUG", $idLog, "Carrito serializado: " . serialize($cart));

                    if ($respuesta < 101) {
                        $gateway_params = $redsys->getGatewayParameters($order_method);
                        $gateway_params['moneda'] = $moneda;

                        if(Redsys_Order::cancellation($gateway_params, $pedidoSecuencial, $total)) {

                            escribirLog("ERROR", $idLog, "ERROR VALIDANDO EL CARRITO, PERO SE HA RECIBIDO RESPUESTA OK POR PARTE DE REDSYS ── La orden se ha anulado y se ha devuelto el importe al cliente.");
                            $metodoOrder = "Anulada 9929";

                            if(Configuration::get('REDSYS_MANTENER_CARRITO')) {
                                echo "Error validando el carrito con ANULACION, no se crea orden ── " . $errorBackofficeSIS;
                                exit();    
                            }

                            $redsys->validateOrder($pedidoSecuencial, _PS_OS_CANCELED_, 0, $metodo, "[REDSYS] " . $errorBackofficeSIS);
                            $redsys->addPaymentInfo($pedidoSecuencial, $pedido, $metodoOrder, $idLog);

                            echo "Error validando el carrito con ANULACION ── " . $errorBackofficeSIS;
                            exit();
                        }

                        /** Lugar peligroso: La validación ha fallado pero la respuesta indica que el pedido ha sido pagado. */
                        escribirLog("ERROR", $idLog, "ERROR VALIDANDO EL CARRITO, PERO SE HA RECIBIDO RESPUESTA OK POR PARTE DE REDSYS ── Revisar en el Portal de Administración la operación " . $pedido . " ya que es posible que el cliente haya completado el pago correctamente.");
                        
                        $redsys->validateOrder($pedidoSecuencial, _PS_OS_ERROR_, $total/100, $metodo, "[REDSYS] " . $errorBackofficeSIS);
                        $redsys->addPaymentInfo($pedidoSecuencial, $pedido, $metodoOrder, $idLog);

                        echo "Error validando el carrito con respuesta OK ── " . $errorBackofficeSIS;
                        exit();
                        
                    }

                    $mensajeError += "";

                    //$redsys->addMessage($id_customer, $pedidoSecuencial, "[REDSYS] " . $errorBackofficeSIS);
                    //$redsys->addMessage($id_customer, $pedidoSecuencial, "[REDSYS] Ha ocurrido un error al procesar el carrito. Revise el Portal de Administración del TPV Virtual para revisar el estado de la operación.");
                    if(Configuration::get('REDSYS_MANTENER_CARRITO') == 0){
                        $redsys->validateOrder($pedidoSecuencial, _PS_OS_CANCELED_, $total/100, $metodo, "[REDSYS] " . $errorBackofficeSIS);
                        $redsys->addPaymentInfo($pedidoSecuencial, $pedido, $metodoOrder, $idLog);
                    }

                    escribirLog("ERROR", $idLog, $mensajeError);
                    escribirLog("ERROR", $idLog, $errorBackofficeSIS);
                    echo "Error validando el carrito ── " . $errorBackofficeSIS;
                    exit();
                }
                /** Validamos Objeto cliente **/
                $customer = $cliente ? new Customer((int)$cart->id_customer) : new Guest((int)$cart->id_guest);

                if (!$cliente && Configuration::get('REDSYS_LOG_CART'))
                    escribirLog("DEBUG", $idLog, "Cliente serializado: " . serialize($customer));

                if (!Validate::isLoadedObject($customer)) {
                    escribirLog("ERROR", $idLog, "Error validando el cliente.");
                    escribirLog("ERROR", $idLog, $errorBackofficeSIS);
                    echo "Error validando al cliente ── " . $errorBackofficeSIS;
                    exit();
                }
                
                // DsResponse
                $respuesta = (int)$respuesta;
                
                if ($respuesta < 101 && checkAutCode($id_trans)) {
                    /** Compra válida **/

                    if ($tipoTransaccion == '0' && ($cart->getOrderTotal(true, Cart::ONLY_SHIPPING) > 0) )
                        $shippingPaid = 1;
                    else
                        $shippingPaid = 0;

                    escribirLog("DEBUG", $idLog, "Importe del envío: " . number_format($cart->getOrderTotal(true, Cart::ONLY_SHIPPING), 2) . " | Status: " . $shippingPaid);

                    
                    //$redsys->addMessage($id_customer, $pedidoSecuencial, "[REDSYS] " . $errorBackofficeSIS);
                    $redsys->validateOrder($cart->id, $estadoFinal, $total/100, $metodo, "[REDSYS] " . $errorBackofficeSIS, array('transaction_id' => $pedido), (int)$cart->id_currency, false, (property_exists($customer, "secure_key") && !is_null($customer->secure_key)) ? $customer->secure_key : false);

                    $merchantIdentifier = $miObj->getParameter('Ds_Merchant_Identifier');
                    if (Configuration::get ( 'REDSYS_REFERENCIA' ) == 1 && ! $cart->isGuestCartByCartId ( $cart->id ) && $merchantIdentifier != null) {
                        $cardNumber=$miObj->getParameter('Ds_Card_Number');
                        $brand=$miObj->getParameter('Ds_Card_Brand');
                        $cardType=$miObj->getParameter('Ds_Card_Type');
                        $redsys->saveReference ( $customer->id, $merchantIdentifier, $cardNumber, $brand, $cardType);
                    }

                    $order = Order::getByCartId($cart->id);

                    Redsys_Order::saveOrderDetails($order->id, $pedido, $order_method, $miObj->getParameter('Ds_TransactionType'), $total, $shippingPaid);
                    $redsys->addPaymentInfo($pedidoSecuencial, $pedido, $metodoOrder, $idLog, true);

                    escribirLog("INFO ", $idLog, "El pedido con ID de carrito " . $cart->id . " (" . $pedido . ") es válido y se ha registrado correctamente.");
                    escribirLog("INFO ", $idLog, $errorBackofficeSIS);
                    echo "Pedido validado con éxito ── " . $errorBackofficeSIS;
                    exit();
                    
                } else {

                    //$redsys->addMessage($id_customer, $pedidoSecuencial, "[REDSYS] " . $errorBackofficeSIS);
                    if(!Configuration::get('REDSYS_MANTENER_CARRITO')){
                        $redsys->validateOrder($pedidoSecuencial, _PS_OS_CANCELED_, 0, $metodo, "[REDSYS] " . $errorBackofficeSIS);
                        $redsys->addPaymentInfo($pedidoSecuencial, $pedido, $metodoOrder, $idLog);
                    }
                }

                echo "El pedido ha finalizado con errores ── " . $errorBackofficeSIS;
                escribirLog("ERROR", $idLog, "El pedido con ID de carrito " . $pedidoSecuencial . " (" . $pedido . ") ha finalizado con errores.");
                escribirLog("ERROR", $idLog, $errorBackofficeSIS);
                exit();

            } else {

                $cart = new Cart($pedidoSecuencial);
                $redsys = new Redsyspur();

                (empty($cart)) ? ($cartInfo = "El objeto del carrito está vacío") : ($cartInfo = serialize($cart));
                    if(Configuration::get('REDSYS_LOG_CART'))
                        escribirLog("INFO ", $idLog, "GET ─ CARRITO SERIALIZADO: " . $cartInfo);
                
                $cliente = true;
                /** Validamos Objeto carrito **/
                if ($cart->id_customer == 0) {
                    escribirLog("DEBUG", $idLog, "Excepción validando el carrito. Cliente vacío. Puede no estar logueado, cargamos el guest.");

                    if ($cart->id_guest == 0)
                        escribirLog("ERROR", $idLog, "Error validando el carrito. Cliente vacío y Guest vacío.");
                    else 
                        $id_customer = $cart->id_guest;

                } else {
                    $id_customer = $cart->id_customer;
                }

                escribirLog("ERROR", $idLog, "Notificación: El pedido con ID de carrito " . $pedidoSecuencial . " es inválido.");
                escribirLog("ERROR", $idLog, "Error validando el pedido con ID de carrito " . $pedidoSecuencial . " (" . $pedido . "). Resultado de las validaciones [Firma|Respuesta|Moneda|FUC|Pedido|Importe]: [" . checkFirma($firma_local, $firma_remota) . "|" . checkRespuesta($respuesta) . "|" . checkMoneda($moneda) . "|" . checkFuc($codigo) . "|" . checkPedidoAlfaNum($pedido, Configuration::get('REDSYS_PEDIDO_EXTENDIDO') == 1) . "|" . checkImporte($total) . "]" );

                if ($respuesta < 101) {
                    /** Lugar peligroso: La validación ha fallado pero la respuesta indica que el pedido ha sido pagado. */
                    
                    $gateway_params = $redsys->getGatewayParameters($order_method);
                    $gateway_params['moneda'] = $moneda;

                    if(Redsys_Order::cancellation($gateway_params, $pedidoSecuencial, $total)){
                        
                        escribirLog("ERROR", $idLog, "ERROR VALIDANDO EL CARRITO, PERO SE HA RECIBIDO RESPUESTA OK POR PARTE DE REDSYS ── La orden se ha anulado y se ha devuelto el importe al cliente.");
                        $metodoOrder = "Anulada 9929";

                        if(Configuration::get('REDSYS_MANTENER_CARRITO')) {
                            echo "Error en las validaciones con ANULACION, no se crea orden ── " . $errorBackofficeSIS;
                            exit();    
                        }
                        
                        $redsys->validateOrder($pedidoSecuencial, _PS_OS_CANCELED_, 0, $metodo, "[REDSYS] " . $errorBackofficeSIS);
                        $redsys->addPaymentInfo($pedidoSecuencial, $pedido, $metodoOrder, $idLog);

                        echo "Error en las validaciones con ANULACION ── " . $errorBackofficeSIS;

                    }else{
                        /** Lugar peligroso: La validación ha fallado pero la respuesta indica que el pedido ha sido pagado. */
                        escribirLog("ERROR", $idLog, "ERROR VALIDANDO EL CARRITO, PERO SE HA RECIBIDO RESPUESTA OK POR PARTE DE REDSYS ── Revisar en el Portal de Administración la operación " . $pedido . " ya que es posible que el cliente haya completado el pago correctamente.");
                    
                        $redsys->validateOrder($pedidoSecuencial, _PS_OS_ERROR_, $total/100, $metodo, "[REDSYS] " . $errorBackofficeSIS);
                        $redsys->addPaymentInfo($pedidoSecuencial, $pedido, $metodoOrder, $idLog); 

                        echo "Error en las validaciones con respuesta OK ── " . $errorBackofficeSIS;
                    }
                    
                    exit();
                }

                if(Configuration::get('REDSYS_MANTENER_CARRITO') == 0){
                    $redsys->validateOrder($pedidoSecuencial, _PS_OS_CANCELED_, 0, $metodo, "[REDSYS] " . $errorBackofficeSIS);
                    $redsys->addPaymentInfo($pedidoSecuencial, $pedido, $metodoOrder, $idLog);
                }

                echo "Error en las validaciones ── " . $errorBackofficeSIS;

                escribirLog("ERROR", $idLog, $errorBackofficeSIS);
                exit();
            }
        
        } catch (Exception $e){
            
            escribirLog("ERROR", "0000000000000000000000000error", "Excepcion en la validacion: ".$e->getMessage());
            die("Excepcion en la validacion.");
        }
    }
}