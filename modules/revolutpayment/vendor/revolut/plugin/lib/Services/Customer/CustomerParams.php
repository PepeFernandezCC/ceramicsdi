<?php

namespace Revolut\Plugin\Services\Customer;

use Revolut\Plugin\Types\Email;
use Revolut\Plugin\Types\PhoneNumber;

class CustomerParams
{
    /** @var Email */
    private $email;

    /** @var string */
    private $platformCustomerId;

    /** @var string */
    private $fullName;

    /** @var PhoneNumber */
    private $phone;

    public function __construct(
        string $platformCustomerId,
        ?string $fullName,
        Email $email,
        ?PhoneNumber $phone,
    ) {
        $this->fullName = $fullName;
        $this->platformCustomerId = $platformCustomerId;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getPlatformCustomerId(): string
    {
        return $this->platformCustomerId;
    }

    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
