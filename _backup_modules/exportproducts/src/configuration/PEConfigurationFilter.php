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

class PEConfigurationFilter
{
    const TABLE_NAME = 'pe_configuration_filter';
    const TABLE_NAME_WITH_PREFIX = _DB_PREFIX_ . self::TABLE_NAME;

    public static function createTableInDb()
    {
        self::dropTableFromDb();

        $query = 'CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME_WITH_PREFIX . '` (
				`id_configuration_filter` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_configuration` INT(11) NULL,
		        `field` VARCHAR(128) NULL,
		        `field_type` VARCHAR(128) NULL,
		        `value` TEXT NOT NULL,
                `label` VARCHAR(128) NULL,
				PRIMARY KEY (`id_configuration_filter`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        return \Db::getInstance()->execute($query);
    }

    public static function dropTableFromDb()
    {
        $query = 'DROP TABLE IF EXISTS `' . self::TABLE_NAME_WITH_PREFIX . '`';
        \Db::getInstance()->execute($query);
    }

    public static function save($id_configuration, $filters)
    {
        self::validate($filters);

        foreach ($filters as $filter) {
            if ($filter['value'] == '') {
                continue;
            }

            $filter_data = [
                'id_configuration' => (int)$id_configuration,
                'field'            => pSQL($filter['field']),
                'field_type'       => pSQL($filter['field_type']),
                'label'            => pSQL($filter['label']),
                'value'            => json_encode($filter['value']),
            ];

            \Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(self::TABLE_NAME, $filter_data);
        }

        return true;
    }

    public static function validate($filters)
    {
        if (empty($filters)) {
            return false;
        }

        foreach ($filters as $filter) {
            if (($filter['field_type'] == 'number') &&
                (($filter['value']['type'] == '>') ||
                    ($filter['value']['type'] == '<') ||
                    ($filter['value']['type'] == '>=') ||
                    ($filter['value']['type'] == '<=') ||
                    ($filter['value']['type'] == '=') ||
                    ($filter['value']['type'] == '!=')) && !\Validate::isFloat($filter['value']['value'])
            ) {
                throw new \Exception(Module::getInstanceByName('exportproducts')->l('Filter field ') . $filter['label'] . Module::getInstanceByName('exportproducts')->l(' value is not valid',__CLASS__));
            }

            if (($filter['field_type'] == 'number') && (($filter['value']['type'] == 'in') || ($filter['value']['type'] == 'not_in'))) {
                $val = explode(",", $filter['value']['value']);
                if ($val) {
                    foreach ($val as $v) {
                        if (!\Validate::isFloat($v)) {
                            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Filter field ') . $filter['label'] . Module::getInstanceByName('exportproducts')->l(' value is not valid',__CLASS__));
                        }
                    }
                }
            }
        }

        return true;
    }

    public static function getByConfigurationId($id_configuration)
    {
        $query = '
        SELECT *
        FROM ' . self::TABLE_NAME_WITH_PREFIX . '  AS c
        WHERE c.id_configuration = ' . (int)$id_configuration . '
			';

        $res = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (isset($res[0]) && $res[0]) {
            return $res;
        }

        return false;
    }

    public static function removeByConfigurationId($id_configuration)
    {
        return \Db::getInstance()->execute("DELETE FROM " . self::TABLE_NAME_WITH_PREFIX . " WHERE id_configuration = " . (int)$id_configuration);
    }

    public static function getAllOptions()
    {
        return [
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Product ID',__CLASS__),
                'id'   => 'product_id',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination ID',__CLASS__),
                'id'   => 'id_product_attribute',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Has Combinations',__CLASS__),
                'id'   => 'has_combinations',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Quantity',__CLASS__),
                'id'   => 'quantity',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Total Quantity',__CLASS__),
                'id'   => 'total_quantity',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Product name',__CLASS__),
                'id'   => 'product_name_clean',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Product Reference',__CLASS__),
                'id'   => 'reference',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Active Status',__CLASS__),
                'id'   => 'active',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Short Description',__CLASS__),
                'id'   => 'description_short',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Description',__CLASS__),
                'id'   => 'description',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Tags',__CLASS__),
                'id'   => 'tags',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('EAN-13 or JAN barcode',__CLASS__),
                'id'   => 'ean13',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('ISBN',__CLASS__),
                'id'   => 'isbn',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Condition',__CLASS__),
                'id'   => 'condition',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Available For Order',__CLASS__),
                'id'   => 'available_for_order',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Online Only',__CLASS__),
                'id'   => 'online_only',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Is Virtual',__CLASS__),
                'id'   => 'is_virtual',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Visibility',__CLASS__),
                'id'   => 'visibility',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Cache Is Pack',__CLASS__),
                'id'   => 'cache_is_pack',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Default Shop ID',__CLASS__),
                'id'   => 'id_shop_default',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Quantity Discount',__CLASS__),
                'id'   => 'quantity_discount',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Redirect Type',__CLASS__),
                'id'   => 'redirect_type',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Indexed',__CLASS__),
                'id'   => 'indexed',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Pack Items Product ID',__CLASS__),
                'id'   => 'pack_items_id',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Pack Items Name',__CLASS__),
                'id' => 'pack_items_name',
                'type' => 'string'
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Pack Items Product Attribute ID',__CLASS__),
                'id' => 'pack_items_id_pack_product_attribute',
                'type' => 'number'
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Pack Items Reference',__CLASS__),
                'id' => 'pack_items_reference',
                'type' => 'string'
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Pack Items EAN13',__CLASS__),
                'id' => 'pack_items_ean13',
                'type' => 'string'
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Pack Items UPC',__CLASS__),
                'id' => 'pack_items_upc',
                'type' => 'string'
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Product Manufacturers',__CLASS__),
                'id'   => 'manufacturers',
                'type' => 'checkbox',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Product Categories',__CLASS__),
                'id'   => 'categories',
                'type' => 'tree',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Is New',__CLASS__),
                'id'   => 'is_new',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Product Add Date',__CLASS__),
                'id'   => 'date_add',
                'type' => 'date',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Date Update',__CLASS__),
                'id'   => 'date_update',
                'type' => 'date',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Show Price',__CLASS__),
                'id'   => 'show_price',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Wholesale Price',__CLASS__),
                'id'   => 'wholesale_price',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Price',__CLASS__),
                'id'   => 'price',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('ID Tax Rules Group',__CLASS__),
                'id'   => 'id_tax_rules_group',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Unit Price Ratio',__CLASS__),
                'id'   => 'unit_price_ratio',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Ecotax',__CLASS__),
                'id'   => 'ecotax',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('On Sale',__CLASS__),
                'id'   => 'on_sale',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Specific Price',__CLASS__),
                'id'   => 'specific_price',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Specific Price Reduction',__CLASS__),
                'id'   => 'specific_price_reduction',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Specific Price Reduction Type',__CLASS__),
                'id'   => 'specific_price_reduction_type',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Specific Price Available From Date',__CLASS__),
                'id'   => 'specific_price_from',
                'type' => 'date',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Specific Price Available To Date',__CLASS__),
                'id'   => 'specific_price_to',
                'type' => 'date',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Specific Price Starting At Unit',__CLASS__),
                'id'   => 'specific_price_from_quantity',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Specific Price Group ID',__CLASS__),
                'id'   => 'specific_price_id_group',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Specific Price Customer ID',__CLASS__),
                'id'   => 'specific_price_id_customer',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Friendly URL',__CLASS__),
                'id'   => 'link_rewrite',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Meta Title',__CLASS__),
                'id'   => 'meta_title',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Meta Description',__CLASS__),
                'id'   => 'meta_description',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Meta Keywords',__CLASS__),
                'id'   => 'meta_keywords',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Category Default ID',__CLASS__),
                'id'   => 'id_category_default',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Category Default Name',__CLASS__),
                'id'   => 'default_category_name',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Accessories Product ID',__CLASS__),
                'id'   => 'id_product_accessories',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Manufacturer ID',__CLASS__),
                'id'   => 'id_manufacturer',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Manufacturer Name',__CLASS__),
                'id'   => 'manufacturer_name',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Package Width',__CLASS__),
                'id'   => 'width',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Package Height',__CLASS__),
                'id'   => 'height',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Package Depth',__CLASS__),
                'id'   => 'depth',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Product Carriers ID',__CLASS__),
                'id'   => 'id_carriers',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Additional shipping fees',__CLASS__),
                'id'   => 'additional_shipping_cost',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Delivery Time',__CLASS__),
                'id'   => 'additional_delivery_times',
                'type' => 'checkbox',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Delivery time of in-stock products',__CLASS__),
                'id'   => 'delivery_in_stock',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Delivery time of out-of-stock products with allowed orders',__CLASS__),
                'id'   => 'delivery_out_stock',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Displayed Text When In-Stock',__CLASS__),
                'id'   => 'available_now',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Displayed Text When Backordering Is Allowed',__CLASS__),
                'id'   => 'available_later',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Advanced Stock Management',__CLASS__),
                'id'   => 'advanced_stock_management',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Pack Stock Type',__CLASS__),
                'id'   => 'pack_stock_type',
                'type' => 'checkbox',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Availability Date',__CLASS__),
                'id'   => 'available_date',
                'type' => 'date',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Image Caption',__CLASS__),
                'id'   => 'image_caption',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Customizable',__CLASS__),
                'id'   => 'customizable',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('File Fields',__CLASS__),
                'id'   => 'uploadable_files',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Text Fields',__CLASS__),
                'id'   => 'text_fields',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Customization Fields Type',__CLASS__),
                'id'   => 'customization_field_type',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Customization Fields Label',__CLASS__),
                'id'   => 'customization_field_label',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Customization Fields Is Required',__CLASS__),
                'id'   => 'customization_field_required',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination Reference',__CLASS__),
                'id'   => 'combination_reference',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination Impact On Price',__CLASS__),
                'id'   => 'combination_price_impact',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination Impact On Unit Price',__CLASS__),
                'id'   => 'combination_unit_price_impact',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination Wholesale Price',__CLASS__),
                'id'   => 'combination_wholesale_price',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination EAN-13 or JAN Barcode',__CLASS__),
                'id'   => 'combination_ean',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination UPC Barcode',__CLASS__),
                'id'   => 'combination_upc',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination ISBN',__CLASS__),
                'id'   => 'combination_isbn',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination Ecotax',__CLASS__),
                'id'   => 'combination_ecotax',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination Location',__CLASS__),
                'id'   => 'combination_location',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Combination Impact On Weight',__CLASS__),
                'id'   => 'combination_weight_impact',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Attribute Group',__CLASS__),
                'id'   => 'attribute_group',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Attribute',__CLASS__),
                'id'   => 'attribute',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Feature',__CLASS__),
                'id'   => 'feature',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Feature Value',__CLASS__),
                'id'   => 'feature_value',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Has Attachments',__CLASS__),
                'id'   => 'cache_has_attachments',
                'type' => 'select',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Attachments ID',__CLASS__),
                'id'   => 'id_attachments',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Attachments Name',__CLASS__),
                'id'   => 'attachments_name',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Attachments Description',__CLASS__),
                'id'   => 'attachments_description',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Attachments File Name',__CLASS__),
                'id'   => 'attachments_file',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Supplier ID',__CLASS__),
                'id'   => 'supplier_id',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Supplier Name',__CLASS__),
                'id'   => 'supplier_name',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Supplier Reference',__CLASS__),
                'id'   => 'supplier_reference',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Supplier Price Without Tax',__CLASS__),
                'id'   => 'supplier_price',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Supplier Currency ID',__CLASS__),
                'id'   => 'supplier_currency',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Default supplier ID',__CLASS__),
                'id'   => 'default_supplier_id',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Default Supplier Name',__CLASS__),
                'id'   => 'default_supplier_name',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Default Supplier Reference',__CLASS__),
                'id'   => 'default_supplier_reference',
                'type' => 'string',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Default Supplier Price Without Tax',__CLASS__),
                'id'   => 'default_supplier_price',
                'type' => 'number',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Default Supplier Currency ID',__CLASS__),
                'id'   => 'default_supplier_currency',
                'type' => 'number',
            ],
        ];
    }

