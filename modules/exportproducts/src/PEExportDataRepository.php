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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PELogger.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/product/PEProductDataProvider.php';

class PEExportDataRepository
{
    private $configuration;
    private $insertion_values;
    private $num_of_products_in_repository;
    private $saving_count;

    const TABLE_NAME = 'pe_data_for_export';
    const TABLE_NAME_WITH_PREFIX = _DB_PREFIX_ . self::TABLE_NAME;

    public function __construct($configuration, $iteration)
    {
        $this->configuration = $configuration;

        if ($iteration == 0) {
            $this->num_of_products_in_repository = (int)0;
        } else {
            $this->num_of_products_in_repository = (int)$this->getNumOfProductsInRepository();
        }

        $this->saving_count = 0;
    }

    public static function createTableInDb()
    {
        self::dropTableFromDb();

        $query = 'CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME_WITH_PREFIX . '` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `row` INT(11) NOT NULL,
                `field` VARCHAR(254) NOT NULL,
                `field_name` VARCHAR(254) NOT NULL,
                `value` TEXT NOT NULL,
                PRIMARY KEY (`id`),
                KEY `index2` (`row`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        \Db::getInstance()->execute($query);
    }

    public static function dropTableFromDb()
    {
        $query = 'DROP TABLE IF EXISTS `' . self::TABLE_NAME_WITH_PREFIX . '`';
        return \Db::getInstance()->execute($query);
    }

    public function saveExportProductsToRepository(PEExportProcess $export_process, $products_ids)
    {
        foreach ($products_ids as $id_product) {
            if ($export_process->getStatus() == PEExportProcess::STATUS_STOPPED) {
                return false;
            }

            if (empty($id_product['id_product'])) {
                continue;
            }

            $id_product_attribute = $this->configuration['separate'] ? $id_product['id_product_attribute'] : false;
            $product_data_provider = new PEProductDataProvider($id_product['id_product'], $this->configuration, $id_product_attribute);
            $product_data = $product_data_provider->getProductDataForExport();

            if (empty($product_data)) {
                continue;
            }

            $this->saving_count++;
            $this->num_of_products_in_repository++;

            foreach ($this->configuration['fields'] as $field_id => $field_data) {
                if (!isset($product_data[$field_id])) {
                    continue;
                }

                $row = (int)$this->num_of_products_in_repository;
                $field_id = pSQL($field_id);
                $field_name = pSQL($field_data['name']);
                $field_value = pSQL($product_data[$field_id], true);

                $this->insertion_values .= '("' . $row . '","' . $field_id . '","' . $field_name . '","' . $field_value . '"),';

                if ($this->isTimeToRunInsertQuery()) {
                    $this->insertValuesInDb();
                    $export_process->updateProgress($this->num_of_products_in_repository);
                }
            }
        }

        $this->insertValuesInDb();
        $export_process->updateProgress($this->num_of_products_in_repository);
    }

    public function getProductByRowNumber($row)
    {
        $product = [];
        $query = "SELECT * FROM " . self::TABLE_NAME_WITH_PREFIX . "
                WHERE `row` = '" . (int)$row . "'";

        $res = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if ($res) {
            foreach ($res as $data) {
                $product[$data['field']] = $data['value'];
            }

            \Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(self::TABLE_NAME, '`row`=' . (int)$row);

            return $product;
        }

        return false;
    }

    public static function getNumOfProductsInRepository()
    {
        $query = "SELECT COUNT(grouped_rows.row) FROM (SELECT `row`
                FROM `" . self::TABLE_NAME_WITH_PREFIX . "`
                GROUP BY `row`) AS grouped_rows";

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query, false);
    }

    public static function clear()
    {
        \Db::getInstance()->execute('TRUNCATE ' . self::TABLE_NAME_WITH_PREFIX);
    }

    private function isTimeToRunInsertQuery()
    {
        $max_number_of_products_per_insert_query = 50;
        $need_to_run_new_iteration = $this->saving_count % $max_number_of_products_per_insert_query == 0;

        if (!$need_to_run_new_iteration) {
            return false;
        }

        return true;
    }

    private function insertValuesInDb()
    {
        $this->insertion_values = rtrim($this->insertion_values, ',');

        if (empty($this->insertion_values)) {
            return true;
        }

        $query = "INSERT INTO " . self::TABLE_NAME_WITH_PREFIX . "
                            (`row`,`field`,`field_name`,`value`)
                          VALUES $this->insertion_values";

        $is_inserted = \Db::getInstance()->execute($query, false);
        $this->insertion_values = '';

        return $is_inserted;
    }
}