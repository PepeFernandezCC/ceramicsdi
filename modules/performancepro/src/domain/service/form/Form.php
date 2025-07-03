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

namespace PrestaShop\Module\PerformancePro\domain\service\form;

use Configuration;
use HelperForm;
use Language;
use Module;
use PrestaShop\Module\PerformancePro\resources\config\Config;
use PrestaShop\Module\PerformancePro\resources\config\Field;
use Tools;

final class Form
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @var HelperForm
     */
    private $helperForm;

    public function __construct(Module $module)
    {
        $this->module = $module;

        $this->helperForm = new HelperForm();
    }

    public function render(array $forms, string $submitName): string
    {
        $this->helperForm->show_toolbar = false;

        $this->helperForm->default_form_language = $this->module->getContext()->language->id;

        $this->helperForm->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $this->helperForm->name_controller = Config::CONTROLLER_NAME;

        $this->helperForm->submit_action = $submitName;

        $this->helperForm->currentIndex = $this->module->getContext()->link->getAdminLink(Config::CONTROLLER_NAME, false, false);

        $this->helperForm->token = Tools::getAdminTokenLite(Config::CONTROLLER_NAME);

        $this->helperForm->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->module->getContext()->controller->getLanguages(),
            'id_language' => $this->module->getContext()->language->id,
        ];

        return $this->helperForm->generateForm($forms);
    }

    /**
     * @return array<string,mixed>
     */
    private function getConfigFormValues(): array
    {
        $languages = Language::getLanguages(false);

        $result = [];

        foreach (Field::getFieldValues() as $key => $multiLang) {
            if ($multiLang) {
                $confKey = Configuration::get($key);

                if ($confKey) {
                    $fields = (array) json_decode($confKey, true);

                    foreach ($languages as $language) {
                        $idLang = $language['id_lang'];

                        $result[$key][$idLang] = $fields[$idLang];
                    }
                }
            } else {
                $result[$key] = Configuration::get($key);
            }
        }

        return $result;
    }
}
