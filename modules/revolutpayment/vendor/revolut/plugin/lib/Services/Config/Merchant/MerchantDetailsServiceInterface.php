<?php

namespace Revolut\Plugin\Services\Config\Merchant;

interface MerchantDetailsServiceInterface
{
    public function getAvailablePaymentMethods(?int $amount = null, ?string $currency = null): array;
    public function getAvailableCardBrands(?int $amount = null, ?string $currency = null): array;
    public function setupAvailablePaymentMethods(?int $amount = null, ?string $currency = null): void;
    public function setupAvailableCardBrands(?int $amount = null, ?string $currency = null): void;
    public function setupMerchantPublicKey(): void;
    public function getMerchantFeatures(): array;
    public function hasFeature(string $feature): bool;

    public function isApplePayAvailable(): bool;
    public function isAppleTapToPayAvailable(): bool;
    public function isBankTransferAvailable(): bool;
    public function isCardAvailable(): bool;
    public function isCardPresentAvailable(): bool;
    public function isCashAvailable(): bool;
    public function isGooglePayAvailable(): bool;
    public function isOpenBankingAvailable(): bool;
    public function isRevolutPayAvailable(): bool;

    public function isMaestroAvailable(): bool;
    public function isMastercardAvailable(): bool;
    public function isVisaAvailable(): bool;
}
