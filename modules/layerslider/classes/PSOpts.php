<?php
/**
 * Creative Slider - Responsive Slideshow Module
 * https://creativeslider.webshopworks.com
 *
 * @author    WebshopWorks <info@webshopworks.com>
 * @copyright 2015-2025 WebshopWorks
 * @license   One Domain Licence
 *
 * Not allowed to resell or redistribute this software
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PSOpts
{
    const TAB = '&ensp;&ensp;';

    const PRODUCT_LIMIT = 3000;

    private static $products = [];

    private static function getProducts($id_lang, $id_category)
    {
        if (empty(self::$products[$id_lang])) {
            self::$products[$id_lang] = [];
            $products = &self::$products[$id_lang];
            $id_shop = $GLOBALS['context']->shop->id;
            $rows = Db::getInstance()->executeS('
                SELECT p.`id_product`, p.`id_category_default`, pl.`name` FROM ' . _DB_PREFIX_ . 'product p
                LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.`id_product` = pl.`id_product`
                WHERE pl.`id_lang` = ' . (int) $id_lang . ' AND pl.`id_shop` = ' . (int) $id_shop . '
                ORDER BY p.`id_category_default`, pl.`name`
            ') ?: [];

            foreach ($rows as &$row) {
                $cat = $row['id_category_default'];

                if (!isset($products[$cat])) {
                    $products[$cat] = [];
                }
                $products[$cat][] = &$row;
            }
        }

        return isset(self::$products[$id_lang][$id_category]) ? self::$products[$id_lang][$id_category] : [];
    }

    public static function getNestedCategories($root_category = null, $id_lang = false, $active = true)
    {
        if (isset($root_category) && !Validate::isInt($root_category) || !Validate::isBool($active)) {
            exit(Tools::displayError());
        }
        $id_shop = $GLOBALS['context']->shop->id;
        $result = Db::getInstance()->executeS('
            SELECT c.*, cl.* FROM ' . _DB_PREFIX_ . 'category c
            INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs ON cs.`id_category` = c.`id_category` AND cs.`id_shop` = 1
            LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON cl.`id_category` = c.`id_category` AND cl.`id_shop` = ' . (int) $id_shop . ($root_category ? '
            RIGHT JOIN ' . _DB_PREFIX_ . 'category c2 ON c2.`id_category` = ' . (int) $root_category . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
            WHERE 1' . ($active ? ' AND c.`active` = 1' : '') . ($id_lang ? ' AND `id_lang` = ' . (int) $id_lang : '
            GROUP BY c.`id_category`') . '
            ORDER BY c.`level_depth`, cs.`position`
        ');
        $categories = [];
        $buff = [];
        $root = (array) Category::getRootCategory();
        array_unshift($result, $root);

        if (!isset($root_category)) {
            $root_category = $root['id'];
        }

        foreach ($result as $row) {
            $current = &$buff[$row['id_category']];
            $current = $row;

            if ($row['id_category'] == $root_category) {
                $categories[$row['id_category']] = &$current;
            } else {
                $buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
            }
        }

        return $categories;
    }

    private static function generateCategories(&$cats, &$a, $tabs = '')
    {
        foreach ($cats as &$cat) {
            if (empty($a)) {
                $a[] = ['value' => 'all', 'option' => '- All -'];
                $a[] = ['value' => '', 'option' => '- None -'];
                $a[] = ['value' => 'index', 'option' => $tabs . '▾ ' . $cat['name']];
            } else {
                $a[] = ['value' => 'c-' . $cat['id_category'], 'option' => $tabs . '▾ ' . $cat['name']];
            }

            if (isset($cat['children'])) {
                self::generateCategories($cat['children'], $a, $tabs . self::TAB);
            }
            if (null !== self::$products) {
                $lang = (int) $GLOBALS['language']->id;
                $prods = self::getProducts($lang, $cat['id_category']);
                foreach ($prods as &$prod) {
                    $a[] = ['value' => 'p-' . $prod['id_product'], 'option' => $tabs . self::TAB . '▸ ' . $prod['name']];
                }
            }
        }
    }

    private static function generateCategoryList(&$cats, &$a, $tabs = '')
    {
        foreach ($cats as &$cat) {
            $a[] = (object) ['term_id' => $cat['id_category'], 'name' => $tabs . $cat['name']];

            if (isset($cat['children'])) {
                self::generateCategoryList($cat['children'], $a, $tabs . self::TAB);
            }
        }
    }

    private static function generateCMSCategories(&$e, &$a, $tabs = '')
    {
        if (empty($a)) {
            $a[] = ['value' => 'all', 'option' => '- All -'];
            $a[] = ['value' => '', 'option' => '- None -'];
            if (file_exists(dirname(__FILE__) . '/../../psblog')) {
                $a[] = ['value' => 'psblogpostsmodulefront', 'option' => $tabs . '▸ Blog home'];
            }
            $a[] = ['value' => 'newproducts', 'option' => $tabs . '▸ New products'];
            $a[] = ['value' => 'bestsales', 'option' => $tabs . '▸ Best sellers'];
            $a[] = ['value' => 'pricesdrop', 'option' => $tabs . '▸ Price drop'];
            $a[] = ['value' => 'manufacturer-0', 'option' => $tabs . '▾ Brands'];
            $manufacturers = Manufacturer::getManufacturers();
            foreach ($manufacturers as &$manufacturer) {
                $a[] = ['value' => 'manufacturer-' . $manufacturer['id_manufacturer'], 'option' => self::TAB . $tabs . '▸ ' . $manufacturer['name']];
            }
            $a[] = ['value' => 'supplier', 'option' => $tabs . '▸ Suppliers'];
            $a[] = ['value' => 'cart', 'option' => $tabs . '▾ Cart'];
            $a[] = ['value' => 'order', 'option' => self::TAB . $tabs . '▸ Order'];
            $a[] = ['value' => 'order-confirmation', 'option' => self::TAB . $tabs . '▸ Order confirmation'];
            $a[] = ['value' => 'contact', 'option' => $tabs . '▸ Contact us'];
            $a[] = ['value' => 'search', 'option' => $tabs . '▸ Search'];
            $a[] = ['value' => 'sitemap', 'option' => $tabs . '▸ Sitemap'];
            $a[] = ['value' => 'stores', 'option' => $tabs . '▸ Stores'];
            $a[] = ['value' => 'c-' . $e['id_cms_category'], 'option' => $tabs . '▾ Pages'];
        } else {
            $a[] = ['value' => 'c-' . $e['id_cms_category'], 'option' => $tabs . '▾ ' . $e['name']];
        }

        if (isset($e['children'])) {
            foreach ($e['children'] as &$child) {
                self::generateCMSCategories($child, $a, $tabs . self::TAB);
            }
        }

        foreach ($e['cms'] as &$c) {
            $a[] = ['value' => 'p-' . $c['id_cms'], 'option' => $tabs . self::TAB . '▸ ' . $c['meta_title']];
        }
    }

    private static function generatePBCategories(&$cats, &$news, &$a, $tabs = '')
    {
        foreach ($cats as &$e) {
            $a[] = ['value' => 'bc-' . $e['id'], 'option' => $tabs . '▾ ' . $e['title']];

            if (isset($e['children'])) {
                self::generatePBCategories($e['children'], $news, $a, $tabs . self::TAB);
            }

            foreach ($news as &$n) {
                foreach ($n['categories'] as $cid => &$c) {
                    if ($e['id'] == $cid) {
                        $a[] = ['value' => 'bn-' . $n['id'], 'option' => $tabs . self::TAB . '▸ ' . $n['title']];
                    }
                }
            }
        }
    }

    public static function getCategoryList()
    {
        $opts = [];
        method_exists('Category', 'getNestedCategories')
            && ($cats = Category::getNestedCategories())
            || ($cats = self::getNestedCategories());

        self::generateCategoryList($cats, $opts);

        return $opts;
    }

    public static function getCategories()
    {
        $opts = [];
        method_exists('Category', 'getNestedCategories')
            && ($cats = Category::getNestedCategories())
            || ($cats = self::getNestedCategories());

        $id_shop = $GLOBALS['context']->shop->id;
        $count = (int) Db::getInstance()->getValue(
            'SELECT COUNT(`id_product`) FROM ' . _DB_PREFIX_ . 'product_shop WHERE `active` = 1 AND `id_shop` = ' . (int) $id_shop
        );
        if (!$count || $count > self::PRODUCT_LIMIT) {
            self::$products = null;
        }

        self::generateCategories($cats, $opts);

        return $opts;
    }

    public static function getCMSCategories()
    {
        $opts = [];
        $cats = CMSCategory::getRecurseCategory();
        self::generateCMSCategories($cats, $opts);

        if (class_exists('CategoriesClass')) {
            $opts[] = ['value' => 'bc-0', 'option' => '▾ PrestaBlog'];
            $blogcats = call_user_func(['CategoriesClass', 'getListe'], null, true);
            $blognews = call_user_func(['NewsClass', 'getListe'], null, true, 0, 0, null, 'NULL', 'ASC');
            self::generatePBCategories($blogcats, $blognews, $opts, self::TAB);
        }

        return $opts;
    }

    public static function getTagList()
    {
        $id_lang = $GLOBALS['language']->id;
        $id_shop = $GLOBALS['context']->shop->id;
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

        if (version_compare(_PS_VERSION_, '1.6.1', '<')) {
            return $db->executeS(
                'SELECT `name`, `id_tag` AS term_id FROM ' . _DB_PREFIX_ . 'tag WHERE `id_lang` = ' . (int) $id_lang . ' ORDER BY `name`'
            );
        }
        $groups = Group::isFeatureActive() ? FrontController::getCurrentCustomerGroups() : [0];

        return $db->executeS('
            SELECT t.`name`, t.`id_tag` AS term_id FROM ' . _DB_PREFIX_ . 'tag_count pt
            LEFT JOIN ' . _DB_PREFIX_ . 'tag t ON t.`id_tag` = pt.`id_tag`
            WHERE pt.`id_lang` = ' . (int) $id_lang . ' AND pt.`id_shop` = ' . (int) $id_shop . '
            AND pt.`id_group` ' . ($groups ? 'IN (' . implode(',', array_map('intval', $groups)) . ')' : '= 1') . '
            ORDER BY t.`name` ASC
        ');
    }

    public static function getProductImgTypes()
    {
        $types = ['' => ls__('original size')];

        foreach (ImageType::getImagesTypes('products') as $type) {
            $types[$type['name']] = $type['name'] . ' (' . $type['width'] . ' x ' . $type['height'] . ')';
        }

        return $types;
    }
}
