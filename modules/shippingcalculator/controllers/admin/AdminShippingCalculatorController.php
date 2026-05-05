<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 */

class AdminShippingCalculatorController extends ModuleAdminController
{
    protected $calculation_result = null;
    protected $selected_products = [];
    protected $selected_province = '';
    protected $selected_country_id = null;
    protected $selected_postal_code = null;
    protected $product_quantities = [];
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        
        $this->module = Module::getInstanceByName('shippingcalculator');
        
        parent::__construct();

        if (!$this->module || !$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function initContent()
    {
        parent::initContent();
        // El contenido se renderiza en renderView()
    }


    private function processCalculation()
    {
        $selected_products = Tools::getValue('selected_products', []);
        $province_code = Tools::getValue('province_code');
        $postal_code = Tools::getValue('postal_code') != '' ? Tools::getValue('postal_code') : '1000';
        $country_id = (int)Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT'));

        if (empty($selected_products) || empty($province_code)) {
            $this->errors[] = $this->l('Por favor, selecciona productos, un país y una provincia');
            return;
        }
        
        if (!$country_id || $country_id <= 0) {
            $this->errors[] = $this->l('Por favor, selecciona un país válido');
            return;
        }

        $module = Module::getInstanceByName('shippingcalculator');
        
        // Obtener cantidades de productos y guardarlas para mantenerlas después del POST
        $product_quantities = Tools::getValue('product_quantities', []);
        $this->product_quantities = $product_quantities; // Guardar para el template
        
        // Crear un carrito temporal para el cálculo (igual que getDeliveryPrice.php)
        $temp_cart = new Cart();
        $temp_cart->id_currency = $this->context->currency->id;
        $temp_cart->id_lang = $this->context->language->id;
        $temp_cart->id_shop = $this->context->shop->id;
        $temp_cart->id_customer = 2; // Cliente temporal (igual que getDeliveryPrice.php)
        $temp_cart->add();
        
        // Añadir productos al carrito temporal con sus cantidades
        foreach ($selected_products as $id_product) {
            $quantity = isset($product_quantities[$id_product]) ? (int)$product_quantities[$id_product] : 1;
            if ($quantity < 1) {
                $quantity = 1;
            }
            $temp_cart->updateQty($quantity, (int)$id_product);
        }
        
        // Guardar carrito después de agregar productos (necesario para que getTotalWeight funcione)
        $temp_cart->update();
        
        // Verificar que el país existe y está activo
        $country = new Country($country_id);
        if (!Validate::isLoadedObject($country)) {
            $this->errors[] = $this->l('El país seleccionado no existe');
            $temp_cart->delete();
            return;
        }
        
        if (!$country->active) {
            //$country_name = $country->name[$this->context->language->id] ?? $country->name[1] ?? 'el país';
            $this->errors[] = $this->l('El país seleccionado está inactivo. Por favor, actívalo en Internacional > Países.');
            $temp_cart->delete();
            return;
        }
        
        $state_id = State::getIdByIso($province_code, $country_id);
        
        if (!$state_id) {
            $this->errors[] = $this->l('La provincia/estado seleccionado no existe para este país');
            $temp_cart->delete();
            return;
        }
        
        // Actualizar la dirección ID 1 directamente en la base de datos (igual que getDeliveryPrice.php)
       
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'address`
                SET `id_country` = ' . (int)$country_id . ',
                `id_state` = ' . (int)$state_id . ',
                `postcode` = "' . pSQL($postal_code) . '"
                WHERE `id_address` = 1';
        
        Db::getInstance()->execute($sql);
        
        // Obtener días de envío (horquilla)
        $shipping_range = $module->getShippingDaysRangeByProvince($province_code);
        if ($shipping_range) {
            $shipping_days_min = (int)$shipping_range['min'];
            $shipping_days_max = (int)$shipping_range['max'];
        } else {
            // Compatibilidad: si no hay horquilla, usar valor único
            $shipping_single = $module->getShippingDaysByProvince($province_code);
            $shipping_days_min = (int)$shipping_single;
            $shipping_days_max = (int)$shipping_single;
        }
        
        // Determinar si mostrar impuestos (igual que en cart-voucher.tpl)
        $tax_config = new \TaxConfiguration();
        $show_taxes = $tax_config->includeTaxes() || !Configuration::get('PS_TAX');
        
        $shipping_cost = $this->calculateShippingCostLikeCart($temp_cart, $show_taxes);
        
        $state = new State($state_id);
        
        // Limpiar carrito temporal (la dirección ID 1 se mantiene para otros usos)
        $temp_cart->delete();

        // Guardar país seleccionado para mantenerlo en el formulario
        $this->selected_country_id = $country_id;
        $this->selected_postal_code = $postal_code;

        // Calcular plazos usando el método del módulo
        $prep_mode = (int)Configuration::get('SHIPPING_CALCULATOR_PREP_MODE');
        
        // Si es modo "por producto", calcular plazos individuales
        if ($prep_mode == 2) {
            $products_delivery = [];
            
            foreach ($selected_products as $id_product) {
                $product = new Product((int)$id_product, false, $this->context->language->id);
                $quantity = isset($product_quantities[$id_product]) ? (int)$product_quantities[$id_product] : 1;
                if ($quantity < 1) {
                    $quantity = 1;
                }
                $prep_days = $module->getProductPreparationDays((int)$id_product);
                $total_days = $prep_days + $shipping_days;
                $start_date = date('d/m/Y', strtotime('+' . $prep_days . ' days'));
                $end_date = date('d/m/Y', strtotime('+' . $total_days . ' days'));
                
                $products_delivery[] = [
                    'id_product' => (int)$id_product,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'preparation_days' => $prep_days,
                    'shipping_days_min' => $shipping_days_min,
                    'shipping_days_max' => $shipping_days_max,
                    'total_days_min' => $prep_days + $shipping_days_min,
                    'total_days_max' => $prep_days + $shipping_days_max,
                    'start_date' => date('d/m/Y', strtotime('+' . ($prep_days + $shipping_days_min) . ' days')),
                    'end_date' => date('d/m/Y', strtotime('+' . ($prep_days + $shipping_days_max) . ' days')),
                ];
            }
            
            // Guardar resultado en modo por producto
            $this->calculation_result = [
                'mode' => 'by_product',
                'products' => $products_delivery,
                'shipping_days_min' => $shipping_days_min,
                'shipping_days_max' => $shipping_days_max,
                'province' => $state->name,
                'shipping_cost' => $shipping_cost,
                'shipping_cost_formatted' => Tools::displayPrice($shipping_cost),
            ];
        } else {
            // Modo máximo o suma: cálculo combinado
            $preparation_days = 0;
            
            if ($prep_mode == 1) {
                // Modo suma
                foreach ($selected_products as $id_product) {
                    $days = $module->getProductPreparationDays((int)$id_product);
                    $preparation_days += $days;
                }
            } else {
                // Modo máximo
                foreach ($selected_products as $id_product) {
                    $days = $module->getProductPreparationDays((int)$id_product);
                    if ($days > $preparation_days) {
                        $preparation_days = $days;
                    }
                }
            }
            
            $total_days_min = $preparation_days + $shipping_days_min;
            $total_days_max = $preparation_days + $shipping_days_max;
            $start_date = date('d/m/Y', strtotime('+' . $total_days_min . ' days'));
            $end_date = date('d/m/Y', strtotime('+' . $total_days_max . ' days'));

            // Guardar resultado en modo combinado
            $this->calculation_result = [
                'mode' => 'combined',
                'preparation_days' => $preparation_days,
                'shipping_days_min' => $shipping_days_min,
                'shipping_days_max' => $shipping_days_max,
                'total_days_min' => $total_days_min,
                'total_days_max' => $total_days_max,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'province' => $state->name,
                'shipping_cost' => $shipping_cost,
                'shipping_cost_formatted' => Tools::displayPrice($shipping_cost),
            ];
        }
        
        $this->selected_products = $selected_products;
        $this->selected_province = $province_code;
    }

    private function processSaveDelays()
    {
        $module = Module::getInstanceByName('shippingcalculator');
        $delays = Tools::getValue('delays', []);

        // Obtener valores actuales de la base de datos para comparar
        $current_delays = $module->getAllShippingDelays();
        $current_delays_by_code = [];
        foreach ($current_delays as $delay) {
            $current_delays_by_code[$delay['province_code']] = $delay;
        }

        $saved_count = 0;
        $skipped_count = 0;

        foreach ($delays as $province_code => $data) {
            if (isset($data['name']) && !empty($province_code)) {
                // Limpiar y validar valores - convertir cadenas vacías a null
                $days_min_raw = isset($data['days_min']) ? trim($data['days_min']) : '';
                $days_max_raw = isset($data['days_max']) ? trim($data['days_max']) : '';
                
                // Validar que sean numéricos antes de convertir
                $days_min = ($days_min_raw !== '' && $days_min_raw !== null && is_numeric($days_min_raw) && $days_min_raw >= 0) ? (int)$days_min_raw : null;
                $days_max = ($days_max_raw !== '' && $days_max_raw !== null && is_numeric($days_max_raw) && $days_max_raw >= 0) ? (int)$days_max_raw : null;
                
                // Obtener valores actuales de la base de datos (si la provincia existe)
                $current_min = null;
                $current_max = null;
                $province_exists = isset($current_delays_by_code[$province_code]);
                
                if ($province_exists) {
                    $current = $current_delays_by_code[$province_code];
                    $current_min = isset($current['delivery_days_min']) && $current['delivery_days_min'] !== null && $current['delivery_days_min'] !== '' ? (int)$current['delivery_days_min'] : null;
                    $current_max = isset($current['delivery_days_max']) && $current['delivery_days_max'] !== null && $current['delivery_days_max'] !== '' ? (int)$current['delivery_days_max'] : null;
                }
                
                // Solo guardar si al menos uno de los valores está definido
                $has_new_value = ($days_min !== null || $days_max !== null);
                
                if ($has_new_value) {
                    // Si la provincia no existe en la BD, siempre guardar (es un INSERT nuevo)
                    // Si la provincia existe, solo guardar si los valores han cambiado
                    $should_save = false;
                    if (!$province_exists) {
                        // Provincia nueva: siempre guardar si tiene valores
                        $should_save = true;
                    } else {
                        // Provincia existente: guardar solo si los valores han cambiado
                        $has_changed = ($days_min !== $current_min || $days_max !== $current_max);
                        $should_save = $has_changed;
                    }
                    
                    if ($should_save) {
                        // Validar que min <= max si ambos están definidos
                        if ($days_min !== null && $days_max !== null && $days_min > $days_max) {
                            $this->errors[] = $this->l('Provincia') . ' ' . $data['name'] . ': ' . $this->l('Los días mínimos no pueden ser mayores que los días máximos');
                            continue;
                        }
                        
                        // Si no hay horquilla, usar el valor disponible como días de envío (compatibilidad)
                        $delivery_days = $days_min !== null ? $days_min : ($days_max !== null ? $days_max : 5);
                        
                        // Limpiar código de provincia para evitar problemas de codificación
                        $province_code_clean = pSQL(trim($province_code));
                        $province_name_clean = pSQL(trim($data['name']));
                        
                        if (empty($province_code_clean)) {
                            $this->errors[] = $this->l('Código de provincia vacío para') . ': ' . $data['name'];
                            continue;
                        }
                        
                        if ($module->saveShippingDaysByProvince(
                            $province_code_clean,
                            $province_name_clean,
                            $delivery_days,
                            $days_min,
                            $days_max
                        )) {
                            $saved_count++;
                        } else {
                            $error_msg = $this->l('Error al guardar provincia') . ': ' . $data['name'];
                            // Añadir información de debug en modo desarrollo
                            if (_PS_MODE_DEV_) {
                                $error_msg .= ' (Código: ' . $province_code_clean . ')';
                            }
                            $this->errors[] = $error_msg;
                        }
                    } else {
                        // Valores no han cambiado, saltar
                        $skipped_count++;
                    }
                } else {
                    // Si ambos están vacíos, no guardar (saltar esta provincia)
                    $skipped_count++;
                }
            }
        }

        if ($saved_count > 0) {
            $this->confirmations[] = $this->l('Plazos y costes de envío guardados correctamente') . ' (' . $saved_count . ' ' . $this->l('provincias') . ')';
        }
        
        if ($skipped_count > 0 && $saved_count == 0) {
            $this->errors[] = $this->l('No se guardaron provincias.') . ' ' . 
                              $this->l('Posibles razones:') . ' ' . 
                              $this->l('(1) No se introdujeron días mínimos o máximos,') . ' ' .
                              $this->l('(2) Los valores ya están guardados y no han cambiado,') . ' ' .
                              $this->l('(3) Los campos están vacíos.') . ' ' .
                              $this->l('Por favor, verifica que los valores sean diferentes a los actuales.');
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('calculateShipping')) {
            $this->processCalculation();
        } elseif (Tools::isSubmit('saveShippingDelays')) {
            $this->processSaveDelays();
        } elseif (Tools::isSubmit('saveConfiguration')) {
            $prep_mode = (int)Tools::getValue('SHIPPING_CALCULATOR_PREP_MODE', 0);
            Configuration::updateValue('SHIPPING_CALCULATOR_PREP_MODE', $prep_mode);
            $this->confirmations[] = $this->l('Configuración guardada correctamente');
        } elseif (Tools::isSubmit('exportCSV')) {
            $this->processExportCSV();
        } elseif (Tools::isSubmit('importCSV')) {
            $this->processImportCSV();
        }
        
        parent::postProcess();
    }
    
    private function processExportCSV()
    {
        try {
            $module = Module::getInstanceByName('shippingcalculator');
            $delays = $module->getAllShippingDelays();
            
            if (empty($delays)) {
                die('No hay datos para exportar');
            }

            // Filtrar solo provincias que tienen datos configurados (min o max)
            $filtered_delays = [];
            foreach ($delays as $row) {
                $has_min = isset($row['delivery_days_min']) && $row['delivery_days_min'] !== null && $row['delivery_days_min'] !== '';
                $has_max = isset($row['delivery_days_max']) && $row['delivery_days_max'] !== null && $row['delivery_days_max'] !== '';
                $has_days = isset($row['delivery_days']) && $row['delivery_days'] !== null && $row['delivery_days'] > 0;
                
                // Solo incluir si tiene al menos uno de los valores configurados
                if ($has_min || $has_max || $has_days) {
                    $filtered_delays[] = $row;
                }
            }
            
            if (empty($filtered_delays)) {
                die('No hay datos configurados para exportar. Por favor, configura al menos días mínimos o máximos para algunas provincias.');
            }

            // Limpiar cualquier salida previa
            if (ob_get_level()) {
                ob_end_clean();
            }

            $filename = 'shipping_delays_' . date('Y-m-d') . '.csv';

            // Exportar como CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');
            
            if (!$output) {
                die('Error: No se pudo abrir el stream de salida');
            }
            
            // BOM para Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            $headers = [
                'province_code',
                'province_name',
                'delivery_days_min',
                'delivery_days_max'
            ];
            fputcsv($output, $headers, ',');
            
            // Datos filtrados
            foreach ($filtered_delays as $row) {
                $csv_row = [
                    $row['province_code'],
                    $row['province_name'],
                    isset($row['delivery_days_min']) && $row['delivery_days_min'] !== null ? (int)$row['delivery_days_min'] : '',
                    isset($row['delivery_days_max']) && $row['delivery_days_max'] !== null ? (int)$row['delivery_days_max'] : ''
                ];
                fputcsv($output, $csv_row, ',');
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

        $module = Module::getInstanceByName('shippingcalculator');
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');

        if (!$handle) {
            $this->errors[] = $this->l('No se pudo leer el archivo');
            return;
        }

        // Leer y validar header
        $header = fgetcsv($handle, 1000, ',');
        if (!$header || count($header) < 3) {
            $this->errors[] = $this->l('Formato de archivo inválido. Debe contener al menos: province_code, province_name, delivery_days_min, delivery_days_max');
            fclose($handle);
            return;
        }
        
        // Limpiar header - eliminar comillas y espacios
        $header = array_map(function($col) {
            // Quitar BOM UTF-8 si existe
            $col = preg_replace('/^\xEF\xBB\xBF/', '', $col);
            return trim(strtolower(trim($col, " \t\n\r\0\x0B\"'")));
        }, $header);
        
        // Detectar índices de columnas
        $province_code_index = array_search('province_code', $header);
        $province_name_index = array_search('province_name', $header);
        $delivery_days_min_index = array_search('delivery_days_min', $header);
        $delivery_days_max_index = array_search('delivery_days_max', $header);
        
        if ($province_code_index === false || $province_name_index === false) {
            $this->errors[] = $this->l('Formato de archivo inválido. Faltan columnas requeridas: province_code, province_name');
            fclose($handle);
            return;
        }
        
        // delivery_days_min y delivery_days_max son requeridos
        if ($delivery_days_min_index === false || $delivery_days_max_index === false) {
            $this->errors[] = $this->l('Formato de archivo inválido. Debe contener: delivery_days_min y delivery_days_max');
            fclose($handle);
            return;
        }

        $imported = 0;
        $errors = 0;
        $error_details = [];
        $line_number = 1; // Empezar en 1 porque ya leímos el header

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $line_number++;
            
            // Limpiar datos
            $data = array_map(function($val) {
                return trim($val, " \t\n\r\0\x0B\"'");
            }, $data);
            
            // Saltar líneas vacías
            if (count($data) < 3 || empty($data[$province_code_index])) {
                continue;
            }
            
            $province_code = $data[$province_code_index];
            $province_name = isset($data[$province_name_index]) ? $data[$province_name_index] : '';
            $delivery_days_min = ($delivery_days_min_index !== false && isset($data[$delivery_days_min_index]) && $data[$delivery_days_min_index] !== '') ? (int)$data[$delivery_days_min_index] : null;
            $delivery_days_max = ($delivery_days_max_index !== false && isset($data[$delivery_days_max_index]) && $data[$delivery_days_max_index] !== '') ? (int)$data[$delivery_days_max_index] : null;
            
            // Validar presencia
            if ($delivery_days_min === null || $delivery_days_max === null) {
                $errors++;
                $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('Faltan delivery_days_min o delivery_days_max');
                continue;
            }
            
            // Para compatibilidad con la función saveShippingDaysByProvince
            $delivery_days = $delivery_days_min;

            if (empty($province_code)) {
                $errors++;
                $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('Código de provincia vacío');
                continue;
            }

            // Validar que min <= max si ambos están definidos
            if ($delivery_days_min !== null && $delivery_days_max !== null && $delivery_days_min > $delivery_days_max) {
                $errors++;
                $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('Los días mínimos no pueden ser mayores que los días máximos');
                continue;
            }

            // Guardar
            if ($module->saveShippingDaysByProvince($province_code, $province_name, $delivery_days, $delivery_days_min, $delivery_days_max)) {
                $imported++;
            } else {
                $errors++;
                $error_details[] = $this->l('Línea') . ' ' . $line_number . ': ' . $this->l('Error al guardar en la base de datos');
            }
        }

        fclose($handle);

        // Mostrar resultados
        if ($imported > 0) {
            $this->confirmations[] = $this->l('Importados') . ' ' . $imported . ' ' . $this->l('registros correctamente');
        }
        if ($errors > 0) {
            $this->errors[] = $this->l('Errores en') . ' ' . $errors . ' ' . $this->l('registros de') . ' ' . ($imported + $errors) . ' ' . $this->l('total');
            
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
        $module = Module::getInstanceByName('shippingcalculator');
        
        // Obtener productos
        $products = Product::getProducts($this->context->language->id, 0, 0, 'name', 'ASC');
        
        // Obtener todos los países activos
        $countries = Country::getCountries($this->context->language->id, true);
        
        // Obtener país seleccionado (del POST o por defecto)
        $selected_country_id = (int)Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT'));
        $states = [];
        $states_by_country = [];
        
        // Obtener todos los estados agrupados por país
        foreach ($countries as $country) {
            $country_states = State::getStatesByIdCountry($country['id_country']);
            if ($country_states !== false && !empty($country_states)) {
                $states_by_country[$country['id_country']] = [
                    'country' => $country,
                    'states' => $country_states
                ];
                
                // Si es el país seleccionado, asignar sus estados
                if ($country['id_country'] == $selected_country_id) {
                    $states = $country_states;
                }
            }
        }
        
        // Si no hay estados para el país seleccionado, usar el país por defecto
        if (empty($states) && $selected_country_id) {
            $default_country_id = (int)Configuration::get('PS_COUNTRY_DEFAULT');
            if ($default_country_id && isset($states_by_country[$default_country_id])) {
                $states = $states_by_country[$default_country_id]['states'];
                $selected_country_id = $default_country_id;
            }
        }
        
        // Obtener plazos de envío configurados
        $shipping_delays = $module->getAllShippingDelays();
        $delays_by_province = [];
        foreach ($shipping_delays as $delay) {
            $delays_by_province[$delay['province_code']] = $delay;
        }

        // Obtener configuración
        $prep_mode = (int)Configuration::get('SHIPPING_CALCULATOR_PREP_MODE', 0);

        // Obtener resultado de cálculo si existe (desde processCalculation)
        $calculation_result = isset($this->calculation_result) ? $this->calculation_result : null;
        
        // Mantener valores seleccionados después del POST
        $selected_products = isset($this->selected_products) ? $this->selected_products : [];
        if (empty($selected_products) && Tools::getValue('selected_products')) {
            $selected_products = Tools::getValue('selected_products');
            // Asegurar que sean enteros
            $selected_products = array_map('intval', $selected_products);
        }
        
        $selected_province = isset($this->selected_province) ? $this->selected_province : '';
        if (empty($selected_province) && Tools::getValue('province_code')) {
            $selected_province = Tools::getValue('province_code');
        }
        
        // Mantener país seleccionado para el filtro de la tabla
        $filter_country_id = (int)Configuration::get('PS_COUNTRY_DEFAULT');
        if (isset($this->selected_country_id)) {
            $filter_country_id = $this->selected_country_id;
        } elseif (Tools::getValue('id_country')) {
            $filter_country_id = (int)Tools::getValue('id_country');
        }
        
        // Si hay un país seleccionado, cargar sus estados
        if ($selected_country_id && isset($states_by_country[$selected_country_id])) {
            $states = $states_by_country[$selected_country_id]['states'];
        }

        // Obtener país por defecto para el template
        $default_country_id = (int)Configuration::get('PS_COUNTRY_DEFAULT');
        
        // Obtener cantidades de productos para el template (mantener después del POST)
        $product_quantities = isset($this->product_quantities) ? $this->product_quantities : [];
        if (empty($product_quantities) && Tools::getValue('product_quantities')) {
            $product_quantities = Tools::getValue('product_quantities');
        }
        
        $this->context->smarty->assign([
            'filter_country_id' => $filter_country_id,
            'default_country_id' => $default_country_id,
            'products' => $products,
            'countries' => $countries,
            'states' => $states,
            'states_by_country' => $states_by_country,
            'selected_country_id' => $selected_country_id,
            'selected_postal_code' => $this->selected_postal_code,
            'shipping_delays' => $delays_by_province,
            'module_dir' => __PS_BASE_URI__ . 'modules/' . $module->name . '/',
            'prep_mode' => $prep_mode,
            'calculation_result' => $calculation_result,
            'selected_products' => $selected_products,
            'selected_province' => $selected_province,
            'product_quantities' => $product_quantities,
        ]);

        // Usar ruta directa del template (mismo método que productfeaturesmanager)
        $template_path = _PS_MODULE_DIR_ . $module->name . '/views/templates/admin/calculator.tpl';
        return $this->context->smarty->fetch($template_path);
    }
    
    /**
     * Calcular coste de envío usando el mismo método que getDeliveryPrice.php (carrito)
     * Replica exactamente la lógica de ajax/getDeliveryPrice.php
     */
    private function calculateShippingCostLikeCart($cart, $showTaxes = true)
    {
        if (!$cart || !$cart->id) {
            return 0;
        }
        
        // Actualizar carrito
        $cart->id_address_delivery = '1';
        $cart->id_address_invoice = '1';
        $cart->id_customer = '2';
        $cart->update();

        //Coge el transportista mas barato
        $bestOption = Carrier::getCheapestDeliveryOptionByCart($cart, $showTaxes);

        if (!$bestOption || empty($bestOption['id_carrier'])) {
            echo json_encode([
                'error' => 'No hay transportistas disponibles'
            ]);
            exit;
        }

        $id_carrier = (int)$bestOption['id_carrier'];
        $result = $showTaxes ? (float)$bestOption['price_with_tax'] : (float)$bestOption['price_without_tax'];
        
        
        // actualizar el carrito
        $cart->id_carrier = (string)$id_carrier;
        $cart->delivery_option = '{"1":"'.$id_carrier.'"}';
        $cart->update();
            
        return $result;
    }
}

