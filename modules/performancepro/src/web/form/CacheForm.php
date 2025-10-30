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

use PrestaShop\Module\PerformancePro\web\util\View;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class CacheForm extends AbstractForm
{
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
     * @return array{form: array{legend: array{title: mixed, icon: string}, description: mixed, input: array<int, array{type: string, label: mixed, name: string, is_bool: true, desc: string, values: array<int, array{id: string, value: bool, label: mixed}>}>, submit: array{title: mixed}}}
     */
    public function getFields(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Cache', $this->className),
                    'icon' => 'icon-rocket',
                ],
                'description' => $this->module->l(
                    "The module serves pages of the website as static content. Therefore, new visitors and crawlers will load your web pages much faster. Once a product is added to the cart or the user login to the website, the content gets dynamic, and PrestaShop's default behaviour is used instead of a static page cache. This is done to prevent conflicts with other modules and to avoid overrides. However, static resources like CSS and JS will still be loaded from the browser cache.",
                    $this->className
                ),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Page Cache', $this->className),
                        'name' => 'PP_PAGE_CACHE',
                        'is_bool' => true,
                        'desc' => $this->module->l(
                            'The module serves as a static HTML document of the website. As a result, new visitors and crawlers will load your web pages much faster.',
                            $this->className
                        )
                            . '<br>' . sprintf(
                                $this->module->l(
                                    '%s Information: If your webshop is in debug mode, the page cache will be turned off.',
                                    $this->className
                                ),
                                $this->view->displayInformationIcon()
                            ) . '<br>' . sprintf(
                                $this->module->l(
                                    '%s Information: Some cookie-policy modules are not compatible with this cached technique.',
                                    $this->className
                                ),
                                $this->view->displayInformationIcon()
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
                        'label' => $this->module->l('Browser cache', $this->className),
                        'name' => 'PP_HTACCESS_CACHE_CONTROL',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'Enabling browser cache, adds Cache-Control HTTP header holds directives for caching in both requests and responses (Apache only). %s.',
                                $this->className
                            ),
                            $this->view->displayLink(
                                'https://web.dev/http-cache/',
                                $this->module->l('Read more', $this->className)
                            )
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
