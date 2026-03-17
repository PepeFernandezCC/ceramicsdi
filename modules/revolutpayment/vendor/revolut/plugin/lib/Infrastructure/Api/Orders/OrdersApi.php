<?php

namespace Revolut\Plugin\Infrastructure\Api\Orders;

use Revolut\Plugin\Infrastructure\Api\MerchantApiClientInterface;

class OrdersApi implements OrdersApiInterface
{
    /** @var MerchantApiClientInterface */
    private $merchantApiClient;

    /** @var string */
    public const ORDERS_ENDPOINT  = 'orders';

    /** @var string */
    public const COMPLETED_STATE = 'completed';

    /** @var string */
    public const AUTHORISED_STATE  = 'authorised';

    /** @var string */
    public const PENDING_STATE  = 'pending';

    public function __construct(MerchantApiClientInterface $merchantApiClient)
    {
        $this->merchantApiClient = $merchantApiClient;
    }

    public function create(array $params): array
    {
        return $this->merchantApiClient->post(self::ORDERS_ENDPOINT, $params);
    }

    public function retrieve(string $id): array
    {
        return $this->merchantApiClient->get(self::ORDERS_ENDPOINT . "/$id");
    }

    public function isAuthorised(string $id): bool
    {
        $order = $this->merchantApiClient->get(self::ORDERS_ENDPOINT . "/$id");
        return $order['state'] === self::AUTHORISED_STATE;
    }

    public function isCompleted(string $id): bool
    {
        $order = $this->merchantApiClient->get(self::ORDERS_ENDPOINT . "/$id");
        return $order['state'] === self::COMPLETED_STATE;
    }

    public function isCompletedOrAuthorised(string $id): bool
    {
        $order = $this->merchantApiClient->get(self::ORDERS_ENDPOINT . "/$id");
        return ($order['state'] === self::AUTHORISED_STATE || $order['state'] === self::COMPLETED_STATE);
    }

    public function isPending(string $id): bool
    {
        $order = $this->merchantApiClient->get(self::ORDERS_ENDPOINT . "/$id");
        return $order['state'] === self::PENDING_STATE;
    }

    public function hasCustomer(string $id): bool
    {
        $order = $this->merchantApiClient->get(self::ORDERS_ENDPOINT . "/$id");
        return isset($order['customer_id']) && !empty($order['customer_id']);
    }

    public function update(string $id, array $params): array
    {
        return $this->merchantApiClient->patch(self::ORDERS_ENDPOINT . "/$id", $params);
    }

    public function refund(string $id, array $params): array
    {
        return $this->merchantApiClient->post(self::ORDERS_ENDPOINT . "/$id/refund", $params);
    }

    public function confirm(string $id, array $params): array
    {
        return $this->merchantApiClient->post(self::ORDERS_ENDPOINT . "/$id/payments", $params);
    }

    public function capture(string $id): array
    {
        return $this->merchantApiClient->post(self::ORDERS_ENDPOINT . "/$id/capture");
    }

    public function cancel(string $id): array
    {
        return $this->merchantApiClient->post(self::ORDERS_ENDPOINT . "/$id/cancel");
    }
}
