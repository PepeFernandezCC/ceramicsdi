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

namespace PrestaShop\Module\PerformancePro\data\repository;

use PrestaShop\Module\PerformancePro\data\util\Connection;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;
use PrestaShopDatabaseException;

final class DatabaseSettingsRepository extends Connection
{
    public function updateValue(string $key, string $value): void
    {
        $this->getConnection()
            ->execute('SET GLOBAL ' . pSQL($key) . ' = ' . pSQL($value));
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function getValue(string $key): string
    {
        try {
            $values = (array) $this->getConnection(false)
                ->executeS('SHOW VARIABLES LIKE "' . pSQL($key) . '"');

            if (empty($values)) {
                return '';
            }

            return $values[0]['Value'];
        } catch (PrestaShopDatabaseException $prestaShopDatabaseException) {
            LogService::error($prestaShopDatabaseException->getMessage(), $prestaShopDatabaseException->getTrace());

            throw new PerformanceProDatabaseException();
        }
    }
}
