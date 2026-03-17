<?php

namespace Revolut\Plugin\Types;

use Exception;
use NumberFormatter;
use Revolut\Plugin\Types\ValueObject;

class PaymentAmount extends ValueObject
{
      /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    private function __construct(float $amount, string $currencyCode)
    {
        if (!is_float($amount)) {
            throw new Exception("Invalid amount: $amount");
        }

        if (strlen($currencyCode) !== 3) {
            throw new Exception("Invalid currencyCode: $currencyCode");
        }

        $this->amount = $amount;
        $this->currency = strtoupper($currencyCode);
    }

    public function getAmount(): int
    {
        $formatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);
        $formatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, $this->currency);
        $fractionalLength = $formatter->getAttribute(NumberFormatter::FRACTION_DIGITS);
        $minorUnitFactor = pow(10, $fractionalLength);
        return (int) round($this->amount * $minorUnitFactor);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getValue(): array
    {
        return [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
        ];
    }

    public static function of(float $amount, string $currency): PaymentAmount
    {
        return new PaymentAmount($amount, $currency);
    }
}
