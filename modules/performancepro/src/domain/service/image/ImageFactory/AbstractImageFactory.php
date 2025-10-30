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

namespace PrestaShop\Module\PerformancePro\domain\service\image\ImageFactory;

use PrestaShop\Module\PerformancePro\domain\service\util\PathService;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AbstractImageFactory
{
    protected function getPath(string $source, string $url): string
    {
        return PathService::createPath(\dirname($source)) . '/' . $this->removeParams(basename($url));
    }

    private function removeParams(string $src): string
    {
        return (string) strtok($src, '?');
    }
}
