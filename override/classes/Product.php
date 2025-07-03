<?php

class Product extends ProductCore {



    public static function getImageByPosition($position, $id_product) {

        $id_image = Db::getInstance()->getValue(
            'SELECT `id_image`
             FROM `' . _DB_PREFIX_ . 'image`
             WHERE `position` = ' . (int)$position . ' AND `id_product` = ' . (int)$id_product
        );
        
       // $id_image = $id_image[0]['id_image'];

        $image_url = 'no-hay-cover';

        if ((int)$id_image > 0) {
            $image = new Image($id_image);
            $image_url = _PS_BASE_URL_SSL_._THEME_PROD_DIR_.$image->getExistingImgPath().".jpg";
        }

        return $image_url;

    }
     
    public static function getFrontFeaturesStatic($id_lang, $id_product)
    {
        if (!Feature::isFeatureActive()) {
            return [];
        }
        if (!array_key_exists($id_product . '-' . $id_lang, self::$_frontFeaturesCache)) {
            self::$_frontFeaturesCache[$id_product . '-' . $id_lang] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                '
                SELECT name, fvl.id_feature_value, value, pf.id_feature, f.position
                FROM ' . _DB_PREFIX_ . 'feature_product pf
                LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = ' . (int) $id_lang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = ' . (int) $id_lang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = ' . (int) $id_lang . ')
                ' . Shop::addSqlAssociation('feature', 'f') . '
                WHERE pf.id_product = ' . (int) $id_product . '
                ORDER BY f.position ASC'
            );
        }

        return self::$_frontFeaturesCache[$id_product . '-' . $id_lang];
    }

    public static function videoProductRoute($reference, $description) {

        $description = strip_tags($description);
        
        $collection_array = [
            'CEMENT_COLORS_4KG',
            'COLECCION_BARROS_ESMALTADOS',
            'COLECCION_BARROS_GRISES',
            'COLECCION_BARROS_NATURALES',
            'COLECCION_BARROS_TINTADOS',
            'COLECCION_CRETA_25',
            'COLECCION_CRETA_50',
            'COLECCION_CRETA_25',
            'COLECCION_PICKET_25',
            'COLECCION_CRETA_13_BRILLO',
            'COLECCION_MAPEI_ULTRACOLOR',
            'COLECCION_FUGABELLA_COLOR'
        ];

        if ($description == $reference || in_array($description, $collection_array)) {
            $mp4Path = '/themes/child_classic/assets/video/product/'.$description.'.mp4';
            $webmPath = '/themes/child_classic/assets/video/product/webm/'.$description.'.webm';

            if (file_exists(_PS_ROOT_DIR_ . $webmPath)) {
                return [
                        'webm'=>true, 
                        'sourceWebm' => $webmPath,
                        'sourceMp4' => $mp4Path,
                        'typeWebm' => 'video/webm',
                        'typeMp4' => 'video/mp4'
                    ];
            }elseif (file_exists(_PS_ROOT_DIR_ . $mp4Path)) {
                return [
                        'webm'=> false,
                        'sourceMp4' => $mp4Path, 
                        'typeMp4' => 'video/mp4'
                    ];
            }
        }

        return false;
    }

    public static function getPriceWebIfExists($idProduct) {
        $query = 'SELECT CAST(REPLACE(SUBSTRING_INDEX(value, " ", 1), ",", ".") AS DECIMAL(10, 2)) as priceWeb
                FROM ' . _DB_PREFIX_ . 'feature_product pf
                LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = 1)
                LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = 1)
                LEFT JOIN ' . _DB_PREFIX_ . 'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = 1)
                WHERE pf.id_product = ' . (int) $idProduct . '
                AND pf.id_feature = 44
                ORDER BY f.position ASC';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        
        // Devuelve false si no hay resultado o si el resultado es nulo
        return $result !== false && $result !== null ? $result : false;
    }

    /**
     * Obtener todos los productos agrupados por colección
     */
    public static function getAllProductsGroupedByCollection()
    {
        $featureId = 57; // ID de la característica 'colección'
        $db = Db::getInstance();

        // Construir la consulta SQL para obtener todos los productos agrupados por colección
        $query = 'SELECT 
                    fp.id_feature_value, 
                    fvl.value,
                    p.id_product, 
                    p.reference, 
                    p.price, 
                    pl.name,
                    uc.active,
                    uc.position
                    FROM ' . _DB_PREFIX_ . 'feature_product fp
                    INNER JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON fvl.id_feature_value = fp.id_feature_value
                    INNER JOIN ' . _DB_PREFIX_ . 'product p ON fp.id_product = p.id_product
                    INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product
                    LEFT JOIN ' . _DB_PREFIX_ . 'updatecatalog uc ON fp.id_feature_value = uc.id
                    WHERE fp.id_feature = ' . (int)$featureId . '
                    AND pl.id_lang = 1
                    AND fvl.id_lang = 1
                    AND p.active = 1
                    AND uc.active = 1  
                    ORDER BY uc.position';

        // Ejecutar la consulta y devolver los resultados
        $products = $db->executeS($query);

        // Devolver los productos si existen, si no devolver un array vacío
        return $products !== false ? $products : [];
    }

    public static function getProductAttribute($productId, $attributeId, $language = 1) {

        $db = Db::getInstance();

        // selecciona el valor de los metros cuadrados por caja
        $query =   'SELECT fl.value 
                    FROM ps_feature_product fp 
                    INNER JOIN ps_feature f ON f.id_feature = fp.id_feature 
                    INNER JOIN ps_feature_value_lang fl ON fl.id_feature_value = fp.id_feature_value 
                    WHERE fp.id_product = '.(int)$productId.' 
                    AND f.id_feature = '.(int)$attributeId.' 
                    AND fl.id_lang = '.(int)$language.';';

        $result = $db->getRow($query);

        if ($result && isset($result['value'])) {
            return  ($result['value']);
        }
                
        return false; // Retorna false si no hay resultado
    }

    private static function getM2CajaValue($productId) {

        $m2_caja_id = 17; // ID de la característica 'colección'
        $db = Db::getInstance();

        // selecciona el valor de los metros cuadrados por caja
        $query =   'SELECT fl.value 
                    FROM ps_feature_product fp 
                    INNER JOIN ps_feature f ON f.id_feature = fp.id_feature 
                    INNER JOIN ps_feature_value_lang fl ON fl.id_feature_value = fp.id_feature_value 
                    WHERE fp.id_product = '.(int)$productId.' 
                    AND f.id_feature = '.(int)$m2_caja_id.' 
                    AND fl.id_lang = 1;';

        $result = $db->getRow($query);

        if ($result && isset($result['value'])) {
            // Reemplazar coma por punto y convertir a float
            $value = str_replace(',', '.', $result['value']);
            return (float) $value;
        }
                
        return false; // Retorna false si no hay resultado

    }

    private static function getTipologyString(bool $pieceTypology) {

        $tipologia = '/m<sup>2</sup>';

        if ($pieceTypology) {
            $tipologia = '/piece';
        }

        return $tipologia;

    }

    private static function getTipology(int $productId) {

        $tipology = 16; // ID de la característica 'colección'
        $piece_tipology = 74;
        $db = Db::getInstance();

        // selecciona el valor de los metros cuadrados por caja
        $query =   'SELECT fl.id_feature_value 
                    FROM ps_feature_product fp 
                    INNER JOIN ps_feature f ON f.id_feature = fp.id_feature 
                    INNER JOIN ps_feature_value_lang fl ON fl.id_feature_value = fp.id_feature_value 
                    WHERE fp.id_product = '.(int)$productId.' 
                    AND f.id_feature = '.(int)$tipology.' 
                    AND fl.id_lang = 1;';

        $result = $db->getRow($query);

        if ($result && isset($result['id_feature_value'])) {
            if ($piece_tipology == (int)$result['id_feature_value'])
            return true;
        }
                
        return false; // Retorna false si no hay resultado

    }

    private static function getPriceWithDiscount($productId, $price) {

        $db = Db::getInstance();

        $query = 'SELECT `reduction` FROM `ps_specific_price` WHERE `id_product` = ' . $productId;

        $result = $db->getRow($query);

        if ($result && isset($result['reduction'])) {
            // Reemplazar coma por punto y convertir a float
            $value = str_replace(',', '.', $result['reduction']);
            $reduction = 1 - (float) $value;
        }else{
            return $price;
        }
                
        return $price * $reduction; 
    }

    public static function calculateCustomPrice($productId, $iva): array
    {
        
        $product = new Product($productId);
        $price = (float) $product->price;

        if ($iva) {
            $price *= 1.21;
        }
       
        // Calcular precio
        if (self::getIfNormalSell($productId)) {
            $price = self::getPriceWithDiscount($productId, $price);
            return [
                'price' => number_format($price, 2, ',', ''),
                'tipologia' => ''
            ];
        } 
        
        $pieceTypology = self::getTipology($productId);
       
        if (!$pieceTypology) {
            $m2Caja = self::getM2CajaValue($productId);
            $price = $price / $m2Caja;
        } 

        $price = self::getPriceWithDiscount($productId, $price);
            
        return [
            'price' => number_format($price, 2, ',', ''),
            'tipologia' => self::getTipologyString($pieceTypology)
        ];
        
    }

    private static function getIfNormalSell(int $productId) {

        $categoriasProducto = self::getProductCategories($productId);
        
        // Determinar si es una venta normal
        $CATEGORY_INSTALACION_ID = '36';
        $CATEGORY_MANTENIMIENTO_ID = '67';
        $CATEGORY_ARTICULATIONS = '94';
        $normalSell = in_array($CATEGORY_INSTALACION_ID, $categoriasProducto) ||
                      in_array($CATEGORY_MANTENIMIENTO_ID, $categoriasProducto) ||
                      in_array($CATEGORY_ARTICULATIONS, $categoriasProducto);

        return $normalSell;

    }

    //GET TAXONOMY STRING
    public static function getM2CajaOrFalse(int $productId) {

        // Calcular precio
        if (self::getIfNormalSell($productId)) {
            return false;
        } 
        
        $pieceTypology = self::getTipology($productId);
       
        if ($pieceTypology) {
            return false;  
        } 

        return self::getM2CajaValue($productId);
        
    }

    /**
     * Check if product attribute is sample 
     */
    public static function isSample($productAttributeId) {

        if ((int)$productAttributeId != 0 ) {

            $sql = "SELECT `default_on` FROM `ps_product_attribute` WHERE `id_product_attribute` = ". (int)$productAttributeId. "";
            $result = Db::getInstance()->getValue($sql);

            if ($result === NULL || $result == 'null') {
                return true;
            }else{
                return false;
            }

        }

        return false;
    }

}