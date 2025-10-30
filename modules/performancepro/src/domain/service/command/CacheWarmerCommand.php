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

use PrestaShop\Module\PerformancePro\domain\service\cache\CacheWarmer;

if (!defined('_PS_VERSION_')) {
    exit;
}

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
