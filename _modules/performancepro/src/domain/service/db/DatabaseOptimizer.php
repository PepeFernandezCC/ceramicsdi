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

use PrestaShop\Module\PerformancePro\data\repository\DatabaseOptimizerRepository;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;

final class DatabaseOptimizer
{
    /**
     * @var DatabaseOptimizerRepository
     */
    private $databaseOptimizerRepository;

    public function __construct(DatabaseOptimizerRepository $databaseOptimizerRepository)
    {
        $this->databaseOptimizerRepository = $databaseOptimizerRepository;
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function changeEngineToInnoDb(): int
    {
        return $this->databaseOptimizerRepository->changeEngineToInnoDb();
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function repairTables(): int
    {
        return $this->databaseOptimizerRepository->repairTables();
    }

    /**
     * @throws PerformanceProDatabaseException
     */
    public function optimizeTables(): int
    {
        return $this->databaseOptimizerRepository->optimizeTables();
    }
}
