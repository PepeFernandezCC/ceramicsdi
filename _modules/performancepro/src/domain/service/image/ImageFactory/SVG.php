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

use PrestaShop\Module\PerformancePro\domain\service\http\client\CURLClient;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDownloadResourceException;
use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidResourceException;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Tools;

final class SVG extends AbstractImageFactory
{
    public function create(string $source, string $svgDestination): bool
    {
        $tempImg = $this->getPath($svgDestination, $source);

        try {
            $curlClient = new CURLClient($source);

            if (200 !== $curlClient->getStatusCode()) {
                return false;
            }

            if (!file_put_contents($tempImg, $curlClient->getResponse())) {
                throw new PerformanceProInvalidResourceException();
            }
        } catch (PerformanceProDownloadResourceException $performanceProDownloadResourceException) {
            return false;
        }

        $this->convert($tempImg, $svgDestination);

        return true;
    }

    private function convert(string $path, string $output): void
    {
        $content = Tools::file_get_contents($path);

        if (!$content) {
            throw new InvalidResourceException('The file is not readable.');
        }

        $minifiedContent = $this->minify((string) $content);

        file_put_contents($output, $minifiedContent);
    }

    private function minify(string $result): string
    {
        $result = (string) preg_replace('#<!--(.*?)-->#', '', $result);

        $result = (string) preg_replace('#\s+#S', ' ', $result);

        $result = str_replace('> <', '><', $result);

        $result = (string) preg_replace('#(<title)(.*)(</title>)#s', '', $result);

        return (string) preg_replace('#(<desc)(.*)(</desc>)#s', '', $result);
    }
}
