<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Revolut
 * @copyright Since 2020 Revolut
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class RevolutpaymentValidationModuleFrontController extends ModuleFrontController
{
    /**
     * Main module instance
     *
     * @var RevolutPayment
     */
    public $module;

    /**
     * Prestashop context instance
     *
     * @var Context
     */
    protected $context;

    public function postProcess()
    {
        if (!empty(Tools::getValue('_rp_fr'))) {
            return $this->redirectCheckoutWithError(Tools::getValue('_rp_fr'));
        }

        // retrieve via form request payload
        $public_id = Tools::getValue('public_id');
        $payment_method_key = Tools::getValue('payment_method', 'revolut');

        // in case revolut pay mobile redirect_url
        if (!empty(Tools::getValue('_rp_oid'))) {
            $public_id = Tools::getValue('_rp_oid');
            $payment_method_key = 'revolut_pay';
        }

        if (!$public_id) {
            $errorMessage = 'Validation error: public_id not found';
            PrestaShopLogger::addLog('Error: ' . $errorMessage, 3);

            return $this->redirectCheckoutWithError($errorMessage);
        }

        $revolut_order_details = $this->module->getRevolutOrderByPublicId($public_id);

        if (empty($revolut_order_details) || empty($revolut_order_details['id_revolut_order']) || empty($revolut_order_details['id_cart'])) {
            return $this->redirectCheckoutWithError('Order process has been interrupted , Missing required payment parameters');
        }

        $revolut_order = RevolutApiHelper::retrieveRevolutOrder($revolut_order_details['id_revolut_order']);

        if (in_array($revolut_order['state'], ['CANCELLED', 'FAILED', 'PENDING'])) {
            return $this->redirectCheckoutWithError('Something went wrong while taking the payment. Payment should be retried. Payment Status: ' . $revolut_order['state']);
        }

        if ($revolut_order_details['id_cart'] != $this->context->cart->id) {
            $cart = new Cart($revolut_order_details['id_cart']);
            $this->context->cookie->id_lang = $cart->id_lang;
            $this->context->cookie->id_guest = $cart->id_guest;
            $this->context->cookie->id_customer = $cart->id_customer;
            $this->context->shop->id_shop_group = $cart->id_shop_group;
            $this->context->shop->id = $cart->id_shop;
            $this->context->customer->id = $cart->id_customer;

            $this->context->cart = $cart;
            $this->context->cookie->write();
            CartRule::autoAddToCart($this->context);
        }

        $psOrderAmount = $this->module->createRevolutAmount($this->context->cart->getOrderTotal(), $this->context->currency->iso_code);
        $revolutOrderAmount = isset($revolut_order['order_amount']['value']) ? $revolut_order['order_amount']['value'] : 0;

        if ($psOrderAmount !== $revolutOrderAmount) {
            RevolutApiHelper::cancelRevolutOrder($revolut_order['id_revolut_order']);

            PrestaShopLogger::addLog('Error: Order (' . $revolut_order['id'] . ') 
                canceled due to the total amount differences PS order amount : '
                . $psOrderAmount
                . ' -  Revolut order amount : ' . $revolutOrderAmount, 3);

            $this->redirectCheckoutWithError('Something went wrong. The payment has been canceled, please try again.');
        }

        /*
         * Verify if this module is enabled and if the cart has
         * a valid customer, delivery address and invoice address
         */
        if (
            !$this->module->active
            || $this->context->cart->id_customer == 0
            || $this->context->cart->id_address_delivery == 0
            || $this->context->cart->id_address_invoice == 0
        ) {
            $this->redirectCheckoutWithError();
        }

        /** @var CustomerCore $customer */
        $customer = new Customer($this->context->cart->id_customer);

        /*
         * Check if this is a vlaid customer account
         */
        if (!Validate::isLoadedObject($customer)) {
            $this->redirectCheckoutWithError();
        }

        if (!isset($revolut_order_details['record_id'])) {
            PrestaShopLogger::addLog('Error: unable to find revolut order table record id column', 3);
        }

        $revolut_order_db_record_id = $revolut_order_details['record_id'];

        $ps_order_id = (int) $this->module->createPrestaShopOrder($payment_method_key, $revolut_order_db_record_id);

        if (!$ps_order_id) {
            PrestaShopLogger::addLog('Error: Can not create an order', 3);

            return $this->redirectCheckoutWithError();
        }

        $this->module->processRevolutOrderResult($ps_order_id, $revolut_order_details['id_revolut_order'], $payment_method_key);

        $this->module->updateRevolutOrderLineItemsAndShippingData($ps_order_id, $revolut_order_details['id_revolut_order']);

        Tools::redirect($this->module->getOrderConfirmationLink($this->context->cart->id, $customer->secure_key, $ps_order_id, $payment_method_key));
    }

    protected function redirectCheckoutWithError($error_message = 'Something went wrong while taking the payment')
    {
        if ($this->module->isPs17) {
            return Tools::redirect('order?_rp_fr=' . $error_message);
        }

        Tools::redirect('index.php?controller=order&step=3&_rp_fr=' . $error_message);
    }
}
