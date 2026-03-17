<?php

namespace Revolut\Plugin\Infrastructure\Api\Webhooks;

use Revolut\Plugin\Types\ApiId;
use Revolut\Plugin\Types\WebhookSigningKey;

class WebhookDTO
{
    /** @var ApiId */
    public $id;

    /** @var WebhookSigningKey */
    public $signingKey;

    public function __construct(ApiId $id, WebhookSigningKey $signingKey)
    {
        $this->id = $id;
        $this->signingKey = $signingKey;
    }

    public static function fromArray(array $webhook): WebhookDTO
    {
        $signingKey = '';

        if (!empty($webhook['signing_secret'])) {
            $signingKey = $webhook['signing_secret'];
        } elseif (!empty($webhook['signing_key'])) {
            $signingKey = $webhook['signing_key'];
        }

        return new WebhookDTO(
            ApiId::of($webhook['id']),
            WebhookSigningKey::of($signingKey)
        );
    }
}
