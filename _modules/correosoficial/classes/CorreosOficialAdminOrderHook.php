<?php

require_once 'CorreosOficialOrders.php';
require_once 'CorreosOficialOrder.php';
require_once 'CorreosOficialCarrier.php';
require_once 'CorreosOficialConfig.php';

require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';
require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialOrderDao.php';

require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/Correos/CorreosRest.php';
require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/Correos/CorreosSoap.php';
require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/Cex/CexRest.php';

require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/Commons/Normalization.php';

define('ALLOW_EXTERNAL_CARRIERS', true);

class CorreosOficialAdminOrderHook
{
    private $module_dir;

    private $correos_rest;
    private $correos_soap;
    private $cex_rest;

    private $correosoficial_context;

    private $context;

    public function __construct($context, $module_dir, $correosoficial_context)
    {
        $this->context = $context;
        $this->module_dir = $module_dir;
        $this->correosoficial_context = $correosoficial_context;

        $this->correos_rest = new CorreosRest();
        $this->correos_soap = new CorreosSoap();
        $this->cex_rest = new CexRest();
    }

    public function hookDisplayAdminOrder()
    {

        // Order
        $id_order = Normalization::normalizeData('id_order');
        $co_order = new CorreosOficialOrder($id_order);
        $order = new Order((int)$id_order);

        // Si es un producto virtual no aplica transportista
        if (!$co_order->id_carrier) {
            return false;
        }

        global $co_module_url_ps;

        $array_packages_order = array();
        $array_packages_return = array();

        $saved_return_pickup = array();

        $pickup_return_data_response = array();
        $pickup_return_cancelable = '';

        $return_status = '';

        $cod_office = '';
        $cod_homepaq = '';

        // Init Modal para remitentes
		$showSenderModal = false;
        $errorSenderName = '';
        $errorCompanyName = '';

        // Multicliente (Se tendría que implementar un método que devuelva contratos activos)
        // $customer_dao = new CorreosOficialActiveCustomersDao();
        // $active_client = $customer_dao->getActivesCustomers();
        $active_client = 'both'; // Forzado

        $is_international = '';
        $require_customs_doc = '';

        $order_returnable = '';
        $is_code_at = false;

		$client_data = array();
        $carriers = array();
        $correos_return = array();

        $correos_order = CorreosOficialOrders::getCorreosOrder($id_order);
        $correos_order = is_array($correos_order) ? $correos_order : [];

        $correos_return = CorreosOficialOrders::getCorreosReturn($id_order);
        $correos_pickup_return = CorreosOficialOrders::getCorreosPickupReturn($id_order);

        // Comprobamos Cash on delivery
        $paymentMethodCodSelected = CorreosOficialConfigDao::getConfigValue('CashOnDeliveryMethod', $order->id_shop);
        if ($co_order->module == $paymentMethodCodSelected) {
            $cash_on_delivery = true;
        } else {
            $cash_on_delivery = false;
        }
        $cash_on_delivery_value = number_format($co_order->getTotalPaid(), 2);

        // Customer
        $customer = new Customer($co_order->id_customer);
        $address = new Address($co_order->id_address_delivery);
        $cart = new Cart($co_order->id_cart);
        $countries = Country::getCountries($this->context->language->id, false);

        // Carrier de prestashop
        $carrier = new Carrier((int) ($co_order->id_carrier));

        // Comprueba que el tranportista sea perteneciente al módulo, y no estén permitidos transportistas externos.
        if ($carrier->external_module_name != 'correosoficial' && !ALLOW_EXTERNAL_CARRIERS) {
            return; // retorna si es transportista ajeno y no se permiten 
        }

        $id_zone = $address->getZoneById($co_order->id_address_delivery);

        $carrier_order = $this->getCarrierOrder($carrier, $co_order, $id_zone, $correos_order, $order->id_shop);

        // Seleccionamos carriers según usuario (Correos, Cex, All)
        if ($active_client != 'none') {

            $all_carriers = array();

            $all_carriers = CorreosOficialCarrier::getCarriersByCompany($active_client, true);

            $shipping_zone_rules = new ShippingMethodZoneRules();

            $countries2 = Country::getCountriesByZoneId($id_zone, $this->context->language->id);

            $carriers = $this->fillCarriers($all_carriers, $carrier, $countries2, $shipping_zone_rules, $carriers);

        } else {
            // Desconectamos todo?
        }

		// Remitente por defecto
		$default_sender = CorreosOficialSenders::getDefaultSender(false, $order->id_shop);
        // Sobreescribimos con correos_code y cex_code
        $default_sender = CorreosOficialSenders::getSendersWithCodesById($default_sender['id']);

		// Si el pedido está preregistrado obtenemos información guardada
		if ($correos_order) {
			$default_sender = CorreosOficialSenders::getSendersWithCodesById($correos_order['id_sender']);
			$carrier_order = CorreosOficialCarrier::getCarrierByProductId($correos_order['id_product']);
		}

		// Contrato según remitente por defecto y producto
		if ($carrier_order && $default_sender) {
			$client_data = CorreosOficialSenders::getCodeBySenderAndCompany($default_sender['id'], strtolower($carrier_order['company']));
		}
        // Lista de remitentes
		$senders = CorreosOficialSenders::getSendersWithCodes($order->id_shop);

		// Client code actual si existe relación
		$client_code = isset($client_data['customer_code']) ? $client_data['customer_code'] : '';

		// Alerta Modal sobre Remitentes
		if (empty($senders) || empty($default_sender)) {
			$showSenderModal = true;
		}else{
			
			// Si está preregistrado
			if ($correos_order) {
				$order_company = $correos_order['carrier_type'];
			}else{
                $order_company = CorreosOficialCarrier::getCompanyByOrder($id_order);
            }
			
			if(
                ($order_company == 'Correos' && !$default_sender['correos_code']) ||
                ($order_company == 'CEX' && !$default_sender['cex_code'])
            ){
                $errorSenderName = $default_sender['sender_name'];
                $errorCompanyName = $order_company;
				$showSenderModal = true;
			}
		}



        $delivered = false;

        // Comprobamos si está preregistrado y si tiene recogida grabada
        if (empty($correos_order)) {

            $order_done = false;
            $cancelable = true;
            $pickup = 0;
            $pickup_cancelable = false;
            $pickup_data_response = self::getPickUpDataResponse('Estado 1');

        } else {
            if ($correos_order['shipping_number'] != "") {

                $order_done = true;

                // Comprobamos bultos para traer información de cada bulto
                $array_packages_order = CorreosOficialOrders::getCorreosPackages($id_order, $correos_order['shipping_number']);

                if ($correos_order['pickup'] == 1) {
                    $pickup = 1;
                    if ($correos_order['company'] == 'Correos') {
                        $pickup_data = array(
                            'CodigoSRE' => $correos_order['pickup_number'],
                            'CorreosContract' => $client_data['CorreosContract'],
                            'CorreosCustomer' => $client_data['CorreosCustomer'],
                            'CorreosOv2Code' => $client_data['CorreosOv2Code'],
                            'ModoOperacion' => '1' // Info + Todos los estados
                        );
                        $pickup_status = $this->correos_soap->ConsultaSRE($pickup_data);
                        if ($pickup_status['xml_retorno'] != null) {
                            $array_status = $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->TrazasSolicitudRecogidaEsporadica ?? null;

                            if ($array_status == null) {
                                $pickup_data_response = self::getPickUpDataResponse($pickup_status['mensajeRetorno'], '5', 'Sin datos');
                            } else {

                                $pickup_last_status = self::returnPickupLastStatus($array_status);

                                $pickup_data_response = array(
                                    'codEstado' => $pickup_last_status->codEstado,
                                    'status' => $pickup_last_status->desTextoResumen,
                                    'pickup_reference' => $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3CodigoSolicitudRecogidaEsporadica->ReferenciaRecogida,
                                    'pickup_date' => substr($pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->FecRecogida,0, 10),
                                    'pickup_from_hour' => date('H:i', strtotime($correos_order['pickup_from_hour'])),
                                    'pickup_to_hour' => date('H:i', strtotime($correos_order['pickup_to_hour'])),
                                    'pickup_address' => $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->NomNombreViaRec,
                                    'pickup_city' => $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->NomLocalidadRec,
                                    'pickup_cp' => $pickup_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->CodigoPostalRecogida
                                );
                            }
                        } else {
                            $pickup_data_response = self::getPickUpDataResponse('Sin trazabilidad', '3', 'En espera de datos');
                        }
                    } elseif ($correos_order['company'] == 'CEX') {
                        $pickup_data = array(
                            'recogida' => $correos_order['pickup_number'],
                            'codigoCliente' => $client_data['CEXCustomer'],
                            'fecRecogida' => "",
                            'idioma' => "ES"
                        );
                        $pickup_status = $this->cex_rest->consultarRecogida($pickup_data);
                        $pickup_data_cex = json_decode($pickup_status['json_retorno']);
                        $pickup_data_response = array(
                            'status' => $pickup_data_cex->situaciones[0]->descSituacion,
                            'pickup_reference' => $pickup_data_cex->referencia,
                            'pickup_date' => $pickup_data_cex->fecRecogida,
                            'pickup_from_hour' => date('H:i', strtotime($correos_order['pickup_from_hour'])),
                            'pickup_to_hour' => date('H:i', strtotime($correos_order['pickup_to_hour'])),
                            'pickup_address' => $pickup_data_cex->domRecogida,
                            'pickup_city' => $pickup_data_cex->pobRecogida,
                            'pickup_cp' => $pickup_data_cex->codPosRecogida
                        );
                    }
                    // Comprobamos estado de la recogida
                    $pickup_cancelable = true;
                    $codEstado = isset($pickup_return_data_response['codEstado'])
                    ? $pickup_return_data_response['codEstado'] : '';

                    if ($correos_order['carrier_type'] == 'Correos' &&
                        $codEstado != 'SR-001' && // Recogida solicitada Correos
                        $pickup_data_response['codEstado'] != 'SR-003' && // Alta Unidad de recogida Correos
                        $pickup_data_response['status'] != 'RECOGIDA REGISTRADA' &&
                        $pickup_data_response['status'] != 'PDTE ASIGNAR') {
                        $pickup_cancelable = false;
                    }
                    if ($pickup_data_response['status'] == 'ANULADA') {
                        $pickup = 0;
                        $pickup_cancelable = false;
                    }
                } else {
                    $pickup = 0;
                    $pickup_cancelable = false;
                    $pickup_data_response = self::getPickUpDataResponse('Estado 2');
                }

                $last_status[] = array(
                    'codEnvio' => "",
                    'codProducto' => "",
                    'desTextoResumen' => "En espera de datos",
                    'fecEvento' => "",
                    'horEvento' => "",
                    'unidad' => ""
                );

                foreach ($array_packages_order as $bulto) {
                    if (isset($correos_order['carrier_type'])) {
                        if ($correos_order['carrier_type'] == 'Correos') {
                            $package_status = $this->correos_rest->getOrderStatus($bulto['shipping_number'], false);
                            if (isset($package_status[0]) && $package_status[0]->eventos !== null) {
                                $i = 0;
                                foreach ($package_status[0]->eventos as $evento) {
                                    if ($evento->desTextoResumen == null) {
                                        continue;
                                    }
                                    $last_status[$i] = array(
                                        'codEnvio' => $package_status[0]->codEnvio,
                                        'desTextoResumen' => $evento->desTextoResumen,
                                        'fecEvento' => $evento->fecEvento,
                                        'unidad' => ''
                                    );
                                    $i++;
                                }
                            }
                        } elseif ($correos_order['carrier_type'] == 'CEX') {
                            $package_status = $this->cex_rest->TrackingCEXK8s($bulto['shipping_number'], false);

                            if ($package_status) {
                                $last_status[0] = array(
                                    'codEnvio' => $package_status->bultoSeguimiento[0]->codUnico,
                                    'codProducto' => $package_status->producto,
                                    'desTextoResumen' => $package_status->bultoSeguimiento[0]->descEstado,
                                    'fecEvento' => $package_status->bultoSeguimiento[0]->fechaEstado,
                                    'unidad' => ''
                                );
                            }
                        }
                    }
                }

                // De inicio ningún en ningún estado se podrá cancelar, hasta comprobar exclusiones.
                $cancelable = false;
                foreach ($last_status as $status_bulto) {

                    $statusBultoResumen = $status_bulto['desTextoResumen'];

                    // Exclusiones de estados en los que se puede cancelar
                    if (
                        $statusBultoResumen == 'En espera de datos' ||
                        $statusBultoResumen == 'Prerregistrado' ||
                        $statusBultoResumen == 'Admisión anulada' ||
                        $statusBultoResumen == 'SIN RECEPCION'
                    ) {
                        $cancelable = true;
                    }

                    // Si está entregado no se podrá cancelar (ya que no está excluido) y marcamos flag delivered
                    if (
                        $statusBultoResumen == 'Entregado' ||
                        $statusBultoResumen == 'ENTREGADO'
                    ) {
                        $delivered = true;
                    }
                }

            } else {
                $order_done = false;
                $cancelable = true;
                $pickup = 0;
            }
        }

        // DEVOLUCIONES
        if (empty($correos_return)) {
            $exist_return = false;
            $return_cancelable = true;
            $pickup_return = 0;
        } else {
            $exist_return = true;
            $saved_return = new CorreosOficialOrderDao();
            $saved_return_pickup = $saved_return->getPickupReturn($id_order);
            $client_data = CorreosOficialSenders::getCodeBySenderAndCompany($default_sender['id'], strtolower($correos_return['carrier_type']));

            if (!empty($saved_return_pickup)) {
                $pickup_return = 1;
                if ($correos_return['carrier_type'] == 'Correos') {
                    $pickup_return_data = array(
                        'CodigoSRE' => $saved_return_pickup[0]->pickup_number,
                        'CorreosContract' => $client_data['CorreosContract'],
                        'CorreosCustomer' => $client_data['CorreosCustomer'],
                        'CorreosOv2Code' => $client_data['CorreosOv2Code'],
                        'ModoOperacion' => '1' // Info + Todos los estados
                    );
                    $pickup_return_status = $this->correos_soap->ConsultaSRE($pickup_return_data);

                    if ($pickup_return_status['xml_retorno'] != null) {
                        $array_status = $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->TrazasSolicitudRecogidaEsporadica ?? null;

                        if ($array_status == null) {
                            $pickup_return_data_response = self::getPickUpDataResponse('Solicitud no encontrada', '4', 'Sin datos');
                        } else {
                            $last_status = self::returnPickupLastStatus($array_status);

                            if (count($array_status->ns3TrazaSolicitudRecogidaEsporadica) == 1) {
                                $codEstado = $array_status->ns3TrazaSolicitudRecogidaEsporadica->codEstado;
                                $status = $array_status->ns3TrazaSolicitudRecogidaEsporadica->desTextoResumen;
                                $pickup_date = substr($array_status->ns3TrazaSolicitudRecogidaEsporadica->fecEstado . " " . $array_status->ns3TrazaSolicitudRecogidaEsporadica->horEstado, 0, 10);
                            } else {
                                $codEstado = $last_status->codEstado;
                                $status = $last_status->desTextoResumen;
                                $pickup_date = substr($last_status->fecEstado . " " . $last_status->horEstado, 0, 10);
                            }

                            $pickup_return_data_response = array(
                                'codEstado' => $codEstado,
                                'status' => $status,
                                'pickup_reference' => $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3CodigoSolicitudRecogidaEsporadica->ReferenciaRecogida,
                                'pickup_from_hour' => date('H:i', strtotime($correos_pickup_return['pickup_from_hour'])),
                                'pickup_to_hour' => date('H:i', strtotime($correos_pickup_return['pickup_to_hour'])),
                                'pickup_date' => $pickup_date,
                                'pickup_address' => $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->NomNombreViaRec,
                                'pickup_city' => $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->NomLocalidadRec,
                                'pickup_cp' => $pickup_return_status['xml_retorno']->soapenvBody->ns2SolicitudConsultaRecogidaEsporadicaResult->ns2RespuestaSolicitudConsultaRecogidaEsporadica->ListaRespuestaCodigoRecogidaEsporadica->ns2RespuestaCodigoRecogidaEsporadica->ns3DatosSolicitudRecogidaEsporadica->ns3Recogida->CodigoPostalRecogida
                            );

                        }

                    } else {
                        $pickup_return_data_response = self::getPickUpDataResponse('Sin trazabilidad', '3', 'En espera de datos');
                    }
                } elseif ($correos_return['carrier_type'] == 'CEX') {
                    $pickup_return_data = array(
                        'recogida' => $saved_return_pickup[0]->pickup_number,
                        'codigoCliente' => $client_data['CEXCustomer'],
                        'fecRecogida' => "",
                        'idioma' => "ES"
                    );
                    $pickup_return_status = $this->cex_rest->consultarRecogida($pickup_return_data, $default_sender['id']);
                    $pickup_data_cex = json_decode($pickup_return_status['json_retorno']);
                    $pickup_return_data_response = array(
                        'status' => $pickup_data_cex->situaciones[0]->descSituacion,
                        'pickup_reference' => $pickup_data_cex->referencia,
                        'pickup_date' => $pickup_data_cex->fecRecogida,
                        'pickup_from_hour' => date('H:i', strtotime($correos_pickup_return['pickup_from_hour'])),
                        'pickup_to_hour' => date('H:i', strtotime($correos_pickup_return['pickup_to_hour'])),
                        'pickup_address' => $pickup_data_cex->domRecogida,
                        'pickup_city' => $pickup_data_cex->pobRecogida,
                        'pickup_cp' => $pickup_data_cex->codPosRecogida
                    );
                }
                // Comprobamos estado de la recogida
				$pickup_cancelable = true;

				if ($pickup_data_response['status'] != 'RECOGIDA REGISTRADA' 
					&& $pickup_data_response['status'] != 'PDTE ASIGNAR'
				) {
					$pickup_cancelable = false;
				}
                // if ($correos_return['carrier_type'] == 'Correos' &&
                //     $pickup_return_data_response['codEstado'] != 'SR-001' && // Recogida solicitada Correos
                //     $pickup_return_data_response['codEstado'] != 'SR-003' && // Alta Unidad de recogida Correos
                //     $pickup_return_data_response['status'] != 'RECOGIDA REGISTRADA' &&
                //     $pickup_return_data_response['status'] != 'PDTE ASIGNAR') {
                //     $pickup_return_cancelable = false;
                // }
                if ($pickup_return_data_response['status'] == 'ANULADA') {
                    $pickup_return = 1;
                    $pickup_return_cancelable = false;
                }
            } else {
                $pickup_return = 0;
                $pickup_return_cancelable = false;
                $pickup_return_data_response = self::getPickUpDataResponse('Estado 3');
            }

            $array_packages_return = CorreosOficialOrders::getCorreosPackagesReturn($id_order);
            foreach ($array_packages_return as $bulto => $field) {
                if ($correos_return['carrier_type'] == 'Correos') {
                    $package_status_return = $this->correos_rest->getOrderStatus($field['shipping_number'], false);

                    if ($package_status_return[0]->eventos != null) {
                        foreach ($package_status_return[0]->eventos as $evento => $field2) {
                            $i = 0;
                            $last_status_return[$i] = array(
                                'codEnvio' => $package_status_return[0]->codEnvio,
                                //'codProducto' => $package_status_return[0]->codProducto,
                                'desTextoResumen' => $field2->desTextoResumen,
                                'fecEvento' => $field2->fecEvento,
                                'unidad' => ""
                            );
                            $i++;
                        }
                    }
                    else {
                        $last_status_return[0] = array(
                                'codEnvio' => "",
                                'codProducto' => "",
                                'desTextoResumen' => "En espera de datos",
                                'fecEvento' => "",
                                'horEvento' => "",
                                'unidad' => ""
                            );
                    }
                } else {
                    $package_status_return = $this->cex_rest->TrackingCEXK8s($field['shipping_number'], false);

                    if ($package_status_return) {
                        $last_status_return[] = array(
                            'codEnvio' => $package_status_return->bultoSeguimiento[0]->codUnico,
                            'codProducto' => $package_status_return->producto,
                            'desTextoResumen' => $package_status_return->bultoSeguimiento[0]->descEstado,
                            'fecEvento' => $package_status_return->bultoSeguimiento[0]->fechaEstado,
                            'unidad' => ''
                        );
                    }
                }
            }

            $return_cancelable = true;
            $return_status = "";
            foreach ($last_status_return as $status_bulto => $field3) {
                if ($field3['desTextoResumen'] != 'Prerregistrado' && $field3['desTextoResumen'] != 'Admisión anulada' && $field3['desTextoResumen'] != 'SIN RECEPCION' && $field3['desTextoResumen'] != 'En espera de datos') {
                    $return_cancelable = false;
                }
                $return_status = $field3['desTextoResumen'];
            }
        }

        $correos_order_bultos = isset($correos_order['bultos']) ? $correos_order['bultos'] : 0;
        // Bultos a devolver
        if (empty($correos_order)) {
            $bultos_return = 1;
        } else {
            $bultos_return = $correos_order_bultos;
        }

        // Si hay request (office, citypaq) de checkout
        $correos_request = CorreosOficialOrders::getRequestRecord($id_order);

        //Comprobamos datos del checkout
        if ($correos_request) {

            /* Conseguimos el tipo de producto por la primera clave del json almacenado ('unidad' para oficina, 'cod_homepaq' para CityPaq) */
            $dataArray = json_decode($correos_request['data'], true);
            $keys = array_keys($dataArray);
            $product_type = reset($keys);

            if ($product_type == 'unidad') {
                $parsed_data = json_decode($correos_request['data']);
                $address_paq = array("dir_paq" => $parsed_data->direccion, "loc_paq" => $parsed_data->descLocalidad, "cp_paq" => $parsed_data->cp);
                $cod_office = $parsed_data->unidad;
                $cod_homepaq = '';
            } elseif ($product_type == 'cod_homepaq') {
                $parsed_data = json_decode($correos_request['data']);
                $address_paq = array("dir_paq" => $parsed_data->des_via . ' ' . $parsed_data->direccion . ' ' . $parsed_data->numero, "loc_paq" => $parsed_data->desc_localidad, "cp_paq" => $parsed_data->cod_postal);
                $cod_office = '';
                $cod_homepaq = $parsed_data->cod_homepaq;
            } else {
                throw new LogicException("ERROR 19040: En la tabla correos_requests campo data, el primer elemento debe ser 'unidad' o 'cod_homepaq'");
            }
        } else {
            $cod_office = '';
            $cod_homepaq = '';
            $address_paq = array("dir_paq" => "", "loc_paq" => "", "cp_paq" => "");
        }

        // Si no hay un remitente por defecto porque se ha borrado tomamos el remitente por defecto.
        if (!$default_sender) {
            $default_sender = CorreosOficialSenders::getDefaultSender(false, $order->id_shop);
        }

        // Aduanas
        $sender_postal_code = $default_sender['sender_cp'];
        $customer_postal_code = $address->postcode;
        $sender_country = $default_sender['sender_iso_code_pais'];
        $country = new Country($address->id_country);
        $customer_country = $country->iso_code;

        $require_customs_doc = NeedCustoms::isCustomsRequired($sender_postal_code, $customer_postal_code, $sender_country, $customer_country);
        $is_international = NeedCustoms::isInternational($sender_country, $customer_country);

        if ($carrier_order) {
            $order_returnable = $this->isOrderReturnable($carrier_order, $customer_country);
            $is_code_at = $this->isATCode($carrier_order, $address, $default_sender);
        }
        $height_by_default = '';
		$large_by_default = '';
		$width_by_default = '';
		$bank_acc_number = '';
        
        // Obtenemos configuración por defecto
        $correos_config = CorreosOficialConfig::getConfig($order->id_shop);
        foreach ($correos_config as $prop) {
            if ($prop['name'] == 'DefaultPackages') {
                $bultos_config = $prop['value'];
                if ($order_done) {
                    $bultos = $correos_order['bultos'];
                } else {
                    $bultos = $bultos_config;
                }
            }
            if ($prop['name'] == 'BankAccNumberAndIBAN') {
                $bank_acc_number = Crypto::decrypt($prop['value']);
                $BankIni = substr($bank_acc_number, 0, -4);
                $BankFin = substr($bank_acc_number, -4);
                $bank_acc_number = str_repeat("*", strlen($BankIni)) . $BankFin;
            }
            if ($prop['name'] == 'DefaultLabel') {
                $DefaultLabel = $prop['value'];
            }
            if ($prop['name'] == 'WeightByDefault') {
                $weight_by_default = $prop['value'];
            }
            /* Obtenemos dimensiones por defecto si existen */
			if ($prop['name'] == 'DimensionsByDefaultHeight') {
				$height_by_default = $prop['value'];
			}
			if ($prop['name'] == 'DimensionsByDefaultLarge') {
				$large_by_default = $prop['value'];
			}
			if ($prop['name'] == 'DimensionsByDefaultWidth') {
				$width_by_default = $prop['value'];
			}
            if ($prop['name'] == 'GoogleMapsApi') {
                $google_maps_api = $prop['value'];
            }
            if ($prop['name'] == 'LabelObservations') {
                if ($prop['value'] == 'on') {
                    $customer_message = substr($co_order->getFirstMessage(), 0, 80);
                } else {
                    $customer_message = '';
                }
            }
            if ($prop['name'] == 'TariffRadio') {
                if ($prop['value'] == 'on') {
                    $config_default_aduanera = 1;
                } else {
                    $config_default_aduanera = 0;
                }
            }
            if ($prop['name'] == 'AgreeToAlterReferences') {
                if ($prop['value'] == 'on') {
                    $option_labeldata = CorreosOficialConfigDao::getConfigValue('ShowLabelData', $order->id_shop);
                    switch ($option_labeldata['value']) {
                        case '1':
                            $ship_reference = $id_order;
                            break;
                        case '2':
                            $ship_reference = $co_order->reference;
                            break;
                        case '3':
                            $ship_reference = $co_order->reference;
                            break;
                        case '4':
                            $ship_reference = "";
                            break;
                        default:
                            $ship_reference = $co_order->reference;
                    }
                } else {
                    $ship_reference = $co_order->reference;
                }
            }
        }

        $orderUnits = $co_order->getUnits();
        // Calculamos peso
        $totalWeight = $cart->getTotalWeight();
        if ($totalWeight == 0) {
            $orderWeight = $weight_by_default;
        } else {
            $orderWeight = $totalWeight;
            if ($bultos > 1) {
                $orderWeight = '';
            }
        }

        // added_values: ocultamos el número de IBAN excepto últimos 4 dígitos
		if (isset($correos_order['added_values_cash_on_delivery_iban'])){
			$BankIni = substr($correos_order['added_values_cash_on_delivery_iban'], 0, -4);
			$BankFin = substr($correos_order['added_values_cash_on_delivery_iban'], -4);
			$correos_order['added_values_cash_on_delivery_iban'] = str_repeat('*', strlen($BankIni)) . $BankFin;
		}

        // Calculamos valor
        if ($bultos > 1) {
            $orderTotalValue = '';
        } else {
            $orderTotalValue = $co_order->getSubtotal();
        }

        // Url + token acceso a settings desde pedido
        $settingsToken = Tools::getAdminTokenLite('AdminCorreosOficialSettings');
        $shop_url_ssl = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
        $shop_admin_url = basename(_PS_ADMIN_DIR_);
        $slug = '/index.php?controller=AdminCorreosOficialSettings&token=';
        $co_url_settings = $shop_url_ssl . $shop_admin_url . $slug . $settingsToken;

        // Descripciones aduaneras
        $customs_desc_array = CorreosOficialConfigDao::getDefaultCustomsDescription();
        // Descripción aduanera por defecto
        $customs_desc_selected = CorreosOficialConfigDao::getConfigValue('DefaultCustomsDescription', $order->id_shop);
        // Número tarifario por defecto
        $customs_tariff_selected = CorreosOficialConfigDao::getConfigValue('Tariff', $order->id_shop);
        // Descripción del número tarifario
        $customs_tariff_description = CorreosOficialConfigDao::getConfigValue('TariffDescription', $order->id_shop);
        // Referencia aduanera del expedidor
        $customs_reference = CorreosOficialConfigDao::getConfigValue('ShippCustomsReference', $order->id_shop);

        $address->phone = str_replace(' ', '', $address->phone ? $address->phone : $address->phone_mobile);

        // Si no están definidas las definimoas a blanco
        $correos_order['shipping_number'] = isset($correos_order['shipping_number']) ? $correos_order['shipping_number'] : '';
        $correos_order['pickup_number'] = isset($correos_order['pickup_number']) ? $correos_order['pickup_number'] : '';
        $correos_order['AT_code'] = isset($correos_order['AT_code']) ? $correos_order['AT_code'] : '';

        $correos_return = !$correos_return ? ['shipping_number' => ''] : $correos_return;

		$carrier_type = isset($correos_return['carrier_type']) ? $correos_return['carrier_type'] : 'Correos';

        $address_paq = isset($address_paq) ? $address_paq : array();
        $address_paq['dir_paq'] = isset($address_paq['dir_paq']) ? $address_paq['dir_paq'] : '';
        $address_paq['loc_paq'] = isset($address_paq['loc_paq']) ? $address_paq['loc_paq'] : '';
        $address_paq['cp_paq'] = isset($address_paq['cp_paq']) ? $address_paq['cp_paq'] : '';

        $pickup_return_data_response['status'] = isset($pickup_return_data_response['status']) ? $pickup_return_data_response['status'] : '';
        $pickup_return_data_response['pickup_date'] = isset($pickup_return_data_response['pickup_date']) ? $pickup_return_data_response['pickup_date'] : '';
        $pickup_return_data_response['pickup_address'] = isset($pickup_return_data_response['pickup_address']) ? $pickup_return_data_response['pickup_address'] : '';
        $pickup_return_data_response['pickup_city'] = isset($pickup_return_data_response['pickup_city']) ? $pickup_return_data_response['pickup_city'] : '';
        $pickup_return_data_response['pickup_cp'] = isset($pickup_return_data_response['pickup_cp']) ? $pickup_return_data_response['pickup_cp'] : '';

        $pickup_to = isset($pickup_to) ? $pickup_to : '';
        $pickup_from = isset($pickup_from) ? $pickup_from : '';

        // Asignamos datos a la plantilla
		$this->context->smarty->assign('show_sender_modal', $showSenderModal);
        $this->context->smarty->assign('error_sender_name', $errorSenderName);
        $this->context->smarty->assign('error_company_name', $errorCompanyName);

        $this->context->smarty->assign("active_client", $active_client);
        $this->context->smarty->assign("order", $co_order);
        $this->context->smarty->assign("order_number", $id_order);
        $this->context->smarty->assign("order_id", $id_order);
        $this->context->smarty->assign("orderTotalValue", $orderTotalValue);
        $this->context->smarty->assign("order_done", $order_done);
        $this->context->smarty->assign("exist_return", $exist_return);
        $this->context->smarty->assign("correos_order", $correos_order);
        $this->context->smarty->assign("correos_return", $correos_return);

        $this->context->smarty->assign("carrier_type", $carrier_type);

        $this->context->smarty->assign("array_packages_order", $array_packages_order);
        $this->context->smarty->assign("array_packages_return", $array_packages_return);

        $this->context->smarty->assign("cash_on_delivery", $cash_on_delivery);
        $this->context->smarty->assign("cash_on_delivery_value", $cash_on_delivery_value);

        $this->context->smarty->assign("customer_message", $customer_message);

        $this->context->smarty->assign("carriers", $carriers);

        $this->context->smarty->assign("id_zone", $id_zone);

        $this->context->smarty->assign("carrier_order", $carrier_order);

        $this->context->smarty->assign("client_code", $client_code);

        $this->context->smarty->assign("default_sender", $default_sender);
        $this->context->smarty->assign("senders", $senders);

        $this->context->smarty->assign("customer", $customer);
        $this->context->smarty->assign("address", $address);
        $this->context->smarty->assign("countries", $countries);

        $this->context->smarty->assign("pickup", $pickup);
        $this->context->smarty->assign("pickup_data_response", $pickup_data_response);
        $this->context->smarty->assign("pickup_cancelable", $pickup_cancelable);

        $this->context->smarty->assign("order_returnable", $order_returnable);

        $this->context->smarty->assign("pickup_return", $pickup_return);
        $this->context->smarty->assign("saved_return_pickup", $saved_return_pickup);
        $this->context->smarty->assign("pickup_return_data_response", $pickup_return_data_response);
        $this->context->smarty->assign("pickup_return_cancelable", $pickup_return_cancelable);

        $this->context->smarty->assign("cancelable", $cancelable);
        $this->context->smarty->assign("delivered", $delivered);

        $this->context->smarty->assign("return_cancelable", $return_cancelable);

        $this->context->smarty->assign("return_status", $return_status);

        $this->context->smarty->assign("select_label_options", [
            LABEL_TYPE_THERMAL => 'Térmica',
            LABEL_TYPE_ADHESIVE => 'Adhesiva'
            /* LABEL_TYPE_HALF     => 'Medio folio', */
        ]);
        $this->context->smarty->assign("DefaultLabel", $DefaultLabel);

        $company = isset($correos_order["company"]) ? $correos_order["company"] :  $carrier_order["company"];

        $this->context->smarty->assign('select_label_options_format', [
            LABEL_FORMAT_STANDAR => 'Papel 4 etiquetas',
            LABEL_FORMAT_3A4 => 'Papel 3 etiquetas (Solo CEX)'
            /* LABEL_FORMAT_4A4     => '4/3A' */
        ]);

        $this->context->smarty->assign("bank_acc_number", $bank_acc_number);

        $this->context->smarty->assign("bultos", $bultos);
        $this->context->smarty->assign("bultos_return", $bultos_return);
        $this->context->smarty->assign("orderWeight", $orderWeight);
        
        // comprobamos si el carier está dentro de los disponibles para las dimiensiones por defecto
		$available_carriers_default_dimensions = ['S0179', 'S0176', 'S0178'];
        $available_carrier_d = (in_array($carrier_order['codigoProducto'],  $available_carriers_default_dimensions )) ? 1 :0;

		$this->context->smarty->assign('available_carrier_default_dimensions', $available_carrier_d);
		$this->context->smarty->assign('height_by_default', $height_by_default);
		$this->context->smarty->assign('large_by_default', $large_by_default);
		$this->context->smarty->assign('width_by_default', $width_by_default);

        $this->context->smarty->assign("orderUnits", $orderUnits);
        $this->context->smarty->assign("ship_reference", $ship_reference);
        $this->context->smarty->assign("order_reference", $ship_reference);
        $this->context->smarty->assign("google_maps_api", $google_maps_api);

        $this->context->smarty->assign("require_customs_doc", $require_customs_doc);
        $this->context->smarty->assign("is_international", $is_international);
        $this->context->smarty->assign("config_default_aduanera", $config_default_aduanera);

        $this->context->smarty->assign("customs_desc_array", $customs_desc_array);
        $this->context->smarty->assign("customs_desc_selected", $customs_desc_selected);
        $this->context->smarty->assign("customs_tariff_selected", $customs_tariff_selected);
        $this->context->smarty->assign("customs_tariff_description", $customs_tariff_description);
        $this->context->smarty->assign("customs_reference", $customs_reference);

        $this->context->smarty->assign("address_paq", $address_paq);
        $this->context->smarty->assign("cod_office", $cod_office);
        $this->context->smarty->assign("cod_homepaq", $cod_homepaq);

        $this->context->smarty->assign("is_code_at", $is_code_at);

        $this->context->smarty->assign('co_base_dir', $co_module_url_ps);

        $this->context->smarty->assign('co_url_settings', $co_url_settings);
        
        return $this->correosoficial_context->display($this->module_dir, 'views/templates/hook/admin-order.tpl');
    }