    public static function getDateFormats()
    {
        return [
            [
                'id'   => 'Y-m-d H:i:s',
                'name' => 'Y-m-d H:i:s',
            ],
            [
                'id'   => 'Y-m-d',
                'name' => 'Y-m-d',
            ],
            [
                'id'   => 'd.m.Y H:i:s',
                'name' => 'd.m.Y H:i:s',
            ],
            [
                'id'   => 'd.m.Y',
                'name' => 'd.m.Y',
            ],
            [
                'id'   => 'Y.m.d H:i:s',
                'name' => 'Y.m.d H:i:s',
            ],
            [
                'id'   => 'Y.m.d',
                'name' => 'Y.m.d',
            ],
            [
                'id'   => 'm/d/Y H:i:s',
                'name' => 'm/d/Y H:i:s',
            ],
            [
                'id'   => 'm/d/Y',
                'name' => 'm/d/Y',
            ],
            [
                'id'   => 'd/m/Y H:i:s',
                'name' => 'd/m/Y H:i:s',
            ],
            [
                'id'   => 'd/m/Y',
                'name' => 'd/m/Y',
            ],
            [
                'id'   => 'Y-M-D G:i:s',
                'name' => 'Y-M-D G:i:s',
            ],
            [
                'id'   => 'Y-M-D',
                'name' => 'Y-M-D',
            ],
            [
                'id'   => 'Y M D G:i:s',
                'name' => 'Y M D G:i:s',
            ],
            [
                'id'   => 'Y M D',
                'name' => 'Y M D',
            ],
        ];
    }

