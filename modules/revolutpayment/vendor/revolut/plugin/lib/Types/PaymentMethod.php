<?php

namespace Revolut\Plugin\Types;

use Exception;
use Revolut\Plugin\Types\ValueObject;

class PaymentMethod extends ValueObject
{
      /** @var string */
    private $method;

    /** @var string  */
    public const CARD = "card";

    /** @var string  */
    public const CARD_SUBSCRIPTION = "card_subscription";

    /** @var string  */
    public const REVOLUT_PAY = "pay_with_revolut";

    /** @var string  */
    public const RPAY_FAST_CHECKOUT = "rpay_fast_checkout";

    /** @var string  */
    public const PAYMENT_REQUEST = "payment_request";

    /** @var string  */
    public const PAYMENT_REQUEST_FAST_CHECKOUT = "payment_request_fast_checkout";

    /** @var string  */
    public const PAY_BY_BANK = "open_banking";

    /** @var string  */
    public const FAST_CHECKOUT = "fast_checkout";

    /** @var string */
    public const APPLE_PAY = 'apple_pay';

    /** @var string */
    public const APPLE_TAP_TO_PAY = 'apple_tap_to_pay';

    /** @var string */
    public const BANK_TRANSFER = 'bank_transfer';

    /** @var string */
    public const CARD_PRESENT = 'card_present';

    /** @var string */
    public const CASH = 'cash';

    /** @var string */
    public const GOOGLE_PAY = 'google_pay';

    /** @var array  */
    public const PAYMENT_METHODS = [
        self::CARD,
        self::CARD_SUBSCRIPTION,
        self::REVOLUT_PAY,
        self::PAYMENT_REQUEST,
        self::PAY_BY_BANK,
        self::RPAY_FAST_CHECKOUT,
        self::PAYMENT_REQUEST_FAST_CHECKOUT,
        self::APPLE_PAY,
        self::APPLE_TAP_TO_PAY,
        self::BANK_TRANSFER,
        self::CARD_PRESENT,
        self::CASH,
        self::GOOGLE_PAY,
    ];

    private function __construct(string $method)
    {
        if (!in_array($method, self::PAYMENT_METHODS)) {
            throw new Exception("Invalid payment method value: $method");
        }

        $this->method = $method;
    }

    public function getValue(): string
    {
        return $this->method;
    }

    public function isPayByBank(): bool
    {
        return $this->method == self::PAY_BY_BANK;
    }

    public function isPaymentRequest(): bool
    {
        return $this->method == self::PAYMENT_REQUEST;
    }

    public function isCard(): bool
    {
        return $this->method == self::CARD;
    }

    public function isCardSubscription(): bool
    {
        return $this->method == self::CARD_SUBSCRIPTION;
    }

    public function isRevolutPay(): bool
    {
        return $this->method == self::REVOLUT_PAY;
    }

    public function isFastCheckout(): bool
    {
        return $this->method == self::FAST_CHECKOUT;
    }

    public static function of(string $method): PaymentMethod
    {
        return new PaymentMethod($method);
    }

    public static function applePay(): PaymentMethod
    {
        return self::of(self::APPLE_PAY);
    }

    public static function appleTapToPay(): PaymentMethod
    {
        return self::of(self::APPLE_TAP_TO_PAY);
    }

    public static function bankTransfer(): PaymentMethod
    {
        return self::of(self::BANK_TRANSFER);
    }

    public static function card(): PaymentMethod
    {
        return self::of(self::CARD);
    }

    public static function cardPresent(): PaymentMethod
    {
        return self::of(self::CARD_PRESENT);
    }

    public static function cash(): PaymentMethod
    {
        return self::of(self::CASH);
    }

    public static function googlePay(): PaymentMethod
    {
        return self::of(self::GOOGLE_PAY);
    }

    public static function payByBank(): PaymentMethod
    {
        return self::of(self::PAY_BY_BANK);
    }

    public static function revolutPay(): PaymentMethod
    {
        return self::of(self::REVOLUT_PAY);
    }
}
