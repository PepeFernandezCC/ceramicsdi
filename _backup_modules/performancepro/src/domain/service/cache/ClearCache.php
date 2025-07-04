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

use Category;
use Media;
use PrestaShop\Module\PerformancePro\data\repository\QueryCacheRepository;
use PrestaShop\Module\PerformancePro\domain\service\util\DirectoryService;
use PrestaShop\Module\PerformancePro\resources\config\Config;
use Tools;

final class ClearCache
{
    public function clearImgCache(): void
    {
        Tools::deleteDirectory(Config::getImgCachePath(), false);

        // Clear HTTP cache to avoid image problems.
        HTTPCache::getInstance()->clear();
    }

    public function clearXmlCache(): void
    {
        Tools::clearXMLCache();
    }

    public function resetQueryCache(): void
    {
        (new QueryCacheRepository())->resetQueryCache();
    }

    public function flushQueryCache(): void
    {
        (new QueryCacheRepository())->flushQueryCache();
    }

    public function clearOpCache(): bool
    {
        if (\function_exists('opcache_get_status')) {
            opcache_reset();

            return true;
        }

        return false;
    }

    public function clearApcCache(): bool
    {
        if (\function_exists('apc_clear_cache')) {
            apc_clear_cache();

            apc_clear_cache('user');

            apc_clear_cache('opcode');

            return true;
        }

        return false;
    }

    public function clearMediaCache(): void
    {
        Media::clearCache();

        // Clear the HTTP cache to avoid CSS problems.
        HTTPCache::getInstance()->clear();
    }

    public function clearSmartyCacheAndSfCache(): void
    {
        Tools::clearSmartyCache();

        Tools::clearSf2Cache('dev');

        Tools::clearSf2Cache('prod');

        self::regenerateCache();
    }

    public function clearLogs(bool $analyze = false): int
    {
        $logPath = _PS_CORE_DIR_ . $this->getLogPath();

        $result = (new DirectoryService($logPath))->countFilesInDirectory();

        if (!$analyze) {
            Tools::deleteDirectory($logPath, false);
        }

        return $result;
    }

    private function regenerateCache(): void
    {
        Tools::generateIndex();

        Category::regenerateEntireNtree();
    }

    private function getLogPath(): string
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.3.0', '<=')) {
            return '/app/logs/';
        }

        return '/var/logs/';
    }
}
