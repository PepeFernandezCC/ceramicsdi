<?php

namespace Revolut\Plugin\Services\Webhooks;

use Exception;
use Revolut\Plugin\Infrastructure\Api\Webhooks\WebhookDTO;
use Revolut\Plugin\Infrastructure\Api\Webhooks\WebhooksApi;
use Revolut\Plugin\Infrastructure\Api\Webhooks\WebhooksApiInterface;
use Revolut\Plugin\Services\Config\Api\ConfigInterface;
use Revolut\Plugin\Services\Location\LocationRepositoryInterface;
use Revolut\Plugin\Services\Location\LocationServiceInterface;
use Revolut\Plugin\Services\Webhooks\WebhookRepositoryInterface;
use Revolut\Plugin\Types\ApiEnv;
use Revolut\Plugin\Types\WebhookType;

class WebhooksService implements WebhooksInterface
{
    /** @var WebhooksApiInterface  */
    private $webhooksApi;

    /** @var WebhookRepositoryInterface  */
    private $repo;

    /** @var ConfigInterface  */
    private $config;

    /** @var LocationServiceInterface  */
    private $locationService;

    public function __construct(
        WebhooksApiInterface $webhooksApi,
        WebhookRepositoryInterface $repo,
        LocationServiceInterface $locationService,
        ConfigInterface $config
    ) {
        $this->webhooksApi = $webhooksApi;
        $this->repo = $repo;
        $this->config = $config;
        $this->locationService = $locationService;
    }

    public function hookPaymentStateEvent(string $url, int $storeId, array $events): void
    {
        if (empty($events)) {
            throw new \Exception('WebhooksService, no event specified');
        }

        $webhooks = $this->webhooksApi->retrieveAll();

        if (!empty($webhooks)) {
            $existWebhook = null;

            foreach ($webhooks as $webhook) {
                if ($webhook['url'] == $url) {
                    $existWebhook = $webhook;
                }
            }

            if ($existWebhook) {
                $webhook = $this->webhooksApi->retrieve($existWebhook['id']);

                $signingKey = $this->repo->fetchSigningKey(
                    WebhookType::ofStandard(),
                    ApiEnv::of($this->config->getMode()),
                    $storeId
                );

                if ($signingKey == $webhook->signingKey->getValue()) {
                    return;
                }

                $this->repo->addSigningKey(
                    WebhookType::ofStandard(),
                    ApiEnv::of($this->config->getMode()),
                    $storeId,
                    $webhook->signingKey
                );

                $this->repo->addWebhookUrl(
                    WebhookType::ofStandard(),
                    ApiEnv::of($this->config->getMode()),
                    $storeId,
                    $url
                );

                return;
            }
        }

        $result = $this->webhooksApi->register([
            "url" => $url,
            "events" => $events
        ]);

        $this->repo->addSigningKey(
            WebhookType::ofStandard(),
            ApiEnv::of($this->config->getMode()),
            $storeId,
            $result->signingKey
        );

        $this->repo->addWebhookUrl(
            WebhookType::ofStandard(),
            ApiEnv::of($this->config->getMode()),
            $storeId,
            $url
        );
    }

    public function hookAddressValidationEvent(string $url, int $storeId): void
    {
        $domain = parse_url($url);
        $domain =  $domain['host'];

        $location = $this->locationService->create($domain);

        $webhooks = $this->webhooksApi->retrieveAllSynchronous();

        if (!empty($webhooks)) {
            $existWebhook = null;
            foreach ($webhooks as $webhook) {
                if ($webhook['url'] == $url) {
                    $existWebhook = $webhook;
                }
            }

            if ($existWebhook) {
                $webhook = WebhookDTO::fromArray($existWebhook);

                $this->repo->addWebhookUrl(
                    WebhookType::ofSync(),
                    ApiEnv::of($this->config->getMode()),
                    $storeId,
                    $url
                );

                $this->repo->addSigningKey(
                    WebhookType::ofSync(),
                    ApiEnv::of($this->config->getMode()),
                    $storeId,
                    $webhook->signingKey
                );

                return;
            }
        }


        $result = $this->webhooksApi->registerSynchronousWebhook([
            "url" => $url,
            "location_id" => $location->id->getValue(),
            'event_type'  => 'fast_checkout.validate_address',
        ]);

        $this->repo->addWebhookUrl(
            WebhookType::ofSync(),
            ApiEnv::of($this->config->getMode()),
            $storeId,
            $url
        );

        $this->repo->addSigningKey(
            WebhookType::ofSync(),
            ApiEnv::of($this->config->getMode()),
            $storeId,
            $result->signingKey
        );
    }

    public function validate(string $request_timestamp, string $received_signature, string $request_body, ApiEnv $env, int $storeId = 0): bool
    {
        $signing_secret = $this->repo->fetchSigningKey(
            WebhookType::ofStandard(),
            $env,
            $storeId
        );

        return WebhooksApi::validate(
            $signing_secret,
            $request_timestamp,
            $received_signature,
            $request_body
        );
    }

    public function validateSyncWebhook(string $received_signature, string $request_body, ApiEnv $env, int $storeId = 0): bool
    {
        $signing_secret = $this->repo->fetchSigningKey(
            WebhookType::ofSync(),
            $env,
            $storeId
        );

        return WebhooksApi::validateSync(
            $signing_secret,
            $received_signature,
            $request_body
        );
    }
}
