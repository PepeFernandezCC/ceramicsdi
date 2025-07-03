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

final class DatabaseOptimizerRepository extends Connection
{
    /**
     * @throws PerformanceProDatabaseException
     */
    public function changeEngineToInnoDb(): int
    {
        try {
            $result = 0;

            $tableResults = (array) $this->getConnection()
                ->executeS('
                SELECT table_name
                FROM INFORMATION_SCHEMA.TABLES
                WHERE ENGINE = "MyISAM" AND TABLE_SCHEMA = "' . _DB_NAME_ . '"
            ');

            $tables = array_column($tableResults, 'table_name');

            foreach ($tables as $table) {
                $this->getConnection()
                    ->execute('ALTER TABLE ' . $table . ' ENGINE=InnoDB;');

                $result += $this->getConnection()
                    ->Affected_Rows();
            }

            return $result;
        } catch (PrestaShopDatabaseException $prestaShopDatabaseException) {
            LogService::error($prestaShopDatabaseException->getMessage(), $prestaShopDatabaseException->getTrace());

            throw new PerformanceProDatabaseException();
        }
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function repairTables(): int
    {
        try {
            $result = 0;

            $tables = (array) $this->getConnection()
                ->executeS('SHOW TABLES FROM `' . _DB_NAME_ . '`');

            foreach ($tables as $table) {
                $currentTable = _DB_PREFIX_ . current($table);

                if (mb_strlen($currentTable) > 64) {
                    continue;
                }

                $this->getConnection()
                    ->execute('REPAIR TABLE `' . $currentTable . '`');

                ++$result;
            }

            return $result;
        } catch (PrestaShopDatabaseException $prestaShopDatabaseException) {
            LogService::error($prestaShopDatabaseException->getMessage(), $prestaShopDatabaseException->getTrace());

            throw new PerformanceProDatabaseException();
        }
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function optimizeTables(): int
    {
        $this->getConnection()
            ->execute('PURGE BINARY LOGS BEFORE NOW();');

        $result = 0;

        $result += $this->optimizeMyIsam();

        $result += $this->optimizeInnoDb();

        $this->getConnection()
            ->execute('FLUSH TABLES;');

        $this->getConnection()
            ->execute('FLUSH QUERY CACHE;');

        return $result;
    }

    private function optimizeMyIsam(): int
    {
        try {
            $result = 0;

            $tableResults = (array) $this->getConnection()
                ->executeS('
                SELECT table_name
                FROM INFORMATION_SCHEMA.TABLES
                WHERE ENGINE = "MyISAM" AND TABLE_SCHEMA = "' . _DB_NAME_ . '"
            ');

            $tables = array_column($tableResults, 'table_name');

            foreach ($tables as $table) {
                if (mb_strlen($table) > 64) {
                    continue;
                }

                $this->getConnection()
                    ->execute('OPTIMIZE NO_WRITE_TO_BINLOG TABLE `' . $table . '`;');

                ++$result;
            }

            return $result;
        } catch (PrestaShopDatabaseException $prestaShopDatabaseException) {
            LogService::error($prestaShopDatabaseException->getMessage(), $prestaShopDatabaseException->getTrace());

            throw new PerformanceProDatabaseException();
        }
    }

    private function optimizeInnoDb(): int
    {
        try {
            $result = 0;

            $tableResults = (array) $this->getConnection()
                ->executeS('
                    SELECT table_name
                    FROM INFORMATION_SCHEMA.TABLES
                    WHERE ENGINE = "InnoDB" AND TABLE_SCHEMA = "' . _DB_NAME_ . '"
                ');

            $tables = array_column($tableResults, 'table_name');

            foreach ($tables as $table) {
                if (mb_strlen($table) > 64) {
                    continue;
                }

                $this->getConnection()
                    ->execute('ALTER TABLE `' . $table . '` ENGINE=InnoDB;');

                $this->getConnection()
                    ->execute('ANALYZE NO_WRITE_TO_BINLOG TABLE `' . $table . '`;');

                ++$result;
            }

            return $result;
        } catch (PrestaShopDatabaseException $prestaShopDatabaseException) {
            LogService::error($prestaShopDatabaseException->getMessage(), $prestaShopDatabaseException->getTrace());

            throw new PerformanceProDatabaseException();
        }
    }
}
