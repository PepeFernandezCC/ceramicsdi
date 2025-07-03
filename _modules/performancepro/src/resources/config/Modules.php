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

final class Modules
{
    /**
     * @var string[]
     */
    public const BAD_PERFORMANCE_MODULES = [
        'dashactivity',
        'dashgoals',
        'dashproducts',
        'dashtrends',
        'gamification',
        'graphnvd3',
        'gridhtml',
        'pagesnotfound',
        'statsbestcategories',
        'statsbestcustomers',
        'statsbestmanufacturers',
        'statsbestproducts',
        'statsbestsuppliers',
        'statsbestvouchers',
        'statscarrier',
        'statscatalog',
        'statscheckup',
        'statsdata',
        'statsforecast',
        'statspersonalinfos',
        'statsproduct',
        'statsregistrations',
        'statssales',
        'statssearch',
        'statsstock',
        'welcome',
    ];
}
