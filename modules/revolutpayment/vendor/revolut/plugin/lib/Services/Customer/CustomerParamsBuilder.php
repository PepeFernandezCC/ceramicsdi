<?php

namespace Revolut\Plugin\Services\Customer;

use Revolut\Plugin\Types\Email;
use Revolut\Plugin\Types\PaymentAmount;
use Revolut\Plugin\Types\PhoneNumber;

class CustomerParamsBuilder
{
    /** @var Email */
    private $email;

    /** @var string */
    private $platformCustomerId;

    /** @var string */
    private $fullName;

    /** @var PhoneNumber */
    private $phone;

    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->email = null;
        $this->phone = null;
        $this->platformCustomerId = null;
        $this->fullName = null;
    }

    public function build(): CustomerParams
    {
        return new CustomerParams(
            $this->platformCustomerId,
            $this->fullName,
            $this->email,
            $this->phone
        );
    }

    public function setPlatformCustomerId(string $platformCustomerId): self
    {
        $this->platformCustomerId = $platformCustomerId;
        return $this;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function setPhone(PhoneNumber $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function setEmail(Email $email): self
    {
        $this->email = $email;
        return $this;
    }
}
