<?php

namespace Revolut\Plugin\Services\Customer;

use Revolut\Plugin\Services\Customer\CustomerModel;
use Revolut\Plugin\Types\ApiEnv;

interface CustomerRepositoryInterface
{
    public function findByPlatformId(string $customerId, ApiEnv $env): ?CustomerModel;
    public function findByRevolutId(string $customerId, ApiEnv $env): CustomerModel;
    public function add(CustomerModel $customer): void;
    public function update(CustomerModel $customer): void;
}
