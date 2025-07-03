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

namespace PrestaShop\Module\PerformancePro\domain\service\util;

use PrestaShop\Module\PerformancePro\resources\config\Config;
use Tools;

final class LinkService
{
    private function __construct()
    {
    }

    public static function createCronLink(string $technicalName, $key, bool $ajax): string
    {
        $tokenKey = $ajax ? 'ajax' : 'cron';

        $token = Tools::hashIV(Config::MODULE_NAME . '/' . $tokenKey . $technicalName);

        return ContextService::getLink()->getModuleLink(
            Config::MODULE_NAME,
            'cron',
            [
                'name' => $technicalName,
                'token' => $token,
                'ajax' => $ajax ? true : null,
                'key' => $key,
            ]
        );
    }

    public static function createNormalizedLink(string $url): string
    {
        $url = strtr($url, '\\', '//');

        return str_replace('//', '/', $url);
    }

    public static function createRelativeLink(string $url): string
    {
        return preg_replace('#^(https?:)?//[^/]+(/?.*)#i', '$2', $url);
    }

    public static function createAbsoluteLink(string $url): string
    {
        $base = (array) parse_url(self::getBaseLink() . '/');

        $newUrl = parse_url($url);

        if (!empty($newUrl['scheme'])) {
            return $url;
        }

        if (!empty($newUrl['host'])) {
            return $base['scheme'] . ':' . $url;
        }

        if (!empty($newUrl['path'])) {
            unset($base['query'], $base['fragment']);

            if ('/' !== mb_substr($newUrl['path'], 0, 1)) {
                $array = explode('/', $newUrl['path']);

                if (!empty($base['path'])) {
                    $_array = explode('/', $base['path']);

                    $_array = \array_slice($_array, 1, -1);

                    $array = array_merge($_array, $array);
                }

                $path = [];

                foreach ($array as $singleArray) {
                    if ('..' === $singleArray) {
                        array_pop($path);
                    } elseif ('.' !== $singleArray) {
                        $path[] = $singleArray;
                    }
                }

                $newUrl['path'] = '/' . implode('/', $path);
            }
        } elseif (!empty($newUrl['query'])) {
            unset($base['fragment']);
        }

        return self::createLink(array_merge($base, $newUrl));
    }

    public static function getBaseLink(): string
    {
        return self::getLink() . __PS_BASE_URI__;
    }

    public static function getLink(bool $http = true): string
    {
        return Tools::getHttpHost($http, true, true);
    }

    private static function createLink(array $parts): string
    {
        $result = $parts['scheme'] . '://';

        if (!empty($parts['user'])) {
            $result .= $parts['user'];
        }

        if (!empty($parts['pass'])) {
            $result .= ':' . $parts['pass'];
        }

        if (!empty($parts['user'])) {
            $result .= '@';
        }

        $result .= $parts['host'];

        if (!empty($parts['port'])) {
            $result .= ':' . $parts['port'];
        }

        if (!empty($parts['path'])) {
            $result .= $parts['path'];
        }

        if (!empty($parts['query'])) {
            $result .= '?' . $parts['query'];
        }

        if (!empty($parts['fragment'])) {
            $result .= '#' . $parts['fragment'];
        }

        return $result;
    }
}
