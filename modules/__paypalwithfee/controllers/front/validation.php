<?php
/**
 * 2020 4webs
 *
 * DEVELOPED By 4webs.es Prestashop Platinum Partner
 *
 * @author    4webs
 * @copyright 4webs 2019
 * @license   4webs
 * @version 5.1.4
 * @category payment_gateways
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include(_PS_MODULE_DIR_ . 'paypalwithfee' . DIRECTORY_SEPARATOR . 'api/Paypalwf.php');

use Fourwebs\PaypalWithFee\Paypalwf;

class PayPalwithFeeValidationModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;

    public function initContent()
    {
        parent::initContent();

        $cart = $this->context->cart;
        $customer = new Customer($cart->id_customer);
        $params = array();

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $json_data = Tools::file_get_contents('php://input');
        $decoded_data = json_decode($json_data, true);
        $data = isset($decoded_data['data']) ? $decoded_data['data'] : array();

        $paypal = new Paypalwf(Configuration::get('PPAL_FEE_USER'), Configuration::get('PPAL_FEE_PASS'));
        $paypalwithfee = new Paypalwithfee();

        $paylater = false;
        if (!Tools::getIsset('token')) {
            $token = $data['orderID'];
            $paylater = true;
        } else {
            $token = Tools::getValue('token');
        }

        $response = $paypal->checkOrderPayment($token);

        $paypal->logError($cart, $response, array());

        if ($response['result'] != 'ok') {
            $responseData = is_string($response['data']) ? $response['data'] : print_r($response['data'], true);

            if (strpos($responseData, 'ORDER_ALREADY_CAPTURED') !== false) {
                $id_order = (int) Order::getIdByCartId((int) $cart->id);

                if ($id_order > 0) {
                    $order = new Order($id_order);

                    if (
                        Validate::isLoadedObject($order)
                        && $order->module == 'paypalwithfee'
                        && ((int) $order->current_state == 0 || !$this->orderHasHistory($id_order))
                        && $this->orderHasPaymentRegistered($order)
                    ) {
                        $history = new OrderHistory();
                        $history->id_order = (int) $order->id;
                        $history->changeIdOrderState((int) Configuration::get('PS_OS_PAYMENT'), $order, true);
                        $history->addWithemail(true);

                        $paypal->logError(
                            $this->context->cart,
                            array(
                                'repair' => 'ORDER_ALREADY_CAPTURED',
                                'id_order' => (int) $order->id,
                                'new_state' => (int) Configuration::get('PS_OS_PAYMENT'),
                            ),
                            $responseData
                        );

                        $urlConfirmation = 'index.php?controller=order-confirmation&id_cart=' . (int) $cart->id .
                            '&id_module=' . (int) $paypalwithfee->id .
                            '&id_order=' . (int) $order->id .
                            '&key=' . $customer->secure_key;

                        if ($paylater) {
                            die(json_encode(array('urlConfirmation' => $urlConfirmation)));
                        } else {
                            Tools::redirect($urlConfirmation);
                        }
                    }
                }
            }

            $params['error'] = 'Paymentprocess';
            $this->context->smarty->assign(
                array(
                    'error_paypal' => $paypal->errors,
                    'response_paypal' => $response['data'],
                    'this_path' => $this->module->getPathUri(),
                    'this_path_check' => $this->module->getPathUri(),
                    'this_path_ssl' => Tools::getShopDomainSsl(true, true) .
                        __PS_BASE_URI__ . 'modules/' . $this->module->name . '/'
                )
            );
            $paypal->logError($this->context->cart, $params, $response['data']);
            return $this->setTemplate('module:paypalwithfee/views/templates/front/error.tpl');
        } elseif ($response['data']->result->status != 'COMPLETED') {
            $this->context->smarty->assign(
                array(
                    'error_paypal' => $paypal->errors,
                    'response_paypal' => $response['data'],
                    'this_path' => $this->module->getPathUri(),
                    'this_path_check' => $this->module->getPathUri(),
                    'this_path_ssl' => Tools::getShopDomainSsl(true, true) .
                        __PS_BASE_URI__ . 'modules/' . $this->module->name . '/'
                )
            );
            $paypal->logError($this->context->cart, $token, $response['data']);
            return $this->setTemplate('module:paypalwithfee/views/templates/front/error.tpl');
        } elseif (!$this->module->isValidHash($cart)) {
            $this->context->smarty->assign(
                array(
                    'id' => $response['data']->result->id,
                    'this_path' => $this->module->getPathUri(),
                    'this_path_check' => $this->module->getPathUri(),
                    'this_path_ssl' => Tools::getShopDomainSsl(true, true) .
                        __PS_BASE_URI__ . 'modules/' . $this->module->name . '/'
                )
            );
            $paypal->logError($this->context->cart, $token, $response['data']);
            return $this->setTemplate('module:paypalwithfee/views/templates/front/error_cart.tpl');
        }

        $transaction_id = null;
        $payment_status = $response['data']->result->status;

        $paypalAddress = null;
        $sellerProtection = true;
        $payment_complete = true;

        foreach ($response['data']->result->purchase_units as $pUnit) {
            foreach ($pUnit->payments as $pUnitPayment) {
                if ($pUnitPayment[0]->seller_protection->status != 'ELIGIBLE') {
                    $sellerProtection = false;
                }

                if ($pUnitPayment[0]->status != 'COMPLETED') {
                    $payment_complete = false;
                }

                if ($transaction_id == null) {
                    $transaction_id = $pUnitPayment[0]->id;
                }

                $paypalAddress = $pUnit->shipping;
            }
        }

        if ($payment_complete) {
            $status_payment = Configuration::get('PS_OS_PAYMENT');
        } else {
            $status_payment = Configuration::get('PPAL_FEE_PENDINGSTATE');
        }

        $currency = new Currency($this->context->cart->id_currency);

        $mailFee = $paypalwithfee->getFee($cart);
        $mailVars = array(
            '{fee}' => $mailFee['fee_with_tax'],
        );

        if ($paylater) {
            $payerID = $data['payerID'];
        } else {
            $payerID = Tools::getValue('payerID');
        }

        $paypalwithfee->validateOrder4webs(
            $cart->id,
            $status_payment,
            number_format($response['data']->result->purchase_units[0]->amount->value, 2),
            $this->module->displayName,
            $transaction_id,
            $payerID,
            $sellerProtection,
            $mailVars,
            (int) $currency->id,
            false,
            $customer->secure_key,
            null,
            $paypalAddress
        );

        /*
        * Reparación para el caso:
        * PayPal devuelve COMPLETED, validateOrder4webs crea el pedido,
        * pero PrestaShop lo deja con current_state = 0 o sin historial.
        */
        $id_order = (int) $paypalwithfee->currentOrder;

        if ($id_order <= 0) {
            $id_order = (int) Order::getIdByCartId((int) $cart->id);
        }

        if ($id_order > 0) {
            $order = new Order($id_order);

            if (
                Validate::isLoadedObject($order)
                && $order->module == 'paypalwithfee'
                && ((int) $order->current_state == 0 || !$this->orderHasHistory($id_order))
            ) {
                $history = new OrderHistory();
                $history->id_order = (int) $order->id;
                $history->changeIdOrderState((int) $status_payment, $order, true);
                $history->addWithemail(true);

                $paypal->logError(
                    $this->context->cart,
                    array(
                        'repair' => 'PAYPAL_COMPLETED_WITHOUT_ORDER_STATE',
                        'id_order' => (int) $order->id,
                        'new_state' => (int) $status_payment,
                        'paypal_order_id' => $response['data']->result->id,
                        'transaction_id' => $transaction_id,
                    ),
                    $response['data']
                );
            }
        }

        $urlConfirmation = 'index.php?controller=order-confirmation&id_cart=' . (int) $cart->id .
            '&id_module=' . (int) $paypalwithfee->id .
            '&id_order=' . (int) $id_order .
            '&key=' . $customer->secure_key;

        if ($paylater) {
            die(json_encode(array('urlConfirmation' => $urlConfirmation)));
        } else {
            Tools::redirect($urlConfirmation);
        }
    }
    
    private function orderHasHistory($id_order)
    {
        $sql = 'SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'order_history`
            WHERE `id_order` = ' . (int) $id_order;

        return (int) Db::getInstance()->getValue($sql) > 0;
    }

    private function orderHasPaymentRegistered(Order $order)
    {
        /*
        * Comprobamos si PrestaShop tiene algún pago registrado para la referencia del pedido.
        * Esto evita marcar como pagado un pedido sin prueba local de pago.
        */
        $sql = 'SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'order_payment`
            WHERE `order_reference` = "' . pSQL($order->reference) . '"
            AND `amount` > 0';

        return (int) Db::getInstance()->getValue($sql) > 0;
    }
}