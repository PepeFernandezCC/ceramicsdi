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

namespace PrestaShop\Module\PerformancePro\domain\service\db;

use PrestaShop\Module\PerformancePro\data\repository\DatabaseSettingsRepository;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;
use Tools;

final class DatabaseSettings
{
    public function updateValue(string $key, string $value): void
    {
        $value = (string) Tools::convertBytes($value);

        (new DatabaseSettingsRepository())->updateValue($key, $value);
    }

    /**
     * @throws PerformanceProDatabaseException
     *
     * @return array<string>
     */
    public function formatConfigKey(string $key, string $recommended, string $url, bool $check): array
    {
        $current = $this->getValue($key);

        return [$key, $check ? $current : Tools::formatBytes($current, 0), $recommended, $url];
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    private function getValue(string $key): string
    {
        return (new DatabaseSettingsRepository())->getValue($key);
    }
}
