<?php
/**
 * CERAMIC CONNECTION - Delivery estimate per order
 *
 * Stores, when a cart becomes an order, the delivery estimate that the
 * shippingcalculator module shows on the web (preparation days, shipping
 * days and estimated delivery date range), so it stays linked to the order
 * and can be queried later (table ps_cc_delivery_estimate).
 *
 * The estimate is a snapshot: it is calculated once in actionValidateOrder
 * and never recalculated, so it keeps what was promised to the customer
 * even if lead times or province delays change afterwards.
 *
 * It is shown to the customer on the order confirmation page and on the
 * order detail page of "My account", and to the staff on the admin order page.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class CcDeliveryEstimate extends Module
{
    const TABLE = 'cc_delivery_estimate';

    public function __construct()
    {
        $this->name = 'ccdeliveryestimate';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'CERAMIC CONNECTION';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CC Plazo de entrega del pedido');
        $this->description = $this->l('Guarda con cada pedido el plazo de preparación y la fecha estimada de entrega calculados por shippingcalculator y se los muestra al cliente.');
        $this->ps_versions_compliancy = ['min' => '1.7.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('actionValidateOrder')
            && $this->registerHook('displayOrderConfirmation')
            && $this->registerHook('displayOrderDetail')
            && $this->registerHook('displayAdminOrderSide');
    }

    /**
     * The table is kept on uninstall on purpose: it holds order history.
     */
    public function uninstall()
    {
        return parent::uninstall();
    }

    protected function installDb()
    {
        return Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE . '` (
                `id_cc_delivery_estimate` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_order` INT(10) UNSIGNED NOT NULL,
                `id_cart` INT(10) UNSIGNED NOT NULL,
                `mode` VARCHAR(16) NOT NULL DEFAULT \'combined\',
                `preparation_days` INT(10) UNSIGNED NOT NULL DEFAULT 0,
                `shipping_days_min` INT(10) UNSIGNED NOT NULL DEFAULT 0,
                `shipping_days_max` INT(10) UNSIGNED NOT NULL DEFAULT 0,
                `delivery_date_min` DATE NOT NULL,
                `delivery_date_max` DATE NOT NULL,
                `province` VARCHAR(255) NOT NULL DEFAULT \'\',
                `summary` VARCHAR(255) NOT NULL DEFAULT \'\',
                `products` TEXT NULL,
                `date_add` DATETIME NOT NULL,
                PRIMARY KEY (`id_cc_delivery_estimate`),
                UNIQUE KEY `id_order` (`id_order`),
                KEY `id_cart` (`id_cart`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;
        ');
    }

    /**
     * Cart -> order: take the snapshot of the estimate.
     * Fired once per order (a cart split into several orders gets one row each).
     */
    public function hookActionValidateOrder($params)
    {
        if (empty($params['order']) || empty($params['cart'])) {
            return;
        }

        try {
            $this->saveEstimate($params['order'], $params['cart']);
        } catch (Exception $e) {
            // Never break the order validation because of the estimate
            PrestaShopLogger::addLog(
                'ccdeliveryestimate: ' . $e->getMessage(),
                2,
                null,
                'Order',
                (int) $params['order']->id
            );
        }
    }

    protected function saveEstimate(Order $order, Cart $cart)
    {
        $calculator = Module::getInstanceByName('shippingcalculator');
        if (!$calculator || !$calculator->active || !method_exists($calculator, 'calculateEstimatedDelivery')) {
            return false;
        }

        $estimate = $calculator->calculateEstimatedDelivery($cart);
        if (empty($estimate)) {
            return false;
        }

        $products = null;
        if (isset($estimate['mode']) && $estimate['mode'] == 'by_product') {
            // Global figures: the slowest product rules the order
            $preparationDays = 0;
            $dateMin = null;
            $dateMax = null;
            $products = [];
            foreach ($estimate['products'] as $product) {
                $preparationDays = max($preparationDays, (int) $product['preparation_days']);
                $dateMin = ($dateMin === null || $product['start_date'] > $dateMin) ? $product['start_date'] : $dateMin;
                $dateMax = ($dateMax === null || $product['end_date'] > $dateMax) ? $product['end_date'] : $dateMax;
                $products[] = [
                    'id_product' => (int) $product['id_product'],
                    'name' => $product['name'],
                    'quantity' => (int) $product['quantity'],
                    'preparation_days' => (int) $product['preparation_days'],
                    'delivery_date_min' => $product['start_date'],
                    'delivery_date_max' => $product['end_date'],
                ];
            }
            if ($dateMin === null) {
                return false;
            }
        } else {
            $preparationDays = (int) $estimate['preparation_days'];
            $dateMin = $estimate['start_date'];
            $dateMax = $estimate['end_date'];
        }

        $shippingMin = (int) $estimate['shipping_days_min'];
        $shippingMax = (int) $estimate['shipping_days_max'];

        // Plain text line, handy to read the estimate without joining columns
        $summary = sprintf(
            'Preparación: %d días hábiles. Envío: %s días hábiles. Entrega estimada: %s - %s',
            $preparationDays,
            $shippingMin == $shippingMax ? $shippingMin : $shippingMin . '-' . $shippingMax,
            date('d/m/Y', strtotime($dateMin)),
            date('d/m/Y', strtotime($dateMax))
        );

        // INSERT IGNORE: the first snapshot of an order is never overwritten
        return Db::getInstance()->insert(
            self::TABLE,
            [
                'id_order' => (int) $order->id,
                'id_cart' => (int) $cart->id,
                'mode' => pSQL(isset($estimate['mode']) ? $estimate['mode'] : 'combined'),
                'preparation_days' => $preparationDays,
                'shipping_days_min' => $shippingMin,
                'shipping_days_max' => $shippingMax,
                'delivery_date_min' => pSQL($dateMin),
                'delivery_date_max' => pSQL($dateMax),
                'province' => pSQL(isset($estimate['province']) ? $estimate['province'] : ''),
                'summary' => pSQL($summary),
                'products' => $products ? pSQL(json_encode($products, JSON_UNESCAPED_UNICODE)) : null,
                'date_add' => date('Y-m-d H:i:s'),
            ],
            true,
            false,
            Db::INSERT_IGNORE
        );
    }

    /**
     * @param int $idOrder
     *
     * @return array|false
     */
    public function getEstimateByOrder($idOrder)
    {
        $row = Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE `id_order` = ' . (int) $idOrder
        );
        if (!$row) {
            return false;
        }

        $row['delivery_date_min_formatted'] = date('d/m/Y', strtotime($row['delivery_date_min']));
        $row['delivery_date_max_formatted'] = date('d/m/Y', strtotime($row['delivery_date_max']));
        $row['products'] = $row['products'] ? json_decode($row['products'], true) : [];
        foreach ($row['products'] as &$product) {
            $product['delivery_date_min_formatted'] = date('d/m/Y', strtotime($product['delivery_date_min']));
            $product['delivery_date_max_formatted'] = date('d/m/Y', strtotime($product['delivery_date_max']));
        }
        unset($product);

        return $row;
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $order = isset($params['order']) ? $params['order'] : null;

        return $this->renderFrontEstimate($order);
    }

    public function hookDisplayOrderDetail($params)
    {
        $order = isset($params['order']) ? $params['order'] : null;

        return $this->renderFrontEstimate($order);
    }

    protected function renderFrontEstimate($order)
    {
        if (!Validate::isLoadedObject($order)) {
            return '';
        }

        $estimate = $this->getEstimateByOrder($order->id);
        if (!$estimate) {
            return '';
        }

        $this->context->smarty->assign([
            'cc_delivery_estimate' => $estimate,
            'cc_delivery_img_dir' => _MODULE_DIR_ . 'shippingcalculator/views/img/',
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/order_estimate.tpl');
    }

    public function hookDisplayAdminOrderSide($params)
    {
        if (empty($params['id_order'])) {
            return '';
        }

        $estimate = $this->getEstimateByOrder((int) $params['id_order']);
        if (!$estimate) {
            return '';
        }

        $this->context->smarty->assign(['cc_delivery_estimate' => $estimate]);

        return $this->display(__FILE__, 'views/templates/hook/admin_order_estimate.tpl');
    }
}
