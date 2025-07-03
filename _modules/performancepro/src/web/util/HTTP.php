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

namespace PrestaShop\Module\PerformancePro\web\util;

use Tools;

final class HTTP
{
    public static function isAjax(): bool
    {
        return false !== Tools::getValue('ajax');
    }

    public static function isGet(): bool
    {
        return 'GET' === $_SERVER['REQUEST_METHOD'];
    }
}
