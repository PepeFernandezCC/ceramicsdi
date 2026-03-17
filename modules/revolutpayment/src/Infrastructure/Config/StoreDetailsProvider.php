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

namespace Revolut\PrestaShop\Infrastructure\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Revolut\Plugin\Services\Config\Store\StoreDetailsProviderInterface;

class StoreDetailsProvider implements StoreDetailsProviderInterface
{
    public function getStoreCurrency(): string
    {
        $id_currency = (int) \Configuration::get('PS_CURRENCY_DEFAULT');
        if (empty($id_currency)) {
            return '';
        }
        $currency = new \Currency($id_currency);

        return $currency->iso_code;
    }

    public function getStoreDomain(): string
    {
        return \Tools::getHttpHost(false);
    }

    public function getStoreWebhookEndpoint(): string
    {
        throw new \Exception('not implemented');
    }

    public function getAddressValidationWebhookEndpoint(): string
    {
        throw new \Exception('not implemented');
    }
}
