<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace Gmerchantcenterpro\Dao;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Gmerchantcenterpro\ModuleLib\moduleTools;

class moduleDao
{
    /**
     * method count the number of product by combination or not
     *
     * @param int $id_shop
     * @param bool $with_combination
     *
     * @return mixed
     */
    public static function countProducts($id_shop, $with_combination = false)
    {
        $query = new \DbQuery();
        $query->select('COUNT(p.id_product) as cnt');
        $query->from('product', 'p');

        if (!empty($with_combination)) {
            $query->leftJoin('product_attribute', 'pa', 'p.id_product = pa.id_product');
        }
        $query->where('p.state = 1');
        // $query->where('p.id_shop_default = ' . (int)$id_shop);

        return \Db::getInstance()->getValue($query);
    }

    /**
     * count the number of product or return all product IDs to export
     *
     * @param int $iShopId
     * @param int $bExportMode
     * @param bool $bCountMode
     * @param int $iFloor
     * @param int $iStep
     * @param bool $bExportCombination
     * @param bool $bExcludedProduct
     *
     * @return mixed
     */
    public static function getProductIds($iShopId, $bExportMode = 0, $bCountMode = false, $iFloor = null, $iStep = null, $bExportCombination = false, $bExcludedProduct = false)
    {
        $sQuery = 'SELECT '
            . ($bCountMode ? 'COUNT(DISTINCT(p.id_product)) as cnt ' : 'DISTINCT(p.id_product) as id')
            . ' FROM ' . _DB_PREFIX_ . 'product p '
            . \Shop::addSqlAssociation('product', 'p', false)
            . (!$bExportMode ? ' LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (p.id_product = cp.id_product)' : ' LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` man ON (p.id_manufacturer = man.id_manufacturer)')
            . ' WHERE product_shop.active = 1'
            . ' AND ' . (!$bExportMode ? 'cp.`id_category`' : 'man.`id_manufacturer`') . ' IN (SELECT id_' . (!$bExportMode ? 'category' : 'brands') . ' FROM `' . _DB_PREFIX_ . 'gmcp_' . (!$bExportMode ? 'categories' : 'brands') . '` gc ' . (\Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') ? ' WHERE gc.`id_shop` = ' . (int) $iShopId : '') . ')';

        if ($iFloor !== null && !empty($iStep)) {
            $sQuery .= ' LIMIT ' . (int) $iFloor . ', ' . (int) $iStep;
        }

        // count products number
        if ($bCountMode) {
            $aResult = \Db::getInstance()->getRow($sQuery);

            $mReturn = $aResult['cnt'] ? $aResult['cnt'] : 0;
        } // return product IDs
        else {
            $mReturn = \Db::getInstance()->ExecuteS($sQuery);
        }

        return $mReturn;
    }

    /**
     * method returns specific attributes
     *
     * @param int $id_product
     * @param mixed $attribute_group_id
     * @param int $id_lang
     * @param int $id_product_attribute
     *
     * @return array
     */
    public static function getProductAttribute($id_product, $attribute_group_id, $id_lang, $id_product_attribute = 0)
    {
        $query = new \DbQuery();
        $query->select('distinct(al.name)');
        $query->from('product_attribute', 'pa');
        $query->leftJoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = pa.id_product_attribute');
        $query->leftJoin('attribute', 'a', 'a.id_attribute = pac.id_attribute');
        $query->leftJoin('attribute_group', 'ag', 'ag.id_attribute_group = a.id_attribute_group');
        $query->leftJoin('attribute_lang', 'al', 'a.id_attribute = al.id_attribute');
        $query->where('pa.id_product = ' . (int) $id_product);

        if (!empty($id_product_attribute)) {
            $query->where('pac.id_product_attribute = ' . (int) $id_product_attribute);
        }
        $query->where('al.id_lang = ' . (int) $id_lang);
        $query->where('ag.id_attribute_group IN(' . $attribute_group_id . ')');
        $query->orderBy('al.name ASC');
        $query->limit(0, 30);

        return \Db::getInstance()->ExecuteS($query);
    }

