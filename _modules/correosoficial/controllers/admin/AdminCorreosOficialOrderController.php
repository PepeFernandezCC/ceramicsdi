<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/../../classes/CorreosOficialSenders.php';
require_once dirname(__FILE__) . '/../../classes/CorreosOficialReturnsMail.php';
require_once dirname(__FILE__) . '/../../classes/CorreosOficialOrders.php';
require_once dirname(__FILE__) . '/../../classes/CorreosOficialOrder.php';
require_once dirname(__FILE__) . '/../../classes/CorreosOficialShippingNumber.php';
require_once dirname(__FILE__) . '/../../classes/CorreosOficialCheckout.php';

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Cex/CexRest.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Correos/CorreosSoap.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Correos/CorreosRest.php';

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialUtilitiesDao.php';

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__) . '/../../vendor/pdfmerger.php';

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Commons/NeedCustoms.php';

class AdminCorreosOficialOrderController extends ModuleAdminController
{
    public $module;
    public $context;
    public $db;
    public $correos_soap;
    public $correos_rest;
    public $cex_rest;
    public $horaActual;
    public $statusProcessActive;
    public $utilities_dao;
    public $order;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->module = Module::getInstanceByName('correosoficial');
        $this->utilities_dao = new CorreosOficialUtilitiesDao();
        $this->correos_soap = new CorreosSoap();
        $this->correos_rest = new CorreosRest();
        $this->cex_rest = new CexRest();
        $this->horaActual = date('Y-m-d H:i:s', time());
        $id_order = Normalization::normalizeData('id_order');
        $this->order = new Order($id_order);

        $this->statusProcessActive = $this->utilities_dao->readSettings('ShowShippingStatusProcess', false, $this->order->id_shop)->value;

