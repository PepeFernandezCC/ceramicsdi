<?php

namespace Revolut\Plugin\Services\Payment;

use Revolut\Plugin\Services\Customer\CustomerParams;
use Revolut\Plugin\Types\PaymentAmount;
use Revolut\Plugin\Types\PaymentToken;

interface PaymentServiceInterface
{
    public function revolutPay(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken;

    public function cardPayment(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken;

    public function paymentRequest(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken;

    public function revolutPayFastCheckout(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken;

    public function payByBank(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken;

    public function cardSubscriptionPayment(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken;

    public function refund(string $orderId, PaymentAmount $amount);
    public function cancel(string $orderId);
    public function capture(string $orderId);
}
