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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PESerializationChecker.php';

class PEProductFilter
{
    private $configuration;
    private $task;
    private $id_lang;

    private $query_filters;

    private $is_tags_table_joined;
    private $is_pack_table_joined;
    private $is_product_attribute_combination_table_joined;
    private $is_attribute_table_joined;
    private $is_stock_available_table_joined;
    private $is_specific_price_table_joined;
    private $is_image_table_joined;
    private $is_feature_tables_joined;
    private $is_customization_tables_joined;
    private $is_attachment_tables_joined;
    private $is_supplier_tables_joined;
    private $is_default_supplier_tables_joined;
    private $is_category_lang_table_joined;

    public function __construct($configuration, $task = false)
    {
        $this->configuration = $configuration;
        $this->task = $task;
        $this->id_lang = $this->configuration['id_lang'];

        $this->setProductFiltersAsSqlQuery();
    }

    public function getTotalNumberOfProductsForExport()
    {
        if (!$this->configuration['separate']) {
            $query = "SELECT count(DISTINCT p.id_product) as count
                     FROM " . _DB_PREFIX_ . "product as p
                     INNER JOIN " . _DB_PREFIX_ . "product_shop as ps
                     ON p.id_product = ps.id_product
                     LEFT JOIN " . _DB_PREFIX_ . "product_lang as pl
                     ON p.id_product = pl.id_product
                     LEFT JOIN " . _DB_PREFIX_ . "product_attribute as pa
                     ON p.id_product = pa.id_product
                      " . $this->query_filters['join'] . "
                      " . $this->query_filters['exported_products'] . "
                     WHERE 1
                     " . $this->query_filters['shop'] . "
                     AND pl.id_lang = " . (int)$this->id_lang . "
                     " . $this->query_filters['where'] . "
                     " . $this->query_filters['exported'] . "
                     " . $this->query_filters['order_by'];
        } else {
            $query = "SELECT count(*) AS count FROM (SELECT DISTINCT p.id_product , pa.id_product_attribute
                     FROM " . _DB_PREFIX_ . "product AS p
                     INNER JOIN " . _DB_PREFIX_ . "product_shop AS ps
                     ON p.id_product = ps.id_product
                     LEFT JOIN " . _DB_PREFIX_ . "product_lang as pl
                     ON p.id_product = pl.id_product
                     LEFT JOIN " . _DB_PREFIX_ . "product_attribute AS pa
                     ON p.id_product = pa.id_product
                     " . $this->query_filters['join'] . "
                      " . $this->query_filters['exported_products'] . "
                     WHERE 1
                     AND pl.id_lang = " . (int)$this->id_lang . "
                     " . $this->query_filters['shop'] . "
                     " . $this->query_filters['where'] . "
                     " . $this->query_filters['exported'] . "
                    ) AS a";
        }

