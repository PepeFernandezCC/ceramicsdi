<?php
/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Academic Free License (AFL 3.0)
 *
 * Additionally, this module is subject to a proprietary End User License Agreement (EULA).
 * For the full copyright, open source license, and EULA information, please view the LICENSE
 * that were distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\Module\PerformancePro\domain\service\h2;

use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\util\ContextService;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ServerPush
{
    /**
     * @var string
     */
    public const COOKIE_KEY = 'PerformanceProH2Pusher';

    /**
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function pushCSS(): void
    {
        if (!$this->isSetCookie()) {
            $header = 'Link: <' . $this->path . '>; rel=preload; as=style;';

            header($header);

            $this->setCookie();
        }
    }

    private function isSetCookie(): bool
    {
        return ContextService::getCookie()->__isset(self::COOKIE_KEY);
    }

    private function setCookie(): void
    {
        try {
            ContextService::getCookie()->__set(self::COOKIE_KEY, '1');

            ContextService::getCookie()->write();
        } catch (\Exception $exception) {
            LogService::error($exception->getMessage(), $exception->getTrace());
        }
    }
}
