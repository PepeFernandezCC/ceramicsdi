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

namespace Revolut\PrestaShop\Infrastructure;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Revolut\Plugin\Services\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    private const LOG_CONTEXT = ['source' => 'revolut-gateway-for-prestashop'];

    public function info(string $message, array $context = self::LOG_CONTEXT)
    {
        \PrestaShopLogger::addLog(self::LOG_CONTEXT['source'] . ' [INFO] ' . $message, 1);
        error_log(self::LOG_CONTEXT['source'] . ' [INFO] ' . $message);
    }

    public function error(string $message, array $context = self::LOG_CONTEXT)
    {
        \PrestaShopLogger::addLog(self::LOG_CONTEXT['source'] . ' [ERROR] ' . $message, 3);
        error_log(self::LOG_CONTEXT['source'] . '[ERROR] ' . $message);
    }

    public function debug(string $message, array $context = self::LOG_CONTEXT)
    {
        \PrestaShopLogger::addLog(self::LOG_CONTEXT['source'] . ' [DEBUG] ' . $message, 2);
        error_log(self::LOG_CONTEXT['source'] . '[DEBUG] ' . $message);
    }
}
