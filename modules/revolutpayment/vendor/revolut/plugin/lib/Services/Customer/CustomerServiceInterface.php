<?php

namespace Revolut\Plugin\Services\Customer;

use Revolut\Plugin\Infrastructure\Api\Customers\DTOs\CustomerDTO;
use Revolut\Plugin\Types\Email;
use Revolut\Plugin\Types\PhoneNumber;

interface CustomerServiceInterface
{
    public function create(CustomerParams $params): ?CustomerDTO;
}
