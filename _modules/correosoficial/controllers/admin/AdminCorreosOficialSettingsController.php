<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/config.inc.php';

require_once dirname(__FILE__) . '/../../classes/CorreosOficialCarrier.php';
require_once dirname(__FILE__) . '/../../classes/CorreosOficialCustomerData.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialSenders.php';

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialSendersDao.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialUserConfigurationDao.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialProductsDao.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialActiveCustomersDao.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Commons/ShippingMethodZoneRules.php';

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/functions.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/CorreosOficialUtils.php';
require_once dirname(__FILE__) . '/../../config.php';

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Cron/CronCorreosOficial.php';

class AdminCorreosOficialSettingsController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;

    private $dao;
    private $senders_dao;
    private $products_dao;

    protected $context;

    public function __construct()
    {

        $this->dao = new CorreosOficialDao();
        $this->senders_dao = new CorreosOficialSenders();

        $this->bootstrap = true;
        $this->display = 'view';

        parent::__construct();

        $this->toolbar_title = $this->l('Settings', 'AdminCorreosOficialSettingsController');
        $this->meta_title = $this->l('Settings Correos Oficial', 'AdminCorreosOficialSettingsController');

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        if (Tools::getValue('action') == 'getDataTable') {
            $this->getDataTableSenders();
        } else {
            $this->renderView();
        }

        $this->context = Context::getContext();
    }

    public function renderView()
    {
        // Enviar un header para excluir mod_pagespeed
        header('X-Mod-Pagespeed: off');

        $accepted = Hook::exec('actionCorreosAdminControllers');

        if ($accepted) {
            $template = $accepted;
        } else {
            global $co_no_soap_error;
            // Comprobación de la extensión SOAP cargada.
            CorreosOficialUtils::checkSoapInstalled($co_no_soap_error);
            $template = '/views/templates/admin/settings.tpl';
        }

        global $co_module_url_ps;

        $token = Tools::getAdminTokenLite('AdminCorreosOficialSettings');
        $home_token = Tools::getAdminTokenLite('AdminCorreosOficialHome');

        $this->context->smarty->assign('id_language', $this->context->language->id);
        $this->context->smarty->assign('id_employee', $this->context->employee->id);

        $this->context->smarty->assign('shop_context', Shop::getContext());
        $this->context->smarty->assign('id_shop', $this->context->shop->id);
        $this->context->smarty->assign('id_shop_group', $this->context->shop->id_shop_group);
        $this->context->smarty->assign('token', $token);
        $this->context->smarty->assign('home_token', $home_token);

        $this->context->smarty->assign('UploadLogoLabels');

        // Rellenamos checkbox y selectores de forma global en Ajustes.
        $this->fillSettingsCheckBoxAndSelectores($this->dao);

        // Rellenar selectores de contrato en formulario remitente
        $this->fillSenderFormContractSelector($this->dao);

        $this->getProducts($this->dao);

        $this->getZonesAndCarriers();

        $defaultLabel = CorreosOficialConfigDao::getConfigValue('DefaultLabel');
        $payment_method_seleted = CorreosOficialConfigDao::getConfigValue('CashOnDeliveryMethod');
        $customs_desc_array = CorreosOficialConfigDao::getDefaultCustomsDescription();
        $customs_desc_selected = CorreosOficialConfigDao::getConfigValue('DefaultCustomsDescription');
        $ShippCustomsReference = CorreosOficialConfigDao::getConfigValue('ShippCustomsReference');

        $select_label_options = array('0' => 'Adhesiva', /* '1' => 'Medio folio', */'2' => 'Térmica');

        // Inicializamos selected payments y rellenamos con los activos
        $select_payment_method = array(
            '0' => 'Ninguno',
        );
        foreach (Module::getPaymentModules() as $module) {
            $moduleObj = Module::getInstanceById((int)$module['id_module']);
            $select_payment_method[$module['name']] = $moduleObj->displayName;
        }

        // Obtenemos status de los pedidos
        $select_shipment_status_options = [];
        $user_configuration_dao = new CorreosOficialUserConfigurationDao();
        $records = $user_configuration_dao->getStatus($this->context->language->id);

        $ShipmentPreregistered = $this->dao->readSettings('ShipmentPreregistered');
        $ShipmentDelivered = $this->dao->readSettings('ShipmentDelivered');
        $ShipmentInProgress = $this->dao->readSettings('ShipmentInProgress');
        $ShipmentCanceled = $this->dao->readSettings('ShipmentCanceled');
        $ShipmentReturned = $this->dao->readSettings('ShipmentReturned');

        $i = 0;
        foreach ($records as $record) {
            $select_shipment_status_options[$i]['id_order_state'] = $record['id_order_state'];
            $select_shipment_status_options[$i]['name'] = $record['name'];
            $i++;
        }

        $this->context->smarty->assign("ShipmentPreregistered", $ShipmentPreregistered);
        $this->context->smarty->assign("ShipmentDelivered", $ShipmentDelivered);
        $this->context->smarty->assign("ShipmentInProgress", $ShipmentInProgress);
        $this->context->smarty->assign("ShipmentCanceled", $ShipmentCanceled);
        $this->context->smarty->assign("ShipmentReturned", $ShipmentReturned);

        $this->context->smarty->assign("select_shipment_status_options", $select_shipment_status_options);
        $this->context->smarty->assign('select_label_options', $select_label_options);
        $this->context->smarty->assign('DefaultLabel', $defaultLabel);
        $this->context->smarty->assign("select_payment_method", $select_payment_method);
        $this->context->smarty->assign('payment_method_seleted', $payment_method_seleted);
        $this->context->smarty->assign("customs_desc_array", $customs_desc_array);
        $this->context->smarty->assign("customs_desc_selected", $customs_desc_selected);
        $this->context->smarty->assign("ShippCustomsReference", $ShippCustomsReference);

        $TariffRadio = $this->dao->readSettings('TariffRadio');
        if ($TariffRadio->value == 'on') {
            $config_default_aduanera = 1;
        } else {
            $config_default_aduanera = 0;
        }
        $this->context->smarty->assign("config_default_aduanera", $config_default_aduanera);

        // Logos header

        // Ruta para recuperar el logo de las etiquetas
        $this->context->smarty->assign('co_base_dir', $co_module_url_ps);
        //plantilla
        return $this->context->smarty->fetch(dirname(__FILE__, 3) . $template);
    }

    /**
     * Rellenamos checkbox y selectores de forma global en Ajustes.
     * @param Oject $dao. El dao.
     */
    public function fillSettingsCheckBoxAndSelectores($dao)
    {
        $records = $dao->readRecord('correos_oficial_configuration', 'WHERE id_shop = '.$this->context->shop->id);

        // si no existen datos de configuración de la tienda actual, rellenamos todos con la nueva id_shop
        if (count($records) == 0) {
            $this->fillSettingsDbWithDefaultSettings($dao);
            $records = $dao->readRecord('correos_oficial_configuration', 'WHERE id_shop = '.$this->context->shop->id);
        }
        /**
         * Autorreleno de Selectores y checkbox
         */
        foreach ($records as $record) {

            $this->context->smarty->assign($record->name, $record->value);

            if ($record->name == 'BankAccNumberAndIBAN') {
                if (!empty($record->value)) {
                    $BankAccNumberAndIBAN = Crypto::decrypt($record->value);

                    //Se sustituyen los primeros caracteres por asteriscos y se dejan sólo los últimos cuatro números
                    $BankIni = substr($BankAccNumberAndIBAN, 0, -4);
                    $BankFin = substr($BankAccNumberAndIBAN, -4);
                    $BankAccNumberAndIBAN = str_repeat("*", strlen($BankIni)) . $BankFin;

                    $this->context->smarty->assign($record->name, $BankAccNumberAndIBAN);
                } else {
                    $this->context->smarty->assign($record->name, $record->value);
                }
            }

            if ($record->name == 'StatusSelector') {
                $this->context->smarty->assign($record->name, $record->value);
            }

            if ($record->name == 'CurrentState') {
                $this->context->smarty->assign($record->name, $record->value);
            }

            if ($record->name == 'DeliveredState') {
                $this->context->smarty->assign($record->name, $record->value);
            }

            if ($record->name == 'CancelledStateValue') {
                $this->context->smarty->assign($record->name, $record->value);
            }

            if ($record->name == 'ReturnedState') {
                $this->context->smarty->assign($record->name, $record->value);
            }

            // Si no ha seleccionado ningún idioma del selector toma el idioma del contexto
            if ($record->name == 'FormSwitchLanguage') {
                if (!empty($record->value)) {
                    $language_id = $record->value;
                } else {
                    $language_id = $this->context->language->id;
                }
            }

            if ($record->name == 'TranslatableInput') {
                $this->context->smarty->assign('TranslatableInputH', CorreosOficialUtils::restoreBadCharacters($record->value));
                $string_translated = CorreosOficialUtils::translateStringsFromDB($record->value, $language_id);
                $this->context->smarty->assign($record->name, $string_translated);
            }

            if ($record->type == 'checkbox' && ($record->value == 'true' || $record->value == 'on')) {
                $this->context->smarty->assign($record->name, 'checked');
            }

            if ($record->name == 'UploadLogoLabels') {
                if ($record->value == '') {
                    $this->context->smarty->assign('UploadLogoLabels', 'default.jpg');
                    $this->context->smarty->assign('uploadedLogo', '0');
                } else {
                    $result = Normalization::filterFiles($record->value);
                    if (strstr($result, 'Error: 12010')) {

                        $result = $this->l('Error 12010: Allowed formats: png, jpg, jpeg', 'AdminCorreosOficialSettingsController');
                        $this->context->smarty->assign('ErrorLogoLabels', $result);
                    } else {
                        $this->context->smarty->assign('UploadLogoLabels', $result);
                    }
                    $this->context->smarty->assign('uploadedLogo', '1');
                }
            }

            if ($record->name == 'CronInterval') {
                $this->context->smarty->assign('CronInterval', $record->value);
            }

        }

        $active_languages = CorreosOficialUtils::getActiveLanguages($this->context);

        CorreosOficialUtils::fillLanguagesSelector($active_languages, $this->context, $language_id);
    }

    /**
     * Funcionalidad para multitienda, en caso de que no existan dato de configuración los inicializamos con el valor vacío
     * @param Oject $dao. El dao.
     * @param Int $id_current_shop. Id de la nueva tienda creada.
     */
    public function fillSettingsDbWithDefaultSettings($dao) {
        // Recogemos los valores actuales de configuración de la tienda por defecto, la primera creada
        $records = $dao->readRecord('correos_oficial_configuration', 'WHERE id_shop = '.(int)Configuration::get('PS_SHOP_DEFAULT'));
        foreach ($records as $record) {
            // Los creamos para la tienda actual, la nueva creada
            $dao->createSettingRecord($record->name, $record->value, 'correos_oficial_configuration', $record->type);
        }
    }

    /**
     * Para conseguir el datatable de Senders
     */
    public function getDataTableSenders()
    {
        // Este código se tiene que mover a readSenders y comprobar en WC

        $recordsSQL = '
            SELECT
            a.*, b.CorreosContract, b.CorreosCustomer, c.CEXCustomer
			FROM
                ' . _DB_PREFIX_ . 'correos_oficial_senders a
			LEFT JOIN
                ' . _DB_PREFIX_ . 'correos_oficial_codes b ON a.correos_code = b.id
			LEFT JOIN
                ' . _DB_PREFIX_ . 'correos_oficial_codes c ON a.cex_code = c.id
                WHERE a.id_shop = '.$this->context->shop->id.'
        ';

        $records = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($recordsSQL);

        die(json_encode($records));
    }

    /**
     * Rellenar selectores de contrato en formulario remitente
     * @param Oject $dao. El dao.
     */
    public function fillSenderFormContractSelector($dao){

        $optionsCountsCorreos = $dao->readRecord(
            'correos_oficial_codes',
            "WHERE company='CORREOS' AND id_shop = ".$this->context->shop->id,
            "`id`, `CorreosContract`, `CorreosCustomer`",
            true
        );

        $optionsCountsCex = $dao->readRecord(
            'correos_oficial_codes',
            "WHERE company='CEX' AND id_shop = ".$this->context->shop->id,
            "`id`, `CEXCustomer`",
            true
        );

        $this->context->smarty->assign('optionsCorreos', $optionsCountsCorreos);
        $this->context->smarty->assign('optionsCex', $optionsCountsCex);

    }

    public function getProducts($dao)
    {
        // Se precargan los productos pero se controlan a nivel de ajax en el frontal
        $products_column1 = $dao->readRecord('correos_oficial_products as cop', "LEFT JOIN "._DB_PREFIX_."correos_oficial_products_shop as cops ON cop.id = cops.id_product AND cops.id_shop = ".$this->context->shop->id." WHERE company='CEX'", "cop.*, cops.active");
        $products_column2 = $dao->readRecord('correos_oficial_products as cop', "LEFT JOIN "._DB_PREFIX_."correos_oficial_products_shop as cops ON cop.id = cops.id_product AND cops.id_shop = ".$this->context->shop->id." WHERE company='CORREOS'", "cop.*, cops.active");
        $cex = true;
        $correos = true;

        $this->context->smarty->assign('exist_products', true);
        $this->context->smarty->assign('cex', $cex);
        $this->context->smarty->assign('correos', $correos);

        $this->context->smarty->assign('products_column1', $products_column1);
        $this->context->smarty->assign('products_column2', $products_column2);

    }

    // Obtenemos la relación de zonas y carriers y cada producto seleccionado para cada carrier
    public function getZonesAndCarriers()
    {
        $zones = Zone::getZones();
        $zonesandcarriers = array();
        $products = array();

        foreach ($zones as $zone) {
            $carriers = array();
            $carriers = Carrier::getCarriers($this->context->language->id, false, false, $zone['id_zone'], null, ALL_CARRIERS);

            $carriers_products = array();

            foreach ($carriers as $carrier) {
                $product_selected = $this->dao->getActiveProductCarrier($carrier['id_carrier'], $zone['id_zone']);

                if ($carrier['external_module_name'] == 'correosoficial') {
                    continue;
                }

                if (!empty($product_selected)) {
                    $carriers_products[] = array('id_carrier' => $carrier['id_carrier'],
                        'name' => $carrier['name'],
                        'active' => $carrier['active'],
                        'product_selected' => $product_selected[0]['id_product']);
                } else {
                    $carriers_products[] = array('id_carrier' => $carrier['id_carrier'],
                        'name' => $carrier['name'],
                        'active' => $carrier['active'],
                        'product_selected' => '0');
                }

                $products = $this->getActiveProductsForSelect($zone['id_zone']);

            }

            $zonesandcarriers[] = array('id_zone' => $zone['id_zone'], 'zonename' => $zone['name'], 'carriers' => $carriers_products, 'products' => $products);
        }

        $this->context->smarty->assign('zonesandcarriers', $zonesandcarriers);
    }

    public function getActiveProductsForSelect($id_zone)
    {
        $products2 = array();
        $add_carrier = false;
        
        $this->products_dao = new CorreosOficialProductsDao();
        $products = $this->products_dao->getActiveProducts(' WHERE cops.active = 1 and coc.id_shop = '.$this->context->shop->id);
        $shipping_zone_rules = new ShippingMethodZoneRules();

        $countries = Country::getCountriesByZoneId($id_zone, $this->context->language->id);

        foreach ($products as $product) {

            $product_obj = new CorreosOficialProductsDao();

            foreach ($countries as $country) {

                $add_carrier = true;
                $exclude = false;

                // Excluir CEX90
                if ($shipping_zone_rules->excludeCEX90($country['iso_code'], $product->codigoProducto)) {
                    $exclude = true;
                }

                // PAQ LIGHT INTERNACIONAL
                if ($shipping_zone_rules->excludeS360($country['iso_code'], $product->codigoProducto)) {
                    $exclude = true;
                }

                // Portugal
                if ($shipping_zone_rules->excludeNationalProducts($country['iso_code'], $product->codigoProducto)) {
                    $exclude = true;
                }

                //Internacionales
                if ($shipping_zone_rules->isInternational($country['iso_code'], $product->product_type) && !$exclude) {
                    break;
                }

                // Nacionales
                if ($shipping_zone_rules->isNational($country['iso_code'], $product->codigoProducto) && !$exclude) {
                    break;
                } else {
                    $add_carrier = false;
                }

            }

            if ($add_carrier) {
                $product_obj->id = $product->id;
                $product_obj->name = $product->name;
                $product_obj->product_type = $product->product_type;
                $products2[] = $product_obj;
            }

        }

        return $products2;
    }

}
