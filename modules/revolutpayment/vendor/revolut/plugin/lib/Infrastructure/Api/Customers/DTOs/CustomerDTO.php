<?php

namespace Revolut\Plugin\Infrastructure\Api\Customers\DTOs;

use Exception;
use Revolut\Plugin\Types\ApiId;
use Revolut\Plugin\Types\Email;
use Revolut\Plugin\Types\PhoneNumber;

class CustomerDTO
{
    /** @var ApiId */
    public $id;

    /** @var string */
    public $fullName;

     /** @var Email */
    public $email;

     /** @var PhoneNumber */
    public $phone;

    public function __construct(ApiId $id, Email $email, ?PhoneNumber $phone = null, ?string $fullName = null)
    {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->phone = $phone;
    }
}
