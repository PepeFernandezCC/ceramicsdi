<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    MyPrestaModules
 * @copyright 2013-2020 MyPrestaModules
 * @license LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationCore.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationField.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationFilter.php';

class Exportproducts extends Module
{
    const PRODUCTEXPORT_CONTROLLER = 'AdminProductsExport';
    const PRODUCTEXPORT_CONTROLLER_NAME = 'Product Export';
    const ID_MODULE = 49440;


    public function __construct()
    {
        $this->name = 'exportproducts';
        $this->tab = 'export';
        $this->version = '5.1.4';
        $this->author = 'MyPrestaModules';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Products Catalog (CSV, Excel, Xml) Export PRO');
        $this->description = $this->l('The Products Catalog (CSV, Excel, Xml) Export Module allows you to do a CSV or EXCEL export of your data from your product function.');
        $this->module_key = "15d42b09042de2f7cb9c610f1871ff1d";
    }

    public function install()
    {
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/PETask.php';
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportProcess.php';
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportDataRepository.php';
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/product/PEExportedProduct.php';

        if (!parent::install()) {
            return false;
        }

        $this->registerHook('actionAdminControllerSetMedia');
        $this->_createTab();
        $this->installConfigurations();

        PEConfigurationCore::createTableInDb();
        PEConfigurationField::createTableInDb();
        PEConfigurationFilter::createTableInDb();
        PEExportedProduct::createTableInDb();
        PEExportDataRepository::createTableInDb();
        PEExportProcess::createTableInDb();
        PETask::createTableInDb();

        return true;
    }

    public function uninstall()
    {
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/PETask.php';
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportProcess.php';
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportDataRepository.php';
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/product/PEExportedProduct.php';

        if (!parent::uninstall()) {
            return false;
        }

        $this->uninstallConfigurations();

        PEConfigurationCore::dropTableFromDb();
        PEConfigurationField::dropTableFromDb();
        PEConfigurationFilter::dropTableFromDb();
        PEExportedProduct::dropTableFromDb();
        PEExportDataRepository::dropTableFromDb();
        PEExportProcess::dropTableFromDb();
        PETask::dropTableFromDb();

        $this->_removeTab();
        return true;
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') != self::PRODUCTEXPORT_CONTROLLER) {
            return false;
        }

        $this->context->controller->addCSS([
            _PS_MODULE_DIR_ . 'exportproducts/views/css/exportproducts.css',
            _PS_MODULE_DIR_ . 'exportproducts/views/css/exportproducts_fonts.css',
        ]);