    public static function getSortsValue()
    {
        return [
            [
                'name' => Module::getInstanceByName('exportproducts')->l('ID'),
                'id'   => 'id_product',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Name'),
                'id'   => 'name',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Default Category Name'),
                'id'   => 'default_category_name',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Retail Price Without Tax'),
                'id'   => 'price',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Total Quantity'),
                'id'   => 'quantity',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Date add'),
                'id'   => 'date_add',
            ],
            [
                'name' => Module::getInstanceByName('exportproducts')->l('Date update'),
                'id'   => 'date_update',
            ]
        ];
    }

    public static function getConditionsOptions()
    {
        return [
            [
                'value' => '1',
                'name'  => Module::getInstanceByName('exportproducts')->l('< Less than'),
            ],
            [
                'value' => '2',
                'name'  => Module::getInstanceByName('exportproducts')->l('> Greater than'),
            ],
            [
                'value' => '3',
                'name'  => Module::getInstanceByName('exportproducts')->l('= Equal'),
            ],
            [
                'value' => '4',
                'name'  => Module::getInstanceByName('exportproducts')->l('!= Not Equal'),
            ],
            [
                'value' => '5',
                'name'  => Module::getInstanceByName('exportproducts')->l('Comma List (ex: itm1,itm2...)'),
            ],
            [
                'value' => '6',
                'name'  => Module::getInstanceByName('exportproducts')->l('Not in Comma List (ex: itm1,itm2...)'),
            ],
            [
                'value' => '7',
                'name'  => Module::getInstanceByName('exportproducts')->l('Empty'),
            ],
            [
                'value' => '8',
                'name'  => Module::getInstanceByName('exportproducts')->l('Not empty'),
            ],
            [
                'value' => '9',
                'name'  => Module::getInstanceByName('exportproducts')->l('regex'),
            ],
            [
                'value' => '10',
                'name'  => Module::getInstanceByName('exportproducts')->l('Any'),
            ],
        ];
    }

