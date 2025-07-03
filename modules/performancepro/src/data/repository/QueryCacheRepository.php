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

final class QueryCacheRepository extends Connection
{
    public function resetQueryCache(): void
    {
        $this->getConnection()
            ->execute('RESET QUERY CACHE');
    }

    public function flushQueryCache(): void
    {
        $this->getConnection()
            ->execute('FLUSH QUERY CACHE');
    }
}
