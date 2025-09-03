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

class PEConfigurationField
{
    const TABLE_NAME = 'pe_configuration_field';
    const TABLE_NAME_WITH_PREFIX = _DB_PREFIX_ . self::TABLE_NAME;

    public static function createTableInDb()
    {
        self::dropTableFromDb();

        $query = 'CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME_WITH_PREFIX . '` (
				`id_configuration_field` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_configuration` INT(11) NULL,
                `name` VARCHAR(128) NULL,
                `field` VARCHAR(128) NULL,
                `gmf_id` VARCHAR(255) NULL,
                `gmf_doc_link` VARCHAR(255) NULL,
                `default_value` TEXT NULL,
                `conditions` TEXT NULL,
                `tab` VARCHAR(128) NULL,
				PRIMARY KEY (`id_configuration_field`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        return \Db::getInstance()->execute($query);
    }

    public static function dropTableFromDb()
    {
        $query = 'DROP TABLE IF EXISTS `' . self::TABLE_NAME_WITH_PREFIX . '`';
        return \Db::getInstance()->execute($query);
    }

    public static function addGMFColumnsToTable()
    {
        if (!self::checkIfColumnExistsInTable('gmf_id')) {
            \Db::getInstance()->execute("ALTER TABLE `" . self::TABLE_NAME_WITH_PREFIX . "`
                                         ADD COLUMN `gmf_id` VARCHAR(255) NULL ");
        }

        if (!self::checkIfColumnExistsInTable('gmf_doc_link')) {
            \Db::getInstance()->execute("ALTER TABLE `" . self::TABLE_NAME_WITH_PREFIX . "`
                                         ADD COLUMN `gmf_doc_link` VARCHAR(255) NULL ");
        }

        return true;
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

    public static function save($id_configuration, $fields)
    {
        foreach ($fields as $field) {
            $conditions = '';
            $default_value = '';

            if (!empty($field['conditions'])) {
                $conditions = json_encode($field['conditions']);
            }

            if (isset($field['value'])) {
                $default_value = $field['value'];
            } else if (isset($field['default_value'])) {
                $default_value = $field['default_value'];
            }

            $fields_data = [
                'id_configuration' => (int)$id_configuration,
                'field'            => pSQL($field['field']),
                'name'             => pSQL($field['name']),
                'gmf_id'           => pSQL($field['gmf_id']),
                'gmf_doc_link'     => pSQL($field['gmf_doc_link']),
                'tab'              => pSQL($field['tab']),
                'default_value'    => pSQL($default_value, true),
                'conditions'       => pSQL($conditions, true),
            ];

            \Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(self::TABLE_NAME, $fields_data);
        }

        return true;
    }

    public static function refreshFields($fields)
    {
        $old_fields_is_deleted = false;

        foreach ($fields as $field) {
            if (!$old_fields_is_deleted) {
                self::removeByConfigurationId($field['id_configuration']);
                $old_fields_is_deleted = true;
            }

            $fields_data = [
                'id_configuration' => (int)$field['id_configuration'],
                'name'             => pSQL($field['name']),
                'field'            => pSQL($field['field']),
                'default_value'    => pSQL($field['default_value']),
                'conditions'       => pSQL($field['conditions']),
                'tab'              => pSQL($field['tab']),
            ];

            \Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(self::TABLE_NAME, $fields_data);
        }

        return true;
    }

    public static function removeByConfigurationId($id_configuration)
    {
        return \Db::getInstance()->execute("DELETE FROM " . self::TABLE_NAME_WITH_PREFIX . " WHERE id_configuration = " . (int)$id_configuration);
    }

    public static function getByConfigurationId($id_configuration)
    {
        $query = 'SELECT * FROM ' . self::TABLE_NAME_WITH_PREFIX . '  AS s
                    WHERE s.id_configuration = ' . (int)$id_configuration;

        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        $fields = [];

        if ($result) {
            foreach ($result as $field) {
                $fields[$field['field']] = $field;
            }
        }

        return $fields;
    }

    public static function getSelectedSpecificPriceFields($selected_fields)
    {
        $specific_price_fields = self::getListOfSpecificPriceFields();
        return array_intersect_key($selected_fields, $specific_price_fields);
    }

    public static function splitSpecificPriceFields($selected_fields, $max_number_of_specific_prices)
    {
        $selected_specific_price_fields = self::getSelectedSpecificPriceFields($selected_fields);

        if (empty($selected_specific_price_fields)) {
            return $selected_fields;
        }

        foreach ($selected_specific_price_fields as $key => $specific_price_field) {
            unset($selected_fields[$key]);

            for ($i = 1; $i <= $max_number_of_specific_prices; $i++) {
                $selected_fields[$key . '_' . $i] = [
                    'id_configuration' => $specific_price_field['id_configuration'],
                    'name'             => $specific_price_field['name'] . '_' . $i,
                    'field'            => $specific_price_field['field'] . '_' . $i,
                    'default_value'    => isset($specific_price_field['default_value']) ? $specific_price_field['default_value'] : '',
                    'conditions'       => $specific_price_field['conditions'],
                    'tab'              => 'exportTabPrices'
                ];
            }
        }

        return $selected_fields;
    }

    private static function getListOfSpecificPriceFields()
    {
        return [
            'id_specific_price'             => '',
            'specific_price'                => '',
            'specific_price_reduction'      => '',
            'specific_price_reduction_type' => '',
            'specific_price_from'           => '',
            'specific_price_to'             => '',
            'specific_price_from_quantity'  => '',
            'specific_price_id_group'       => '',
            'specific_price_id_customer'    => ''
        ];
    }

    public static function splitSupplierFields($selected_fields, $products_for_export_ids)
    {
        $selected_supplier_fields = self::getSelectedSupplierFields($selected_fields);
        $supplier_ids = \Db::getInstance()->executeS("SELECT DISTINCT(id_supplier) FROM " . _DB_PREFIX_ . "product_supplier
                                                          WHERE id_product IN (" . pSQL($products_for_export_ids) . ")
                                                          ORDER BY id_supplier ASC");

        foreach ($selected_supplier_fields as $key => $supplier_field) {
            unset($selected_fields[$key]);

            foreach ($supplier_ids as $supplier_id_container) {
                $id_supplier = $supplier_id_container['id_supplier'];

                $selected_fields[$key . '_' . $id_supplier] = [
                    'id_configuration' => $supplier_field['id_configuration'],
                    'name'             => $supplier_field['name'] . '_' . $id_supplier,
                    'field'            => $supplier_field['field'] . '_' . $id_supplier,
                    'default_value'    => !empty($supplier_field['default_value']) ? $supplier_field['default_value'] : '',
                    'conditions'       => $supplier_field['conditions'],
                    'tab'              => 'exportTabSuppliers'
                ];
            }
        }

        return $selected_fields;
    }

    public static function getSelectedSupplierFields($selected_fields)
    {
        $supplier_fields = self::getListOfSupplierFields();
        return array_intersect_key($selected_fields, $supplier_fields);
    }

    private static function getListOfSupplierFields()
    {
        return [
            'separate_supplier_id'        => '',
            'separate_supplier_name'      => '',
            'separate_supplier_reference' => '',
            'separate_supplier_price'     => '',
            'separate_supplier_currency'  => ''
        ];
    }

    public static function splitCategoryTreeFields($id_configuration, $selected_fields, $max_num_of_trees)
    {
        unset($selected_fields['separated_categories']);

        for ($i = 1; $i <= $max_num_of_trees; $i++) {
            $selected_fields['category_tree_' . $i] = [
                'id_configuration' => $id_configuration,
                'name'             => 'Category Tree_' . $i,
                'field'            => 'category_tree_' . $i,
                'default_value'    => '',
                'conditions'       => '',
                'tab'              => 'exportTabAssociations'
            ];
        }

        return $selected_fields;
    }

    public static function splitAttributeGroupFields(
        $id_configuration,
        $selected_fields,
        $attribute_group_ids,
        $id_lang,
        $id_shop
    ) {
        if (empty($attribute_group_ids)) {
            return false;
        }

        unset($selected_fields['combinations_value']);

        foreach ($attribute_group_ids as $attribute_group_id_container) {
            $id_attribute_group = $attribute_group_id_container['id_attribute_group'];

            if (!$id_attribute_group) {
                continue;
            }

            $attribute_group = new \AttributeGroup($id_attribute_group, $id_lang, $id_shop);
            $attribute_group_name = $attribute_group->name;
            $selected_fields['attribute_group_' . $id_attribute_group] = [
                'id_configuration' => $id_configuration,
                'name'             => 'Attribute Group_' . $attribute_group_name,
                'field'            => 'attribute_group_' . $id_attribute_group,
                'default_value'    => '',
                'conditions'       => '',
                'tab'              => 'exportTabCombinations'
            ];
        }

        return $selected_fields;
    }

    public static function splitImageUrlFields($id_configuration, $selected_fields, $max_num_of_images)
    {
        unset($selected_fields['images_value']);

        for ($i = 1; $i <= $max_num_of_images; $i++) {
            $selected_fields['images_value_' . $i] = [
                'id_configuration' => $id_configuration,
                'name'             => 'Product Image ' . $i,
                'field'            => 'images_value_' . $i,
                'gmf_id'           => 'additional_image_link',
                'gmf_doc_link'     => 'https://support.google.com/merchants/answer/6324370?hl=en&ref_topic=6324338',
                'default_value'    => '',
                'conditions'       => '',
                'tab'              => 'exportTabImages'
            ];
        }

        return $selected_fields;
    }

    public static function getFieldOptions()
    {
        $export_field_options = [];

        $export_field_options[0] = [
            'tab'    => 'exportTabInformation',
            'name'   => Module::getInstanceByName('exportproducts')->l('Information', __CLASS__),
            'fields' => [
                [
                    'id'           => 'id_product',
                    'name'         => Module::getInstanceByName('exportproducts')->l('Product ID',__CLASS__),
                    'gmf_id'       => 'id|item_group_id',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324405'
                ],
                [
                    'id'           => 'name',
                    'name'         => Module::getInstanceByName('exportproducts')->l('Product Name',__CLASS__),
                    'gmf_id'       => 'title',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324415'
                ],
                [
                    'id'           => 'name_with_combination',
                    'name'         => Module::getInstanceByName('exportproducts')->l('Product Name With Combination',__CLASS__),
                    'gmf_id'       => 'title',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324415'
                ],
                [
                    'id'           => 'reference',
                    'name'         => Module::getInstanceByName('exportproducts')->l('Product Reference Code',__CLASS__),
                    'gmf_id'       => 'id|item_group_id',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324405'
                ],
                [
                    'id'           => 'active',
                    'name'         => Module::getInstanceByName('exportproducts')->l('Active',__CLASS__),
                    'gmf_id'       => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'description_short',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Short Description',__CLASS__),
                    'gmf_id' => 'description',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324468'
                ],
                [
                    'id'     => 'description',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Description',__CLASS__),
                    'gmf_id' => 'description',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324468'
                ],
                [
                    'id'     => 'tags',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Tags',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'ean13',
                    'name'   => Module::getInstanceByName('exportproducts')->l('EAN-13 Or JAN Barcode',__CLASS__),
                    'gmf_id' => 'gtin',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324461'
                ],
                [
                    'id'     => 'upc',
                    'name'   => Module::getInstanceByName('exportproducts')->l('UPC Barcode',__CLASS__),
                    'gmf_id' => 'gtin',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324461'
                ],
                [
                    'id'     => 'isbn',
                    'name'   => Module::getInstanceByName('exportproducts')->l('ISBN',__CLASS__),
                    'gmf_id' => 'gtin',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324461'
                ],
                [
                    'id'     => 'mpn',
                    'name'   => Module::getInstanceByName('exportproducts')->l('MPN',__CLASS__),
                    'gmf_id' => 'mpn',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324482'
                ],
                [
                    'id'     => 'identifier_exists',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Identifier Exists',__CLASS__),
                    'gmf_id' => 'identifier_exists',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324478'
                ],
                [
                    'id'     => 'condition',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Condition',__CLASS__),
                    'gmf_id' => 'condition',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324469'
                ],
                [
                    'id'     => 'new',
                    'name'   => Module::getInstanceByName('exportproducts')->l('New',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'available_for_order',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Available For Order',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'online_only',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Online Only',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'is_virtual',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Is Virtual',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'visibility',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Visibility',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'cache_is_pack',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Cache is Pack',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'product_link',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Product Link',__CLASS__),
                    'gmf_id' => 'link',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324416'
                ],
                [
                    'id'     => 'date_add',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Date Add',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'date_upd',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Date Update',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_shop_default',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Shop ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'quantity_discount',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Quantity Discount',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'redirect_type',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Redirect Type',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_product_redirected',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Product Redirected ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'indexed',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Indexed',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_color_default',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Color ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'is_fully_loaded',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Is Fully Loaded',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'pack_items_id',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Pack Items Product ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'pack_items_name',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Pack Items Name',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'pack_items_id_pack_product_attribute',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Pack Items Product Attribute ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'pack_items_reference',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Pack Items Reference',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'pack_items_ean13',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Pack Items EAN13',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'pack_items_upc',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Pack Items UPC',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'pack_items_quantity',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Pack Items Quantity',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ]
            ],
        ];

        $export_field_options[1] = [
            'tab'    => 'exportTabPrices',
            'name'   => Module::getInstanceByName('exportproducts')->l('Prices',__CLASS__),
            'fields' => [
                [
                    'id'     => 'show_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Show Price',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'wholesale_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Wholesale Price',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'base_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Retail Price Without Tax',__CLASS__),
                    'gmf_id' => 'price',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324371'
                ],
                [
                    'id'     => 'base_price_with_tax',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Retail Price With Tax',__CLASS__),
                    'gmf_id' => 'price',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324371'
                ],
                [
                    'id'     => 'price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Final Price Without Tax',__CLASS__),
                    'gmf_id' => 'price',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324371'
                ],
                [
                    'id'     => 'final_price_with_tax',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Final Price With Tax',__CLASS__),
                    'gmf_id' => 'price',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324371'
                ],
                [
                    'id'     => 'final_price_without_tax_and_no_reduction',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Final Price Without Tax And No Reduction',__CLASS__),
                    'gmf_id' => 'price',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324371'
                ],
                [
                    'id'     => 'final_price_with_tax_and_no_reduction',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Final Price With Tax And No Reduction',__CLASS__),
                    'gmf_id' => 'price',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324371'
                ],
                [
                    'id'     => 'combination_final_price_pre_tax',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Combination Final Price Without Tax',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combination_final_price_with_tax',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Combination Final Price With Tax',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'tax_rate',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Tax Rate',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_tax_rules_group',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Tax Rules Group ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'unit_price_ratio',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Unit Price Ratio',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'unit_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Unit Price Without Tax',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'unit_price_with_tax',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Unit Price With Tax',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'unity',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Unity',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'ecotax',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Ecotax',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'on_sale',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Display The On Sale Icon',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_specific_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Specific Price ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'specific_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Specific Fixed Price',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'specific_price_reduction',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Specific Price Reduction',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'specific_price_reduction_type',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Specific Price Reduction Type',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'specific_price_from',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Specific Price Available From Date',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'specific_price_to',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Specific Price Available To Date',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'specific_price_from_quantity',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Specific Price Starting At Unit',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'specific_price_id_group',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Specific Price Group ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                  'id'     => 'specific_price_id_customer',
                  'name'   => Module::getInstanceByName('exportproducts')->l('Specific Price Customer ID',__CLASS__),
                  'gmf_id' => false,
                  'gmf_doc_link' => false
                ]
            ]
        ];

        $export_field_options[2] = [
            'tab'    => 'exportTabSeo',
            'name'   => Module::getInstanceByName('exportproducts')->l('SEO',__CLASS__),
            'fields' => [
                [
                    'id'     => 'link_rewrite',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Friendly URL',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'meta_title',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Meta Title',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'meta_description',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Meta Description',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'meta_keywords',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Meta Keywords',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ]
            ]
        ];

        $export_field_options[3] = [
            'tab'    => 'exportTabAssociations',
            'name'   => Module::getInstanceByName('exportproducts')->l('Associations',__CLASS__),
            'fields' => [
                [
                    'id'     => 'google_category_id',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Google Category ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'google_category',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Google Category',__CLASS__),
                    'gmf_id' => 'google_product_category',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324436'
                ],
                [
                    'id'     => 'default_category_tree',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Category Tree',__CLASS__),
                    'gmf_id' => 'product_type',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324406'
                ],
                [
                    'id'     => 'categories_ids',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Associated Categories IDs',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'separated_categories',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Categories Tree (each category tree in a separate field)',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'categories_names',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Associated Categories Name',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_category_default',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Category Default ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'default_category_name',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Category Default Name',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_product_accessories',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Accessories Product ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_manufacturer',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Manufacturer ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'manufacturer_name',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Manufacturer Name',__CLASS__),
                    'gmf_id' => 'brand',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324351'
                ]
            ]
        ];

        $export_field_options[4] = [
            'tab'    => 'exportTabShipping',
            'name'   => Module::getInstanceByName('exportproducts')->l('Shipping',__CLASS__),
            'fields' => [
                [
                    'id'     => 'weight',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Weight',__CLASS__),
                    'gmf_id' => 'shipping_weight',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324503'
                ],
                [
                    'id'     => 'weight_unit',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Weight Unit',__CLASS__),
                    'gmf_id' => '',
                    'gmf_doc_link' => ''
                ],
                [
                    'id'     => 'width',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Width',__CLASS__),
                    'gmf_id' => 'shipping_width',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324498'
                ],
                [
                    'id'     => 'height',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Height',__CLASS__),
                    'gmf_id' => 'shipping_height',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324498'
                ],
                [
                    'id'     => 'depth',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Depth',__CLASS__),
                    'gmf_id' => 'shipping_length',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324498'
                ],
                [
                    'id'     => 'dimension_unit',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Dimension Unit',__CLASS__),
                    'gmf_id' => '',
                    'gmf_doc_link' => ''
                ],
                [
                    'id'     => 'additional_shipping_cost',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Additional Shipping Fees',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_carriers',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Product Carriers ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'additional_delivery_times',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Delivery Time',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'delivery_in_stock',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Delivery Time Of In Stock Products',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'delivery_out_stock',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Delivery Time Of Out Of Stock Products With Allowed Orders',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ]
            ]
        ];

        $export_field_options[5] = [
            'tab'    => 'exportTabCombinations',
            'name'   => Module::getInstanceByName('exportproducts')->l('Combinations',__CLASS__),
            'fields' => [
                [
                    'id'     => 'id_product_attribute',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Product Combinations ID',__CLASS__),
                    'gmf_id' => 'id',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324507'
                ],
                [
                    'id'     => 'is_default_combination',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Is Default Combination',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combinations_name',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Attribute Group Attribute Value',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combinations_value',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Attribute Value (each value in separate field)',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combinations_reference',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations Reference Code',__CLASS__),
                    'gmf_id' => 'id',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324507'
                ],
                [
                    'id'     => 'combinations_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations Impact On Price Without Tax',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combinations_price_with_tax',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations Impact On Price With Tax',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combinations_unit_price_impact',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations Impact On Unit Price',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combinations_wholesale_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations Wholesale Price',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combinations_ean13',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations EAN-13 Or JAN Barcode',__CLASS__),
                    'gmf_id' => 'gtin',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324461'
                ],
                [
                    'id'     => 'combinations_upc',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations UPC Barcode',__CLASS__),
                    'gmf_id' => 'gtin',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324461'
                ],
                [
                    'id'     => 'combinations_isbn',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations ISBN',__CLASS__),
                    'gmf_id' => 'gtin',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324461'
                ],
                [
                    'id'     => 'combinations_mpn',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations MPN',__CLASS__),
                    'gmf_id' => 'mpn',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324482'
                ],
                [
                    'id'     => 'combinations_ecotax',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combination Ecotax',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combinations_location',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations Location',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'combinations_weight',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Combinations Impact On Weight',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'cache_default_attribute',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Product Combination ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ]
            ]
        ];

        $all_attribute_groups = \AttributeGroup::getAttributesGroups(\Context::getContext()->language->id);
        foreach ($all_attribute_groups as $attribute_group) {
            $attribute_group_field = [
                'id'     => 'attribute_group_' . $attribute_group['id_attribute_group'],
                'name'   => Module::getInstanceByName('exportproducts')->l('Attribute Group ' . $attribute_group['name']),
                'gmf_id' => false,
                'gmf_doc_link' => false
            ];

            if (strpos(Tools::strtolower($attribute_group['name']), 'color') !== false) {
                $attribute_group_field['gmf_id'] = 'color';
                $attribute_group_field['gmf_doc_link'] = 'https://support.google.com/merchants/answer/6324487?hl=en&ref_topic=6324338';
            } else if (strpos(Tools::strtolower($attribute_group['name']), 'gender') !== false) {
                $attribute_group_field['gmf_id'] = 'gender';
                $attribute_group_field['gmf_doc_link'] = 'https://support.google.com/merchants/answer/6324479?hl=en&ref_topic=6324338';
            } else if (strpos(Tools::strtolower($attribute_group['name']), 'material') !== false) {
                $attribute_group_field['gmf_id'] = 'material';
                $attribute_group_field['gmf_doc_link'] = 'https://support.google.com/merchants/answer/6324410?hl=en&ref_topic=6324338';
            } else if (strpos(Tools::strtolower($attribute_group['name']), 'material') !== false) {
                $attribute_group_field['gmf_id'] = 'pattern';
                $attribute_group_field['gmf_doc_link'] = 'https://support.google.com/merchants/answer/6324483?hl=en&ref_topic=6324338';
            } else if (strpos(Tools::strtolower($attribute_group['name']), 'size') !== false) {
                $attribute_group_field['gmf_id'] = 'size';
                $attribute_group_field['gmf_doc_link'] = 'https://support.google.com/merchants/answer/6324492?hl=en&ref_topic=6324338';
            }

            array_unshift($export_field_options[5]['fields'], $attribute_group_field);
        }

        $export_field_options[6] = [
            'tab'    => 'exportTabQuantities',
            'name'   => Module::getInstanceByName('exportproducts')->l('Quantities',__CLASS__),
            'fields' => [
                [
                    'id'     => 'availability',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Availability',__CLASS__),
                    'gmf_id' => 'availability',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324448'
                ],
                [
                    'id'     => 'quantity',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Quantity',__CLASS__),
                    'gmf_id' => 'quantity',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/7569847'
                ],
                [
                    'id'     => 'total_quantity',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Total Quantity With Combinations',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'minimal_quantity',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Minimum Quantity',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'location',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Stock Location',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'low_stock_threshold',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Low Stock Level',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'low_stock_alert',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Low Stock Email Alert',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'out_of_stock',
                    'name'   => Module::getInstanceByName('exportproducts')->l('When Out Of Stock',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'available_now',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Displayed Text When In Stock',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'available_later',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Displayed Text When Backordering Is Allowed',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'advanced_stock_management',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Advanced Stock Management',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'depends_on_stock',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Depends On Stock',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'pack_stock_type',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Pack Stock Type',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'available_date',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Availability Date',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
            ]
        ];

        $export_field_options[7] = [
            'tab'    => 'exportTabImages',
            'name'   => Module::getInstanceByName('exportproducts')->l('Images',__CLASS__),
            'fields' => [
                [
                    'id'     => 'images',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Product Image Urls',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'cover_image_url',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Product Cover Image Url',__CLASS__),
                    'gmf_id' => 'image_link',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324350'
                ],
                [
                    'id'     => 'images_value',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Product Image Urls In Separate Columns',__CLASS__),
                    'gmf_id' => 'additional_image_link',
                    'gmf_doc_link' => 'https://support.google.com/merchants/answer/6324370'
                ],
                [
                    'id'     => 'image_cover',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Product Cover Image',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'image_caption',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Product Image Caption',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ]
            ]
        ];

        $export_field_options[8] = [
            'tab'    => 'exportTabFeatures',
            'name'   => Module::getInstanceByName('exportproducts')->l('Features',__CLASS__),
            'fields' => []
        ];

        $all_features = \Feature::getFeatures(\Context::getContext()->language->id);
        foreach ($all_features as $feature) {
            array_push($export_field_options[8]['fields'], [
                'id'     => 'feature_' . $feature['id_feature'],
                'name'   => Module::getInstanceByName('exportproducts')->l('Feature ' . $feature['name']),
                'gmf_id' => false,
                'gmf_doc_link' => false
            ]);
        }

        $export_field_options[9] = [
            'tab'    => 'exportTabCustomization',
            'name'   => Module::getInstanceByName('exportproducts')->l('Customization',__CLASS__),
            'fields' => [
                [
                    'id'     => 'customizable',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Customizable',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'uploadable_files',
                    'name'   => Module::getInstanceByName('exportproducts')->l('File Fields',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'text_fields',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Text Fields',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'customization_field_type',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Customization Fields Type',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'customization_field_name',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Customization Fields Label',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'customization_field_required',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Customization Fields Is Required',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ]
            ]
        ];

        $export_field_options[10] = [
            'tab'    => 'exportTabAttachments',
            'name'   => Module::getInstanceByName('exportproducts')->l('Attachments',__CLASS__),
            'fields' => [
                [
                    'id'     => 'id_attachments',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Attachments ID',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'attachments_name',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Attachments Name',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'attachments_description',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Attachments Description',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'attachments_file',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Attachments File URL',__CLASS__),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'cache_has_attachments',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Has Attachments'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ]
            ]
        ];

        $export_field_options[11] = [
            'tab'    => 'exportTabSuppliers',
            'name'   => Module::getInstanceByName('exportproducts')->l('Suppliers'),
            'fields' => [
                [
                    'id'     => 'suppliers_ids',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Supplier IDs'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'suppliers_name',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Suppliers'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'id_supplier',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Supplier ID'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'supplier_name',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Supplier name'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'supplier_reference',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Supplier Reference'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'supplier_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Supplier Unit Price Without Tax'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'supplier_price_currency',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Default Supplier Unit Price Currency'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'separate_supplier_id',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Supplier ID'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'separate_supplier_name',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Supplier Name'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'separate_supplier_reference',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Supplier Reference'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'separate_supplier_price',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Supplier Price Without Tax'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ],
                [
                    'id'     => 'separate_supplier_currency',
                    'name'   => Module::getInstanceByName('exportproducts')->l('Supplier Currency'),
                    'gmf_id' => false,
                    'gmf_doc_link' => false
                ]
            ]
        ];

        return $export_field_options;
    }

    public static function getSavedFields($id_configuration)
    {
        $query = 'SELECT * FROM ' . self::TABLE_NAME_WITH_PREFIX . ' AS s
                  WHERE s.id_configuration = ' . (int)$id_configuration;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * Used for migration from old to pro version
     *
     * @param $export_field_id
     * @return bool|string
     */
    public static function getExportFieldTabByFieldId($export_field_id)
    {
        $information_tab_fields = [
            'id_product',
            'name',
            'name_with_combination',
            'reference',
            'active',
            'description_short',
            'description',
            'tags',
            'ean13',
            'upc',
            'isbn',
            'condition',
            'new',
            'available_for_order',
            'online_only',
            'is_virtual',
            'visibility',
            'cache_is_pack',
            'product_link',
            'date_add',
            'date_upd',
            'id_shop_default',
            'quantity_discount',
            'redirect_type',
            'id_product_redirected',
            'indexed',
            'id_color_default',
            'isFullyLoaded',
            'pack_items_id',
            'pack_items_name',
            'pack_items_id_pack_product_attribute',
            'pack_items_reference',
            'pack_items_ean13',
            'pack_items_upc'
        ];

        $prices_tab_fields = [
            'show_price',
            'wholesale_price',
            'base_price',
            'base_price_with_tax',
            'price',
            'final_price_with_tax',
            'combination_final_price_pre_tax',
            'combination_final_price_with_tax',
            'tax_rate',
            'id_tax_rules_group',
            'unit_price_ratio',
            'unit_price',
            'unity',
            'ecotax',
            'on_sale',
            'id_specific_price',
            'specific_price',
            'specific_price_reduction',
            'specific_price_reduction_type',
            'specific_price_from',
            'specific_price_to',
            'specific_price_from_quantity',
            'specific_price_id_group',
            'specific_price_id_customer',
        ];

        $seo_tab_fields = [
            'link_rewrite',
            'meta_title',
            'meta_description',
            'meta_keywords'
        ];

        $associations_tab_fields = [
            'categories_ids',
            'separated_categories',
            'categories_names',
            'id_category_default',
            'category_default_name',
            'id_product_accessories',
            'id_manufacturer',
            'manufacturer_name'
        ];

        $shipping_tab_fields = [
            'width',
            'height',
            'depth',
            'weight',
            'additional_shipping_cost',
            'id_carriers',
            'additional_delivery_times',
            'delivery_in_stock',
            'delivery_out_stock'
        ];

        $combinations_tab_fields = [
            'id_product_attribute',
            'combinations_name',
            'combinations_value',
            'combinations_reference',
            'combinations_price',
            'combinations_price_with_tax',
            'combinations_unit_price_impact',
            'combinations_wholesale_price',
            'cache_default_attribute',
            'combinations_ean13',
            'combinations_upc',
            'combinations_isbn',
            'combinations_ecotax',
            'combinations_location',
            'combinations_weight',
        ];

        foreach( AttributeGroup::getAttributesGroups( ContextCore::getContext()->language->id ) as $attribute ){
            $combinations_tab_fields[] = 'attribute_group_'.$attribute['id_attribute_group'];
        }

        $quantities_tab_fields = [
            'quantity',
            'total_quantity',
            'minimal_quantity',
            'location',
            'low_stock_threshold',
            'low_stock_alert',
            'out_of_stock',
            'available_now',
            'available_later',
            'advanced_stock_management',
            'depends_on_stock',
            'pack_stock_type',
            'available_date'
        ];

        $images_tab_fields = [
            'images',
            'cover_image_url',
            'images_value',
            'image_cover',
            'image_caption'
        ];

        $features_tab_fields = [];

        foreach( Feature::getFeatures( Context::getContext()->language->id ) as $feature ){
            $features_tab_fields[] = 'feature_'.$feature['id_feature'];
        }

        $customization_tab_fields = [
            'customizable',
            'uploadable_files',
            'text_fields',
            'customization_field_type',
            'customization_field_name',
            'customization_field_required'
        ];

        $attachments_tab_fields = [
            'id_attachments',
            'attachments_name',
            'attachments_description',
            'attachments_file',
            'cache_has_attachments'
        ];

        $suppliers_tab_fields = [
            'suppliers_ids',
            'suppliers_name',
            'id_supplier',
            'supplier_name',
            'supplier_reference',
            'supplier_price',
            'supplier_price_currency',
            'separate_supplier_id',
            'separate_supplier_name',
            'separate_supplier_reference',
            'separate_supplier_price',
            'separate_supplier_currency'
        ];

        if (in_array($export_field_id, $information_tab_fields)) {
            return 'exportTabInformation';
        } elseif (in_array($export_field_id, $prices_tab_fields)) {
            return 'exportTabPrices';
        } elseif (in_array($export_field_id, $seo_tab_fields)) {
            return 'exportTabSeo';
        } elseif (in_array($export_field_id, $associations_tab_fields)) {
            return 'exportTabAssociations';
        } elseif (in_array($export_field_id, $shipping_tab_fields)) {
            return 'exportTabShipping';
        } elseif (in_array($export_field_id, $combinations_tab_fields)) {
            return 'exportTabCombinations';
        } elseif (in_array($export_field_id, $quantities_tab_fields)) {
            return 'exportTabQuantities';
        } elseif (in_array($export_field_id, $images_tab_fields)) {
            return 'exportTabImages';
        } elseif (in_array($export_field_id, $features_tab_fields)) {
            return 'exportTabFeatures';
        } elseif (in_array($export_field_id, $customization_tab_fields)) {
            return 'exportTabCustomization';
        } elseif (in_array($export_field_id, $attachments_tab_fields)) {
            return 'exportTabAttachments';
        } elseif (in_array($export_field_id, $suppliers_tab_fields)) {
            return 'exportTabSuppliers';
        }

        return false;
    }
}