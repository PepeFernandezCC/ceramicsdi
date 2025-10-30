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

namespace PrestaShop\Module\PerformancePro\web\form;

use PrestaShop\Module\PerformancePro\domain\service\util\LinkService;
use PrestaShop\Module\PerformancePro\web\util\View;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class DashboardForm extends AbstractForm
{
    /**
     * @var View
     */
    private $view;

    public function __construct(\Module $module)
    {
        parent::__construct($module);

        $this->view = new View();
    }

    /**
     * @var mixed|null
     */
    public $module;

    /**
     * @var mixed|null
     */
    public $className;

    /**
     * @return array{form: array{legend: array{title: mixed, icon: string}, description: string, warning: string, input: array<int, array{type: string, label: string, html_content: string, col: int, name: string}>}}
     */
    public function getFields(): array
    {
        $result = [];

        $result[]
            = $this->view->displayHeader($this->module->l('Cache', $this->className), true)
            . $this->view->displayTable($this->generateTable($this->getCacheFields()));

        $result[]
            = $this->view->displayHeader($this->module->l('Cache warmer', $this->className))
            . $this->view->displayTable($this->generateTable($this->getCacheWarmerFields()));

        $result[]
            = $this->view->displayHeader($this->module->l('Logs', $this->className))
            . $this->view->displayTable($this->generateTable($this->getLogFields()));

        $result[]
            = $this->view->displayHeader($this->module->l('Database', $this->className))
            . $this->view->displayTable($this->generateTable($this->getTableFields()));

        $result[]
            = $this->view->displayHeader($this->module->l('Statistics', $this->className))
            . $this->view->displayTable($this->generateTable($this->getStatisticFields()));

        $result[]
            = $this->view->displayHeader($this->module->l('Images', $this->className))
            . $this->view->displayTable($this->generateTable($this->getImageFields()));

        $result[]
            = $this->view->displayHeader($this->module->l('Tools', $this->className))
            . $this->view->displayTable($this->generateTable($this->getToolFields()));

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Dashboard', $this->className),
                    'icon' => 'icon-dashboard',
                ],
                'description' => $this->module->l(
                    'Cleaning up outdated data is an excellent way of improving the speed of your application. The module offers some great tools regarding this cleaning process! You can either run the action from the button or do it from a GET request by a cronjob.',
                    $this->className
                ) . '<br>' . $this->module->l(
                    "It's not advisable to clear the cache regularly. Instead, the cache should be removed if there is a good reason, otherwise every six months. This is because the cache keeps your page fast. It needs to be rebuilt every time you clear the cache, which takes time. If you have a service where you automatically sync products via an API, you may want to consider integrating some of these endpoints into your pipeline.",
                    $this->className
                ) . '<br>' . $this->module->l(
                    'For those features that have nothing to do with clearing the cache, you may want to consider setting up some cronjobs. The interval depends on many factors, but every 14-30 days is appropriate for most.',
                    $this->className
                ),
                'warning' => $this->module->l(
                    'These tools make changes in your system that cannot be undone. Therefore, it is recommended to back up the database and file system for security reasons. To avoid problems clearing smarty and SF cache, wait until it is done before doing anything else on your application.',
                    $this->className
                ) . '<br>' . $this->module->l(
                    'Some of the commands will take a while to run. So it is recommended to run one at a time. Especially the cache warmer runs long.',
                    $this->className
                ),
                'input' => [
                    [
                        'type' => 'html',
                        'label' => '',
                        'html_content' => implode('<br>', $result),
                        'col' => 12,
                        'name' => '',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, mixed>> $rows
     *
     * @return array<int, array<int|string, mixed>>
     */
    private function generateTable(array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $result[] = [
                $this->module->l('Title', $this->className) => $row[0],
                $this->module->l('Cronjob (for advanced users)', $this->className) => $this->view->displayMonospaceLink(
                    LinkService::createCronLink($row[1], null, false),
                    true
                ),
                $this->view->displayAlign($this->module->l('Action', $this->className)) => $this->view->displayAlign(
                    $this->view->displayBtnAjax(
                        $row[1],
                        sprintf($this->module->l('%s Execute', $this->className), $this->view->displayBoltIcon()),
                        $this->module->l('Are you sure?', $this->className)
                    )
                ),
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function getCacheFields(): array
    {
        $result = [
            [$this->module->l('Clear smarty and SF cache', $this->className), 'clear-smarty-cache-and-sf-cache'],
            [$this->module->l('Clear image cache', $this->className), 'clear-image-cache'],
            [$this->module->l('Clear HTTP cache', $this->className), 'clear-http-cache'],
            [$this->module->l('Clear media (CSS/JS) cache', $this->className), 'clear-media-cache'],
            [$this->module->l('Clear XML cache', $this->className), 'clear-xml-cache'],
            [$this->module->l('Reset query cache', $this->className), 'reset-query-cache'],
            [$this->module->l('Flush query cache', $this->className), 'flush-query-cache'],
        ];

        if (\function_exists('opcache_get_status')) {
            $result[] = [
                $this->module->l('Clear OP cache', $this->className),
                'clear-op-cache',
            ];
        }

        if (\function_exists('apc_clear_cache')) {
            $result[] = [
                $this->module->l('Clear APC cache', $this->className),
                'clear-apc-cache',
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function getCacheWarmerFields(): array
    {
        return [
            [$this->module->l('Build all caches', $this->className), 'build-cache'],
        ];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function getLogFields(): array
    {
        return [
            [$this->module->l('Delete log files from the filesystem', $this->className), 'clear-logs'],
            [$this->module->l('Truncate log tables the from database', $this->className), 'clear-log-table'],
            [$this->module->l('Truncate e-mail logs the from database', $this->className), 'clear-mail-table'],
        ];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function getTableFields(): array
    {
        return [
            [$this->module->l('Truncate useless leftover data', $this->className), 'clean-tables'],
            [$this->module->l('Repair tables', $this->className), 'repair-tables'],
            [$this->module->l('Optimize and defrag tables', $this->className), 'optimize-tables'],
            [$this->module->l('Change database engine to InnoDb', $this->className), 'change-engine-to-innodb'],
        ];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function getStatisticFields(): array
    {
        return [
            [$this->module->l('Delete search statistics', $this->className), 'clear-stats-search-table'],
            [$this->module->l('Delete page-not-found statistics', $this->className), 'clear-page-not-found-table'],
            [$this->module->l('Delete connections statistics', $this->className), 'clear-connection-tables'],
        ];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function getImageFields(): array
    {
        return [
            [$this->module->l('Delete unused images', $this->className), 'delete-unused-images'],
            [$this->module->l('Delete broken images', $this->className), 'delete-broken-images'],
            [$this->module->l('Delete empty image folders', $this->className), 'delete-empty-images-folder'],
            [$this->module->l('Delete temporary images', $this->className), 'clear-image-tmp-dir'],
        ];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function getToolFields(): array
    {
        return [
            [$this->module->l('Delete abandoned carts', $this->className), 'clear-cart-table'],
            [$this->module->l('Delete expired cart rules', $this->className), 'clear-cart-rule-table'],
            [
                $this->module->l('Delete expired specific prices', $this->className),
                'delete-expired-specific-prices',
            ],
        ];
    }
}
