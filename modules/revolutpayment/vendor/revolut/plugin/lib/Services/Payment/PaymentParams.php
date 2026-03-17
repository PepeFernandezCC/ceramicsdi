<?php

namespace Revolut\Plugin\Services\Payment;

use Revolut\Plugin\Types\PaymentAmount;

class PaymentParams
{
    /** @var PaymentAmount */
    private $paymentAmount;

    /** @var string */
    private $cartId;

    /** @var string */
    private $domain;

    public function __construct(int $cartId, PaymentAmount $paymentAmount, ?string $domain)
    {
        $this->paymentAmount = $paymentAmount;
        $this->cartId = $cartId;
        $this->domain = $domain;
    }

    public function getCartId(): string
    {
        return $this->cartId;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getPaymentAmount(): PaymentAmount
    {
        return $this->paymentAmount;
    }
}
