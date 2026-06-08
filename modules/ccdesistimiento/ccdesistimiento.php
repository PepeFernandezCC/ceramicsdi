<?php
/**
 * Ceramic Connection - Solicitud de desistimiento
 * Prestashop 1.7 / 8.x module.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class CcDesistimiento extends Module
{
    public function __construct()
    {
        $this->name = 'ccdesistimiento';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Ceramic Connection';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Solicitud de desistimiento');
        $this->description = $this->l('Permite al cliente solicitar el desistimiento de un pedido desde su area de cliente.');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('displayOrderDetail')
            && $this->registerHook('displayCustomerAccount')
            && Configuration::updateValue('CC_DESISTIMIENTO_DAYS', 14)
            && Configuration::updateValue('CC_DESISTIMIENTO_EMAIL', 'info@ceramicconnection.es')
            && Configuration::updateValue('CC_DESISTIMIENTO_PHONE', '+34 623 240 148')
            && Configuration::updateValue('CC_DESISTIMIENTO_RETURN_ADDRESS', 'Avenida Real de Extremadura, 9, Onda 12200, Espana')
            && Configuration::updateValue('CC_DESISTIMIENTO_DELIVERED_STATES', '5')
            && Configuration::updateValue('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES', '');
    }

    public function uninstall()
    {
        return Configuration::deleteByName('CC_DESISTIMIENTO_DAYS')
            && Configuration::deleteByName('CC_DESISTIMIENTO_EMAIL')
            && Configuration::deleteByName('CC_DESISTIMIENTO_PHONE')
            && Configuration::deleteByName('CC_DESISTIMIENTO_RETURN_ADDRESS')
            && Configuration::deleteByName('CC_DESISTIMIENTO_DELIVERED_STATES')
            && Configuration::deleteByName('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES')
            && parent::uninstall();
    }

    private function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'cc_desistimiento` (
            `id_cc_desistimiento` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_order` INT UNSIGNED NOT NULL,
            `id_customer` INT UNSIGNED NOT NULL,
            `customer_name` VARCHAR(255) NOT NULL,
            `customer_email` VARCHAR(255) NOT NULL,
            `products` TEXT NULL,
            `comment` TEXT NULL,
            `status` VARCHAR(64) NOT NULL DEFAULT "pendiente",
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_cc_desistimiento`),
            KEY `id_order` (`id_order`),
            KEY `id_customer` (`id_customer`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';
        return Db::getInstance()->execute($sql);
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitCcDesistimientoConfig')) {
            Configuration::updateValue('CC_DESISTIMIENTO_DAYS', (int) Tools::getValue('CC_DESISTIMIENTO_DAYS'));
            Configuration::updateValue('CC_DESISTIMIENTO_EMAIL', pSQL(Tools::getValue('CC_DESISTIMIENTO_EMAIL')));
            Configuration::updateValue('CC_DESISTIMIENTO_PHONE', pSQL(Tools::getValue('CC_DESISTIMIENTO_PHONE')));
            Configuration::updateValue('CC_DESISTIMIENTO_RETURN_ADDRESS', pSQL(Tools::getValue('CC_DESISTIMIENTO_RETURN_ADDRESS')));
            Configuration::updateValue('CC_DESISTIMIENTO_DELIVERED_STATES', pSQL(Tools::getValue('CC_DESISTIMIENTO_DELIVERED_STATES')));
            Configuration::updateValue('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES', pSQL(Tools::getValue('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES')));
            $output .= $this->displayConfirmation($this->l('Configuracion guardada.'));
        }
        return $output . $this->renderForm() . $this->renderRequestsTable();
    }

    private function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array('title' => $this->l('Configuracion')),
                'input' => array(
                    array('type' => 'text', 'label' => $this->l('Plazo en dias'), 'name' => 'CC_DESISTIMIENTO_DAYS'),
                    array('type' => 'text', 'label' => $this->l('Email interno'), 'name' => 'CC_DESISTIMIENTO_EMAIL'),
                    array('type' => 'text', 'label' => $this->l('Telefono/WhatsApp'), 'name' => 'CC_DESISTIMIENTO_PHONE'),
                    array('type' => 'text', 'label' => $this->l('Direccion devolucion'), 'name' => 'CC_DESISTIMIENTO_RETURN_ADDRESS'),
                    array('type' => 'text', 'label' => $this->l('Estados entregado IDs separados por coma'), 'name' => 'CC_DESISTIMIENTO_DELIVERED_STATES', 'desc' => $this->l('Ejemplo: 5. Si usas otro estado Entregado, indica su ID.')),
                    array('type' => 'text', 'label' => $this->l('Categorias excluidas IDs separados por coma'), 'name' => 'CC_DESISTIMIENTO_EXCLUDED_CATEGORIES', 'desc' => $this->l('Productos a medida/personalizados. Si un producto pertenece a estas categorias, no se ofrecera desistimiento.')),
                ),
                'submit' => array('title' => $this->l('Guardar')),
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCcDesistimientoConfig';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (array('CC_DESISTIMIENTO_DAYS','CC_DESISTIMIENTO_EMAIL','CC_DESISTIMIENTO_PHONE','CC_DESISTIMIENTO_RETURN_ADDRESS','CC_DESISTIMIENTO_DELIVERED_STATES','CC_DESISTIMIENTO_EXCLUDED_CATEGORIES') as $key) {
            $helper->fields_value[$key] = Configuration::get($key);
        }
        return $helper->generateForm(array($fields_form));
    }

    private function renderRequestsTable()
    {
        $rows = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'cc_desistimiento` ORDER BY date_add DESC LIMIT 50');
        $html = '<div class="panel"><h3>Ultimas solicitudes</h3><table class="table"><thead><tr><th>ID</th><th>Pedido</th><th>Cliente</th><th>Email</th><th>Productos</th><th>Estado</th><th>Fecha</th></tr></thead><tbody>';
        if (!$rows) {
            $html .= '<tr><td colspan="7">Sin solicitudes.</td></tr>';
        } else {
            foreach ($rows as $row) {
                $html .= '<tr><td>' . (int) $row['id_cc_desistimiento'] . '</td><td>#' . (int) $row['id_order'] . '</td><td>' . htmlspecialchars($row['customer_name']) . '</td><td>' . htmlspecialchars($row['customer_email']) . '</td><td>' . htmlspecialchars($row['products']) . '</td><td>' . htmlspecialchars($row['status']) . '</td><td>' . htmlspecialchars($row['date_add']) . '</td></tr>';
            }
        }
        $html .= '</tbody></table></div>';
        return $html;
    }

    public function hookDisplayCustomerAccount($params)
    {
        $this->context->smarty->assign(array(
            'cc_desistimiento_orders_link' => $this->context->link->getPageLink('history', true),
        ));
        return $this->display(__FILE__, 'views/templates/hook/customer_account.tpl');
    }

    public function hookDisplayOrderDetail($params)
    {
        $order = null;
        if (isset($params['order']) && $params['order'] instanceof Order) {
            $order = $params['order'];
        } elseif (Tools::getValue('id_order')) {
            $order = new Order((int) Tools::getValue('id_order'));
        }
        if (!$order || !Validate::isLoadedObject($order)) {
            return '';
        }
        if (!$this->canRequestWithdrawal($order)) {
            return '';
        }
        $existing = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'cc_desistimiento` WHERE id_order=' . (int) $order->id);
        if ($existing > 0) {
            $this->context->smarty->assign(array('cc_desistimiento_already_requested' => true));
            return $this->display(__FILE__, 'views/templates/hook/order_detail.tpl');
        }
        $this->context->smarty->assign(array(
            'cc_desistimiento_already_requested' => false,
            'cc_desistimiento_link' => $this->context->link->getModuleLink($this->name, 'request', array('id_order' => (int) $order->id), true),
        ));
        return $this->display(__FILE__, 'views/templates/hook/order_detail.tpl');
    }

    public function canRequestWithdrawal(Order $order)
    {
        $customer = $this->context->customer;
        if (!$customer || !$customer->isLogged() || (int) $customer->id !== (int) $order->id_customer) {
            return false;
        }
        if ($this->orderHasOnlyExcludedProducts($order)) {
            return false;
        }
        $deliveryDate = $this->getDeliveryDate($order);
        if (!$deliveryDate) {
            return false;
        }
        $days = (int) Configuration::get('CC_DESISTIMIENTO_DAYS');
        if ($days <= 0) {
            $days = 14;
        }
        $limit = strtotime('+' . $days . ' days', strtotime($deliveryDate));
        return time() <= $limit;
    }

    public function getDeliveryDate(Order $order)
    {
        $deliveredStates = array_filter(array_map('intval', explode(',', (string) Configuration::get('CC_DESISTIMIENTO_DELIVERED_STATES'))));
        if (!$deliveredStates) {
            $deliveredStates = array(5);
        }
        $history = $order->getHistory((int) $order->id_lang, false, false, 0);
        foreach ($history as $state) {
            if (in_array((int) $state['id_order_state'], $deliveredStates, true)) {
                return $state['date_add'];
            }
        }
        if (in_array((int) $order->current_state, $deliveredStates, true)) {
            return $order->date_upd;
        }
        return false;
    }

    public function orderHasOnlyExcludedProducts(Order $order)
    {
        $excluded = array_filter(array_map('intval', explode(',', (string) Configuration::get('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES'))));
        if (!$excluded) {
            return false;
        }
        $products = $order->getProducts();
        if (!$products) {
            return false;
        }
        foreach ($products as $productRow) {
            $idProduct = (int) $productRow['product_id'];
            $categories = Product::getProductCategories($idProduct);
            if (!array_intersect($excluded, array_map('intval', $categories))) {
                return false;
            }
        }
        return true;
    }
}
