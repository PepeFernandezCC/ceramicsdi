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

namespace PrestaShop\Module\PerformancePro\domain\service\command;

use PrestaShop\Module\PerformancePro\domain\service\command\util\CommandUserConfig;
use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseCleaner;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;
use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidDateException;

final class ClearCartTableCommand implements Command
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
     * @throws PerformanceProDatabaseException|PerformanceProInvalidDateException
     *
     * @return array{result: bool, amount: int}
     */
    public function execute(): array
    {
        $range = CommandUserConfig::getRangeByKey('PP_CART_TABLE_CLEANER');

        $amount = $this->databaseCleaner->cleanCartTable($range);

        $this->databaseCleaner->cleanGuestTable();

        return [
            'amount' => $amount,
        ];
    }
}
