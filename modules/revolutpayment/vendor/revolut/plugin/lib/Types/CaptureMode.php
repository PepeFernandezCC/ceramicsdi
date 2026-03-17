<?php

namespace Revolut\Plugin\Types;

use Exception;
use Revolut\Plugin\Types\ValueObject;

class CaptureMode extends ValueObject
{
      /** @var string */
    private $mode;

    /** @var string  */
    public const MANUAL_CAPTURE_MODE = "manual";

    /** @var string  */
    public const AUTO_CAPTURE_MODE = "automatic";

    private function __construct(string $mode)
    {
        $mode = strtolower($mode);

        if ($mode !== self::MANUAL_CAPTURE_MODE && $mode !== self::AUTO_CAPTURE_MODE) {
            throw new Exception("Invalid capture mode value: $mode");
        }

        $this->mode = $mode;
    }

    public function getValue(): string
    {
        return $this->mode;
    }

    public function isManualMode(): bool
    {
        return $this->mode == self::MANUAL_CAPTURE_MODE;
    }

    public function isAutoMode(): bool
    {
        return $this->mode == self::AUTO_CAPTURE_MODE;
    }

    public static function of(string $mode): CaptureMode
    {
        return new CaptureMode($mode);
    }
}
