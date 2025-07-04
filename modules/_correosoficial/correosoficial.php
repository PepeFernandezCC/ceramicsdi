<?php

/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
define("MODULE_CORREOS_OFICIAL_PATH", __FILE__);

require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialCheckout.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialOrders.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialOrder.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialCarrier.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialSenders.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/Analitica.php';

require_once _PS_MODULE_DIR_ . 'correosoficial/src/CorreosProducts.php';

require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Dao/CorreosOficialActiveCustomersDao.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Dao/CorreosOficialUtilitiesDao.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Dao/CorreosOficialOrderDao.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Dao/CorreosOficialProductsDao.php';

require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/CorreosOficialUtils.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/config.php';

require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Correos/CorreosRest.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Correos/CorreosSoap.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Cex/CexRest.php';

require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Commons/NeedCustoms.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Commons/Normalization.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Commons/ShippingMethodZoneRules.php';

require_once _PS_MODULE_DIR_ . 'correosoficial/vendor/ecommerce_common_lib/Cron/CronCorreosOficial.php';

require_once _PS_MODULE_DIR_ . 'correosoficial/sql/CorreosOficialIndexes.php';

class Correosoficial extends CarrierModule
{
   /* Versiones 1.7.6.x no implementa estas constante en la clase logger */
   private const LOG_SEVERITY_LEVEL_INFORMATIVE = 1;
   private const LOG_SEVERITY_LEVEL_WARNING = 2;
   private const LOG_SEVERITY_LEVEL_ERROR = 3;
   private const LOG_SEVERITY_LEVEL_MAJOR = 4;

    protected $config_form = false;
    protected $opc_counter;
    
    public $hours_to_comprove = 12;

    public $tabs = [
        [
            'name' => 'Ajustes',
            // One name for all langs
            'class_name' => 'Correosoficial',
            'visible' => true,
            'parent_class_name' => 'AdminDashboard'
        ]
    ];

    public function __construct()
    {
        $moduleVersionName = 'CORREOS_OFICIAL_VERSION';
        $this->name = 'correosoficial';
        $this->tab = 'shipping_logistics';
        $this->author = 'Grupo Correos';
        $this->old_version = Configuration::get($moduleVersionName);
        // Versión para el menú Modulos de Prestashop
        $this->version = $this->getModuleVersion();
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_
        ];

        global $co_no_soap_error;
        $co_no_soap_error = $this->l('ERROR 12050: To use Correos webservice credentials, you must have the SOAP feature installed. Please contact your hosting for more information.', 'correosoficial');

        $this->opc_counter = 1;

        /* Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)*/
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('Correos Ecommerce', 'correosoficial');
        $this->description = $this->l('Correos and Correos Express Spain module for shipment management. It integrates national and international parcel services, making the management of your orders a quick and easy task.', 'correosoficial');

        $this->confirmUninstall = $this->l('Do you confirm that you want to uninstall it?', 'correosoficial');
        
        // CORREOS_OFICIAL_VERSION
        define($moduleVersionName,$this->version);

