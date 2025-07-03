<?php

class Category extends CategoryCore {

    public function getSubCategoriesArray($idCategory, $idLang) {
        
        $sql = '
        SELECT c.`id_category`, cl.`name`, c.id_parent 
        FROM `' . _DB_PREFIX_ . 'category` c JOIN `' . _DB_PREFIX_ . 'category_lang` cl 
        WHERE cl.`id_category` = c.`id_category` 
        AND cl.`id_lang` = ' . (int) $idLang . ' 
        AND c.id_parent = '. (int) $idCategory . ' 
        GROUP BY c.id_category 
        ORDER BY c.`id_category`';
        
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;

    }

    public function getPopularCategoriesArray($idLang) {
        
        $sql = '
        SELECT c.`id_category`, cl.`name`, c.id_parent 
        FROM `' . _DB_PREFIX_ . 'category` c JOIN `' . _DB_PREFIX_ . 'category_lang` cl 
        WHERE cl.`id_category` = c.`id_category` 
        AND cl.`id_lang` = ' . (int) $idLang . ' 
        AND c.id_category IN (41, 20, 4, 25, 12, 13) 
        GROUP BY c.id_category 
        ORDER BY c.`id_category`';
        
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;

    }


    public function categoryProductsCountById($idCategory) {

        $sql = '
        SELECT COUNT(`id_category`) 
        FROM `' . _DB_PREFIX_ . 'category_product`
        WHERE `id_category` = '. (int) $idCategory . ' 
        ';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        return (int) $result;
          
    }
    public function getCategoryMinPriceById($idCategory) {

        $query = '
        SELECT MIN(CAST(REPLACE(SUBSTRING_INDEX(value, " ", 1), ",", ".") AS DECIMAL(10, 2))) AS precio_min
        FROM ps_category_product AS cp
        INNER JOIN ps_feature_product AS fp ON cp.id_product = fp.id_product
        INNER JOIN ps_feature_value_lang AS fvl ON fp.id_feature_value = fvl.id_feature_value
        WHERE cp.id_category = ' . $idCategory . '
          AND fvl.id_lang = 1
          AND fp.id_feature = 44
        ';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        return $result;

    }

    public function getCategoryMaxPriceById($idCategory) {

        $query = '
        SELECT MAX(CAST(REPLACE(SUBSTRING_INDEX(value, " ", 1), ",", ".") AS DECIMAL(10, 2))) AS precio_min
        FROM ps_category_product AS cp
        INNER JOIN ps_feature_product AS fp ON cp.id_product = fp.id_product
        INNER JOIN ps_feature_value_lang AS fvl ON fp.id_feature_value = fvl.id_feature_value
        WHERE cp.id_category = ' . $idCategory . '
          AND fvl.id_lang = 1
          AND fp.id_feature = 44
        ';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        return $result;

    }

    public function getSchemaCategoryData($idCategory) {

        $productsCount = Category::categoryProductsCountById($idCategory);
        $minPrice = Category::getCategoryMinPriceById($idCategory);
        $maxPrice = Category::getCategoryMaxPriceById($idCategory);

        return [
            "total_items" => $productsCount,
            "min_price" => $minPrice,
            "max_price" => $maxPrice
        ];

    }
    
}