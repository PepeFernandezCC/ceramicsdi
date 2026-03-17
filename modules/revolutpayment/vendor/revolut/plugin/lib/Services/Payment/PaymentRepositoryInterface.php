<?php

namespace Revolut\Plugin\Services\Payment;

use Revolut\Plugin\Services\Payment\PaymentModel;
use Revolut\Plugin\Types\PaymentMethod;

interface PaymentRepositoryInterface
{
    public function findFromSession(PaymentMethod $paymentMethod): ?PaymentModel;
    public function findByPlatformCartId(string $cartId, PaymentMethod $paymentMethod): ?PaymentModel;
    public function findByPlatformOrderId(string $orderId): ?PaymentModel;
    public function findByRevolutOrderId(string $revolutOrderId): ?PaymentModel;
    public function findByRevolutOrderToken(string $revolutOrderToken): ?PaymentModel;
    public function add(PaymentModel $payment): void;
    public function save(PaymentModel $payment): void;
}
