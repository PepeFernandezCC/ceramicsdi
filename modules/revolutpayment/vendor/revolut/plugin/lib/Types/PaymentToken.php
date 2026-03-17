<?php

namespace Revolut\Plugin\Types;

use Exception;
use Revolut\Plugin\Types\ValueObject;

class PaymentToken extends ValueObject
{
    /** @var string */
    private $token;

    private function __construct(string $token)
    {
        if (empty($token)) {
            throw new Exception("Payment Token can not be empty");
        }

        $this->token = $token;
    }

    public function getValue(): string
    {
        return $this->token;
    }

    public static function of(string $token): PaymentToken
    {
        return new PaymentToken($token);
    }
}
