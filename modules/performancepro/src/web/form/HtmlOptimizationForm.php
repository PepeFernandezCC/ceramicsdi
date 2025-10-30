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

final class HtmlOptimizationForm extends AbstractForm
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
     * @return array{form: array{legend: array{title: mixed, icon: string}, description: string, warning: mixed, input: array<int, array{type: string, label: mixed, name: string, is_bool: true, desc: string, values: array<int, array{id: string, value: bool, label: mixed}>}>, submit: array{title: mixed}}}
     */
    public function getFields(): array
    {
        $minifyHtml = [
            $this->module->l('Remove default HTML comments.', $this->className),
            $this->module->l('Sum up extra whitespace from the DOM.', $this->className),
            $this->module->l('Remove whitespace around tags.', $this->className),
        ];

        $optimizeAttributes = [
            $this->module->l('Remove deprecated anchor jump.', $this->className),
            $this->module->l('Remove deprecated script-mime-types.', $this->className),
            $this->module->l('Remove some empty attributes.', $this->className),
            $this->module->l('Remove value tag from empty input tag.', $this->className),
            $this->module->l('Remove deprecated charset-attribute - the browser will use the charset from the HTTP-Header, anyway.', $this->className),
            $this->module->l('Remove "media="all" from all links and styles.', $this->className),
            $this->module->l('Sort CSS-class-names for better Gzip/DEFLATE results.', $this->className),
            $this->module->l('Sort HTML attributes for better Gzip/DEFLATE results.', $this->className),
            $this->module->l("Remove quotes attributes if they don't contain characters that necessitate quoting.", $this->className),
            $this->module->l('Remove omitted HTML tags.', $this->className),
        ];

        $link = 'https://validator.w3.org/nu/?doc=' . LinkService::getBaseLink();

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('HTML Optimization', $this->className),
                    'icon' => 'icon-html5',
                ],
                'description' => sprintf(
                    $this->module->l(
                        'Safely reduce the size of your HTML code, making your websites load faster. These features can also optimize the HTML document to follow modern HTML5 markups. You can scan your web application to see the difference using the %s.',
                        $this->className
                    ),
                    $this->view->displayLink($link, $this->module->l('W3C validator', $this->className))
                ),
                'warning' => $this->module->l(
                    'Testing the website with and without the HTML optimization features is recommended. This is because, in some cases, some of these features could negatively affect the performance.',
                    $this->className
                ),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Minify HTML', $this->className),
                        'name' => 'PP_MINIFY_HTML',
                        'is_bool' => true,
                        'desc' => '</p>'
                            . $this->view->displayList($minifyHtml, 'help-block')
                            . '<p>',
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
                        'label' => $this->module->l('Optimize attributes', $this->className),
                        'name' => 'PP_OPTIMIZE_ATTRIBUTES',
                        'is_bool' => true,
                        'desc' => '</p>'
                            . $this->view->displayList($optimizeAttributes, 'help-block')
                            . '<p>',
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
                        'label' => $this->module->l('Defer Javascript', $this->className),
                        'name' => 'PP_LOAD_SCRIPT_ASYNC',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'Improve site\'s performance by adding "defer" tag to the external combined javascript bundle. %s.',
                                $this->className
                            ),
                            $this->view->displayLink(
                                'https://web.dev/efficiently-load-third-party-javascript/',
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
                        'label' => $this->module->l('Async image decoding', $this->className),
                        'name' => 'PP_DECODE_IMG_ASYNC',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'Decode the image asynchronously. Rendering of pages and decoding of the image is done in parallel. This makes the page render faster. %s.',
                                $this->className
                            ),
                            $this->view->displayLink(
                                'https://usefulangle.com/post/277/img-decoding-attribute',
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
                        'label' => $this->module->l('Add missing image size', $this->className),
                        'name' => 'PP_IMG_SIZE',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l('Add missing size attributes to the images. %s.', $this->className),
                            $this->view->displayLink(
                                'https://web.dev/optimize-cls/',
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
