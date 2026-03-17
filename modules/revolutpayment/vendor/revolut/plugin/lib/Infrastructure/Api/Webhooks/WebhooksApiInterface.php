<?php

namespace Revolut\Plugin\Infrastructure\Api\Webhooks;

use Revolut\Plugin\Infrastructure\Api\Webhooks\WebhookDTO;

interface WebhooksApiInterface
{
    public function register(array $payload): WebhookDTO;
    public function retrieve($id): WebhookDTO;
    public function retrieveAll(): array;

    public function registerSynchronousWebhook(array $payload): WebhookDTO;
    public function retrieveAllSynchronous(): array;

    public function delete(string $id): void;
    public static function validate(string $signing_secret, string $request_timestamp, string $received_signature, string $request_body): bool;
}
