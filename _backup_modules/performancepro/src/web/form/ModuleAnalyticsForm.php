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

use Module;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\util\DirectoryService;
use PrestaShop\Module\PerformancePro\resources\config\Modules;
use PrestaShop\Module\PerformancePro\web\util\View;
use PrestaShopException;

final class ModuleAnalyticsForm extends AbstractForm
{
    /**
     * @var string[]
     */
    private const ERROR_MODULES = ['blockwishlist'];

    /**
     * @var null|mixed
     */
    public $module;

    /**
     * @var null|mixed
     */
    public $className;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getFields(): array
    {
        $result = [];

        try {
            $modules = Module::getModulesDirOnDisk();
        } catch (PrestaShopException $prestaShopException) {
            LogService::error($prestaShopException->getMessage(), $prestaShopException->getTrace());

            return $result;
        }

        $enabledBadPerformanceModules = View::filterModules(Modules::BAD_PERFORMANCE_MODULES);

        if (empty($enabledBadPerformanceModules)) {
            $warning = null;
        } else {
            $warning = sprintf(
                $this->module->l(
                    'Statistic modules slow down your website. Using a Google Analytics module like %s is much faster. It is recommended to disable/uninstall the following modules:',
                    $this->className
                ),
                View::formatStrong('PrestaShop Metrics')
            )
                . '<br>' . View::arrayToStringList($enabledBadPerformanceModules);
        }

        $enabledErrorModules = View::filterModules(self::ERROR_MODULES);

        if (empty($enabledErrorModules)) {
            $error = null;
        } else {
            $error = $this->module->l(
                'It is highly recommended to disable/uninstall the following modules as they are known to display a non-proper HTML markup:',
                $this->className
            )
                . '<br>' . View::arrayToStringList($enabledErrorModules);
        }

        if (null !== $modules) {
            foreach ($modules as $module) {
                $name = basename($module);

                $path = _PS_MODULE_DIR_ . $name;

                $result[] = [
                    $this->module->l('Display module name', $this->className) => Module::getModuleName($name),
                    $this->module->l('Technical module name', $this->className) => $name,
                    $this->module->l('Size', $this->className) => (new DirectoryService($path))
                        ->calcDirectorySize()
                        ->getAsBytes(),
                    View::displayAlign($this->module->l('Status', $this->className)) => Module::isEnabled($name)
                        ? View::displayAlign(View::displayLabelInfo($this->module->l('Enabled', $this->className)))
                        : View::displayAlign(View::displayLabelInfo($this->module->l('Disabled', $this->className))),
                ];
            }
        }

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Module Analytics', $this->className),
                    'icon' => 'icon-list',
                ],
                'description' => $this->module->l(
                    "Modules are slowing down your website. You do need modules to add features to your website, but if you have modules installed that you don't use or don't need, you can improve your page load by removing unnecessary modules. Of course, it is better to uninstall a module rather than disable it. However, disabling the modules will help you a lot as well.",
                    $this->className
                ),
                'warning' => $warning,
                'error' => $error,
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
