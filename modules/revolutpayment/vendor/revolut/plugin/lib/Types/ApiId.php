<?php

namespace Revolut\Plugin\Types;

use Exception;
use Revolut\Plugin\Types\ValueObject;

class ApiId extends ValueObject
{
      /** @var string */
    private $id;

    private function __construct(string $id)
    {
        if (empty($id)) {
            throw new Exception("Invalid api id value: $id");
        }

        $this->id = $id;
    }

    public function getValue(): string
    {
        return $this->id;
    }

    public static function of(string $id): ApiId
    {
        return new ApiId($id);
    }
}
