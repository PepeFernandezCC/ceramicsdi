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

use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseOptimizer;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;

final class ChangeEngineToInnoDbCommand implements Command
{
    /**
     * @var DatabaseOptimizer
     */
    private $databaseOptimizer;

    public function __construct(DatabaseOptimizer $databaseOptimizer)
    {
        $this->databaseOptimizer = $databaseOptimizer;
    }

    /**
     * @throws PerformanceProDatabaseException
     *
     * @return array{result: bool, amount: int}
     */
    public function execute(): array
    {
        $amount = $this->databaseOptimizer->changeEngineToInnoDb();

        return [
            'amount' => $amount,
        ];
    }
}
