<?php

namespace Revolut\Plugin\Infrastructure\Api\Customers;

use Revolut\Plugin\Infrastructure\Api\MerchantApiClientInterface;
use Revolut\Plugin\Infrastructure\Api\Customers\DTOs\CustomerDTO;
use Revolut\Plugin\Infrastructure\Api\Customers\DTOs\CustomerDTOFactory;
use Revolut\Plugin\Infrastructure\Api\Customers\DTOs\CustomerSavedPaymentMethodDTOFactory;
use Revolut\Plugin\Types\ApiId;
use Revolut\Plugin\Types\Email;
use Revolut\Plugin\Types\PhoneNumber;

class CustomersApi implements CustomersApiInterface
{
    private $merchantApiClient;

    public function __construct(MerchantApiClientInterface $merchantApiClient)
    {
        $this->merchantApiClient = $merchantApiClient;
    }

    public function create(Email $email, ?string $fullName, ?PhoneNumber $phoneNumber): CustomerDTO
    {

        $customer = $this->merchantApiClient->post('customers', [
            'email' => $email->getValue(),
            'full_name' => $fullName ? $fullName : null,
            'phone' => $phoneNumber ? $phoneNumber->getValue() : null
        ]);

        return CustomerDTOFactory::fromArray($customer);
    }

    public function update(CustomerDTO $customer, array $payload): CustomerDTO
    {
        $customer = $this->merchantApiClient->patch("/customers/{$customer->id->getValue()}", $payload);
        return CustomerDTOFactory::fromArray($customer);
    }

    public function getById(ApiId $customerId): ?CustomerDTO
    {
        $customer = $this->merchantApiClient->get("customers/{$customerId->getValue()}");

        if (empty($customer['id'])) {
            return null;
        }

        return CustomerDTOFactory::fromArray($customer);
    }

    public function getByEmail(Email $email): ?CustomerDTO
    {
        $customer = $this->merchantApiClient->get('/customers?term=' . $email->getValue());
        if (empty($customer['id'])) {
            return null;
        }

        return CustomerDTOFactory::fromArray($customer);
    }

    public function getSavedPaymentMethod(ApiId $customerId): array
    {
        $savedPaymentMethods = $this->merchantApiClient->get("customer/{$customerId->getValue()}/payment-methods");

        return array_map(function (array $item) {
            return CustomerSavedPaymentMethodDTOFactory::fromArray($item);
        }, $savedPaymentMethods);
    }
}