    private function isOrderReturnable($carrier_order, $customer_country)
    {
		// Comprobación envío admite devolución
		$order_returnable = false;

		// Para cualquier transportista ajeno a Correos/CEX
		if (empty($carrier_order['company'])) {
			return true;
		} elseif ($carrier_order['company'] == 'CEX') {
			// CEX admite ES/AD
			if ($customer_country == 'ES' || $customer_country == 'AD') {
				$order_returnable = true;
			}
		} elseif ($carrier_order['company'] == 'Correos') {
			// Correos admite ES/PT
			if ($customer_country == 'ES' || $customer_country == 'PT') {
				$order_returnable = true;
			}
		}

		return $order_returnable;
	}

    private function isATCode($carrier_order, $address, $default_sender) {
		// CódigoAT -> Exclusivo CEX
		if ($default_sender && $carrier_order['company'] == 'CEX'
		&& Country::getIsoById($address->id_country) == 'PT' && $default_sender['sender_iso_code_pais'] == 'PT') {
			return true;
		}
	
		return false;
	}

    private function fillCarriers($all_carriers, $carrier, $countries, $shipping_zone_rules, $carriers)
    {
        foreach ($all_carriers as $carrier) {

            foreach ($countries as $country) {

                $add_carrier = true;
                $exclude = false;

                // Excluir CEX90
                if ($shipping_zone_rules->excludeCEX90($country['iso_code'], $carrier['codigoProducto'])) {
                    $exclude = true;
                }

                // PAQ LIGHT INTERNACIONAL
                if ($shipping_zone_rules->excludeS360($country['iso_code'], $carrier['codigoProducto'])) {
                    $exclude = true;
                }

                // Portugal
                if ($shipping_zone_rules->excludeNationalProducts($country['iso_code'], $carrier['codigoProducto'])) {
                    $exclude = true;
                }

                //Internacionales
                if ($shipping_zone_rules->isInternational($country['iso_code'], $carrier['product_type']) && !$exclude) {
                    break;
                }

                // Nacionales
                if ($shipping_zone_rules->isNational($country['iso_code'], $carrier['codigoProducto']) && !$exclude) {
                    break;
                } else {
                    $add_carrier = false;
                }

            }

            if ($add_carrier) {
                $carriers[] = $carrier;
            }

        }

        return $carriers;
    }

