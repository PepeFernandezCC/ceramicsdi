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

use PrestaShop\Module\PerformancePro\web\util\View;

final class HelpForm extends AbstractForm
{
    /**
     * @var string
     */
    private const URL = 'https://addons.prestashop.com/contact-form.php?id_product=86977';

    /**
     * @var null|mixed
     */
    public $module;

    /**
     * @var null|mixed
     */
    public $className;

    /**
     * @return array{form: array{legend: array{title: mixed, icon: string}, input: array<int, array{type: string, label: string, html_content: string, col: int, name: string}>}}
     */
    public function getFields(): array
    {
        $result = [];

        $result[] = View::displayHeader($this->module->l('Configure the module', $this->className), true);

        $result[] = View::displayParagraph(
            $this->module->l(
                'All settings are described in the module. However, if you have questions about the configuration, you can contact the module developer.',
                $this->className
            )
        );

        $result[] = View::displayHeader($this->module->l('Test your website', $this->className));

        $result[] = View::displayParagraph(
            $this->module->l(
                'Once the module is configured, you want to ensure that everything works on your website. To verify that the core features of PrestaShop are working, do the following:',
                $this->className
            )
        );

        $testWebsite = [
            $this->module->l('Register a new customer', $this->className),
            $this->module->l('Make a test order', $this->className),
            $this->module->l('Navigate to different products', $this->className),
            $this->module->l('Navigate to different categories', $this->className),
        ];

        $result[] = View::displayList($testWebsite);

        $result[] = View::displayHeader($this->module->l('Contact developer', $this->className));

        $result[] = View::displayParagraph(
            $this->module->l('Do you have questions, issues or feature requests?', $this->className)
        );

        $text = '<i class="icon icon-envelope-o"></i> ' . $this->module->l(
            'Contact module developer',
            $this->className
        );

        $result[] = View::displayBtnLink($text, self::URL);

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Help', $this->className),
                    'icon' => 'icon-question-circle',
                ],
                'input' => [
                    [
                        'type' => 'html',
                        'label' => '',
                        'html_content' => implode('', $result),
                        'col' => 12,
                        'name' => '',
                    ],
                ],
            ],
        ];
    }
}
