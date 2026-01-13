<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 */

class AdminProductFeaturesManagerController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        
        $this->module = Module::getInstanceByName('productfeaturesmanager');
        
        parent::__construct();

        if (!$this->module || !$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('updateFeatureValue')) {
            $this->processUpdateFeatureValue();
        } elseif (Tools::isSubmit('bulkUpdateFeatures')) {
            $this->processBulkUpdate();
        } elseif (Tools::isSubmit('exportCSV')) {
            $this->processExportCSV();
        } elseif (Tools::isSubmit('importCSV')) {
            $this->processImportCSV();
        }
        
        parent::postProcess();
    }

    private function processUpdateFeatureValue()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_feature = (int)Tools::getValue('id_feature');
        $id_feature_value = (int)Tools::getValue('id_feature_value');

        if ($id_product && $id_feature) {
            $this->module->updateProductFeature($id_product, $id_feature, $id_feature_value);
            
            if (Tools::isSubmit('ajax')) {
                die(json_encode(['success' => true]));
            }
            
            $this->confirmations[] = $this->l('Característica actualizada correctamente');
        }
    }

    private function processBulkUpdate()
    {
        $updates = Tools::getValue('updates', []);
        $id_feature = (int)Tools::getValue('id_feature');

        foreach ($updates as $id_product => $id_feature_value) {
            $this->module->updateProductFeature((int)$id_product, $id_feature, (int)$id_feature_value);
        }

        $this->confirmations[] = $this->l('Características actualizadas correctamente');
    }

    private function processExportCSV()
    {
        try {
            $id_feature = (int)Tools::getValue('id_feature');
            $id_lang = (int)Tools::getValue('id_lang', $this->context->language->id);
            $search = Tools::getValue('search', ''); // Obtener filtro de búsqueda

            if (!$id_feature) {
                die('Error: No se ha seleccionado una característica');
            }

            // Para exportación, usamos un límite alto pero razonable
            $export_limit = 10000;
            $data = $this->module->getFeatureValuesForProducts($id_feature, $id_lang, $export_limit, 0, $search);
            $languages = Language::getLanguages(true);
            
            // Advertir si hay más productos que el límite
            $total = $this->module->countProductsForFeature($id_feature, $search);
            if ($total > $export_limit) {
                // Log para el usuario (se verá después de la descarga)
                error_log('Advertencia: Se exportaron solo los primeros ' . $export_limit . ' de ' . $total . ' productos');
            }

            // Limpiar cualquier salida previa
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Obtener el nombre de la característica
            $feature = new Feature($id_feature, $id_lang);
            $feature_name = $feature->name ? Tools::str2url($feature->name) : 'caracteristica_' . $id_feature;
            
            // Nombre del archivo según si hay filtro o no
            $filename = $feature_name;
            if (!empty($search)) {
                $filename .= '_filtrado';
            }
            $filename .= '_' . date('Y-m-d') . '.csv';

            // Exportar solo como CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');
            
            if (!$output) {
                die('Error: No se pudo abrir el stream de salida');
            }
            
            // BOM para Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers - Formato simplificado para importación
            $headers = ['ID Producto', 'Referencia'];
            foreach ($languages as $lang) {
                $headers[] = 'Producto (' . $lang['iso_code'] . ')';
            }
            $headers[] = 'ID Valor';
            foreach ($languages as $lang) {
                $headers[] = 'Valor (' . $lang['iso_code'] . ')';
            }
            fputcsv($output, $headers, ';');
            
            // Nota: Para importar, solo se necesitan las columnas: ID Producto (columna 0) e ID Valor

            // Obtener todos los productos con sus nombres en todos los idiomas
            $products_all_langs = [];
            foreach ($data as $row) {
                $id_product = (int)$row['id_product'];
                if ($id_product > 0 && !isset($products_all_langs[$id_product])) {
                    $products_all_langs[$id_product] = [];
                    foreach ($languages as $lang) {
                        try {
                            $product_lang = new Product($id_product, false, $lang['id_lang']);
                            $products_all_langs[$id_product][$lang['id_lang']] = Validate::isLoadedObject($product_lang) ? $product_lang->name : '';
                        } catch (Exception $e) {
                            $products_all_langs[$id_product][$lang['id_lang']] = '';
                        }
                    }
                }
            }

            foreach ($data as $row) {
                $csv_row = [
                    (int)$row['id_product'],
                    $row['reference'] ?: ''
                ];
                
                // Añadir nombres de producto en todos los idiomas
                $id_product = (int)$row['id_product'];
                foreach ($languages as $lang) {
                    $csv_row[] = isset($products_all_langs[$id_product][$lang['id_lang']]) 
                        ? $products_all_langs[$id_product][$lang['id_lang']] 
                        : '';
                }
                
                $csv_row[] = $row['id_feature_value'] ?: '';
                
                // Añadir valores de característica en todos los idiomas
                if ($row['id_feature_value']) {
                    try {
                        $all_lang_values = $this->module->getFeatureValueInAllLanguages($row['id_feature_value']);
                        foreach ($languages as $lang) {
                            $csv_row[] = isset($all_lang_values[$lang['id_lang']]) ? $all_lang_values[$lang['id_lang']]['value'] : '';
                        }
                    } catch (Exception $e) {
                        foreach ($languages as $lang) {
                            $csv_row[] = '';
                        }
                    }
                } else {
                    foreach ($languages as $lang) {
                        $csv_row[] = '';
                    }
                }
                
                fputcsv($output, $csv_row, ';');
            }

            fclose($output);
            exit;
        } catch (Exception $e) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            die('Error al exportar: ' . $e->getMessage());
        }
    }

    private function processImportCSV()
    {
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->l('Error al subir el archivo CSV');
            return;
        }

        $id_feature = (int)Tools::getValue('id_feature');
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');

        if (!$handle) {
            $this->errors[] = $this->l('No se pudo leer el archivo');
            return;
        }

        // Leer y validar header
        $header = fgetcsv($handle, 1000, ';');
        if (!$header || count($header) < 2) {
            $this->errors[] = $this->l('Formato de archivo inválido. Debe contener al menos: ID Producto, ID Valor');
            fclose($handle);
            return;
        }
        
        // Limpiar header - eliminar comillas y espacios
        $header = array_map(function($col) {
            return trim(strtolower(trim($col, " \t\n\r\0\x0B\"'")));
        }, $header);
        
        // Detectar índices de columnas
        $id_product_index = null;
        $id_value_index = null;
        
        foreach ($header as $index => $col) {
            $col_clean = trim(strtolower($col));
            // Buscar columna de ID Producto
            if ($id_product_index === null && (
                stripos($col_clean, 'id producto') !== false || 
                stripos($col_clean, 'id_producto') !== false ||
                stripos($col_clean, 'id product') !== false ||
                ($index == 0 && (stripos($col_clean, 'id') !== false || stripos($col_clean, 'producto') !== false))
            )) {
                $id_product_index = $index;
            }
            // Buscar columna de ID Valor
            if ($id_value_index === null && (
                stripos($col_clean, 'id valor') !== false || 
                stripos($col_clean, 'id_valor') !== false ||
                stripos($col_clean, 'id value') !== false ||
                stripos($col_clean, 'id_feature_value') !== false
            )) {
                $id_value_index = $index;
            }
        }
        
        // Si no se encontraron, usar posiciones por defecto
        if ($id_product_index === null) {
            $id_product_index = 0; // Primera columna
        }
        
        if ($id_value_index === null) {
            // Buscar después de las columnas de producto (ID, Referencia, Nombres en idiomas)
            $languages = Language::getLanguages(true);
            $id_value_index = 2 + count($languages); // Después de ID, Referencia y nombres
            // Si no hay suficientes columnas, buscar la última columna numérica
            if ($id_value_index >= count($header)) {
                // Buscar la última columna que parezca un ID
                for ($i = count($header) - 1; $i >= 0; $i--) {
                    $col_test = trim(strtolower($header[$i]));
                    if (stripos($col_test, 'id') !== false && stripos($col_test, 'valor') !== false) {
                        $id_value_index = $i;
                        break;
                    }
                }
                // Si aún no se encontró, buscar cualquier columna con "id"
                if ($id_value_index === null) {
                    for ($i = count($header) - 1; $i >= 0; $i--) {
                        if (stripos($header[$i], 'id') !== false) {
                            $id_value_index = $i;
                            break;
                        }
                    }
                }
                if ($id_value_index === null) {
                    $id_value_index = count($header) - 1; // Última columna
                }
            }
        }

        $imported = 0;
        $errors = 0;
        $error_details = [];
        $line_number = 1; // Empezar en 1 porque ya leímos el header

        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            $line_number++;
            
            // Limpiar datos - eliminar comillas y espacios
            $data = array_map(function($val) {
                return trim($val, " \t\n\r\0\x0B\"'");
            }, $data);
            
            // Saltar líneas vacías o con muy pocas columnas
            if (count($data) < 2) {
                continue;
            }
            
            // Obtener ID de producto de la columna correcta
            $id_product_raw = isset($data[$id_product_index]) ? trim($data[$id_product_index]) : '';
            // Si está vacío o solo tiene caracteres no numéricos, saltar
            if (empty($id_product_raw) || !preg_match('/\d/', $id_product_raw)) {
                $errors++;
                $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('ID de producto vacío o inválido');
                continue;
            }
            // Extraer solo números
            $id_product_raw = preg_replace('/[^0-9]/', '', $id_product_raw);
            $id_product = (int)$id_product_raw;
            
            // Obtener ID de valor de característica
            $id_feature_value_raw = isset($data[$id_value_index]) ? trim($data[$id_value_index]) : '';
            // Extraer solo números si existe
            if (!empty($id_feature_value_raw) && preg_match('/\d/', $id_feature_value_raw)) {
                $id_feature_value_raw = preg_replace('/[^0-9]/', '', $id_feature_value_raw);
                $id_feature_value = (int)$id_feature_value_raw;
            } else {
                $id_feature_value = 0; // Valor vacío significa eliminar la característica
            }

            // Validar ID de producto
            if ($id_product <= 0) {
                $errors++;
                $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('ID de producto inválido') . ' (' . ($id_product_raw ?: 'vacío') . ')';
                continue;
            }

            // Validar que el producto existe
            $product = new Product($id_product);
            if (!Validate::isLoadedObject($product)) {
                $errors++;
                $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('Producto ID') . ' ' . $id_product . ' ' . $this->l('no existe');
                continue;
            }

            // Validar que el valor de característica existe (si se proporciona)
            if ($id_feature_value > 0) {
                $feature_value = new FeatureValue($id_feature_value);
                if (!Validate::isLoadedObject($feature_value)) {
                    $errors++;
                    $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('Producto ID') . ' ' . $id_product . ' - ' . $this->l('Valor de característica ID') . ' ' . $id_feature_value . ' ' . $this->l('no existe');
                    continue;
                }
                if ($feature_value->id_feature != $id_feature) {
                    $errors++;
                    $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('Producto ID') . ' ' . $id_product . ' - ' . $this->l('El valor de característica no pertenece a esta característica');
                    continue;
                }
            }

            // Intentar actualizar
            if ($this->module->updateProductFeature($id_product, $id_feature, $id_feature_value)) {
                $imported++;
            } else {
                $errors++;
                $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('Producto ID') . ' ' . $id_product . ' - ' . $this->l('Error al actualizar en la base de datos');
            }
        }

        fclose($handle);

        // Mostrar resultados
        if ($imported > 0) {
            $this->confirmations[] = $this->l('Importados') . ' ' . $imported . ' ' . $this->l('productos correctamente');
        }
        if ($errors > 0) {
            $this->errors[] = $this->l('Errores en') . ' ' . $errors . ' ' . $this->l('productos de') . ' ' . ($imported + $errors) . ' ' . $this->l('total');
            
            // Mostrar hasta 20 errores detallados
            $max_errors_to_show = 20;
            $errors_to_show = array_slice($error_details, 0, $max_errors_to_show);
            foreach ($errors_to_show as $detail) {
                $this->errors[] = $detail;
            }
            if (count($error_details) > $max_errors_to_show) {
                $this->errors[] = $this->l('... y') . ' ' . (count($error_details) - $max_errors_to_show) . ' ' . $this->l('errores más');
            }
        }
    }

    public function renderView()
    {
        $module = Module::getInstanceByName('productfeaturesmanager');
        
        if (!$module || !$module->id) {
            return $this->l('Módulo no encontrado');
        }
        
        // Obtener características
        $features = [];
        try {
            $features = $module->getAllFeatures();
        } catch (Exception $e) {
            $this->errors[] = $this->l('Error al obtener características: ') . $e->getMessage();
        }
        
        // Obtener idiomas
        $languages = Language::getLanguages(true);
        
        // Obtener característica seleccionada
        $selected_feature = (int)Tools::getValue('id_feature', 0);
        $selected_lang = (int)Tools::getValue('id_lang', $this->context->language->id);
        
        // Paginación y búsqueda
        $page = (int)Tools::getValue('page', 1);
        $per_page = (int)Tools::getValue('per_page', 100);
        $search = Tools::getValue('search', '');
        
        if ($page < 1) $page = 1;
        if ($per_page < 10) $per_page = 10;
        if ($per_page > 500) $per_page = 500;
        
        $offset = ($page - 1) * $per_page;
        
        $products_data = [];
        $feature_values = [];
        $total_products = 0;
        $total_pages = 0;
        
        if ($selected_feature > 0) {
            try {
                $products_data = $module->getFeatureValuesForProducts($selected_feature, $selected_lang, $per_page, $offset, $search);
                $feature_values = $module->getFeatureValues($selected_feature, $selected_lang);
                $total_products = $module->countProductsForFeature($selected_feature, $search);
                $total_pages = ceil($total_products / $per_page);
            } catch (Exception $e) {
                $this->errors[] = $this->l('Error al obtener datos: ') . $e->getMessage();
            }
        }

        $this->context->smarty->assign([
            'features' => $features ?: [],
            'languages' => $languages ?: [],
            'selected_feature' => $selected_feature,
            'selected_lang' => $selected_lang,
            'products_data' => $products_data ?: [],
            'feature_values' => $feature_values ?: [],
            'module_dir' => __PS_BASE_URI__ . 'modules/' . $module->name . '/',
            'page' => $page,
            'per_page' => $per_page,
            'total_products' => $total_products,
            'total_pages' => $total_pages,
            'search' => $search,
        ]);

        $template_path = _PS_MODULE_DIR_ . $module->name . '/views/templates/admin/manager.tpl';
        
        if (!file_exists($template_path)) {
            return $this->l('Template no encontrado: ') . $template_path;
        }
        
        return $this->context->smarty->fetch($template_path);
    }
}

