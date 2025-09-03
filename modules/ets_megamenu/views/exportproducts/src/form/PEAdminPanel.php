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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationCore.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationFilter.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationField.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportProcess.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/PETask.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEGoogleCategory.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PESerializationChecker.php';

class PEAdminPanel
{
    public static function get()
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/index.tpl');

        $tpl->assign([
            'img_folder' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/exportproducts/views/img/',
            'token_product_export' => \Tools::getAdminTokenLite('AdminProductsExport'),
            'id_configuration' => \Tools::getValue('id_configuration'),
            'id_shop' => \Context::getContext()->shop->id,
            'id_lang' => \Context::getContext()->language->id,
            'left_column' => self::getLeftColumn(),
            'right_column' => self::getRightColumn(),
            'debug_mode' => Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_DEBUG_MODE')
        ]);

        return $tpl->fetch();
    }

    private static function getLeftColumn()
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/left-column.tpl');
        $default_tab_name = 'overview';
        $tab = \Tools::getValue('export_tab') ? \Tools::getValue('export_tab') : $default_tab_name;

        $tpl->assign([
            'tab' => $tab,
            'tab_content' => self::getTabContent($tab),
            'count_scheduled_tasks' => PETask::getNumberOfTasks(),
            'count_saved_options' => PEConfigurationCore::getNumOfSavedConfigurations(),
            'count_history' => PEExportProcess::count(),
        ]);

        return $tpl->fetch();
    }

    private static function getRightColumn()
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/right-column.tpl');

        $tpl->assign([
            'id_module' => \exportproducts::ID_MODULE
        ]);

        return $tpl->fetch();
    }

    public static function getControllerLink()
    {
        $admin_folder_name = str_replace(_PS_ROOT_DIR_ . '/', null, basename(_PS_ADMIN_DIR_));

        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $path = \Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . $admin_folder_name . '/';
            $controller_link = $path . \Context::getContext()->link->getAdminLink('AdminProductsExport', false);
        } else {
            $controller_link = \Context::getContext()->link->getAdminLink('AdminProductsExport', false);
        }

        return $controller_link . '&token=' . \Tools::getAdminTokenLite('AdminProductsExport');
    }

    public static function getTabContent($tab)
    {
        $tpl_name = 'tab-content-' . $tab . '.tpl';
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/' . $tpl_name);
        $tpl->assign(self::getTabContentTemplateVariables($tab));

        return $tpl->fetch();
    }

    private static function getTabContentTemplateVariables($tab)
    {
        $data = [
            'id_configuration' => \Tools::getValue('id_configuration'),
            'tab' => $tab,
            'img_folder' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/exportproducts/views/img/',
            'path_tpl' => _PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/',
        ];

        if ($tab == 'new_export') {
            $data['settings'] = false;
            $data['export_template_name'] = 'exported_product_{d-m-Y H:i:s}';
            $data['export_files_path'] = \Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/exportproducts/files/';
            $data['all_currencies'] = self::getCurrencies();
            $data['all_shops'] = PEConfigurationFilter::getShopsOptions();
            $data['all_languages'] = \Language::getLanguages();
            $data['date_format'] = PEConfigurationFilter::getDateFormats();
            $data['sorts'] = PEConfigurationFilter::getSortsValue();
            $data['filter_fields'] = PEConfigurationFilter::getAllOptions();
            $data['product_fields'] = PEConfigurationField::getFieldOptions();
            $data['images_types'] = \ImageType::getImagesTypes('products', true);
            $data['shop_categories_tree'] = self::getCategoriesTree('google_categories_tree', 'google_category', []);
            $data['shop_categories_associated_with_google'] = '';

            if (\Tools::getValue('id_configuration')) {
                $data['settings'] = PEConfigurationCore::getById(\Tools::getValue('id_configuration'));
                $data['saved_filters'] = self::getSavedFilterBlocks();
                $data['selected_export_fields'] = PEConfigurationField::getSavedFields(\Tools::getValue('id_configuration'));
                $data['shop_categories_tree'] = self::getCategoriesTree('google_categories_tree', 'google_category', PEGoogleCategory::getShopCategoriesLinkedToGoogleCategories($data['settings']));
                $data['saved_google_categories_data'] = PEGoogleCategory::getSavedGoogleCategoriesTplData($data['settings']);
                
                $shop_categories_associated_with_google = PEGoogleCategory::getShopCategoriesLinkedToGoogleCategories($data['settings']);
                $data['shop_categories_associated_with_google'] = empty($shop_categories_associated_with_google) ? '' : implode(',', $shop_categories_associated_with_google);

                if (!empty($data['selected_export_fields'])) {
                    foreach ($data['selected_export_fields'] as $k => $selected_field) {
                        $data['selected_export_fields'][$k]['line'] = 'default';
                        $data['selected_export_fields'][$k]['default_value_label'] = $selected_field['default_value'];

                        if ($selected_field['tab'] == 'staticTab') {
                            $fields_name = explode("_", $selected_field['field']);
                            $data['selected_export_fields'][$k]['line'] = $fields_name[0];

                            if (!empty($selected_field['conditions'])) {
                                if (PESerializationChecker::isStringSerialized($selected_field['conditions'])) {
                                    $conditions = Tools::unSerialize($selected_field['conditions']);
                                } else {
                                    $conditions = json_decode($selected_field['conditions'], true);
                                }

                                if (!empty($conditions) && $conditions != '[]') {
                                    $data['selected_export_fields'][$k]['condition_field'] = $conditions['condition_field'];
                                    $data['selected_export_fields'][$k]['condition'] = $conditions['condition'];
                                    $data['selected_export_fields'][$k]['condition_value'] = $conditions['condition_value'];
                                    $data['selected_export_fields'][$k]['formula_type'] = $conditions['formula_type'];
                                    $data['selected_export_fields'][$k]['format_as_price'] = $conditions['format_as_price'];
                                    $default_value = json_decode($selected_field['default_value'], true);
                                    $data['selected_export_fields'][$k]['default_value_label'] = implode(",", $default_value);
                                }
                            }
                        }
                    }
                }
            }
        }

        $controller_link = self::getControllerLink();

        if ($tab == 'saved_options') {
            $data['setting_url'] = $controller_link . '&export_tab=new_export&id_configuration=';
            $data['settings_list'] = PEConfigurationCore::getSavedConfigurations();
        }

        if ($tab == 'history') {
            $data['setting_url'] = $controller_link . '&export_tab=new_export&id_configuration=';
            $data['settings_list'] = PEExportProcess::getLastExecutedProcessesData(100);
            ;
        }

        if ($tab == 'overview') {
            $data['setting_url'] = $controller_link . '&export_tab=new_export&id_configuration=';
            $data['product_reports'] = PEExportProcess::getLastExecutedProcessesData(5);
            ;
        }

        if ($tab == 'scheduled_tasks') {
            $data['scheduled_tasks'] = PETask::getAllTasks();
            $data['schedule_url'] = PETask::getUrl();
        }

        return $data;
    }

    private static function getSavedFilterBlocks()
    {
        $res = '';
        $saved_filters = PEConfigurationFilter::getSavedFilters(\Tools::getValue('id_configuration'));

        if (!empty($saved_filters)) {
            foreach ($saved_filters as $filter) {
                $res .= self::getFilterBlock($filter['field_type'], $filter['field'], $filter['label'], $filter['value']);
            }
        }

        return $res;
    }

    public static function getFilterBlock($type, $id, $label, $values = false)
    {
        $list = [];
        $options = [];

        if ($type == 'date') {
            $list['selected'] = [];
            $list['selected']['type'] = false;

            $options = [
                ['name' => Module::getInstanceByName('exportproducts')->l('Today',__CLASS__), 'value' => 'today'],
                ['name' => Module::getInstanceByName('exportproducts')->l('Yesterday',__CLASS__), 'value' => 'yesterday'],
                ['name' => Module::getInstanceByName('exportproducts')->l('This week',__CLASS__), 'value' => 'this_week'],
                ['name' => Module::getInstanceByName('exportproducts')->l('Last week',__CLASS__), 'value' => 'last_week'],
                ['name' => Module::getInstanceByName('exportproducts')->l('Last 7 Days',__CLASS__), 'value' => 'last_seven_days'],
                ['name' => Module::getInstanceByName('exportproducts')->l('This month',__CLASS__), 'value' => 'this_month'],
                ['name' => Module::getInstanceByName('exportproducts')->l('Last month',__CLASS__), 'value' => 'last_month'],
                ['name' => Module::getInstanceByName('exportproducts')->l('Before date',__CLASS__), 'value' => 'before_date'],
                ['name' => Module::getInstanceByName('exportproducts')->l('After date',__CLASS__), 'value' => 'after_date'],
                ['name' => Module::getInstanceByName('exportproducts')->l('Select date',__CLASS__), 'value' => 'period'],
            ];
        }

        if ($type == 'select') {
            $options = [
                ['name' => Module::getInstanceByName('exportproducts')->l('Yes', __CLASS__), 'value' => '1'],
                ['name' => Module::getInstanceByName('exportproducts')->l('No', __CLASS__), 'value' => '0']
            ];

            if ($id == 'customization_field_type') {
                $options = [
                    ['name' => Module::getInstanceByName('exportproducts')->l('Text', __CLASS__), 'value' => '1'],
                    ['name' => Module::getInstanceByName('exportproducts')->l('File', __CLASS__), 'value' => '0']
                ];
            }

            $list['selected'] = false;
        }

        if ($type == 'string') {
            $list['selected'] = [];
            $list['selected']['type'] = false;

            $options = [
                ['name' => Module::getInstanceByName('exportproducts')->l('is', __CLASS__), 'value' => 'is'],
                ['name' => Module::getInstanceByName('exportproducts')->l('is not', __CLASS__), 'value' => 'is_not'],
                ['name' => Module::getInstanceByName('exportproducts')->l('comma list (ex: item1,item2..)', __CLASS__), 'value' => 'list'],
                ['name' => Module::getInstanceByName('exportproducts')->l('not comma list (ex: item1,item2..)', __CLASS__), 'value' => 'not_list'],
                ['name' => Module::getInstanceByName('exportproducts')->l('contains', __CLASS__), 'value' => 'contains'],
                ['name' => Module::getInstanceByName('exportproducts')->l('does not contain', __CLASS__), 'value' => 'not_contain'],
                ['name' => Module::getInstanceByName('exportproducts')->l('empty', __CLASS__), 'value' => 'empty'],
                ['name' => Module::getInstanceByName('exportproducts')->l('not_empty', __CLASS__), 'value' => 'not_empty'],
            ];
        }

        if ($type == 'number') {
            $list['selected'] = [];
            $list['selected']['type'] = false;

            $options = [
                ['name' => '>', 'value' => '>'],
                ['name' => '<', 'value' => '<'],
                ['name' => '>=', 'value' => '>='],
                ['name' => '<=', 'value' => '<='],
                ['name' => '==', 'value' => '='],
                ['name' => '!=', 'value' => '!='],
                ['name' => Module::getInstanceByName('exportproducts')->l('in', __CLASS__), 'value' => 'in'],
                ['name' => Module::getInstanceByName('exportproducts')->l('not_in', __CLASS__), 'value' => 'not_in'],
                ['name' => Module::getInstanceByName('exportproducts')->l('empty', __CLASS__), 'value' => 'empty'],
                ['name' => Module::getInstanceByName('exportproducts')->l('not_empty', __CLASS__), 'value' => 'not_empty'],
            ];
        }

        if ($type == 'checkbox') {
            if ($id == 'additional_delivery_times') {
                $list['values'] = [
                    ['value' => '0', 'name' => Module::getInstanceByName('exportproducts')->l('None', __CLASS__)],
                    ['value' => '1', 'name' => Module::getInstanceByName('exportproducts')->l('Use Default Information', __CLASS__)],
                    ['value' => '2', 'name' => Module::getInstanceByName('exportproducts')->l('Use Product Information', __CLASS__)],
                ];

                $list['key'] = 'value';
            }

            if ($id == 'specific_price_reduction_type') {
                $list['values'] = [
                    ['value' => 'amount', 'name' => Module::getInstanceByName('exportproducts')->l('Amount', __CLASS__)],
                    ['value' => 'percentage', 'name' => Module::getInstanceByName('exportproducts')->l('Percentage', __CLASS__)]
                ];

                $list['key'] = 'value';
            }

            if ($id == 'pack_stock_type') {
                $list['values'] = [
                    ['value' => \Pack::STOCK_TYPE_PACK_ONLY, 'name' => Module::getInstanceByName('exportproducts')->l('Pack Only', __CLASS__)],
                    ['value' => \Pack::STOCK_TYPE_PRODUCTS_ONLY, 'name' => Module::getInstanceByName('exportproducts')->l('Products Only', __CLASS__)],
                    ['value' => \Pack::STOCK_TYPE_PACK_BOTH, 'name' => Module::getInstanceByName('exportproducts')->l('Both', __CLASS__)],
                    ['value' => \Pack::STOCK_TYPE_DEFAULT, 'name' => Module::getInstanceByName('exportproducts')->l('Default', __CLASS__)]
                ];

                $list['key'] = 'value';
            }

            if ($id == 'customers_group') {
                $list['values'] = \Group::getGroups(\Context::getContext()->language->id);
                $list['key'] = 'id_group';
            }
            if ($id == 'carriers') {
                $list['values'] = \Carrier::getCarriers(\Context::getContext()->language->id, false, false, false, null,
                    false);
                $list['key'] = 'id_carrier';
            }
            if ($id == 'suppliers') {
                $list['values'] = \Supplier::getSuppliers();
                $list['key'] = 'id_supplier';
            }
            if ($id == 'visibility') {
                $list['values'] = [
                    ['id_visibility' => 'both', 'name' => Module::getInstanceByName('exportproducts')->l('Everywhere',__CLASS__)],
                    ['id_visibility' => 'catalog', 'name' => Module::getInstanceByName('exportproducts')->l('Catalog only',__CLASS__)],
                    ['id_visibility' => 'search', 'name' => Module::getInstanceByName('exportproducts')->l('Search',__CLASS__)],
                    ['id_visibility' => 'none', 'name' => Module::getInstanceByName('exportproducts')->l('Nowhere',__CLASS__)]
                ];

                $list['key'] = 'id_visibility';
            }
            if ($id == 'manufacturers') {
                $list['values'] = PEConfigurationFilter::getManufacturerOptions();
                $list['key'] = 'id_manufacturer';
            }
            if ($id == 'payment') {
                $list['values'] = PEConfigurationFilter::getPaymentOptions(\Context::getContext()->shop->id);
                $list['key'] = 'name';
            }
            if ($id == 'customers') {
                $list['values'] = PEConfigurationFilter::getCustomerOptions();
                $list['key'] = 'id';
            }

            $list['selected'] = [];
        }

        if ($type == 'tree') {
            $list['selected'] = [];
        }

        if ($values) {
            if (PESerializationChecker::isStringSerialized($values)) {
                $values = \Tools::unSerialize($values);
            } else {
                $values = json_decode($values, true);
            }

            $list['selected'] = $values;
        }

        if ($type == 'tree' && $values) {
            $list['selected'] = PEConfigurationFilter::getSelectedCategoryOptions(implode(',', $list['selected']));
        }

        $data = [
            'options' => $options,
            'type' => $type,
            'list' => $list,
            'name_field' => $id,
            'label' => $label,
            'path_tpl' => _PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/',
        ];

        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/filter-section.tpl');
        $tpl->assign($data);
        return $tpl->fetch();
    }

    public static function getExtraFieldForm()
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/extra_field_form.tpl');
        $condition_fields = [];
        $conditions = [];

        $type = \Tools::getValue('type');
        $condition = \Tools::getValue('condition');
        $condition_value = \Tools::getValue('condition_value');
        $value_default_field = \Tools::getValue('value_default_field');
        $formula_type = \Tools::getValue('formula_type') ? \Tools::getValue('formula_type') : '';
        $format_as_price = \Tools::getValue('format_as_price') ? \Tools::getValue('format_as_price') : '';
        $export_file_format = Tools::getValue('export_file_format');

        if (\Tools::getValue('type') == 'extra') {
            $conditions_all_fields = PEConfigurationField::getFieldOptions();
            $conditions = PEConfigurationFilter::getConditionsOptions();

            $condition_fields = array_merge(
                $conditions_all_fields[0]['fields'], $conditions_all_fields[1]['fields'],
                $conditions_all_fields[2]['fields'], $conditions_all_fields[3]['fields'],
                $conditions_all_fields[4]['fields'], $conditions_all_fields[5]['fields'],
                $conditions_all_fields[6]['fields'], $conditions_all_fields[7]['fields'],
                $conditions_all_fields[8]['fields'], $conditions_all_fields[9]['fields'],
                $conditions_all_fields[10]['fields'], $conditions_all_fields[11]['fields']
            );
        }

        if ($type == 'extra') {
            $condition = json_decode($condition, true);
            $condition_value = json_decode($condition_value, true);
            $value_default_field = json_decode($value_default_field, true);
            $formula_type = json_decode($formula_type, true);
            $format_as_price = json_decode($format_as_price, true);
        }

        $tpl->assign([
            'conditions' => $conditions,
            'condition_fields' => $condition_fields,
            'condition_field' => \Tools::getValue('condition_field'),
            'condition' => $condition,
            'condition_value' => $condition_value,
            'formula_type' => $formula_type,
            'format_as_price' => $format_as_price,
            'type' => \Tools::getValue('type'),
            'custom_field' => \Tools::getValue('custom_field'),
            'name_field' => \Tools::getValue('name_field'),
            'value_default_field' => $value_default_field,
            'gmf_attributes' => (($export_file_format == 'gmf') ? self::getGoogleMerchantFeedAttributes() : false),
            'selected_gmf_attribute' => Tools::getValue('selected_gmf_attribute')
        ]);

        return $tpl->fetch();
    }

    private static function getGoogleMerchantFeedAttributes()
    {
        return [
            ['id' => 'id', 'doc_link' => 'https://support.google.com/merchants/answer/6324405'],
            ['id' => 'item_group_id', 'doc_link' => 'https://support.google.com/merchants/answer/6324507'],
            ['id' => 'title', 'doc_link' => 'https://support.google.com/merchants/answer/6324415'],
            ['id' => 'description', 'doc_link' => 'https://support.google.com/merchants/answer/6324468'],
            ['id' => 'link', 'doc_link' => 'https://support.google.com/merchants/answer/6324416'],
            ['id' => 'image_​​link', 'doc_link' => 'https://support.google.com/merchants/answer/6324350'],
            ['id' => 'additional_image_​​link', 'doc_link' => 'https://support.google.com/merchants/answer/6324370'],
            ['id' => 'mobile_link', 'doc_link' => 'https://support.google.com/merchants/answer/6324459'],
            ['id' => 'availability', 'doc_link' => 'https://support.google.com/merchants/answer/6324448'],
            ['id' => 'availability_date', 'doc_link' => 'https://support.google.com/merchants/answer/6324470'],
            ['id' => 'cost_of_goods_sold', 'doc_link' => 'https://support.google.com/merchants/answer/9017895'],
            ['id' => 'expiration_date', 'doc_link' => 'https://support.google.com/merchants/answer/6324499'],
            ['id' => 'price', 'doc_link' => 'https://support.google.com/merchants/answer/6324371'],
            ['id' => 'sale_price', 'doc_link' => 'https://support.google.com/merchants/answer/6324471'],
            ['id' => 'sale_price_effective_date', 'doc_link' => 'https://support.google.com/merchants/answer/6324460'],
            ['id' => 'unit_pricing_measure', 'doc_link' => 'https://support.google.com/merchants/answer/6324455'],
            ['id' => 'unit_pricing_base_measure', 'doc_link' => 'https://support.google.com/merchants/answer/6324490'],
            ['id' => 'installment', 'doc_link' => 'https://support.google.com/merchants/answer/6324474'],
            ['id' => 'subscription_cost', 'doc_link' => 'https://support.google.com/merchants/answer/7437904'],
            ['id' => 'loyalty_points', 'doc_link' => 'https://support.google.com/merchants/answer/6324456'],
            ['id' => 'google_product_category', 'doc_link' => 'https://support.google.com/merchants/answer/6324436'],
            ['id' => 'product_type', 'doc_link' => 'https://support.google.com/merchants/answer/6324406'],
            ['id' => 'brand', 'doc_link' => 'https://support.google.com/merchants/answer/6324351'],
            ['id' => 'gtin', 'doc_link' => 'https://support.google.com/merchants/answer/6324461'],
            ['id' => 'mpn', 'doc_link' => 'https://support.google.com/merchants/answer/6324482'],
            ['id' => 'identifier_exists', 'doc_link' => 'https://support.google.com/merchants/answer/6324478'],
            ['id' => 'condition', 'doc_link' => 'https://support.google.com/merchants/answer/6324469'],
            ['id' => 'adult', 'doc_link' => 'https://support.google.com/merchants/answer/6324508'],
            ['id' => 'multipack', 'doc_link' => 'https://support.google.com/merchants/answer/6324488'],
            ['id' => 'is_bundle', 'doc_link' => 'https://support.google.com/merchants/answer/6324449'],
            ['id' => 'energy_efficiency_class', 'doc_link' => 'https://support.google.com/merchants/answer/6324491'],
            ['id' => 'min_energy_efficiency_class ', 'doc_link' => 'https://support.google.com/merchants/answer/7562785'],
            ['id' => 'max_energy_efficiency_class', 'doc_link' => 'https://support.google.com/merchants/answer/7562785'],
            ['id' => 'age_group', 'doc_link' => 'https://support.google.com/merchants/answer/6324463'],
            ['id' => 'color', 'doc_link' => 'https://support.google.com/merchants/answer/6324487'],
            ['id' => 'gender', 'doc_link' => 'https://support.google.com/merchants/answer/6324479'],
            ['id' => 'material', 'doc_link' => 'https://support.google.com/merchants/answer/6324410'],
            ['id' => 'pattern', 'doc_link' => 'https://support.google.com/merchants/answer/6324483'],
            ['id' => 'size', 'doc_link' => 'https://support.google.com/merchants/answer/6324492'],
            ['id' => 'size_type', 'doc_link' => 'https://support.google.com/merchants/answer/6324497'],
            ['id' => 'size_system', 'doc_link' => 'https://support.google.com/merchants/answer/6324502'],
            ['id' => 'product_detail', 'doc_link' => 'https://support.google.com/merchants/answer/9218260'],
            ['id' => 'product_highlight', 'doc_link' => 'https://support.google.com/merchants/answer/9216100'],
            ['id' => 'ads_redirect', 'doc_link' => 'https://support.google.com/merchants/answer/6324450'],
            ['id' => 'custom_label_0', 'doc_link' => 'https://support.google.com/merchants/answer/6324473'],
            ['id' => 'custom_label_1', 'doc_link' => 'https://support.google.com/merchants/answer/6324473'],
            ['id' => 'custom_label_2', 'doc_link' => 'https://support.google.com/merchants/answer/6324473'],
            ['id' => 'custom_label_3', 'doc_link' => 'https://support.google.com/merchants/answer/6324473'],
            ['id' => 'custom_label_4', 'doc_link' => 'https://support.google.com/merchants/answer/6324473'],
            ['id' => 'promotion_id', 'doc_link' => 'https://support.google.com/merchants/answer/7050148'],
            ['id' => 'excluded_destination', 'doc_link' => 'https://support.google.com/merchants/answer/6324486'],
            ['id' => 'shopping_ads_excluded_country', 'doc_link' => 'https://support.google.com/merchants/answer/9837523'],
            ['id' => 'included_​​destination', 'doc_link' => 'https://support.google.com/merchants/answer/7501026'],
            ['id' => 'shipping', 'doc_link' => 'https://support.google.com/merchants/answer/6324484'],
            ['id' => 'shipping_label', 'doc_link' => 'https://support.google.com/merchants/answer/6324504'],
            ['id' => 'shipping_weight', 'doc_link' => 'https://support.google.com/merchants/answer/6324503'],
            ['id' => 'shipping_length', 'doc_link' => 'https://support.google.com/merchants/answer/6324498'],
            ['id' => 'shipping_width', 'doc_link' => 'https://support.google.com/merchants/answer/6324498'],
            ['id' => 'shipping_height', 'doc_link' => 'https://support.google.com/merchants/answer/6324498'],
            ['id' => 'transit_time_label', 'doc_link' => 'https://support.google.com/merchants/answer/9298965'],
            ['id' => 'max_handling_time', 'doc_link' => 'https://support.google.com/merchants/answer/7388496'],
            ['id' => 'min_handling_time', 'doc_link' => 'https://support.google.com/merchants/answer/7388496'],
            ['id' => 'tax', 'doc_link' => 'https://support.google.com/merchants/answer/6324454'],
            ['id' => 'tax_category', 'doc_link' => 'https://support.google.com/merchants/answer/7569847'],
            ['id' => 'quantity', 'doc_link' => 'https://support.google.com/merchants/answer/3061342?hl=en'],
            ['id' => 'store_code', 'doc_link' => 'https://support.google.com/merchants/answer/3061342?hl=en'],
            ['id' => 'pickup_method', 'doc_link' => 'https://support.google.com/merchants/answer/3061342?hl=en'],
            ['id' => 'pickup_sla', 'doc_link' => 'https://support.google.com/merchants/answer/3061342?hl=en']
        ];
    }

    public static function getErrorForm($errors, $file_error_log = false)
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/error_form.tpl');

        $tpl->assign([
            'errors' => $errors,
            'file_error_log' => $file_error_log
        ]);

        return $tpl->fetch();
    }

    public static function getSuccessForm($messages, $link = false, $file_download = false)
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/success_form.tpl');
        $tpl->assign([
            'messages' => $messages,
            'link' => $link,
            'file_for_download' => $file_download
        ]);

        return $tpl->fetch();
    }

    public static function getRelatedModulesBlock()
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/related_modules.tpl');
        $url = 'https://myprestamodules.com/modules/relatedmodules/send.php?get_related_modules=true&ajax=true&module=' . \exportproducts::ID_MODULE;

        $modules = $url ? json_decode(\Tools::file_get_contents($url), true) : false;

        $tpl->assign([
            'modules' => $modules,
        ]);

        return $tpl->fetch();
    }

    public static function getCategoriesTree($id_category_tree, $input_name, $categories)
    {
        $data = [];
        $treeCategoriesHelper = new \HelperTreeCategories($id_category_tree, Module::getInstanceByName('exportproducts')->l('LIST YOUR CATEGORIES:',__CLASS__));
        $treeCategoriesHelper->setRootCategory(\Category::getRootCategory()->id_category)
                             ->setUseCheckBox(true)
                             ->setSelectedCategories($categories)
                             ->setInputName($input_name)
                             ->setUseSearch(false);

        $data['tree'] = $treeCategoriesHelper->render();
        $data['is_ajax_request'] = Tools::getValue('ajax') && Tools::getValue('action') == 'getCategoriesTree';

        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/categories-tree.tpl');
        $tpl->assign($data);
        return $tpl->fetch();
    }

    public static function getSearchCustomers($search, $selected)
    {
        $customers = PEConfigurationFilter::getCustomerOptions(false, $search);
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/customers_list.tpl');
        $tpl->assign([
            'selected' => $selected,
            'customers' => $customers,
        ]);

        return $tpl->fetch();
    }

    public static function getConditionLine($count_conditions)
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/new_condition.tpl');
        $conditions = PEConfigurationFilter::getConditionsOptions();
        $tpl->assign([
            'conditions' => $conditions,
            'count_conditions' => $count_conditions + 1,
        ]);

        return $tpl->fetch();
    }

    private static function getCurrencies()
    {
        if (self::isCurrencyLangTableExists()) {
            $currencies = \Db::getInstance()->executeS('
                SELECT c.`id_currency`, cl.`name` FROM `' . _DB_PREFIX_ . 'currency` c
                LEFT JOIN `'._DB_PREFIX_.'currency_lang` as cl
                ON c.id_currency = cl.id_currency
                WHERE c.`deleted` = 0
                AND c.`active` = 1
                AND cl.`id_lang` = '.(int)\Context::getContext()->language->id.'
                ORDER BY c.`id_currency` ASC');
        } else {
            $currencies = \Db::getInstance()->executeS('
                SELECT c.`id_currency`, c.`name` FROM `' . _DB_PREFIX_ . 'currency` c
                WHERE c.`deleted` = 0
                AND c.`active` = 1
                ORDER BY c.`id_currency` ASC');
        }

        return $currencies;
    }

    private static function isCurrencyLangTableExists()
    {
        $currency_lang_table_info = Db::getInstance()->executeS("SELECT * FROM INFORMATION_SCHEMA.TABLES 
                                             WHERE TABLE_NAME = '"._DB_PREFIX_."currency_lang'
                                             AND TABLE_SCHEMA = '" . _DB_NAME_ . "'");

        if (empty($currency_lang_table_info)) {
            return false;
        }

        return true;
    }
}