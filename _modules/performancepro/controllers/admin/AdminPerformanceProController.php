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

use PrestaShop\Module\PerformancePro\domain\service\file\PublicHtaccessFactory;
use PrestaShop\Module\PerformancePro\domain\service\file\PublicHtaccessFile;
use PrestaShop\Module\PerformancePro\domain\service\form\Form;
use PrestaShop\Module\PerformancePro\domain\service\form\FormValidate;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\util\DefineValueService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDefineValueException;
use PrestaShop\Module\PerformancePro\resources\config\Config;
use PrestaShop\Module\PerformancePro\resources\config\Field;
use PrestaShop\Module\PerformancePro\web\form\CacheForm;
use PrestaShop\Module\PerformancePro\web\form\DashboardForm;
use PrestaShop\Module\PerformancePro\web\form\DatabaseAnalyticsForm;
use PrestaShop\Module\PerformancePro\web\form\DebugForm;
use PrestaShop\Module\PerformancePro\web\form\HelpForm;
use PrestaShop\Module\PerformancePro\web\form\HtmlOptimizationForm;
use PrestaShop\Module\PerformancePro\web\form\ImageOptimizationForm;
use PrestaShop\Module\PerformancePro\web\form\LazyLoadingForm;
use PrestaShop\Module\PerformancePro\web\form\ModuleAnalyticsForm;
use PrestaShop\Module\PerformancePro\web\form\PageOptimizationForm;
use PrestaShop\Module\PerformancePro\web\form\ProfilerForm;
use PrestaShop\Module\PerformancePro\web\form\ResourceLoadingForm;
use PrestaShop\Module\PerformancePro\web\form\ServerAnalyticsForm;
use PrestaShop\Module\PerformancePro\web\form\SystemAnalyticsForm;
use PrestaShop\Module\PerformancePro\web\form\ToolsConfigurationForm;

final class AdminPerformanceProController extends ModuleAdminController
{
    /**
     * @var string
     */
    private const SUBMIT_NAME = 'submitConfig';

    /**
     * @var array
     */
    private $customErrors = [];

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();
    }

    public function renderList(): string
    {
        if (!$this->module->active || Config::DEMO_MODE) {
            $this->module->hookActionAdminControllerSetMedia();
        }

        $result = '';

        if (Tools::isSubmit(self::SUBMIT_NAME)) {
            if ($this->submitInputData()) {
                (new PublicHtaccessFactory(new PublicHtaccessFile()))->create();

                $result .= $this->displaySaveNotification();

                $result .= $this->displayErrorNotification();
            } else {
                $result .= $this->displayDemoNotification();
            }
        }

        return $result . $this->renderAdminForm();
    }

    public function displayDemoNotification(): string
    {
        $error = $this->module->l('The configuration has been disabled in the demo mode.');

        return $this->module->getSuccessTemplate($error);
    }

    public function displaySaveNotification(): string
    {
        $error = $this->module->l('Settings saved.');

        return $this->module->getSuccessTemplate($error);
    }

    private function submitInputData(): bool
    {
        if (Config::DEMO_MODE) {
            return false;
        }

        $languages = Language::getLanguages(false);

        foreach (Field::getFieldValues() as $key => $multiLang) {
            if ($multiLang) {
                $fields = [];

                foreach ($languages as $language) {
                    $idLang = $language['id_lang'];

                    $field = (new FormValidate($key, (string) Tools::getValue($key . '_' . $idLang)))
                        ->validate()
                        ->getResponse();

                    $this->setError($field['error'] ?: '');

                    $fields[$idLang] = $field['result'];
                }

                Configuration::updateValue($key, json_encode($fields));
            } else {
                $field = (new FormValidate($key, (string) Tools::getValue($key)))
                    ->validate()
                    ->getResponse();

                $this->setError($field['error'] ?: '');

                Configuration::updateValue($key, $field['result']);
            }
        }

        $this->defineGlobalValues();

        return true;
    }

    private function setError(string $text): void
    {
        if (!empty($text)) {
            $this->customErrors[] = $text;
        }
    }

    private function defineGlobalValues(): void
    {
        $value = Configuration::get('PP_DEBUG_PROFILING') ? 'true' : 'false';

        try {
            (new DefineValueService())->updateValue('_PS_DEBUG_PROFILING_', $value);
        } catch (PerformanceProDefineValueException $performanceProDefineValueException) {
            LogService::error(
                $performanceProDefineValueException->getMessage(),
                $performanceProDefineValueException->getTrace()
            );
        }
    }

    private function displayErrorNotification(): string
    {
        $result = '';

        foreach ($this->customErrors as $customError) {
            $result .= $this->module->getWarningTemplate($customError);
        }

        return $result;
    }

    private function renderAdminForm(): string
    {
        $forms = [
            (new DashboardForm($this->module))->getFields(),
            (new ToolsConfigurationForm($this->module))->getFields(),
            (new ResourceLoadingForm($this->module))->getFields(),
            (new CacheForm($this->module))->getFields(),
            (new LazyLoadingForm($this->module))->getFields(),
            (new PageOptimizationForm($this->module))->getFields(),
            (new HtmlOptimizationForm($this->module))->getFields(),
            (new ImageOptimizationForm($this->module))->getFields(),
            (new SystemAnalyticsForm($this->module))->getFields(),
            (new ModuleAnalyticsForm($this->module))->getFields(),
            (new ServerAnalyticsForm($this->module))->getFields(),
            (new DatabaseAnalyticsForm($this->module))->getFields(),
            (new ProfilerForm($this->module))->getFields(),
            (new HelpForm($this->module))->getFields(),
            (new DebugForm($this->module))->getFields(),
        ];

        return (new Form($this->module))->render($forms, self::SUBMIT_NAME);
    }
}
