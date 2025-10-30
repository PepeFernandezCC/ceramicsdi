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

if (!defined('_PS_VERSION_')) {
    exit;
}

final class DirectoryService
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var float|int
     */
    private $size;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function calcDirectorySize(): self
    {
        $result = 0;

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(
            $this->path,
            \FilesystemIterator::SKIP_DOTS
        ));

        foreach ($files as $file) {
            $result += $file->getSize();
        }

        $this->size = $result;

        return $this;
    }

    public function getAsBytes(): string
    {
        return \Tools::formatBytes($this->size);
    }

    public function countFilesInDirectory(): int
    {
        return iterator_count(new \FilesystemIterator($this->path, \FilesystemIterator::SKIP_DOTS));
    }
}
