<?php

namespace Revolut\Plugin\Services\Config\Merchant;

use Revolut\Plugin\Services\Config\Api\ConfigInterface;
use Revolut\Plugin\Infrastructure\Api\MerchantDetails\MerchantDetailsApiInterface;
use Revolut\Plugin\Services\Config\Store\StoreDetailsProviderInterface;
use Revolut\Plugin\Services\Repositories\OptionRepositoryInterface;
use Revolut\Plugin\Services\Config\Merchant\MerchantDetailsServiceInterface;
use Revolut\Plugin\Types\CardBrand;
use Revolut\Plugin\Types\PaymentMethod;

class MerchantDetailsService implements MerchantDetailsServiceInterface
{
    public const AVAILABLE_PAYMENT_METHODS_CACHE_TTL_IN_SECONDS = 86400;
    private $merchantDetails;
    private $repo;
    private $apiConfig;
    private $storeDetails;

    private $availablePaymentMethods = [];
    private $merchantFeatures = [];

    public function __construct(OptionRepositoryInterface $repo, MerchantDetailsApiInterface $merchantDetails, ConfigInterface $apiConfig, StoreDetailsProviderInterface $storeDetails)
    {
        $this->merchantDetails = $merchantDetails;
        $this->repo = $repo;
        $this->apiConfig = $apiConfig;
        $this->storeDetails = $storeDetails;
    }

    public function getMerchantFeatures(): array
    {
        if (empty($this->merchantFeatures)) {
            $this->merchantFeatures = $this->merchantDetails->getFeatures();
        }
        return $this->merchantFeatures;
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->getMerchantFeatures());
    }

    public function isApplePayAvailable(): bool
    {
        return in_array(PaymentMethod::APPLE_PAY, $this->getAvailablePaymentMethods());
    }

    public function isAppleTapToPayAvailable(): bool
    {
        return in_array(PaymentMethod::APPLE_TAP_TO_PAY, $this->getAvailablePaymentMethods());
    }

    public function isBankTransferAvailable(): bool
    {
        return in_array(PaymentMethod::BANK_TRANSFER, $this->getAvailablePaymentMethods());
    }

    public function isCardAvailable(): bool
    {
        return in_array(PaymentMethod::CARD, $this->getAvailablePaymentMethods());
    }

    public function isCardPresentAvailable(): bool
    {
        return in_array(PaymentMethod::CARD_PRESENT, $this->getAvailablePaymentMethods());
    }

    public function isCashAvailable(): bool
    {
        return in_array(PaymentMethod::CASH, $this->getAvailablePaymentMethods());
    }

    public function isGooglePayAvailable(): bool
    {
        return in_array(PaymentMethod::GOOGLE_PAY, $this->getAvailablePaymentMethods());
    }

    public function isOpenBankingAvailable(): bool
    {
        return in_array(PaymentMethod::PAY_BY_BANK, $this->getAvailablePaymentMethods());
    }

    public function isRevolutPayAvailable(): bool
    {
        return in_array(PaymentMethod::REVOLUT_PAY, $this->getAvailablePaymentMethods());
    }

    public function isMaestroAvailable(): bool
    {
        return in_array(CardBrand::MAESTRO, $this->getAvailableCardBrands());
    }

    public function isMastercardAvailable(): bool
    {
        return in_array(CardBrand::MASTERCARD, $this->getAvailableCardBrands());
    }

    public function isVisaAvailable(): bool
    {
        return in_array(CardBrand::VISA, $this->getAvailableCardBrands());
    }

    public function getAvailablePaymentMethods(?int $amount = null, ?string $currency = null): array
    {
        if (empty($amount)) {
            $amount = 0;
        }

        if (empty($currency)) {
            $currency = $this->storeDetails->getStoreCurrency();
        }

        $cacheKey = $this->keyAvailablePaymentMethods($currency);

        $availablePaymentMethods = $this->repo->getCached($cacheKey);

        if (empty($availablePaymentMethods)) {
            $this->setupAvailablePaymentMethods($amount, $currency);
            $availablePaymentMethods = $this->repo->getCached($cacheKey);
        }


        return $availablePaymentMethods;
    }

    public function getAvailableCardBrands(?int $amount = null, ?string $currency = null): array
    {
        if (empty($amount)) {
            $amount = 0;
        }

        if (empty($currency)) {
            $currency = $this->storeDetails->getStoreCurrency();
        }

        $cacheKey = $this->keyAvailableCardBrands($currency);

        $availableCardBrands = $this->repo->getCached($cacheKey);

        if (empty($availableCardBrands)) {
            $this->setupAvailableCardBrands($amount, $currency);
            $availableCardBrands = $this->repo->getCached($cacheKey);
        }

        return $availableCardBrands;
    }

    public function setupAvailablePaymentMethods(?int $amount = null, ?string $currency = null): void
    {
        if (empty($amount)) {
            $amount = 0;
        }

        if (empty($currency)) {
            $currency = $this->storeDetails->getStoreCurrency();
        }

        $availablePaymentMethods = $this->merchantDetails->availablePaymentMethods($amount, $currency);

        if (empty($availablePaymentMethods)) {
            throw new \Exception("Unable to retrieve available payment methods");
        }

        $cacheKey = $this->keyAvailablePaymentMethods($currency);

        $this->repo->addCached($cacheKey, $availablePaymentMethods, self::AVAILABLE_PAYMENT_METHODS_CACHE_TTL_IN_SECONDS);
    }

    public function setupAvailableCardBrands(?int $amount = null, ?string $currency = null): void
    {
        if (empty($amount)) {
            $amount = 0;
        }

        if (empty($currency)) {
            $currency = $this->storeDetails->getStoreCurrency();
        }

        $availableCardBrands = $this->merchantDetails->availableCardBrands($amount, $currency);

        if (empty($availableCardBrands)) {
            throw new \Exception("Unable to retrieve available card brands");
        }

        $cacheKey = $this->keyAvailableCardBrands($currency);

        $this->repo->addCached($cacheKey, $availableCardBrands, self::AVAILABLE_PAYMENT_METHODS_CACHE_TTL_IN_SECONDS);
    }

    public function setupMerchantPublicKey(): void
    {
        $publicKey = $this->merchantDetails->getPublicKey();

        if (empty($publicKey)) {
            throw new \Exception("Unable to retrieve merchant public key");
        }

        $this->repo->addOrUpdate($this->keyMerchantPublicKey(), $publicKey);
    }

    public function getMerchantPublicKey(): ?string
    {
        $publicKey = $this->repo->get($this->keyMerchantPublicKey());

        if (!empty($publicKey)) {
            return $publicKey;
        }

        $this->setupMerchantPublicKey();
        return $this->repo->get($this->keyMerchantPublicKey());
    }

    private function keyMerchantPublicKey()
    {
        $mode = $this->apiConfig->getMode();
        return "{$mode}_revolut_merchant_public_key";
    }

    private function keyAvailablePaymentMethods($currency)
    {
        $mode = $this->apiConfig->getMode();
        return "revolut_available_payment_methods_{$mode}_$currency";
    }

    private function keyAvailableCardBrands($currency)
    {
        $mode = $this->apiConfig->getMode();
        return "revolut_available_card_brands_{$mode}_$currency";
    }
}