    private function getCarrierOrder($carrier, $co_order, $id_zone, $correos_order, $id_shop)
    {
        $carrier_order = array(
            'id' => '',
            'name' => '',
            'active' => '',
            'delay' => '',
            'company' => '',
            'url' => '',
            'codigoProducto' => '',
            'product_type' => '',
            'max_packages' => '',
            'id_carrier' => ''
        );

        if ($carrier->external_module_name != 'correosoficial') {

            if (!empty($correos_order['id_order'])) {
                return CorreosOficialCarrier::getCarrierByProductId($correos_order['id_product']);
            }

            $id_carrier_product = CorreosOficialCarrier::getCarriersProducts($co_order->id_carrier, $id_zone, $id_shop);
            if (empty($id_carrier_product)) {
                return $carrier_order;
            } else {
                $carrier_order = CorreosOficialCarrier::getCarrierByProductId($id_carrier_product['id_product']);
            }
        } else {
            if (empty($correos_order)) {
                $carrier_order = CorreosOficialCarrier::getCarrier($co_order->id_carrier);
            } else {
                if ($correos_order['shipping_number'] != "") {
                    $carrier_order = CorreosOficialCarrier::getCarrierByProductId($correos_order['id_product']);

                } else {
                    $carrier_order = CorreosOficialCarrier::getCarrier($co_order->id_carrier);
                }
            }
        }

        return $carrier_order;
    }

    private static function getPickUpDataResponse($status, $cod_status = '', $pickup_date = '')
    {
        return array(
            'codEstado' => $cod_status,
            'status' => $status,
            'pickup_reference' => '',
            'pickup_date' => $pickup_date,
            'pickup_from_hour' => '',
            'pickup_to_hour' => '',
            'pickup_address' => '',
            'pickup_city' => '',
            'pickup_cp' => ''
        );
    }

    private static function returnPickupLastStatus($array_status)
    {
        $last_status = '';
        $count = 1;

        if (is_array($array_status) || is_object($array_status)) {

            $count = count($array_status->ns3TrazaSolicitudRecogidaEsporadica);

            if ($count == 1) {
                $last_status = end($array_status); //Nos quedamos con estado único
            } else {
                $last_status = $array_status->ns3TrazaSolicitudRecogidaEsporadica[$count - 1]; //Nos quedamos con el último estado
            }
        }
        return $last_status;
    }
}
