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

use PrestaShop\Module\PerformancePro\domain\service\cache\CacheWarmer;

final class CacheWarmerCommand implements Command
{
    /**
     * @var CacheWarmer
     */
    private $cacheWarmer;

    public function __construct(CacheWarmer $cacheWarmer)
    {
        $this->cacheWarmer = $cacheWarmer;
    }

    /**
     * @return array{amount: mixed}
     */
    public function execute(): array
    {
        $result = $this->cacheWarmer->run()
            ->getResult();

        return [
            'amount' => $result,
        ];
    }
}
