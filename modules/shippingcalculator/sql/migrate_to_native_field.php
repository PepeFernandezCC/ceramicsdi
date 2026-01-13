<?php
/**
 * Script de migración para eliminar la tabla personalizada
 * y migrar los datos al campo nativo delivery_in_stock
 * 
 * NOTA: Este script se ejecuta automáticamente cuando se actualiza el módulo
 * o puede ejecutarse manualmente desde el controlador admin
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Migrar datos de la tabla personalizada al campo nativo de PrestaShop
 */
function migrateToNativeDeliveryField()
{
    $db = Db::getInstance();
    
    // Verificar si existe la tabla antigua
    $table_exists = $db->getValue('
        SELECT COUNT(*) 
        FROM information_schema.tables 
        WHERE table_schema = DATABASE() 
        AND table_name = "' . _DB_PREFIX_ . 'shipping_calculator_product_preparation"
    ');
    
    if (!$table_exists) {
        // La tabla no existe, no hay nada que migrar
        return true;
    }
    
    // Obtener todos los productos con días de preparación configurados
    $products = $db->executeS('
        SELECT id_product, preparation_days 
        FROM `' . _DB_PREFIX_ . 'shipping_calculator_product_preparation`
        WHERE preparation_days > 0
    ');
    
    if (!empty($products)) {
        // Obtener todos los idiomas y tiendas
        $languages = Language::getLanguages(true);
        $shops = Shop::getShops(true);
        
        foreach ($products as $product) {
            $id_product = (int)$product['id_product'];
            $days = (int)$product['preparation_days'];
            
            // Preparar el texto de entrega
            $delivery_text_es = $days . ' días';
            $delivery_text_en = $days . ' days';
            $delivery_text_fr = $days . ' jours';
            $delivery_text_de = $days . ' Tage';
            $delivery_text_pt = $days . ' dias';
            
            // Actualizar para cada idioma y tienda
            foreach ($shops as $shop) {
                foreach ($languages as $lang) {
                    $id_lang = (int)$lang['id_lang'];
                    $id_shop = (int)$shop['id_shop'];
                    
                    // Determinar el texto según el idioma
                    $delivery_text = $delivery_text_es; // Por defecto español
                    if (isset($lang['iso_code'])) {
                        switch (strtolower($lang['iso_code'])) {
                            case 'en':
                                $delivery_text = $delivery_text_en;
                                break;
                            case 'fr':
                                $delivery_text = $delivery_text_fr;
                                break;
                            case 'de':
                                $delivery_text = $delivery_text_de;
                                break;
                            case 'pt':
                                $delivery_text = $delivery_text_pt;
                                break;
                        }
                    }
                    
                    // Verificar si ya existe un valor en delivery_in_stock
                    $existing = $db->getValue('
                        SELECT delivery_in_stock 
                        FROM `' . _DB_PREFIX_ . 'product_lang`
                        WHERE id_product = ' . $id_product . '
                        AND id_lang = ' . $id_lang . '
                        AND id_shop = ' . $id_shop . '
                    ');
                    
                    // Solo actualizar si está vacío
                    if (empty($existing)) {
                        $db->update(
                            'product_lang',
                            ['delivery_in_stock' => pSQL($delivery_text)],
                            'id_product = ' . $id_product . ' 
                             AND id_lang = ' . $id_lang . ' 
                             AND id_shop = ' . $id_shop
                        );
                    }
                }
            }
            
            // Configurar el producto para usar plazos específicos (additional_delivery_times = 2)
            // Solo si no está ya configurado
            $current_mode = $db->getValue('
                SELECT additional_delivery_times 
                FROM `' . _DB_PREFIX_ . 'product`
                WHERE id_product = ' . $id_product
            );
            
            // Si está en 0 o NULL, cambiar a 2 (usar plazo específico del producto)
            if ($current_mode == 0 || $current_mode === null) {
                $db->update(
                    'product',
                    ['additional_delivery_times' => 2],
                    'id_product = ' . $id_product
                );
            }
        }
    }
    
    // Eliminar la tabla antigua
    $db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'shipping_calculator_product_preparation`');
    
    return true;
}

// Ejecutar migración si se llama directamente
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    require_once(dirname(__FILE__) . '/../../../config/config.inc.php');
    
    if (migrateToNativeDeliveryField()) {
        echo "Migración completada exitosamente.\n";
    } else {
        echo "Error durante la migración.\n";
    }
}