    /**
     * method returns specific feature
     *
     * @param int $id_product
     * @param int $id_feature
     * @param int $id_lang
     *
     * @return string
     */
    public static function getProductFeature($id_product, $id_feature, $id_lang)
    {
        $query = new \DbQuery();
        $query->select('fvl.value');
        $query->from('feature_value_lang', 'fvl');
        $query->leftJoin('feature_value', 'fv', 'fvl.id_feature_value = fv.id_feature_value');
        $query->leftJoin('feature_product', 'fp', 'fv.id_feature_value = fp.id_feature_value');
        $query->where('fp.id_product = ' . (int) $id_product);
        $query->where('fp.id_feature = ' . (int) $id_feature);
        $query->where('fvl.id_lang = ' . (int) $id_lang);

        return \Db::getInstance()->getValue($query);
    }

    /**
     * method returns the product's combinations
     *
     * @param int $id_shop
     * @param int $id_product
     * @param bool $has_exclusion
     *
     * @return mixed
     */
    public static function getProductCombination($id_shop, $id_product, $has_exclusion)
    {
        $query = new \DbQuery();
        $query->select('*, pa.id_product_attribute, pas.id_shop, sa.quantity as combo_quantity');
        $query->from('product_attribute', 'pa');
        $query->leftJoin('product_attribute_shop', 'pas', 'pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = ' . (int) $id_shop . '');
        $query->leftJoin('stock_available', 'sa', 'pas.id_product_attribute = sa.id_product_attribute');
        $query->where('pa.id_product = ' . (int) $id_product);

        // Check the column according to the multishop feature
        if (!empty(\Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE'))) {
            $idShopGroup = \GMerchantCenterPro::$oContext->shop->id_shop_group;
            $shareStock = moduleTools::getGroupShopDetail('share_stock');
            if ($shareStock) {
                if ($id_shop != \GMerchantCenterPro::$oContext->shop->id) {
                    $request = new \DbQuery();
                    $request->select('id_shop_group');
                    $request->from('shop');
                    $request->where('id_shop = ' . (int) $id_shop);
                    $data = \Db::getInstance()->getValue($request);
                    /** @phpstan-ignore-next-line */
                    $idShopGroup = $data[0]['id_shop_group'];
                }
                $query->where('sa.id_shop_group = ' . (int) $idShopGroup);
            } else {
                $query->where('sa.id_shop = ' . (int) $id_shop);
            }
        }

        if (!empty($has_exclusion)) {
            $query->where('pa.id_product_attribute NOT IN (SELECT id_product_attribute FROM `' . _DB_PREFIX_ . 'gmcp_product_excluded`)');
        }

        $result = \Db::getInstance()->ExecuteS($query);

        return !empty($result) ? $result : false;
    }

    /**
     * method returns the product's combination attributes
     *
     * @param int $id_product_attribute
     * @param int $id_lang
     * @param int $id_shop
     *
     * @return mixed
     */
    public static function getProductComboAttributes($id_product_attribute, $id_lang, $id_shop)
    {
        $query = new \DbQuery();
        $query->select('distinct(al.`name`)');
        $query->from('product_attribute_shop', 'pa');
        $query->leftJoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = pa.id_product_attribute');
        $query->innerJoin('attribute_lang', 'al', 'pac.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . '');
        $query->where('pac.id_product_attribute = ' . (int) $id_product_attribute);
        $query->where('pa.id_shop = ' . (int) $id_shop);

        $data = \Db::getInstance()->ExecuteS($query);

        return !empty($data) ? $data : false;
    }

    /**
     * method returns shop's categories
     *
     * @param int $id_shop
     * @param int $id_lang
     * @param int $home_cat_id
     *
     * @return array
     */
    public static function getShopCategories($id_shop, $id_lang, $home_cat_id = null)
    {
        $query = new \DbQuery();
        $query->select('c.id_category, cl.name, cl.id_lang');
        $query->from('category', 'c');
        $query->innerJoin('category_shop', 'cs', 'c.id_category = cs.id_category AND cs.id_shop = ' . intval($id_shop) . '');
        $query->innerJoin('category_lang', 'cl', 'c.id_category = cl.id_category AND cl.`id_lang` = ' . (int) $id_lang . \Shop::addSqlRestrictionOnLang('cl') . '');
        $query->where('level_depth > 0');

        $categories = \Db::getInstance()->ExecuteS($query);

        if ($home_cat_id !== null) {
            $translations = is_string(\GMerchantCenterPro::$conf['GMCP_HOME_CAT']) ? moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf['GMCP_HOME_CAT']) : \GMerchantCenterPro::$conf['GMCP_HOME_CAT'];
        }

        foreach ($categories as $k => &$category) {
            // set category path
            $category['path'] = $category['id_category'] == $home_cat_id ? (!empty($translations[$id_lang]) ? $translations[$id_lang] : $category['name']) : moduleTools::getProductPath((int) $category['id_category'], $id_lang);
            $category['len'] = strlen($category['path']);

            $has_to_delete = trim($category['path']);

            if (empty($has_to_delete)) {
                unset($categories[$k]);
            }
        }

        return $categories;
    }

    /**
     * method check a language as active
     *
     * @param string $iso_code
     *
     * @return bool
     */
    public static function checkActiveLanguage($iso_code)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('lang', 'l');
        $query->where('l.active = 1');
        $query->where('l.iso_code ="' . \pSQL($iso_code) . '"');

        $result = \Db::getInstance()->getRow($query);

        return !empty($result) && count($result) ? true : false;
    }

