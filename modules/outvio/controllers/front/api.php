<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class OutvioApiModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    protected $salt = '!0utv10s3cr3t#';

    public function postProcess()
    {
        $action = Tools::getValue('action', '');
        if (!in_array($_SERVER['REQUEST_METHOD'], array('POST', 'GET'))) {
            $this->response(array(
                'success' => false,
                'message' => Tools::displayError(
                    sprintf('This request method %s is not allowed', $_SERVER['REQUEST_METHOD'])
                )
            ));
        }
        if ($action == 'setStatus') {
            $id_order = (int)Tools::getValue('idOrder', 0);
            $status = Tools::getValue('status', '');
            $token = Tools::getValue('token', '');
            if (md5($id_order.$status) != $token) {
                $this->response(array(
                    'success' => false,
                    'message' => Tools::displayError('Unauthorized')
                ) + (Configuration::get('OUTVIO_DEBUG_MODE') ? array('validToken' => md5($id_order.$status)) : array()));
            }
            $strtolower_status = Tools::strtolower($status);
            $statuses = $this->module->getApiStatusList();
            if (!in_array($strtolower_status, $statuses)) {
                
                $states = new OrderState(1);
                $order_states = $states->getOrderStates($this->context->language->id);
                $found = false;
                foreach($order_states as $state) {
                  if($state['name'] === $status) {
                    $found = true;
                    $id_order_state = (int)$state['id_order_state'];
                  }
                }
                if(!$found) {
                  $this->response(array(
                    'success' => false,
                    'message' => Tools::displayError('Wrong status name')
                  ) + (Configuration::get('OUTVIO_DEBUG_MODE') ? array('status' => $status, 'availableStatuses' => implode(', ', $statuses)) : array()));
                }
            }
            $order = new Order($id_order);
            if (!Validate::isLoadedObject($order)) {
                $this->response(array(
                    'success' => false,
                    'message' => Tools::displayError('Order is not found')
                ));
            }
            /* build configuration key */
            $configKey = Tools::strtoupper($this->module->name.'_'.$status.'_STATUS');
            /* get assigned order state id */
            $id_order_state = isset($id_order_state) ? $id_order_state : (int)Configuration::get($configKey, null, null, null, null);
            if ($id_order_state) {
                $order_state = new OrderState($id_order_state, $this->context->language->id);
                if (!Validate::isLoadedObject($order_state)) {
                    $this->response(array(
                        'success' => true,
                        'message' => Tools::displayError('Order state is not found')
                    ));
                }
                /* disable send request to outvio */
                $_POST['disable_hook_update_status_outvio'] = true;
                /* get current order state */
                $current_order_state = $order->getCurrentOrderState();
                if ($order_state->id == $current_order_state->id) {
                    $this->response(array(
                        'success' => true,
                        'message' => $this->module->l('Currently, the order has this state')
                    ));
                }
                /* change order state */
                if ($this->setOrderState($order, $order_state->id)) {
                    $this->response(array(
                        'success' => true,
                        'message' => 'Order state successfully changed'
                    ));
                } else {
                    $this->response(array(
                        'success' => false,
                        'message' => 'State could not be changed'
                    ));
                }
            } else {
                $this->response(array(
                    'success' => false,
                    'message' => Tools::displayError(
                        sprintf('The status %s is not assigned in the shop.', Tools::ucfirst($status))
                    )
                ));
            }
          } elseif ($action == 'setTrackingNumber') {
            $id_order = (int)Tools::getValue('idOrder', 0);
            $tracking_number = Tools::getValue('trackingNumber', '');
            $token = Tools::getValue('token', '');
            if (md5($id_order.$tracking_number) != $token) {
                $this->response(array(
                    'success' => false,
                    'message' => Tools::displayError('Unauthorized')
                ) + (Configuration::get('OUTVIO_DEBUG_MODE') ? array('validToken' => md5($id_order.$tracking_number)) : array()));
            }
            
            $order = new Order($id_order);
            if (!Validate::isLoadedObject($order)) {
                $this->response(array(
                    'success' => false,
                    'message' => Tools::displayError('Order is not found')
                ));
            }
            /* get assigned order state id */
            $has_tracking_number = $order->getWsShippingNumber();
            
            /* disable send request to outvio */
            $_POST['disable_hook_update_status_outvio'] = true;
            /* change order state */
            if ($order->setWsShippingNumber($tracking_number)) {
                $this->response(array(
                    'success' => true,
                    'message' => 'Order tracking number successfully changed'
                ));
            } else {
                $this->response(array(
                    'success' => false,
                    'message' => 'Tracking number could not be changed'
                ));
            }
        } elseif ($action == 'getData') {
            try {
            $this->response(array(
                'success' => true,
                'data' => array(
                    'statuses' => $this->getStatuses(),
                    'payment_methods' => $this->getPaymentMethods(),
                    'shipping_methods' => $this->getShippingMethods()
                ),
            ));
            } catch (Exception $e) {
                print_r($e->getMessage());
                PrestaShopLogger::addLog($e->getMessage());
            }
        } elseif ($action == 'fetchOldOrders') {
            try {
            $module = new Outvio();
            $this->response(array(
                'success' => true,
                'data' => array(
                    'orders' => $module->sendOrdersByFilter(Tools::getValue('from', ''))
                ),
            ));
            } catch (Exception $e) {
                print_r($e->getMessage());
                PrestaShopLogger::addLog($e->getMessage());
            }
        } elseif ($action == 'setConfig') {
            $token = Tools::getValue('token', '');
            $input = Tools::file_get_contents('php://input');
            if (hash_hmac('sha256', $input, $this->salt) != $token) {
                $this->response(array(
                    'success' => false,
                    'message' => Tools::displayError('Unauthorized')
                ));
            }
            $body = json_decode($input, true);
            foreach ($body as $name => $item) {
                if (in_array($name, array('api_key','api_mode','debug_mode', 'use_map', 'google_maps_api_key'))) {
                    Configuration::updateGlobalValue('OUTVIO_'.Tools::strtoupper($name), $item);
                } else {
                    foreach ($item as $key => $value) {
                        Configuration::updateGlobalValue('OUTVIO_'.Tools::strtoupper($key), !is_array($value) ? $value : implode('|', $value));
                    }
                }
            }
            $this->response(array(
                'success' => true,
                'message' => 'Config set',
            ));
        } else {
            $this->response(array(
                'success' => false,
                'message' => Tools::displayError('The action is undefined')
            ));
        }
    }

    private function getStatuses()
    {
        $states = OrderState::getOrderStates($this->context->language->id);
        return array_map(function ($item) {
            return array('id' => $item['id_order_state'], 'name' => $item['name']);
        }, $states);
    }

    private function getPaymentMethods()
    {
        $payment_methods = [];
        foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
            $module = Module::getInstanceByName($payment['name']);
            if (Validate::isLoadedObject($module) && $module->active) {
                $payment_methods[] = array('id' => $module->name, 'name' => $module->displayName);
            }
        }
        return $payment_methods;
    }

    /**
     * @param Order $order
     * @param int $id_new_order_state
     * @return bool
     */
    private function setOrderState(Order $order, $id_new_order_state)
    {
        $history = new OrderHistory();
        $history->id_order = $order->id;
        $history->id_employee = 0;

        $use_existings_payment = false;

        if (!$order->hasInvoice()) {
            $use_existings_payment = true;
        }

        $history->changeIdOrderState((int)$id_new_order_state, $order, $use_existings_payment);

        $carrier = new Carrier($order->id_carrier, $order->id_lang);
        $templateVars = array();

        if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
            $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
        }

        return $history->addWithemail(true, $templateVars);
    }

    private function getShippingMethods()
    {
        $methods = array();
        $res = Db::getInstance()->executeS("SELECT DISTINCT name FROM "._DB_PREFIX_."carrier c WHERE c.`deleted` = 0");
        foreach ($res as $row) {
            $methods[] = $row['name'];
        }
        return $methods;
    }

    private function response(array $response)
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        if (_PS_VERSION_ >= '8.0.0') die(json_encode($response));
        die(Tools::jsonEncode($response));
    }
}
