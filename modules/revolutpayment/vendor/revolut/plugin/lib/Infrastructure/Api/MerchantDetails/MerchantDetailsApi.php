<?php

namespace Revolut\Plugin\Infrastructure\Api\MerchantDetails;

use Revolut\Plugin\Infrastructure\Api\MerchantApiClientInterface;

class MerchantDetailsApi implements MerchantDetailsApiInterface
{
    private $publicApiClient;
    private $privateApiClient;

    public function __construct(
        MerchantApiClientInterface $publicApiClient,
        MerchantApiClientInterface $privateApiClient
    ) {
        $this->publicApiClient = $publicApiClient;
        $this->privateApiClient = $privateApiClient;
    }

    public function availablePaymentMethods(int $amount, string $currency): array
    {

        $response =  $this->publicApiClient->get('available-payment-methods', [
            'amount' => $amount,
            'currency' => $currency
        ]);

        return isset($response['available_payment_methods']) ? $response['available_payment_methods'] : [];
    }

    public function availableCardBrands(int $amount, string $currency): array
    {

        $response =  $this->publicApiClient->get('available-payment-methods', [
            'amount' => $amount,
            'currency' => $currency
        ]);

        return isset($response['available_card_brands']) ? $response['available_card_brands'] : [];
    }

    public function getDetails(): array
    {
        return $this->publicApiClient->get('merchant');
    }

    public function getFeatures(): array
    {
        $details = $this->getDetails();
        return isset($details['features']) ? $details['features'] : [];
    }

    public function getPublicKey(): string
    {
        $response = $this->privateApiClient->get('public-key/latest');
        return $response["public_key"] ?? "";
    }
}
