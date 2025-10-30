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

namespace PrestaShop\Module\PerformancePro\resources\config;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class Hook
{
    private function __construct()
    {
    }

    /**
     * Returns array of used hooks.
     *
     * @return array<string>
     */
    public static function getHooks(): array
    {
        return [
            'actionClearCache',
            'actionClearCompileCache',
            'actionAdminControllerSetMedia',
            'actionDispatcher',
            'actionFrontControllerSetMedia',
            'actionOutputHTMLBefore',
            'actionCategoryAdd',
            'actionCategoryDelete',
            'actionCategoryUpdate',
            'actionProductAdd',
            'actionProductDelete',
            'actionProductUpdate',
            'displayBackOfficeTop',
            'displayBeforeBodyClosingTag',
            'header',
        ];
    }
}
