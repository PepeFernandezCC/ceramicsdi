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
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDownloadResourceException;
use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidResourceException;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use WebPConvert\WebPConvert;

final class WebP extends AbstractImageFactory
{
    public function create(string $source, string $webpDestination, array $options): bool
    {
        $tempImg = $this->getPath($webpDestination, $source);

        try {
            $curlClient = new CURLClient($source);

            if (200 !== $curlClient->getStatusCode()) {
                return false;
            }

            if (!\in_array($curlClient->getContentType(), ['image/jpeg', 'image/png'], true)) {
                return false;
            }

            if (!file_put_contents($tempImg, $curlClient->getResponse())) {
                throw new PerformanceProInvalidResourceException();
            }
        } catch (PerformanceProDownloadResourceException $performanceProDownloadResourceException) {
            LogService::error(
                $performanceProDownloadResourceException->getMessage(),
                $performanceProDownloadResourceException->getTrace()
            );

            return false;
        }

        if (!file_exists($tempImg)) {
            return false;
        }

        try {
            WebPConvert::convert($tempImg, $webpDestination, $options);

            if (file_exists($tempImg)) {
                unlink($tempImg);
            }
        } catch (ConversionFailedException $conversionFailedException) {
            LogService::error($conversionFailedException->getMessage(), $conversionFailedException->getTrace());
        }

        return true;
    }
}