    /**
     * returns the additional shipping cost
     *
     * @param int $id_product
     * @param int $id_shop
     *
     * @return mixed : int or float
     */
    public static function getAdditionalShippingCost($id_product, $id_shop)
    {
        $query = new \DbQuery();
        $query->select('additional_shipping_cost');
        $query->from('product_shop', 'ps');
        $query->where('ps.id_product =' . (int) $id_product);
        $query->where('ps.id_shop =' . (int) $id_shop);

        return \Db::getInstance()->getValue($query);
    }

    /**
     * method returns the good supplier reference
     *
     * @param int $id_product
     * @param int $id_supplier
     * @param int $id_product_attribute
     *
     * @return string
     */
    public static function getProductSupplierReference($id_product, $id_supplier, $id_product_attribute = 0)
    {
        // set vars
        $supplier_ref = '';

        if ($id_supplier != 0) {
            $supplier_ref = \ProductSupplier::getProductSupplierReference($id_product, $id_product_attribute, $id_supplier);

            if (empty($supplier_ref)) {
                $query = new \DbQuery();
                $query->select('product_supplier_reference');
                $query->from('product_supplier', 'ps');
                $query->innerJoin('product_attribute', 'pa', 'pa.id_product_attribute = ps.id_product_attribute AND pa.default_on = 1');
                $query->where('ps.id_product =' . (int) $id_product);
                $query->where('ps.id_supplier =' . (int) $id_supplier);

                $supplier_ref = \Db::getInstance()->getValue($query);
            }
        } elseif (!empty($id_product_attribute)) {
            $query = new \DbQuery();
            $query->select('product_supplier_reference');
            $query->from('product_supplier', 'ps');
            $query->where('ps.id_product_attribute =' . (int) $id_product_attribute);
            $query->where('ps.product_supplier_reference != ""');

            $supplier_ref = \Db::getInstance()->getValue($query);
        }

        return $supplier_ref;
    }

