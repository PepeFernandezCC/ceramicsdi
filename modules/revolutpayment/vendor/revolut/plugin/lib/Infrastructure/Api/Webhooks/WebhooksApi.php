<?php

namespace Revolut\Plugin\Infrastructure\Api\Webhooks;

use Error;
use Revolut\Plugin\Infrastructure\Api\Webhooks\WebhookDTO;
use Revolut\Plugin\Infrastructure\Api\Webhooks\WebhooksApiInterface;
use Revolut\Plugin\Infrastructure\Api\MerchantApiClientInterface;

class WebhooksApi implements WebhooksApiInterface
{
    private $merchantApiClient;

    private $legacyMerchantApiClient;

    public function __construct(
        MerchantApiClientInterface $merchantApiClient,
        MerchantApiClientInterface $legacyMerchantApiClient
    ) {
        $this->merchantApiClient = $merchantApiClient;
        $this->legacyMerchantApiClient = $legacyMerchantApiClient;
    }

    public function register(array $payload): WebhookDTO
    {
        $response = $this->legacyMerchantApiClient->post('webhooks', $payload);

        return WebhookDTO::fromArray($response);
    }

    public function registerSynchronousWebhook(array $payload): WebhookDTO
    {
        $response = $this->merchantApiClient->post('/synchronous-webhooks', $payload);

        return WebhookDTO::fromArray($response);
    }

    public function retrieve($id): WebhookDTO
    {
        $response = $this->legacyMerchantApiClient->get("webhooks/$id");
        return WebhookDTO::fromArray($response);
    }

    public function retrieveAll(): array
    {
        return $this->legacyMerchantApiClient->get("webhooks");
    }

    public function retrieveAllSynchronous(): array
    {
        return $this->merchantApiClient->get("/synchronous-webhooks");
    }

    public function delete($id): void
    {
        $this->legacyMerchantApiClient->delete("webhooks/$id");
    }

    public static function validate(string $signing_secret, string $request_timestamp, string $received_signature, string $request_body): bool
    {

        $payload_to_sign = 'v1.' . $request_timestamp . '.' . $request_body;

        $calculated_signature = 'v1=' . hash_hmac('sha256', $payload_to_sign, $signing_secret);

        return hash_equals($calculated_signature, $received_signature);
    }

    public static function validateSync(string $signing_secret, string $received_signature, string $request_body): bool
    {
        $calculated_signature = hash_hmac('sha256', $request_body, $signing_secret);
        return hash_equals($calculated_signature, $received_signature);
    }
}
