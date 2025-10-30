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

namespace PrestaShop\Module\PerformancePro\domain\service\util;

use PrestaShop\Module\PerformancePro\domain\model\configuration\DefineValueConfiguration;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDefineValueException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class DefineValueService
{
    /**
     * @throws PerformanceProDefineValueException
     */
    public function updateValue(string $key, string $value): void
    {
        $file = _PS_CONFIG_DIR_ . '/defines.inc.php';

        (new DefineValueConfiguration($file, $key, $value))->configure();

        if (\function_exists('opcache_invalidate')) {
            opcache_invalidate($file);
        }
    }
}
