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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/product/PEProductDataProvider.php';

class PECategoryTreeGenerator
{
    private $parent_categories;
    private $category_trees_count;

    private $id_lang;

    public function __construct($id_lang)
    {
        $this->id_lang = $id_lang;
        $this->parent_categories = [];
    }

    public function setMaxNumberOfTreesInOneProduct($product_ids)
    {
        foreach ($product_ids as $product_id_container) {
            $this->parent_categories = [];
            $product_categories = PEProductDataProvider::getProductCategoryIds($product_id_container['id_product']);

            $current_count = 0;
            foreach ($product_categories as $category_id_container) {
                if ($this->getCategoryTree($category_id_container['id'])) {
                    $current_count++;
                }
            }

            if ($this->category_trees_count < $current_count) {
                $this->category_trees_count = $current_count;
                \Configuration::updateGlobalValue('MPM_PE_MAX_CATEGORY_TREE_COUNT', $this->category_trees_count);
            }
        }
    }

    public function getMaxNumberOfTreesInOneProduct()
    {
        return \Configuration::get('MPM_PE_MAX_CATEGORY_TREE_COUNT');
    }

    public function getCategoryTree($id_category, $level = [])
    {
        $category = new \Category($id_category, $this->id_lang);

        if (in_array($id_category, $this->parent_categories) && !$level) {
            return false;
        }

        if ($level) {
            $this->parent_categories[] = $id_category;
        }

        if ($category->id_parent) {
            $level[] = $category->name;
            return $this->getCategoryTree($category->id_parent, $level);
        }

        return array_reverse($level);
    }
}