<?php

namespace Revolut\Plugin\Types;

abstract class ValueObject
{
    public function equals(ValueObject $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    abstract public function getValue();
}
