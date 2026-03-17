<?php

namespace Revolut\Plugin\Types;

use Exception;
use Revolut\Plugin\Types\ValueObject;

class WebhookSigningKey extends ValueObject
{
      /** @var string */
    private $signingKey;

    private function __construct(string $signingKey)
    {
        if (empty($signingKey)) {
            throw new Exception("Invalid Webhook SigningKey value: $signingKey");
        }

        $this->signingKey = $signingKey;
    }

    public function getValue(): string
    {
        return $this->signingKey;
    }

    public static function of(string $signingKey): WebhookSigningKey
    {
        return new WebhookSigningKey($signingKey);
    }
}
