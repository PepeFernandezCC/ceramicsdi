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

use PrestaShop\Module\PerformancePro\domain\service\cache\ClearCache;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class FlushQueryCacheCommand implements Command
{
    /**
     * @var ClearCache
     */
    private $clearCache;

    public function __construct(ClearCache $clearCache)
    {
        $this->clearCache = $clearCache;
    }

    /**
     * @return array{result: bool}
     */
    public function execute(): array
    {
        $this->clearCache->flushQueryCache();

        return [];
    }
}
