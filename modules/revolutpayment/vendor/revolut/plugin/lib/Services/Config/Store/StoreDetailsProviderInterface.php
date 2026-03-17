<?php

namespace Revolut\Plugin\Services\Config\Store;

interface StoreDetailsProviderInterface
{
    public function getStoreDomain(): string;
    public function getStoreWebhookEndpoint(): string;
    public function getAddressValidationWebhookEndpoint(): string;
    public function getStoreCurrency(): string;
}
