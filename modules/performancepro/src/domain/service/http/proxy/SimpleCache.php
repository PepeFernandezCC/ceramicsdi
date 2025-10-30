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

namespace PrestaShop\Module\PerformancePro\domain\service\http\proxy;

use PrestaShop\Module\PerformancePro\domain\service\http\client\CURLClient;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\util\PathService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDownloadResourceException;
use PrestaShop\Module\PerformancePro\resources\config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class SimpleCache
{
    /**
     * @var string
     */
    private const EXTENSION = '.txt';

    /**
     * @var int
     */
    public $ttl = 3600;

    /**
     * @var string
     */
    public $key;

    public function get(string $key, string $url): string
    {
        if ($this->isHit($key)) {
            $filename = $this->getFilename($key);

            return \Tools::file_get_contents($filename);
        }

        try {
            $response = (new CURLClient($url))->getResponse();

            $this->set($key, $response);

            return $response;
        } catch (PerformanceProDownloadResourceException $performanceProDownloadResourceException) {
            LogService::error(
                $performanceProDownloadResourceException->getMessage(),
                $performanceProDownloadResourceException->getTrace()
            );
        }

        return '';
    }

    private function isHit(string $key): bool
    {
        $filename = $this->getFilename($key);

        return file_exists($filename) && (filemtime($filename) + $this->ttl >= time());
    }

    private function getFilename(string $key): string
    {
        return PathService::createPath(Config::getModuleCachePath()) . sha1($key) . self::EXTENSION;
    }

    private function set(string $key, string $data): void
    {
        $filename = $this->getFilename($key);

        file_put_contents($filename, $data);
    }

    public function expiresAfter(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }
}