        $this->context->controller->addJqueryPlugin('datepicker');
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJqueryUI('ui.progressbar');
        $this->context->controller->addJS([
            _PS_MODULE_DIR_ . 'exportproducts/views/js/tree.js',
            _PS_MODULE_DIR_ . 'exportproducts/views/js/exportproducts.js?v='.$this->version
        ]);
    }

    private function _createTab()
    {
        if (!$this->existsTab(self::PRODUCTEXPORT_CONTROLLER)) {
            $tabIsAdded = $this->addTab(self::PRODUCTEXPORT_CONTROLLER_NAME, self::PRODUCTEXPORT_CONTROLLER, $this->getIdTabFromClassName('AdminCatalog'));

            if (!$tabIsAdded) {
                return false;
            }
        }
    }

    public function addTab($tabName, $tabClass, $id_parent, $icon = false)
    {
        $tab = new Tab();
        $langs = Language::getLanguages();
        foreach ($langs as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $id_parent;
        if ($icon) {
            $tab->icon = $icon;
        }
        $save = $tab->save();
        if (!$save) {
            return false;
        }

        return true;
    }

    public function existsTab($tabClass)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT id_tab AS id
		FROM `' . _DB_PREFIX_ . 'tab` t
		WHERE LOWER(t.`class_name`) = \'' . pSQL($tabClass) . '\'');
        $count = count($result);
        if ($count == 0) {
            return false;
        }

        return true;
    }

    public function getIdTabFromClassName($tabName)
    {
        $query = 'SELECT id_tab FROM ' . _DB_PREFIX_ . 'tab WHERE class_name="' . pSQL($tabName) . '"';
        $tab = Db::getInstance()->getRow($query);
        return (int)$tab['id_tab'];
    }

    private function _removeTab()
    {
        $id_tab = (int)Tab::getIdFromClassName(self::PRODUCTEXPORT_CONTROLLER);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
    }

    public function installConfigurations()
    {
        Configuration::updateGlobalValue('MPM_PRODUCT_EXPORT_TASKS_KEY', md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME')));
        Configuration::updateGlobalValue('MPM_PRODUCT_EXPORT_ACTIVE', false);
        Configuration::updateGlobalValue('MPM_PRODUCT_EXPORT_ERROR_MESSAGE', '');
        Configuration::updateGlobalValue('MPM_PRODUCT_EXPORT_DEBUG_MODE', 0);
    }

    public function uninstallConfigurations()
    {
        Configuration::deleteByName('MPM_PRODUCT_EXPORT_TASKS_KEY');
        Configuration::deleteByName('MPM_PRODUCT_EXPORT_ACTIVE');
        Configuration::deleteByName('MPM_PRODUCT_EXPORT_ERROR_MESSAGE');
        Configuration::deleteByName('MPM_PRODUCT_EXPORT_DEBUG_MODE');
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink(self::PRODUCTEXPORT_CONTROLLER));
    }

    public function upgradeTo5_1_0()
    {
      if( file_exists( _PS_MODULE_DIR_ . 'exportproducts/datamodel.php' ) ){
        unlink(_PS_MODULE_DIR_ . 'exportproducts/datamodel.php');
      }

      if( file_exists( _PS_MODULE_DIR_ . 'exportproducts/automatic_export.php' ) ){
        unlink(_PS_MODULE_DIR_ . 'exportproducts/automatic_export.php');
      }

      if( file_exists( _PS_MODULE_DIR_ . 'exportproducts/export.php' ) ){
        unlink(_PS_MODULE_DIR_ . 'exportproducts/export.php');
      }

      if( file_exists( _PS_MODULE_DIR_ . 'exportproducts/send.php' ) ){
        unlink(_PS_MODULE_DIR_ . 'exportproducts/send.php');
      }

      if( file_exists( _PS_MODULE_DIR_ . 'exportproducts/src/send.php' ) ){
        unlink(_PS_MODULE_DIR_ . 'exportproducts/src/send.php');
      }

      if( file_exists( _PS_MODULE_DIR_ . 'exportproducts/download.php' ) ){
        unlink(_PS_MODULE_DIR_ . 'exportproducts/download.php');
      }

      if( file_exists( _PS_MODULE_DIR_ . 'exportproducts/src/download.php' ) ){
        unlink(_PS_MODULE_DIR_ . 'exportproducts/src/download.php');
      }

      return true;
    }

    public function upgradeTo506()
    {
        $sql = "
           SELECT NULL
           FROM INFORMATION_SCHEMA.COLUMNS
           WHERE table_name = '" . _DB_PREFIX_ . "pe_configuration'
           AND table_schema = '" . _DB_NAME_ . "'
           AND column_name = 'ftp_authentication_type'
        ";
        $check = Db::getInstance()->executeS($sql);
        if (!$check) {
            $sql = '
              ALTER TABLE ' . _DB_PREFIX_ . 'pe_configuration
              ADD COLUMN `ftp_authentication_type` VARCHAR(255) AFTER `ftp_protocol`;
            ';
            Db::getInstance()->execute($sql);
        }

        $sql = "
           SELECT NULL
           FROM INFORMATION_SCHEMA.COLUMNS
           WHERE table_name = '" . _DB_PREFIX_ . "pe_configuration'
           AND table_schema = '" . _DB_NAME_ . "'
           AND column_name = 'ftp_key_path'
        ";
        $check = Db::getInstance()->executeS($sql);
        if (!$check) {
            $sql = '
              ALTER TABLE ' . _DB_PREFIX_ . 'pe_configuration
              ADD COLUMN `ftp_key_path` VARCHAR(255) AFTER `ftp_authentication_type`;
            ';
            Db::getInstance()->execute($sql);
        }
        return true;
    }

    public function upgradeTo500()
    {
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/PETask.php';
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportProcess.php';
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportDataRepository.php';
        require_once _PS_MODULE_DIR_ . 'exportproducts/src/product/PEExportedProduct.php';

        $this->registerHook('actionAdminControllerSetMedia');
        $this->_createTab();
        $this->installConfigurations();

        PEConfigurationCore::createTableInDb();
        PEConfigurationField::createTableInDb();
        PEConfigurationFilter::createTableInDb();
        PEExportedProduct::createTableInDb();
        PEExportDataRepository::createTableInDb();
        PEExportProcess::createTableInDb();
        PETask::createTableInDb();

        if(isset(Context::getContext()->shop->id_shop_group) ){
            $id_shop_group = Context::getContext()->shop->id_shop_group;
        } elseif( isset(Context::getContext()->shop->id_group_shop) ){
            $id_shop_group = Context::getContext()->shop->id_group_shop;
        }

        $id_shop = Context::getContext()->shop->id;

        $all_old_version_setting_ids = Tools::unserialize( Configuration::get('GOMAKOIL_ALL_SETTINGS','', $id_shop_group, $id_shop));

        if (!empty($all_old_version_setting_ids)) {
            foreach ($all_old_version_setting_ids as $old_setting_id) {
                $core_configuration = [];

                $core_configuration['id'] = 0;
                $core_configuration['name'] = Configuration::get('GOMAKOIL_NAME_SETTING_'.$old_setting_id, '' , $id_shop_group, $id_shop);
                $core_configuration['id_shop'] = $id_shop;
                $core_configuration['id_lang'] = Configuration::get('GOMAKOIL_LANG_CHECKED_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['format_file'] = Configuration::get('GOMAKOIL_TYPE_FILE_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['delimiter_csv'] = Configuration::get('GOMAKOIL_CSV_DELIMITER_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['separator_csv'] = Configuration::get('GOMAKOIL_CSV_SEPERATOR_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['feed_target'] = Configuration::get('GOMAKOIL_FEED_TARGET_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['ftp_protocol'] = 'ftp';
                $core_configuration['ftp_server'] = Configuration::get('GOMAKOIL_FTP_SERVER_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['ftp_password'] = Configuration::get('GOMAKOIL_FTP_PASSWORD_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['ftp_username'] = Configuration::get('GOMAKOIL_FTP_USER_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['ftp_folder_path'] = Configuration::get('GOMAKOIL_FTP_FOLDER_PATH_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['ftp_passive_mode'] = false;
                $core_configuration['ftp_file_transfer_mode'] = false;
                $core_configuration['file_name'] = Configuration::get('GOMAKOIL_NAME_FILE_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['display_header'] = Configuration::get('GOMAKOIL_DISPLAY_HEADERS_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['strip_tags'] = Configuration::get('GOMAKOIL_STRIP_TAGS_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['date_format'] = 'Y-m-d H:i:s';
                $core_configuration['separator_decimal_points'] = Configuration::get('GOMAKOIL_DECIMAL_PRICE_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['thousands_separator'] = 'Space';
                $core_configuration['image_type'] = 'Original Size';
                $core_configuration['round_value'] = Configuration::get('GOMAKOIL_DESIMAL_POINTS_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['currency'] = Configuration::get('GOMAKOIL_CURRENCY_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['price_decoration'] = Configuration::get('GOMAKOIL_DECORATION_PRICE_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['sort_by'] = Configuration::get('GOMAKOIL_ORDER_BY_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['order_way'] = Configuration::get('GOMAKOIL_ORDER_WAY_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['is_saved'] = true;
                $core_configuration['separate'] = Configuration::get('GOMAKOIL_SEPARATE_SETTING_EX_'.$old_setting_id, '' ,$id_shop_group , $id_shop);
                $core_configuration['style_spreadsheet'] = true;
                $core_configuration['encoding'] = 'UTF-8-BOM';
                $core_configuration['ftp_port'] = '21';
                $core_configuration['products_per_iteration'] = 1000;
                $core_configuration['google_categories'] = '';

                $old_version_export_fields = Tools::unSerialize(Configuration::get('GOMAKOIL_FIELDS_CHECKED_'.$old_setting_id, '' ,$id_shop_group, $id_shop));

                $fields_configuration = [];
                foreach ($old_version_export_fields as $old_version_export_field_id => $old_version_export_field_name) {
                    if ($old_version_export_field_id == 'features' || preg_match('/^extra_field_(\d+)$/', $old_version_export_field_id)) {
                        continue;
                    }

                    if (preg_match('/^Attribute_(\d+)$/', $old_version_export_field_id, $matches)) {
                        $attribute_group_id = $matches[1];
                        $old_version_export_field_id = 'attribute_group_' . $attribute_group_id;
                    }

                    if (preg_match('/^FEATURE_(.+)$/', $old_version_export_field_id, $matches)) {
                        $feature_name = $matches[1];
                        $feature_id = Db::getInstance()->getValue("SELECT `id_feature` FROM `" . _DB_PREFIX_ . "feature_lang` 
                                                                        WHERE `name` = '".pSQL($feature_name)."'");

                        $old_version_export_field_id = 'feature_' . $feature_id;
                    }

                    $fields_configuration[] = [
                        'id_configuration' => 0,
                        'field' => $old_version_export_field_id,
                        'name' => $old_version_export_field_name,
                        'gmf_id' => false,
                        'gmf_doc_link' => false,
                        'tab' => PEConfigurationField::getExportFieldTabByFieldId($old_version_export_field_id),
                        'value' => '',
                        'conditions' => ''
                    ];
                }

                $old_version_extra_export_fields = Tools::unSerialize(Configuration::get('GOMAKOIL_EXTRA_FIELDS_'.$old_setting_id, '' ,$id_shop_group, $id_shop));

                if (!empty($old_version_extra_export_fields)) {
                    foreach ($old_version_extra_export_fields as $old_version_extra_export_field) {
                        preg_match('/^extra_field_(\d+)$/', $old_version_extra_export_field['id'], $matches);
                        $static_field_id = !empty($matches[1]) ? $matches[1] : 0;

                        $fields_configuration[] = [
                            'id_configuration' => 0,
                            'field' => 'static_field_' .  $static_field_id,
                            'name' => $old_version_extra_export_field['name'],
                            'gmf_id' => false,
                            'gmf_doc_link' => false,
                            'tab' => 'staticTab',
                            'value' => $old_version_extra_export_field['value'],
                            'conditions' => ''
                        ];


                    }
                }

                $core_configuration['fields'] = $fields_configuration;

                $filter_configuration = [];

                $old_products_filter = Tools::unSerialize(Configuration::get('GOMAKOIL_PRODUCTS_CHECKED_'.$old_setting_id,'' , $id_shop_group, $id_shop));
                $old_manufacturers_filter = Tools::unSerialize(Configuration::get('GOMAKOIL_MANUFACTURERS_CHECKED_'.$old_setting_id,'' , $id_shop_group, $id_shop));
                $old_suppliers_filter = Tools::unSerialize(Configuration::get('GOMAKOIL_SUPPLIERS_CHECKED_'.$old_setting_id,'' , $id_shop_group, $id_shop));
                $old_categories_filter = Tools::unSerialize(Configuration::get('GOMAKOIL_CATEGORIES_CHECKED_'.$old_setting_id,'' , $id_shop_group, $id_shop));
                $old_price_filter = Tools::unSerialize(Configuration::get('GOMAKOIL_PRODUCTS_PRICE_'.$old_setting_id, '' , $id_shop_group, $id_shop));
                $old_quantity_filter = Tools::unSerialize(Configuration::get('GOMAKOIL_PRODUCTS_QUANTITY_'.$old_setting_id, '' , $id_shop_group, $id_shop));
                $old_condition_filter = Tools::unSerialize(Configuration::get('GOMAKOIL_PRODUCTS_CONDITION_'.$old_setting_id, '' , $id_shop_group, $id_shop));

                $old_is_active_filter = Configuration::get('GOMAKOIL_ACTIVE_PRODUCTS_SETTING_'.$old_setting_id, '' , $id_shop_group, $id_shop);
                $old_has_ean_filter = Configuration::get('GOMAKOIL_EAN_PRODUCTS_SETTING_'.$old_setting_id, '' , $id_shop_group, $id_shop);
                $old_has_specific_price_filter = Configuration::get('GOMAKOIL_SPECIFIC_PRICES_PRODUCTS_SETTING_'.$old_setting_id, '' , $id_shop_group, $id_shop);

                if (!empty($old_products_filter)) {
                    $filter_configuration[] = [
                        'field' => 'product_id',
                        'field_type' => 'number',
                        'value' => [
                            'type' => 'in',
                            'value' => implode(',', $old_products_filter)
                        ],
                        'label' => 'Product ID'
                    ];
                }

                if (!empty($old_manufacturers_filter)) {
                    $filter_configuration[] = [
                        'field' => 'id_manufacturer',
                        'field_type' => 'number',
                        'value' => [
                            'type' => 'in',
                            'value' => implode(',', $old_manufacturers_filter)
                        ],
                        'label' => 'Manufacturer ID'
                    ];
                }

                if (!empty($old_suppliers_filter)) {
                    $filter_configuration[] = [
                        'field' => 'supplier_id',
                        'field_type' => 'number',
                        'value' => [
                            'type' => 'in',
                            'value' => implode(',', $old_suppliers_filter)
                        ],
                        'label' => 'Supplier ID'
                    ];
                }

                if (!empty($old_categories_filter)) {
                    $filter_configuration[] = [
                        'field' => 'categories',
                        'field_type' => 'tree',
                        'value' => $old_categories_filter,
                        'label' => 'Product Categories'
                    ];
                }

                if (!empty($old_is_active_filter)) {
                    $filter_configuration[] = [
                        'field' => 'active',
                        'field_type' => 'select',
                        'value' => $old_is_active_filter,
                        'label' => 'Active Status'
                    ];
                }

                if (!empty($old_has_ean_filter)) {
                    $filter_configuration[] = [
                        'field' => 'ean13',
                        'field_type' => 'string',
                        'value' => [
                            'type' => 'not_empty',
                            'value' => ''
                        ],
                        'label' => 'EAN-13 or JAN barcode'
                    ];
                }

                if (!empty($old_has_specific_price_filter)) {
                    $filter_configuration[] = [
                        'field' => 'specific_price',
                        'field_type' => 'number',
                        'value' => [
                            'type' => 'not_empty',
                            'value' => ''
                        ],
                        'label' => 'Specific Price'
                    ];
                }

                if (!empty($old_price_filter) && !empty($old_price_filter['price_value'])) {
                    switch ($old_price_filter['selection_type_price']) {
                        case 1:
                            $condition = '<';
                            break;
                        case 2:
                            $condition = '>';
                            break;
                        case 3:
                            $condition = '=';
                            break;
                        default:
                            $condition = '';
                    }

                    $filter_configuration[] = [
                        'field' => 'price',
                        'field_type' => 'number',
                        'value' => [
                            'type' => $condition,
                            'value' => $old_price_filter['price_value']
                        ],
                        'label' => 'Price'
                    ];
                }

                if (!empty($old_quantity_filter) && !empty($old_quantity_filter['quantity_value'])) {
                    switch ($old_quantity_filter['selection_type_quantity']) {
                        case 1:
                            $condition = '<';
                            break;
                        case 2:
                            $condition = '>';
                            break;
                        case 3:
                            $condition = '=';
                            break;
                        default:
                            $condition = '';
                    }

                    $filter_configuration[] = [
                        'field' => 'total_quantity',
                        'field_type' => 'number',
                        'value' => [
                            'type' => $condition,
                            'value' => $old_quantity_filter['quantity_value']
                        ],
                        'label' => 'Total Quantity'
                    ];
                }

                if (!empty($old_condition_filter)) {
                    $filter_configuration[] = [
                        'field' => 'condition',
                        'field_type' => 'string',
                        'value' => [
                            'type' => 'list',
                            'value' => implode(',', $old_condition_filter)
                        ],
                        'label' => 'Condition'
                    ];
                }

                $core_configuration['filters'] = $filter_configuration;

                $new_setting_id = PEConfigurationCore::createNewConfiguration($core_configuration);

                $old_tasks = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "productsexport_tasks 
                                                            WHERE export_settings = '".(int)$old_setting_id."'");

                if (!empty($old_tasks) && !empty($new_setting_id)) {
                    $automatic_export_data = Tools::unSerialize(Configuration::get('GOMAKOIL_PRODUCTS_AUTOMATIC_EXPORT_'.$old_setting_id, '' ,$id_shop_group , $id_shop));

                    foreach ($old_tasks as $old_task) {
                        $frequency = ['0'];
                        $frequency[] = $old_task['hour'] == '-1' ? '*' : $old_task['hour'];
                        $frequency[] = $old_task['day'] == '-1' ? '*' : $old_task['day'];
                        $frequency[] = $old_task['month'] == '-1' ? '*' : $old_task['month'];

                        if ($old_task['day_of_week'] == 7) {
                            $frequency[] = '0';
                        } else if ($old_task['day_of_week'] == '-1') {
                            $frequency[] = '*';
                        } else {
                            $frequency[] = $old_task['day_of_week'];
                        }

                        $frequency = implode(' ', $frequency);

                        if (!empty($automatic_export_data['notification_emails'])) {
                            $send_notification = true;
                            $notification_emails = $automatic_export_data['notification_emails'];
                        } else {
                            $send_notification = false;
                            $notification_emails = '';
                        }

                        $new_task = [
                            'id_task' => 0,
                            'description' => $old_task['description'],
                            'id_configuration' => $new_setting_id,
                            'one_shot' => $old_task['one_shot'],
                            'export_not_exported' => Configuration::get('GOMAKOIL_NOT_EXPORDED_'.$old_setting_id, '' ,$id_shop_group , $id_shop),
                            'email_message' => $send_notification,
                            'attach_file' => false,
                            'frequency' => $frequency,
                            'export_emails' => $notification_emails
                        ];

                        PETask::save($new_task);
                    }
                }
            }
        }

        return true;
    }

    public function upgradeTo400()
    {
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'exportproducts_data';
        $res = Db::getInstance()->execute($sql);
        if( !$res ){
            return false;
        }

        $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'exportproducts_data(
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `row` int(11) NOT NULL,
            `field` varchar(254) NOT NULL,
            `value` text NOT NULL,
            `id_task` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `index2` (`row`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $res = Db::getInstance()->execute($sql);
        if( !$res ){
            return false;
        }

        $sql = '
      ALTER TABLE ' . _DB_PREFIX_ . 'productsexport_tasks
      ADD COLUMN `progress` VARCHAR(500) NOT NULL AFTER `last_finish`
        ;
    ';

        $res = Db::getInstance()->execute($sql);
        if( !$res ){
            return false;
        }

        return true;
    }

    public function upgradeTo370()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'productsexport_tasks(
			`id_task` int(11) NOT NULL AUTO_INCREMENT,
      `description` varchar(255) NOT NULL,
      `export_settings` varchar(255) NOT NULL,
      `hour` int(11) NOT NULL,
      `day` int(11) NOT NULL,
      `month` int(11) NOT NULL,
      `day_of_week` int(11) NOT NULL,
      `last_start` varchar(45) NOT NULL,
      `last_finish` varchar(45) NOT NULL,
      `active` int(1) NOT NULL,
      `one_shot` int(1) NOT NULL,
      `id_shop` int(11) NOT NULL,
      `id_shop_group` int(11) NOT NULL,
      PRIMARY KEY (`id_task`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        Db::getInstance()->execute($sql);

        Configuration::updateGlobalValue('GOMAKOIL_PRODUCTS_EXPORT_TASKS_KEY', md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')));

        $this->_createTab();

        return true;
    }

    public function upgradeTo310()
    {
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'exported_products';
        Db::getInstance()->execute($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'exported_products(
				id_exported_products int(11) unsigned NOT NULL AUTO_INCREMENT,
			  id_product  int(11) NULL,
		    id_setting int(11) NULL,
				PRIMARY KEY (`id_exported_products`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        Db::getInstance()->execute($sql);
        return true;
    }
}