    public static function getSavedFilters($id_configuration)
    {
        $query = 'SELECT *
            FROM ' . self::TABLE_NAME_WITH_PREFIX . ' AS c
            WHERE c.id_configuration = ' . (int)$id_configuration . '
        ';

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getShopsOptions()
    {
        $shop_options = \Shop::getShops();

        if (count($shop_options) == 1) {
            return (int)key($shop_options);
        }

        return $shop_options;
    }

    public static function getPaymentOptions($id_shop)
    {
        if ($id_shop === false) {
            $id_shop = \Context::getContext()->shop->id;
        }

        $query = '
			SELECT DISTINCT o.payment as name
            FROM ' . _DB_PREFIX_ . 'product AS o
            WHERE o.id_shop = ' . (int)$id_shop . '
			';
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getCustomerOptions($id_shop = false, $search = false)
    {
        if ($id_shop === false) {
            $id_shop = \Context::getContext()->shop->id;
        }

        $where = "";
        if ($search) {
            $where = " AND (c.firstname LIKE '%" . pSQL($search) . "%' OR c.lastname LIKE '%" . pSQL($search) . "%' OR c.id_customer LIKE '%" . pSQL($search) . "%')";
        }

        $query = '
			SELECT c.id_customer AS id, c.id_customer,
			(SELECT concat(c.firstname, " ", c.lastname)) AS name,
			c.firstname, c.lastname
              FROM ' . _DB_PREFIX_ . 'customer  AS c
              WHERE c.id_shop = ' . (int)$id_shop . '
              ' . $where . '
              LIMIT 100
                    ';

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getSelectedCategoryOptions($ids, $id_shop = false, $id_lang = false)
    {
        if ($id_shop == null) {
            $id_shop = \Context::getContext()->shop->id;
        }

        if ($id_lang == null) {
            $id_lang = \Context::getContext()->language->id;
        }

        $query = '
			SELECT c.id_category, c.name
          FROM ' . _DB_PREFIX_ . 'category_lang  as c
          WHERE c.id_category IN(' . pSQL($ids) . ')
          AND c.id_lang = ' . (int)$id_lang . '
          AND c.id_shop = ' . (int)$id_shop . '

			';

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getManufacturerOptions()
    {
        $manufacturers_ids = [];
        $manufacturers = array_filter(\Manufacturer::getManufacturers(),
            function ($manufacturer) use (&$manufacturers_ids) {
                if (in_array($manufacturer['id_manufacturer'], $manufacturers_ids)) {
                    return false;
                }

                array_push($manufacturers_ids, $manufacturer['id_manufacturer']);
                return true;
            });

        return $manufacturers;
    }
}