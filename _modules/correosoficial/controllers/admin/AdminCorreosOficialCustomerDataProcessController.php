<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__) . '/../../classes/CorreosOficialCustomerData.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/functions.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminCorreosOficialCustomerDataProcessController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;

    private $dao;

    public function __construct()
    {

        // Obtenemos un objetoDao
        $this->dao = new CorreosOficialCustomerData();

        parent::__construct();

        $action = Tools::getValue('action');
        $operation = Tools::getValue('operation');

        if ($operation === 'CorreosCustomerDataForm') {
            $this->getCorreosData();
        } elseif ($operation === 'CEXCustomerDataForm') {
            $this->getCEXData();
        } elseif ($action === 'DeleteCustomerCode') {
            $CorreosOficialCustomerCode = Normalization::normalizeData('CorreosOficialCustomerCode');
            $this->dao->deleteCustomerCode($CorreosOficialCustomerCode);
        } elseif ($action === 'getDataTableCustomerList') {
            $this->dao->getDataTableCustomerList($this->context->shop->id);
        } elseif ($action === 'getCustomerCode') {
            $this->getCode();
        } elseif ($action === 'getCustomerCodes') {
            $this->getCodes();
        } else {
            throw new Exception('ERROR CORREOS OFICIAL 10502: No se ha indicado un "action" para el formulario.');
        }
    }

    public function getCorreosData()
    {
        // Obtenemos campos de los formularios
        $idCorreos = Normalization::normalizeData('idCorreos');
        $CorreosContract = Normalization::normalizeData('CorreosContract');
        $CorreosCustomer = Normalization::normalizeData('CorreosCustomer');
        $CorreosKey = Normalization::normalizeData('CorreosKey');
        $CorreosUser = Normalization::normalizeData('CorreosUser', 'user');
        $CorreosPassword = Normalization::normalizeData('CorreosPassword', 'password');
        $CorreosOv2Code = Normalization::normalizeData('CorreosOv2Code', 'email');
        $Company = Normalization::normalizeData('CorreosCompany');

        // Los metemos en un array
        $fields = array(
            // Correos
            'idCorreos' => $idCorreos,
            'CorreosContract' => $CorreosContract,
            'CorreosCustomer' => $CorreosCustomer,
            'CorreosKey' => $CorreosKey,
            'CorreosUser' => $CorreosUser,
            'CorreosPassword' => $CorreosPassword,
            'CorreosOv2Code' => $CorreosOv2Code,
            'Company' => $Company,
            'CEXCustomer' => 'n/a',
            'CEXUser' => 'n/a',
            'CEXPassword' => 'n/a',
            'id_shop' => $this->context->shop->id
        );

        // Estos datos tienen que ser los asociados al remitente activo
        (new Analitica())->configurationCall('undefined');

        $result = $this->dao->addCustomerCode($CorreosCustomer, $Company, $fields);
        $this->badResponse($result);
    }

    public function getCEXData()
    {
        $idCEX = Normalization::normalizeData('idCEX');
        $CEXCustomer = Normalization::normalizeData('CEXCustomer');
        $CEXUser = Normalization::normalizeData('CEXUser', 'user');
        $CEXPassword = Normalization::normalizeData('CEXPassword', 'password');
        $Company = Normalization::normalizeData('CEXCompany');

        $fields = array(
            'idCEX' => $idCEX,
            'CEXCustomer' => $CEXCustomer,
            'CEXUser' => $CEXUser,
            'CEXPassword' => $CEXPassword,
            'Company' => $Company,
            'CorreosContract' => 'n/a',
            'CorreosCustomer' => 'n/a',
            'CorreosKey' => 'n/a',
            'CorreosUser' => 'n/a',
            'CorreosPassword' => 'n/a',
            'CorreosOv2Code' => 'n/a',
            'id_shop' => $this->context->shop->id
        );

        // Estos datos tienen que ser los asociados al remitente activo
        (new Analitica())->configurationCall('undefined');
        
        $result = $this->dao->addCustomerCode($CEXCustomer, $Company, $fields);
        $this->badResponse($result);
    }

    // Obtenemos información de un contrato (AJAX)
    public function getCode()
    {
        $id = Tools::getValue('id');
        $code = $this->dao->readRecord('correos_oficial_codes', "WHERE id=" . $id . " LIMIT 1");

        // si tenemos resultados obtenemos el primer registro
        if ($code) {
            die(json_encode($code[0]));
        }else{
            die(json_encode([]));
        }
    }

    // Obtenemos los contratos (AJAX)
    public function getCodes()
    {

        $optionsCountsCorreos = $this->dao->readRecord(
            'correos_oficial_codes',
            "WHERE company='CORREOS' AND id_shop = ".$this->context->shop->id,
            "`id`, `CorreosContract`, `CorreosCustomer`",
            true
        );

        $optionsCountsCex = $this->dao->readRecord(
            'correos_oficial_codes',
            "WHERE company='CEX' AND id_shop = ".$this->context->shop->id,
            "`id`, `CEXCustomer`",
            true
        );

        // Componemos array de contratos
        $contracts = array(
            'correos' => $optionsCountsCorreos,
            'cex' => $optionsCountsCex
        );

        die(json_encode($contracts));
    }

    public function badResponse($result) {
		if (!$result) {
			$mensajeRetorno = array(
				'codigoRetorno' => '10502',
				'mensajeRetorno' => $this->l('The customer code you are triying to save, is already in use'),
				'status_code' => '409',
			);
	
			echo json_encode($mensajeRetorno);
			die();
		}
	}
}
