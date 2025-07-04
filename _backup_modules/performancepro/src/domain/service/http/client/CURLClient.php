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

namespace PrestaShop\Module\PerformancePro\domain\service\http\client;

use PrestaShop\Module\PerformancePro\exception\PerformanceProDownloadResourceException;
use PrestaShop\Module\PerformancePro\resources\config\Config;

final class CURLClient
{
    /**
     * @var int
     */
    private const TIMEOUT = 60;

    /**
     * @var int
     */
    private const MAXREDIRS = 5;

    /**
     * @var int
     */
    private const CONNECTTIMEOUT = 5;

    /**
     * @var string
     */
    private const ENCODING = '';

    /**
     * @var string
     */
    private const REFERER = '';

    /**
     * @var bool
     */
    private const FOLLOWLOCATION = true;

    /**
     * @var bool
     */
    private const RETURNTRANSFER = true;

    /**
     * @var bool
     */
    private const SSL_VERIFYPEER = false;

    /**
     * @var bool
     */
    private const SSL_VERIFYHOST = false;

    /**
     * @var bool
     */
    private const HEADER = false;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $body;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @throws PerformanceProDownloadResourceException
     */
    public function __construct(string $source)
    {
        $this->source = str_replace(' ', '%20', $source);

        $this->response();
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponse(): string
    {
        return $this->body;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @throws PerformanceProDownloadResourceException
     */
    private function response(): void
    {
        $curlHandle = curl_init();

        if (!$curlHandle) {
            throw new PerformanceProDownloadResourceException('Failed to connect with CURL');
        }

        $options = [
            \CURLOPT_URL => $this->source,
            \CURLOPT_RETURNTRANSFER => self::RETURNTRANSFER,
            \CURLOPT_HEADER => self::HEADER,
            \CURLOPT_FOLLOWLOCATION => self::FOLLOWLOCATION,
            \CURLOPT_ENCODING => self::ENCODING,
            \CURLOPT_REFERER => self::REFERER,
            \CURLOPT_CONNECTTIMEOUT => self::CONNECTTIMEOUT,
            \CURLOPT_TIMEOUT => self::TIMEOUT,
            \CURLOPT_MAXREDIRS => self::MAXREDIRS,
            \CURLOPT_SSL_VERIFYPEER => self::SSL_VERIFYPEER,
            \CURLOPT_SSL_VERIFYHOST => self::SSL_VERIFYHOST,
            \CURLOPT_USERAGENT => Config::USER_AGENT,
        ];

        curl_setopt_array($curlHandle, $options);

        $this->body = curl_exec($curlHandle);

        $error = curl_error($curlHandle);

        $this->statusCode = curl_getinfo($curlHandle, \CURLINFO_HTTP_CODE);

        $this->contentType = curl_getinfo($curlHandle, \CURLINFO_CONTENT_TYPE);

        curl_close($curlHandle);

        if ('' === $error) {
            return;
        }

        if ('0' === $error) {
            return;
        }

        throw new PerformanceProDownloadResourceException($error);
    }
}
