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

namespace PrestaShop\Module\PerformancePro\domain\service\command;

use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseCleaner;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class CleanTablesCommand implements Command
{
    /**
     * @var DatabaseCleaner
     */
    private $databaseCleaner;

    public function __construct(DatabaseCleaner $databaseCleaner)
    {
        $this->databaseCleaner = $databaseCleaner;
    }

    /**
     * @return array{result: bool, amount: int}
     *
     * @throws PerformanceProDatabaseException
     */
    public function execute(): array
    {
        $amount = 0;

        $amount += $this->databaseCleaner->cleanDoubletsConfiguration();

        $amount += $this->databaseCleaner->cleanLangConfiguration();

        $amount += $this->databaseCleaner->cleanLangTable();

        $amount += $this->databaseCleaner->cleanShopTable();

        $amount += $this->databaseCleaner->cleanStockAvailable();

        return [
            'amount' => $amount,
        ];
    }
}
