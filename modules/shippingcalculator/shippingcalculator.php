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

class ShippingCalculator extends Module
{
    public function __construct()
    {
        $this->name = 'shippingcalculator';
        $this->tab = 'shipping_logistics';
        $this->version = '1.3.1';
        $this->author = 'Qwavee';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Calculadora de Envíos y Plazos');
        $this->description = $this->l('Sistema para calcular costes de envío y plazos de entrega');
        $this->confirmUninstall = $this->l('¿Estás seguro de que quieres desinstalar?');
    }

    /**
     * Instalación del módulo
     */
    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        // Añadir campos de horquilla si no existen (migración)
        include(dirname(__FILE__) . '/sql/add_delivery_days_range.php');

        // Configuración por defecto
        Configuration::updateValue('SHIPPING_CALCULATOR_PREP_MODE', 0); // 0 = máximo, 1 = suma

        // Migrar datos antiguos si existen
        $this->migrateFromOldTable();

        return parent::install() &&
            $this->installTab() &&
            $this->registerHook('displayShoppingCartFooter') &&
            $this->registerHook('displayHeader');
    }
    
    /**
     * Migrar datos de la tabla antigua al campo nativo (si existe)
     */
    private function migrateFromOldTable()
    {
        $migrate_script = dirname(__FILE__) . '/sql/migrate_to_native_field.php';
        if (file_exists($migrate_script)) {
            include_once($migrate_script);
            if (function_exists('migrateToNativeDeliveryField')) {
                migrateToNativeDeliveryField();
            }
        }
    }

    /**
     * Desinstalación del módulo
     */
    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        // Eliminar configuración
        Configuration::deleteByName('SHIPPING_CALCULATOR_PREP_MODE');

        return parent::uninstall() && $this->uninstallTab();
    }

    /**
     * Instalar tab en el menú admin
     */
    private function installTab()
    {
        // Verificar si el tab ya existe
        $id_tab = (int)Tab::getIdFromClassName('AdminShippingCalculator');
        if ($id_tab) {
            return true;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->enabled = 1;
        $tab->class_name = 'AdminShippingCalculator';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Calculadora de Envíos';
        }
        
        // Intentar encontrar el parent correcto
        $id_parent = (int)Tab::getIdFromClassName('AdminParentShipping');
        if (!$id_parent) {
            $id_parent = (int)Tab::getIdFromClassName('AdminParentOrders');
        }
        if (!$id_parent) {
            $id_parent = (int)Tab::getIdFromClassName('AdminCatalog');
        }
        if (!$id_parent) {
            $id_parent = 0; // Root level
        }
        
        $tab->id_parent = $id_parent;
        $tab->module = $this->name;
        $tab->icon = 'local_shipping';
        
        if ($tab->save()) {
            // PrestaShop asigna permisos automáticamente al crear el tab
            // Inicializar permisos usando el método de PrestaShop
            Tab::initAccess($tab->id, $this->context);
            return true;
        }
        
        return false;
    }

    /**
     * Desinstalar tab
     */
    private function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminShippingCalculator');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return false;
    }

    /**
     * Hook: Mostrar plazo estimado en el carrito
     */
    public function hookDisplayShoppingCartFooter($params = [])
    {
        // Verificar que el módulo esté activo
        if (!$this->active) {
            return '';
        }

        // Obtener el carrito del contexto (más confiable)
        $cart = $this->context->cart;
        
        // Si no hay carrito en el contexto, intentar obtenerlo de params
        if (!$cart || !$cart->id) {
            if (isset($params['cart']) && is_object($params['cart']) && isset($params['cart']->id) && $params['cart']->id) {
                $cart = $params['cart'];
            } else {
                return '';
            }
        }
        
        // Verificar que hay productos en el carrito
        $products = $cart->getProducts(true);
        if (empty($products)) {
            return '';
        }

        // Calcular el plazo estimado
        $estimated_delivery = $this->calculateEstimatedDelivery($cart);

        // Asignar variables a Smarty
        $this->context->smarty->assign([
            'estimated_delivery' => $estimated_delivery,
            'module_dir' => $this->_path,
            'has_delivery_info' => !empty($estimated_delivery),
        ]);

        // Siempre mostrar el template (incluso si no hay datos, para debugging)
        return $this->display(__FILE__, 'views/templates/hook/shopping_cart_delivery.tpl');
    }

    /**
     * Hook: Añadir CSS/JS en el front
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
    }

    /**
     * Obtener días de preparación de un producto desde el campo nativo delivery_in_stock
     * Respeta la configuración de additional_delivery_times:
     *   0 = No usar plazos de entrega
     *   1 = Usar plazo predeterminado (configuración global)
     *   2 = Usar plazo específico del producto
     */
    public function getProductPreparationDays($id_product)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;

        // 1) PRIORIDAD: característica id=60 (días de preparación)
        $sql = 'SELECT fvl.value
                FROM `' . _DB_PREFIX_ . 'feature_product` fp
                INNER JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl
                    ON (fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = ' . (int)$id_lang . ')
                WHERE fp.id_product = ' . (int)$id_product . '
                AND fp.id_feature = 60
                ';

        $feature_value = Db::getInstance()->getValue($sql);

        if (!empty($feature_value)) {
            return $this->parseDaysFromText($feature_value);
        }

        
        // Obtener el valor de additional_delivery_times del producto
        $sql = 'SELECT additional_delivery_times 
                FROM `' . _DB_PREFIX_ . 'product`
                WHERE id_product = ' . (int)$id_product;
        
        $delivery_mode = (int)Db::getInstance()->getValue($sql);
        
        $delivery_text = '';
        
        switch ($delivery_mode) {
            case 1:
                // Usar plazo predeterminado de la configuración global
                $delivery_text = Configuration::get('PS_LABEL_IN_STOCK_PRODUCTS', $id_lang);
                break;
                
            case 2:
                // Usar plazo específico del producto
                $sql = 'SELECT delivery_in_stock 
                        FROM `' . _DB_PREFIX_ . 'product_lang`
                        WHERE id_product = ' . (int)$id_product . '
                        AND id_lang = ' . $id_lang . '
                        AND id_shop = ' . $id_shop;
                
                $delivery_text = Db::getInstance()->getValue($sql);
                break;
                
            default:
                // No usar plazos (0 o NULL)
                return 0;
        }
        
        // Parsear días desde el texto
        return $this->parseDaysFromText($delivery_text);
    }
    
    /**
     * Extraer número de días desde texto libre
     * Ejemplos: "3 días" -> 3, "5-7 días" -> 5, "24h" -> 1, "Consultar" -> 0
     */
    private function parseDaysFromText($text)
    {
        if (empty($text)) {
            return 0;
        }
        
        // Si contiene "24h", "24 h", "48h", etc, convertir a días
        if (preg_match('/(\d+)\s*h/i', $text, $matches)) {
            $hours = (int)$matches[1];
            return (int)ceil($hours / 24);
        }
        
        // Buscar el primer número en el texto
        if (preg_match('/(\d+)/', $text, $matches)) {
            return (int)$matches[1];
        }
        
        return 0;
    }


    /**
     * Calcular plazo estimado de entrega
     */
    public function calculateEstimatedDelivery($cart)
    {
        if (!$cart || !$cart->id) {
            return null;
        }

        $prep_mode = (int)Configuration::get('SHIPPING_CALCULATOR_PREP_MODE');

        // Obtener dirección de envío
        $address = null;
        if ($cart->id_address_delivery) {
            $address = new Address($cart->id_address_delivery);
        }
        
        // Si no hay dirección de envío, intentar usar la dirección de facturación
        if (!$address || !$address->id) {
            if ($cart->id_address_invoice) {
                $address = new Address($cart->id_address_invoice);
            }
        }
        
        // Si aún no hay dirección, intentar obtener la primera dirección del cliente
        if ((!$address || !$address->id) && $cart->id_customer) {
            $customer = new Customer($cart->id_customer);
            if ($customer && $customer->id) {
                $addresses = $customer->getAddresses($this->context->language->id);
                if (!empty($addresses)) {
                    $address = new Address($addresses[0]['id_address']);
                }
            }
        }
        
        // Si no hay dirección válida, no podemos calcular
        if (!$address || !$address->id) {
            return null;
        }

        // Obtener provincia
        $state = new State($address->id_state);
        if (!$state || !$state->id) {
            return null;
        }

        // Obtener días de envío según provincia (valor medio) y horquilla si existe
        $shipping_days = $this->getShippingDaysByProvince($state->iso_code);
        $shipping_days_range = $this->getShippingDaysRangeByProvince($state->iso_code);
        $shipping_days_min = null;
        $shipping_days_max = null;
        if (is_array($shipping_days_range)) {
            $shipping_days_min = $shipping_days_range['min'];
            $shipping_days_max = $shipping_days_range['max'];
        } else {
            // Si no hay rango, usar el valor único como min y max
            $shipping_days_min = $shipping_days;
            $shipping_days_max = $shipping_days;
        }

        // Calcular coste de envío
        $shipping_cost = $this->calculateShippingCost($cart, $address);

        // Si es modo "Por producto", calcular plazos individuales
        if ($prep_mode == 2) {
            $products_preparation = $this->getCartProductsPreparationDays($cart);
            $products_delivery = [];
            
            foreach ($products_preparation as $product_prep) {
                $prep_days = $product_prep['preparation_days'];
                $total_days = $prep_days + $shipping_days;
                $start_date = date('Y-m-d', strtotime('+' . $prep_days . ' days'));
                $end_date = date('Y-m-d', strtotime('+' . $total_days . ' days'));
                
                $products_delivery[] = [
                    'id_product' => $product_prep['id_product'],
                    'name' => $product_prep['name'],
                    'quantity' => $product_prep['quantity'],
                    'preparation_days' => $prep_days,
                    'shipping_days' => $shipping_days,
                    'total_days' => $total_days,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_date_formatted' => date('d/m/Y', strtotime($start_date)),
                    'end_date_formatted' => date('d/m/Y', strtotime($end_date)),
                ];
            }
            
            return [
                'mode' => 'by_product',
                'products' => $products_delivery,
                'shipping_days' => $shipping_days,
                'shipping_days_min' => $shipping_days_min,
                'shipping_days_max' => $shipping_days_max,
                'province' => $state->name,
                'shipping_cost' => $shipping_cost,
                'shipping_cost_formatted' => Tools::displayPrice($shipping_cost),
            ];
        }

        // Modo máximo o suma: cálculo combinado
        $preparation_days = $this->getCartPreparationDays($cart);
        $total_days = $preparation_days + $shipping_days;
        $start_date = date('Y-m-d', strtotime('+' . $preparation_days . ' days'));
        $end_date = date('Y-m-d', strtotime('+' . $total_days . ' days'));

        return [
            'mode' => 'combined',
            'preparation_days' => $preparation_days,
            'shipping_days' => $shipping_days,
            'shipping_days_min' => $shipping_days_min,
            'shipping_days_max' => $shipping_days_max,
            'total_days' => $total_days,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'start_date_formatted' => date('d/m/Y', strtotime($start_date)),
            'end_date_formatted' => date('d/m/Y', strtotime($end_date)),
            'province' => $state->name,
            'shipping_cost' => $shipping_cost,
            'shipping_cost_formatted' => Tools::displayPrice($shipping_cost),
        ];
    }

    /**
     * Obtener plazo de preparación del carrito (máximo, suma o por producto según configuración)
     */
    private function getCartPreparationDays($cart)
    {
        $products = $cart->getProducts();
        $prep_mode = (int)Configuration::get('SHIPPING_CALCULATOR_PREP_MODE'); // 0 = máximo, 1 = suma, 2 = por producto
        
        if ($prep_mode == 1) {
            // Modo suma: sumar todos los días de preparación
            $total_days = 0;
            foreach ($products as $product) {
                $days = $this->getProductPreparationDays($product['id_product']);
                $total_days += $days * $product['cart_quantity'];
            }
            return $total_days;
        } elseif ($prep_mode == 2) {
            // Modo por producto: devolver el máximo para cálculo general, pero se mostrará por producto
            $max_days = 0;
            foreach ($products as $product) {
                $days = $this->getProductPreparationDays($product['id_product']);
                if ($days > $max_days) {
                    $max_days = $days;
                }
            }
            return $max_days;
        } else {
            // Modo máximo: tomar el máximo
            $max_days = 0;
            foreach ($products as $product) {
                $days = $this->getProductPreparationDays($product['id_product']);
                if ($days > $max_days) {
                    $max_days = $days;
                }
            }
            return $max_days;
        }
    }

    /**
     * Obtener plazos de preparación por producto del carrito
     */
    private function getCartProductsPreparationDays($cart)
    {
        $products = $cart->getProducts();
        $products_preparation = [];
        
        foreach ($products as $product) {
            $days = $this->getProductPreparationDays($product['id_product']);
            $products_preparation[] = [
                'id_product' => $product['id_product'],
                'name' => $product['name'],
                'quantity' => $product['cart_quantity'],
                'preparation_days' => $days,
            ];
        }
        
        return $products_preparation;
    }

    /**
     * Obtener máximo plazo de preparación del carrito (método legacy)
     */
    private function getCartMaxPreparationDays($cart)
    {
        return $this->getCartPreparationDays($cart);
    }

    /**
     * Obtener días de envío por provincia
     * Si hay horquilla (min/max), devuelve el promedio. Si no, devuelve el valor único.
     */
    public function getShippingDaysByProvince($province_code)
    {
        $sql = 'SELECT delivery_days, delivery_days_min, delivery_days_max 
                FROM `' . _DB_PREFIX_ . 'shipping_calculator_delays`
                WHERE province_code = "' . pSQL($province_code) . '"';

        $result = Db::getInstance()->getRow($sql);
        
        if ($result) {
            // Si hay horquilla, usar el promedio
            if ($result['delivery_days_min'] !== null && $result['delivery_days_max'] !== null) {
                return (int)ceil(($result['delivery_days_min'] + $result['delivery_days_max']) / 2);
            }
            // Si solo hay mínimo o máximo, usar ese valor
            if ($result['delivery_days_min'] !== null) {
                return (int)$result['delivery_days_min'];
            }
            if ($result['delivery_days_max'] !== null) {
                return (int)$result['delivery_days_max'];
            }
            // Si no hay horquilla, usar el valor único
            return $result['delivery_days'] ? (int)$result['delivery_days'] : 5;
        }
        
        return 5; // Default 5 días
    }
    
    /**
     * Obtener horquilla de días de envío por provincia
     * Devuelve array con min y max, o null si no hay horquilla
     */
    public function getShippingDaysRangeByProvince($province_code)
    {
        $sql = 'SELECT delivery_days_min, delivery_days_max 
                FROM `' . _DB_PREFIX_ . 'shipping_calculator_delays`
                WHERE province_code = "' . pSQL($province_code) . '"';

        $result = Db::getInstance()->getRow($sql);
        
        if ($result && $result['delivery_days_min'] !== null && $result['delivery_days_max'] !== null) {
            return [
                'min' => (int)$result['delivery_days_min'],
                'max' => (int)$result['delivery_days_max']
            ];
        }
        
        return null;
    }

    /**
     * Guardar días de envío por provincia
     */
    public function saveShippingDaysByProvince($province_code, $province_name, $delivery_days, $delivery_days_min = null, $delivery_days_max = null)
    {
        // Asegurar que las columnas existen (evita errores en instalaciones antiguas)
        $this->ensureDeliveryDaysRangeColumns();

        $days_min_sql = $delivery_days_min !== null ? (int)$delivery_days_min : 'NULL';
        $days_max_sql = $delivery_days_max !== null ? (int)$delivery_days_max : 'NULL';
        
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'shipping_calculator_delays`
                (province_code, province_name, delivery_days, delivery_days_min, delivery_days_max)
                VALUES ("' . pSQL($province_code) . '", "' . pSQL($province_name) . '", ' . (int)$delivery_days . ', ' . $days_min_sql . ', ' . $days_max_sql . ')
                ON DUPLICATE KEY UPDATE 
                    province_name = "' . pSQL($province_name) . '",
                    delivery_days = ' . (int)$delivery_days . ',
                    delivery_days_min = ' . $days_min_sql . ',
                    delivery_days_max = ' . $days_max_sql;

        return Db::getInstance()->execute($sql);
    }

    /**
     * Verifica y crea las columnas delivery_days_min y delivery_days_max si no existen
     */
    private function ensureDeliveryDaysRangeColumns()
    {
        static $checked = false;
        if ($checked) {
            return;
        }

        $table = _DB_PREFIX_ . 'shipping_calculator_delays';

        // Comprobar si la tabla existe usando information_schema (compatible con MariaDB/MySQL)
        $tableExists = Db::getInstance()->getValue('
            SELECT COUNT(*) 
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = "' . pSQL($table, true) . '"
        ');
        if (!$tableExists) {
            $checked = true;
            return;
        }

        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . pSQL($table) . '`');
        $columnNames = array_column($columns, 'Field');

        $queries = [];

        if (!in_array('delivery_days_min', $columnNames)) {
            $queries[] = 'ALTER TABLE `' . pSQL($table) . '` ADD COLUMN `delivery_days_min` int(11) UNSIGNED DEFAULT NULL AFTER `delivery_days`';
        }

        if (!in_array('delivery_days_max', $columnNames)) {
            $queries[] = 'ALTER TABLE `' . pSQL($table) . '` ADD COLUMN `delivery_days_max` int(11) UNSIGNED DEFAULT NULL AFTER `delivery_days_min`';
        }

        foreach ($queries as $query) {
            Db::getInstance()->execute($query);
        }

        $checked = true;
    }

    /**
     * Obtener todas las provincias con sus plazos
     */
    public function getAllShippingDelays()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'shipping_calculator_delays`
                ORDER BY province_name ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Calcular coste de envío para un carrito
     */
    public function calculateShippingCost($cart, $address = null)
    {
        if (!$cart || !$cart->id) {
            return 0;
        }

        if (!$address) {
            $address = new Address($cart->id_address_delivery);
        }

        if (!$address || !$address->id) {
            return 0;
        }

        // Obtener estado/provincia para usar como fallback
        $state = new State($address->id_state);
        if (!$state || !$state->id) {
            return 0;
        }

        // Primero intentar obtener coste por provincia (más confiable para la calculadora)
        $province_cost = $this->getShippingCostByProvince($state->iso_code);
        if ($province_cost > 0) {
            return $province_cost;
        }

        // Si no hay coste por provincia configurado, intentar con transportistas
        $id_carrier = $cart->id_carrier;
        if (!$id_carrier) {
            // Intentar obtener el primer transportista disponible para el país
            $country = new Country($address->id_country);
            if ($country && $country->id) {
                $id_zone = $country->id_zone;
                $carriers = Carrier::getCarriers(
                    Context::getContext()->language->id, 
                    true, 
                    false, 
                    $id_zone, 
                    null, 
                    Carrier::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
                );
                if (!empty($carriers)) {
                    $id_carrier = $carriers[0]['id_carrier'];
                }
            }
        }

        if (!$id_carrier) {
            // Si no hay transportista, devolver 0 (ya intentamos coste por provincia)
            return 0;
        }

        // Verificar que el transportista esté disponible para la zona del país
        $country = new Country($address->id_country);
        if ($country && $country->id) {
            $carrier = new Carrier($id_carrier);
            if ($carrier && $carrier->id) {
                // Verificar si el transportista está disponible para esta zona
                $carrier_zones = $carrier->getZones();
                $country_zone = $country->id_zone;
                $carrier_available = false;
                foreach ($carrier_zones as $zone) {
                    if ($zone['id_zone'] == $country_zone) {
                        $carrier_available = true;
                        break;
                    }
                }
                
                if (!$carrier_available) {
                    // Transportista no disponible para este país, devolver 0
                    return 0;
                }
            }
        }

        // Calcular coste usando el método de PrestaShop
        try {
            $shipping_cost = $cart->getPackageShippingCost($id_carrier, true);
            if ($shipping_cost === false || $shipping_cost === 0) {
                // Si falla o es 0, devolver 0 (ya intentamos coste por provincia)
                return 0;
            }
            return (float)$shipping_cost;
        } catch (Exception $e) {
            // Si hay error, devolver 0 (ya intentamos coste por provincia)
            return 0;
        }
    }

    /**
     * Obtener coste de envío por provincia
     */
    public function getShippingCostByProvince($province_code)
    {
        $sql = 'SELECT shipping_cost 
                FROM `' . _DB_PREFIX_ . 'shipping_calculator_delays`
                WHERE province_code = "' . pSQL($province_code) . '"';

        $result = Db::getInstance()->getValue($sql);
        return $result ? (float)$result : 0;
    }

    /**
     * Guardar coste de envío por provincia
     */
    public function saveShippingCostByProvince($province_code, $shipping_cost)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'shipping_calculator_delays`
                SET shipping_cost = ' . (float)$shipping_cost . '
                WHERE province_code = "' . pSQL($province_code) . '"';

        return Db::getInstance()->execute($sql);
    }
}