        switch (Tools::getValue('action')) {
            case 'getSenderById':
                $sender_id = Normalization::normalizeData('sender_id');
                $sender = CorreosOficialSenders::getSendersWithCodesById($sender_id);
                die(json_encode($sender));
                break;
            case 'getOrderStatus':
                $order_status = $this->getOrderStatus($id_order);
                die(json_encode($order_status));
                break;
            case 'printLabel':
                $expNumber = Normalization::normalizeData('exp_number');
                $labelType = Normalization::normalizeData('selectedTipoEtiquetaReimpresion');
                $labelFormat = Normalization::normalizeData('selectedFormatEtiquetaReimpresion');
                $labelPosition = Normalization::normalizeData('selectedPosicionEtiquetaReimpresion');
                $company = Normalization::normalizeData('company');
                $correos_oficial_order = new CorreosOficialOrder($id_order);
                $shipping_number = $correos_oficial_order->getShippingNumbersByIdOrderForSavedOrder();
                $this->getEtiquetasByExpNumber(false,$company, 'order', $shipping_number, $expNumber, $labelType, $labelPosition, $labelFormat);
                break;
            case 'printLabelReturn':
                $selectedTipoEtiquetaReimpresionReturn = Normalization::normalizeData('selectedTipoEtiquetaReimpresionReturn');
                $selectedPosicionEtiquetaReimpresionReturn = Normalization::normalizeData('selectedPosicionEtiquetaReimpresionReturn');
                $company = Normalization::normalizeData('company');
                $correos_oficial_order = new CorreosOficialOrder($id_order);
                $expNumber = $correos_oficial_order->getExpeditionNumberByIdOrderForReturn();
                $shipping_number = $correos_oficial_order->getShippingNumbersByIdOrderForReturns();
                $labels = $this->getEtiquetasByExpNumber(false,$company, 'return', $shipping_number, $expNumber, $selectedTipoEtiquetaReimpresionReturn, $selectedPosicionEtiquetaReimpresionReturn);
                die(json_encode($labels));
                break;
            case 'getCustomsDoc':
                $type = Normalization::normalizeData('type', 'no_uppercase');
                $exp_number = Normalization::normalizeData('exp_number');
                $customer_country = Normalization::normalizeData('customer_country');
                $customer_name = Normalization::normalizeData('customer_name');
                $optionButton = Tools::getValue('optionButton');

                if ($type == 'order') {
                    $destination_country = Normalization::normalizeData('customer_country');
                    $destination_name = Normalization::normalizeData('customer_name') . " " . Normalization::normalizeData('customer_lastname');
                } elseif ($type == 'return') {
                    $destination_country = Normalization::normalizeData('sender_country');
                    $destination_name = Normalization::normalizeData('sender_name');
                } else {
                    throw new Exception("ERROR 19010: El tipo debe ser order o return");
                }

                switch ($optionButton) {
                    case 'ImprimirCN23Button':
                    case 'ImprimirCN23Button2':
                        $optionButton = 'ImprimirCN23Button';
                        break;
                    case 'ImprimirDUAButton':
                    case 'ImprimirDUAButton2':
                        $optionButton = 'ImprimirDUAButton';
                        break;
                    case 'ImprimirDDPButton':
                    case 'ImprimirDDPButton2':
                        $optionButton = 'ImprimirDDPButton';
                        break;
                }
                $this->getDocAduanera($type, $exp_number, $optionButton, $destination_country, $destination_name);
                break;
            case 'deleteFiles':
                $result = CorreosOficialUtils::deleteFiles();
                die(json_encode($result));
                break;
            case 'generateOrder':
                $selectedTipoEtiquetaReimpresion = Normalization::normalizeData('selectedTipoEtiquetaReimpresion');
                $result = $this->generateOrder($selectedTipoEtiquetaReimpresion);
                die(json_encode($result));
                break;
            case 'cancelOrder':
                $result_cancel_order = $this->cancelOrder('order');
                die(json_encode($result_cancel_order));
                break;
            case 'cancelReturn':
                $result_cancel_return = $this->cancelOrder('return');
                die(json_encode($result_cancel_return));
                break;
            case 'generatePickup':
                $result_pickup = $this->generatePickup();
                die(json_encode($result_pickup));
                break;
            case 'cancelPickup':
                $result_cancel_pickup = $this->cancelPickup();
                die(json_encode($result_cancel_pickup));
                break;
            case 'generateReturn':
                $selectedPosicionEtiquetaReimpresionReturn = Normalization::normalizeData('selectedPosicionEtiquetaReimpresionReturn');
                $result_generate_return = $this->generateReturn($selectedPosicionEtiquetaReimpresionReturn);
                die(json_encode($result_generate_return));
                break;
            case 'sendEmail':
                $result_send_email = $this->sendEmail();
                die(json_encode($result_send_email));
                break;
            case 'RequireCustom':
                $cp_source = Normalization::normalizeData('cp_source');
                $cp_dest = Normalization::normalizeData('cp_dest');
                $country_source = Normalization::normalizeData('country_source');
                $country_dest = Normalization::normalizeData('country_dest');
                $result['require_custom'] = NeedCustoms::isCustomsRequired($cp_source, $cp_dest, $country_source, $country_dest);
                die(json_encode($result));
                break;                
            default:
                throw exception ('ERROR 19050: No se ha indicado un action');
        }
    }

    public function generateReturn($selectedTipoEtiquetaReimpresion)
    {
        $order_id = Normalization::normalizeData('id_order');
        $company = Normalization::normalizeData('company');
        $order_form = Normalization::normalizeData('order_form');
        $id_sender = Normalization::normalizeData('id_sender');
        $needPickup = Normalization::normalizeData('needPickup');
		$devolutionSucceded = false;

        $bultos = $order_form['correos-num-parcels-return'];

        if ($company == 'Correos') {
            $customs_desc_array = self::getCustomsDesc($bultos);
        } else {
            $customs_desc_array = array();
        }

        $reference = $order_form['order_reference'];

        $client = $this->utilities_dao->getDataClient($company, false, $id_sender);

        $result_done = array();
        $result_errors = array();

        switch ($company) {
            // DEVOLUCIÓN CORREOS
            case 'Correos':

                $this->utilities_dao->deleteReturns($order_id);

                for ($i = 1; $i <= $bultos; $i++) {

                    $shipping_return_data = array(
                        'id_order' => $order_id,
                        'company' => $company,
                        'bulto' => $i,
                        'order_form' => $order_form,
                        'client' => $client,
                        'customs_desc_array' => $customs_desc_array,
                        'source_channel' => 'PRS',
                        'needPickup' => $needPickup,
                        'order_reference' => Normalization::normalizeData('order_reference'),
                        'pickup_date' => Normalization::normalizeData('pickup_date'),
                        'sender_from_time' => Normalization::normalizeData('sender_from_time'),
                        'sender_to_time' => Normalization::normalizeData('sender_to_time'),
                    );

                    $result_correos = $this->correos_soap->registrarDevolucion($shipping_return_data, $id_sender);

                    if ($result_correos['codigoRetorno'] == '0') {

                        $CodExpedicion = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->CodExpedicion;
                        $CodEnvio = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto->CodEnvio;

                        $return_done = array(
                            'id_order' => $order_id,
                            'shipping_number' => $CodEnvio,
                            'exp_number' => $CodExpedicion
                        );

                        $return_update = array(
                            'id_order' => $order_id,
                            'id_sender' => $id_sender,
                            'reference' => $reference,
                            'shipping_number' => $CodExpedicion,
                            'carrier_type' => 'Correos',
                            'date_add' => $this->horaActual,
                            'id_carrier' => '0',
                            'id_product' => '0',
                            'bultos' => $i,
                            'AT_code' => '',
                            'last_status' => "Prerregistrado",
                            'status' => "Grabado",
                            'updated_at' => $this->horaActual,
                            'pickup' => 0,
                            'pickup_status' => "None",
                            'require_customs_doc' => 0
                        );

                        $resultado = array(
                            'codigoRetorno' => utf8_decode($result_correos['codigoRetorno']),
                            'exp_number' => utf8_decode($CodExpedicion),
                            'shipping_number' => utf8_decode($CodEnvio),
                            'num_bulto' => $i,
                            'changeStatus' => $this->getStatus('ShipmentReturned')
                        );

                        $result_done[] = $resultado;

                        $this->utilities_dao->insertDataOrder('correos_oficial_saved_returns', $return_done);
                        $this->utilities_dao->insertReturn($return_update);
                        $devolutionSucceded = true;

                    } else {
                        $mensaje_retorno = utf8_decode($result_correos['mensajeRetorno']);

                        if ($result_correos['status_code'] == 200) {
                            $resultado = array(
                                'codigoRetorno' => $result_correos['codigoRetorno'][0],
                                'mensajeRetorno' => $mensaje_retorno,
                                'num_bulto' => $i
                            );
                            $result_errors[] = $resultado;
                        } elseif ($result_correos['status_code'] != 200) {

                            if ($result_correos['status_code'] == 0) {
                                $mensaje_retorno = CorreosOficialErrorManager::checkStateConnection($status_code);
                            }

                            $resultado = array(
                                'codigoRetorno' => $result_correos['codigoRetorno'][0],
                                'mensajeRetorno' => $mensaje_retorno,
                                'num_bulto' => $i
                            );
                            $result_errors[] = $resultado;
                        }
                    }

                }

                break;
            // PREREGISTRO CEX
            case 'CEX':

                $this->utilities_dao->deleteReturns($order_id);

                $shipping_return_data = array(
                    'id_order' => $order_id,
                    'company' => $company,
                    'bultos' => $bultos,
                    'order_form' => $order_form,
                    'client' => $client,
                    'needPickup' => $needPickup,
                    'order_reference' => Normalization::normalizeData('order_reference'),
                    'pickup_date' => Normalization::normalizeData('pickup_date'),
                    'sender_from_time' => Normalization::normalizeData('sender_from_time'),
                    'sender_to_time' => Normalization::normalizeData('sender_to_time'),
                );

                $result_cex = $this->cex_rest->registrarDevolucion($shipping_return_data, $id_sender);
                $result_cex_decoded = json_decode($result_cex['json_retorno'], true);

                if ($result_cex_decoded['codigoRetorno'] == '0') {
                    $bultos_reg_cex = $result_cex_decoded['listaBultos'];
                    $num_bultos_reg = count($result_cex_decoded['listaBultos']);
                    $CodExpedicion = $result_cex_decoded['datosResultado'];

                    foreach ($bultos_reg_cex as $bulto => $field) {
                        $CodEnvio = str_replace("'", "", $field['codUnico']);
                        $orden = intval($field['orden']);

                        $return_done = array(
                            'id_order' => $order_id,
                            'shipping_number' => $CodEnvio,
                            'exp_number' => $CodExpedicion
                        );

                        $resultado = array(
                            'codigoRetorno' => utf8_decode($result_cex_decoded['codigoRetorno']),
                            'exp_number' => utf8_decode($CodExpedicion),
                            'shipping_number' => utf8_decode($CodEnvio),
                            'num_bulto' => $orden
                        );
                        $result_done[] = $resultado;

                        $this->utilities_dao->insertDataOrder('correos_oficial_saved_returns', $return_done);
                    }

                    $return_update = array(
                        'id_order' => $order_id,
                        'id_sender' => $id_sender,
                        'reference' => $reference,
                        'shipping_number' => $CodExpedicion,
                        'carrier_type' => 'CEX',
                        'date_add' => $this->horaActual,
                        'id_carrier' => '0',
                        'id_product' => '0',
                        'bultos' => $num_bultos_reg,
                        'AT_code' => $order_form['code_at'] ? $order_form['code_at'] : '',
                        'last_status' => "Prerregistrado",
                        'status' => "Grabado",
                        'updated_at' => $this->horaActual,
                        'pickup' => 0,
                        'pickup_status' => "None",
                        'require_customs_doc' => 0
                    );

                    $this->utilities_dao->insertReturn($return_update);

                    // Para CEX pasamos a guardar la recogida ya que se hace en la misma llamada al WS
                    $aux_date = date_create_from_format('dmY', $result_cex_decoded["fechaRecogida"]);
                    $pickup_date_format = date_format($aux_date, 'Y-m-d');
                      
                    $pickup_order_data = array(
                        'id_order' => $order_id,
                        'pickup_number' => $result_cex_decoded['numRecogida'],
                        'pickup_date' => $pickup_date_format,
                        'pickup_from_hour' => $result_cex_decoded["horaRecogidaDesde"],
                        'pickup_to_hour' => $result_cex_decoded["horaRecogidaHasta"],
                        'package_size' => 0,
                        'print_label' => "N",
                        'pickup_status' => "Grabado"
                    );

                    $this->utilities_dao->saveReturnPickup($pickup_order_data);

                    $devolutionSucceded = true;
                    CorreosOficialUtils::deleteFiles();
                } else {
                    if ($result_cex['status_code'] == 200) {
                        $resultado = array(
                            'codigoRetorno' => $result_cex_decoded['codigoRetorno'],
                            'mensajeRetorno' => $result_cex_decoded['mensajeRetorno'],
                            'changeStatus' => $this->getStatus('ShipmentReturned')
                        );
                        $result_errors[] = $resultado;
                    } elseif ($result_cex_decoded['status_code'] != 200) {
                        if (isset($result_cex_decoded['status_code']) && $result_cex_decoded['status_code'] == 0) {
                            $result_cex_decoded['mensajeRetorno'] = CorreosOficialErrorManager::checkStateConnection($result_cex_decoded['status_code']);
                        }

                        $resultado = array(
                            'codigoRetorno' => $result_cex_decoded['codigoRetorno'],
                            'mensajeRetorno' => $result_cex_decoded['mensajeRetorno'],
                            'bultos' => 1
                        );
                        $result_errors[] = $resultado;
                    }
                }

                break;
        }

		if($devolutionSucceded && $this->utilities_dao->readSettings('ShowShippingStatusProcess', false, $this->order->id_shop)->value == 'on') {
			$config_status = $this->utilities_dao->readSettings('ShipmentReturned', false, $this->order->id_shop);
			CorreosOficialUtils::changeOrderStatus($order_id, $config_status->value);
		}

        return array(
            'aciertos' => $result_done,
            'errores' => $result_errors
        );
    }

    public function generateOrder($selectedTipoEtiquetaReimpresion)
    {
        $order_id = Normalization::normalizeData('id_order');
        $company = Normalization::normalizeData('company');
        $delivery_mode = Normalization::normalizeData('delivery_mode', 'no_uppercase');
        $id_carrier = Normalization::normalizeData('id_carrier');
        $id_product = Normalization::normalizeData('id_product');
        $order_form = Normalization::normalizeData('order_form');
        $needPickup = Normalization::normalizeData('needPickup');
        $pickupDateRegister = Normalization::normalizeData('pickupDateRegister');
        $pickupFromRegister = Normalization::normalizeData('pickupFromRegister');
        $pickupToRegister = Normalization::normalizeData('pickupToRegister');
        $needPrintLablPickup = Normalization::normalizeData('needPrintLablPickup');
        $packetSize = Normalization::normalizeData('packetSize');
        $id_sender = Normalization::normalizeData('id_sender');

        $order_ps = new Order((int)$order_id);
        
        $correos_order = new CorreosOficialOrders($order_id);

        $id_zone = $order_form['id_zone'];

        $bultos = $order_form['correos-num-parcels'];

        $shippingSucceed = false;

        $added_values_cash_on_delivery = Normalization::normalizeData('added_values_cash_on_delivery');
		$added_values_insurance = Normalization::normalizeData('added_values_insurance');
		$added_values_partial_delivery = Normalization::normalizeData('added_values_partial_delivery');
		$added_values_delivery_saturday = Normalization::normalizeData('added_values_delivery_saturday');
		$added_values_cash_on_delivery_iban = Normalization::normalizeData('added_values_cash_on_delivery_iban');
		$added_values_cash_on_delivery_value = Normalization::normalizeData('added_values_cash_on_delivery_value');
		$added_values_insurance_value = Normalization::normalizeData('added_values_insurance_value');

        $added_values = [
			'added_values_cash_on_delivery' => $added_values_cash_on_delivery == 'true' ? 1 : 0,
			'added_values_insurance' => $added_values_insurance == 'true' ? 1 : 0,
			'added_values_partial_delivery' => $added_values_partial_delivery == 'true' ? 1 : 0,
			'added_values_delivery_saturday' => $added_values_delivery_saturday == 'true' ? 1 : 0,
			'added_values_cash_on_delivery_iban' => $added_values_cash_on_delivery == 'true' ? $added_values_cash_on_delivery_iban : null,
			'added_values_cash_on_delivery_value' => $added_values_cash_on_delivery == 'true' ? floatval($added_values_cash_on_delivery_value) : 0,
			'added_values_insurance_value' => $added_values_insurance == 'true' ? floatval($added_values_insurance_value) : 0,
		];

        // get información de los bultos
		$info_bultos = json_decode(stripslashes($_REQUEST["info_bultos"]), true);
		$all_packages_equal = Tools::getValue('all_packages_equal');

        if ($company == 'Correos') {
            $customs_desc_array = self::getCustomsDesc($bultos);
        } else {
            $customs_desc_array = array();
        }

        $reference = $order_form['order_reference'];

        $client = $this->utilities_dao->getDataClient($company, false, $id_sender);

        // Si no tiene contrato, no se puede generar el envío
        if($client[0]['id'] == null){
            return array(
                'codigoRetorno' => '',
                'mensajeRetorno' => $this->l('No valid sender selected'),
                'bultos' => '');
        }

        $shipping_data = array(
            'id_order' => $order_id,
            'company' => $company,
            'bultos' => $bultos,
            'delivery_mode' => $delivery_mode,
            'order_form' => $order_form,
            'client' => $client,
            'customs_desc_array' => $customs_desc_array,
            'source_channel' => 'PRS',
            'needPickup' => $needPickup,
            'pickupDateRegister' => $pickupDateRegister,
            'pickupFromRegister' => $pickupFromRegister,
            'pickupToRegister' => $pickupToRegister,
            'needPrintLablPickup' => $needPrintLablPickup,
            'packetSize' => $packetSize,
        );

        $sender_postal_code = $order_form['sender_cp'];
        $customer_postal_code = $order_form['customer_cp'];
        $sender_country = $order_form['sender_country'];
        $customer_country = $order_form['customer_country'];

        $require_customs_doc = NeedCustoms::isCustomsRequired($sender_postal_code, $customer_postal_code, $sender_country, $customer_country);

        switch ($company) {
            // PREREGISTRO CORREOS
            case 'Correos':
                $result_correos = $this->correos_soap->registrarEnvio($shipping_data, null, $id_sender);
                // Comprobamos si tenemos que registrar recogida
                $updatePickup = false;
                $pickupError = false;

                if ($bultos > 1) {
                    $bultosDatas = [];
                    foreach ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->Bultos->bulto as $bultoInside) {
                        $bultosDatas[] = $bultoInside->CodEnvio;
                    }
                } else {
                    // Esto da un error
                    //$bultosDatas = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto->CodEnvio;
                    //$shipping_numbers[ ] = $bultosDatas[0];

                    // Recibimos cosas diferentes en $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio
                    // así que hay que tenerlo en cuenta. Por eso estaba dando un error.
                    // Si no hay error...
                    if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto) {
                        $codigoEnvio = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto->CodEnvio;
                        $shipping_numbers[ ] = $codigoEnvio[0];
                    }
                    // Si hay error...
                    if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->BultoError) {
                        // no hacemos nada, por ahora
                        // ya se está mostrando bien el error, pero dejamos esta condición para saber que
                        // BultoError trae la información correspondiente al error que se ha producido.
                    }
                }

                
                // FIX
                // $result_correos['codigoRetorno']  devuelve un SimpleXMLElement que puede ser:
                //      0 : "0"  si no hay errores
                //      0 : "1"  si hay algún error
                // La comparación $result_correos['codigoRetorno'] == '0'   es erronea
                //      antes hay que convertir el SimpleXMLElement en un string

                //if ($result_correos['codigoRetorno'] == '0') {
                $result_correos_string = (string) $result_correos['codigoRetorno'];
                
                if ($needPickup === 'S' && $result_correos_string == "0") {
                    $pickup_details_array = array(
                        'id_order' => $order_id,
                        'bultos' => $bultos,
                        'order_reference' => $reference,
                        'pickup_date' => $pickupDateRegister,
                        'sender_from_time' => $pickupFromRegister,
                        'sender_to_time' =>$pickupFromRegister,
                        'sender_address' => $order_form['sender_address'],
                        'sender_city' => $order_form['sender_city'],
                        'sender_cp' => $order_form['sender_cp'],
                        'sender_name' => $order_form['sender_name'],
                        'sender_contact' => $order_form['sender_contact'],
                        'sender_phone' => $order_form['sender_phone'],
                        'sender_email' => $order_form['sender_email'],
                        'sender_nif_cif' => $order_form['sender_nif_cif'],
                        'sender_country' => $order_form['sender_country'],
                        'producto' => $id_product,
                        'print_label' => $needPrintLablPickup,
                        'package_type' => $packetSize,
                        'shipping_numbers' => CorreosOficialUtils::transformArrayForPickups($shipping_numbers),
                        'client' => $client
                    );

                    $result_correos_pickups = $this->correos_soap->registrarRecogida($pickup_details_array, $id_sender);
                    if ($result_correos_pickups['codigoRetorno'] == '0') {

                        $pickup_number = $result_correos_pickups['codSolicitud'];

                        $updatePickup = array(
                            'id_order' => $order_id,
                            'pickup_number' => utf8_decode($pickup_number),
                            'pickup_date' => $pickupDateRegister,
                            'pickup_from_hour' => $pickupFromRegister,
                            'pickup_to_hour' => $pickupToRegister,
                            'package_size' => intval($packetSize),
                            'print_label' => $needPrintLablPickup,
                            'pickup_status' => "Grabado",
                            'pickup' => 1,
                        );
                    } 
                }

                if ($result_correos_string == "0") {
                    $id_carrier = $correos_order->getIdCarrier($order_id, $id_product);

                    // Bulto único
                    if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->TotalBultos == 1) {
                        $CodExpedicion = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->CodExpedicion;
                        $CodEnvio = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->Bulto->CodEnvio;
                    
                        $order_done = array(
                            'id_order' => $order_id,
                            'shipping_number' => $CodEnvio,
                            'exp_number' => $CodExpedicion,
                        );

                        $order_update = array(
                            'id_order' => $order_id,
                            'id_sender' => $order_form['senderSelect'],
                            'reference' => $reference,
                            'shipping_number' => $CodExpedicion,
                            'carrier_type' => 'Correos',
                            'date_add' => $this->horaActual,
                            'id_carrier' => $id_carrier,
                            'id_product' => $id_product,
                            'bultos' => 1,
                            'AT_code' => '',
                            'last_status' => "Prerregistrado",
                            'status' => "Grabado",
                            'updated_at' => $this->horaActual,
                            'pickup' => 0,
                            'pickup_status' => "None",
                            'require_customs_doc' => $require_customs_doc == true ? 1 : 0
                        );

                        $bultos_reg[] = array(
                            'package_number' => 1,
                            'shipping_number' => utf8_decode($CodEnvio)
                        );

                        $codRet = utf8_decode($result_correos['codigoRetorno']);
                        $textRet = '';
                        if ($needPickup == 'S' && $result_correos_pickups['codigoRetorno'] != 0) {
                            $textRet .= $this->l("Se genero el envio correctamente aunque la recogida ") . $result_correos_pickups['mensajeRetorno'];
                            $codRet = 1111;
                        }

                        $result = array(
                            'codigoRetorno' => $codRet,
                            'num_bultos_reg' => 1,
                            'bultos_reg' => $bultos_reg,
                            'exp_number' => utf8_decode($CodExpedicion),
                            'mensajeRetorno' => $textRet,
                            'changeStatus' => $this->getStatus('ShipmentPreregistered')
                        );

                        // Actualiza el tracking_number de Resumen transportista en el pedido
                        CorreosOficialShippingNumber::updateShipingNumberInOrder($order_id, utf8_decode($CodEnvio));
                        
                        /* merge info bultos - Bulto único Correos */
						$order_done = array_merge($order_done, $info_bultos[1]);
                        $this->utilities_dao->insertDataOrder('correos_oficial_saved_orders', $order_done);

                        // Insertamos los datos de Oficina o Citypaq Monobulto
                        $this->insertRequestData($order_id);

                        // merge added_values para insertarlo en la db
						$order_update = array_merge($order_update, $added_values);
                        $this->utilities_dao->insertOrder($order_update);
                        $shippingSucceed = true;


                        if ($updatePickup) {
                            $query = Db::getInstance()->update('correos_oficial_orders', $updatePickup, 'id_order = ' . (int) $order_id);
                        }
                        // Multibulto
                    } else {
                        if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->TotalBultos > 1) {
                            $bultos_reg_correos = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->Bultos;
                            $num_bultos_reg = intval($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->TotalBultos);
                            $CodExpedicion = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->CodExpedicion;

                            foreach ($bultos_reg_correos->Bulto as $bulto => $field) {
                                $CodEnvio = $field->CodEnvio;
                                $NumBulto = $field->NumBulto;

                                $order_done = array(
                                    'id_order' => $order_id,
                                    'shipping_number' => $CodEnvio,
                                    'exp_number' => $CodExpedicion
                                );

                                $bultos_reg[] = array(
                                    'package_number' => utf8_decode($NumBulto),
                                    'shipping_number' => utf8_decode($CodEnvio)
                                );

                                // comprobamos que si se ha marcado que todos los bultos sean igual recoge la info del primer bulto
                                $info_bulto = ($all_packages_equal == true) ? $info_bultos[1] : $info_bultos[(int)$NumBulto];

                                /* merge info bultos -  multibulto Correos */
								$order_done = array_merge($order_done, $info_bulto);
                                $this->utilities_dao->insertDataOrder('correos_oficial_saved_orders', $order_done);

                                // Insertamos los datos de Oficina o Citypaq Multibulto
                                $this->insertRequestData($order_id);
                            }

                            $order_update = array(
                                'id_order' => $order_id,
                                'id_sender' => $order_form['senderSelect'],
                                'reference' => $reference,
                                'shipping_number' => $CodExpedicion,
                                'carrier_type' => 'Correos',
                                'date_add' => $this->horaActual,
                                'id_carrier' => $id_carrier,
                                'id_product' => $id_product,
                                'bultos' => $num_bultos_reg,
                                'AT_code' => '',
                                'last_status' => "Prerregistrado",
                                'status' => "Grabado",
                                'updated_at' => $this->horaActual,
                                'pickup' => 0,
                                'pickup_status' => "None",
                                'require_customs_doc' => $require_customs_doc == true ? 1 : 0
                            );

                            $codRet = utf8_decode($result_correos['codigoRetorno']);
                            $textRet = '';
                            if ($needPickup == 'S' && $result_correos_pickups['codigoRetorno'] != 0) {
                                $textRet .= $this->l("Se genero el envio correctamente aunque la recogida ") . $result_correos_pickups['mensajeRetorno'];
                                $codRet = 1111;
                            }

                            $result = array(
                                'codigoRetorno' => $codRet,
                                'num_bultos_reg' => $num_bultos_reg,
                                'bultos_reg' => $bultos_reg,
                                'exp_number' => utf8_decode($CodExpedicion),
                                'mensajeRetorno' => $textRet,
                                'changeStatus' => $this->getStatus('ShipmentPreregistered')
                            );

                            // Actualiza el tracking_number de Resumen transportista en el pedido
                            CorreosOficialShippingNumber::updateShipingNumberInOrder($order_id, utf8_decode($CodEnvio));
                            
                            // merge added_values para insertarlo en la db
                            $order_update = array_merge($order_update, $added_values);
                            $this->utilities_dao->insertOrder($order_update);
                            $shippingSucceed = true;
                            if ($updatePickup) {
                                $query = Db::getInstance()->update('correos_oficial_orders', $updatePickup, 'id_order = ' . (int) $order_id);
                            }
                        }
                    }
                } else {
                    $mensaje_retorno = '';
                    if ($bultos == 1) {
                        $mensaje_retorno = utf8_decode($result_correos['mensajeRetorno']);
                    } else {
                        $bultos_error = $result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvioMultibulto->BultosError;
                        for ($i = 0; $i < $bultos; $i++) {
                            if (isset($bultos_error[0]->BultoError[$i]->NumBulto)) {
                                $mensaje_retorno = $mensaje_retorno . 'Error bulto ' . $bultos_error[0]->BultoError[$i]->NumBulto . ': ' . $bultos_error[0]->BultoError[$i]->DescError . '</br>';
                            }
                        }
                    }

                    if ($result_correos['status_code'] != 200) {

                        if ($result_correos['status_code'] == 0) {
                            $mensaje_retorno = CorreosOficialErrorManager::checkStateConnection($status_code);
                        }

                        $result = array(
                            'codigoRetorno' => '',
                            'mensajeRetorno' => 'ERROR 18004: ' . $mensaje_retorno,
                            'bultos' => '');
                    } else {
                        // Traducción de mensaje del webservice de Correos
                        if ($result_correos['xml_retorno']->soapenvBody->RespuestaPreregistroEnvio->BultoError->Error == 159) {
                            $mensaje_retorno = $this->l('The dimensions of the packages must be indicated');
                        }
                        $result = array(
                            //'codigoRetorno' => $result_correos['codigoRetorno'][0],
                            'codigoRetorno' => (string) $result_correos['codigoRetorno'],
                            'mensajeRetorno' => $mensaje_retorno,
                            'bultos' => $bultos);
                    }

                }
                break;
            // PREREGISTRO CEX
            case 'CEX':
                $shipping_data['ChangeLogoOnLabel'] = CorreosOficialConfigDao::getConfigValue('ChangeLogoOnLabel', $order_ps->id_shop);
                $shipping_data['UploadLogoLabels'] = CorreosOficialConfigDao::getConfigValue('UploadLogoLabels', $order_ps->id_shop);

                $result_cex = $this->cex_rest->registrarEnvio($shipping_data, $id_sender);
                $result_cex_decoded = json_decode($result_cex['json_retorno'], true);


                if ($result_cex_decoded['codigoRetorno'] == '0') {
                    $bultos_reg_cex = $result_cex_decoded['listaBultos'];
                    $num_bultos_reg = count($result_cex_decoded['listaBultos']);
                    $CodExpedicion = $result_cex_decoded['datosResultado'];

                    foreach ($bultos_reg_cex as $bulto => $field) {
                        $CodEnvio = str_replace("'", "", $field['codUnico']);
                        $orden = intval($field['orden']);

                        $order_done = array(
                            'id_order' => $order_id,
                            'shipping_number' => $CodEnvio,
                            'exp_number' => $CodExpedicion
                        );

                        $bultos_reg[] = array(
                            'package_number' => $orden,
                            'shipping_number' => $CodEnvio
                        );
                        
                        // comprobamos que si se ha marcado que todos los bultos sean igual recoge la info del primer bulto
                        $info_bulto = ($all_packages_equal == true) ? $info_bultos[1] : $info_bultos[(int)$orden];

                        /* merge info bultos - multibulto CEX */
						$order_done = array_merge($order_done, $info_bulto);
                        $this->utilities_dao->insertDataOrder('correos_oficial_saved_orders', $order_done);
                        $this->insertRequestData($order_id);
                    }

                  /**
                     * Aplica cuando es un transportista externo
                     */
                    if (empty($id_carrier)) {
                        $carrier_order = CorreosOficialCarrier::getCarrierByProductId($id_product, $id_zone);
                        $id_carrier = $carrier_order['id_carrier'];
                    }

                    $order_update = array(
                        'id_order' => $order_id,
                        'id_sender' => $order_form['senderSelect'],
                        'reference' => $reference,
                        'shipping_number' => $CodExpedicion,
                        'carrier_type' => 'CEX',
                        'date_add' => $this->horaActual,
                        'id_carrier' => $id_carrier,
                        'id_product' => $id_product,
                        'bultos' => $num_bultos_reg,
                        'AT_code' => $order_form['code_at'],
                        'last_status' => "Prerregistrado",
                        'status' => "Grabado",
                        'updated_at' => $this->horaActual,
                        'pickup' => 0,
                        'pickup_status' => "None",
                        'require_customs_doc' => 0
                    );

                    $result = array(
                        'codigoRetorno' => utf8_decode($result_cex_decoded['codigoRetorno']),
                        'num_bultos_reg' => $num_bultos_reg,
                        'bultos_reg' => $bultos_reg,
                        'exp_number' => utf8_decode($CodExpedicion),
                        'changeStatus' => $this->getStatus('ShipmentPreregistered')
                    );

                    // Actualiza el tracking_number de Resumen transportista en el pedido
                    CorreosOficialShippingNumber::updateShipingNumberInOrder($order_id, utf8_decode($CodEnvio));
                    
                    // merge added_values para insertarlo en la db
                    $order_update = array_merge($order_update, $added_values);
                    $this->utilities_dao->insertOrder($order_update);
					$shippingSucceed = true;

                    $pickupdateDay = substr($result_cex_decoded['fechaRecogida'],0,2);
                    $pickupdateMonth = substr($result_cex_decoded['fechaRecogida'],2,2);
                    $pickupdateYear = substr($result_cex_decoded['fechaRecogida'],4,4);

                    $pickupdateValidFormat = $pickupdateYear.'-'.$pickupdateMonth.'-'.$pickupdateDay;

                    if (!empty($result_cex_decoded['numRecogida'])) {
                        $update = [
                            'pickup' => 1,
                            'pickup_number' => $result_cex_decoded['numRecogida'],
                            'pickup_date' => $pickupdateValidFormat,
                            'pickup_from_hour' => $result_cex_decoded['horaRecogidaDesde'],
                            'pickup_to_hour' => $result_cex_decoded['horaRecogidaHasta'],
                            'package_size' => $packetSize,
                            'print_label' => $needPrintLablPickup,
                            'pickup_status' => 'Grabado',
                        ];
                        $query = Db::getInstance()->update('correos_oficial_orders', $update, 'id_order = ' . (int) $order_id);
                    }


                    CorreosOficialUtils::deleteFiles();
                } else {

                    if ($result_cex['status_code'] != 200) {

                        if ($result_cex['status_code'] == 0) {
                            $mensaje_retorno = CorreosOficialErrorManager::checkStateConnection($status_code);
                        }

                        $result = array(
                            'codigoRetorno' => '',
                            'mensajeRetorno' => 'ERROR 18005: ' . $mensaje_retorno,
                            'bultos' => ''
                        );
                    } else {
                        $result = array(
                            'codigoRetorno' => $result_cex_decoded['codigoRetorno'],
                            'mensajeRetorno' => $result_cex_decoded['mensajeRetorno'],
                            'bultos' => 1
                        );
                    }
                }

                break;
        }

        if($shippingSucceed && $this->utilities_dao->readSettings('ShowShippingStatusProcess', false, $this->order->id_shop)->value == 'on') {
			$config_status = $this->utilities_dao->readSettings('ShipmentPreregistered', false, $this->order->id_shop);
			CorreosOficialUtils::changeOrderStatus($order_id, $config_status->value);
		}

        return $result;
    }

    /** FUNCIÓN PARA CANCELAR PEDIDOS Y DEVOLUCIONES
     * params: $type -> tipo 'order' cancela pedido
     *               -> tipo 'return' cancela devolución
     */
    public function cancelOrder($type)
    {
        $order_id = Normalization::normalizeData('id_order');
        $company = Normalization::normalizeData('company');
        $expedition_number = Normalization::normalizeData('expedition_number');
        $lang = Normalization::normalizeData('lang');
        $id_sender = Normalization::normalizeData('id_sender');
        $cancelOrderSucceeded = false;
        $pickup_number_return = Normalization::normalizeData('pickup_number_return');

        if ($type == 'order') {
            $shipping_numbers = $this->utilities_dao->getShippingNumbersByExp($expedition_number);
        } elseif ($type == 'return') {
            $shipping_numbers = $this->utilities_dao->getShippingNumbersByIdOrderForReturns($order_id);
            $company = $this->utilities_dao->getCompanyReturn($order_id);
            $company = $company['carrier_type'];
        }

        $cod_envio = '';
        // Actualiza el tracking_number de Resumen transportista en el pedido
        CorreosOficialShippingNumber::updateShipingNumberInOrder($order_id, utf8_decode($cod_envio));

        switch ($company) {
            case 'Correos':
                if (empty($shipping_numbers)) {
                    $result_operation = $this->correos_soap->cancelarPreRegistroEnvio($lang, '99999999999999X', $id_sender);
                } else {
                    foreach ($shipping_numbers as $shipping_number) {
                        $result_operation = $this->correos_soap->cancelarPreRegistroEnvio($lang, $shipping_number['shipping_number'], $id_sender);
                    }
                    $result_operation['codigoRetorno'] = intval($result_operation['codigoRetorno']);
                    $result_operation['changeStatus'] = $this->getStatus('ShipmentCanceled');
					$cancelOrderSucceeded = true;
                }
                break;
            case 'CEX':
                $returnPickupCancelled = false;

                $hasPickup = Db::getInstance()->getRow('SELECT pickup, pickup_number FROM ' . _DB_PREFIX_ . 'correos_oficial_orders WHERE id_order = ' . (int) $order_id);


                $client = $this->utilities_dao->getDataClient('CEX', false, $id_sender);

                if ($type =='order' && !empty($hasPickup) && (int) $hasPickup['pickup'] === 1) {
                    $pickupDatas = array(
                        'id_order' => $order_id,
                        'codSolicitud' => $hasPickup['pickup_number'],
                        'client' => $client,
                    );
                    $result_operation = $this->cex_rest->cancelarRecogida($pickupDatas, $id_sender);
                    $returnPickupCancelled = true;
                } elseif ($type == 'return' && $pickup_number_return) {
                    $pickupReturnDatas = array(
                        'id_order' => $order_id,
                        'codSolicitud' => $pickup_number_return,
                        'client' => $client,
                    );
                    $result_operation = $this->cex_rest->cancelarRecogida($pickupReturnDatas, $id_sender);
                    $this->utilities_dao->cancelReturnPickup($order_id);
                    $returnPickupCancelled = true;
                } else {
                    //Su solicitud de envio ha sido cancelada (CEX)
                    $result_operation = array(
                        'codigoRetorno' => 0,
                        'changeStatus' => $this->getStatus('ShipmentCanceled'),
                        'status_code' => 200
                    );
                }

                if ($result_operation['codigoRetorno'] == 0) {

                    $result_operation['mensajeRetorno'] = $this->l('The shipment cancel request has been succeded (CEX)');

                    if ($returnPickupCancelled) {
                        $result_operation['mensajeRetorno'] = $this->l('The pickup and shipment cancel request has been succeded (CEX)');
                    }
                }

                //Su solicitud de envio ha sido cancelada (CEX)
                $result_operation['changeStatus'] = $this->getStatus('ShipmentCanceled');
                $cancelOrderSucceeded = true;
                break;
            default:
                throw new LogicException('ERROR 19012: Devolución no cancelable. Debe ser un producto de Correos o de CEX');
        }

        if ($result_operation['codigoRetorno'] == 0 || ($result_operation['codigoRetorno'] == 1 && $result_operation['codigoError'] == 67)) {
            
            // Informamos el id_carrier con el que se preregistro en ps_orders para que este sea ahora el id_carrier principal
            if ($type == 'order') {
                $order = new Order($order_id);
                $correos_order = CorreosOficialOrders::getCorreosOrder($order_id);
                $order->id_carrier = $correos_order['id_carrier'];
                $order->update();
            }

            $this->utilities_dao->cancelOrder($expedition_number);
            $this->utilities_dao->deleteReturns($order_id);

			if($cancelOrderSucceeded && $this->utilities_dao->readSettings('ShowShippingStatusProcess', false, $this->order->id_shop)->value == 'on') {
				$config_status = $this->utilities_dao->readSettings('ShipmentCanceled', false, $this->order->id_shop);
				CorreosOficialUtils::changeOrderStatus($order_id, $config_status->value);
			}

        } 

        return $result_operation;
    }

    public function generatePickup()
    {
        $order_id = Normalization::normalizeData('id_order');
        $company = Normalization::normalizeData('company');
        $expedition_number = Normalization::normalizeData('expedition_number');
        $id_sender = Normalization::normalizeData('id_sender');

        $client = $this->utilities_dao->getDataClient($company, false, $id_sender);
        $mode_pickup = Tools::getValue('mode_pickup');

        $shipping_numbers = $this->utilities_dao->getShippingNumbersByExp($expedition_number, $mode_pickup);

        $pickup_data = array(
            'id_order' => $order_id,
            'bultos' => Normalization::normalizeData('bultos'),
            'order_reference' => Normalization::normalizeData('order_reference'),
            'pickup_date' => Normalization::normalizeData('pickup_date'),
            'sender_from_time' => Normalization::normalizeData('sender_from_time'),
            'sender_to_time' => Normalization::normalizeData('sender_to_time'),
            'sender_address' => Normalization::normalizeData('sender_address'),
            'sender_city' => Normalization::normalizeData('sender_city'),
            'sender_cp' => Normalization::normalizeData('sender_cp'),
            'sender_name' => Normalization::normalizeData('sender_name'),
            'sender_contact' => Normalization::normalizeData('sender_contact'),
            'sender_phone' => Normalization::normalizeData('sender_phone'),
            'sender_email' => Normalization::normalizeData('sender_email', 'email'),
            'sender_nif_cif' => Normalization::normalizeData('sender_nif_cif'),
            'sender_country' => Normalization::normalizeData('sender_country'),
            'producto' => Normalization::normalizeData('producto'),
            'print_label' => Normalization::normalizeData('print_label') == 0 ? 'N' : 'S',
            'package_type' => Normalization::normalizeData('package_type'),
            'shipping_numbers' => $shipping_numbers,
            'client' => $client
        );

        switch ($company) {
            case 'Correos':
                if ($mode_pickup == 'return') {
                    $pickup_data['producto'] = "S0148";
                }
                $result_operation = $this->correos_soap->registrarRecogida($pickup_data, $id_sender);
                $result_pickup = array(
                    'codSolicitud' => utf8_decode($result_operation['codSolicitud']),
                    'codigoRetorno' => $result_operation['codigoRetorno'],
                    'mensajeRetorno' => utf8_decode($result_operation['mensajeRetorno'])
                );

                if ($result_operation['codigoRetorno'] == 0) {
                    $pickup_from_hour = $pickup_data['sender_from_time'];
                    $pickup_to_hour = $pickup_data['sender_to_time'];
                    $pickup_order_data = array(
                        'id_order' => $order_id,
                        'pickup_number' => utf8_decode($result_operation['codSolicitud']),
                        'pickup_date' => $pickup_data['pickup_date'],
                        'pickup_from_hour' => $pickup_from_hour,
                        'pickup_to_hour' => $pickup_to_hour,
                        'package_size' => intval($pickup_data['package_type']),
                        'print_label' => $pickup_data['print_label'],
                        'pickup_status' => "Grabado"
                    );

                    if ($mode_pickup == 'pickup') {
                        $this->utilities_dao->savePickup($pickup_order_data);
                    } elseif ($mode_pickup == 'return') {
                        $this->utilities_dao->saveReturnPickup($pickup_order_data);
                    }
                }
                break;
            case 'CEX':
                if ($mode_pickup == 'return') {
                    $pickup_data['producto'] = "63";
                }
                $result_operation = $this->cex_rest->registrarRecogida($pickup_data, $id_sender);
                $result_cex_decoded = json_decode($result_operation['json_retorno'], true);

                $result_pickup = array(
                    'codSolicitud' => $result_cex_decoded['numRecogida'],
                    'codigoRetorno' => $result_operation['codigoRetorno'],
                    'mensajeRetorno' => $result_operation['mensajeRetorno']
                );

                if ($result_operation['codigoRetorno'] == 0) {

                    $string_date = $result_operation['mensajeRetorno'];
                    preg_match('/ fechaRecogida: (.*?),/is', $string_date, $pickup_date);
                    $aux_date = date_create_from_format('dmY', $pickup_date[1]);
                    $pickup_date_format = date_format($aux_date, 'Y-m-d');

                    preg_match('/ horaDesde1: (.*?),/is', $string_date, $pickup_from_hour);
                    preg_match('/ horaHasta1: (.*?)$/', $string_date, $pickup_to_hour);

                    $pickup_order_data = array(
                        'id_order' => $order_id,
                        'pickup_number' => $result_cex_decoded['numRecogida'],
                        'pickup_date' => $pickup_date_format,
                        'pickup_from_hour' => $pickup_from_hour[1],
                        'pickup_to_hour' => $pickup_to_hour[1],
                        'package_size' => 0,
                        'print_label' => "N",
                        'pickup_status' => "Grabado"
                    );

                    if ($mode_pickup == 'pickup') {
                        $this->utilities_dao->savePickup($pickup_order_data);
                    } elseif ($mode_pickup == 'return') {
                        $this->utilities_dao->saveReturnPickup($pickup_order_data);
                    }
                }
                break;
        }

        return $result_pickup;
    }

    public function sendEmail()
    {
        $order_id = Normalization::normalizeData('id_order');
        $company = Normalization::normalizeData('company');

        $customer_email = Normalization::normalizeData('customer_email', 'email');
        $default_sender_email = Normalization::normalizeData('default_sender_email', 'email');

        $customer_cp = Normalization::normalizeData('customer_cp');
        $customer_country = Normalization::normalizeData('customer_country');
        $sender_cp = Normalization::normalizeData('sender_cp');
        $sender_country = Normalization::normalizeData('sender_country');
        $pickup_date = Normalization::normalizeData('pickup_date');
        $sender_from_time = Normalization::normalizeData('sender_from_time');

        $returns_code = array();

        for ($i = 1; $i < 11; $i++) {
            $returns_code[] = Normalization::normalizeData('return_code_' . $i);
        }

        $result_pickup = array();

        $correos_oficial_order = new CorreosOficialOrder($order_id);
        $ws_shipping_numbers = $correos_oficial_order->getShippingNumbersByIdOrderForReturns();
        $expNumber = $correos_oficial_order->getExpeditionNumberByIdOrderForReturn();

        if (!is_array($ws_shipping_numbers)) {
            $ws_shipping_numbers = array($ws_shipping_numbers);
        }

        $label = $this->getEtiquetasByExpNumber(true, $company, 'return', $ws_shipping_numbers, $expNumber, '2', '', $label_format = 0);

        $cp_source = $sender_cp;
        $country_source = $sender_country;

        $cp_dest = $customer_cp;
        $country_dest = $customer_country;

        $require_customs_doc = NeedCustoms::isCustomsRequired($cp_source, $cp_dest, $country_source, $country_dest);

        if ($require_customs_doc) {
            $cn23 = $this->getCN23ToEmail($order_id, $sender_country);
        } else {
            $cn23 = null;
        }

        $label_shipings_numbers = '';
        $label_shipings_numbers = trim($label_shipings_numbers, "_");

        $returns_code_string = '';
        
        foreach ($returns_code as $return_code) {
            if (!empty($return_code)) {
                $returns_code_string .= $return_code . "<br />";
                $label_shipings_numbers .= $return_code . "_";
            }
        }

        if(count($ws_shipping_numbers) == 1 && $returns_code_string == '' ) {
            $label_shipings_numbers .= $ws_shipping_numbers[0];
        }

        $returns_data = array(
            'customer_email' => $customer_email,
            'sender_email' => $default_sender_email,
            'label' => $label,
            'cn23' => $cn23,
            'company' => $company,
            'shipping_number' => $label_shipings_numbers,
            'pickup_date' => $pickup_date,
            'sender_from_time' => $sender_from_time,
            'return_code' => $returns_code_string,
            'order_id' => $order_id,
            'shop_name' => $this->context->shop->name
        );

        // Envío de email
        $email = new CorreosOficialReturnsMail($returns_data, $this->module, $this->context);

        $result_email = $email->sendEmail();

        if ($result_email == 'Enviado') {
            $result_pickup['mensajeRetorno'] = $this->l('An email was sended to the customer with details of the return');
            $result_pickup['codigoRetorno'] = 0;
        } else {
            $result_pickup['mensajeRetorno'] = $result_email . ". " . $this->l('Can not send returns email to your customer. Please, print the label and CN23 documents and send an email to your customer');
        }
        CorreosOficialUtils::deleteFiles();

        return $result_pickup;
    }

    public function cancelPickup()
    {
        $order_id = Normalization::normalizeData('id_order');
        $company = Normalization::normalizeData('company');
        $client = $this->utilities_dao->getDataClient($company);
        $mode_pickup = Tools::getValue('mode_pickup');
        $id_sender = Normalization::normalizeData('id_sender');

        $pickup_data = array(
            'id_order' => $order_id,
            'codSolicitud' => Normalization::normalizeData('codSolicitud'),
            'client' => $client
        );
        switch ($company) {
            case 'Correos':
                $result_operation = $this->correos_soap->cancelarRecogida($pickup_data, $id_sender);
                $result_operation['codigoRetorno'] = utf8_decode($result_operation['codigoRetorno']);
                break;
            case 'CEX':
                $result_operation = $this->cex_rest->cancelarRecogida($pickup_data, $id_sender);
                break;
        }

        if ($result_operation['codigoRetorno'] == 0 || $result_operation['codigoRetorno'] == '20') {
            if ($mode_pickup == 'pickup') {
                $this->utilities_dao->cancelPickup($order_id);
            } elseif ($mode_pickup == 'return') {
                $this->utilities_dao->cancelReturnPickup($order_id);
            }
        }

        return $result_operation;
    }
    public function getEtiquetasByExpNumber($send_email, $company, $type, $shipping_numbers, $exp_number, $labelType, $labelPosition, $labelFormat = 0, $id_order = null)
    {
        $order = ($id_order !== null) ? new Order((int)$id_order) : null;
        $id_shop = ($order !== null) ? $order->id_shop : $this->context->shop->id;
        $pdf = new PDFMerger($labelType, $labelFormat);
        $tempFolder = get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/pdftmp";
        $pdfOutputFile = $tempFolder . "/" . uniqid('labels_') . '.pdf';

        $useUserLogo = CorreosOficialConfigDao::getConfigValue('ChangeLogoOnLabel', $id_shop);
		$getUserLogo = CorreosOficialConfigDao::getConfigValue('UploadLogoLabels', $id_shop);
        
        $labels = [];
        
        $logoBase64 = '';
		if ($useUserLogo == 'on') {
            $imagedata = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/media/logo_label/" . $getUserLogo);
            $logoBase64 = base64_encode($imagedata);
        }

        // Acciones según formato
        switch ($labelFormat) {

            case LABEL_FORMAT_3A4: // 3/A4
                if ($labelPosition == null) {
                    $labelPosition = 0;
                }
                $labelsResponse = $this->cex_rest->getLabelFromWS($exp_number, "3", $logoBase64, $labelPosition, $type);
                $labels = $labelsResponse->listaEtiquetas;
                break;
            case LABEL_FORMAT_4A4: // 4/A4

                break;
            default:
                if ($company == 'Correos') {
                    $i = 0;
                    // sin formato o estandar
                    foreach ($shipping_numbers as $shippingNumber) {
                        if ($type == 'order') {
                            $label = $this->correos_soap->SolicitudEtiquetaOp($shippingNumber);
                        } elseif ($type == 'return') {
                            $label = $this->correos_soap->SolicitudEtiquetaOp($shippingNumber);
                        } else {
                            throw new LogicException('ERROR 19011: El tipo debe ser order o return');
                        }
                        if ($label !== null) {
                            $labels[] = $label; // Agrega la etiqueta al array de etiquetas
                        } else {
                            $mensaje_retorno = 'ERROR 19020: '.$this->l('The user is not activated in the pickup service');

                            $result = array(
                                'codigoRetorno' => '403',
                                'mensajeRetorno' => $mensaje_retorno
                            );
                            die(json_encode($result));
                        }
                    }
                } elseif ($company == 'CEX') {
                    if ($labelPosition == null) {
                        $labelPosition = 0;
                    }
                    $labelsResponse = $this->cex_rest->getLabelFromWS($exp_number, "1", $logoBase64, $labelPosition, $type);
                    $labels = $labelsResponse->listaEtiquetas;
                    break;
                }
        }

        // Generación PDFs temporales
        for ($i = 0; $i < count($labels); $i++) {
            $tempPathPDF = $tempFolder . "/E_" . $i . "_" . $shipping_numbers[$i] . ".pdf";
            file_put_contents($tempPathPDF, base64_decode($labels[$i]));
            $pdf->addPDF($tempPathPDF, 'all');
        }

        $labels = array();

        // Opciones de mergeo
        if ($labelType == LABEL_TYPE_THERMAL || $labelFormat == LABEL_FORMAT_3A4 || $labelFormat == LABEL_FORMAT_4A4) {
            $pdf->mergeTopages('file', $pdfOutputFile);
        } else { // Adhesivas
            $pdf->merge('file', $pdfOutputFile, $labelType, $labelPosition
            );
        }

		if($send_email) {
            $labels [] = base64_encode(file_get_contents($pdfOutputFile));
			return $labels;
		}
        die(json_encode($pdfOutputFile));
    }

    public function getDocAduanera($type, $exp_number, $optionButton, $customer_country, $customer_name)
    {
        $files = array();
        $errors = array();

        if ($type == 'order') {
            $shipping_numbers = $this->utilities_dao->getShippingNumbersByExp($exp_number);
        } else {
            $shipping_numbers = $this->utilities_dao->getShippingNumbersByIdOrderForReturns($exp_number);
        }

        foreach ($shipping_numbers as $shipping_number => $field) {
            $result_doc_aduanera = $this->correos_soap->documentacionAduaneraOp($optionButton, $field['shipping_number'], $customer_country, $customer_name);

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
                file_put_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . "/pdftmp/" . $prefijo_archivo . "_" . $field['shipping_number'] . ".pdf", base64_decode($fichero));
                $files[] = $prefijo_archivo . "_" . $field['shipping_number'] . ".pdf";
            } else {
                $errors[] = array('error_msg' => utf8_decode($result_doc_aduanera['mensajeRetorno']));
            }
        }

        $result = array(
            'errors' => $errors,
            'files' => $files
        );

        die(json_encode($result));
    }

    public function getOrderStatus($order_id)
    {
        $correos_order = CorreosOficialOrders::getCorreosOrder($order_id);

        if (!isset($correos_order['shipping_number'])) {
            return false;
        }

        $shipping_numbers = $this->utilities_dao->getShippingNumbersByExp($correos_order['shipping_number']);

        $last_status[] = array(
            'codEnvio' => "",
            'codProducto' => "",
            'desTextoResumen' => "En espera de datos",
            'fecEvento' => "",
            'horEvento' => "",
            'unidad' => ""
        );

        foreach ($shipping_numbers as $bulto => $field) {
            if ($correos_order['carrier_type'] == 'Correos') {
                $package_status = $this->correos_rest->getOrderStatus($field['shipping_number'], true);

                if ($package_status != null) {

                    $i = 0;

                    if ($package_status[0]->eventos == null) {
                        return $last_status;
                    }
                    foreach ($package_status[0]->eventos as $evento) {

                        if ($evento->desTextoResumen == null) {
                            continue;
                        }

                        $last_status[$i] = array(
                            'codEnvio' => $package_status[0]->codEnvio,
                            'codProducto' => $correos_order['name'],
                            'desTextoResumen' => $evento->desTextoResumen,
                            'fecEvento' => $evento->fecEvento,
                            'horEvento' => $evento->horEvento,
                            'unidad' => $evento->unidad ?? ''
                        );
                        $i++;
                    }
                }
            } elseif (($correos_order['carrier_type'] == 'CEX')) {
                $cex_count = 0;
                $package_status = $this->cex_rest->TrackingCEXK8s($field['shipping_number'], false);

                if ($package_status) {
                    $i = 0;
                    foreach ($package_status->estadoEnvios as $package_status_cex) {
                        $cex_hour[0] = substr($package_status_cex->horaEstado, 0, 2);
                        $cex_hour[1] = substr($package_status_cex->horaEstado, 2, 2);
                        $cex_hour[2] = substr($package_status_cex->horaEstado, 4, 2);

                        $last_status[$i] = array(
                            'codEnvio' => $package_status->bultoSeguimiento[$cex_count]->codUnico,
                            'codProducto' => $correos_order['name'],
                            'desTextoResumen' => $package_status_cex->descEstado,
                            'fecEvento' => $package_status_cex->fechaEstado,
                            'horEvento' => $cex_hour[0] . ':' . $cex_hour[1] . ':' . $cex_hour[2],
                            'unidad' => ''
                        );
                        $i++;
                    }
                }
                $cex_count++;
            }
        }

        return $last_status;
    }

    public function getCN23ToEmail($order_id, $iso_code) {
		/* Se consigue ruta del CN23 */
        $json =  file_get_contents(_PS_BASE_URL_ . __PS_BASE_URI__ .basename(_PS_ADMIN_DIR_).'/index.php?controller=AdminCorreosOficialOrder&ajax=true&action=getCustomsDoc&exp_number=' . $order_id . '&type=return&customer_country=' . $iso_code . '&optionButton=ImprimirCN23Button2');
		$result = json_decode($json);

		if (empty($result->errors[0])) {
			$filename = $result->files[0];
			$path = _PS_MODULE_DIR_ . '/correosoficial/pdftmp/' . $filename;

			/**
			 * 
			 * Lectura del fichero de CN23 de devolución 
			 */
			$handle = fopen($path, 'rb');
			$contents = fread($handle, filesize($path));
			fclose($handle);

			// CN23 codificado en base64
			return base64_encode($contents);
		}
		return null;
	}

    public static function getCustomsDesc($bultos)
    {

        $customs_desc_array = array();
        $returned_customs_desc_array = array();
        $units = array(" €", " Kg", " Unid.");

        for ($i = 1; $i <= $bultos; $i++) {
            $n = 0;

            if (!isset($_POST['order_form']['customs_desc[' . $i])) {
                return;
            }

            foreach ($_POST['order_form']['customs_desc[' . $i] as $customs_desc) {

                $customs_desc = str_replace($units, "", $customs_desc);
                $customs_desc = rtrim($customs_desc, " • ");
                $customs_desc_array[$i][$n + 1] = $customs_desc;
                $n++;
            }

            foreach ($customs_desc_array as $customs_desc) {
                $h = 0;

                foreach ($customs_desc as $cd) {

                    // Informamos solo las descripciones necesarias.
                    if ($h < count($customs_desc_array[$i])) {

                        $elements = explode(" • ", $cd);

                        $len_ntarifario = strlen($elements[0]);

                        if ($len_ntarifario == 6 || $len_ntarifario == 8 || $len_ntarifario == 10) {
                            $returned_customs_desc_array[$i][$h]['numero_tarifario'] = $elements[0];
                            $returned_customs_desc_array[$i][$h]['descripcion_aduanera'] = $elements[1];
                        } else {
                            $returned_customs_desc_array[$i][$h]['numero_tarifario'] = '';
                            $returned_customs_desc_array[$i][$h]['descripcion_aduanera'] = $elements[0];
                        }

                        $returned_customs_desc_array[$i][$h]['valor_neto'] = $elements[2] * 100;
                        $returned_customs_desc_array[$i][$h]['weight'] = $elements[3] * 1000;
                        $returned_customs_desc_array[$i][$h]['unidades'] = $elements[4];
                        $h++;
                    }
                }
            }
        }

        return $returned_customs_desc_array;
    }

	public function getStatus($search) {
		if($this->statusProcessActive == 'on') {
			return $this->utilities_dao->readSettings($search, false, $this->order->id_shop)->value;
		}
		return false;
	}

    /**
     * Inserta los datos de Oficina o CityPaq del pedido en correos_oficial_requests
     * int $order_id Id del pedido
     * return void
     */
    private function insertRequestData($order_id) {
        $error_insert = 'Error al insertar datos de Oficina/CityPaq';

        if (!$order_id) {
            throw new LogicException('ERROR 19030: '.$error_insert. 'Debe existir un número de pedido $order_id');
        }

        // Si no se informan los campos reference_code ni request_data es que no queremos cambiar de Oficina/CityPaq
        if (empty(Tools::getValue('order_form')['reference_code']) && empty(Tools::getValue('order_form')['request_data'])) {
            return false;
        }

        $order_form = Normalization::normalizeData('order_form');

        $id_cart =(int) Order::getCartIdStatic($order_id);

        $reference_code = $order_form['reference_code'];
        $data = json_decode(Tools::getValue('order_form')['request_data']);

        // Comprobamos que el JSON de data es válido y si sí asignamos
        if ($data) {
            $data = json_encode($data);
        } else {
            throw new LogicException('ERROR 19031: '.$error_insert. 'El json introducido no es válido');
        }

        $existing_cart = CorreosOficialCheckout::insertCartIntoRequests($id_cart);

        try {
            CorreosOficialCheckout::insertReferenceCode($id_cart, $reference_code, $data, $existing_cart);
        } catch (Excepcion $e) {
            throw new LogicException('ERROR 19032: '.$error_insert);
        }
        
    }

}