        // actualizar en caso de no tener una version definina o que sea diferente de la actual.
        if (!empty($this->old_version) && $this->old_version!= $this->version) {
            self::mustUpdate($this);
            $this->deleteLabelFromTables();
        }

    }

    public static function mustUpdate($module)
    {
        if (Configuration::get('CORREOS_OFICIAL_UPDATE_STATUS') === 'error') {
            return false; // Detiene la ejecución
        }

        $from_version_to_old_version = $module->name ." ".$module->version. ' desde la versión ' . $module->old_version;

        try {
            CorreosOficialUtils::writeInstallErrorLog('Actualización del módulo, método CorreosOficial::mustUpdate: ' . $from_version_to_old_version);
            // Para versiones menores que 1.2
            include_once dirname(__FILE__) . '/upgrade/upgrade_minor_to_1.2.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade_minor_to_1.2 OK');

            // Producto CEX Paq 24 Oficina Elegida
            include_once dirname(__FILE__) . '/upgrade/upgrade_paq24.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade_paq24 OK');

            // Se añade control de versiones 1.3
            include_once dirname(__FILE__) . '/upgrade/upgrade_1_3_0.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade_1_3_0 OK');

            // Se añade multicliente 1.4
            include_once dirname(__FILE__) . '/upgrade/multicliente.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade multicliente OK');

            include_once dirname(__FILE__) . '/upgrade/upgrade_1_5_0.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade_1_5_0 OK');

            include_once dirname(__FILE__) . '/upgrade/upgrade_1_5_3.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade_1_5_3 OK');
            
            include_once dirname(__FILE__) . '/upgrade/upgrade_1_5_4.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade_1_5_4 OK');
            
            include_once dirname(__FILE__) . '/upgrade/upgrade_1_5_5.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade_1_5_5 OK');
            
            // Multitienda
            include_once dirname(__FILE__) . '/upgrade/upgrade_1_6_0.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade_1_6_0 OK');
            
            include_once dirname(__FILE__) . '/upgrade/upgrade_1_6_2.php';
            CorreosOficialUtils::writeInstallErrorLog('upgrade_1_6_2 OK');

            // instlar nuevos controladores 
            $module->installTab('AdminCorreosOficialOrder', $module->l('Order', 'correosoficial'), 'AdminCorreosOficialCronProcess', 0);
            CorreosOficialUtils::writeInstallErrorLog('Controlador Order OK');
            
            $module->installTab('AdminCorreosOficialUtilitiesAjax', $module->l('Utilities Ajax', 'correosoficial'), 'AdminCorreosOficialCronProcess', 0);
            CorreosOficialUtils::writeInstallErrorLog('Controlador Utilities Ajax OK');

            CorreosOficialIndexes::checkIfIndexesExists();
            CorreosOficialUtils::writeInstallErrorLog('Indices Ok');

            // Si va bien, resetea el estado de la actualización
            Configuration::updateValue('CORREOS_OFICIAL_UPDATE_STATUS', 'success');

            // Dejar este log siempre al final.
            CorreosOficialUtils::writeInstallErrorLog('Actualización completa');
            PrestaShopLogger::addLog('Actualización del módulo, método CorreosOficial::mustUpdate Correcta: ' . $from_version_to_old_version,  self::LOG_SEVERITY_LEVEL_INFORMATIVE);

        } catch (Exception $e) {
            Configuration::updateValue('CORREOS_OFICIAL_UPDATE_STATUS', 'error');
            PrestaShopLogger::addLog('Actualización del módulo, método CorreosOficial::mustUpdate Fallida: ' . $from_version_to_old_version.". Vea el archivo modules/correosoficial/sql/install_error.log",  self::LOG_SEVERITY_LEVEL_WARNING);
            PrestaShopLogger::addLog('Actualización del módulo, método CorreosOficial::mustUpdate Fallida: ' . $e->getMessage(), self::LOG_SEVERITY_LEVEL_ERROR);
            
            // Muestra un mensaje de error en la ventana modal de instalación
            $context = Context::getContext();
            $context->controller->errors[] = $module->l('An error occurred during the module update. Please check the logs.');
            
            // Puede no existir la tabla con nueva instalación;
            return false;
        }

        // Actualizamos versiones
        Configuration::updateValue('CORREOS_OFICIAL_VERSION', CORREOS_OFICIAL_VERSION);
        Db::getInstance()->update('module', ['version' => CORREOS_OFICIAL_VERSION], 'name="' . $module->name . '"');

        return true;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        global $install_error;

        Configuration::updateValue('CORREOS_OFICIAL_VERSION', CORREOS_OFICIAL_VERSION);

        // Installing tables in db
        include_once dirname(__FILE__) . '/sql/install.php';

        // Excepto 1062: Errores de clave duplicada, se muestra excepción
        if (!empty($install_error) && !strstr($install_error, '1062')) {
            error_log('INSTALL_ERROR_2: ' . $install_error);
            $this->_errors[] = $this->l($install_error);
            return false;
        }

        if (!extension_loaded('curl')) {
            $this->_errors[] = $this->l('You have to enable the CURL extension on your server to install this module', 'correosoficial');
            return false;
        }

        Configuration::updateValue('CORREOSOFICIAL_LIVE_MODE', false);

        $languages = Language::getLanguages(false);

        return parent::install() &&
            $this->installTab('AdminCorreosOficialSettings', $this->l('Settings', 'correosoficial'), 'AdminCorreosOficialCronProcess') &&
            $this->installTab('AdminCorreosOficialUtilities', $this->l('Utilities', 'correosoficial'), 'AdminCorreosOficialCronProcess') &&
            $this->installTab('AdminCorreosOficialNotifications', $this->l('Notifications', 'correosoficial'), 'AdminCorreosOficialCronProcess') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayAdminOrder') &&
            $this->registerHook('displayAdminAfterHeader') &&
            $this->registerHook('actionCarrierUpdate') &&
            $this->registerHook('displayCarrierExtraContent') &&
            $this->registerHook('actionValidateStepComplete') &&
            $this->registerHook('actionCorreosAdminControllers') &&
            $this->registerHook('displaybackOfficeHeader') &&
            $this->registerHook('actionAdminControllerSetMedia');
    }

    public function deleteLabelFromTables()
    {
        // Obtener la instancia de la base de datos de PrestaShop
        $db = Db::getInstance();

        $tableExists = Db::getInstance()->ExecuteS('SHOW TABLES LIKE "'. _DB_PREFIX_ .'correos_oficial_saved_orders"');

        if (!$tableExists) {
            return false;
        }

        $table_orders = _DB_PREFIX_ . 'correos_oficial_saved_orders';
        $table_returns = _DB_PREFIX_ . 'correos_oficial_saved_returns';
        
        try {
            $column_exists_orders = $db->executeS("SHOW COLUMNS FROM `$table_orders` LIKE 'label'");
            $column_exists_returns = $db->executeS("SHOW COLUMNS FROM `$table_returns` LIKE 'label'");

            if ($column_exists_orders || $column_exists_returns) {
                if ($column_exists_orders) {
                    $db->execute("ALTER TABLE `$table_orders` DROP COLUMN label");
                }
                if ($column_exists_returns) {
                    $db->execute("ALTER TABLE `$table_returns` DROP COLUMN label");
                }
                error_log("CORREOS ECOMMERCE PRESTASHOP: SE HAN ELIMINADO CORRECTAMENTE EL CAMPO LABEL DE LAS TABLAS  $table_orders y  $table_returns");
            }
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }

    public function uninstall()
    {

        // PS configs
        Configuration::deleteByName('CORREOS_OFICIAL_VERSION');
        Configuration::deleteByName('CORREOS_OFICIAL_LAST_NOTIFICATIONS_CALL');
        Configuration::deleteByName('CORREOS_OFICIAL_UPDATE_STATUS');

        $analitica = (new Analitica())->uninstallCall();
        // Uninstall tables
        include_once dirname(__FILE__) . '/sql/uninstall.php';
        return parent::uninstall() &&
            $this->uninstallTab('AdminCorreosOficialSettings') &&
            $this->uninstallTab('AdminCorreosOficialUtilities') &&
            $this->uninstallTab('AdminCorreosOficialNotifications') &&
            $this->uninstallTab('AdminCorreosOficialCronProcess');
    }

    public function disable($force_all = false)
    {
        if (Analitica::gdprAccepted()) {
            (new Analitica())->disableCall();
        }

        return parent::disable($force_all);
    }

    public function enable($force_all = false)
    {
        if (Analitica::gdprAccepted()) {
            (new Analitica())->moduleRecord();
        }

        return parent::enable($force_all);
    }

    public function installTab($class_name, $name, $parent, $active = 1)
    {
        $db =  \Db::getInstance();
        $language_ids = Language::getIDs(false);
        $needParent = Tab::getIdFromClassName('AdminCorreosOficialCronProcess');

        $query = "SELECT id_tab FROM " . _DB_PREFIX_ . "tab WHERE class_name='SELL'";

        if (empty($needParent)) {
            $main_tab = new Tab();
            $main_tab->name = array_fill_keys($language_ids, 'Correos Ecommerce');
            $main_tab->class_name = 'AdminCorreosOficialCronProcess';
            // Instalamos el módulo bajo el menú "VENDER" en la administración
            $main_tab->id_parent = $db->getValue($query);
            $main_tab->position = 6;
            $main_tab->module = $this->name;

            if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
                $main_tab->icon = 'ico';
            }

            $main_tab->save();
        }

        $tab = new Tab();
        $tab->active = $active;
        $tab->id_parent = (int) Tab::getIdFromClassName($parent);
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        $tab->class_name = $class_name;
        $tab->module = $this->name;

        return $tab->add();
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function uninstallTab($class_name)
    {
        $tab = new Tab();

        return $tab->delete();
    }

    /**
     * Hook Detalles del usuario
     */
    public function hookOrderDetailDisplayed($params)
    {
        global $co_module_url_ps;

        // Carrier de prestashop
        $carrier = new Carrier((int) ($params['order']->id));

        if ($carrier->external_module_name != 'correosoficial') {
            return false;
        }

        $saved_order = new CorreosOficialOrderDao();
        $saved_order_record = $saved_order->getShippingNumberByOrderId($params['order']->id);

        // Salimos si el envío todavía no se ha prerregistrado.
        if (!isset($saved_order_record[0])) {
            return;
        }

        $shipping_number = $saved_order_record[0]->shipping_number;

        $this->context->smarty->assign('co_base_dir', $co_module_url_ps);
        $this->context->smarty->assign('shipping_number', $shipping_number);

        return $this->display(__FILE__, 'views/templates/hook/orderDetail.tpl');
    }

    /**
     * Hook Inicio, Ajustes, Utilidades
    -*/
    public function hookdisplaybackOfficeHeader($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/tab.css');
        
        /* ps_edition_basic: módulo que cambia el backoffice a "new theme (blanco)". Cambiamos el logo a "oscuro" */
        if (Module::isInstalled('ps_edition_basic') && Module::isEnabled('ps_edition_basic')) {
            $this->context->controller->addCSS($this->_path . 'views/css/tab_new_theme_admin.css');
        }

        /* Lo cargamos si es de AdminOrder o es un controlador de CorreosOficial */
        if ($this->context->controller->php_self == 'AdminOrders' || Tools::getValue('controller') == 'AdminOrders' || stristr(Tools::getValue('controller'), $this->name)) {
            $this->context->controller->addCSS($this->_path . 'views/css/global.css');
        }

        // Se fuerza la carga de Jquery antes que los javascript del módulo
        if (stristr(Tools::getValue('controller'), $this->name)) {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path . 'js/prestashop.js');

            $this->context->controller->addCSS($this->_path . 'views/css/bootstrap.min.css');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');

            $this->context->controller->addJS($this->_path . 'views/js/bootstrap.bundle.min.js');
            $this->context->controller->addJS($this->_path . 'js/back.js');
            $this->context->controller->addJS($this->_path . 'js/execute-cron.js');

            $controller_name = Tools::getValue('controller');

            switch ($controller_name) {
                case 'AdminCorreosOficialHome':
                    $this->context->controller->addCSS($this->_path . 'views/css/home.css');

                    $this->context->controller->addJS($this->_path . 'views/js/jquery.validate.min.js');
                    $this->context->controller->addJS($this->_path . 'views/js/commons/custom-validators.js');
                    $this->context->controller->addJS($this->_path . 'views/js/commons/home.js');
                    break;
                case 'AdminCorreosOficialSettings':
                    $this->context->controller->addCSS($this->_path . 'views/css/settings.css');

                    $this->context->controller->addJS($this->_path . 'views/js/jquery.validate.min.js');
                    $this->context->controller->addJS($this->_path . 'views/js/commons/custom-validators.js');
                    $this->context->controller->addJS($this->_path . 'views/js/commons/common-settings.js');
                    $this->context->controller->addJS($this->_path . 'views/js/commons/customs-processing.js');

                    $this->context->controller->addJS($this->_path . 'js/customer-data.js');
                    $this->context->controller->addJS($this->_path . 'js/senders.js');
                    $this->context->controller->addJS($this->_path . 'js/user-configuration.js');
                    $this->context->controller->addJS($this->_path . 'js/cron.js');
                    $this->context->controller->addJS($this->_path . 'js/products.js');
                    $this->context->controller->addJS($this->_path . 'js/zones-carriers.js');
                    break;
                case 'AdminCorreosOficialUtilities':
                    $this->context->controller->addCSS($this->_path . 'views/css/utilities.css');

                    $this->context->controller->addJS($this->_path . 'js/utilities.js');
                    break;
                case 'AdminCorreosOficialFaq':
                    break;
                case 'AdminCorreosOficial':
                    $this->context->controller->addJS($this->_path . 'views/js/ajax.js');
                    break;
                case 'AdminCorreosOficialNotifications':
                    $this->context->controller->addCSS($this->_path . 'views/css/notification.css');
                    $this->context->controller->addJS($this->_path . 'js/notification.js');
                    break;
                default:
            }
        }
    }

    /**
     * Hook de Checkout
     */
    public function hookDisplayHeader()
    {
        if ($this->context->controller->php_self == 'order') {
            $this->context->controller->addCSS($this->_path . 'views/css/checkout.css');
            $this->context->controller->addCSS($this->_path . 'views/css/global.css');


            $google_api_key = CorreosOficialConfigDao::getConfigValue('GoogleMapsApi');
            $this->context->controller->registerJavascript('modules-correosoficial', 'https://maps.googleapis.com/maps/api/js?callback=Function.prototype&key=' . $google_api_key, ['server' => 'remote', 'position' => 'bottom', 'priority' => 20, 'attributes' => 'async']);
            $this->context->controller->addJS($this->_path . 'js/prestashop.js');
            $this->opcIsActive();
        }
    }

    public function opcIsActive()
    {
        $module = Module::getInstanceByName('onepagecheckoutps');

        if ($module && $module->active) {
            $this->context->controller->addCSS($this->_path . 'views/css/opc.css');

            if ($this->themeExists()) {
                $this->context->controller->addCSS(
                    $this->_path . 'views/css/warehouse-theme-enabled.css'
                );
            }
            return 'active';
        }

        $this->context->controller->addJS($this->_path . 'js/checkout.js');
        return 'no active';
    }

    public function themeExists()
    {
        $query = "SELECT theme_name FROM " . CorreosOficialUtils::getPrefix()
            . "shop WHERE theme_name LIKE '%warehouse%' AND id_shop = 1";

        $record = Db::getInstance()->getRow($query);

        return !empty($record['theme_name']);
    }

    public function hookActionCarrierUpdate($params)

    {
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            $shopAssociations = Tools::getValue('checkBoxShopAsso_carrier');
            if (isset($shopAssociations)) {
                foreach (Shop::getShops(true, null, true) as $shop) {
                    $active = (array_key_exists($shop, $shopAssociations)) ? 1 : 0;
                    CorreosOficialCarrier::updateProductFromCarriersPS($params['id_carrier'], $params['carrier']->id, $active, $shop);
                }
            }
        } else {
            // Actualización de carrier en ps_correos_oficial_products (correosoficial)
            CorreosOficialCarrier::updateProductFromCarriersPS($params['id_carrier'], $params['carrier']->id, $params['carrier']->active, $this->context->shop->id);
        }

        // Actualización de carrier en ps_orders (prestashop)
        CorreosOficialCarrier::updateOrdersFromCarriersPS('orders', $params['id_carrier'], $params['carrier']->id);

        // Actualización de carrier en ps_correos_oficial_orders (correosoficial)
        CorreosOficialCarrier::updateOrdersFromCarriersPS('correos_oficial_orders', $params['id_carrier'], $params['carrier']->id);
    }

    public function hookActionAdminControllerSetMedia($params)
    {

        /* Para versiones inferiores a 1.7.7.0 */
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            // Se añade isset para contemplar bug de PS 1.7.6.2
            if (Tools::getValue('vieworder') || isset($_GET['vieworder'])) {
                $this->context->controller->addJS($this->_path . 'views/js/popper.min.js');

                // WIP: INC000052980897 No funciona botón Modificar Transportista en pedido
                // if (version_compare(_PS_VERSION_, '1.7.6.8', '<')) {
                //     $this->context->controller->addJS($this->_path . 'views/js/bootstrap.min.js');
                // }

                $this->context->controller->addCSS($this->_path . 'views/css/datatables/jquery.dataTables.css');
                $this->context->controller->addCSS($this->_path . 'views/css/admin-order.css');

                $this->context->controller->addJS($this->_path . 'js/prestashop.js');
                $this->context->controller->addJS($this->_path . 'views/js/jquery.validate.min.js');
                $this->context->controller->addJS($this->_path . 'views/js/commons/custom-validators.js');
                $this->context->controller->addJS($this->_path . 'js/library/admin-order.js');
                $this->context->controller->addJS($this->_path . 'js/admin-order.js');
                $this->context->controller->addJS($this->_path . 'js/execute-cron.js');
                $this->context->controller->addJS($this->_path . 'views/js/datatables/jquery.dataTables.js');
            }
            /* Para versiones superiores a 1.7.7.0 */
        } else {
            if (Tools::getValue('controller') == 'AdminOrders' && Tools::getValue('id_order') != false) {
                $this->context->controller->addJS($this->_path . 'views/js/popper.min.js');
                $this->context->controller->addJS($this->_path . 'views/js/bootstrap.min.js');

                $this->context->controller->addCSS($this->_path . 'views/css/datatables/jquery.dataTables.css');
                $this->context->controller->addCSS($this->_path . 'views/css/admin-order.css');

                $this->context->controller->addJS($this->_path . 'js/prestashop.js');
                $this->context->controller->addJS($this->_path . 'views/js/jquery.validate.min.js');
                $this->context->controller->addJS($this->_path . 'views/js/commons/custom-validators.js');
                $this->context->controller->addJS($this->_path . 'js/library/admin-order.js');
                $this->context->controller->addJS($this->_path . 'js/admin-order.js');
                $this->context->controller->addJS($this->_path . 'js/execute-cron.js');
                $this->context->controller->addJS($this->_path . 'views/js/datatables/jquery.dataTables.js');

                $google_api_key = CorreosOficialConfigDao::getConfigValue('GoogleMapsApi');
                if($google_api_key){
                    $this->context->controller->addJS('https://maps.googleapis.com/maps/api/js?callback=Function.prototype&key=' . $google_api_key, true);
                }
            }
        }
    }

    public function hookDisplayAdminOrder()
    {
        global $co_module_url_ps;
        $module_dir = _PS_MODULE_DIR_ . '/correosoficial/';
        require_once dirname(__FILE__) . '/classes/CorreosOficialAdminOrderHook.php';

        $coaho = new CorreosOficialAdminOrderHook($this->context, $module_dir, $this);

        $analitica = new Analitica();
        if (Module::isEnabled($this->name) && $analitica->gdpr(Tools::getAllValues())) {
            $this->smarty->assign("co_base_dir", $co_module_url_ps);
            return $this->display(__FILE__, 'views/templates/admin/correosGdpr.tpl');
        }
        return $coaho->hookDisplayAdminOrder();
    }

    /** Hook CheckOut para comprobar el código postal en office/citypaq */
    public function hookActionValidateStepComplete($params)
    {
        if ($params['step_name'] != 'delivery') {
            return;
        }
        $id_carrier = $params['cart']->id_carrier;
        if (empty($id_carrier)) {
            $params['completed'] = false;
        } else {
            $carrier = CorreosOficialCarrier::getCarrier($id_carrier);
            $carrier_type = $carrier['product_type'];

            if ($carrier_type == 'citypaq') {
                if ($params['request_params']['SearchCityPaqByCPInput_' . $id_carrier] == '') {
                    $this->context->controller->errors[] = $this->l('Find and select a CityPaq', 'correosoficial');
                    $params['completed'] = false;
                } else {
                    if ($params['request_params']['CityPaqSelect_' . $id_carrier] == 'none') {
                        $this->context->controller->errors[] = $this->l('Find and select a CityPaq', 'correosoficial');
                        $params['completed'] = false;
                    }
                }
            }

            if ($carrier_type == 'office') {
                if ($params['request_params']['SearchOfficeByCPInput_' . $id_carrier] == '') {
                    $this->context->controller->errors[] = $this->l('Find and select an office', 'correosoficial');
                    $params['completed'] = false;
                } else {
                    if ($params['request_params']['OfficeSelect_' . $id_carrier] == 'none') {
                        $this->context->controller->errors[] = $this->l('Find and select an office', 'correosoficial');
                        $params['completed'] = false;
                    }
                }
            }
        }
    }

    /** Hook CheckOut para mostrar carriers Correos/CEX */
    public function hookdisplayCarrierExtraContent($params)
    {

        global $co_module_url_ps;
        $return = false;

        $carrier = $params['carrier'];

        if (!isset($carrier['external_module_name']) && $carrier['external_module_name'] != $this->name) {
            $return = true;
        }

        $cart = $params['cart'];
        if (!$cart->id_address_delivery) {
            $return = true;
        }

        $result = CorreosOficialCheckout::getCarrierParams($carrier['id']);

        if (!isset($result)) {
            $return = true;
        }

        if ($return) {
            return false;
        }

        $google_maps_config = CorreosOficialCheckout::getValueConf('GoogleMapsApi', 'correos_oficial_configuration');
        $google_maps_config = $google_maps_config['value'];
        $show_maps = false;
        $defined_google_api_key = false;

        if (!empty($google_maps_config)) {
            $show_maps = true;
            $defined_google_api_key = true;
        }
        $this->context->smarty->assign("show_maps", $show_maps);
        $this->context->smarty->assign("defined_google_api_key", $defined_google_api_key);

        $aviso_aduanas_interiores = CorreosOficialCheckout::getValueConf('MessageToWarnBuyer', 'correos_oficial_configuration');
        $aviso_aduanas_interiores = $aviso_aduanas_interiores['value'];
        $this->context->smarty->assign("aviso_aduanas_interiores", $aviso_aduanas_interiores);

        $customsMessage = CorreosOficialCheckout::getValueConf('TranslatableInput', 'correos_oficial_configuration');
        $string_translated = CorreosOficialUtils::translateStringsFromDB($customsMessage['value'], $this->context->language->id);
        $this->smarty->assign(array('string_translated' => $string_translated));

        $default_sender = CorreosOficialSenders::getDefaultSender();

        // Si no tenemos remitente por defecto configurado en Ajustes -> Remitentes, ponemos valores por defecto
        if (empty($default_sender)) {
            $sender_country = 'ES';
            $sender_postal_code = '';
        } else {
            $sender_country = $default_sender['sender_iso_code_pais'];
            $sender_postal_code = $default_sender['sender_cp'];
        }

        // Dirección de envío
        $address = new Address($cart->id_address_delivery);

        // Aduanas
        $customer_postal_code = $address->postcode;
        $country = new Country($address->id_country);
        $customer_country = $country->iso_code;

        $require_customs_doc = NeedCustoms::isCustomsRequired(
            $sender_postal_code,
            $customer_postal_code,
            $sender_country,
            $customer_country
        );
        $this->context->smarty->assign("require_customs_doc", $require_customs_doc);

        $this->context->smarty->assign('co_base_dir', $_SERVER['DOCUMENT_ROOT'] . $co_module_url_ps);
        switch ($result['product_type']) {
            case 'homedelivery':
                $params_tpl = array(
                    'carrier_type' => 'homedelivery',
                    'id_carrier' => (int) $carrier['id']
                );
                break;
            case 'office':
                $params_tpl = array(
                    'carrier_type' => 'office',
                    'id_carrier' => (int) $carrier['id']
                );
                break;
            case 'citypaq':
                $params_tpl = array(
                    'carrier_type' => 'citypaq',
                    'id_carrier' => (int) $carrier['id']
                );
                break;
            case 'international';
                $params_tpl = array(
                    'carrier_type' => 'international',
                    'id_carrier' => (int) $carrier['id']
                );
                break;
            default:
                $params_tpl = array(
                    'carrier_type' => '',
                    'id_carrier' => (int) $carrier['id']
                );
                break;
        }

        /**
         * Conseguimos el código postal de la dirección de envío
         */
        $id_address_delivery = $params['carrier']['product_list'][0]['id_address_delivery'];
        $address = new Address($id_address_delivery);
        $address_fields = $address->getFields();

        /**
         * Vemos si internacional nos permite el envío al pais en cuestion
         */
        //if ($params['carrier'])
        $carrierPS = Carrier::getCarrierByReference((int) $params['carrier']['id_reference']);
        $id_carrier = (int) $carrierPS->id;
        unset($carrierPS);
        $cop = CorreosProducts::getCorreosProductByIdCarrier($id_carrier);
        $codProducts = $cop['codigoProducto'];
        unset($cop);

        $countryIso = (new Country((int) $address_fields['id_country']))->iso_code;

        $isValidCountry = true;

        if ($codProducts === 'S0360') {
            $shippingMethodZone = new ShippingMethodZoneRules();
            $countryValid = $shippingMethodZone->excludeS360($countryIso, $codProducts);
            if ($countryValid) {
                $isValidCountry = false;
            }
        }

        $params_tpl['postcode'] = $address_fields['postcode'];
        $params_tpl['office_reference'] = "";
        $params_tpl['citypaq_reference'] = "";
        $params_tpl['onepagecheckout'] = $this->opcIsActive();

        /**
         * Comprobamos si ya tenemos el carrito guardado en request
         */
        if ($cartRequest = CorreosOficialCheckout::getValue("data", $params['cart']->id, "id_cart", "correos_oficial_requests")) {

            $cartRequest = json_decode($cartRequest["data"]);

            // Para Oficina
            if (isset($cartRequest->cp)) {
                $params_tpl['postcode'] = $cartRequest->cp;
                $params_tpl['office_reference'] = $cartRequest->unidad;
            }

            // Para CityPaq
            if (isset($cartRequest->cod_postal)) {
                $params_tpl['postcode'] = $cartRequest->cod_postal;
                $params_tpl['citypaq_reference'] = $cartRequest->cod_homepaq;
            }
        }

        /* Para cargar javascript solo una vez */
        $params_tpl['opc_counter'] = $this->opc_counter;

        if (isset($params_tpl['office_reference']) || isset($params_tpl['citypaq_reference'])) {
            $this->opc_counter++;
        }

        $this->smarty->assign(array('params' => $params_tpl));
        if ($isValidCountry) {
            return $this->display(__FILE__, 'views/templates/hook/displayCarrierExtraContent.tpl');
        } else {
            $this->smarty->assign([
                'id_carrier' => $id_carrier,
                'msgS0360' => $this->l('There are no transports for your location'),
            ]);
            return $this->display(__FILE__, 'views/templates/hook/forbidenCountry.tpl');
        }
    }

    /** Obtenemos versión del fichero config.xml */
    public function getModuleVersion()
    {
        $configFile = file_get_contents(_PS_CORE_DIR_ . "/modules/correosoficial/config.xml");
        $module = new SimpleXMLElement($configFile);
        return (string) $module->version;
    }

    public function hookActionCorreosAdminControllers()
    {
        $analitica = new Analitica();
        $last_comprove = $analitica->lastHour();
        $now = date('Y-m-d H:i:s');

        if (Module::isEnabled($this->name) && !empty($last_comprove) && strtotime($now) > strtotime($last_comprove . '+ ' . $this->hours_to_comprove . ' hours')) {
            $analitica->moduleRecord();
            $analitica->externalModulesRecord();
            $analitica->configurationCall('undefined');
            Db::getInstance(_PS_USE_SQL_SLAVE_)->update('correos_oficial_configuration', ['value' => $now], 'name = "Analitica_date"');
        }

        if (Module::isEnabled($this->name) && $analitica->gdpr(Tools::getAllValues())) {
            return '/views/templates/admin/correosGdpr.tpl';
        } else {
            return false;
        }
    }

    public function hookDisplayAdminAfterHeader()
    {
        if (!Analitica::gdprAccepted()) {
            return false;
        }

        $lastNotificationsCall = Configuration::get("CORREOS_OFICIAL_LAST_NOTIFICATIONS_CALL");

        if(
            !$lastNotificationsCall ||
            ($lastNotificationsCall && strtotime(date('Y-m-d H:i:s')) > strtotime($lastNotificationsCall . '+ 1 hours'))
        ){

            Configuration::updateValue("CORREOS_OFICIAL_LAST_NOTIFICATIONS_CALL", date('Y-m-d H:i:s'));
            
            $notifications = (new Analitica())->getNotifications();

            if (!empty($notifications['output']) && is_array($notifications['output']) && Tools::getIsset('controller') && Tools::getValue('controller') !== 'AdminCorreosOficialNotifications') {
                $token = Tools::getAdminToken('AdminCorreosOficialNotifications' . (int) Tab::getIdFromClassName('AdminCorreosOficialNotifications') . (int) $this->context->employee->id);
                $this->smarty->assign([
                    'notifications' => count($notifications['output']),
                    'link' => 'index.php?controller=AdminCorreosOficialNotifications&token=' . $token,
                    'img' => __PS_BASE_URI__ . 'modules/' . $this->name . '/logo.gif'
                ]);
    
                return $this->display(__FILE__, 'views/templates/admin/notificationalert.tpl');
            }

        }

    }

    /** **********************************************************************************************
     *                                     Métodos de Prestashop
     * **********************************************************************************************/

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminCorreosOficialSettings'));
    }

    /** **********************************************************************************************
     *                                     Métodos abstractos de Prestashop
     * **********************************************************************************************/

    /**
     * @abstract
     */
    public function getOrderShippingCost($params, $shipping_cost)
    {
    }

    /**
     * @abstract
     */
    public function getOrderShippingCostExternal($params)
    {
    }
}