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

namespace PrestaShop\Module\PerformancePro\domain\service\validation;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ServerSettingsValidator
{
    /**
     * @return array<int, bool|string>
     */
    public function checkBoolean(string $key, bool $recommended, bool $default): array
    {
        $iniKey = (string) \ini_get($key);

        $current = $this->isOn($iniKey) ?: $default;

        $status = $current !== $recommended;

        return [$key, $current ? 'On' : 'Off', $recommended ? 'On' : 'Off', $status];
    }

    private function isOn(string $key): bool
    {
        if ('0' === $key) {
            return false;
        }

        if ('off' === \Tools::strtolower($key)) {
            return false;
        }

        return '' !== \Tools::strtolower($key);
    }

    /**
     * @return array<int, bool|string>
     */
    public function checkString(string $key, string $recommended): array
    {
        $current = \ini_get($key) ?: '';

        $status = $current !== $recommended;

        return [$key, $current, $recommended, $status];
    }

    /**
     * @return array<int, bool|int|string>
     */
    public function checkInteger(string $key, int $recommended): array
    {
        $current = \ini_get($key) ?: '';

        $status = (int) $current !== $recommended;

        return [$key, $current, $recommended, $status];
    }

    /**
     * @return array<int, bool|string>
     */
    public function checkByte(string $key, string $recommended): array
    {
        $current = \ini_get($key) ?: '';

        $status = \Tools::convertBytes($current) !== \Tools::convertBytes($recommended);

        return [$key, $current, $recommended, $status];
    }
}
