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

namespace PrestaShop\Module\PerformancePro\domain\service\cache;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\util\PathService;
use PrestaShop\Module\PerformancePro\resources\config\Config;
use ReflectionException;

final class HTTPCache
{
    /**
     * @var int
     */
    private const TTL = 604800;

    /**
     * @var null|HTTPCache
     */
    private static $httpCache;

    /**
     * @var null|ExtendedCacheItemPoolInterface
     */
    private $extendedCacheItemPool;

    private function __construct()
    {
        $this->extendedCacheItemPool = $this->getCache();
    }

    /**
     * @return null|ExtendedCacheItemPoolInterface
     */
    public function getCache()
    {
        try {
            CacheManager::setDefaultConfig(
                new ConfigurationOption(
                    [
                        'path' => PathService::createPath(Config::getHttpCachePath(), true),
                        'defaultChmod' => Config::DEFAULT_MODE_FOLDER,
                    ]
                )
            );
        } catch (PhpfastcacheInvalidConfigurationException|
        ReflectionException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
        }

        try {
            return CacheManager::getInstance('files');
        } catch (PhpfastcacheDriverCheckException|
        PhpfastcacheDriverException|
        PhpfastcacheInvalidArgumentException|
        PhpfastcacheInvalidConfigurationException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
        }

        return null;
    }

    public static function getInstance(): self
    {
        if (null === self::$httpCache) {
            self::$httpCache = new self();
        }

        return self::$httpCache;
    }

    public function clear(): void
    {
        $this->extendedCacheItemPool->clear();
    }

    public function save(string $html, string $key): void
    {
        try {
            $item = $this->extendedCacheItemPool->getItem($key);

            if (!$item->isHit()) {
                $item->set($html)
                    ->expiresAfter(self::TTL);

                $this->extendedCacheItemPool->save($item);
            }
        } catch (PhpfastcacheInvalidArgumentException $phpfastcacheInvalidArgumentException) {
            LogService::error(
                $phpfastcacheInvalidArgumentException->getMessage(),
                $phpfastcacheInvalidArgumentException->getTrace()
            );
        }
    }

    /**
     * @return null|mixed
     */
    public function getItem(string $key)
    {
        try {
            return $this->extendedCacheItemPool->getItem($key)
                ->get();
        } catch (PhpfastcacheInvalidArgumentException $phpfastcacheInvalidArgumentException) {
            LogService::error(
                $phpfastcacheInvalidArgumentException->getMessage(),
                $phpfastcacheInvalidArgumentException->getTrace()
            );
        }

        return null;
    }
}
