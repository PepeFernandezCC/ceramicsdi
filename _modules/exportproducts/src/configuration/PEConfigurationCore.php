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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportProcess.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PESerializationChecker.php';

class PEConfigurationCore
{
    const TABLE_NAME = 'pe_configuration';
    const TABLE_NAME_WITH_PREFIX = _DB_PREFIX_ . self::TABLE_NAME;

    /**
     * @return bool
     */
    public static function createTableInDb()
    {
        self::dropTableFromDb();

        $query = "CREATE TABLE IF NOT EXISTS `" . self::TABLE_NAME_WITH_PREFIX . "` (
            `id_configuration` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shop` INT(11) NULL,
            `id_lang` INT(11) NULL,
		    `name` VARCHAR(255) NULL,
		    `format_file` VARCHAR(32) NULL,
		    `delimiter_csv` VARCHAR(32) NULL,
		    `separator_csv` VARCHAR(32) NULL,
		    `feed_target` VARCHAR(32) NULL,
		    `ftp_protocol` VARCHAR(255) NULL,
		    `ftp_authentication_type` VARCHAR(255) NULL,
		    `ftp_key_path` VARCHAR(255) NULL,
		    `ftp_server` VARCHAR(255) NULL,
		    `ftp_password` VARCHAR(255) NULL,
		    `ftp_username` VARCHAR(255) NULL,
		    `ftp_port` VARCHAR(32) NULL,
		    `ftp_folder_path` VARCHAR(255) NULL,
		    `ftp_passive_mode` INT(11) NULL,
            `ftp_file_transfer_mode` INT(11) NULL,
		    `file_name` VARCHAR(255) NULL,
            `display_header` INT(11) NULL,
            `strip_tags` INT(11) NULL,
		    `date_format` VARCHAR(255) NULL,
            `separator_decimal_points` VARCHAR(255) NULL,
            `thousands_separator` VARCHAR(255) NULL,
            `image_type` VARCHAR(255) NULL,
            `round_value`  INT(11) NULL,
            `currency`  INT(11) NULL,
            `price_decoration`  VARCHAR(255) NULL,
		    `sort_by` VARCHAR(32) NULL,
		    `order_way` VARCHAR(32) NULL,
            `encoding` VARCHAR(32) NULL,
            `separate`  INT(11) NULL,
            `style_spreadsheet` INT(11) NULL,
            `products_per_iteration` INT(11) DEFAULT '1000' NOT NULL,
            `is_saved`  INT(11) NULL,
            `google_categories` TEXT NULL,
		    `date_add` VARCHAR(128) NULL,
				PRIMARY KEY (`id_configuration`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        return \Db::getInstance()->execute($query);
    }

    /**
     * @return bool
     */
    public static function dropTableFromDb()
    {
        $query = 'DROP TABLE IF EXISTS `' . self::TABLE_NAME_WITH_PREFIX . '`';
        return \Db::getInstance()->execute($query);
    }

    /**
     * @param $id_configuration
     * @return bool
     * @throws \PrestaShopDatabaseException
     */
    public static function getCompleteConfiguration($id_configuration)
    {
        $configuration = PEConfigurationCore::getById($id_configuration);
        $configuration['fields'] = PEConfigurationField::getByConfigurationId($id_configuration);
        $configuration['filters'] = PEConfigurationFilter::getByConfigurationId($id_configuration);

        return $configuration;
    }

    /**
     * @param $id_configuration
     * @return bool
     * @throws \PrestaShopDatabaseException
     */
    public static function getById($id_configuration)
    {
        $configuration = false;

        $query = 'SELECT * FROM ' . self::TABLE_NAME_WITH_PREFIX . '
                WHERE id_configuration = ' . (int)$id_configuration;

        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (!empty($result[0])) {
            $configuration = $result[0];
        }

        return $configuration;
    }

    /**
     * @param $all_configuration_data
     * @return int|mixed|string
     * @throws \Exception
     */
    public static function createNewConfiguration($all_configuration_data)
    {
        $id_configuration = $all_configuration_data['id'];
        $configuration_core = self::prepareCoreConfigurationForSaving($all_configuration_data);

        if ($id_configuration) {
            PEConfigurationCore::update($id_configuration, $configuration_core);
            PEConfigurationFilter::removeByConfigurationId($id_configuration);
            PEConfigurationField::removeByConfigurationId($id_configuration);
        } else {
            $id_configuration = PEConfigurationCore::add($configuration_core);
        }

        PEConfigurationField::save($id_configuration, $all_configuration_data['fields']);

        if (!empty($all_configuration_data['filters'])) {
            PEConfigurationFilter::save($id_configuration, $all_configuration_data['filters']);
        }

        return $id_configuration;
    }

    public static function add($configuration)
    {
        \Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(self::TABLE_NAME, $configuration);
        $id_configuration = \Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();

        if (!$id_configuration) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Can\'t save export configuration. Please contact us!',__CLASS__));
        }

        return $id_configuration;
    }

    public static function validate($configuration)
    {
        if (!$configuration['name']) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Please enter Export Template Name!',__CLASS__));
        }

        if ($configuration['feed_target'] === 'ftp') {
            if (!$configuration['ftp_server']) {
                throw new \Exception(Module::getInstanceByName('exportproducts')->l('Please enter FTP Server!',__CLASS__));
            }

            if (!$configuration['ftp_username']) {
                throw new \Exception(Module::getInstanceByName('exportproducts')->l('Please enter FTP User Name!',__CLASS__));
            }

            if ($configuration['ftp_authentication_type'] === 'password' && !$configuration['ftp_password']) {
                throw new \Exception(Module::getInstanceByName('exportproducts')->l('Please enter FTP Password!',__CLASS__));
            }
            if ($configuration['ftp_authentication_type'] === 'key' && !$configuration['ftp_key_path']) {
                throw new \Exception(Module::getInstanceByName('exportproducts')->l('Please enter path to key file!',__CLASS__));
            }
        }

        return true;
    }

    public static function update($id_configuration, $configuration)
    {
        \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, $configuration, 'id_configuration=' . (int)$id_configuration);
    }

    public static function delete($id_configuration)
    {
        return \Db::getInstance()->update(self::TABLE_NAME, ['is_saved' => false], 'id_configuration = ' . (int)$id_configuration);
    }

    public static function getNameById($id_configuration)
    {
        $query = "SELECT `name` FROM `" . self::TABLE_NAME_WITH_PREFIX . "`
                 WHERE `id_configuration` = '".(int)$id_configuration."'";

        return \Db::getInstance()->getValue($query);
    }

    public static function getSavedConfigurations()
    {
        $query = "SELECT * FROM `" . self::TABLE_NAME_WITH_PREFIX . "`
                 WHERE `is_saved` = '1'";

        $configurations = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (empty($configurations)) {
            return [];
        }

        foreach ($configurations as &$configuration) {
            $data_from_last_export_process = PEExportProcess::getLastProcessByConfigurationId($configuration['id_configuration']);

            if ($data_from_last_export_process) {
                $configuration = array_merge($configuration, $data_from_last_export_process);
            } else {
                unset($configuration);
            }
        }

        return $configurations;
    }

    public static function getLastExportSettings($id_configuration)
    {
        $query = '
			SELECT s.id_configuration
      FROM ' . self::TABLE_NAME_WITH_PREFIX . '  as s
      WHERE s.id_parent = ' . (int)$id_configuration . '
      ORDER BY s.id_configuration DESC

			';
        $res = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (isset($res[0]['id_configuration']) && $res[0]['id_configuration']) {
            return $res[0]['id_configuration'];
        }

        return false;
    }

    public static function getNumOfSavedConfigurations()
    {
        $query = "SELECT COUNT(id_configuration) as count_settings
                FROM " . self::TABLE_NAME_WITH_PREFIX . "
                WHERE is_saved = '1'";

        $res = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if (isset($res[0]['count_settings']) && $res[0]['count_settings']) {
            return $res[0]['count_settings'];
        }
        return 0;
    }

    public static function upload()
    {
        if (!isset($_FILES['file']) || empty($_FILES['file']['tmp_name'])) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Select configuration for upload!',__CLASS__));
        }

        $file_name = $_FILES['file']['name'];
        $file_type = \Tools::substr($file_name, strrpos($file_name, '.') + 1);
        $file_type = \Tools::strtolower($file_type);

        if ($file_type != 'txt') {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Settings must have txt format!',__CLASS__));
        }

        $configuration = \Tools::file_get_contents($_FILES['file']['tmp_name']);

        if (PESerializationChecker::isStringSerialized($configuration)) {
            $configuration = Tools::unSerialize($configuration);
        } else {
            $configuration = json_decode($configuration, true);
        }

        $configuration['is_saved'] = true;
        $configuration_filters = $configuration['filters'];
        $configuration_fields = $configuration['fields'];
        unset($configuration['filters']);
        unset($configuration['fields']);

        \Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(self::TABLE_NAME, $configuration);
        $id_configuration = \Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();

        if (!$id_configuration) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Can not insert configuration in database!',__CLASS__));
        }

        if (!empty($configuration_filters)) {
            PEConfigurationFilter::save($id_configuration, $configuration_filters);
        }

        if (!empty($configuration_fields)) {
            PEConfigurationField::save($id_configuration, $configuration_fields);
        }

        return true;
    }

    public static function getLinkForDownload($id_configuration)
    {
        $configuration = PEConfigurationCore::getById($id_configuration);
        $configuration['filters'] = PEConfigurationFilter::getByConfigurationId($id_configuration);
        $configuration['fields'] = PEConfigurationField::getByConfigurationId($id_configuration);

        if (isset($configuration['id_configuration'])) {
            unset($configuration['id_configuration']);
        }

        if (!empty($configuration['filters'])) {
            foreach ($configuration['filters'] as &$filter) {
                if (PESerializationChecker::isStringSerialized($filter['value'])) {
                    $filter['value'] = Tools::unSerialize($filter['value']);
                } else {
                    $filter['value'] = json_decode($filter['value'], true);
                }

                unset($filter['id_configuration_filter']);
                unset($filter['id_configuration']);
            }
        }

        if (!empty($configuration['fields'])) {
            foreach ($configuration['fields'] as &$field) {
                unset($field['id_configuration_field']);
                unset($field['id_configuration']);
            }
        }

        file_put_contents(_PS_MODULE_DIR_ . 'exportproducts/upload/settings.txt', json_encode($configuration));

        return \Context::getContext()->link->getAdminLink('AdminProductsExport') . '&action=saveSettings';
    }

    public static function addGoogleCategoriesColumnToTable()
    {
        if (self::checkIfColumnExistsInTable('google_categories')) {
            return true;
        }

        return \Db::getInstance()->execute("ALTER TABLE `" . self::TABLE_NAME_WITH_PREFIX . "`
                                         ADD COLUMN `google_categories` TEXT NULL 
                                         AFTER `is_saved`");
    }

    public static function checkIfColumnExistsInTable($col_name)
    {
        $check_query = "SELECT NULL
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '" . self::TABLE_NAME_WITH_PREFIX . "'
            AND table_schema = '" . _DB_NAME_ . "'
            AND column_name = '" . $col_name . "'
        ";

        if (!Db::getInstance()->executeS($check_query)) {
            return false;
        }

        return true;
    }

    private static function prepareCoreConfigurationForSaving($configuration_data)
    {
        self::validate($configuration_data);

        return [
            'name'                     => pSQL($configuration_data['name']),
            'id_shop'                  => (int)$configuration_data['id_shop'],
            'id_lang'                  => (int)$configuration_data['id_lang'],
            'format_file'              => pSQL($configuration_data['format_file']),
            'delimiter_csv'            => pSQL($configuration_data['delimiter_csv']),
            'separator_csv'            => pSQL($configuration_data['separator_csv']),
            'feed_target'              => pSQL($configuration_data['feed_target']),
            'ftp_authentication_type'  => pSQL($configuration_data['ftp_authentication_type']),
            'ftp_key_path'             => pSQL($configuration_data['ftp_key_path']),
            'ftp_protocol'             => pSQL($configuration_data['ftp_protocol']),
            'ftp_server'               => pSQL($configuration_data['ftp_server']),
            'ftp_password'             => pSQL($configuration_data['ftp_password']),
            'ftp_username'             => pSQL($configuration_data['ftp_username']),
            'ftp_folder_path'          => pSQL($configuration_data['ftp_folder_path']),
            'ftp_passive_mode'          => isset($configuration_data['ftp_passive_mode']) ? (int)$configuration_data['ftp_passive_mode'] : (int)0,
            'ftp_file_transfer_mode'    => isset($configuration_data['ftp_file_transfer_mode']) ? (int)$configuration_data['ftp_file_transfer_mode'] : 1,
            'file_name'                => pSQL($configuration_data['file_name']),
            'display_header'           => (int)$configuration_data['display_header'],
            'strip_tags'               => (int)$configuration_data['strip_tags'],
            'date_format'              => pSQL($configuration_data['date_format']),
            'separator_decimal_points' => pSQL($configuration_data['separator_decimal_points']),
            'thousands_separator'      => pSQL($configuration_data['thousands_separator']),
            'image_type'              =>  pSQL($configuration_data['image_type']),
            'round_value'              => (int)$configuration_data['round_value'],
            'currency'                 => (int)$configuration_data['currency'],
            'price_decoration'         => pSQL($configuration_data['price_decoration']),
            'sort_by'                  => pSQL($configuration_data['sort_by']),
            'order_way'                 => pSQL($configuration_data['order_way']),
            'is_saved'                 => (int)$configuration_data['is_saved'],
            'separate'                 => (int)$configuration_data['separate'],
            'style_spreadsheet'        => (int)$configuration_data['style_spreadsheet'],
            'encoding'                 => pSQL($configuration_data['encoding']),
            'ftp_port'                 => (int)$configuration_data['ftp_port'],
            'products_per_iteration'   => (int)$configuration_data['products_per_iteration'],
            'google_categories'        => isset($configuration_data['google_categories']) ? pSQL(json_encode($configuration_data['google_categories'])) : '',
            'date_add'                 => pSQL(time()),
        ];
    }
}