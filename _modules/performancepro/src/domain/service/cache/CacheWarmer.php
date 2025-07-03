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

namespace PrestaShop\Module\PerformancePro\domain\service\cache;

use DOMDocument;
use PrestaShop\Module\PerformancePro\domain\service\http\proxy\SimpleCache;
use PrestaShop\Module\PerformancePro\resources\config\Config;

final class CacheWarmer
{
    /**
     * @var array
     */
    private $sitemaps;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var int
     */
    private $count = 0;

    /**
     * @param string[] $sitemaps
     */
    public function __construct(array $sitemaps)
    {
        $this->sitemaps = $sitemaps;

        $this->userAgent = Config::USER_AGENT;
    }

    public function run(): self
    {
        if (!empty(array_filter($this->sitemaps))) {
            $this->crawler($this->normalizeSitemaps($this->sitemaps));
        }

        return $this;
    }

    /**
     * @param string[] $sitemaps
     */
    private function crawler(array $sitemaps): void
    {
        foreach ($sitemaps as $sitemap) {
            $dom = $this->getDom($this->getData($sitemap));

            $nodes = $dom->getElementsByTagName('loc');

            $urls = [];

            foreach ($nodes as $node) {
                $urls[] = $node->nodeValue;
            }

            foreach ($urls as $url) {
                $this->call($url);

                ++$this->count;
            }
        }
    }

    private function getDom(string $sitemap): DOMDocument
    {
        $domDocument = new DOMDocument('1.0', 'UTF-8');

        $domDocument->preserveWhiteSpace = false;

        $domDocument->validateOnParse = false;

        $domDocument->strictErrorChecking = false;

        $domDocument->recover = true;

        $domDocument->formatOutput = false;

        libxml_use_internal_errors(true);

        $domDocument->loadXML($sitemap);

        libxml_clear_errors();

        return $domDocument;
    }

    private function getData(string $source): string
    {
        $key = basename($source);

        return (new SimpleCache())
            ->expiresAfter(360)
            ->get($key, $source);
    }

    /**
     * Keep this method in the class due to performance reasons.
     */
    private function call(string $url): void
    {
        $curlHandle = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => '',
            CURLOPT_REFERER => '',
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => $this->userAgent,
        ];

        curl_setopt_array($curlHandle, $options);

        curl_exec($curlHandle);

        curl_close($curlHandle);
    }

    /**
     * @return string[]
     */
    private function normalizeSitemaps(array $sitemaps): array
    {
        $result = [];

        foreach ($sitemaps as $sitemap) {
            $dom = $this->getDom($this->getData($sitemap));

            $tags = $dom->getElementsByTagName('sitemapindex');

            if (!empty($tags->length)) {
                $tags = $dom->getElementsByTagName('loc');

                foreach ($tags as $tag) {
                    $result[] = $tag->nodeValue;
                }
            }

            $tags = $dom->getElementsByTagName('urlset');

            if (!empty($tags->length)) {
                $result[] = $sitemap;
            }
        }

        return $result;
    }

    public function getResult(): int
    {
        return $this->count;
    }
}
