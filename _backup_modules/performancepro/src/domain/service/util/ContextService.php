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

use Context;
use Cookie;
use Link;
use Shop;
use Smarty;

final class ContextService
{
    public static function getContext()
    {
        return Context::getContext();
    }

    public static function getLanguage()
    {
        return Context::getContext()->language;
    }

    public static function getCurrency()
    {
        return Context::getContext()->currency;
    }

    public static function getSmarty(): Smarty
    {
        return Context::getContext()->smarty;
    }

    public static function getShop(): Shop
    {
        return Context::getContext()->shop;
    }

    public static function getController()
    {
        return Context::getContext()->controller;
    }

    public static function getCookie(): Cookie
    {
        return Context::getContext()->cookie;
    }

    public static function getLink(): Link
    {
        return Context::getContext()->link;
    }
}