        $res = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $res[0]['count'];
    }

    public function getExportProductIds($iteration, $max_products_per_iteration)
    {
        if (!$iteration) {
            $limit = " LIMIT 0," . (int)$max_products_per_iteration . " ";
        } else {
            $limit = " LIMIT " . ((int)$iteration * (int)$max_products_per_iteration) . "," . (int)$max_products_per_iteration . " ";
        }

        if (!$this->configuration['separate']) {
            $query = "SELECT DISTINCT p.id_product
                     FROM " . _DB_PREFIX_ . "product as p
                     INNER JOIN " . _DB_PREFIX_ . "product_shop as ps
                     ON p.id_product = ps.id_product
                     LEFT JOIN " . _DB_PREFIX_ . "product_lang as pl
                     ON p.id_product = pl.id_product
                     LEFT JOIN " . _DB_PREFIX_ . "product_attribute as pa
                     ON p.id_product = pa.id_product
                     " . $this->query_filters['join'] . "
                      " . $this->query_filters['exported_products'] . "
                     WHERE 1
                     " . $this->query_filters['shop'] . "
                     AND pl.id_lang = " . (int)$this->id_lang . "
                     " . $this->query_filters['where'] . "
                     " . $this->query_filters['exported'] . "
                     " . $this->query_filters['order_by'] . "
                     " . $limit;

        } else {
            $query = "SELECT DISTINCT p.id_product , pa.id_product_attribute
                         FROM " . _DB_PREFIX_ . "product AS p
                         INNER JOIN " . _DB_PREFIX_ . "product_shop AS ps
                         ON p.id_product = ps.id_product
                         LEFT JOIN " . _DB_PREFIX_ . "product_lang AS pl
                         ON p.id_product = pl.id_product
                         LEFT JOIN " . _DB_PREFIX_ . "product_attribute AS pa
                         ON p.id_product = pa.id_product
                         " . $this->query_filters['join'] . "
                          " . $this->query_filters['exported_products'] . "
                         WHERE 1
                        " . $this->query_filters['shop'] . "
                         AND pl.id_lang = " . (int)$this->id_lang . "
                            " . $this->query_filters['where'] . "
                            " . $this->query_filters['exported'] . "
                            " . $this->query_filters['order_by'] . "
                            " . $limit;
        }

        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $result;
    }

    public function getExportProductIdsAsString()
    {
        $query = "SELECT GROUP_CONCAT(DISTINCT p.id_product) as ids
                 FROM " . _DB_PREFIX_ . "product as p
                 INNER JOIN " . _DB_PREFIX_ . "product_shop as ps
                 ON p.id_product = ps.id_product
                 LEFT JOIN " . _DB_PREFIX_ . "product_lang as pl
                 ON p.id_product = pl.id_product
                 LEFT JOIN " . _DB_PREFIX_ . "product_attribute as pa
                 ON p.id_product = pa.id_product
                 " . $this->query_filters['join'] . "
                  " . $this->query_filters['exported_products'] . "
                 WHERE 1
                     " . $this->query_filters['shop'] . "
                 AND pl.id_lang = " . (int)$this->id_lang . "
                 " . $this->query_filters['where'] . "
                 " . $this->query_filters['exported'] . "
                 " . $this->query_filters['order_by'];

        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (!empty($result[0]['ids'])) {
            return $result[0]['ids'];
        }

        return '';
    }

    public function getMaxNumberOfSpecificPrices()
    {
        $this->joinSpecificPriceTable();

        $query = "SELECT max(specific_price_count) AS specific_price_max_count
                FROM(
                    SELECT  count(DISTINCT sp.id_specific_price) AS specific_price_count
                    FROM " . _DB_PREFIX_ . "product AS p
                    INNER JOIN " . _DB_PREFIX_ . "product_shop AS ps
                    ON p.id_product = ps.id_product
                    LEFT JOIN " . _DB_PREFIX_ . "product_lang AS pl
                    ON p.id_product = pl.id_product
                    LEFT JOIN " . _DB_PREFIX_ . "product_attribute as pa
                    ON p.id_product = pa.id_product
                    " . $this->query_filters['join'] . "
                    " . $this->query_filters['exported_products'] . "
                    WHERE 1
                     " . $this->query_filters['shop'] . "
                    AND pl.id_lang = " . (int)$this->id_lang . "
                    " . $this->query_filters['where'] . "
                    " . $this->query_filters['exported'] . "
                    GROUP BY sp.id_product
                ) AS a";

        //Remove specific price filter from this query
        $query = $this->removeSpecificPriceFilterFromQuery($query);

        $res = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return !empty($res[0]['specific_price_max_count']) ? $res[0]['specific_price_max_count'] : 0;
    }

    private function joinSpecificPriceTable()
    {
        if ($this->is_specific_price_table_joined) {
            return false;
        }

        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "specific_price AS sp ";
        $this->query_filters['join'] .= " ON p.id_product = sp.id_product ";

        $this->is_specific_price_table_joined = true;

        return true;
    }

    private function joinCategoryLangTableByDefaultCategory()
    {
        if ($this->is_category_lang_table_joined) {
            return false;
        }

        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "category_lang AS cl ";
        $this->query_filters['join'] .= " ON ps.id_category_default = cl.id_category AND pl.id_lang = cl.id_lang";

        $this->is_category_lang_table_joined = true;

        return true;
    }

    private function removeSpecificPriceFilterFromQuery($query)
    {
        if (empty($this->configuration['filters'])) {
            return $query;
        }

        foreach ($this->configuration['filters'] as $filter) {
            if (!preg_match('/^specific_price.+$/', $filter['field'])) {
                continue;
            }

            if ($filter['field_type'] == 'date') {
                switch($filter['value']['type']) {
                    case 'today':
                    case 'yesterday':
                    case 'period':
                        $query = preg_replace("/AND sp\..+ BETWEEN \'.+\' AND DATE_ADD\(.+\)/", '', $query);
                        break;
                    case 'this_week':
                    case 'last_week':
                        $query = preg_replace("/AND YEARWEEK\(sp\..+\) = YEARWEEK\(.+\)/", '', $query);
                        break;
                    case 'last_seven_days':
                        $query = preg_replace("/AND sp\..+ BETWEEN CURRENT_TIMESTAMP - INTERVAL \'.+\' DAY AND CURRENT_TIMESTAMP/", '', $query);
                        break;
                    case 'this_month':
                        $query = preg_replace("/AND YEAR\(sp\..+\) = YEAR\(NOW\(\)\) AND MONTH\(sp\..+\) = MONTH\(NOW\(\)\)/", '', $query);
                        break;
                    case 'last_month':
                        $query = preg_replace("/AND YEAR\(sp\..+\) = YEAR\(CURRENT_DATE - INTERVAL 1 MONTH\) AND MONTH\(sp\..+\) = MONTH\(CURRENT_DATE - INTERVAL 1 MONTH\)/", '', $query);
                        break;
                    default:
                        break;
                }
            } else {
                $query = preg_replace("/AND sp\..+ (>|>=|<=|=|!=|<|IN|LIKE) (\'(.+|)\'|\(.+\))( OR sp\..+ IS( NOT)? NULL)?/", '', $query);
            }
        }

        return $query;
    }

    public function getProductIdsForCategoriesExport()
    {
        $query = "SELECT DISTINCT p.id_product
                FROM " . _DB_PREFIX_ . "product AS p
                INNER JOIN " . _DB_PREFIX_ . "product_shop AS ps
                ON p.id_product = ps.id_product
                LEFT JOIN " . _DB_PREFIX_ . "product_lang AS pl
                ON p.id_product = pl.id_product
                LEFT JOIN " . _DB_PREFIX_ . "product_attribute AS pa
                ON p.id_product = pa.id_product
                " . $this->query_filters['join'] . "
                " . $this->query_filters['exported_products'] . "
                WHERE 1
                     " . $this->query_filters['shop'] . "
                AND pl.id_lang = " . (int)$this->id_lang . "
                " . $this->query_filters['where'] . "
                " . $this->query_filters['exported'] . "
          ";

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public function getAttributeGroupIds()
    {
        $join_product_attribute_combination_table = '';
        $join_attribute_table = '';

        if (!$this->is_product_attribute_combination_table_joined) {
            $join_product_attribute_combination_table = "
                LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination AS pac
                ON pa.id_product_attribute = pac.id_product_attribute
            ";
        }

        if (!$this->is_attribute_table_joined) {
            $join_attribute_table = "
                LEFT JOIN " . _DB_PREFIX_ . "attribute AS a
                ON a.id_attribute = pac.id_attribute
            ";
        }

        $query = "
        SELECT DISTINCT a.id_attribute_group
         FROM " . _DB_PREFIX_ . "product AS p
         INNER JOIN " . _DB_PREFIX_ . "product_shop AS ps
         ON p.id_product = ps.id_product
         LEFT JOIN " . _DB_PREFIX_ . "product_lang AS pl
         ON p.id_product = pl.id_product
         LEFT JOIN " . _DB_PREFIX_ . "product_attribute AS pa
         ON p.id_product = pa.id_product
         " . $this->query_filters['join'] . "
         " . $join_product_attribute_combination_table . "
         " . $join_attribute_table . "
         " . $this->query_filters['exported_products'] . "
         WHERE 1
        " . $this->query_filters['shop'] . "
         AND pl.id_lang = " . (int)$this->id_lang . "
            " . $this->query_filters['where'] . "
            " . $this->query_filters['exported'] . "
      ";

      $this->is_product_attribute_combination_table_joined = true;
      $this->is_attribute_table_joined = true;

      return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public function getMaxNumberOfImages()
    {
        $this->joinImageTable();

        $query = "SELECT max(image_count) AS image_count
                FROM(
                    SELECT  count(DISTINCT i.id_image) AS image_count
                     FROM " . _DB_PREFIX_ . "product AS p
                     INNER JOIN " . _DB_PREFIX_ . "product_shop AS ps
                     ON p.id_product = ps.id_product
                     LEFT JOIN " . _DB_PREFIX_ . "product_lang AS pl
                     ON p.id_product = pl.id_product
                     LEFT JOIN " . _DB_PREFIX_ . "product_attribute as pa
                     ON p.id_product = pa.id_product
                      " . $this->query_filters['join'] . "
                      " . $this->query_filters['exported_products'] . "
                     WHERE 1
                     " . $this->query_filters['shop'] . "
                     AND pl.id_lang = " . (int)$this->id_lang . "
                        " . $this->query_filters['where'] . "
                        " . $this->query_filters['exported'] . "
                     GROUP BY i.id_product
                ) AS a";

        $res = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $image_count = 0;
        if (isset($res[0]['image_count']) && $res[0]['image_count']) {
            $image_count = $res[0]['image_count'];
        }

        return $image_count;
    }

    private function setProductFiltersAsSqlQuery()
    {
        $this->query_filters = [
            'shop' => '',
            'join' => '',
            'where' => '',
            'exported_products' => '',
            'exported' => '',
            'order_by' => '',
        ];

        if ($this->configuration['id_shop']) {
            $this->query_filters['shop'] = " AND ps.id_shop = '" . (int)$this->configuration['id_shop'] . "'";
        }

        if (!empty($this->task) && $this->task['export_not_exported']) {
            $this->query_filters['exported_products'] = " LEFT JOIN " . _DB_PREFIX_ . "pe_exported_product as expp
                                                         ON expp.id_product = p.id_product 
                                                         AND expp.id_task = '" . (int)$this->task['id_task'] . "' ";

            $this->query_filters['exported'] = " AND expp.id_task IS NULL ";
        }

        if ($this->configuration['sort_by'] == 'quantity' && !$this->is_stock_available_table_joined) {
            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "stock_available as sa
                                                ON p.id_product = sa.id_product AND sa.id_product_attribute = 0 ";

            $this->is_stock_available_table_joined = true;
        }

        if ($this->configuration['sort_by'] == 'default_category_name') {
            $this->joinCategoryLangTableByDefaultCategory();
        }

        if (!$this->configuration['separate']) {
            $order_way = $this->configuration['order_way'] == 'asc' ? ' ASC' : ' DESC';

            $order_by_queries = [
                'id_product'  => ' ORDER BY p.id_product' . $order_way,
                'name'        => ' ORDER BY pl.name ' . $order_way . ', p.id_product ' . $order_way ,
                'default_category_name'  => ' ORDER BY cl.name ' . $order_way . ', p.id_product ' . $order_way ,
                'price'       => ' ORDER BY p.price ' . $order_way . ', p.id_product ' . $order_way ,
                'quantity'    => ' ORDER BY sa.quantity ' . $order_way . ', p.id_product ' . $order_way ,
                'date_add'    => ' ORDER BY p.date_add ' . $order_way . ', p.id_product ' . $order_way ,
                'date_update' => ' ORDER BY p.date_upd ' . $order_way . ', p.id_product ' . $order_way ,
            ];

            $this->query_filters['order_by'] = $order_by_queries[$this->configuration['sort_by']];

        } else {
            $order_way = $this->configuration['order_way'] == 'asc' ? ' ASC' : ' DESC';

            $order_by_queries = [
                'id_product'  => ' ORDER BY p.id_product' . $order_way . ', p.id_product ' . $order_way . ', pa.id_product_attribute ' . $order_way ,
                'name'        => ' ORDER BY pl.name ' . $order_way . ', p.id_product ' . $order_way . ', pa.id_product_attribute ' . $order_way ,
                'default_category_name'   => ' ORDER BY cl.name ' . $order_way . ', p.id_product ' . $order_way . ', pa.id_product_attribute ' . $order_way ,
                'price'       => ' ORDER BY p.price ' . $order_way . ', pa.price ' . $order_way . ', p.id_product ' . $order_way . ', pa.id_product_attribute ' . $order_way ,
                'quantity'    => ' ORDER BY sa.quantity ' . $order_way . ', p.id_product ' . $order_way . ', pa.id_product_attribute ' . $order_way ,
                'date_add'    => ' ORDER BY p.date_add ' . $order_way . ', p.id_product ' . $order_way . ', pa.id_product_attribute ' . $order_way ,
                'date_update' => ' ORDER BY p.date_upd ' . $order_way . ', p.id_product ' . $order_way . ', pa.id_product_attribute ' . $order_way
            ];

            $this->query_filters['order_by'] = $order_by_queries[$this->configuration['sort_by']];
        }

        $saved_filters = $this->configuration['filters'];

        if (!empty($saved_filters)) {
            foreach ($saved_filters as $saved_filter) {
                $filter_components = isset($saved_filter['value']) ? $saved_filter['value'] : [];

                if (is_string($saved_filter['value'])) {
                    if (PESerializationChecker::isStringSerialized($saved_filter['value'])) {
                        $filter_components = Tools::unSerialize($saved_filter['value']);
                    } else {
                        $filter_components = json_decode($saved_filter['value'], true);
                    }
                }

                if (is_array($filter_components) && (isset($filter_components['value']) || isset($filter_components['type'])) && $saved_filter['field_type'] != 'date') {
                    if ($filter_components['value'] == '' && !in_array($filter_components['type'], ['empty', 'not_empty'])) {
                        continue;
                    }
                }

                if (empty($filter_components) && $filter_components != 0) {
                    continue;
                }

                if ($saved_filter['field'] == 'product_id') {
                    $this->addNumberTypeFilterToQuery('ps.id_product', $filter_components);
                } elseif ($saved_filter['field'] == 'has_combinations') {
                    $filter_value = $filter_components ? ' IS NOT NULL ' : ' IS NULL ';
                    $this->query_filters['where'] .= " AND pa.`id_product_attribute` " . $filter_value;
                } elseif ($saved_filter['field'] == 'product_name_clean') {
                    $this->addStringTypeFilterToQuery('pl.name', $filter_components);
                } elseif ($saved_filter['field'] == 'reference') {
                    $this->addStringTypeFilterToQuery('p.reference', $filter_components);
                } elseif ($saved_filter['field'] == 'active') {
                    $this->addSelectTypeFilterToQuery('ps.active', $filter_components);
                } elseif ($saved_filter['field'] == 'description_short') {
                    $this->addStringTypeFilterToQuery('pl.description_short', $filter_components);
                } elseif ($saved_filter['field'] == 'description') {
                    $this->addStringTypeFilterToQuery('pl.description', $filter_components);
                } elseif ($saved_filter['field'] == 'tags') {
                    $this->addTagsFilterToQuery($filter_components);
                } elseif ($saved_filter['field'] == 'ean13') {
                    $this->addStringTypeFilterToQuery('p.ean13', $filter_components);
                } elseif ($saved_filter['field'] == 'isbn') {
                    $this->addStringTypeFilterToQuery('p.isbn', $filter_components);
                } elseif ($saved_filter['field'] == 'condition') {
                    $this->addStringTypeFilterToQuery('ps.condition', $filter_components);
                } elseif ($saved_filter['field'] == 'available_for_order') {
                    $this->addSelectTypeFilterToQuery('ps.available_for_order', $filter_components);
                } elseif ($saved_filter['field'] == 'online_only') {
                    $this->addSelectTypeFilterToQuery('ps.online_only', $filter_components);
                } elseif ($saved_filter['field'] == 'is_virtual') {
                    $this->addSelectTypeFilterToQuery('p.is_virtual', $filter_components);
                } elseif ($saved_filter['field'] == 'cache_is_pack') {
                    $this->addSelectTypeFilterToQuery('p.cache_is_pack', $filter_components);
                } elseif ($saved_filter['field'] == 'visibility') {
                    $this->addCheckboxTypeFilterToQuery('ps.visibility', $filter_components);
                } elseif ($saved_filter['field'] == 'id_shop_default') {
                    $this->addNumberTypeFilterToQuery('p.id_shop_default', $filter_components);
                } elseif ($saved_filter['field'] == 'quantity_discount') {
                    $this->addSelectTypeFilterToQuery('p.quantity_discount', $filter_components);
                } elseif ($saved_filter['field'] == 'redirect_type') {
                    $this->addStringTypeFilterToQuery('ps.redirect_type', $filter_components);
                } elseif ($saved_filter['field'] == 'indexed') {
                    $this->addSelectTypeFilterToQuery('ps.indexed', $filter_components);
                } elseif ($saved_filter['field'] == 'manufacturers') {
                    $this->addCheckboxTypeFilterToQuery('p.id_manufacturer', $filter_components);
                } elseif ($saved_filter['field'] == 'categories') {
                    $this->addProductCategoryFilterToQuery($filter_components);
                } elseif ($saved_filter['field'] == 'is_new') {
                    $this->addIsNewFilterToQuery($filter_components);
                } elseif ($saved_filter['field'] == 'date_add') {
                    $this->addDateTypeFilterToQuery('ps.date_add', $filter_components);
                } elseif ($saved_filter['field'] == 'date_update') {
                    $this->addDateTypeFilterToQuery('ps.date_upd', $filter_components);
                } elseif ($saved_filter['field'] == 'show_price') {
                    $this->addSelectTypeFilterToQuery('ps.show_price', $filter_components);
                } elseif ($saved_filter['field'] == 'wholesale_price') {
                    $this->addNumberTypeFilterToQuery('ps.wholesale_price', $filter_components, true);
                } elseif ($saved_filter['field'] == 'price') {
                    $this->addNumberTypeFilterToQuery('ps.price', $filter_components, true);
                } elseif ($saved_filter['field'] == 'id_tax_rules_group') {
                    $this->addNumberTypeFilterToQuery('ps.id_tax_rules_group', $filter_components);
                } elseif ($saved_filter['field'] == 'unit_price_ratio') {
                    $this->addNumberTypeFilterToQuery('ps.unit_price_ratio', $filter_components, true);
                } elseif ($saved_filter['field'] == 'ecotax') {
                    $this->addNumberTypeFilterToQuery('ps.ecotax', $filter_components, true);
                } elseif ($saved_filter['field'] == 'on_sale') {
                    $this->addSelectTypeFilterToQuery('ps.on_sale', $filter_components);
                } elseif ($saved_filter['field'] == 'link_rewrite') {
                    $this->addStringTypeFilterToQuery('pl.link_rewrite', $filter_components);
                } elseif ($saved_filter['field'] == 'link_rewrite') {
                    $this->addStringTypeFilterToQuery('pl.link_rewrite', $filter_components);
                } elseif ($saved_filter['field'] == 'meta_title') {
                    $this->addStringTypeFilterToQuery('pl.meta_title', $filter_components);
                } elseif ($saved_filter['field'] == 'meta_description') {
                    $this->addStringTypeFilterToQuery('pl.meta_description', $filter_components);
                } elseif ($saved_filter['field'] == 'meta_keywords') {
                    $this->addStringTypeFilterToQuery('pl.meta_keywords', $filter_components);
                } elseif ($saved_filter['field'] == 'id_category_default') {
                    $this->addNumberTypeFilterToQuery('ps.id_category_default', $filter_components);
                } elseif ($saved_filter['field'] == 'default_category_name') {
                    $this->joinCategoryLangTableByDefaultCategory();
                    $this->addStringTypeFilterToQuery('cl.name', $filter_components);
                } elseif ($saved_filter['field'] == 'id_manufacturer') {
                    $this->addNumberTypeFilterToQuery('p.id_manufacturer', $filter_components);
                } elseif ($saved_filter['field'] == 'manufacturer_name') {
                    $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "manufacturer AS manufacturer ";
                    $this->query_filters['join'] .= " ON p.id_manufacturer = manufacturer.id_manufacturer";
                    $this->addStringTypeFilterToQuery('manufacturer.name', $filter_components);
                } elseif ($saved_filter['field'] == 'width') {
                    $this->addNumberTypeFilterToQuery('p.width', $filter_components, true);
                } elseif ($saved_filter['field'] == 'height') {
                    $this->addNumberTypeFilterToQuery('p.height', $filter_components, true);
                } elseif ($saved_filter['field'] == 'depth') {
                    $this->addNumberTypeFilterToQuery('p.depth', $filter_components, true);
                } elseif ($saved_filter['field'] == 'additional_shipping_cost') {
                    $this->addNumberTypeFilterToQuery('ps.additional_shipping_cost', $filter_components, true);
                } elseif ($saved_filter['field'] == 'additional_delivery_times') {
                    $this->addCheckboxTypeFilterToQuery('p.additional_delivery_times', $filter_components);
                } elseif ($saved_filter['field'] == 'delivery_in_stock') {
                    $this->addStringTypeFilterToQuery('pl.delivery_in_stock', $filter_components);
                } elseif ($saved_filter['field'] == 'delivery_out_stock') {
                    $this->addStringTypeFilterToQuery('pl.delivery_out_stock', $filter_components);
                } elseif ($saved_filter['field'] == 'available_now') {
                    $this->addStringTypeFilterToQuery('pl.available_now', $filter_components);
                } elseif ($saved_filter['field'] == 'available_later') {
                    $this->addStringTypeFilterToQuery('pl.available_later', $filter_components);
                } elseif ($saved_filter['field'] == 'advanced_stock_management') {
                    $this->addSelectTypeFilterToQuery('ps.advanced_stock_management', $filter_components);
                } elseif ($saved_filter['field'] == 'image_caption') {
                    $this->joinImageTable();
                    $this->addStringTypeFilterToQuery('il.legend', $filter_components);
                } elseif ($saved_filter['field'] == 'pack_stock_type') {
                    $this->addCheckboxTypeFilterToQuery('ps.pack_stock_type', $filter_components);
                } elseif ($saved_filter['field'] == 'customizable') {
                    $this->addSelectTypeFilterToQuery('ps.customizable', $filter_components);
                } elseif ($saved_filter['field'] == 'uploadable_files') {
                    $this->addSelectTypeFilterToQuery('ps.uploadable_files', $filter_components);
                } elseif ($saved_filter['field'] == 'text_fields') {
                    $this->addSelectTypeFilterToQuery('ps.text_fields', $filter_components);
                } elseif ($saved_filter['field'] == 'cache_has_attachments') {
                    $this->addSelectTypeFilterToQuery('p.cache_has_attachments', $filter_components);
                } elseif ($saved_filter['field'] == 'quantity') {
                    if (!$this->is_stock_available_table_joined) {
                        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "stock_available as sa
                                                          ON p.id_product = sa.id_product AND sa.id_product_attribute = 0 ";

                        $this->is_stock_available_table_joined = true;
                    }

                    $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "stock_available AS sa_combination ";
                    $this->query_filters['join'] .= " ON p.id_product = sa_combination.id_product AND IF(pa.id_product_attribute IS NULL, sa_combination.id_product_attribute = 0, sa_combination.id_product_attribute = pa.id_product_attribute) ";
                    $this->addNumberTypeFilterToQuery('sa_combination.quantity', $filter_components);
                } elseif ($saved_filter['field'] == 'total_quantity') {
                    if (!$this->is_stock_available_table_joined) {
                        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "stock_available as sa
                                                          ON p.id_product = sa.id_product AND sa.id_product_attribute = 0 ";

                        $this->is_stock_available_table_joined = true;
                    }

                    $this->addNumberTypeFilterToQuery('sa.quantity', $filter_components);
                } elseif ($saved_filter['field'] == 'combination_reference') {
                    $this->addStringTypeFilterToQuery('pa.reference', $filter_components);
                } elseif ($saved_filter['field'] == 'combination_price_impact') {
                    $this->addNumberTypeFilterToQuery('pa.price', $filter_components, true);
                } elseif ($saved_filter['field'] == 'combination_unit_price_impact') {
                    $this->addNumberTypeFilterToQuery('pa.unit_price_impact', $filter_components, true);
                } elseif ($saved_filter['field'] == 'combination_wholesale_price') {
                    $this->addNumberTypeFilterToQuery('pa.wholesale_price', $filter_components, true);
                } elseif ($saved_filter['field'] == 'combination_ean') {
                    $this->addStringTypeFilterToQuery('pa.ean13', $filter_components);
                } elseif ($saved_filter['field'] == 'combination_upc') {
                    $this->addStringTypeFilterToQuery('pa.upc', $filter_components);
                } elseif ($saved_filter['field'] == 'combination_isbn') {
                    $this->addStringTypeFilterToQuery('pa.isbn', $filter_components);
                } elseif ($saved_filter['field'] == 'combination_ecotax') {
                    $this->addNumberTypeFilterToQuery('pa.ecotax', $filter_components, true);
                } elseif ($saved_filter['field'] == 'combination_isbn') {
                    $this->addStringTypeFilterToQuery('pa.isbn', $filter_components);
                } elseif ($saved_filter['field'] == 'combination_weight_impact') {
                    $this->addNumberTypeFilterToQuery('pa.weight', $filter_components, true);
                } elseif ($saved_filter['field'] == 'combination_location') {
                    $this->addStringTypeFilterToQuery('pa.location', $filter_components);
                } elseif ($saved_filter['field'] == 'pack_items_id') {
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(pack2.id_product_pack) FROM " . _DB_PREFIX_ . "pack as pack2
                            WHERE pack2.id_product_item IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        if (!$this->is_pack_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "pack as pack ";
                            $this->query_filters['join'] .= " ON p.id_product = pack.id_product_pack";

                            $this->is_pack_table_joined = true;
                        }

                        $this->addNumberTypeFilterToQuery('pack.id_product_item', $filter_components);
                    }
                } elseif ($saved_filter['field'] == 'pack_items_name') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE pack_pl.name IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE pack_pl.name LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(pack2.id_product_pack) FROM " . _DB_PREFIX_ . "pack as pack2
                            LEFT JOIN "._DB_PREFIX_."product_lang as pack_pl
                            ON pack2.id_product_item = pack_pl.id_product AND pack_pl.id_lang = '".(int)$this->id_lang."'
                            " . $inner_where . "
                        )";
                    } else {
                        if (!$this->is_pack_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "pack as pack ";
                            $this->query_filters['join'] .= " ON p.id_product = pack.id_product_pack";

                            $this->is_pack_table_joined = true;
                        }

                        $this->query_filters['join'] .= " LEFT JOIN " ._DB_PREFIX_. "product_lang as pack_pl ";
                        $this->query_filters['join'] .= " ON pack.id_product_item = pack_pl.id_product AND pack_pl.id_lang = '".(int)$this->id_lang."'";

                        $this->is_pack_table_joined = true;

                        $this->addStringTypeFilterToQuery('pack_pl.name', $filter_components);
                    }
                } elseif ($saved_filter['field'] == 'pack_items_id_pack_product_attribute') {
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(pack2.id_product_pack) FROM " . _DB_PREFIX_ . "pack as pack2
                            WHERE pack2.id_product_attribute_item IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        if (!$this->is_pack_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "pack as pack ";
                            $this->query_filters['join'] .= " ON p.id_product = pack.id_product_pack";

                            $this->is_pack_table_joined = true;
                        }

                        $this->addNumberTypeFilterToQuery('pack.id_product_attribute_item', $filter_components);
                    }
                } elseif ($saved_filter['field'] == 'pack_items_reference') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE pack_p.reference IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE pack_p.reference LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(pack2.id_product_pack) FROM " . _DB_PREFIX_ . "pack as pack2
                            LEFT JOIN "._DB_PREFIX_."product as pack_p
                            ON pack2.id_product_item = pack_p.id_product
                            " . $inner_where . "
                        )";
                    } else {
                        if (!$this->is_pack_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "pack as pack ";
                            $this->query_filters['join'] .= " ON p.id_product = pack.id_product_pack";

                            $this->is_pack_table_joined = true;
                        }

                        $this->query_filters['join'] .= " LEFT JOIN " ._DB_PREFIX_. "product as pack_p ";
                        $this->query_filters['join'] .= " ON pack.id_product_item = pack_p.id_product";

                        $this->is_pack_table_joined = true;

                        $this->addStringTypeFilterToQuery('pack_p.reference', $filter_components);
                    }
                } elseif ($saved_filter['field'] == 'pack_items_ean13') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE pack_p.ean13 IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE pack_p.ean13 LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(pack2.id_product_pack) FROM " . _DB_PREFIX_ . "pack as pack2
                            LEFT JOIN "._DB_PREFIX_."product as pack_p
                            ON pack2.id_product_item = pack_p.id_product
                            " . $inner_where . "
                        )";
                    } else {
                        if (!$this->is_pack_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "pack as pack ";
                            $this->query_filters['join'] .= " ON p.id_product = pack.id_product_pack";

                            $this->is_pack_table_joined = true;
                        }

                        $this->query_filters['join'] .= " LEFT JOIN " ._DB_PREFIX_. "product as pack_p ";
                        $this->query_filters['join'] .= " ON pack.id_product_item = pack_p.id_product";

                        $this->is_pack_table_joined = true;

                        $this->addStringTypeFilterToQuery('pack_p.ean13', $filter_components);
                    }
                } elseif ($saved_filter['field'] == 'pack_items_upc') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE pack_p.upc IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE pack_p.upc LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(pack2.id_product_pack) FROM " . _DB_PREFIX_ . "pack as pack2
                            LEFT JOIN "._DB_PREFIX_."product as pack_p
                            ON pack2.id_product_item = pack_p.id_product
                            " . $inner_where . "
                        )";
                    } else {
                        if (!$this->is_pack_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "pack as pack ";
                            $this->query_filters['join'] .= " ON p.id_product = pack.id_product_pack";

                            $this->is_pack_table_joined = true;
                        }

                        $this->query_filters['join'] .= " LEFT JOIN " ._DB_PREFIX_. "product as pack_p ";
                        $this->query_filters['join'] .= " ON pack.id_product_item = pack_p.id_product";

                        $this->is_pack_table_joined = true;

                        $this->addStringTypeFilterToQuery('pack_p.upc', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'specific_price') {
                    $this->joinSpecificPriceTable();

                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(sp2.id_product) FROM " . _DB_PREFIX_ . "specific_price as sp2
                            WHERE sp2.price IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        $this->addNumberTypeFilterToQuery('sp.price', $filter_components, true);
                    }
                } else if ($saved_filter['field'] == 'specific_price_reduction') {
                    $this->joinSpecificPriceTable();

                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(sp2.id_product) FROM " . _DB_PREFIX_ . "specific_price as sp2
                            WHERE sp2.reduction IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        $this->addNumberTypeFilterToQuery('sp.reduction', $filter_components, true);
                    }
                } else if ($saved_filter['field'] == 'specific_price_reduction_type') {
                    $this->joinSpecificPriceTable();

                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE sp2.reduction_type IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE sp2.reduction_type LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(sp2.id_product) FROM " . _DB_PREFIX_ . "specific_price as sp2
                            ".$inner_where."
                        )";
                    } else {
                        $this->addStringTypeFilterToQuery('sp.reduction_type', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'specific_price_from') {
                    $this->joinSpecificPriceTable();
                    $this->addDateTypeFilterToQuery('sp.from', $filter_components);
                } else if ($saved_filter['field'] == 'specific_price_to') {
                    $this->joinSpecificPriceTable();
                    $this->addDateTypeFilterToQuery('sp.to', $filter_components);
                } else if ($saved_filter['field'] == 'specific_price_from_quantity') {
                    $this->joinSpecificPriceTable();
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(sp2.id_product) FROM " . _DB_PREFIX_ . "specific_price as sp2
                            WHERE sp2.from_quantity IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        $this->addNumberTypeFilterToQuery('sp.from_quantity', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'specific_price_id_group') {
                    $this->joinSpecificPriceTable();
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(sp2.id_product) FROM " . _DB_PREFIX_ . "specific_price as sp2
                            WHERE sp2.id_group IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        $this->addNumberTypeFilterToQuery('sp.id_group', $filter_components);
                    }
                }
                else if ($saved_filter['field'] == 'specific_price_id_customer') {
                  $this->joinSpecificPriceTable();
                  $negative_clauses = ['!=', 'not_in'];

                  if (in_array($filter_components['type'], $negative_clauses)) {
                    $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(sp2.id_product) FROM " . _DB_PREFIX_ . "specific_price as sp2
                            WHERE sp2.id_customer IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                  } else {
                    $this->addNumberTypeFilterToQuery('sp.id_customer', $filter_components);
                  }
                }
                else if ($saved_filter['field'] == 'id_product_accessories') {
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(accessory2.id_product_1) FROM " . _DB_PREFIX_ . "accessory as accessory2
                            WHERE accessory2.id_product_2 IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        if (!$this->is_pack_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "accessory as accessory ";
                            $this->query_filters['join'] .= " ON p.id_product = accessory.id_product_1";

                            $this->is_pack_table_joined = true;
                        }

                        $this->addNumberTypeFilterToQuery('accessory.id_product_2', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'id_carriers') {
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(carrier2.id_product) FROM " . _DB_PREFIX_ . "product_carrier as carrier2
                            WHERE carrier2.id_carrier_reference IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        if (!$this->is_pack_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "product_carrier as carrier ";
                            $this->query_filters['join'] .= " ON p.id_product = carrier.id_product";

                            $this->is_pack_table_joined = true;
                        }

                        $this->addNumberTypeFilterToQuery('carrier.id_carrier_reference', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'attribute_group') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE agl2.name IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE agl2.name LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(pa2.id_product) 
                            FROM "._DB_PREFIX_."product_attribute as pa2
                            LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac2
                            ON pa2.id_product_attribute = pac2.id_product_attribute
                            LEFT JOIN " . _DB_PREFIX_ . "attribute as a2
                            ON pac2.id_attribute = a2.id_attribute
                            LEFT JOIN " . _DB_PREFIX_ . "attribute_group_lang as agl2
                            ON (a2.id_attribute_group = agl2.id_attribute_group AND agl2.id_lang = '".(int)$this->id_lang."')
                            " . $inner_where . "
                        )";
                    } else {
                        if (!$this->is_product_attribute_combination_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ";
                            $this->query_filters['join'] .= " ON pa.id_product_attribute = pac.id_product_attribute";

                            $this->is_product_attribute_combination_table_joined = true;
                        }

                        if (!$this->is_attribute_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "attribute as a ";
                            $this->query_filters['join'] .= " ON pac.id_attribute = a.id_attribute ";

                            $this->is_attribute_table_joined = true;
                        }

                        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "attribute_group_lang as agl ";
                        $this->query_filters['join'] .= " ON (a.id_attribute_group = agl.id_attribute_group AND agl.id_lang = '".(int)$this->id_lang."')";

                        $this->addStringTypeFilterToQuery('agl.name', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'attribute') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE al2.name IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE al2.name LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(pa2.id_product) 
                            FROM "._DB_PREFIX_."product_attribute as pa2
                            LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac2
                            ON pa2.id_product_attribute = pac2.id_product_attribute
                            LEFT JOIN " . _DB_PREFIX_ . "attribute_lang as al2
                            ON (pac2.id_attribute = al2.id_attribute AND al2.id_lang = '".($this->id_lang)."')
                            " . $inner_where . "
                        )";
                    } else {
                        if (!$this->is_product_attribute_combination_table_joined) {
                            $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination as pac ";
                            $this->query_filters['join'] .= " ON pa.id_product_attribute = pac.id_product_attribute";

                            $this->is_product_attribute_combination_table_joined = true;
                        }

                        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "attribute_lang as al ";
                        $this->query_filters['join'] .= " ON (pac.id_attribute = al.id_attribute AND al.id_lang = '".($this->id_lang)."')";

                        $this->addStringTypeFilterToQuery('al.name', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'feature') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE fl2.name IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE fl2.name LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(fp2.id_product) FROM " . _DB_PREFIX_ . "feature_product as fp2
                            LEFT JOIN " . _DB_PREFIX_ . "feature_lang as fl2  
                            ON fp2.id_feature = fl2.id_feature AND fl2.id_lang = '".($this->id_lang)."'  
                            LEFT JOIN " . _DB_PREFIX_ . "feature_value_lang as fvl2
                            ON fp2.id_feature_value = fvl2.id_feature_value AND fvl2.id_lang = '".($this->id_lang)."'
                            " . $inner_where . ")";
                    } else {
                        $this->joinFeatureTables();
                        $this->addStringTypeFilterToQuery('fl.name', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'feature_value') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE fvl2.value IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE fvl2.value LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(fp2.id_product) FROM " . _DB_PREFIX_ . "feature_product as fp2
                            LEFT JOIN " . _DB_PREFIX_ . "feature_lang as fl2  
                            ON fp2.id_feature = fl2.id_feature AND fl2.id_lang = '".($this->id_lang)."'  
                            LEFT JOIN " . _DB_PREFIX_ . "feature_value_lang as fvl2
                            ON fp2.id_feature_value = fvl2.id_feature_value AND fvl2.id_lang = '".($this->id_lang)."'
                            " . $inner_where . ")";
                    } else {
                        $this->joinFeatureTables();
                        $this->addStringTypeFilterToQuery('fvl.value', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'customization_field_type') {
                    $this->joinCustomizationTables();
                    $this->addSelectTypeFilterToQuery('cf.type', $filter_components);
                } else if ($saved_filter['field'] == 'customization_field_label') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE cfl2.name IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE cfl2.name LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(cf2.id_product) FROM " . _DB_PREFIX_ . "customization_field as cf2
                            LEFT JOIN " . _DB_PREFIX_ . "customization_field_lang as cfl2  
                            ON cf2.id_customization_field = cfl2.id_customization_field 
                            AND cfl2.id_lang = '".(int)$this->id_lang."'
                            AND cfl2.id_shop = '".(int)$this->getIdShop()."'
                            " . $inner_where . ")";
                    } else {
                        $this->joinCustomizationTables();
                        $this->addStringTypeFilterToQuery('cfl.name', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'customization_field_required') {
                    $this->joinCustomizationTables();
                    $this->addSelectTypeFilterToQuery('cf.required', $filter_components);
                } else if ($saved_filter['field'] == 'id_attachments') {
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(product_attachment2.id_product) FROM " . _DB_PREFIX_ . "product_attachment as product_attachment2
                            LEFT JOIN " . _DB_PREFIX_ . "attachment as attachment2
                            ON product_attachment2.id_attachment = attachment2.id_attachment
                            LEFT JOIN " . _DB_PREFIX_ . "attachment_lang as attachment_lang2
                            ON attachment2.id_attachment = attachment_lang2.id_attachment AND attachment_lang2.id_lang = '" . (int)$this->id_lang . "'
                            WHERE attachment2.id_attachment IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        $this->joinAttachmentTables();
                        $this->addNumberTypeFilterToQuery('attachment.id_attachment', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'attachments_name') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE attachment_lang2.name IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE attachment_lang2.name LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(product_attachment2.id_product) FROM " . _DB_PREFIX_ . "product_attachment as product_attachment2
                            LEFT JOIN " . _DB_PREFIX_ . "attachment as attachment2
                            ON product_attachment2.id_attachment = attachment2.id_attachment
                            LEFT JOIN " . _DB_PREFIX_ . "attachment_lang as attachment_lang2
                            ON attachment2.id_attachment = attachment_lang2.id_attachment AND attachment_lang2.id_lang = '" . (int)$this->id_lang . "'
                            ".$inner_where.")";
                    } else {
                        $this->joinAttachmentTables();
                        $this->addStringTypeFilterToQuery('attachment_lang.name', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'attachments_description') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE attachment_lang2.description IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE attachment_lang2.description LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(product_attachment2.id_product) FROM " . _DB_PREFIX_ . "product_attachment as product_attachment2
                            LEFT JOIN " . _DB_PREFIX_ . "attachment as attachment2
                            ON product_attachment2.id_attachment = attachment2.id_attachment
                            LEFT JOIN " . _DB_PREFIX_ . "attachment_lang as attachment_lang2
                            ON attachment2.id_attachment = attachment_lang2.id_attachment AND attachment_lang2.id_lang = '" . (int)$this->id_lang . "'
                            ".$inner_where.")";
                    } else {
                        $this->joinAttachmentTables();
                        $this->addStringTypeFilterToQuery('attachment_lang.description', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'attachments_file') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE attachment2.file_name IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE attachment2.file_name LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(product_attachment2.id_product) FROM " . _DB_PREFIX_ . "product_attachment as product_attachment2
                            LEFT JOIN " . _DB_PREFIX_ . "attachment as attachment2
                            ON product_attachment2.id_attachment = attachment2.id_attachment
                            LEFT JOIN " . _DB_PREFIX_ . "attachment_lang as attachment_lang2
                            ON attachment2.id_attachment = attachment_lang2.id_attachment AND attachment_lang2.id_lang = '" . (int)$this->id_lang . "'
                            ".$inner_where.")";
                    } else {
                        $this->joinAttachmentTables();
                        $this->addStringTypeFilterToQuery('attachment.file_name', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'supplier_id') {
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(product_supplier2.id_product) FROM " . _DB_PREFIX_ . "product_supplier as product_supplier2
                            LEFT JOIN " . _DB_PREFIX_ . "supplier as supplier2
                            ON product_supplier2.id_supplier = supplier2.id_supplier
                            WHERE product_supplier2.id_supplier IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        $this->joinSupplierTables();
                        $this->addNumberTypeFilterToQuery('product_supplier.id_supplier', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'supplier_name') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE supplier2.name IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE supplier2.name LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(product_supplier2.id_product) FROM " . _DB_PREFIX_ . "product_supplier as product_supplier2
                            LEFT JOIN " . _DB_PREFIX_ . "supplier as supplier2
                            ON product_supplier2.id_supplier = supplier2.id_supplier
                            ".$inner_where."
                        )";
                    } else {
                        $this->joinSupplierTables();
                        $this->addStringTypeFilterToQuery('supplier.name', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'supplier_reference') {
                    $negative_clauses = ['is_not', 'not_list', 'not_contain'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $inner_where = "WHERE product_supplier2.product_supplier_reference IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

                        if ($filter_components['type'] == 'not_contain') {
                            $inner_where = "WHERE product_supplier2.product_supplier_reference LIKE '%".pSQL($filter_components['value'])."%'";
                        }

                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(product_supplier2.id_product) FROM " . _DB_PREFIX_ . "product_supplier as product_supplier2
                            ".$inner_where."
                        )";
                    } else {
                        $this->joinSupplierTables();
                        $this->addStringTypeFilterToQuery('product_supplier.product_supplier_reference', $filter_components);
                    }
                } else if ($saved_filter['field'] == 'supplier_price') {
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(product_supplier2.id_product) FROM " . _DB_PREFIX_ . "product_supplier as product_supplier2
                            WHERE product_supplier2.product_supplier_price_te IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        $this->joinSupplierTables();
                        $this->addNumberTypeFilterToQuery('product_supplier.product_supplier_price_te', $filter_components, true);
                    }
                } else if ($saved_filter['field'] == 'supplier_currency') {
                    $negative_clauses = ['!=', 'not_in'];

                    if (in_array($filter_components['type'], $negative_clauses)) {
                        $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(product_supplier2.id_product) FROM " . _DB_PREFIX_ . "product_supplier as product_supplier2
                            WHERE product_supplier2.id_currency IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")
                        )";
                    } else {
                        $this->joinSupplierTables();
                        $this->addNumberTypeFilterToQuery('product_supplier.id_currency', $filter_components, true);
                    }
                } elseif ($saved_filter['field'] == 'default_supplier_id') {
                    $this->addNumberTypeFilterToQuery('p.id_supplier', $filter_components);
                } elseif ($saved_filter['field'] == 'default_supplier_name') {
                    $this->joinDefaultSupplierTables();
                    $this->addStringTypeFilterToQuery('default_supplier.name', $filter_components);
                } elseif ($saved_filter['field'] == 'default_supplier_reference') {
                    $this->joinDefaultSupplierTables();
                    $this->addStringTypeFilterToQuery('default_product_supplier.product_supplier_reference', $filter_components);
                } elseif ($saved_filter['field'] == 'default_supplier_price') {
                    $this->joinDefaultSupplierTables();
                    $this->addStringTypeFilterToQuery('default_product_supplier.product_supplier_price_te', $filter_components);
                } elseif ($saved_filter['field'] == 'default_supplier_currency') {
                    $this->joinDefaultSupplierTables();
                    $this->addNumberTypeFilterToQuery('default_product_supplier.id_currency', $filter_components);
                }
            }
        }

        return true;
    }

    private function addProductCategoryFilterToQuery($filter_components)
    {
        $num_of_categories_in_filter = count($filter_components);

        if (!$num_of_categories_in_filter) {
            return false;
        }

        $this->query_filters['where'] .= " AND p.id_product IN (
                                SELECT cp2.id_product FROM "._DB_PREFIX_."category_product as cp2
                                WHERE cp2.id_category IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components).")
                            )";

        return true;
    }

    private function addTagsFilterToQuery($filter_components)
    {
        $negative_clauses = ['is_not', 'not_list', 'not_contain'];

        if (in_array($filter_components['type'], $negative_clauses)) {
            $inner_where = "WHERE t.name IN (".$this->prepareValuesForInAndNotInQueryConditions($filter_components['value']).")";

            if ($filter_components['type'] == 'not_contain') {
                $inner_where = "WHERE t.name LIKE '%".pSQL($filter_components['value'])."%'";
            }

            $this->query_filters['where'] .= " AND p.id_product NOT IN (
                            SELECT DISTINCT(pt.id_product) FROM " . _DB_PREFIX_ . "product_tag as pt
                            LEFT JOIN " . _DB_PREFIX_ . "tag as t
                            ON pt.id_tag = t.id_tag
                            ".$inner_where."
                        )";
        } else {
            if (!$this->is_tags_table_joined) {
                $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "product_tag as pt ";
                $this->query_filters['join'] .= " ON p.id_product = pt.id_product";
                $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "tag as t ";
                $this->query_filters['join'] .= " ON pt.id_tag = t.id_tag ";

                $this->is_tags_table_joined = true;
            }

            $this->addStringTypeFilterToQuery('t.name', $filter_components);
        }
    }

    private function addNumberTypeFilterToQuery($field_name_in_db, $filter_components, $is_float = false)
    {
        $filter_value = $is_float ? (float)$filter_components['value'] : (int)$filter_components['value'];

        switch ($filter_components['type']) {
            case '>':
            case '<':
            case '>=':
            case '<=':
            case '=':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " " . pSQL($filter_components['type'], true) . " '" . $filter_value . "'";
                break;
            case '!=':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " != '" . $filter_value . "' ";
                break;
            case 'in':
                $allowed_values = $this->prepareValuesForInAndNotInQueryConditions($filter_components['value']);
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " IN (" . $allowed_values . ")";
                break;
            case 'not_in':
                $not_allowed_values = $this->prepareValuesForInAndNotInQueryConditions($filter_components['value']);
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " NOT IN (" . $not_allowed_values . ")";
                break;
            case 'empty':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " = '' OR "  . pSQL($field_name_in_db) .  " IS NULL";
                break;
            case 'not_empty':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " != '' AND "  . pSQL($field_name_in_db) .  " IS NOT NULL";
                break;
            default:
                break;
        }
    }

    private function addStringTypeFilterToQuery($field_name_in_db, $filter_components)
    {
        switch ($filter_components['type']) {
            case 'is':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " = '" . pSQL($filter_components['value']) . "'";
                break;
            case 'is_not':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " != '" . pSQL($filter_components['value']) . "' 
                                                   OR " . pSQL($field_name_in_db) . " IS NULL";
                break;
            case 'list':
                $allowed_values = $this->prepareValuesForInAndNotInQueryConditions($filter_components['value']);
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " IN (" . $allowed_values . ")";
                break;
            case 'not_list':
                $not_allowed_values = $this->prepareValuesForInAndNotInQueryConditions($filter_components['value']);
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " NOT IN (" . $not_allowed_values . ") 
                                                    OR " . pSQL($field_name_in_db) . " IS NULL";
                break;
            case 'contains':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " LIKE '%" . pSQL($filter_components['value']) . "%'";
                break;
            case 'not_contain':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " NOT LIKE '%" . pSQL($filter_components['value']) . "%' 
                                                    OR " . pSQL($field_name_in_db) . " IS NULL";
                break;
            case 'empty':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " = '' OR " . pSQL($field_name_in_db) . " IS NULL";
                break;
            case 'not_empty':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " != '' AND " . pSQL($field_name_in_db) . " IS NOT NULL";
                break;
            default:
                break;
        }
    }

    private function addSelectTypeFilterToQuery($field_name_in_db, $filter_value)
    {
        $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " = '" . pSQL($filter_value) . "' AND " . pSQL($field_name_in_db) . " IS NOT NULL";
    }

    private function addCheckboxTypeFilterToQuery($field_name_in_db, $filter_value)
    {
        $allowed_values = implode(',', $filter_value);
        $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " IN (".$this->prepareValuesForInAndNotInQueryConditions($allowed_values).")";
    }

    private function addDateTypeFilterToQuery($field_name_in_db, $filter_components)
    {
        switch ($filter_components['type']) {
            case 'today':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " BETWEEN '" . pSQL(date('Y-m-d', time())) . "' AND DATE_ADD('" . pSQL(date('Y-m-d', time())) . "',INTERVAL 1 DAY)  \n";
                break;
            case 'yesterday':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " BETWEEN '" . pSQL(date('Y-m-d',  strtotime("-1 days"))) . "' AND DATE_ADD('" . pSQL(date('Y-m-d',  strtotime("-1 days"))) . "',INTERVAL 1 DAY)  \n";
                break;
            case 'this_week':
                $this->query_filters['where'] .= " AND YEARWEEK(" . pSQL($field_name_in_db) . ", 1) = YEARWEEK(CURDATE(), 1)  \n";
                break;
            case 'last_week':
                $this->query_filters['where'] .= " AND YEARWEEK(" . pSQL($field_name_in_db) . ") = YEARWEEK(NOW() - INTERVAL 1 WEEK)  \n";
                break;
            case 'last_seven_days':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " BETWEEN CURRENT_TIMESTAMP - INTERVAL '7' DAY AND CURRENT_TIMESTAMP   \n";
                break;
            case 'this_month':
                $this->query_filters['where'] .= " AND YEAR(" . pSQL($field_name_in_db) . ") = YEAR(NOW()) AND MONTH(" . pSQL($field_name_in_db) . ") = MONTH(NOW())  \n";
                break;
            case 'last_month':
                $this->query_filters['where'] .= " AND YEAR(" . pSQL($field_name_in_db) . ") = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(" . pSQL($field_name_in_db) . ") = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)  \n";
                break;
            case 'before_date':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " <= '" . pSQL($filter_components['val_1']) . "'  \n";
                break;
            case 'after_date':
                $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " >= '" . pSQL($filter_components['val_1']) . "'  \n";
                break;
            case 'period':
                if ($filter_components['val_1'] && !$filter_components['val_2']) {
                    $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " >= '" . pSQL($filter_components['val_1']) . "'  \n";
                } else if (!$filter_components['val_1'] && $filter_components['val_2']) {
                    $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " <= '" . pSQL($filter_components['val_2']) . "'  \n";
                } else if ($filter_components['val_1'] && $filter_components['val_2']) {
                    $this->query_filters['where'] .= " AND " . pSQL($field_name_in_db) . " BETWEEN '" . pSQL($filter_components['val_1']) . "' AND DATE_ADD('" . pSQL($filter_components['val_2']) . "',INTERVAL 1 DAY)  \n";
                }

                break;
        }
    }

    private function addIsNewFilterToQuery($filter_value)
    {
        $number_of_days_when_considered_new = \Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        $number_of_days_when_considered_new = (\Validate::isUnsignedInt($number_of_days_when_considered_new) ? $number_of_days_when_considered_new : 20);

        $this->query_filters['where'] .= " AND DATEDIFF(ps.`date_add`, DATE_SUB('" . date('Y-m-d') . " 00:00:00',
                                                        INTERVAL " . (int)$number_of_days_when_considered_new . " DAY)
                                                        ) " . ($filter_value == 1 ? ">" : "<") . " 0 ";
    }

    private function prepareValuesForInAndNotInQueryConditions($list_of_values)
    {
        if (is_string($list_of_values)) {
            $list_of_values = explode(',', $list_of_values);
        }

        foreach ($list_of_values as &$value) {
            $value = "'" . pSQL(trim($value)) . "'";
        }

        return implode(',', $list_of_values);
    }

    public static function filter($filtered_value, $filter)
    {
        if (is_string($filter['value']) && PESerializationChecker::isStringSerialized($filter['value'])) {
            $filter_components = Tools::unSerialize($filter['value']);
        } else if (is_string($filter['value']) && json_decode($filter['value'], true)) {
            $filter_components = json_decode($filter['value'], true);
        } else {
            $filter_components = $filter['value'];
        }

        $filter_type = $filter_components['type'];
        $filter_value = $filter_components['value'];

        if ($filter['field'] == 'attribute' || $filter['field'] == 'attribute_group') {
            return self::filterCombinationByAttribute($filter_value, $filtered_value, $filter_type);
        }

        switch ($filter_type) {
            case '>':
                return $filtered_value > $filter_value;
            case '<':
                return $filtered_value < $filter_value;
            case '>=':
                return $filtered_value >= $filter_value;
            case '<=':
                return $filtered_value <= $filter_value;
            case '=':
            case 'is':
                return $filtered_value == $filter_value;
            case '!=':
            case 'is_not':
                return $filtered_value != $filter_value;
            case 'in':
            case 'list':
                $filter_value = explode(',', $filter_value);
                return in_array($filtered_value, $filter_value);
            case 'not_in':
            case 'not_list':
                $filter_value = explode(',', $filter_value);
                return !in_array($filtered_value, $filter_value);
            case 'contains':
                return strpos($filtered_value, $filter_value) !== false;
            case 'not_contain':
                return strpos($filtered_value, $filter_value) === false;
            case 'empty':
                return $filtered_value == '';
            case 'not_empty':
                return $filtered_value != '';
            default:
                break;
        }

        return true;
    }

    private static function filterCombinationByAttribute($filter_value, $filtered_value, $filter_type)
    {
        $filtered_value = explode(',', $filtered_value);

        switch ($filter_type) {
            case 'is':
                return in_array($filter_value, $filtered_value);
            case 'is_not':
                return !in_array($filter_value, $filtered_value);
            case 'list':
                $filter_value = explode(',', $filter_value);
                return !empty(array_intersect($filtered_value, $filter_value));
            case 'not_list':
                $filter_value = explode(',', $filter_value);
                return empty(array_intersect($filtered_value, $filter_value));
            case 'contains':
                foreach ($filtered_value as $value) {
                    if (strpos($value, $filter_value) !== false) {
                        return true;
                    }
                }

                return false;
            case 'not_contain':
                foreach ($filtered_value as $value) {
                    if (strpos($value, $filter_value) === false) {
                        return true;
                    }
                }

                return false;
            case 'empty':
                return empty($filtered_value);
            case 'not_empty':
                return !empty($filtered_value);
            default:
                break;
        }

        return false;
    }

    private function getIdShop()
    {
        if ($this->configuration['id_shop']) {
            return $this->configuration['id_shop'];
        }

        return \Configuration::get('PS_SHOP_DEFAULT');
    }

    private function joinFeatureTables()
    {
        if ($this->is_feature_tables_joined) {
            return false;
        }

        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "feature_product as fp ";
        $this->query_filters['join'] .= " ON p.id_product = fp.id_product ";
        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "feature_lang as fl ";
        $this->query_filters['join'] .= " ON fp.id_feature = fl.id_feature AND fl.id_lang = '".(int)$this->id_lang."' ";
        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "feature_value_lang as fvl ";
        $this->query_filters['join'] .= " ON fp.id_feature_value = fvl.id_feature_value AND fvl.id_lang = '".(int)$this->id_lang."' ";

        $this->is_feature_tables_joined = true;

        return true;
    }

    private function joinCustomizationTables()
    {
        if ($this->is_customization_tables_joined) {
            return false;
        }

        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "customization_field as cf ";
        $this->query_filters['join'] .= " ON p.id_product = cf.id_product ";
        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "customization_field_lang as cfl ";
        $this->query_filters['join'] .= " ON cf.id_customization_field = cfl.id_customization_field 
                                          AND cfl.id_lang = '" . (int)$this->id_lang . "' 
                                          AND cfl.id_shop = '" . (int)$this->getIdShop() . "'";

        $this->is_customization_tables_joined = true;

        return true;
    }

    private function joinImageTable()
    {
        if ($this->is_image_table_joined) {
            return false;
        }

        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "image AS i ";
        $this->query_filters['join'] .= " ON p.id_product = i.id_product ";
        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "image_lang AS il ";
        $this->query_filters['join'] .= " ON i.id_image = il.id_image AND il.id_lang = '" . (int)$this->id_lang . "' ";

        $this->is_image_table_joined = true;

        return true;
    }

    private function joinAttachmentTables()
    {
        if ($this->is_attachment_tables_joined) {
            return false;
        }

        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "product_attachment as product_attachment ";
        $this->query_filters['join'] .= " ON p.id_product = product_attachment.id_product ";
        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "attachment as attachment ";
        $this->query_filters['join'] .= " ON product_attachment.id_attachment = attachment.id_attachment ";
        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "attachment_lang as attachment_lang ";
        $this->query_filters['join'] .= " ON attachment.id_attachment = attachment_lang.id_attachment 
                                          AND attachment_lang.id_lang = '" . (int)$this->id_lang . "' ";

        $this->is_attachment_tables_joined = true;

        return true;
    }

    private function joinSupplierTables()
    {
        if ($this->is_supplier_tables_joined) {
            return false;
        }

        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "product_supplier as product_supplier ";
        $this->query_filters['join'] .= " ON p.id_product = product_supplier.id_product ";
        $this->query_filters['join'] .= " AND (pa.id_product_attribute = product_supplier.id_product_attribute OR product_supplier.id_product_attribute = '0') ";
        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "supplier as supplier ";
        $this->query_filters['join'] .= " ON product_supplier.id_supplier = supplier.id_supplier ";

        $this->is_supplier_tables_joined = true;

        return true;
    }

    private function joinDefaultSupplierTables()
    {
        if ($this->is_default_supplier_tables_joined) {
            return false;
        }

        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "product_supplier as default_product_supplier ";
        $this->query_filters['join'] .= " ON p.id_product = default_product_supplier.id_product ";
        $this->query_filters['join'] .= " AND (pa.id_product_attribute = default_product_supplier.id_product_attribute OR default_product_supplier.id_product_attribute = '0') ";
        $this->query_filters['join'] .= " AND p.id_supplier = default_product_supplier.id_supplier ";
        $this->query_filters['join'] .= " LEFT JOIN " . _DB_PREFIX_ . "supplier as default_supplier ";
        $this->query_filters['join'] .= " ON default_product_supplier.id_supplier = default_supplier.id_supplier ";

        $this->is_default_supplier_tables_joined = true;

        return true;
    }
}