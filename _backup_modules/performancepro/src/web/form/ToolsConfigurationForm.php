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

final class ToolsConfigurationForm extends AbstractForm
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
     * @return array{form: array{legend: array{title: mixed, icon: string}, description: mixed, input: array{type: string, col: int, label: mixed, desc: string, name: string}[]|array{type: string, desc: mixed, name: string, label: mixed, suffix: string, class: string}[], submit: array{title: mixed}}}
     */
    public function getFields(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Tools configuration', $this->className),
                    'icon' => 'icon-cog',
                ],
                'description' => $this->module->l(
                    "This page is a configuration page for some of the options on the Dashboard. If you don't want to clear recent data, you can declare how many days you want to skip.",
                    $this->className
                ),
                'input' => [
                    [
                        'type' => 'text',
                        'desc' => $this->module->l(
                            'The number of days you want to keep abandoned carts.',
                            $this->className
                        ),
                        'name' => 'PP_CART_TABLE_CLEANER',
                        'label' => $this->module->l('Abandoned carts', $this->className),
                        'suffix' => 'days',
                        'class' => 'pp-input-sm',
                    ],
                    [
                        'type' => 'text',
                        'desc' => $this->module->l(
                            'The number of days you want to keep expired/inactive cart rules.',
                            $this->className
                        ),
                        'name' => 'PP_CART_RULE_TABLE_CLEANER',
                        'label' => $this->module->l('Expired cart rules', $this->className),
                        'suffix' => 'days',
                        'class' => 'pp-input-sm',
                    ],
                    [
                        'type' => 'text',
                        'desc' => $this->module->l(
                            'The number of days you want to keep search statistics.',
                            $this->className
                        ),
                        'name' => 'PP_STATS_SEARCH_TABLE_CLEANER',
                        'label' => $this->module->l('Stats search statistics', $this->className),
                        'suffix' => 'days',
                        'class' => 'pp-input-sm',
                    ],
                    [
                        'type' => 'text',
                        'desc' => $this->module->l(
                            'The number of days you want to keep connection statistics.',
                            $this->className
                        ),
                        'name' => 'PP_CONNECTION_TABLE_CLEANER',
                        'label' => $this->module->l('Connection statistics', $this->className),
                        'suffix' => 'days',
                        'class' => 'pp-input-sm',
                    ],
                    [
                        'type' => 'text',
                        'desc' => $this->module->l(
                            'The number of days you want to keep page-not-found statistics.',
                            $this->className
                        ),
                        'name' => 'PP_PAGE_NOT_FOUND_TABLE_CLEANER',
                        'label' => $this->module->l('Page not found statistics', $this->className),
                        'suffix' => 'days',
                        'class' => 'pp-input-sm',
                    ],
                    [
                        'type' => 'text',
                        'desc' => $this->module->l(
                            'The number of days you want to keep logs from the database.',
                            $this->className
                        ),
                        'name' => 'PP_LOG_TABLE_CLEANER',
                        'label' => $this->module->l('Logs', $this->className),
                        'suffix' => 'days',
                        'class' => 'pp-input-sm',
                    ],
                    [
                        'type' => 'text',
                        'desc' => $this->module->l(
                            'The number of days you want to keep mail statistics.',
                            $this->className
                        ),
                        'name' => 'PP_MAIL_TABLE_CLEANER',
                        'label' => $this->module->l('E-mail statistics', $this->className),
                        'suffix' => 'days',
                        'class' => 'pp-input-sm',
                    ],
                    [
                        'type' => 'textarea',
                        'col' => 8,
                        'label' => $this->module->l('Sitemap links', $this->className),
                        'desc' => $this->module->l(
                            'The sitemap is used by the cache warmer. It crawls each page to trigger the generation of HTML and images.',
                            $this->className
                        ) . ' ' . sprintf(
                            $this->module->l('Separate each URL by a pipe "%s" without spaces.'),
                            $this->module->l(Config::PIPE_SEPARATOR, $this->className)
                        ),
                        'name' => 'PP_CACHE_WARMER_SITEMAPS',
                    ],
                ],
                'submit' => [
                    'title' => $this->module->l('Save', $this->className),
                ],
            ],
        ];
    }
}
