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

final class ClearConnectionTablesCommand implements Command
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
     */
    public function execute(): array
    {
        $range = CommandUserConfig::getRangeByKey('PP_CONNECTION_TABLE_CLEANER');

        $amount = $this->databaseCleaner->cleanConnectionTables($range);

        $this->databaseCleaner->cleanGuestTable();

        return [
            'result' => true,
            'amount' => $amount,
        ];
    }
}
