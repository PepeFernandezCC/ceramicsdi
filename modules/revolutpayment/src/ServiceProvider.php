<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Revolut
 * @copyright Since 2020 Revolut
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

namespace Revolut\PrestaShop;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Revolut\Plugin\Infrastructure\Api\Auth\AccessTokenAuthStrategy;
use Revolut\Plugin\Infrastructure\Api\Auth\AuthStrategyFactory;
use Revolut\Plugin\Infrastructure\Api\MerchantApi;
use Revolut\Plugin\Infrastructure\Api\MerchantApiClient;
use Revolut\Plugin\Infrastructure\Lock\TokenRefreshJobLockService;
use Revolut\Plugin\Infrastructure\Lock\TokenRefreshLockService;
use Revolut\Plugin\Infrastructure\Repositories\OptionTokenRepository;
use Revolut\Plugin\Services\AuthConnect\AuthConnect;
use Revolut\Plugin\Services\Config\Api\ConfigInterface;
use Revolut\Plugin\Services\Config\Merchant\MerchantDetailsService;
use Revolut\Plugin\Services\Config\Store\StoreDetailsProviderInterface;
use Revolut\Plugin\Services\Log\RLog;
use Revolut\PrestaShop\Infrastructure\AuthConnectJob;
use Revolut\PrestaShop\Infrastructure\Config\ApiConfigProvider;
use Revolut\PrestaShop\Infrastructure\Config\StoreDetailsProvider;
use Revolut\PrestaShop\Infrastructure\HttpClient;
use Revolut\PrestaShop\Infrastructure\Logger;
use Revolut\PrestaShop\Infrastructure\OptionRepository;
use Revolut\PrestaShop\Infrastructure\PSConfigOptionRepository;

class ServiceProvider
{
    public static function apiConfigProvider(): ApiConfigProvider
    {
        return new ApiConfigProvider(self::pSConfigOptionRepository());
    }

    public static function apiConfig(): ConfigInterface
    {
        return self::apiConfigProvider()->getConfig();
    }

    public static function optionRepository(): OptionRepository
    {
        return new OptionRepository();
    }

    public static function pSConfigOptionRepository(): PSConfigOptionRepository
    {
        return new PSConfigOptionRepository();
    }

    public static function optionTokenRepository(): OptionTokenRepository
    {
        return new OptionTokenRepository(self::optionRepository());
    }

    public static function tokenRefreshLockService(): TokenRefreshLockService
    {
        return new TokenRefreshLockService(self::optionRepository());
    }

    public static function tokenRefreshJobLockService(): TokenRefreshJobLockService
    {
        return new TokenRefreshJobLockService(self::optionRepository());
    }

    public static function httpClient(): HttpClient
    {
        return new HttpClient();
    }

    public static function accessTokenAuthStrategy(): AccessTokenAuthStrategy
    {
        return new AccessTokenAuthStrategy(
            self::tokenRefreshLockService(),
            self::apiConfigProvider(),
            self::optionTokenRepository(),
            self::authConnectService()
        );
    }

    public static function authStrategyFactory(): AuthStrategyFactory
    {
        return new AuthStrategyFactory(
            self::apiConfigProvider(),
            self::tokenRefreshLockService(),
            self::optionTokenRepository(),
            self::authConnectService()
        );
    }

    public static function authConnectService(): AuthConnect
    {
        return new AuthConnect(
            self::optionTokenRepository(),
            self::httpClient(),
            self::apiConfigProvider(),
            self::tokenRefreshLockService()
        );
    }

    public static function authConnectJob(): AuthConnectJob
    {
        return new AuthConnectJob(
            self::tokenRefreshJobLockService(),
            self::authConnectService()
        );
    }

    public static function initMerchantApi(): void
    {
        MerchantApi::init(
            self::authStrategyFactory(),
            self::httpClient()
        );
    }

    public static function logger(): Logger
    {
        return RLog::getLogger();
    }

    public static function privateMerchantApi(): MerchantApiClient
    {
        return MerchantApi::private();
    }

    public static function legacyMerchantApi(): MerchantApiClient
    {
        return MerchantApi::privateLegacy();
    }

    public static function publicMerchantApi(): MerchantApiClient
    {
        return MerchantApi::public();
    }

    public static function storeDetails(): StoreDetailsProviderInterface
    {
        return new StoreDetailsProvider();
    }

    public static function merchantDetailsService(): MerchantDetailsService
    {
        return new MerchantDetailsService(self::optionRepository(), MerchantApi::merchantDetails(), self::apiConfig(), self::storeDetails());
    }
}
