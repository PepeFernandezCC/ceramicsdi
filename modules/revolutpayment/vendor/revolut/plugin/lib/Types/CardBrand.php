<?php

namespace Revolut\Plugin\Types;

use Exception;
use Revolut\Plugin\Types\ValueObject;

class CardBrand extends ValueObject
{
    /** @var string */
    private $brand;

    /** @var string */
    public const VISA = 'visa';

    /** @var string */
    public const MASTERCARD = 'mastercard';

    /** @var string */
    public const MAESTRO = 'maestro';

    /** @var array */
    public const CARD_BRANDS = [
        self::VISA,
        self::MASTERCARD,
        self::MAESTRO,
    ];

    private function __construct(string $brand)
    {
        if (!in_array($brand, self::CARD_BRANDS)) {
            throw new Exception("Invalid card brand value: $brand");
        }

        $this->brand = $brand;
    }

    public function getValue(): string
    {
        return $this->brand;
    }

    public static function of(string $brand): CardBrand
    {
        return new CardBrand($brand);
    }
}
