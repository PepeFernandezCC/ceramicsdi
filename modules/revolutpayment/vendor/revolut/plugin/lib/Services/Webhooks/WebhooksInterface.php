<?php

namespace Revolut\Plugin\Services\Webhooks;

use Revolut\Plugin\Types\ApiEnv;

interface WebhooksInterface
{
    public function hookPaymentStateEvent(string $url, int $storeId, array $events);
    public function hookAddressValidationEvent(string $url, int $storeId);
    public function validate(string $request_timestamp, string $received_signature, string $request_body, ApiEnv $env, int $storeId = 0): bool;
    public function validateSyncWebhook(string $received_signature, string $request_body, ApiEnv $env, int $storeId = 0): bool;
}
