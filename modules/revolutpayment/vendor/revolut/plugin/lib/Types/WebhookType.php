<?php

namespace Revolut\Plugin\Types;

use Exception;
use Revolut\Plugin\Types\ValueObject;

class WebhookType extends ValueObject
{
      /** @var string */
    private $webhookType;

    /** @var string  */
    public const STANDARD = "standard";

    /** @var string  */
    public const SYNC = "synchronous";

    private function __construct(string $webhookType)
    {
        $webhookType = strtolower($webhookType);

        if ($webhookType !== self::STANDARD && $webhookType !== self::SYNC) {
            throw new Exception("Invalid webhook type value: $webhookType");
        }

        $this->webhookType = $webhookType;
    }

    public function getValue(): string
    {
        return $this->webhookType;
    }

    public static function ofStandard(): WebhookType
    {
        return new WebhookType(self::STANDARD);
    }

    public static function ofSync(): WebhookType
    {
        return new WebhookType(self::SYNC);
    }

    public static function of(string $webhookType): WebhookType
    {
        return new WebhookType($webhookType);
    }
}
