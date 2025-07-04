<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Cex/CexRest.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Correos/CorreosSoap.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Correos/CorreosRest.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialSendersDao.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialProductsDao.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialUtilitiesDao.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/functions.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Commons/NeedCustoms.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialOrder.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialOrders.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialSenders.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialCarrier.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialConfig.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialShippingNumber.php';

require_once dirname(__FILE__) . '/../../vendor/pdfmerger.php';

class AdminCorreosOficialUtilitiesProcessController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;
    protected $context;

    private $dao;
    private $utilities_dao;
    private $senders_dao;
    private $products_dao;

    public $correos_soap;
    public $cex_rest;

    private $default_sender;

    public function __construct()
    {
        parent::__construct();

        global $co_debugCorreosOficial;
        $this->context = Context::getContext();

        $this->dao = new CorreosOficialDao();
        $this->senders_dao = new CorreosOficialSenders();
        $this->utilities_dao = new CorreosOficialUtilitiesDao();

        $this->correos_soap = new CorreosSoap();
        $this->cex_rest = new CexRest();

        $this->default_sender = CorreosOficialSenders::getDefaultSender();

        switch (Tools::getValue('action')) {

                // Acciones y llamadas a WS
            case 'registerOrders':
                $orders = Normalization::normalizeData('selectedData');
                $pickup = Normalization::normalizeData('selectedGrabarRecogida');
                $print_label = Normalization::normalizeData('selectedImprimirEtiqueta');
                $package_size = Normalization::normalizeData('selectedTamanioPaquete');
                $PickupDate = Normalization::normalizeData('PickupDateRegister');
                $PickupFrom = Normalization::normalizeData('PickupFromRegister');
                $PickupTo = Normalization::normalizeData('PickupToRegister');
                $this->registerOrders($orders, $package_size, $PickupDate, $PickupFrom, $PickupTo, $pickup, $print_label);
                break;

            case 'printLabelsGenerated':
                $orders = Normalization::normalizeData('selectedDataReimpresion');
                $labelType = Normalization::normalizeData('selectedTipoEtiquetaReimpresion');
                $labelFormat = Normalization::normalizeData('selectedFormatEtiquetaReimpresion');
                $labelPosition = Normalization::normalizeData('selectedPosicionEtiquetaReimpresion');
                $this->getEtiquetasByShippingNumber($orders, $labelType, $labelPosition, $labelFormat);
                break;

            case 'generatePickups':
                $orders = Normalization::normalizeData('selectedDataPickups');
                $PickupDate = Normalization::normalizeData('PickupDate');
                $PickupFrom = Normalization::normalizeData('PickupFrom');
                $PickupTo = Normalization::normalizeData('PickupTo');
                $print_label = Normalization::normalizeData('PrintLabelPickups');
                $package_size = Normalization::normalizeData('TamLabelPickups');
                $this->generatePickups($orders, $print_label, $package_size, $PickupDate, $PickupFrom, $PickupTo);
                break;

            case 'getCustomsDoc':
                $orders = Normalization::normalizeData('selectedDataDocAduanera');
                $optionButton = Tools::getValue('optionButton');
                $this->getDocAduanera($orders, $optionButton);
                break;

                // Búsquedas
            case 'searchOrdersRegistration':
                $date_from = Normalization::normalizeData('FromDateOrdersReg');
                $date_to = Normalization::normalizeData('ToDateOrdersReg');
                $this->getDataTableSearch($date_from, $date_to);
                break;
            case 'searchLabels':
                $date_from = Normalization::normalizeData('FromDateLabels');
                $date_to = Normalization::normalizeData('ToDateLabels');
                $this->getShippingsPreregistered($date_from, $date_to);
                break;
            case 'searchOrdersSummary':
                $date_from = Normalization::normalizeData('FromDateSummary');
                $date_to = Normalization::normalizeData('ToDateSummary');
                $this->getShippingsPreregistered($date_from, $date_to);
                break;
            case 'searchPickups':
                $date_from = Normalization::normalizeData('FromDatePickups');
                $date_to = Normalization::normalizeData('ToDatePickups');
                $this->getShippingsPreregistered($date_from, $date_to);
                break;
            case 'searchCustomsDoc':
                $date_from = Normalization::normalizeData('FromDateCustomsDoc');
                $date_to = Normalization::normalizeData('ToDateCustomsDoc');
                $this->getShippingsCustomsDoc($date_from, $date_to);
                break;

                // Funciones Auxiliares
            case 'getActiveProducts':
                $this->getActiveProductsForDataTableOrders();
                break;
            case 'deleteFiles':
                $this->deleteFiles();
                break;
            case 'generatePDFManifest':
                $orders = Normalization::normalizeData('selectedData');
                $this->generatePDFManifest($orders);
                break;
        }
    }

    // PREREGISTRO MASIVO DE PEDIDOS
    public function registerOrders($orders, $package_size, $PickupDate, $PickupFrom, $PickupTo, $pickup = 'N', $print_label = 'N')
    {

        $errores = array();
        $done_orders = array();
        $shipping_numbers = array();

        $horaActual = date('Y-m-d H:i:s', time());

        $activateDimensionsByDefault = CorreosOficialConfig::checkDimensionsByDefaultActivated();

        foreach ($orders as $order => $row) {

            // Si el pedido ya está preregistrado continuamos
            if (!empty($row['shipping_number'])) {
                continue;
            }

            $co_order = new CorreosOficialOrder($row['id_order']);

            if ($co_order->isCashOnDeliveryMethodType()) {
                $bank_acc_number = CorreosOficialConfigDao::getConfigValue('BankAccNumberAndIBAN');
                $bank_acc_number = Crypto::decrypt($bank_acc_number);
                $row['payment_method'] = 'cod';
                $row['cash_on_delivery_value'] = number_format($co_order->getTotalPaid(), 2);
                $row['cashondelivery_bankac'] = $bank_acc_number;
            }

            $customer = CorreosOficialOrders::getCustomerOrder($row['id_order']);
            $require_customs_doc = NeedCustoms::isCustomsRequired(
                $this->default_sender['sender_cp'],
                $customer['delivery_postcode'],
                $this->default_sender['sender_iso_code_pais'],
                $customer['delivery_country_iso']
            );

            $id_product_reg = $row['id_product'];
            $company = $this->utilities_dao->getCompany($row['id_product']);

			if (!$company) {
				$error_message = $this->l('Selected Carrier is not a product of Correos neither Correos Express. Please, select a valid product');
			} else {
				$client = $this->utilities_dao->getDataClient($company['company'], false, $row['sender_default']);
				if (!isset($client[0]['id'])) {
					$error_message = $this->l('No valid sender selected');
				}
			}

            if(!isset($client[0]['id']) || !$company){
                $errores[] = array(
                    'id_order' => $row['id_order'],
                    'reference' => $row['reference'],
                    'error' => $error_message
                );
                continue;
            }

            $product = CorreosOficialCarrier::getCarrierByProductId($row['id_product']);

            // Comprobamos que el carrier estçe dentro de los habilitados para las dimensiones por defecto
			$available_carriers_default_dimensions = ['S0179', 'S0176', 'S0178'];
			$available_carrier_d = (in_array($product['codigoProducto'],  $available_carriers_default_dimensions )) ? 1 : 0;
			$dimensions = $this->getDimensionsByType($available_carrier_d, $activateDimensionsByDefault);

            switch ($company['company']) {
                case 'Correos':

                    $shipping_data = array(
                        'id_order' => $row['id_order'],
                        'order' => $row,
                        'default_sender' => $this->default_sender,
                        'customer' => $customer,
                        'require_customs_doc' => $require_customs_doc ? 1 : 0,
                        'product' => $product,
                        'company' => 'Correos',
                        'bultos' => $row['bultos'],
                        'client' => $client,
                        'source_channel' => 'PRS'
                    );

                    /* mergeamos las dimensiones */
                    $shipping_data = array_merge($shipping_data, $dimensions);
                    $result_correos = $this->correos_soap->registrarEnvio($shipping_data, 'utilities', $row['sender_default']);

                    // PREREGISTRO OK
                    if ($result_correos['codigoRetorno'] == '0') {

                        // UN SOLO BULTO
                        if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->TotalBultos == 1) {
                            $num_bultos = 1;
                            $CodExpedicion = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->CodExpedicion;
                            $CodEnvio = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto->CodEnvio;

                            $shipping_numbers[] = $CodEnvio;

                            $order_done = array(
                                'id_order' => $row['id_order'],
                                'shipping_number' => $CodEnvio,
                                'exp_number' => $CodExpedicion,
                            );

                            $order_done_ajax = array(
                                'id_order' => $row['id_order'],
                                'shipping_number' => $CodExpedicion,
                                'exp_number' => $CodExpedicion
                            );

                            $order_update = array(
                                'id_order' => $row['id_order'],
                                'reference' => $row['reference'],
                                'shipping_number' => $CodExpedicion,
                                'carrier_type' => 'Correos',
                                'date_add' => $row['date_add'],
                                'id_product' => $id_product_reg,
                                'id_carrier' => $product['id_carrier'],
                                'bultos' => $num_bultos,
                                'AT_code' => '',
                                'last_status' => 'Preregistrado',
                                'status' => 'Grabado',
                                'updated_at' => $horaActual,
                                'pickup' => 0,
                                'pickup_status' => '',
                                'id_sender' => $row['sender_default'],
                                'require_customs_doc' => $require_customs_doc ? 1 : 0,
                                // En utilidades añadimos solo el iban y el value por si es contrareembolso, el resto de added_values no se añaden
                                'added_values_cash_on_delivery_iban' => isset($row['cashondelivery_bankac']) ? $row['cashondelivery_bankac'] : null,
                                'added_values_cash_on_delivery_value' => isset($row['cash_on_delivery_value']) ? floatval($row['cash_on_delivery_value']) : 0,
                            );

                            // Actualiza el tracking_number de Resumen transportista en el pedido
                            CorreosOficialShippingNumber::updateShipingNumberInOrder($row['id_order'], mb_convert_encoding($CodEnvio,'UTF-8','ISO-8859-1'));

                            /* Se añaden las dimensiones a la base de datos */
							$order_done = array_merge($order_done, $dimensions);
                            $this->utilities_dao->insertDataOrder('correos_oficial_saved_orders', $order_done);
                            $this->utilities_dao->insertOrder($order_update);
                            $this->changeOrderStatus($row['id_order']);
                            $done_orders[] = $order_done_ajax;

                            // MULTIBULTO
                        } elseif ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->TotalBultos > 1) {
                            $bultos = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->Bultos;
                            $num_bultos = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->TotalBultos;
                            $CodExpedicion = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->CodExpedicion;

                            foreach ($bultos->Bulto as $bulto => $field) {
                                $CodEnvio = $field->CodEnvio;

                                $shipping_numbers[] = $CodEnvio;

                                $order_done = array(
                                    'id_order' => $row['id_order'],
                                    'shipping_number' => $CodEnvio,
                                    'exp_number' => $CodExpedicion
                                );

                                $order_done_ajax = array(
                                    'id_order' => $row['id_order'],
                                    'shipping_number' => $CodExpedicion,
                                    'exp_number' => $CodExpedicion
                                );

                                /* Se añaden las dimensiones a la base de datos */
								$order_done = array_merge($order_done, $dimensions);
                                $this->utilities_dao->insertDataOrder('correos_oficial_saved_orders', $order_done);
                            }

                            $order_update = array(
                                'id_order' => $row['id_order'],
                                'reference' => $row['reference'],
                                'shipping_number' => $CodExpedicion,
                                'carrier_type' => 'Correos',
                                'date_add' => $row['date_add'],
                                'id_product' => $id_product_reg,
                                'id_carrier' => $product['id_carrier'],
                                'bultos' => $num_bultos,
                                'last_status' => 'Preregistrado',
                                'AT_code' => '',
                                'status' => 'Grabado',
                                'updated_at' => $horaActual,
                                'pickup' => 0,
                                'pickup_status' => '',
                                'id_sender' => $this->default_sender['id'],
                                'require_customs_doc' => $require_customs_doc ? 1 : 0,
                                // En utilidades añadimos solo el iban y el value por si es contrareembolso, el resto de added_values no se añaden
                                'added_values_cash_on_delivery_iban' => isset($row['cashondelivery_bankac']) ? $row['cashondelivery_bankac'] : null,
                                'added_values_cash_on_delivery_value' => isset($row['cash_on_delivery_value']) ? floatval($row['cash_on_delivery_value']) : 0,
                            );

                            // Actualiza el tracking_number de Resumen transportista en el pedido
                            CorreosOficialShippingNumber::updateShipingNumberInOrder($row['id_order'], mb_convert_encoding($CodEnvio,'UTF-8','ISO-8859-1'));

                            $this->utilities_dao->insertOrder($order_update);
                            $this->changeOrderStatus($row['id_order']);
                            $done_orders[] = $order_done_ajax;
                        }

                        // Bloque de recogidas Correos en Gestión masiva de productos
                        if ($pickup == 'S') {

                            $pickup_details_array = array(
                                'id_order' => $row['id_order'],
                                'bultos' => $num_bultos,
                                'order_reference' => $row['reference'],
                                'pickup_date' => $PickupDate,
                                'sender_from_time' => $PickupFrom,
                                'sender_to_time' => $PickupTo,
                                'sender_address' => $this->default_sender['sender_address'],
                                'sender_city' => $this->default_sender['sender_city'],
                                'sender_cp' => $this->default_sender['sender_cp'],
                                'sender_name' => $this->default_sender['sender_name'],
                                'sender_contact' => $this->default_sender['sender_contact'],
                                'sender_phone' => $this->default_sender['sender_phone'],
                                'sender_email' => $this->default_sender['sender_email'],
                                'sender_nif_cif' => $this->default_sender['sender_nif_cif'],
                                'sender_country' => $this->default_sender['sender_iso_code_pais'],
                                'producto' => $product['codigoProducto'],
                                'print_label' => $print_label,
                                'package_type' => $package_size,
                                'shipping_numbers' => CorreosOficialUtils::transformArrayForPickups($shipping_numbers),
                                'client' => $client
                            );

                            $result_correos_pickups = $this->correos_soap->registrarRecogida($pickup_details_array);

                            if ($result_correos_pickups['codigoRetorno'] == '0') {

                                $pickup_number = $result_correos_pickups['codSolicitud'];

                                $pickup_order_data = array(
                                    'id_order' => $row['id_order'],
                                    'pickup_number' => mb_convert_encoding($pickup_number,'UTF-8','ISO-8859-1'),
                                    'pickup_date' => $PickupDate,
                                    'pickup_from_hour' => $PickupFrom,
                                    'pickup_to_hour' => $PickupTo,
                                    'package_size' => intval($package_size),
                                    'print_label' => $print_label,
                                    'pickup_status' => "Grabado"
                                );

                                $this->utilities_dao->savePickup($pickup_order_data);
                            } else {
                                $errores[] = array(
                                    'id_order' => $row['id_order'],
                                    'reference' => $row['reference'],
                                    'error' => mb_convert_encoding($result_correos_pickups['mensajeRetorno'],'UTF-8','ISO-8859-1')
                                );
                            }
                        }
                    }
                    // PREREGISTRO KO
                    else {
                        if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->TotalBultos == 1) {
                            $errores[] = array(
                                'id_order' => $row['id_order'],
                                'reference' => $row['reference'],
                                'error' => mb_convert_encoding($result_correos['mensajeRetorno'],'UTF-8','ISO-8859-1')
                            );
                        } else {

                            $bulto_error = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->BultosError->BultoError->DescError;

                            if ($result_correos['status_code'] == 0) {
                                $mensaje_retorno = CorreosOficialErrorManager::checkStateConnection($result_correos['status_code']);
                            }

                            $error = $bulto_error == '' ? $mensaje_retorno : $bulto_error;
                            $errores[] = array(
                                'id_order' => $row['id_order'],
                                'reference' => $row['reference'],
                                'error' => $error
                            );
                        }
                    }

                    break;

                case 'CEX':

                    $shipping_details_array = array(
                        'id_order' => $row['id_order'],
                        'order' => $row,
                        'default_sender' => $this->default_sender,
                        'customer' => $customer,
                        'require_customs_doc' => 0,
                        'product' => $product,
                        'company' => 'CEX',
                        'bultos' => $row['bultos'],
                        'AT_code' => $row['AT_code'],
                        'pickup' => $pickup,
                        'PickupDate' => $PickupDate,
                        'PickupFrom' => $PickupFrom,
                        'PickupTo' => $PickupTo,
                        'client' => $client
                    );

                    $result_cex = $this->cex_rest->registrarEnvioUtilidades($shipping_details_array, $row['sender_default']);
                    $result_cex_decoded = json_decode($result_cex['json_retorno'], true);

                    if ($result_cex['codigoRetorno'] == '0') {

                        $bultos_reg_cex = $result_cex_decoded['listaBultos'];
                        $num_bultos_reg = count($result_cex_decoded['listaBultos']);
                        $CodExpedicion = mb_convert_encoding($result_cex_decoded['datosResultado'],'UTF-8','ISO-8859-1');

                        foreach ($bultos_reg_cex as $bulto => $field) {
                            $CodEnvio = str_replace("'", "", $field['codUnico']);
                            $orden = intval($field['orden']);

                            $order_done = array(
                                'id_order' => $row['id_order'],
                                'shipping_number' => $CodEnvio,
                                'exp_number' => $CodExpedicion
                            );

                            $order_done_ajax = array(
                                'id_order' => $row['id_order'],
                                'shipping_number' => $CodExpedicion,
                                'exp_number' => $CodExpedicion,
                                'bultos' => $num_bultos_reg,
                                'company' => 'CEX'
                            );

                            /* Se añaden las dimensiones a la base de datos */
							$order_done = array_merge($order_done, $dimensions);
                            $this->utilities_dao->insertDataOrder('correos_oficial_saved_orders', $order_done);
                        }

                        $order_update = array(
                            'id_order' => $row['id_order'],
                            'reference' => $row['reference'],
                            'shipping_number' => $CodExpedicion,
                            'carrier_type' => 'CEX',
                            'date_add' => $row['date_add'],
                            'id_product' => $id_product_reg,
                            'id_carrier' => $product['id_carrier'],
                            'bultos' => count($result_cex_decoded['listaBultos']),
                            'AT_code' => $row['AT_code'] == 'null' ? '' : $row['AT_code'],
                            'last_status' => 'SIN RECEPCION',
                            'status' => 'Grabado',
                            'updated_at' => $horaActual,
                            'pickup' => 0,
                            'pickup_status' => '',
                            'id_sender' => $row['sender_default'],
                            'require_customs_doc' => 0, // No aplica para CEX
                            // En utilidades añadimos solo el iban y el value por si es contrareembolso, el resto de added_values no se añaden
                            'added_values_cash_on_delivery_iban' => isset($row['cashondelivery_bankac']) ? $row['cashondelivery_bankac'] : null,
                            'added_values_cash_on_delivery_value' => isset($row['cash_on_delivery_value']) ? floatval($row['cash_on_delivery_value']) : 0
                        );

                        // Actualiza el tracking_number de Resumen transportista en el pedido
                        CorreosOficialShippingNumber::updateShipingNumberInOrder($row['id_order'], mb_convert_encoding($CodEnvio,'UTF-8','ISO-8859-1'));

                        $this->utilities_dao->insertOrder($order_update);
                        $done_orders[] = $order_done_ajax;
                        $this->deleteFiles();

                        // Bloque de recogidas CEX en Gestión masiva de productos
                        if ($pickup == 'S') {
                            $string_date = $result_cex_decoded['fechaRecogida'];
                            $aux_date = date_create_from_format('dmY', $string_date);
                            $pickup_date_format = date_format($aux_date, 'Y-m-d');

                            $pickup_order_data = array(
                                'id_order' => $row['id_order'],
                                'pickup_number' => $result_cex_decoded['numRecogida'],
                                'pickup_date' => $pickup_date_format,
                                'pickup_from_hour' => $result_cex_decoded['horaRecogidaDesde'],
                                'pickup_to_hour' => $result_cex_decoded['horaRecogidaHasta'],
                                'package_size' => 0,
                                'print_label' => "N",
                                'pickup_status' => "Grabado"
                            );

                            $this->utilities_dao->savePickup($pickup_order_data);
                        }

                        $this->changeOrderStatus($row['id_order']);
                        
                    } else {
                        $error = mb_convert_encoding($result_cex['mensajeRetorno'],'UTF-8','ISO-8859-1');

                        if ($error == 'timeout') {
                            $error = CorreosOficialErrorManager::checkStateConnection(0);
                        }

                        $errores[] = array(
                            'id_order' => $row['id_order'],
                            'reference' => $row['reference'],
                            'error' => $error
                        );
                    }

                    break;
            }
        }
        $response = array("done_orders" => $done_orders, "errors" => $errores);
        die(json_encode($response));
    }

    // GENERACIÓN ORDENES DE RECOGIDA
    public function generatePickups($orders, $print_label, $package_size, $PickupDate, $PickupFrom, $PickupTo)
    {

        $errores = array();
        $done_pickups = array();

        $sender = CorreosOficialSenders::getDefaultSender();

        foreach ($orders as $order => $row) {

            // Si la recogida ya está grabada continuamos
            if (!empty($row['pickup_number'])) {
                continue;
            }

            // Si el user selecciona imprimir etiqueta global
            if ($print_label == 'S') {
                $row['print_label'] = 'S';
            }

            // Si el user selecciona tamaño de paquete global
            if ($package_size != 0) {
                $row['package_size'] = $package_size;
            }

            $client = $this->utilities_dao->getDataClient($row['company']);
            $shipping_numbers[] = $this->utilities_dao->getShippingNumbersByExpediton($row['shipping_number']);

            $pickup_details_array = array(
                'id_order' => $row['id_order'],
                'bultos' => $row['bultos'],
                'order_reference' => $row['reference'],
                'pickup_date' => $PickupDate,
                'sender_from_time' => $PickupFrom,
                'sender_to_time' => $PickupTo,
                'sender_address' => $sender['sender_address'],
                'sender_city' => $sender['sender_city'],
                'sender_cp' => $sender['sender_cp'],
                'sender_name' => $sender['sender_name'],
                'sender_contact' => $sender['sender_contact'],
                'sender_phone' => $sender['sender_phone'],
                'sender_email' => $sender['sender_email'],
                'sender_nif_cif' => $sender['sender_nif_cif'],
                'sender_country' => $sender['sender_iso_code_pais'],
                'producto' => $row['codigoProducto'],
                'print_label' => $row['print_label'],
                'package_type' => $row['package_size'],
                'shipping_numbers' => $shipping_numbers,
                'client' => $client
            );

            switch ($row['company']) {
                case 'Correos':

                    $result_correos_pickups = $this->correos_soap->registrarRecogida($pickup_details_array);

                    if ($result_correos_pickups['codigoRetorno'] == '0') {

                        $pickup_number = $result_correos_pickups['codSolicitud'];

                        $pickup_order_data = array(
                            'id_order' => $row['id_order'],
                            'pickup_number' => mb_convert_encoding($pickup_number,'UTF-8','ISO-8859-1'),
                            'pickup_date' => $pickup_details_array['pickup_date'],
                            'pickup_from_hour' => $PickupFrom,
                            'pickup_to_hour' => $PickupTo,
                            'package_size' => intval($row['package_size']),
                            'print_label' => $row['print_label'],
                            'pickup_status' => "Grabado"
                        );

                        $done_pickups[] = array(
                            'id_order' => $row['id_order']
                        );

                        $this->utilities_dao->savePickup($pickup_order_data);
                    } else {
                        $errores[] = array(
                            'id_order' => $row['id_order'],
                            'reference' => $row['reference'],
                            'error' => mb_convert_encoding($result_correos_pickups['mensajeRetorno'],'UTF-8','ISO-8859-1')
                        );
                    }

                    break;

                case 'CEX':

                    $result_cex_pickups = $this->cex_rest->registrarRecogida($pickup_details_array);
                    $result_cex_pickups_decoded = json_decode($result_cex_pickups['json_retorno'], true);

                    if ($result_cex_pickups['codigoRetorno'] == '0') {

                        $pickup_number = $result_cex_pickups_decoded['numRecogida'];

                        $string_date = $result_cex_pickups['mensajeRetorno'];
                        preg_match('/ fechaRecogida: (.*?),/is', $string_date, $pickup_date);
                        $aux_date = date_create_from_format('dmY', $pickup_date[1]);
                        $pickup_date_format = date_format($aux_date, 'Y-m-d');

                        preg_match('/ horaDesde1: (.*?),/is', $string_date, $pickup_from_hour);
                        preg_match('/ horaHasta1: (.*?)$/', $string_date, $pickup_to_hour);

                        $pickup_order_data = array(
                            'id_order' => $row['id_order'],
                            'pickup_number' => $pickup_number,
                            'pickup_date' => $pickup_date_format,
                            'pickup_from_hour' => $pickup_from_hour[1],
                            'pickup_to_hour' => $pickup_to_hour[1],
                            'package_size' => 0,
                            'print_label' => "N",
                            'pickup_status' => "Grabado"
                        );

                        $done_pickups[] = array(
                            'id_order' => $row['id_order']
                        );

                        $this->utilities_dao->savePickup($pickup_order_data);
                    } else {
                        $errores[] = array(
                            'id_order' => $row['id_order'],
                            'reference' => $row['reference'],
                            'error' => $result_cex_pickups['mensajeRetorno']
                        );
                    }
                    break;
            }
        }

        $response = array("done_pickups" => $done_pickups, "errors" => $errores);
        die(json_encode($response));
    }

    //Genera HTML con Option para Columna ModProdcut de Datatable Orders
    public function getActiveProductsForDataTableOrders()
    {
        $this->products_dao = new CorreosOficialProductsDao();
        // $products = $this->products_dao->getActiveProducts(" WHERE cop.active=1 ORDER BY id ASC");
        $products = $this->products_dao->getActiveProducts(" WHERE coc.active = 1 and coc.id_shop = ".$this->context->shop->id." ORDER BY id ASC");

        $result = [];

        foreach ($products as $product) {
            $result[] = [
                "id" => $product->id,
                "name" => $product->name,
                "max_packages" => $product->max_packages,
                "codigoProducto" => $product->codigoProducto,
                "company" => $product->company,
                "product_type" => $product->product_type
            ];
        }

        die(json_encode($result));
    }

    public function getDataTableSearch($date_from, $date_to)
    {
        $orders = $this->utilities_dao->getOrdersForMassManagement($date_from, $date_to);
        $orders = $this->addExtraPackagesInfo($orders);
        die(json_encode($orders));
    }

    // Rellena la tabla de Reimpresion de Etiquetas con pedidos ya prerregistrados
    public function getShippingsPreregistered($date_from, $date_to)
    {
        $orders = $this->utilities_dao->getShippings($date_from, $date_to);
        $orders = $this->addExtraPackagesInfo($orders);
        die(json_encode($orders));
    }

    //Llama la función del dao para rellenar la tabla de Generación de documentación aduanera
    public function getShippingsCustomsDoc($date_from, $date_to)
    {
        $orders = $this->utilities_dao->getShippingsCustomsDoc($date_from, $date_to);
        $orders = $this->addExtraPackagesInfo($orders);
        die(json_encode($orders));
    }

    //Obtiene etiquetas
    public function getEtiquetasByShippingNumber($orders, $labelType, $labelPosition, $labelFormat = 0)
    {
        $pdf = new PDFMerger($labelType, $labelFormat);
        $tempFolder = get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/pdftmp";
        $pdfOutputFile = $tempFolder . "/" . uniqid('labels_') . '.pdf';
        $expedition_number = '';
        $shipping_numbers = '';
        $labels = [];
        $logoBase64 = '';

        $useUserLogo = CorreosOficialConfigDao::getConfigValue('ChangeLogoOnLabel');
		$getUserLogo = CorreosOficialConfigDao::getConfigValue('UploadLogoLabels');
		
		if ($useUserLogo == 'on') {
            $imagedata = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/media/logo_label/" . $getUserLogo);
            $logoBase64 = base64_encode($imagedata);
        }

        // Obtención de etiquetas
        foreach ($orders as $order) {
            $correos_oficial_order = new CorreosOficialOrder($order['id_order']);
            $shipping_numbers = $correos_oficial_order->getShippingNumbersByIdOrderForSavedOrder();
            if (!array_key_exists('exp_number', $order)) {
                $expedition_number = $correos_oficial_order->getExpeditionNumberByIdOrderForSavedOrder();
            } else {
                $expedition_number = $order['exp_number'];
            }

            $company = $correos_oficial_order->getCarrierTypeByOrderId();

            if ($labelType == LABEL_TYPE_THERMAL) { // Térmicas
                if ($company == 'CEX') {
                    $labelsResponse = $this->cex_rest->getLabelFromWS($expedition_number, CEX_LABEL_THERMAL_ADHESIVE, $logoBase64, $labelFormat, 'order');
                    $label = $labelsResponse->listaEtiquetas;
                    if ($label !== null) {
                        $labels[] = $label;
                    }
                } elseif ($company == 'Correos') {
                    foreach ($shipping_numbers as $number) {
                        $label = $this->correos_soap->SolicitudEtiquetaOp($number);
                        if ($label !== null) {
                            $labels[] = $label;
                        }
                    }

                }

            } else { // Adhesiva
                // Acciones según formato
                switch ($labelFormat) {
                    case LABEL_FORMAT_3A4:
                        $bulks = $order['bultos'];

                        $labelsResponse = $this->cex_rest->getLabelFromWS($expedition_number, CEX_LABEL_3A4, $logoBase64, $labelFormat, 'order');
                        $label = $labelsResponse->listaEtiquetas;

                        if ($label != null) {
                            // si tenemos más de un bulto
                            if ($bulks > 1) {
                                // Generamos un pdf temporal con las etiquetas obtenidas
                                $tempPath = $tempFolder . '/split_labels_' . $expedition_number . '.pdf';
                                file_put_contents($tempPath, base64_decode($label[0]));

                                // Cortamos en pdf individuales
                                $labels[] = $pdf->splitByFormat($tempPath, $bulks, LABEL_FORMAT_3A4, 3);

                                // Eliminamos el archivo temporal
                                unlink($tempPath);
                            } else {
                                $labels[] = $this->mergeLabelArraysIntoOne($label); // Agrega la etiqueta al array de etiquetas
                            }
                        }

                        break;
                    case LABEL_FORMAT_4A4: // 4/A4
                        break;
                    default: // Estandar
                        if ($company == 'CEX') {
                            $labelsResponse = $this->cex_rest->getLabelFromWS($expedition_number, CEX_LABEL_THERMAL_ADHESIVE, $logoBase64, $labelFormat, 'order');
                            $label = $labelsResponse->listaEtiquetas;
                            if ($label !== null) {
                                $labels[] = $label;
                            }
                        } elseif ($company == 'Correos') {
                            foreach ($shipping_numbers as $number) {
                                $label = $this->correos_soap->SolicitudEtiquetaOp($number);
                                if ($label !== null) {
                                    $labels[] = $label;
                                }
                            }
                        }

                        break;
                }

            }

        }

        $labels = $this->mergeLabelArraysIntoOne($labels);
        // Generación PDFs temporales
        for ($i = 0; $i < count($labels); $i++) {
            $tempPathPDF = $tempFolder . '/E_' . $i . '_' . $expedition_number[$i] . '.pdf';
            file_put_contents($tempPathPDF, base64_decode($labels[$i]));
            $pdf->addPDF($tempPathPDF, 'all');
        }

        // Unión de etiquetas
        if ($labelType == LABEL_TYPE_THERMAL) { // Térmicas
            $pdf->mergeTopages(
                'file',
                $pdfOutputFile
            );
        } else { // Adhesivas
            $pdf->merge(
                'file',
                $pdfOutputFile,
                $labelType,
                $labelPosition
            );
        }

        $mensajeRetorno = array(
			'codigoRetorno' => 0,
			'filePath' => $pdfOutputFile,
		);

		die(json_encode($mensajeRetorno));
    }

    public function mergeLabelArraysIntoOne($labels) {
		$is_multidimensional = false;
	
		foreach ($labels as $label) {
			if (is_array($label)) {
				$is_multidimensional = true;
				break;
			}
		}
	
		if ($is_multidimensional) {
			$flattenedLabels = [];
			foreach ($labels as $labelGroup) {
				if (is_array($labelGroup)) {
					$flattenedLabels = array_merge($flattenedLabels, $labelGroup);
				} else {
					$flattenedLabels[] = $labelGroup;
				}
			}
			return $flattenedLabels;
		} else {
			return $labels;
		}
	}

    public function getDocAduanera($orders, $optionButton)
    {

        $errors = array();
        $files = array();

        foreach ($orders as $order => $field) {
            $customer_country = $field['customer_country'];
            $customer_name = $field['customer_name'];
            $exp_number = $field['shipping_number'];
            $shipping_numbers = $this->utilities_dao->getShippingNumbersByExp($exp_number);

            foreach ($shipping_numbers as $shipping_number => $field2) {
                $result_doc_aduanera = $this->correos_soap->documentacionAduaneraOp($optionButton, $field2['shipping_number'], $customer_country, $customer_name);
                if ($result_doc_aduanera['codigoRetorno'] == '0') {
                    switch ($optionButton) {
                        case 'ImprimirCN23Button':
                            $prefijo_archivo = 'CN23';
                            $fichero = $result_doc_aduanera['xml_retorno']->soapenvBody->RespuestaSolicitudDocumentacionAduaneraCN23CP71->Fichero;
                            break;
                        case 'ImprimirDUAButton':
                            $prefijo_archivo = 'DCAF';
                            $fichero = $result_doc_aduanera['xml_retorno']->soapenvBody->RespuestaSolicitudDocumentacionAduanera->Fichero;
                            break;
                        case 'ImprimirDDPButton':
                            $prefijo_archivo = 'DDP';
                            $fichero = $result_doc_aduanera['xml_retorno']->soapenvBody->RespuestaSolicitudDocumentacionAduanera->Fichero;
                            break;
                    }
                    $pdf = file_put_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/pdftmp/" . $prefijo_archivo . "_" . $field2['shipping_number'] . ".pdf", base64_decode($fichero));
                    $files[] = array('filename' => $prefijo_archivo . "_" . $field2['shipping_number'] . ".pdf");
                } else {
                    $errors[] = array(
                        'id_order' => $field['id_order'],
                        'reference' => $field['reference'],
                        'error' => mb_convert_encoding($result_doc_aduanera['mensajeRetorno'],'UTF-8','ISO-8859-1')
                    );
                }
            }
        }
        $response = array("files" => $files, "errors" => $errors);
        die(json_encode($response));
    }

    // Borra archivos temporales de la carpeta pdftmp
    public function deleteFiles()
    {
        foreach (glob('../modules/correosoficial/pdftmp/labels*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob('../modules/correosoficial/pdftmp/CEX_*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob('../modules/correosoficial/pdftmp/E_*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob('../modules/correosoficial/pdftmp/CN23*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob('../modules/correosoficial/pdftmp/DCAF*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob('../modules/correosoficial/pdftmp/DDP*.*') as $filename) {
            unlink($filename);
        }
        foreach (glob('../modules/correosoficial/pdftmp/manifest*.*') as $filename) {
            unlink($filename);
        }
        return 'ok';
    }

    public function generatePDFManifest($orders)
    {
        // formateamos las líneas de los pedidos dividiéndolas por compañía y por sender
        // para que haya una página en el PDF con cada información
        $client_pages = [];
        // Esta variable es para comprobar que hay más de un sender, de tal modo ponemos el nombre de la tienda en lugar del sender
        $name_store = 0;
        foreach ($orders as $order) {
            $correos_order = CorreosOficialOrders::getCorreosOrder($order['id_order']);
            $correos_packages = CorreosOficialOrders::getCorreosPackages($order['id_order'], null);
            $order_ps = new Order((int)$order['id_order']);
            $delivery_address = new Address($order_ps->id_address_delivery);

            $sender = CorreosOficialSenders::getSenderById($correos_order['id_sender']);
            $product = CorreosOficialCarrier::getCarrierByProductId($correos_order['id_product']);

            // Creamos una key con company para agruparlos
            $key = $order['company'];
            $sender_customer_code = ($key == 'Correos') ? $sender['correos_code'] : $sender['cex_code'];
			$id_sender = $correos_order['id_sender'];

            if (!isset($client_pages[$key])) {
                $client_pages[$key] = [
                    'name' => $sender['sender_name'],
                    'client_code' => $sender_customer_code,
                    'id_sender' => $correos_order['id_sender'],
                    'company' => $order['company'],
                    'orders' => []
                ];
            } else {
                // Comprobamos que haya más de un sender
                if ($id_sender !== $client_pages[$key]['id_sender']) {
                    $name_store = 1;
                }
            }

            $client_pages[$key]['orders'][$order['id_order']] = $order;

            //Añadimos los bultos
            $client_pages[$key]['orders'][$order['id_order']]['bultos'] = $correos_order['bultos'];

            // Agrupamos los paquetes del pedido para tener el los total KGs y los Shipping numbers agrupados
            $client_pages[$key]['orders'][$order['id_order']]['total_weight'] = 0;
            $shipping_numbers = [];
            foreach ($correos_packages as $package) {
                $shipping_numbers[] = $package['shipping_number'];
                $client_pages[$key]['orders'][$order['id_order']]['total_weight'] += $package['weight'];
            }

            // shipping numbers largos de saved orders separados por comas
            $client_pages[$key]['orders'][$order['id_order']]['shipping_numbers'] = implode(',', $shipping_numbers);

            // shipping number corto, en saved_orders es el exp_number
            $client_pages[$key]['orders'][$order['id_order']]['exp_number'] = $order['shipping_number'];

            // generamos la dirección añadiendo el CP + el iso code del país
            $client_pages[$key]['orders'][$order['id_order']]['address'] = $delivery_address->address1.' '.$delivery_address->address2.' '.$delivery_address->city.' - '.$delivery_address->postcode.' '.  Country::getIsoById($delivery_address->id_country);

            // Añadimos el total de reembolso y el total del seguro en caso de tener
            $client_pages[$key]['orders'][$order['id_order']]['insurance_value'] = $correos_order['added_values_insurance_value'];
            $client_pages[$key]['orders'][$order['id_order']]['cash_on_delivery_value'] = $correos_order['added_values_cash_on_delivery_value'];

            // Creamos la información para el total por productos dentro de cada agrupación
            if (!isset($client_pages[$key]['total_products'][$correos_order['id_product']])) {

                //totales por producto
                $client_pages[$key]['total_products'][$correos_order['id_product']] = [
                    'product' => $product['name'],
                    'total_bultos' => 0,
                    'total_weight' => 0,
                    'total_insurance' => 0,
                    'total_cash_on_delivery_value' => 0
                ];
            }

            //totales por página
            if (!isset($client_pages[$key]['total_page'])) {
                $client_pages[$key]['total_page'] = [
                    'total_bultos_page' => 0,
                    'total_weight_page' => 0,
                    'total_insurance_page' => 0,
                    'total_cash_on_delivery_value_page' => 0
                ];
            }
            // Añadimos los totales a cada producto
            $client_pages[$key]['total_products'][$correos_order['id_product']]['total_bultos'] += (int)$correos_order['bultos'];
            $client_pages[$key]['total_products'][$correos_order['id_product']]['total_weight'] += floatval($client_pages[$key]['orders'][$order['id_order']]['total_weight']);
            $client_pages[$key]['total_products'][$correos_order['id_product']]['total_insurance'] += floatval($correos_order['added_values_insurance_value']);
            $client_pages[$key]['total_products'][$correos_order['id_product']]['total_cash_on_delivery_value'] += floatval($correos_order['added_values_cash_on_delivery_value']);

            // Añadimos los totales a la página
            $client_pages[$key]['total_page']['total_bultos_page'] += (int)$correos_order['bultos'];
            $client_pages[$key]['total_page']['total_weight_page'] += floatval($client_pages[$key]['orders'][$order['id_order']]['total_weight']);
            $client_pages[$key]['total_page']['total_insurance_page'] += floatval($correos_order['added_values_insurance_value']);
            $client_pages[$key]['total_page']['total_cash_on_delivery_value_page'] += floatval($correos_order['added_values_cash_on_delivery_value']);
        }

        // cambiamos el nombre al nombre de la tienda en caso de que haya más de 1 sender
        if ($name_store == 1) {
            foreach ($client_pages as $key => $page) {
                $client_pages[$key]['name'] = Configuration::get('PS_SHOP_NAME');
                $client_pages[$key]['client_code'] = '';
            }
        }

        $pdf = new PDFMerger();
        $tempFolder = get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/pdftmp";
        $pdfOutputFile = $tempFolder . "/" . uniqid('manifest_') . '.pdf';

        $pdf_generated = $pdf->createManifest($pdfOutputFile, $client_pages);

        // si se ha generado correctamente el pdf actualizamos la manifest_date para cada pedido seleccionado
        if ($pdf_generated instanceof PDF_MC_Table) {
            foreach ($orders as $order) {
                CorreosOficialOrders::updateOrderManifestDate($order['id_order']);
            }
        }

        $pdf_generated->Output('F', $pdfOutputFile);

        die($pdfOutputFile);

    }

    private function addExtraPackagesInfo($expeditions)
    {
        $i = 0;
        foreach ($expeditions as $expedition => $row) {
            if ($row['shipping_number'] != "") {
                if ($row['bultos'] > 1) {
                    $expeditions[$i]['first_shipping_number'] = $expeditions[$i]['first_shipping_number'] . " (" . $row['bultos'] . ")";
                }
            }
            $i++;
        }
        return $expeditions;
    }

    public function changeOrderStatus($id_order) {
		if($this->utilities_dao->readSettings('ShowShippingStatusProcess')->value == 'on') {
			$config_status = $this->utilities_dao->readSettings('ShipmentPreregistered');
			CorreosOficialUtils::changeOrderStatus($id_order, $config_status->value);
		}
	}

    private function getDimensionsByType($available_carrier_d, $activateDimensionsByDefault) {
		return [
			'large' => ($available_carrier_d == 1 && $activateDimensionsByDefault == true) ?
			(int)CorreosOficialConfigDao::getConfigValue('DimensionsByDefaultLarge') : 0,
			'height' => ($available_carrier_d == 1 && $activateDimensionsByDefault == true) ?
			(int)CorreosOficialConfigDao::getConfigValue('DimensionsByDefaultHeight') : 0,
			'width' => ($available_carrier_d == 1 && $activateDimensionsByDefault == true) ?
			(int)CorreosOficialConfigDao::getConfigValue('DimensionsByDefaultWidth') : 0,
		];
	}

}


