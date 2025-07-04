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

namespace PrestaShop\Module\PerformancePro\domain\service\file;

use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidResourceException;
use Tools;

final class PublicHtaccessFile
{
    /**
     * @var string
     */
    private const PRESTASHOP_END_TAG = '# ~~end~~ Do not remove this comment, Prestashop will keep automatically the code outside this comment when .htaccess will be generated again';

    /**
     * @var string
     */
    private const MODULE_START_TAG = '# ~performance_pro_start~';

    /**
     * @var string
     */
    private const MODULE_END_TAG = '# ~performance_pro_end~';

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $content = [];

    public function __construct()
    {
        $this->path = _PS_ROOT_DIR_ . '/.htaccess';
    }

    public function setContent(string $content): self
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * @throws PerformanceProInvalidResourceException
     */
    public function replaceContent(): void
    {
        $fileContent = $this->getFileContent();

        $newContent = $this->getNewContent();

        if ('' === $fileContent || '0' === $fileContent) {
            Tools::generateHtaccess();
        }

        if (preg_match(sprintf('/%s(.*?)%s/s', self::MODULE_START_TAG, self::MODULE_END_TAG), $fileContent, $m)) {
            $removeContent = $m[0];

            $htaccessContent = str_replace($removeContent, $newContent, $fileContent);
        } else {
            $htaccessContent = str_replace(
                self::PRESTASHOP_END_TAG,
                self::PRESTASHOP_END_TAG . PHP_EOL . PHP_EOL . $newContent,
                $fileContent
            );
        }

        file_put_contents($this->path, $htaccessContent);
    }

    /**
     * @throws PerformanceProInvalidResourceException
     */
    public function reset(): void
    {
        $fileContent = $this->getFileContent();

        if ('' === $fileContent) {
            return;
        }

        if ('0' === $fileContent) {
            return;
        }

        $htaccessContent = preg_replace(
            '/' . preg_quote(self::MODULE_START_TAG, '/') . '[\s\S]+?' . preg_quote(self::MODULE_END_TAG, '/') . '/',
            '',
            $fileContent
        );

        file_put_contents($this->path, $htaccessContent);
    }

    private function getFileContent(): string
    {
        $fileContent = Tools::file_get_contents($this->path);

        if (!$fileContent) {
            throw new PerformanceProInvalidResourceException('The file is not readable.');
        }

        return (string) $fileContent;
    }

    private function getNewContent(): string
    {
        $body = implode(PHP_EOL, $this->content);

        $fileContent = [self::MODULE_START_TAG, $body, self::MODULE_END_TAG];

        return implode(PHP_EOL, $fileContent);
    }
}
