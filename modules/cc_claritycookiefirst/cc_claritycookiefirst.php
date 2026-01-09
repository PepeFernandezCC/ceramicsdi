<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Cc_ClarityCookieFirst extends Module
{
    const CFG_CLARITY_ID = 'CCCLARITY_CF_ID';
    const CFG_CATEGORY  = 'CCCLARITY_CF_CATEGORY';
    const CFG_DELAY_MS  = 'CCCLARITY_CF_DELAY_MS';
    const CFG_BOT_REGEX = 'CCCLARITY_CF_BOT_REGEX';

    public function __construct()
    {
        $this->name = 'cc_claritycookiefirst';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.0';
        $this->author = 'CERAMIC CONNECTION';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Microsoft Clarity (deferred) + CookieFirst');
        $this->description = $this->l('Loads Microsoft Clarity only after CookieFirst consent (category-based), with first-visit delay, excludes employees and common bots.');
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && Configuration::updateValue(self::CFG_CLARITY_ID, '')
            && Configuration::updateValue(self::CFG_CATEGORY, 'performance')
            && Configuration::updateValue(self::CFG_DELAY_MS, 5000)
            && Configuration::updateValue(self::CFG_BOT_REGEX, 'Google|Bingbot|Yandex')
            && $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName(self::CFG_CLARITY_ID)
            && Configuration::deleteByName(self::CFG_CATEGORY)
            && Configuration::deleteByName(self::CFG_DELAY_MS)
            && Configuration::deleteByName(self::CFG_BOT_REGEX);
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitCcClarityCookieFirst')) {
            $clarityId = trim((string)Tools::getValue(self::CFG_CLARITY_ID));
            $category  = trim((string)Tools::getValue(self::CFG_CATEGORY));
            $delayMs   = (int)Tools::getValue(self::CFG_DELAY_MS);
            $botRegex  = trim((string)Tools::getValue(self::CFG_BOT_REGEX));

            // Basic validation
            if ($delayMs < 0) { $delayMs = 0; }
            if ($delayMs > 60000) { $delayMs = 60000; } // cap to 60s for safety

            // CookieFirst default categories: necessary, performance, functional, advertising
            $allowedCategories = ['performance','functional','advertising'];
            if (!in_array($category, $allowedCategories, true)) {
                $category = 'performance';
            }

            Configuration::updateValue(self::CFG_CLARITY_ID, $clarityId);
            Configuration::updateValue(self::CFG_CATEGORY, $category);
            Configuration::updateValue(self::CFG_DELAY_MS, $delayMs);
            Configuration::updateValue(self::CFG_BOT_REGEX, $botRegex);

            $output .= $this->displayConfirmation($this->l('Settings updated.'));
        }

        return $output . $this->renderForm();
    }

    private function renderForm()
    {
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Microsoft Clarity Project ID'),
                        'name' => self::CFG_CLARITY_ID,
                        'desc' => $this->l('Example: ujxnfdqv8e (from https://www.clarity.ms/tag/XXXX)'),
                        'required' => true,
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('CookieFirst consent category to require'),
                        'name' => self::CFG_CATEGORY,
                        'desc' => $this->l('Clarity is typically "performance" (analytics/statistics).'),
                        'options' => [
                            'query' => [
                                ['id' => 'performance', 'name' => $this->l('performance (analytics/statistics)')],
                                ['id' => 'functional',  'name' => $this->l('functional')],
                                ['id' => 'advertising', 'name' => $this->l('advertising (marketing)')],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('First-visit delay (ms)'),
                        'name' => self::CFG_DELAY_MS,
                        'desc' => $this->l('Delay only the first time Clarity loads (stored in localStorage). Default 5000 = 5 seconds.'),
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Bot User-Agent regex (optional)'),
                        'name' => self::CFG_BOT_REGEX,
                        'desc' => $this->l('If matches navigator.userAgent, Clarity will not load. Default: Google|Bingbot|Yandex'),
                        'required' => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCcClarityCookieFirst';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value[self::CFG_CLARITY_ID] = Configuration::get(self::CFG_CLARITY_ID);
        $helper->fields_value[self::CFG_CATEGORY]  = Configuration::get(self::CFG_CATEGORY);
        $helper->fields_value[self::CFG_DELAY_MS]  = (int)Configuration::get(self::CFG_DELAY_MS);
        $helper->fields_value[self::CFG_BOT_REGEX] = Configuration::get(self::CFG_BOT_REGEX);

        return $helper->generateForm([$fieldsForm]);
    }

    private function isFrontOffice()
    {
        if (!isset($this->context->controller) || !method_exists($this->context->controller, 'getControllerType')) {
            return true;
        }
        return $this->context->controller->getControllerType() === 'front';
    }

    private function isEmployee()
    {
        return isset($this->context->employee) && (int)$this->context->employee->id > 0;
    }

    public function hookDisplayFooter($params)
    {
        if (!$this->isFrontOffice() || $this->isEmployee()) {
            return '';
        }

        $clarityId = trim((string)Configuration::get(self::CFG_CLARITY_ID));
        if ($clarityId === '') {
            return '';
        }

        $this->context->smarty->assign([
            'cc_clarity_id' => $clarityId,
            'cc_cf_category' => (string)Configuration::get(self::CFG_CATEGORY),
            'cc_first_delay_ms' => (int)Configuration::get(self::CFG_DELAY_MS),
            'cc_bot_regex' => (string)Configuration::get(self::CFG_BOT_REGEX),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/clarity.tpl');
    }
}
