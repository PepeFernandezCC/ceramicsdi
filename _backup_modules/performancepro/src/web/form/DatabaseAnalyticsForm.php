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

namespace PrestaShop\Module\PerformancePro\web\form;

use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseSettings;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDatabaseException;
use PrestaShop\Module\PerformancePro\resources\config\Database;
use PrestaShop\Module\PerformancePro\web\util\View;

final class DatabaseAnalyticsForm extends AbstractForm
{
    /**
     * @var string
     */
    private const ICON_FILTER = '<i class="icon icon-filter"></i>';

    /**
     * @var null|mixed
     */
    public $module;

    /**
     * @var null|mixed
     */
    public $className;

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
                    View::displayBtnAjax(
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
                    View::displayMonospaceLink('%s = <span class="pp-amount">%s</span>'),
                    $checkGrid[0],
                    $checkGrid[1]
                ),
                $this->module->l('Recommended setting', $this->className) => View::displayMonospaceLink(
                    $checkGrid[0] . ' = ' . $checkGrid[2],
                    true
                ),
                View::displayAlign($this->module->l('Action', $this->className)) => View::displayAlign($checkGrid[3]),
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
                    View::displayLink(
                        'https://devdocs.prestashop.com/1.7/scale/optimizations/',
                        $this->module->l('Read more', $this->className)
                    ),
                    'my.conf'
                ),
                'input' => [
                    [
                        'type' => 'html',
                        'label' => '',
                        'html_content' => View::displayArrayAsTable($result),
                        'col' => 12,
                        'name' => '',
                    ],
                ],
            ],
        ];
    }
}
