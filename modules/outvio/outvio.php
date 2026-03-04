<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Outvio OU
 * @copyright 2017-2025 Outvio OU
 * @license   Outvio OU
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Outvio extends Module
{
    private $api_payment_methods = array();
    private $api_order_status = array();

    private $payment_modules = null;
    private $mode_develop = true;

    /**
     * @var array
     */
    private $api_statuses = array();

    public function __construct()
    {
        $this->name = 'outvio';
        $this->tab = 'shipping_logistics';
        $this->version = '2.2.6';
        $this->author = 'Outvio';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '8.9.99');
        $this->bootstrap = true;

        $this->module_key = '8b362dead2556faa77e7e7d8fc1c28ee';

        parent::__construct();

        $this->displayName = $this->l('Outvio');
        $this->description = $this->l('Outvio shipping integration.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('OUTVIO_NAME'))  $this->warning = $this->l('No name provided');

        $this->api_payment_methods = array(
            'credit_card' => array(
                'title' => $this->l('Credit Card'),
                'name' => 'credit_card',
            ),
            'bank_transfer' => array(
                'title' => $this->l('Bank transfer'),
                'name' => 'bank_transfer',
            ),
            'c_o_d' => array(
                'title' => $this->l('Cash on delivery'),
                'name' => 'c_o_d',
            ),
        );

        $this->api_order_status = array(
            'paid' => array(
                'title' => $this->l('Paid'),
                'name' => 'paid'
            ),
            'not_paid' => array(
                'title' => $this->l('Not Paid'),
                'name' => 'not_paid'
            )
        );

        $this->api_statuses = array(
            array(
                'alias' => 'processing',
                'name' => 'Processing',
                'desc' => 'When entering in shipping queue change status to ("Processing")'
            ),
            array(
                'alias' => 'shipped',
                'name' => 'Shipped',
                'desc' => 'When entering the tracking tab, after label is printed, change status to ("Shipped")'
            ),
            array(
                'alias' => 'delivered',
                'name' => 'Delivered',
                'desc' => 'When being delivered, change status to ("Delivered")'
            ),
            array(
                'alias' => 'returned',
                'name' => 'Returned',
                'desc' => 'When being returned by client, change status to ("Returned")'
            ),
            array(
                'alias' => 'refunded',
                'name' => 'Refunded',
                'desc' => 'When return is refunded change status to ("Refunded")'
            ),
            array(
                'alias' => 'deliveredandpaid', //have to write without camelCase to keep API flow
                'name' => 'Delivered and Paid',
                'desc' => 'When we get COD from courier ("Delivered and paid COD")'
            )
        );
        //echo "ok";
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Outvio settings'),
                    'icon' => 'icon-cog',
                ),

                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Display map:'),
                        'name' => 'OUTVIO_USE_MAP',
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => '1',
                                    'name' => $this->l('Yes')
                                ),
                                array(
                                    'id_option' => '0',
                                    'name' => $this->l('No')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                ),

                'submit' => array('title' => $this->l('Save')),

                'buttons' => array(
                    array(
                        'href' => 'https://outvio.com',
                        'target' => '_blank',
                        'title' => $this->l('To configure all other settings, please, go to Outvio.com'),
                    )
                )
            )
        );

        $helper = new HelperForm();
        $helper->name_controller = $this->name;
        $helper->submit_action = 'submit'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure=outvio&tab_module=shipping_logistics&module_name=outvio';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array(
                'OUTVIO_USE_MAP' => Tools::getValue('OUTVIO_USE_MAP', Configuration::get('OUTVIO_USE_MAP')),
            ),
            'languages' => $this->context->controller->getLanguages(),
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit'.$this->name)) {
            Configuration::updateValue('OUTVIO_USE_MAP', strval(Tools::getValue('OUTVIO_USE_MAP')));
        }

        $form = $this->renderForm();

        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/outvio-hello.tpl');
        $tpl->assign('form', $form);

        return $tpl->fetch();
    }

    public function reset()
    {
        include(_PS_MODULE_DIR_ . 'outvio/sql/install.php');
        return true;
    }

    public function install()
    {
        include(_PS_MODULE_DIR_ . 'outvio/sql/install.php');
        Configuration::updateGlobalValue('OUTVIO_API_MODE', 0);
        Configuration::deleteByName('OUTVIO_SEND_STATUS');
        Configuration::updateGlobalValue('OUTVIO_API_KEY', '');

        //$this->newCarrier();
        return parent::install() && $this->registerHook('displayHeader') && $this->registerHook('actionValidateOrder') && $this->registerHook('actionOrderStatusPostUpdate') && $this->registerHook('displayBeforeCarrier') && $this->registerHook('actionObjectOrderAddBefore') && $this->registerHook('actionOrderEdited');
        // actionOrderEdited  // to fetch updated products
    }

    public function uninstall()
    {
        include(_PS_MODULE_DIR_ . 'outvio/sql/uninstall.php');
        Configuration::deleteByName('OUTVIO_SEND_STATUS');
        Configuration::deleteByName('OUTVIO_API_MODE');
        Configuration::deleteByName('OUTVIO_API_KEY');
        Configuration::deleteByName('OUTVIO_GOOGLE_MAPS_API_KEY');
        return parent::uninstall();
    }

    /*Shipping module*/
    public static function getSelectedPoint($id_customer)
    {
        $sql = 'SELECT data
            FROM ' . _DB_PREFIX_ . 'outvio_cart
            WHERE id_customer=' . (int)$id_customer;
        $data = Db::getInstance()->getValue($sql);
        if ($data) {
            $data = html_entity_decode($data);
            if (_PS_VERSION_ >= '8.0.0') return json_decode($data);
            return Tools::jsonDecode($data);
        } else {
            return false;
        }
    }

    public static function saveSelectedPoint($data, $reset = false)
    {

        $id_customer = Context::getContext()->customer->id;
        if (!$id_customer) {
            return false;
        }
        if ($reset && $id_customer) {
            $query = 'DELETE FROM ' . _DB_PREFIX_ . 'outvio_cart
                    WHERE id_customer=' . (int)$id_customer;
            return Db::getInstance()->execute($query);
        }

        $is_exists_sql = 'SELECT id
            FROM ' . _DB_PREFIX_ . 'outvio_cart
            WHERE id_customer=' . (int)$id_customer;
        $exists = Db::getInstance()->executeS($is_exists_sql);

        if ((count($exists) > 0) && ($id = $exists[0]['id'])) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'outvio_cart
                        SET data = \'' . pSQL($data) . '\'
                        WHERE id=' . (int)$id;
        } else {
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'outvio_cart
                        SET data = \'' . pSQL($data) . '\',
                            id_customer=' . (int)$id_customer;
        }

        return Db::getInstance()->execute($sql);
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'outvio/views/css/front.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'outvio/views/css/chosen.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'outvio/views/js/chosen/chosen.jquery.js');
        if (_PS_VERSION_ < '1.7.0.0') {
            $this->context->smarty->assign('oversion', '161');
            $this->context->controller->addJS(_PS_MODULE_DIR_ . 'outvio/views/js/front16.js?v=' . random_int(1, 999));
        } else {
            // 1.7
            $this->context->smarty->assign('oversion', '17');
            $this->context->controller->addJS(_PS_MODULE_DIR_ . 'outvio/views/js/front.js');
        }
        return '';
    }

    public function hookActionObjectOrderAddBefore($params)
    {
        /** Order $order */
        $order = $params['object'];
        //$cart = $this->context->cart;
        $new_address_id = $this->addPickupAddress($order);
        if ($new_address_id) {
            $params['object']->id_address_delivery = $new_address_id;
        }
        file_put_contents(_PS_MODULE_DIR_ . 'outvio/good.txt', 111);
        // to stop order creation you need to redirect from this hook
    }

    public function addPickupAddress($order)
    {
        $id_lang = Context::getContext()->language->id;
        $id_customer = (int)$order->id_customer;
        //$id_customer = 3;
        $pickupPoint = self::getSelectedPoint($id_customer);
        if (!$pickupPoint) {
            return 0;
        }
        $id_cart = $order->id_cart;
        //$id_cart = 7;
        $cart = new Cart($id_cart);
        $id_address_default = (int)$cart->id_address_delivery;
        $old_address_delivery = new Address($id_address_default, $id_lang);

        //print_r($old_address_delivery);
        $new_address_delivery = $old_address_delivery;
        $new_address_alias = $pickupPoint->courier . ' ' . $pickupPoint->id;
        $new_address_delivery_exists = self::addresAliasExist($new_address_alias, $id_customer);
        //echo "$new_address_delivery_exists == $old_address_delivery->id == $new_address_delivery_exists<br>";
        if (!$new_address_delivery_exists) {
            /*change address fields*/
            $new_address_delivery->alias = $new_address_alias;
            $new_address_delivery->address1 = $pickupPoint->address;
            $new_address_delivery->address2 = '';
            $new_address_delivery->city = $pickupPoint->city;
            $new_address_delivery->postcode = $pickupPoint->postcode;
            $new_address_delivery->add();
            $new_address_id = $new_address_delivery->id;
        } else {
            if ($old_address_delivery->id == $new_address_delivery_exists) {
                return 0;
            }
            $new_address_id = $new_address_delivery_exists;
        }
        $cart->id_address_delivery = $new_address_id;
        if ($new_address_id) {
            $cart->update();
        }
        return $new_address_id;
    }

    public static function addresAliasExist($alias, $id_customer)
    {
        $query = new DbQuery();
        $query->select('id_address');
        $query->from('address');
        $query->where('alias = \'' . pSQL($alias) . '\'');
        $query->where('id_customer = ' . (int)$id_customer);
        $query->where('deleted = 0');

        return Db::getInstance()->getValue($query);
    }

    public function hookDisplayBeforeCarrier($params)
    {
        $id_address = (int)$params['cart']->id_address_delivery;
        $output = $this->assignCarriersContent($id_address);
        return $output;
    }

    public function assignCarriersContent($id_address)
    {
        $id_lang = $this->context->language->id;
        $address_delivery = new Address($id_address, $id_lang);
        $country_code = Tools::strtoupper(Country::getIsoById($address_delivery->id_country));
        $use_map = Configuration::get('OUTVIO_USE_MAP');
        if (!isset($use_map)) $use_map = '1';

        $address = array(
            "countryCode" => "$country_code",
            "postCode" => "{$address_delivery->postcode}",
            "city" => "{$address_delivery->city}",
            "address" => "{$address_delivery->address1}"
        );
        $google_maps_api_key = Configuration::get('OUTVIO_GOOGLE_MAPS_API_KEY');
        $ajax_url = $this->context->link->getModuleLink($this->name, 'frontajax');
        $this->context->smarty->assign('delivery_address', $address);
        $this->context->smarty->assign('outvio_ajax_url', $ajax_url);
        $this->context->smarty->assign('google_maps_api_key', $google_maps_api_key);
        $this->context->smarty->assign('url_shop', Context::getContext()->shop->getBaseURL(true));
        $this->context->smarty->assign('outvio_use_map', strval($use_map));

        if (_PS_VERSION_ < '1.7.0.0') {
            $this->context->smarty->assign('oversion', '161');
        } else {
            $this->context->smarty->assign('oversion', '17');
        }

        return $this->context->smarty->fetch($this->local_path . 'views/templates/front/before_carriers.tpl');
    }

    public static function getCarrierSearchPointsAjax()
    {
        $id_carrier = Tools::getValue('id_carrier');
        $search_text = Tools::getValue('search_text');
        $result = self::getCarrierSearchPoints($id_carrier, $search_text);
        return $result;
    }

    public static function getCarrierPointsAjax()
    {
        $outv_token = Tools::getValue('outv_token');
        $id_carrier = Tools::getValue('id_carrier');
        $result = self::getCarrierPoints($id_carrier);
        return $result;
    }

    public static function getCarrierSearchPoints($id_carrier, $search_text)
    {
        $context = Context::getContext();
        $id_address = Context::getContext()->cart->id_address_delivery;
        $id_lang = $context->language->id;
        $address_delivery = new Address($id_address, $id_lang);
        $country_code = Tools::strtoupper(Country::getIsoById($address_delivery->id_country));
        $country_name = Country::getNameById($id_lang, $address_delivery->id_country);
        if ($search_text && $search_text != '') {
            $search_text = $country_name . ', ' . $search_text;
        } else {
            $search_text = false;
        }
        if (!$search_text) {
            return array();
        }
        $sql = 'SELECT name
            FROM ' . _DB_PREFIX_ . 'carrier
            WHERE id_carrier=' . (int)$id_carrier;

        $carrier_name = Db::getInstance()->getValue($sql);
        $address = array(
            "countryCode" => "$country_code",
            "postCode" => "{$address_delivery->postcode}",
            "city" => "{$address_delivery->city}",
            "address" => "{$address_delivery->address1}"
        );

        $response_json = self::requestSearchCenter($address, $carrier_name, $search_text);
        $response = _PS_VERSION_ >= '8.0.0'
            ? json_decode($response_json)
            : Tools::jsonDecode($response_json);

        if ($response && $response->success == true) {
            $points = $response->results;

        } else {
            $points = array();
        }

        return $points;
    }

    public static function getCarrierPoints($id_carrier)
    {
        $context = Context::getContext();
        $id_address = $context->cart->id_address_delivery;
        $id_lang = $context->language->id;
        $address_delivery = new Address($id_address, $id_lang);
        $country_code = Tools::strtoupper(Country::getIsoById($address_delivery->id_country));
        $sql = 'SELECT name
            FROM ' . _DB_PREFIX_ . 'carrier
            WHERE id_carrier=' . (int)$id_carrier;

        $carrier_name = Db::getInstance()->getValue($sql);

        $address = array(
            "countryCode" => "$country_code",
            "postCode" => "{$address_delivery->postcode}",
            "city" => "{$address_delivery->city}",
            "address" => "{$address_delivery->address1}"
        );

        $response_json = self::requestShipping($address, $carrier_name);
        $response =  _PS_VERSION_ >= '8.0.0'
            ? json_decode($response_json)
            : Tools::jsonDecode($response_json);

        if ($response && $response->success == 1) {
            $points = $response->points;
            foreach ($points as $key => $point) {
                $points[$key]->json_info = _PS_VERSION_ >= '8.0.0'
                    ? json_encode($point)
                    : Tools::jsonEncode($point);
            }
        } else {
            $points = array();
        }

        return $points;
    }

    //API

    private function getOutvioUrl()
    {
        if (Configuration::get('OUTVIO_API_MODE')) return 'https://api.dev.outvio.com/v1';
        return 'https://api.outvio.com/v1';
    }

    private function getShippingSearchApiUrl()
    {
        return $this->getOutvioUrl() . '/courier/location';
    }

    private function getShippingApiUrl()
    {
        return $this->getOutvioUrl() . '/courier/points/prestashop';
    }

    private function getLocationApiUrl()
    {
        return $this->getOutvioUrl() . '/courier/location';
    }

    public static function requestSearchCenter($address, $carrier_name, $search)
    {
        $module = new Outvio;
        $shop_url = Context::getContext()->shop->getBaseURL(true);
        //$shop_url = 'http://prestashopdemo.zlabsolutions.com/1761';
        $length = strlen($shop_url);
        if ($shop_url[$length - 1] == '/') {
            $shop_url = substr($shop_url, 0, $length - 1);
        }
        $params = array(
            'API_KEY' => Configuration::get('OUTVIO_API_KEY'),
            'courier' => $carrier_name,
            'url' => $shop_url,
            'address' => $address,
            'search' => isset($search) ? $search : $address['countryCode'] . ', ' . $address['postCode']
        );
        $requestContent = json_encode($params);

        $apiResponse = '';
        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            $fileName = '2-' . Tools::passwdGen(10) . '.txt';
            $handle = fopen($module->getDebugFolder() . DIRECTORY_SEPARATOR . $fileName, 'w');
            fwrite($handle, "Endpoint: " . $module->getShippingSearchApiUrl() . " \n");
        }

        /* send order data */
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $module->getShippingSearchApiUrl());
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestContent);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            $apiResponse = curl_exec($ch);

            if (Configuration::get('OUTVIO_DEBUG_MODE')) {
                fwrite($handle, "curl_exec done \n\n");
                if (curl_errno($ch)) {
                    $error_msg = curl_error($ch);
                    fwrite($handle, $error_msg);
                    $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    fwrite($handle, "\n\n curl_getinfo\n");
                    fwrite($handle, $response);
                    fwrite($handle, "\n\n apiResponse\n");
                    fwrite($handle, $apiResponse);
                }

            }
            curl_close($ch);
        } else {
            $API_OPTS = array(
                'http' => array(
                    'method' => "POST",
                    'header' => 'Content-Type: application/json',
                    'content' => $requestContent
                )
            );
            $API_CONTEXT = stream_context_create($API_OPTS);
            $apiResponse = Tools::file_get_contents($module->getShippingSearchApiUrl(), false, $API_CONTEXT);
        }
        /* end send order data */

        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            fwrite($handle, "\n\n");
            fwrite($handle, "Request:\n");
            fwrite($handle, $requestContent);
            fwrite($handle, "\n\n");
            fwrite($handle, "Response:\n");
            fwrite($handle, $apiResponse);
            fclose($handle);
        }
        return $apiResponse;
    }

    /**
     * Send request method
     * @param array $params
     */
    public static function requestShipping(array $address, $carrier_name, $search = false)
    {
        $module = new Outvio;
        $response = $module->_requestShipping($address, $carrier_name, $search);
        return $response;
    }

    private function _requestShipping(array $address, $carrier_name, $search)
    {
        $shop_url = Context::getContext()->shop->getBaseURL(true);
        $length = strlen($shop_url);
        if ($shop_url[$length - 1] == '/') {
            $shop_url = substr($shop_url, 0, $length - 1);
        }
        $params = array(
            'API_KEY' => Configuration::get('OUTVIO_API_KEY'),
            'courier' => $carrier_name,
            'url' => $shop_url,
            'address' => $address
        );
        if ($search) {
            $params['search'] = $search;
        }

        $requestContent = json_encode($params);

        $apiResponse = '';
        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            $fileName = '1-' . Tools::passwdGen(10) . '.txt';
            $handle = fopen($this->getDebugFolder() . DIRECTORY_SEPARATOR . $fileName, 'w');
            fwrite($handle, "Endpoint: " . $this->getShippingApiUrl() . " \n");
        }
        /* send order data */
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getShippingApiUrl());
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestContent);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            $apiResponse = curl_exec($ch);

            if (Configuration::get('OUTVIO_DEBUG_MODE')) {
                fwrite($handle, "curl_exec done \n\n");
                if (curl_errno($ch)) {
                    $error_msg = curl_error($ch);
                    fwrite($handle, $error_msg);
                    $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    fwrite($handle, "\n\n curl_getinfo\n");
                    fwrite($handle, $response);
                    fwrite($handle, "\n\n apiResponse\n");
                    fwrite($handle, $apiResponse);
                }

            }
            curl_close($ch);
        } else {
            $API_OPTS = array(
                'http' => array(
                    'method' => "POST",
                    'header' => 'Content-Type: application/json',
                    'content' => $requestContent
                )
            );
            $API_CONTEXT = stream_context_create($API_OPTS);
            $apiResponse = Tools::file_get_contents($this->getShippingApiUrl(), false, $API_CONTEXT);
        }
        /* end send order data */

        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            fwrite($handle, "\n\n");
            fwrite($handle, "Request:\n");
            fwrite($handle, $requestContent);
            fwrite($handle, "\n\n");
            fwrite($handle, "Response:\n");
            fwrite($handle, $apiResponse);
            fclose($handle);
        }
        return $apiResponse;
    }

    /**
     * Send request method
     * @param array $params
     */
    public static function requestLocations($address)
    {
        $module = new Outvio;
        $response = $module->_requestLocations($address);
        return $response;
    }

    private function _requestLocations($address)
    {
        $shop_url = Context::getContext()->shop->getBaseURL(true);
        $length = strlen($shop_url);
        if ($shop_url[$length - 1] == '/') {
            $shop_url = substr($shop_url, 0, $length - 1);
        }
        $params = array(
            'API_KEY' => Configuration::get('OUTVIO_API_KEY'),
            'url' => $shop_url,
            'search' => $address
        );

        $requestContent = json_encode($params);

        $apiResponse = '';
        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            $fileName = '1-' . Tools::passwdGen(10) . '.txt';
            $handle = fopen($this->getDebugFolder() . DIRECTORY_SEPARATOR . $fileName, 'w');
            fwrite($handle, "Endpoint: " . $this->getShippingApiUrl() . " \n");
        }
        /* send order data */
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getLocationApiUrl());
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestContent);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            $apiResponse = curl_exec($ch);

            if (Configuration::get('OUTVIO_DEBUG_MODE')) {
                fwrite($handle, "curl_exec done \n\n");
                if (curl_errno($ch)) {
                    $error_msg = curl_error($ch);
                    fwrite($handle, $error_msg);
                    $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    fwrite($handle, "\n\n curl_getinfo\n");
                    fwrite($handle, $response);
                    fwrite($handle, "\n\n apiResponse\n");
                    fwrite($handle, $apiResponse);
                }

            }
            curl_close($ch);
        } else {
            $API_OPTS = array(
                'http' => array(
                    'method' => "POST",
                    'header' => 'Content-Type: application/json',
                    'content' => $requestContent
                )
            );
            $API_CONTEXT = stream_context_create($API_OPTS);
            $apiResponse = Tools::file_get_contents($this->getShippingApiUrl(), false, $API_CONTEXT);
        }
        /* end send order data */

        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            fwrite($handle, "\n\n");
            fwrite($handle, "Request:\n");
            fwrite($handle, $requestContent);
            fwrite($handle, "\n\n");
            fwrite($handle, "Response:\n");
            fwrite($handle, $apiResponse);
            fclose($handle);
        }

        return $apiResponse;
    }

    /*End Shipping module*/

    public function check($id_order)
    {
        $params = $this->getOrderInfo($id_order);
        $order = new Order($id_order);
        if (Configuration::get('OUTVIO_SEND_STATUS') !== false and !in_array($order->current_state, $this->getSendStatus())) {
            die('false by send status');
        }

        $params['OUTVIO_PARAMS'] = array(
            'API_KEY' => Configuration::get('OUTVIO_API_KEY'),
            'PLUGIN_VERSION' => $this->version,
            'CMS_ID' => 'prestashop',
            'CMS_VERSION' => _PS_VERSION_
        );

        $requestContent = json_encode($params);

        $apiResponse = '';

        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            $fileName = $id_order . '-resend-' . date('H-m-d_His') . '.txt';
            $handle = fopen($this->getDebugFolder() . DIRECTORY_SEPARATOR . $fileName, 'w');
            fwrite($handle, "Endpoint:\n");
            fwrite($handle, $this->getApiUrl());
            fwrite($handle, "\n\n");
            fwrite($handle, "Request:\n");
            fwrite($handle, $requestContent);
            fwrite($handle, "\n\n");
            fwrite($handle, "Response:\n");
            fwrite($handle, $apiResponse);
            fclose($handle);
        }

        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/outvio-check.tpl');
        $tpl->assign(array(
            'endpoint' => $this->getApiUrl(),
            'request' => $requestContent,
            'response' => $apiResponse,
        ));
        return $tpl->fetch();
    }

    /**
     * Get order info
     * @param int $id_order
     * @return array
     */
    private function getOrderInfo($id_order)
    {
        $order = new Order((int)$id_order);
        if (!Validate::isloadedObject($order)) {
            return array();
        }
        // get the order status from the db
        $query = new DbQuery();
        $query->select('DISTINCT osl.`name` AS `order_status`');
        $query->from('orders', 'o');
        $query->leftJoin('order_state', 'os', '(os.`id_order_state` = o.`current_state`)');
        $query->leftJoin('order_state_lang', 'osl', '(os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int)Context::getContext()->language->id . ')');
        $query->where('o.`id_order` = ' . (int)$id_order);
        $order_status = Db::getInstance()->getValue($query);

        $currency = new Currency($order->id_currency);

        $carrier = new Carrier($order->id_carrier, $order->id_lang);
        $orderCarrier = new OrderCarrier($order->getIdOrderCarrier());

        $address_invoice = new Address($order->id_address_invoice, $order->id_lang);
        $invoice_country_code = Tools::strtoupper(Country::getIsoById($address_invoice->id_country));
        //$this->_debug($address_invoice);
        $address_delivery = new Address($order->id_address_delivery, $order->id_lang);
        $delivery_country_code = Tools::strtoupper(Country::getIsoById($address_delivery->id_country));

        $client_invoice_phone = trim($address_invoice->phone_mobile);
        if ($client_invoice_phone == '') {
            $client_invoice_phone = $address_invoice->phone;
        }

        $client_delivery_phone = trim($address_delivery->phone_mobile);
        if ($client_delivery_phone == '') {
            $client_delivery_phone = $address_delivery->phone;
        }

        $state1 = new State($address_invoice->id_state, $order->id_lang);
        $state2 = new State($address_delivery->id_state, $order->id_lang);

        $customer = new Customer($order->id_customer);

        // $order->setInvoice(true);
        // $order->setDelivery();

        // $invoice = new OrderInvoice($order->invoice_number);

        $order_payment_status = '';
        $payment_method = '';

        if ($order->module) {
            foreach ($this->api_payment_methods as $k => $item) {
                $key = $this->name . '_' . $k;
                $val = Configuration::get(Tools::strtoupper($key));
                if ($val) {
                    $values = explode('|', $val);
                    if (in_array($order->module, $values)) {
                        $payment_method = $item['title'];
                        break;
                    }
                }
            }
        }

        foreach ($this->api_order_status as $k => $item) {
            $key = $this->name . '_' . $k;
            $val = Configuration::get(Tools::strtoupper($key));
            if ($val) {
                $values = explode('|', $val);
                if (in_array($order->current_state, $values)) {
                    $order_payment_status = $item['title'];
                    break;
                }
            }
        }

        if (empty($address_invoice->address1)) {
            $invoice_address = $address_delivery->address1 . ($address_delivery->address2 ? ' - ' . $address_delivery->address2 : '');
        } else {
            $invoice_address = $address_invoice->address1 . ($address_invoice->address2 ? ' - ' . $address_invoice->address2 : '');
        }

        $delivery_address = $address_delivery->address1 . ($address_delivery->address2 ? ' - ' . $address_delivery->address2 : '');
        $pickupPoint = self::getSelectedPoint($order->id_customer);

        // save info for debug
        file_put_contents(_PS_MODULE_DIR_ . 'outvio/carrier.txt', print_r($carrier, true));
        file_put_contents(_PS_MODULE_DIR_ . 'outvio/order.txt', print_r($order, true));
//	      var_dump('Test', $carrier, '------------------------', $order); exit(0);
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        $total_products_price = Tools::ps_round($order->total_paid_tax_incl, 2);
        if (($order_carrier->carrier_name == 'Correos' || $order_carrier->carrier_name == 'Internal Postal Service') && $total_products_price == 0 ) {
            $total_products_price = 1;
        }
        $result = array(
            'id' => (int)$order->id,
            'originalId' => $order->reference,
            'dateTime' => gmdate('Y-m-d\TH:i:s.000\Z', strtotime($order->date_add)),
            'status' => $order_status,
            'payment' => array(
                'status' => $order_payment_status,
                'method' => $payment_method
            ),
            'total' => $total_products_price,
            'currency' => $currency->iso_code,
            'tax' => ($order->getTotalProductsWithTaxes() - $order->getTotalProductsWithoutTaxes()),
            'products' => array(),
            'originalLocale' => Context::getContext()->language->iso_code,
            'invoicingCompany' => (empty($address_invoice->company) ? $address_delivery->company : $address_invoice->company),
            'invoicingCompanyTaxId' => (empty($address_invoice->vat_number) ? $address_delivery->vat_number : $address_invoice->vat_number),
            'client' => array(
                'invoicing' => array(
                    "name" => (
                    empty(trim($address_invoice->firstname . ' ' . $address_invoice->lastname))
                        ? trim($address_delivery->firstname . ' ' . $address_delivery->lastname)
                        : trim($address_invoice->firstname . ' ' . $address_invoice->lastname)
                    ),
                    "postcode" => (empty($address_invoice->postcode) ? $address_delivery->postcode : $address_invoice->postcode),
                    "countryCode" => (empty($invoice_country_code) ? $delivery_country_code : $invoice_country_code),
                    "state" => (empty($state1->name) ? $state2->name : $state1->name),
                    "city" => (empty($address_invoice->city) ? $address_delivery->city : $address_invoice->city),
                    "address" => $invoice_address,
                    "email" => $customer->email,
                    "phone" => (empty($client_invoice_phone) ? $client_delivery_phone : $client_invoice_phone),
                    "company" => !empty($address_invoice->company) ? $address_invoice->company : $address_delivery->company,
                ),
                'delivery' => array(
                    "name" => trim($address_delivery->firstname . ' ' . $address_delivery->lastname),
                    "postcode" => $address_delivery->postcode,
                    "countryCode" => $delivery_country_code,
                    "state" => $state2->name,
                    "city" => $address_delivery->city,
                    "address" => $delivery_address,
                    "email" => $customer->email,
                    "phone" => $client_delivery_phone,
                    "company" => $address_delivery->company,
                    // "comment" => $address_delivery->other,
                    // get firts message from order created by customer
                    "comment" => (string)Db::getInstance()->getValue(
                        "SELECT message FROM " . _DB_PREFIX_ . "message
                            WHERE
                                id_order=" . (int)$order->id . "
                                AND id_customer=" . (int)$order->id_customer . "
                                AND private=0
                            ORDER BY
                                id_message ASC"
                    )
                ),
                'pickupPoint' => $pickupPoint,
            ),
            'shipping' => array(
                'price' => (float)Tools::ps_round($order->total_shipping_tax_incl, 2),
                'method' => $carrier->name,
                'trackingNumber' => isset($order->shipping_number) ? $order->shipping_number : $orderCarrier->tracking_number
            ),
            'gift' => array(
                'wrapAsGift' => (boolean)$order->gift,
                'comment' => $order->gift_message,
                'recyclable' => (boolean)$order->recyclable,
//			          'price' => 0
            ),
            // 'invoiceUrl' => $this->context->link->getPageLink('pdf-invoice', null, null, array('id_order'=>$order->id,'secure_key'=>$order->secure_key)),
            // 'invoiceNumber' => $invoice->getInvoiceNumberFormatted($order->id_lang),
            'api_url' => $this->context->link->getModuleLink($this->name, 'api', array(), true)
        );

        if (!empty($order_carrier->tracking_number) && !empty($order_carrier->carrier_name)) {
            $result['shipping']['method'] = $order_carrier->carrier_name;
            $result['shipping']['trackingNumber'] = $order_carrier->tracking_number;
        }

//		    $result['shipping']['carrier_name2'] = $carrier->carrier_name;
//        file_put_contents(_PS_MODULE_DIR_.'/outvio/result.txt', print_r($result, true));
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $base_dir__ssl = $protocol_link . Tools::getShopDomainSsl() . __PS_BASE_URI__;

        $products = $order->getProducts();

        foreach ($products as $product) {
            if ($product['cache_is_pack']) {
                $pack_hs = false;

                $pack_info = $this->getProductInfo($product, $order->id_lang, $base_dir__ssl);
                //adding the pack products in this list for shipping, but all with 0 price tag so the original order amount is not affected
                $pack_products = Pack::getItems((int)$product['product_id'], $order->id_lang);
                $pack_count = 0;

                foreach ($pack_products as $pack_product) {

                    $pack_prod['product_id'] = $pack_product->id;
                    $pack_prod['price'] = 0;//($product['price']/$pack_count) * $pack_product->pack_quantity;
                    $pack_prod['tax_rate'] = 0;//$product['tax_rate'];
                    $pack_prod['unit_price_tax_incl'] = 0;//($product['price'] * (100 + $product['tax_rate'])/100)/$pack_count;
                    $pack_prod['product_quantity'] = $pack_product->pack_quantity;
                    $pack_prod['product_reference'] = $pack_product->reference;
                    $pack_prod['weight'] = 0;//$product['weight']/$pack_count;
                    $cover = $pack_product->id_pack_product_attribute
                        ? Product::getCombinationImageById($pack_product->id_pack_product_attribute, $order->id_lang)
                        : Product::getCover($pack_product->id);
                    $pack_prod['image'] = Context::getContext()->link->getImageLink(
                        $pack_product->link_rewrite, $cover ? $cover['id_image'] : '',
                        ImageType::getFormattedName('home')
                    );
                    $pack_product_info = $this->getProductInfo($pack_prod, $order->id_lang, $base_dir__ssl, $product['product_name']);
                    if (false === $pack_hs && isset($pack_product_info['hsCode']) && $pack_product_info['hsCode'] != '') {
                        $pack_hs = $pack_product_info['hsCode'];
                    }
                    if($pack_product_info!='')
                        $result['products'][] = $pack_product_info;
                }
                if (!(isset($pack_info['hsCode']) && $pack_info['hsCode'] != '')) {
                    $pack_info['hsCode'] = $pack_hs;
                }
                //adding the pack as a product with the correct price and weight
                if($pack_info!='')
                    $result['products'][] = $pack_info;
            } else {
                $productItem = $this->getProductInfo($product, $order->id_lang, $base_dir__ssl);
                if($productItem!='')
                    $result['products'][] = $productItem;
            }
        }

        foreach ($result as &$data) {
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $j => $p) {
                            if (is_null($p)) {
                                $data[$k][$j] = '';
                            }
                        }
                    } elseif (is_null($v)) {
                        $data[$k] = '';
                    }
                }
            } elseif (is_null($data)) {
                $data = '';
            }
        }

        return $result;
    }

    /**
     * @param array $product
     * @param int $id_lang
     * @param string $base_dir__ssl
     * @param string $pack_name
     *
     * @return array
     */
    private function getProductInfo($product, $id_lang, $base_dir__ssl, $pack_name = '')
    {
        $p = new Product($product['product_id'], false, $id_lang);
        $price = (float)Tools::ps_round((float)$product['product_price_wt'], 2);
        $discountedPrice = (float)Tools::ps_round($product['unit_price_tax_incl'], 2);
        $discountedPrice = $discountedPrice < $price ? $discountedPrice : '';
        if(empty($discountedPrice)){
            $discountedPrice = null;
        }
        $product_name = (string)$p->name;
        if ((string)$product['product_reference'] == 'PPWF'){
            $product_name = 'Recargo Paypal';
        }

        return array(
            "id" => $p->id,
            "name" => $product_name,
            "price" => $price,
            "discountPrice" => $discountedPrice,
            "vat" => (float)$product['unit_price_tax_excl'] / 100 * (float)$product['tax_rate'],
            "vatAmount" => (float)$product['tax_rate'],
            "quantity" => (int)$product['product_quantity'],
            "barcode" => $this->getBarcode($p->id, $product['product_attribute_id'], $product['ean13']),
            "locationInWarehouse" => $this->getLocation($p->id, $product['product_attribute_id'], $product['location']),
            "sku" => (string)$product['product_reference'],
            //"variant" => $this->getProductVariantName($p->id, $product['product_attribute_id'], $id_lang),
            "variant" => $product_name,
            "hsCode" => $this->getHSCode($p->id, $id_lang),
            "weight" => (float)Tools::ps_round($product['product_weight'], 2),
            "pictureUrl" => ($product['image']
                ? is_object($product['image']) ?
                    $base_dir__ssl . 'img/p/' . $product['image']->getExistingImgPath() . '.' . $product['image']->image_format
                    : $product['image']
                : $base_dir__ssl . 'img/p/' . $this->context->language->iso_code . '.jpg'),
            "tags" => $this->getProductOutvioTags($p->id),
            "description" => $this->getProductDescription($p->id, $id_lang),
            "no_return" => $this->getProductReturnable($p->id, $id_lang)
        );
    }

    /**
     * @param int $id_product
     * @param int $id_lang
     * @return array
     */
    private function getHSCode($id_product, $id_lang)
    {
        $res = Db::getInstance()->executeS("SELECT DISTINCT t.name FROM " . _DB_PREFIX_ . "product_tag AS pt
            LEFT JOIN " . _DB_PREFIX_ . "tag AS t ON (t.id_tag=pt.id_tag AND t.id_lang=" . (int)$id_lang . ")
            WHERE (t.name LIKE 'HS %' OR t.name LIKE 'HS%') AND pt.id_product=" . (int)$id_product .
            " ORDER BY t.name ASC");
        $codes = array();
        foreach ($res as $row) {
            $codes[] = $row['name'];
        }
        return isset($codes[0]) ? $codes[0] : '';
    }

    /**
     * @param int $id_product
     * @param int $id_product_attribute
     * @param string $default
     * @return string
     */
    private function getBarcode($id_product, $id_product_attribute, $default)
    {
        // creates the query object
        $query = new DbQuery();

        // adds joins & where clauses for combinations
        if ($id_product_attribute) {
            $query->select('DISTINCT pa.ean13 as name');
            $query->from('product_attribute', 'pa');
            $query->where('pa.id_product = ' . (int)$id_product . ' AND pa.id_product_attribute = ' . (int)$id_product_attribute);
        } else {
            // or just adds a 'where' clause for a simple product
            $query->select('DISTINCT p.ean13 as name');
            $query->from('product', 'p');
            $query->where('p.id_product = ' . (int)$id_product);
        }
        $dbValue = Db::getInstance()->getValue($query);

        return isset($dbValue) && $dbValue ? $dbValue : $default;
    }

    /**
         * @param int $id_product
         * @param int $id_product_attribute
         * @param string $default
         * @return string
         */
        private function getLocation($id_product, $id_product_attribute, $default)
        {
        // Deprecated in version 8+ so return empty String for new versions
            if (_PS_VERSION_ >= '8.0.0') {
                $query = new DbQuery();
                $query->select('DISTINCT sa.location as location');
                $query->from('stock_available', 'sa');
                if ($id_product_attribute) {
                    $query->where('sa.id_product = ' . (int)$id_product . ' AND sa.id_product_attribute = ' . (int)$id_product_attribute);
                } else {
                    $query->where('sa.id_product = ' . (int)$id_product);
                }
                $dbValue = Db::getInstance()->getValue($query);

                return isset($dbValue) && $dbValue ? $dbValue : $default;
            }
            // creates the query object
            $query = new DbQuery();

            // adds joins & where clauses for combinations
            if ($id_product_attribute) {
                $query->select('DISTINCT pa.location as name');
                $query->from('product_attribute', 'pa');
                $query->where('pa.id_product = ' . (int)$id_product . ' AND pa.id_product_attribute = ' . (int)$id_product_attribute);
            } else {
                // or just adds a 'where' clause for a simple product
                $query->select('DISTINCT p.location as name');
                $query->from('product', 'p');
                $query->where('p.id_product = ' . (int)$id_product);
            }
            $dbValue = Db::getInstance()->getValue($query);

            return isset($dbValue) && $dbValue ? $dbValue : $default;
        }

    /**
     * @param int $id_product
     * @param int $id_lang
     * @return array
     */
    private function getProductDescription($id_product, $id_lang)
    {
        $tagsLoaded = Tag::getProductTags($id_product);
        if (!$tagsLoaded) return '';
        $tags = $tagsLoaded[intval(Context::getContext()->cookie->id_lang)]; // may be not work correctly for multi lang installations
        if (is_array($tags)){
            $description_tags = array_filter($tags, function ($i) {
                return strpos($i, "PD") === 0;
            });
        } else {
            $description_tags = [];
        }
        $codes = array();
        foreach ($description_tags as $row) {
            $codes[] = str_replace(array("PD ", "PD"), array("", ""), $row);
        }
        return $codes;
    }

    /**
     * @param int $id_product
     * @param int $id_lang
     * @return array
     */
    private function getProductReturnable($id_product, $id_lang)
    {
        $res = Db::getInstance()->executeS("SELECT DISTINCT t.name FROM " . _DB_PREFIX_ . "product_tag AS pt
                                                                                LEFT JOIN " . _DB_PREFIX_ . "tag AS t ON (t.id_tag=pt.id_tag AND t.id_lang=" . (int)$id_lang . ")
                                                                                WHERE (t.name='no_return' OR t.name='non returnable' OR t.name='no return' OR t.name='NORETURN') AND pt.id_product=" . (int)$id_product .
            " ORDER BY t.name ASC");

        $codes = array();
        foreach ($res as $row) {
            $codes[] = $row['name'];
        }

        return $codes;
    }

    /**
     * Gets the name of a given product, in the given lang
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $id_lang Optional
     * @return string
     * @since 1.5.0
     */
    private function getProductVariantName($id_product, $id_product_attribute = null, $id_lang = null)
    {
        // use the lang in the context if $id_lang is not defined
        if (!$id_lang) {
            $id_lang = (int)Context::getContext()->language->id;
        }

        // creates the query object
        $query = new DbQuery();

        // selects different names, if it is a combination
        if ($id_product_attribute) {
            //$query->select('GROUP_CONCAT(DISTINCT agl.`name`, \' - \', al.name SEPARATOR \', \') as name');
            $query->select('GROUP_CONCAT(DISTINCT al.name SEPARATOR \' \') as name');
        } else {
            $query->select('DISTINCT pl.name as name');
        }

        // adds joins & where clauses for combinations
        if ($id_product_attribute) {
            $query->from('product_attribute', 'pa');
            $query->join(Shop::addSqlAssociation('product_attribute', 'pa'));
            $query->innerJoin('product_lang', 'pl', 'pl.id_product = pa.id_product AND pl.id_lang = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl'));
            $query->leftJoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = pa.id_product_attribute');
            $query->leftJoin('attribute', 'atr', 'atr.id_attribute = pac.id_attribute');
            $query->leftJoin('attribute_lang', 'al', 'al.id_attribute = atr.id_attribute AND al.id_lang = ' . (int)$id_lang);
            $query->leftJoin('attribute_group_lang', 'agl', 'agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = ' . (int)$id_lang);
            $query->where('pa.id_product = ' . (int)$id_product . ' AND pa.id_product_attribute = ' . (int)$id_product_attribute);
        } else {
            // or just adds a 'where' clause for a simple product

            $query->from('product_lang', 'pl');
            $query->where('pl.id_product = ' . (int)$id_product);
            $query->where('pl.id_lang = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl'));
        }

        return Db::getInstance()->getValue($query);
    }

    /**
     * Gets the name of a given product, in the given lang
     *
     * @param int $id_product
     * @return string
     * @since 1.5.0
     */
    private function getProductOutvioTags($id_product)
    {
        $tagsLoaded = Tag::getProductTags($id_product);
        if (!$tagsLoaded) return '';
        $tags = $tagsLoaded[intval(Context::getContext()->cookie->id_lang)]; // may be not work correctly for multi lang installations
        return $tags;
    }

    public function hookActionValidateOrder($params)
    {
        $orderStatus = $params['orderStatus'];
        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            $fileName = $params['order']->id . '-hookActionValidateOrder-' . date('H-m-d_His') . '.txt';
            $handle = fopen($this->getDebugFolder() . DIRECTORY_SEPARATOR . $fileName, 'w');
            fwrite($handle, "Endpoint:\n");
            fwrite($handle, $this->getApiUrl());
            fwrite($handle, "\n\n");
            fwrite($handle, "params:\n");
            fwrite($handle, print_r($params, true));
            fwrite($handle, "\n\n");
        }
        // if ($params === false)
        if (Configuration::get('OUTVIO_SEND_STATUS') !== false and !in_array($orderStatus->id, $this->getSendStatus())) {
            return true;
        }
        $orderid = $params['order']->id;
        // only get all order info when we need to send it to outvio
        $params = $this->getOrderInfo($params['order']->id);
        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            fwrite($handle, "NEW params:\n");
            fwrite($handle, print_r($params, true));
            fwrite($handle, "\n\n");
        }
        if (!empty($params) and empty($params['payment']['status'])) {
            //$params['payment']['status'] = $orderStatus->name;
            foreach ($this->api_order_status as $k => $item) {
                $key = $this->name . '_' . $k;
                $val = Configuration::get(Tools::strtoupper($key));
                if ($val) {
                    $values = explode('|', $val);
                    if (in_array($orderStatus->id, $values)) {
                        $params['payment']['status'] = $item['title'];
                        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
                            $fileName = $orderid . '-hookActionValidateOrder-' . date('H-m-d_His') . '.txt';
                            $handle = fopen($this->getDebugFolder() . DIRECTORY_SEPARATOR . $fileName, 'w');
                            fwrite($handle, "params['payment']['status']:\n");
                            fwrite($handle, $params['payment']['status']);
                        }
                        break;
                    }
                }
            }
        }

        $this->_request($params);
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        if (Tools::getIsset(Tools::getValue('disable_hook_update_status_outvio', '')) && Tools::getValue('disable_hook_update_status_outvio', '') == true) {
            return true;
        }

        $orderStatus = $params['newOrderStatus'];

        // check (Send orders to Outvio with the following statuses)
        if (Configuration::get('OUTVIO_SEND_STATUS') !== false and !in_array($orderStatus->id, $this->getSendStatus())) {
            return true;
        }
        // only get the order info when we need to send it to outvio
        $params = $this->getOrderInfo($params['id_order']);

        if (empty($params) || !is_array($params)) {
            return true;
        }

        if (!empty($params) and empty($params['payment']['status'])) {
            foreach ($this->api_order_status as $k => $item) {
                $key = $this->name . '_' . $k;
                $val = Configuration::get(Tools::strtoupper($key));
                if ($val) {
                    $values = explode('|', $val);
                    if (in_array($orderStatus->id, $values)) {
                        $params['payment']['status'] = $item['title'];
                        break;
                    }
                }
            }
        }

        $this->_request($params);
    }

    public function hookActionOrderEdited(array $params)
    {
        // check (Send orders to Outvio with the following statuses)
        if (Configuration::get('OUTVIO_SEND_STATUS') !== false and !in_array($params['order']->current_state, $this->getSendStatus())) {
            return true;
        }
        // only get the order info when we need to send it to outvio
        $params = $this->getOrderInfo($params['order']->id);

        if (empty($params) || !is_array($params)) {
            return true;
        }

        if (!empty($params) and empty($params['payment']['status'])) {
            foreach ($this->api_order_status as $k => $item) {
                $key = $this->name . '_' . $k;
                $val = Configuration::get(Tools::strtoupper($key));
                if ($val) {
                    $values = explode('|', $val);
                    if (in_array($orderStatus->id, $values)) {
                        $params['payment']['status'] = $item['title'];
                        break;
                    }
                }
            }
        }

        $this->_request($params);
    }

    /**
     * Send request method
     * @param array $params
     */
    private function _request(array $params)
    {
        $params['OUTVIO_PARAMS'] = array(
            'API_KEY' => Configuration::get('OUTVIO_API_KEY'),
            'PLUGIN_VERSION' => $this->version,
            'CMS_ID' => 'prestashop',
            'CMS_VERSION' => _PS_VERSION_
        );

        $requestContent = json_encode($params);

        $apiResponse = '';
        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            $fileName = $params['id'] . '-' . Tools::passwdGen(10) . '.txt';
            $handle = fopen($this->getDebugFolder() . DIRECTORY_SEPARATOR . $fileName, 'w');
            fwrite($handle, "Endpoint: " . $this->getApiUrl() . " \n");
        }
        /* send order data */
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getApiUrl());
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestContent);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            $apiResponse = curl_exec($ch);

            if (Configuration::get('OUTVIO_DEBUG_MODE')) {
                fwrite($handle, "curl_exec done \n\n");
                if (curl_errno($ch)) {
                    $error_msg = curl_error($ch);
                    fwrite($handle, $error_msg);
                    $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    fwrite($handle, "\n\n curl_getinfo\n");
                    fwrite($handle, $response);
                    fwrite($handle, "\n\n apiResponse\n");
                    fwrite($handle, $apiResponse);
                }

            }
            curl_close($ch);
        } else {
            $API_OPTS = array(
                'http' => array(
                    'method' => "POST",
                    'header' => 'Content-Type: application/json',
                    'content' => $requestContent
                )
            );
            $API_CONTEXT = stream_context_create($API_OPTS);
            $apiResponse = Tools::file_get_contents($this->getApiUrl(), false, $API_CONTEXT);
        }
        /* end send order data */

        if (Configuration::get('OUTVIO_DEBUG_MODE')) {
            fwrite($handle, "\n\n");
            fwrite($handle, "Request:\n");
            fwrite($handle, $requestContent);
            fwrite($handle, "\n\n");
            fwrite($handle, "Response:\n");
            fwrite($handle, $apiResponse);
            fclose($handle);
        }
    }

    public function getApiStatusList()
    {
        return array_map(function ($a) {
            return $a['alias'];
        }, $this->api_statuses);
    }

    private function getDebugFolder()
    {
        $dir = dirname(__FILE__);
        if (!file_exists($dir . DIRECTORY_SEPARATOR . 'logs')) {
            mkdir($dir . DIRECTORY_SEPARATOR . 'logs');
            $f = fopen($dir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'index.php', 'w');
            fwrite($f, Tools::getDefaultIndexContent());
            fclose($f);
        }
        return $dir . DIRECTORY_SEPARATOR . 'logs';
    }

    private function _debug($var, $dump = false)
    {
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/outvio-debug.tpl');
        $tpl->assign(array(
            'dump' => $dump,
            'var' => $var,
        ));
        return $tpl->fetch();
    }

    public function getGoogleApiKey()
    {
        if (Configuration::get('OUTVIO_API_MODE')) {
            return 'AIzaSyBdyJ5BbDbJnkLD2x69NQ8qspWRpx6EuK0';
        }

        return Configuration::get('OUTVIO_GOOGLE_MAPS_API_KEY');
    }


    public function getApiUrl()
    {
        return $this->getOutvioUrl() . '/order';
    }

    private function getSendStatus()
    {
        if (Configuration::get('OUTVIO_SEND_STATUS') === false) {
            return array();
        }

        return explode('|', Configuration::get('OUTVIO_SEND_STATUS'));
    }

    public function sendOrdersByFilter($from)
    {
        // be default import for last week
        if (!isset($from) || $from == '')
            $from = date_sub(date_create(), DateInterval::createFromDateString('7 days'))->format('Y-m-d 00:00:00');

        $query = 'SELECT id_order
                 FROM ' . _DB_PREFIX_ . 'orders
                 WHERE current_state IN (' . implode(', ', explode('|', Configuration::get('OUTVIO_SEND_STATUS')))
                 . ') AND date_add >= "' . $from . '"';
        $orders = Db::getInstance()->executeS($query);
        $counters = array('success' => 0, 'total' => 0, 'failed' => 0);

        foreach ($orders as $order) {
        $counters['total'] += 1;
            try {
                $params = $this->getOrderInfo($order);

                if (empty($params) || !is_array($params)) {
                    continue;
                }

                $this->_request($params);
                $counters['success'] += 1;
            } catch (Exception $e) {
                print_r($e->getMessage());
                PrestaShopLogger::addLog($e->getMessage());
                $counters['failed'] += 1;
            }
        }
        return $counters;
    }
}
