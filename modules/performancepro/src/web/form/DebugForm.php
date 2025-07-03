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

use PrestaShop\Module\PerformancePro\resources\config\Config;
use PrestaShop\Module\PerformancePro\web\util\View;

final class DebugForm extends AbstractForm
{
    /**
     * @var null|mixed
     */
    public $module;

    /**
     * @var null|mixed
     */
    public $className;

    /**
     * @return array{form: array{legend: array{title: mixed, icon: string}, description: string, input: array<int, array{type: string, label: mixed, name: string, is_bool: true, desc: mixed, values: array<int, array{id: string, value: bool, label: mixed}>}>, submit: array{title: mixed}}}
     */
    public function getFields(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Debug', $this->className),
                    'icon' => 'icon-bug',
                ],
                'description' => sprintf(
                    $this->module->l('Log files are save to: %s', $this->className),
                    View::formatStrong(Config::getLogPath())
                ),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Log exceptions', $this->className),
                        'name' => 'PP_LOG_EXCEPTIONS',
                        'is_bool' => true,
                        'desc' => $this->module->l(
                            'Errors can occur if something unexpected happens. The module will continue, but you can choose whether these errors should be saved in a log file or not.',
                            $this->className
                        ) . '<br>' . sprintf(
                            $this->module->l(
                                "%s Warning: It's better to disable this feature in production because logging the exceptions can take up some space on your drive.",
                                $this->className
                            ),
                            View::displayWarningIcon()
                        ),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled', $this->className),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled', $this->className),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Disable DOM parser on the order-page', $this->className),
                        'name' => 'PP_DISABLE_OPTIMIZATION_ORDER',
                        'is_bool' => true,
                        'desc' => $this->module->l(
                            'The module uses a DOM parser. If the HTML of the document is malformed, the HTML can change its layout. Some modules on the order page are known to have this issue. In short, if your order page looks weird, enable this feature to solve it.',
                            $this->className
                        ),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled', $this->className),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled', $this->className),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->module->l('Save', $this->className),
                ],
            ],
        ];
    }
}
