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
        $data = json_decode($json_data, true)['data'];

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
        } elseif ($response['data']->result->status != 'COMPLETED') { //Response is ok, now check the return code
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
        //Store the address in a variable because may be need after
        $paypalAddress = null;

        $sellerProtection = true;
        $payment_complete = true;
        foreach ($response['data']->result->purchase_units as $pUnit) {
            foreach ($pUnit->payments as $pUnitPayment) {
                if ($pUnitPayment[0]->seller_protection->status != 'ELIGIBLE') {
                    $sellerProtection = false;
                }

                if($pUnitPayment[0]->status != 'COMPLETED'){
                    $payment_complete = false;
                }

                //The transaction id is stored inside the purchase_unit > payments
                if ($transaction_id == null) {
                    $transaction_id = $pUnitPayment[0]->id;
                }

                $paypalAddress = $pUnit->shipping;
            }
        }

        if($payment_complete){
            $status_payment = Configuration::get('PS_OS_PAYMENT');
        }else{
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
            (int)$currency->id,
            false,
            $customer->secure_key,
            null,
            $paypalAddress
        );

        $urlConfirmation = 'index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module='
            . $paypalwithfee->id . '&id_order=' . $paypalwithfee->currentOrder . '&key=' . $customer->secure_key;
        if ($paylater) {
            die(json_encode(['urlConfirmation' => $urlConfirmation]));
        } else {
            Tools::redirect($urlConfirmation);
        }
    }
}