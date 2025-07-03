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

namespace PrestaShop\Module\PerformancePro\domain\service\provider;

use PrestaShop\Module\PerformancePro\domain\service\http\proxy\SimpleCache;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidResourceException;
use Tools;

final class PrestaShopVersionProvider
{
    /**
     * @var string
     */
    private const PRESTASHOP_API = 'https://api.prestashop.com/xml/channel.xml';

    public function isPrestaShopUpToDate(): bool
    {
        try {
            return !(Tools::version_compare(_PS_VERSION_, $this->getPrestashopLatestVersion()));
        } catch (PerformanceProInvalidResourceException $performanceProInvalidResourceException) {
            LogService::error(
                $performanceProInvalidResourceException->getMessage(),
                $performanceProInvalidResourceException->getTrace()
            );

            return false;
        }
    }

    /**
     * @throws PerformanceProInvalidResourceException
     */
    public function getPrestashopLatestVersion(): string
    {
        $content = (new SimpleCache())
            ->expiresAfter(3600)
            ->get(self::PRESTASHOP_API, self::PRESTASHOP_API);

        if ('' === $content || '0' === $content) {
            throw new PerformanceProInvalidResourceException('Unable to get content.');
        }

        $xml = simplexml_load_string($content);

        if (!$xml) {
            throw new PerformanceProInvalidResourceException('Unable to get content.');
        }

        return (string) $xml->channel->branch[3]->num[0];
    }
}
