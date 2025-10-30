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

final class PageOptimizationForm extends AbstractForm
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
     * @return array{form: array<string, mixed>}
     */
    public function getFields(): array
    {
        $enabledIncompatibleModules = $this->view->filterModules(['pm_advancedsearch4']);

        if (empty($enabledIncompatibleModules) || !\Configuration::get('PP_ORIGIN_AGENT_CLUSTER')) {
            $error = null;
        } else {
            $error = $this->module->l(
                'Following installed modules are not compatible with "Origin Agent Cluster":',
                $this->className
            )
                . '<br>' . $this->view->displayList($enabledIncompatibleModules);
        }

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Page Optimization', $this->className),
                    'icon' => 'icon-lightbulb-o',
                ],
                'description' => $this->module->l(
                    'Improves the speed of your website by optimizing your application on different criteria. Using these features will also tune your application to follow best practices.',
                    $this->className
                ),
                'error' => $error,
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Use passive event listeners', $this->className),
                        'name' => 'PP_USE_PASSIVE_LISTENERS',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'Improve scrolling performance by adding a passive flag to every passive event listener. %s.',
                                $this->className
                            ),
                            $this->view->displayLink(
                                'https://web.dev/uses-passive-event-listeners/',
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
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Gzip/DEFLATE', $this->className),
                        'name' => 'PP_HTACCESS_DEFLATE',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'Enable Gzip/DEFLATE output filter that allows output from your server to be compressed before being sent to the client over the network (Apache only). %s.',
                                $this->className
                            ),
                            $this->view->displayLink(
                                'https://httpd.apache.org/docs/2.4/mod/mod_deflate.html',
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
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('HTTP/2 push CSS', $this->className),
                        'name' => 'PP_CSS_HTTP2_PUSH',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'HTTP/2 server push sends the CSS in the same request as HTML in the first request. After the first request, the CSS is loaded from the browser cache instead. %s.',
                                $this->className
                            ),
                            $this->view->displayLink(
                                'https://medium.com/@mena.meseha/http-2-server-push-tutorial-d8714154ef9a',
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
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Origin Agent Cluster', $this->className),
                        'name' => 'PP_ORIGIN_AGENT_CLUSTER',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'Instructs the browser to prevent synchronous scripting access between same-site cross-origin pages. %s.',
                                $this->className
                            ),
                            $this->view->displayLink('https://web.dev/origin-agent-cluster/', $this->module->l(
                                'Read more',
                                $this->className
                            ))
                        ) . '<br>' . sprintf(
                            $this->module->l(
                                '%s Warning: Not all modules are compatible with this feature.',
                                $this->className
                            ),
                            $this->view->displayWarningIcon()
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
                        'label' => $this->module->l('Add noopener', $this->className),
                        'name' => 'PP_ADD_NOOPENER',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'Improve the site\'s performance by adding rel="noopener" to all links. %s.',
                                $this->className
                            ),
                            $this->view->displayLink(
                                'https://web.dev/external-anchors-use-rel-noopener/',
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
