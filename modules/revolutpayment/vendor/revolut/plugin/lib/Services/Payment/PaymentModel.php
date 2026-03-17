<?php

namespace Revolut\Plugin\Services\Payment;

use Revolut\Plugin\Types\PaymentMethod;

class PaymentModel
{
    /** @var string */
    private $paymentId;

    /** @var string */
    private $token;

    /** @var string|null */
    private $orderId;

    /** @var PaymentMethod */
    private $paymentMethod;

    private function __construct(
        string $paymentId,
        string $token,
        ?PaymentMethod $paymentMethod,
        ?int $orderId,
    ) {
        $this->paymentId = $paymentId;
        $this->orderId = $orderId;
        $this->paymentMethod = $paymentMethod;
        $this->token = $token;
    }

    public static function newWithPayment(string $paymentId, string $token, PaymentMethod $paymentMethod)
    {
        return new self(
            $paymentId,
            $token,
            $paymentMethod,
            null
        );
    }

    public function attachOrderId($orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getPublicToken(): string
    {
        return $this->token;
    }
}
