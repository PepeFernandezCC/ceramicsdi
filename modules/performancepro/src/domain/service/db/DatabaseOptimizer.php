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

use PrestaShop\Module\PerformancePro\data\repository\DatabaseOptimizerRepository;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;

if (!defined('_PS_VERSION_')) {
    exit;
}

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
