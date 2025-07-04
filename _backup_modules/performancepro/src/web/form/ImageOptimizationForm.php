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

final class ImageOptimizationForm extends AbstractForm
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
     * @return array{form: array{legend: array{title: mixed, icon: string}, description: mixed, warning: mixed, input: array{type: string, desc: string, name: string, label: mixed, required: true, class: string}[]|array{type: string, label: mixed, name: string, is_bool: true, desc: mixed, values: array<int, array{id: string, value: bool, label: mixed}>}[], submit: array{title: mixed}}}
     */
    public function getFields(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Image Optimization', $this->className),
                    'icon' => 'icon-picture-o',
                ],
                'description' => $this->module->l(
                    'Be sure that your images are no larger than they need to be, that they are in the next-gen image formats and that they are compressed for the web. None of your images will be overridden; the module will create an optimized version of your image in a cache folder and serve this image for your users instead of the original. Again, this is done on the fly.',
                    $this->className
                ),
                'warning' => $this->module->l(
                    "In general, all images will be converted if the module's features are enabled. However, if an image is called from outside the image tag or if the image is loaded into the DOM after the page load, the image will stay in its original form. If the browser does not support WebP, the image will be delivered in its original format.",
                    $this->className
                ),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Convert JPEG images to WebP', $this->className),
                        'name' => 'PP_CONVERT_TO_WEBP_JPEG',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'Convert JPEG to WebP. WebP is a next-gen format that supports lossless and loss compression quality for images on the Internet. The Google Company developed this format specifically to do work online as quickly and conveniently as possible. The main advantage is that its file size is small compared to other image formats but similar in quality. %s.',
                                $this->className
                            ),
                            View::displayLink(
                                'https://web.dev/serve-images-webp/',
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
                        'type' => 'text',
                        'desc' => sprintf(
                            $this->module->l(
                                'Select image quality (default is %s). Must be between 0 and 100: The more significant number, the less compression.',
                                $this->className
                            ),
                            Config::DEFAULT_JPEG_TO_WEBP_QUALITY
                        ),
                        'name' => 'PP_CONVERT_JPEG_TO_WEBP_QUALITY',
                        'label' => $this->module->l('JPEG image quality', $this->className),
                        'required' => true,
                        'class' => 'pp-input-sm',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Convert PNG images to WebP', $this->className),
                        'name' => 'PP_CONVERT_TO_WEBP_PNG',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l(
                                'Convert PNG to WebP. WebP is a next-gen format that supports lossless and loss compression quality for images on the Internet. The Google Company developed this format specifically to do work online as quickly and conveniently as possible. The main advantage is that its file size is small compared to other image formats but similar in quality. %s.',
                                $this->className
                            ),
                            View::displayLink(
                                'https://web.dev/serve-images-webp/',
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
                        'type' => 'text',
                        'desc' => sprintf(
                            $this->module->l(
                                'Select image quality (default is %s). Must be between 0 and 100: The more significant number, the less compression.',
                                $this->className
                            ),
                            Config::DEFAULT_PNG_TO_WEBP_QUALITY
                        ),
                        'name' => 'PP_CONVERT_PNG_TO_WEBP_QUALITY',
                        'label' => $this->module->l('PNG image quality', $this->className),
                        'required' => true,
                        'class' => 'pp-input-sm',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Lossless compression of SVG images', $this->className),
                        'name' => 'PP_MINIFY_SVG',
                        'is_bool' => true,
                        'desc' => $this->module->l(
                            'Compress the SVG images by removing unnecessary data, resulting in the same image but differences in file size.',
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
