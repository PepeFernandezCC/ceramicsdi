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
use PrestaShop\Module\PerformancePro\resources\config\Database;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class PHPVersionProvider
{
    /**
     * @var string
     */
    private const PHP_API = 'https://www.php.net/releases/';

    public function isPhpVersionUpToDate(): bool
    {
        return (bool) \Tools::version_compare(
            \Tools::checkPhpVersion(),
            $this->getNewestPhpVersionForThisPsVersion(),
            '>='
        );
    }

    public function getNewestPhpVersionForThisPsVersion(): string
    {
        $latestVersion = '';

        foreach (Database::getRecommendedDatabaseVersions() as $version => $phpVersion) {
            if (\Tools::version_compare(_PS_VERSION_, $version, '>=')) {
                $latestVersion = $phpVersion;
            }
        }

        return $this->getNewestPhpVersion($latestVersion);
    }

    private function getNewestPhpVersion(string $currentVersion): string
    {
        $params = [
            'json' => '1',
            'version' => $currentVersion,
        ];

        $content = (new SimpleCache())
            ->expiresAfter(3600)
            ->get(self::PHP_API, self::PHP_API . '?' . http_build_query($params));

        $versions = (array) json_decode($content, true);

        return $versions['version'] ?: '';
    }
}