    /**
     * return all feature available
     *
     * @return array
     */
    public static function getFeature()
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('feature_lang', 'fl');
        $query->where('fl.`id_lang` = ' . (int) \GMerchantCenterPro::$oContext->cookie->id_lang);
        $query->orderBy('name ASC');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * method search matching product names for autocomplete
     *
     * @param string $search_string
     * @param bool $export_combination
     * @param string $exclusion_ids
     *
     * @return array
     */
    public static function searchProducts($search_string, $export_combination = false, $exclusion_ids = '')
    {
        if ($exclusion_ids != '0,' && !empty($exclusion_ids)) {
            $excluded_product_ids = implode(',', array_map('intval', explode(',', $exclusion_ids)));
        }

        $query = new \DbQuery();
        if (empty($export_combination)) {
            $query->select('p.id_product, pl.name');
        } else {
            $query->select('p.id_product, pl.name,pa.id_product_attribute');
        }
        $query->from('product', 'p');
        // $query->where('p.id_shop_default = ' . (int)(int)\Context::getContext()->shop->id);

        if (!empty($export_combination)) {
            $query->leftJoin('product_attribute', 'pa', 'p.id_product = pa.id_product');
        }
        $query->leftJoin('product_lang', 'pl', 'p.id_product = pl.id_product ' . \Shop::addSqlRestrictionOnLang('pl') . '');
        $query->where('pl.name LIKE "%' . \pSQL($search_string) . '%"');
        $query->where('pl.id_lang = ' . (int) \GMerchantCenterPro::$iCurrentLang);

        if (empty(\GMerchantCenterPro::$conf['GMCP_P_COMBOS']) && !empty($excluded_product_ids)) {
            $query->where('p.id_product NOT IN (' . $excluded_product_ids . ') ');
        }

        return \Db::getInstance()->ExecuteS($query);
    }

    /**
     * getProductIdsByFeature() method return all product with a specific feature
     *
     * @param int $id_feature
     *
     * @return array
     */
    public static function getProductIdsByFeature($id_feature)
    {
        $query = new \DbQuery();
        $query->select('id_product');
        $query->from('feature_product', 'pf');
        $query->leftJoin('feature_lang', 'fl', 'fl.id_feature = pf.id_feature AND fl.id_lang = ' . (int) \GMerchantCenterPro::$iCurrentLang . '');
        $query->leftJoin('feature_value_lang', 'fvl', 'fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = ' . (int) \GMerchantCenterPro::$iCurrentLang . '');
        $query->leftJoin('feature', 'f', 'f.id_feature = pf.id_feature');
        $query->where('fvl.id_feature_value=' . (int) $id_feature);
        $query->groupBy('id_product');
        $query->orderBy('f.position ASC');

        return \Db::getInstance()->executeS($query);
    }

    /**
     * getProductsIdFromAttribute() method return all product with a specific attribute ID
     *
     * @param int $id_product_attribute
     *
     * @return array
     */
    public static function getProductsIdFromAttribute($id_product_attribute)
    {
        $query = new \DbQuery();
        if (empty(\GMerchantCenterPro::$conf['GMCP_P_COMBOS'])) {
            $query->select('DISTINCT(p.id_product)');
        } else {
            $query->select('pa.id_product, pac.id_product_attribute');
        }

        $query->from('product', 'p');
        $query->leftJoin('product_attribute', 'pa', 'p.id_product = pa.id_product');
        $query->leftJoin('product_attribute_combination', 'pac', 'pa.id_product_attribute = pac.id_product_attribute');
        // $query->where('p.id_shop_default = ' . (int)(int)\Context::getContext()->shop->id);
        $query->where('p.state=1');
        $query->where('pac.id_attribute = ' . (int) $id_product_attribute);

        return \Db::getInstance()->executeS($query);
    }

    /**
     * return the order id from the cart id
     *
     * @param int $id_cart the id_cart value
     *
     * @return int
     */
    public static function getOrderIdFromCart($id_cart)
    {
        $query = new \DbQuery();
        $query->select('id_order');
        $query->from('orders', 'o');
        $query->where('o.id_cart=' . (int) $id_cart);

        return (int) \Db::getInstance()->getValue($query);
    }
}
