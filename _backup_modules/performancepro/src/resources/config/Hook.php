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

namespace PrestaShop\Module\PerformancePro\resources\config;

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
