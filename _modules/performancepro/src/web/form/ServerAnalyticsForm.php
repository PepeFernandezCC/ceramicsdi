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

use PrestaShop\Module\PerformancePro\domain\service\validation\ServerSettingsValidator;
use PrestaShop\Module\PerformancePro\web\util\View;

final class ServerAnalyticsForm extends AbstractForm
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
     * @return array{form: array{legend: array{title: mixed, icon: string}, description: mixed, input: array<int, array{type: string, label: string, html_content: string, col: int, name: string}>}}
     */
    public function getFields(): array
    {
        $serverSettingsValidator = new ServerSettingsValidator();

        $opCache = \function_exists('opcache_get_status');

        $checkGrids = [
            $serverSettingsValidator->checkString('date.timezone', 'UTC'),
            $serverSettingsValidator->checkBoolean('session.auto_start', false, false),
            $serverSettingsValidator->checkBoolean('short_open_tag', false, false),
            $serverSettingsValidator->checkBoolean('display_errors', false, true),
            $serverSettingsValidator->checkBoolean('magic_quotes_gpc', false, true),
            $serverSettingsValidator->checkByte('memory_limit', '512M'),
            $serverSettingsValidator->checkInteger('max_execution_time', 300),
            $serverSettingsValidator->checkByte('upload_max_filesize', '20M'),
            $serverSettingsValidator->checkByte('post_max_size', '22M'),
            $serverSettingsValidator->checkInteger('max_input_vars', 20000),
            $serverSettingsValidator->checkBoolean('allow_url_fopen', true, true),
            $serverSettingsValidator->checkByte('realpath_cache_size', '4096K'),
            $serverSettingsValidator->checkInteger('realpath_cache_ttl', 600),
            $serverSettingsValidator->checkBoolean('opcache.enable', true, $opCache),
            $serverSettingsValidator->checkBoolean('opcache.enable_cli', false, !$opCache),
            $serverSettingsValidator->checkInteger('opcache.memory_consumption', 256),
            $serverSettingsValidator->checkInteger('opcache.interned_strings_buffer', 32),
            $serverSettingsValidator->checkInteger('opcache.max_accelerated_files', 16229),
            $serverSettingsValidator->checkInteger('opcache.max_wasted_percentage', 10),
            $serverSettingsValidator->checkInteger('opcache.revalidate_freq', 10),
            $serverSettingsValidator->checkBoolean('opcache.fast_shutdown', true, !$opCache),
            $serverSettingsValidator->checkBoolean('opcache.enable_file_override', false, !$opCache),
            $serverSettingsValidator->checkBoolean('opcache.max_file_size', false, !$opCache),
            $serverSettingsValidator->checkBoolean('zlib.output_compression', true, false),
            $serverSettingsValidator->checkBoolean('allow_url_fopen', true, true),
            $serverSettingsValidator->checkBoolean('allow_url_include', false, false),
        ];

        if (\extension_loaded('suhosin')) {
            $checkGridsSuhosin = [
                $serverSettingsValidator->checkInteger('suhosin.get.max_vars', 20000),
                $serverSettingsValidator->checkInteger('suhosin.post.max_vars', 20000),
            ];
        } else {
            $checkGridsSuhosin = [];
        }

        $checkGridTotal = array_merge($checkGrids, $checkGridsSuhosin);

        $result = [];

        foreach ($checkGridTotal as $singleCheckGridTotal) {
            $result[] = [
                $this->module->l('Current setting', $this->className) => View::displayMonospaceLink(
                    $singleCheckGridTotal[0] . ' = ' . $singleCheckGridTotal[1]
                ),
                $this->module->l('Recommended setting', $this->className) => View::displayMonospaceLink(
                    $singleCheckGridTotal[0] . ' = ' . $singleCheckGridTotal[2],
                    true
                ),
                View::displayAlign($this->module->l('Status', $this->className)) => $singleCheckGridTotal[3]
                    ? View::displayAlign(View::displayLabelInfo($this->module->l('Can be improved', $this->className)))
                    : View::displayAlign(View::displayLabelSuccess($this->module->l('Well done!', $this->className))),
            ];
        }

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Server Analytics', $this->className),
                    'icon' => 'icon-list',
                ],
                'description' => $this->module->l(
                    'Here are some advanced tips for configuring PHP for best performance. Your PHP configuration file is named php.ini. This file could be stored in different locations according to your setup. If you are not familiar with php.ini, you can ask your host for help.',
                    $this->className
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
