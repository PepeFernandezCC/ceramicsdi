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

namespace PrestaShop\Module\PerformancePro\domain\service\util;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ContextService
{
    public static function getLanguage()
    {
        return \Context::getContext()->language;
    }

    public static function getContext()
    {
        return \Context::getContext();
    }

    public static function getCurrency()
    {
        return \Context::getContext()->currency;
    }

    public static function getSmarty(): \Smarty
    {
        return \Context::getContext()->smarty;
    }

    public static function getShop(): \Shop
    {
        return \Context::getContext()->shop;
    }

    public static function getController()
    {
        return \Context::getContext()->controller;
    }

    public static function getCookie(): \Cookie
    {
        return \Context::getContext()->cookie;
    }

    public static function getLink(): \Link
    {
        return \Context::getContext()->link;
    }
}
