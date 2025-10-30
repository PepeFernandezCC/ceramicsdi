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

namespace PrestaShop\Module\PerformancePro\domain\model\configuration;

use PrestaShop\Module\PerformancePro\exception\PerformanceProDefineValueException;
use Symfony\Component\Translation\Exception\InvalidResourceException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class DefineValueConfiguration
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @throws PerformanceProDefineValueException
     */
    public function __construct(string $file, string $key, string $value)
    {
        $this->setFile($file);

        $this->setKey($key);

        $this->setValue($value);
    }

    /**
     * @throws PerformanceProDefineValueException
     */
    private function setFile(string $file): void
    {
        if (!is_readable($file)) {
            throw new PerformanceProDefineValueException('The file is not readable.');
        }

        $this->file = $file;
    }

    /**
     * @throws PerformanceProDefineValueException
     */
    private function setKey(string $key): void
    {
        $cleanedFileContent = php_strip_whitespace($this->file);

        if (!preg_match('/define\(\'' . $key . '\', ([a-zA-Z]+)\);/Ui', $cleanedFileContent)) {
            throw new PerformanceProDefineValueException('Unable to find the defined key.');
        }

        $this->key = $key;
    }

    private function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @throws PerformanceProDefineValueException
     */
    public function configure(): void
    {
        $content = \Tools::file_get_contents($this->file);

        if (!$content) {
            throw new InvalidResourceException('The file is not readable.');
        }

        $content = preg_replace(
            '/define\(\'' . $this->key . '\', ([a-zA-Z]+)\);/Ui',
            "define('" . $this->key . "', " . $this->value . ');',
            (string) $content
        );

        if (!file_put_contents($this->file, $content)) {
            throw new PerformanceProDefineValueException('The file is not writeable.');
        }
    }
}
