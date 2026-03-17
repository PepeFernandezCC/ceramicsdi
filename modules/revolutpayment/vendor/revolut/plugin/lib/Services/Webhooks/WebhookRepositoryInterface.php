<?php

namespace Revolut\Plugin\Services\Webhooks;

use Revolut\Plugin\Types\ApiEnv;
use Revolut\Plugin\Types\WebhookSigningKey;
use Revolut\Plugin\Types\WebhookType;

interface WebhookRepositoryInterface
{
    public function addSigningKey(WebhookType $webhookType, ApiEnv $env, int $storeId, WebhookSigningKey $signingKey): void;
    public function fetchSigningKey(WebhookType $webhookType, ApiEnv $env, int $storeId): ?string;

    public function addWebhookUrl(WebhookType $webhookType, ApiEnv $env, int $storeId, string $url): void;
    public function fetchWebhookUrl(WebhookType $webhookType, ApiEnv $env, int $storeId): ?string;

    public function deleteSigningKey(WebhookType $webhookType, ApiEnv $env, int $storeId): void;
}
