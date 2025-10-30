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

use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseSettings;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;
use PrestaShop\Module\PerformancePro\resources\config\Database;
use PrestaShop\Module\PerformancePro\web\util\View;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class DatabaseAnalyticsForm extends AbstractForm
{
    /**
     * @var string
     */
    private const ICON_FILTER = '<i class="icon icon-filter"></i>';

    /**
     * @var mixed|null
     */
    public $module;

    /**
     * @var mixed|null
     */
    public $className;

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
     * @return array{form: array{legend: array{title: mixed, icon: string}, description: string, input: array<int, array{type: string, label: string, html_content: string, col: int, name: string}>}}
     */
    public function getFields(): array
    {
        $settings = Database::getDatabaseSettings();

        $checkGrids = [];

        $databaseSettings = new DatabaseSettings();

        foreach ($settings as $setting => $key) {
            try {
                $checkGrids[] = $databaseSettings->formatConfigKey(
                    $setting,
                    (string) $key,
                    $this->view->displayBtnAjax(
                        'updateDbValue',
                        sprintf($this->module->l('%s Optimize value', $this->className), self::ICON_FILTER),
                        $this->module->l('Are you sure?', $this->className),
                        $setting
                    ),
                    is_numeric($key)
                );
            } catch (PerformanceProDatabaseException $performanceProDatabaseException) {
                LogService::error(
                    $performanceProDatabaseException->getMessage(),
                    $performanceProDatabaseException->getTrace()
                );
            }
        }

        $result = [];

        foreach ($checkGrids as $checkGrid) {
            $result[] = [
                $this->module->l('Current setting', $this->className) => sprintf(
                    $this->view->displayMonospaceLink('%s = <span class="pp-amount">%s</span>'),
                    $checkGrid[0],
                    $checkGrid[1]
                ),
                $this->module->l('Recommended setting', $this->className) => $this->view->displayMonospaceLink(
                    $checkGrid[0] . ' = ' . $checkGrid[2],
                    true
                ),
                $this->view->displayAlign($this->module->l('Action', $this->className)) => $this->view->displayAlign($checkGrid[3]),
            ];
        }

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Database Analytics', $this->className),
                    'icon' => 'icon-list',
                ],
                'description' => sprintf(
                    $this->module->l(
                        'Here are some advanced tips for configuring your database for best performance. These settings are recommended for most PrestaShop websites. %s. By clicking "Optimize value", you update the value to the recommended value. This value is saved until the database is restarted. If you want to change to value permanent, you must do it in %s. The location of your database configuration file depends on your webserver setup.',
                        $this->className
                    ),
                    $this->view->displayLink(
                        'https://devdocs.prestashop.com/1.7/scale/optimizations/',
                        $this->module->l('Read more', $this->className)
                    ),
                    'my.conf'
                ),
                'input' => [
                    [
                        'type' => 'html',
                        'label' => '',
                        'html_content' => $this->view->displayTable($result),
                        'col' => 12,
                        'name' => '',
                    ],
                ],
            ],
        ];
    }
}
