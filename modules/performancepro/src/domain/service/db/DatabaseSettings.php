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

namespace PrestaShop\Module\PerformancePro\domain\service\db;

use PrestaShop\Module\PerformancePro\data\repository\DatabaseSettingsRepository;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class DatabaseSettings
{
    public function updateValue(string $key, string $value): void
    {
        $value = (string) \Tools::convertBytes($value);

        (new DatabaseSettingsRepository())->updateValue($key, $value);
    }

    /**
     * @return array<string>
     *
     * @throws PerformanceProDatabaseException
     */
    public function formatConfigKey(string $key, string $recommended, string $url, bool $check): array
    {
        $current = $this->getValue($key);

        return [$key, $check ? $current : \Tools::formatBytes($current, 0), $recommended, $url];
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    private function getValue(string $key): string
    {
        return (new DatabaseSettingsRepository())->getValue($key);
    }
}
