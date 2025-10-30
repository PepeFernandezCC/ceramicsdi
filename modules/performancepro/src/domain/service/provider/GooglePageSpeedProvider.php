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

if (!defined('_PS_VERSION_')) {
    exit;
}

final class GooglePageSpeedProvider
{
    /**
     * @var string
     */
    private const GOOGLE_PAGE_SPEED_API = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    /**
     * @var string
     */
    private $url;

    /**
     * @return array<string>
     *
     * @throws \Exception
     */
    public function getFontDisplay(): array
    {
        return $this->getGooglePageSpeedResult('font-display');
    }

    /**
     * @return array<string>
     *
     * @throws \Exception
     */
    private function getGooglePageSpeedResult(string $type): array
    {
        $result = [];

        $googlePageSpeedReport = $this->getGooglePageSpeedReport();

        $googlePageSpeedResults = (array) json_decode($googlePageSpeedReport, true);

        if (isset($googlePageSpeedResults['error'])) {
            return $result;
        }

        $audits = (array) $googlePageSpeedResults['lighthouseResult']['audits'][$type]['details']['items'];

        foreach ($audits as $audit) {
            $result[] = $audit['url'];
        }

        return $result;
    }

    /**
     * @throws \Exception
     */
    private function getGooglePageSpeedReport(): string
    {
        $params = [
            'url' => $this->url,
            'category' => 'performance',
        ];

        return (new SimpleCache())
            ->expiresAfter(3600)
            ->get(self::GOOGLE_PAGE_SPEED_API, self::GOOGLE_PAGE_SPEED_API . '?' . http_build_query($params));
    }

    /**
     * @return array<string>
     *
     * @throws \Exception
     */
    public function getUserRelPreconnect(): array
    {
        return $this->getGooglePageSpeedResult('uses-rel-preconnect');
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
