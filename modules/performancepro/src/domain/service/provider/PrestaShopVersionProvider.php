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

namespace PrestaShop\Module\PerformancePro\domain\service\provider;

use PrestaShop\Module\PerformancePro\domain\service\http\proxy\SimpleCache;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidResourceException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class PrestaShopVersionProvider
{
    /**
     * @var string
     */
    private const PRESTASHOP_API = 'https://api.prestashop.com/xml/channel.xml';

    public function isPrestaShopUpToDate(): bool
    {
        try {
            return !\Tools::version_compare(_PS_VERSION_, $this->getPrestashopLatestVersion());
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
