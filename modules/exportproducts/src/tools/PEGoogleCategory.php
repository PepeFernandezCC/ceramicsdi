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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PECategoryTreeGenerator.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PESerializationChecker.php';

class PEGoogleCategory
{
    public static function getGoogleCategoryOptions($id_category, $saved_option_id = false)
    {
        $all_google_category_options = self::getCategoriesFromResourceFile();
        $valid_google_category_options = [];
        $default_options = [];

        $category = new Category($id_category, Context::getContext()->language->id);
        $all_google_category_options = explode(PHP_EOL, $all_google_category_options);

        if (empty($all_google_category_options)) {
            throw new Exception(Module::getInstanceByName('exportproducts')->l('Can\'t retrieve google category options!',__CLASS__));
        }

        foreach ($all_google_category_options as $key => $google_category_option) {
            if ($key == 0 || empty($google_category_option)) {
                continue;
            }

            $option = explode('-', $google_category_option, 2);

            $id_option = trim($option[0]);
            $option_category_tree = trim($option[1]);
            $exploded_option_category_tree = explode('>', $option_category_tree);

            $google_category_name = '';
            if (count($exploded_option_category_tree) == 1) {
                $google_category_name = $exploded_option_category_tree[0];
            } else if (count($exploded_option_category_tree) > 1) {
                $google_category_name = end($exploded_option_category_tree);
            }

            if (strpos($google_category_name, $category->name) !== false) {
                $valid_google_category_options[$id_option] = [
                    'id' => $id_option,
                    'title' => $option_category_tree
                ];
            }

            if ($key < 100) {
                $default_options[$id_option] = [
                    'id' => $id_option,
                    'title' => $option_category_tree
                ];
            }

            if ($saved_option_id && $saved_option_id == $id_option && !isset($valid_google_category_options[$id_option])) {
                $valid_google_category_options[$id_option] = [
                    'id' => $id_option,
                    'title' => $option_category_tree
                ];
            }
        }

        if (empty($valid_google_category_options)) {
            return $default_options;
        }

        return $valid_google_category_options;
    }

    public static function searchGoogleCategory($search_query)
    {
        $valid_google_category_options = [];

        if (!$search_query) {
            return $valid_google_category_options;
        }

        $all_google_category_options = self::getCategoriesFromResourceFile();
        $all_google_category_options = explode(PHP_EOL, $all_google_category_options);

        if (empty($all_google_category_options)) {
            throw new Exception(Module::getInstanceByName('exportproducts')->l('Can\'t retrieve google category options!',__CLASS__));
        }

        foreach ($all_google_category_options as $key => $google_category_option) {
            if ($key == 0 || empty($google_category_option)) {
                continue;
            }

            $option = explode('-', $google_category_option, 2);

            $id_option = trim($option[0]);
            $option_category_tree = trim($option[1]);

            if (strpos(Tools::strtolower($option_category_tree), Tools::strtolower($search_query)) === false) {
                continue;
            }

            $valid_google_category_options[] = [
                'id' => $id_option,
                'title' => $option_category_tree
            ];
        }

        return $valid_google_category_options;
    }

    public static function getNameById($id_google_category)
    {
        $all_google_category_options = self::getCategoriesFromResourceFile();
        $all_google_category_options = explode(PHP_EOL, $all_google_category_options);

        if (empty($all_google_category_options)) {
            return false;
        }

        foreach ($all_google_category_options as $key => $google_category_option) {
            if ($key == 0 || empty($google_category_option)) {
                continue;
            }

            $option = explode('-', $google_category_option, 2);

            $id_option = trim($option[0]);
            $option_category_tree = trim($option[1]);

            if ($id_option == $id_google_category) {
                return $option_category_tree;
            }
        }

        return false;
    }

    private static function getCategoriesFromResourceFile()
    {
        $path_to_google_categories_file = _PS_MODULE_DIR_ . 'exportproducts/resources/google_categories.txt';
        return Tools::file_get_contents($path_to_google_categories_file);
    }

    public static function getSavedGoogleCategoriesTplData($configuration)
    {
        $google_categories_tpl_data = [];

        if (PESerializationChecker::isStringSerialized($configuration['google_categories'])) {
            $google_categories = Tools::unSerialize($configuration['google_categories']);
        } else {
            $google_categories = json_decode($configuration['google_categories'], true);
        }

        if (empty($google_categories)) {
            return $google_categories_tpl_data;
        }

        foreach ($google_categories as $shop_category_id => $google_category_id) {
            $google_categories_tpl_data[] = [
                'shop_category_id' => $shop_category_id,
                'shop_category_name' => self::generateShopCategoryTree($shop_category_id),
                'selected_google_category_id' => $google_category_id,
                'google_category_options' => PEGoogleCategory::getGoogleCategoryOptions($shop_category_id, $google_category_id)
            ];
        }

        return $google_categories_tpl_data;
    }

    public static function getShopCategoriesLinkedToGoogleCategories($configuration)
    {
        $shop_categories_linked_to_google_categories = [];

        if (PESerializationChecker::isStringSerialized($configuration['google_categories'])) {
            $google_categories = Tools::unSerialize($configuration['google_categories']);
        } else {
            $google_categories = json_decode($configuration['google_categories'], true);
        }

        if (empty($google_categories)) {
            return $shop_categories_linked_to_google_categories;
        }

        foreach ($google_categories as $shop_category_id => $google_category_id) {
            $shop_categories_linked_to_google_categories[] = $shop_category_id;
        }

        return $shop_categories_linked_to_google_categories;
    }

    public static function getGoogleCategoryAssocBlockTpl($id_category)
    {
        $category = new Category($id_category, Context::getContext()->language->id);

        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/google_category_assoc.tpl');
        $tpl->assign([
            'shop_category_id' => $category->id,
            'shop_category_name' => self::generateShopCategoryTree($category->id),
            'google_category_options' => PEGoogleCategory::getGoogleCategoryOptions($id_category),
            'img_folder' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/exportproducts/views/img/'
        ]);

        return $tpl->fetch();
    }

    private static function generateShopCategoryTree($id_category)
    {
        $category_tree_generator = new PECategoryTreeGenerator(Context::getContext()->language->id);
        $tree = $category_tree_generator->getCategoryTree($id_category);
        return implode(' > ', $tree);
    }
}