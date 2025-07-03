<?php
/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Commercial Software License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\Module\PerformancePro\domain\service\h2;

use Exception;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\util\ContextService;

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
        } catch (Exception $exception) {
            LogService::error($exception->getMessage(), $exception->getTrace());
        }
    }
}
