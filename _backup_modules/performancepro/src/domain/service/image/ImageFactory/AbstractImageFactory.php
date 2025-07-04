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

namespace PrestaShop\Module\PerformancePro\domain\service\image\ImageFactory;

use PrestaShop\Module\PerformancePro\domain\service\util\PathService;

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
