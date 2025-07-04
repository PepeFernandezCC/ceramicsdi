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

namespace PrestaShop\Module\PerformancePro\domain\service\db;

use Configuration;

final class DatabaseConfiguration
{
    public function updateValue(string $key, string $value): void
    {
        Configuration::updateValue($key, $value);
    }
}
