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

use Revolut\Plugin\Services\AuthConnect\Exceptions\TokenRefreshInProgressException;
use Revolut\Plugin\Services\AuthConnect\TokenRefreshServiceInterface;
use Revolut\Plugin\Services\Lock\LockInterface;
use Revolut\Plugin\Services\Log\RLog;

class AuthConnectJob
{
    private $tokenRefreshJobLock;

    private $tokenRefreshService;

    public function __construct(
        LockInterface $tokenRefreshJobLock,
        TokenRefreshServiceInterface $tokenRefreshService
    ) {
        $this->tokenRefreshJobLock = $tokenRefreshJobLock;
        $this->tokenRefreshService = $tokenRefreshService;
    }

    public function run()
    {
        $this->handleTokenRefreshJob();
    }

    public function refreshTokenJob()
    {
        if (!$this->tokenRefreshJobLock->acquire()) {
            throw new TokenRefreshInProgressException('token refresh in progress...');
        }

        try {
            // RLog::info('refresh_token job start.');
            $this->tokenRefreshService->refreshToken();
            // RLog::info('refresh_token job completed.');
        } catch (\Exception $e) {
            // $dt = \DateTime::createFromFormat('U.u', (string) microtime(true));
            // RLog::error('refreshToken error - ' . $e->getMessage() . ' - ' . $dt->format('H:i:s.u'));
        }
    }

    public function handleTokenRefreshJob()
    {
        try {
            $this->refreshTokenJob();
        } catch (\Exception $e) {
            if ($e instanceof TokenRefreshInProgressException) {
                return;
            }

            RLog::error('refresh_token job error. ' . $e->getMessage());
        }
    }
}
