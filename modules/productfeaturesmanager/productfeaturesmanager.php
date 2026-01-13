<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @author    Qwavee
 * @copyright 2007-2024
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductFeaturesManager extends Module
{
    public function __construct()
    {
        $this->name = 'productfeaturesmanager';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'Qwavee';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Gestor de Características de Productos');
        $this->description = $this->l('Permite visualizar, editar y controlar masivamente las características de los productos');
        $this->confirmUninstall = $this->l('¿Estás seguro de que quieres desinstalar?');
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install() &&
            $this->installTab() &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall() && $this->uninstallTab();
    }

    private function installTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminProductFeaturesManager');
        if ($id_tab) {
            return true;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->enabled = 1;
        $tab->class_name = 'AdminProductFeaturesManager';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Gestor de Características';
        }
        
        $id_parent = (int)Tab::getIdFromClassName('AdminCatalog');
        if (!$id_parent) {
            $id_parent = (int)Tab::getIdFromClassName('AdminParentCatalog');
        }
        if (!$id_parent) {
            $id_parent = 0;
        }
        
        $tab->id_parent = $id_parent;
        $tab->module = $this->name;
        $tab->icon = 'list';
        
        if ($tab->save()) {
            Tab::initAccess($tab->id, $this->context);
            return true;
        }
        
        return false;
    }

    private function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminProductFeaturesManager');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return false;
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('controller') == 'AdminProductFeaturesManager') {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addJS($this->_path . 'views/js/admin.js');
        }
    }

    /**
     * Obtener todas las características disponibles
     */
    public function getAllFeatures()
    {
        $sql = 'SELECT f.*, fl.name 
                FROM `' . _DB_PREFIX_ . 'feature` f
                LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` fl 
                    ON f.id_feature = fl.id_feature 
                    AND fl.id_lang = ' . (int)$this->context->language->id . '
                ORDER BY fl.name ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Obtener valores de una característica para todos los productos (con paginación y filtros)
     */
    public function getFeatureValuesForProducts($id_feature, $id_lang = null, $limit = 500, $offset = 0, $search = '')
    {
        if (!$id_lang) {
            $id_lang = $this->context->language->id;
        }

        $id_shop = isset($this->context->shop) && $this->context->shop->id ? (int)$this->context->shop->id : (int)Configuration::get('PS_SHOP_DEFAULT');

        $search_condition = '';
        if (!empty($search)) {
            $search = pSQL($search);
            $search_condition = ' AND (
                pl.name LIKE "%' . $search . '%" 
                OR p.reference LIKE "%' . $search . '%"
                OR p.id_product LIKE "%' . $search . '%"
                OR EXISTS (
                    SELECT 1 FROM `' . _DB_PREFIX_ . 'product_supplier` ps_search
                    INNER JOIN `' . _DB_PREFIX_ . 'supplier` s_search ON ps_search.id_supplier = s_search.id_supplier
                    WHERE ps_search.id_product = p.id_product
                    AND s_search.name LIKE "%' . $search . '%"
                )
                OR EXISTS (
                    SELECT 1 FROM `' . _DB_PREFIX_ . 'supplier` s_default_search
                    WHERE s_default_search.id_supplier = p.id_supplier
                    AND p.id_supplier > 0
                    AND s_default_search.name LIKE "%' . $search . '%"
                )
            )';
        }

        $sql = 'SELECT 
                    p.id_product,
                    p.reference,
                    pl.name as product_name,
                    MAX(fv.id_feature_value) as id_feature_value,
                    MAX(fvl.value) as feature_value,
                    COALESCE(
                        NULLIF(
                            TRIM(BOTH ", " FROM GROUP_CONCAT(DISTINCT supplier_combined.name ORDER BY supplier_combined.name SEPARATOR ", ")),
                            ""
                        ),
                        "--"
                    ) as supplier_names
                FROM `' . _DB_PREFIX_ . 'product` p
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl 
                    ON p.id_product = pl.id_product 
                    AND pl.id_lang = ' . (int)$id_lang . '
                    AND pl.id_shop = ' . (int)$id_shop . '
                LEFT JOIN `' . _DB_PREFIX_ . 'feature_product` fp 
                    ON p.id_product = fp.id_product 
                    AND fp.id_feature = ' . (int)$id_feature . '
                LEFT JOIN `' . _DB_PREFIX_ . 'feature_value` fv 
                    ON fp.id_feature_value = fv.id_feature_value
                LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl 
                    ON fv.id_feature_value = fvl.id_feature_value 
                    AND fvl.id_lang = ' . (int)$id_lang . '
                LEFT JOIN (
                    SELECT ps.id_product, s.id_supplier, s.name
                    FROM `' . _DB_PREFIX_ . 'product_supplier` ps
                    INNER JOIN `' . _DB_PREFIX_ . 'supplier` s ON ps.id_supplier = s.id_supplier
                    UNION
                    SELECT p2.id_product, s2.id_supplier, s2.name
                    FROM `' . _DB_PREFIX_ . 'product` p2
                    INNER JOIN `' . _DB_PREFIX_ . 'supplier` s2 ON p2.id_supplier = s2.id_supplier
                    WHERE p2.id_supplier > 0
                ) supplier_combined ON supplier_combined.id_product = p.id_product
                WHERE p.id_shop_default = ' . (int)$id_shop . 
                $search_condition . '
                GROUP BY p.id_product, p.reference, pl.name
                ORDER BY pl.name ASC
                LIMIT ' . (int)$offset . ', ' . (int)$limit;

        $result = Db::getInstance()->executeS($sql);
        return $result ?: [];
    }
    
    /**
     * Contar total de productos para paginación
     */
    public function countProductsForFeature($id_feature, $search = '')
    {
        $id_shop = isset($this->context->shop) && $this->context->shop->id ? (int)$this->context->shop->id : (int)Configuration::get('PS_SHOP_DEFAULT');
        
        // Construir filtro de búsqueda (incluyendo búsqueda por nombre de proveedor)
        $search_condition = '';
        if (!empty($search)) {
            $search = pSQL($search);
            $id_lang = (int)$this->context->language->id;
            $search_condition = ' AND (
                EXISTS (
                    SELECT 1 FROM `' . _DB_PREFIX_ . 'product_lang` pl 
                    WHERE pl.id_product = p.id_product 
                    AND pl.id_lang = ' . $id_lang . '
                    AND pl.id_shop = ' . $id_shop . '
                    AND (pl.name LIKE "%' . $search . '%" OR p.reference LIKE "%' . $search . '%" OR p.id_product LIKE "%' . $search . '%")
                ) OR EXISTS (
                    SELECT 1 FROM `' . _DB_PREFIX_ . 'product_supplier` ps_search
                    INNER JOIN `' . _DB_PREFIX_ . 'supplier` s_search ON ps_search.id_supplier = s_search.id_supplier
                    WHERE ps_search.id_product = p.id_product
                    AND s_search.name LIKE "%' . $search . '%"
                ) OR EXISTS (
                    SELECT 1 FROM `' . _DB_PREFIX_ . 'supplier` s_default_search
                    WHERE s_default_search.id_supplier = p.id_supplier
                    AND p.id_supplier > 0
                    AND s_default_search.name LIKE "%' . $search . '%"
                )
            )';
        }
        
        $sql = 'SELECT COUNT(DISTINCT p.id_product) as total
                FROM `' . _DB_PREFIX_ . 'product` p
                WHERE p.id_shop_default = ' . (int)$id_shop . 
                $search_condition;
        
        $result = Db::getInstance()->getValue($sql);
        return (int)$result;
    }

    /**
     * Actualizar valor de característica para un producto
     */
    public function updateProductFeature($id_product, $id_feature, $id_feature_value)
    {
        try {
            // Validar que el producto existe
            $product = new Product($id_product);
            if (!Validate::isLoadedObject($product)) {
                return false;
            }

            // PRIMERO: Eliminar cualquier valor existente de esta característica para este producto
            $sql_delete = 'DELETE FROM `' . _DB_PREFIX_ . 'feature_product`
                           WHERE id_product = ' . (int)$id_product . ' 
                           AND id_feature = ' . (int)$id_feature;
            Db::getInstance()->execute($sql_delete);

            // Si id_feature_value es 0, solo eliminamos (ya está hecho arriba)
            if ($id_feature_value == 0) {
                return true;
            }

            // Validar que el valor de característica existe y pertenece a la característica correcta
            $feature_value = new FeatureValue($id_feature_value);
            if (!Validate::isLoadedObject($feature_value) || $feature_value->id_feature != $id_feature) {
                return false;
            }

            // SEGUNDO: Insertar el nuevo valor (ahora sin duplicados)
            $sql_insert = 'INSERT INTO `' . _DB_PREFIX_ . 'feature_product` 
                           (id_feature, id_product, id_feature_value) 
                           VALUES (' . (int)$id_feature . ', ' . (int)$id_product . ', ' . (int)$id_feature_value . ')';
            
            return Db::getInstance()->execute($sql_insert);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener valores disponibles de una característica (sin duplicados)
     */
    public function getFeatureValues($id_feature, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = $this->context->language->id;
        }

        $sql = 'SELECT DISTINCT fv.id_feature_value, fvl.value
                FROM `' . _DB_PREFIX_ . 'feature_value` fv
                INNER JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl 
                    ON fv.id_feature_value = fvl.id_feature_value 
                    AND fvl.id_lang = ' . (int)$id_lang . '
                WHERE fv.id_feature = ' . (int)$id_feature . '
                AND (fv.custom = 0 OR fv.custom IS NULL)
                GROUP BY fv.id_feature_value, fvl.value
                ORDER BY fvl.value ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Obtener un valor de característica en todos los idiomas
     */
    public function getFeatureValueInAllLanguages($id_feature_value)
    {
        $sql = 'SELECT fvl.id_lang, fvl.value
                FROM `' . _DB_PREFIX_ . 'feature_value_lang` fvl
                WHERE fvl.id_feature_value = ' . (int)$id_feature_value;

        $results = Db::getInstance()->executeS($sql);
        $values_by_lang = [];
        
        if ($results) {
            foreach ($results as $row) {
                $values_by_lang[$row['id_lang']] = [
                    'id_lang' => $row['id_lang'],
                    'value' => $row['value']
                ];
            }
        }
        
        return $values_by_lang;
    }

    /**
     * Crear nuevo valor de característica
     */
    public function createFeatureValue($id_feature, $values_by_lang)
    {
        $feature_value = new FeatureValue();
        $feature_value->id_feature = (int)$id_feature;
        $feature_value->custom = 0;

        if ($feature_value->add()) {
            // Añadir traducciones
            foreach ($values_by_lang as $id_lang => $value) {
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'feature_value_lang`
                        (id_feature_value, id_lang, value)
                        VALUES (' . (int)$feature_value->id . ', ' . (int)$id_lang . ', "' . pSQL($value) . '")
                        ON DUPLICATE KEY UPDATE value = "' . pSQL($value) . '"';
                Db::getInstance()->execute($sql);
            }
            return $feature_value->id;
        }

        return false;
    }
}

