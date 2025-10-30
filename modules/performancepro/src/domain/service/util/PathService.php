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

use PrestaShop\Module\PerformancePro\resources\config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class PathService
{
    public static function createPath(string $path, bool $protected = false): string
    {
        if (!file_exists($path)) {
            mkdir($path, Config::DEFAULT_MODE_FOLDER, true);

            if ($protected) {
                file_put_contents($path . '.htaccess', self::htaccessContent());
            }
        }

        return $path;
    }

    private static function htaccessContent(): string
    {
        return '<IfModule mod_authz_host>
    Require all denied
</IfModule>
<IfModule !mod_authz_host>
    Order Allow,Deny
    Deny from all
</IfModule>';
    }
}
