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

use PrestaShop\Module\PerformancePro\domain\service\cache\ClearCache;

final class ClearXmlCacheCommand implements Command
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
        $this->clearCache->clearXmlCache();

        return [];
    }
}
