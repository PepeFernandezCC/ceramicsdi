<?php
/**
 * 2021 4webs
 *
 * DEVELOPED By 4webs.es Prestashop Platinum Partner
 *
 * @author    4webs
 * @copyright 4webs 2021
 * @license   4webs
 * @version 5.1.4
 * @category payment_gateways
 */

namespace Fourwebs\PaypalWithFee;

if (!defined('_PS_VERSION_')) { exit; }

require_once _PS_MODULE_DIR_ . 'paypalwithfee/vendor/autoload.php';

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;
use PayPalCheckoutSdk\Payments\CapturesGetRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalHttp\HttpException;
use Tools;

/**
 * @author: Borja J.
 * 2020-09-10
 */
class Paypalwf
{
    private $user;
    private $pwd;
    public $errors = array();
    
    private $env;
    private $client;

    
    public function __construct($user = false, $pwd = false)
    {
        if ($user) {
            $this->user = $user;
        } else {
            $this->user = \Configuration::get('PPAL_FEE_USER');
        }
        if ($pwd) {
            $this->pwd = $pwd;
        } else {
            $this->pwd = \Configuration::get('PPAL_FEE_PASS');
        }

        //We are in production or sandbox mode?
        $sandbox = \Configuration::get('PPAL_FEE_TEST');
        if ($sandbox) {
            $this->env = new SandboxEnvironment($this->user, $this->pwd);
        } else {
            $this->env = new ProductionEnvironment($this->user, $this->pwd);
        }
        //Set up the requests API
        $this->client = new PayPalHttpClient($this->env);
    }
    
    /**
     * Creates a order object (OrdersCreateObject) and then returns it (IT DON'T EXECUTE ANYTHING)
     *
     * @param array $items - The order items see: https://github.com/paypal/Checkout-PHP-SDK#code
     * @param array $endpoints - The order items see: https://github.com/paypal/Checkout-PHP-SDK#code
     *
     * @return OrdersCreateRequest - The request to execute it later.
     */
    public function createOrder($items, $endpoints)
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');

        $body = [
            'intent' => 'CAPTURE',
            'purchase_units' => $items,
            'application_context' => $endpoints
        ];
        $request->body = $body;

        return $request;
    }

    /**
     * This function can get a payment status after the payment was made.
     */
    public function createPaymentGetRequest($transactionID)
    {
        return new CapturesGetRequest($transactionID);
    }

    /**
     * This function will create a payment refund order
     *
     * @param string $reason - A text to send to the paypal server with the refund reason.
     * @param string $orderRef - The order reference to set as the paypal's invoice id.
     * @param string $currencyCode - The currency ISO code (3 digits) EX: USD, EUR, GBP, AUD.
     * @param string $transactionID - The original transaction id from paypal.
     * @param string $amount - The quantity to refund (only for non-refund orders)
     * @param bool $fullRefund - Is a full refund?
     */
    public function createPaymentRefund(
        string $reason,
        string $orderRef,
        string $currencyCode,
        string $transactionID,
        string $amount,
        bool $fullRefund
    ) {
        $request = new CapturesRefundRequest($transactionID);
        $request->prefer('return=representation');

        $body = [];
        if (!$fullRefund) {
            $body['amount'] =  [
                'value' => $amount,
                'currency_code' => $currencyCode
            ];
        }

        $body['invoice_id'] = $orderRef;
        if (Tools::strlen($reason)) {
            $body['note_to_payer'] = $reason;
        }

        $request->body = $body;

        return $request;
    }

    /**
     * Generates the request and points to the endpoint to process it
     *
     * @param $request - The well-formed request to send to the server
     *
     * @return array - An array with the response data, two keys, result -> if done or fail and data
     */
    public function executeRequest($request)
    {
        try {
            return [
                'result' => 'ok',
                'data' => $this->client->execute($request)
            ];
        } catch (HttpException  $e) {
            return [
                'result' => 'ko',
                'data' => $e->getMessage()
            ];
        }
    }

    /**
     * This function will check if the paypal payment was correctly captured (successfully payment)
     *
     * @param string $token - The token string
     *
     * @return array - A mixed array with the response
     */
    public function checkOrderPayment($token)
    {
        $request = new OrdersCaptureRequest($token);
        $request->prefer('return=representation');
        try {
            // Call API with your client and get a response for your call
            return [
                'result' => 'ok',
                'data' => $this->client->execute($request)
            ];
        } catch (HttpException $e) {
            return [
                'result' => 'ko',
                'data' => $e->getMessage()
            ];
        }
    }

    /**
     * @legacy
     *
     * Writes the error into the log
     *
     * @param $cart - The cart object
     * @param $paypal_params - The paypal requuest params
     * @param $paypal_error - The paypal response
     */
    public function logError($cart, $paypal_params, $paypal_error)
    {
        if (is_object($cart)) {
            $log_name = date('y_m_d_h_i_s') . $cart->id;
        } else {
            $log_name = date('y_m_d_h_i_s') . $cart;
        }


        $log_file = _PS_MODULE_DIR_ . 'paypalwithfee/log/log_' . $log_name . '.log';
        $handle = fopen($log_file, 'w') or die('Cannot open file:  ' . $log_file);

        fwrite($handle, print_r($paypal_error, true));
        fwrite($handle, print_r($paypal_params, true));
        if (is_object($cart)) {
            fwrite($handle, print_r($cart, true));
        }
        fclose($handle);
    }
}
