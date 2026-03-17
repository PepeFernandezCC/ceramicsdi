<?php

namespace Revolut\Plugin\Infrastructure\Api\Orders;

interface OrdersApiInterface
{
    public function create(array $params): array;
    public function retrieve(string $id): array;
    public function update(string $id, array $params): array;
    public function confirm(string $id, array $params): array;
    public function refund(string $id, array $params): array;
    public function capture(string $id): array;
    public function cancel(string $id): array;

    public function isCompletedOrAuthorised(string $id): bool;
    public function isCompleted(string $id): bool;
    public function isAuthorised(string $id): bool;
    public function isPending(string $id): bool;
    public function hasCustomer(string $id): bool;
}
