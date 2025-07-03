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

use PrestaShop\Module\PerformancePro\data\repository\DatabaseCleanerRepository;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;
use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidDateException;

final class DatabaseCleaner
{
    /**
     * @var DatabaseCleanerRepository
     */
    private $databaseCleanerRepository;

    public function __construct(DatabaseCleanerRepository $databaseCleanerRepository)
    {
        $this->databaseCleanerRepository = $databaseCleanerRepository;
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function cleanDoubletsConfiguration(): int
    {
        return $this->databaseCleanerRepository->cleanConfigurationDoublets();
    }

    public function cleanLangConfiguration(): int
    {
        return $this->databaseCleanerRepository->cleanConfigurationLang();
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function cleanLangTable(): int
    {
        return $this->databaseCleanerRepository->cleanLangTable();
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function cleanShopTable(): int
    {
        return $this->databaseCleanerRepository->cleanShopTable();
    }

    public function cleanStockAvailable(): int
    {
        return $this->databaseCleanerRepository->cleanStockAvailable();
    }

    /**
     * @throws PerformanceProInvalidDateException
     */
    public function cleanCartTable(int $range): int
    {
        return $this->databaseCleanerRepository->cleanCartTable($range);
    }

    /**
     * @throws PerformanceProInvalidDateException
     */
    public function cleanRuleTable(int $range): int
    {
        return $this->databaseCleanerRepository->cleanRuleTable($range);
    }

    /**
     * @throws PerformanceProInvalidDateException
     */
    public function cleanConnectionTables(int $range): int
    {
        return $this->databaseCleanerRepository->cleanConnectionTables($range);
    }

    /**
     * @throws PerformanceProInvalidDateException
     */
    public function cleanStatsSearchTable(int $range): int
    {
        return $this->databaseCleanerRepository->cleanStatsSearchTable($range);
    }

    /**
     * @throws PerformanceProInvalidDateException
     */
    public function cleanLogTable(int $range): int
    {
        return $this->databaseCleanerRepository->cleanLogTable($range);
    }

    /**
     * @throws PerformanceProInvalidDateException
     */
    public function cleanMailTable(int $range): int
    {
        return $this->databaseCleanerRepository->cleanMailTable($range);
    }

    public function cleanExpiredSpecificPrices(): int
    {
        return $this->databaseCleanerRepository->cleanExpiredSpecificPrices();
    }

    /**
     * @throws PerformanceProInvalidDateException
     */
    public function cleanPageNotFoundTable(int $range): int
    {
        return $this->databaseCleanerRepository->cleanPageNotFoundTable($range);
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function cleanGuestTable(): int
    {
        return $this->databaseCleanerRepository->cleanGuestTable();
    }
}
