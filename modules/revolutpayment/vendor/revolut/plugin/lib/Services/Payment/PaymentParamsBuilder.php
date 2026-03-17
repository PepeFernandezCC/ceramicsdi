<?php

namespace Revolut\Plugin\Services\Payment;

use Revolut\Plugin\Types\PaymentAmount;

class PaymentParamsBuilder
{
    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var string */
    private $cartId;

    /** @var string */
    private $domain;

    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->amount = null;
        $this->currency = null;
        $this->cartId = null;
        $this->domain = null;
    }

    public function build(): PaymentParams
    {
        return new PaymentParams(
            $this->cartId,
            PaymentAmount::of($this->amount, $this->currency),
            $this->domain
        );
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function setCartId(int $cartId): self
    {
        $this->cartId = $cartId;
        return $this;
    }
}
