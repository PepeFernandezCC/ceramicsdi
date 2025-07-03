<?php
    /**
     * NOTICE OF LICENSE
     *
     * This source file is subject to the Commercial License and is not open source.
     * Each license that you purchased is only available for 1 website only.
     * You can't distribute, modify or sell this code.
     * If you want to use this file on more websites, you need to purchase additional licenses.
     *
     * DISCLAIMER
     *
     * Do not edit or add to this file.
     * If you need help please contact <attechteams@gmail.com>
     *
     * @author    Alpha Tech <attechteams@gmail.com>
     * @copyright 2022 Alpha Tech
     * @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
     */

    class CoreCookieCookie extends ObjectModel {
        private static $_module = null;
        private static $ITEM_PER_PAGE = 20;

        public $id;
        public $name;
        public $user_id;
        public $content;
        public $keywords;
        public $status;
        public $category;
        public $date_add;
        public $since;
        public $last_update;

        public static $CATEGORY_STRICT = 'strictly';
        public static $CATEGORY_REPORT_ANALYTICS = 'report_analytics';
        public static $CATEGORY_MARKETING = 'marketing';
        public static $CATEGORY_FUNCTIONAL = 'functional';
        public static $STATUS_ACTIVE = 'active';
        public static $STATUS_INACTIVE = 'inactive';

        private static $eu_countries = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'NO', 'IS', 'LI');
        public static $STATUS_ACCEPT_ALL = 'accept_all';
        public static $STATUS_ACCEPT_SELECTED = 'accept_selected';
        public static $STATUS_DENY = 'deny';

        public static $definition = array(
            'table' => 'at_cookie_cookies',
            'primary' => 'id',
            'multilang' => true,
            'multishop' => true,
            'fields' => array(
                'name' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'user_id' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
                'content' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
                'keywords' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
                'status' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'category' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateOrNull'),
                'since' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
                'last_update' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            )
        );

        public function __construct($id = null, $id_lang = null, $id_shop = null)
        {
            if(version_compare(_PS_VERSION_,'1.5.3') != -1) {
                Shop::addTableAssociation('at_cookie_cookies', array('type' => 'shop'));
            }
            
            parent::__construct($id, $id_lang, $id_shop);
        }
    
        /**
         * @desc Release object
         */
        public function release() {
            $obj = $this->getFields();
            $fields_lang = $this->getFieldsLang();
            $obj['langs'] = [];
            $name_fields_lang = [];
            foreach ($fields_lang as $field_lang) {
                if (!isset($obj['langs'][$field_lang['id_lang']])) {
                    $obj['langs'][$field_lang['id_lang']] = [];
                }
                foreach ($field_lang as $field => $value) {
                    if (in_array($field, ['id', 'id_lang'])) {
                        continue;
                    }
                    if (!in_array($field, $name_fields_lang)) {
                        $name_fields_lang[] = $field;
                    }
                    $obj['langs'][$field_lang['id_lang']][$field] = $value;
                }
            }
            foreach ($name_fields_lang as $name_field_lang) {
                $obj[$name_field_lang] = [];
                foreach ($obj['langs'] as $lang_id => $fields) {
                    $obj[$name_field_lang][$lang_id] = isset($fields[$name_field_lang]) ? $fields[$name_field_lang] : '';
                }
            }
            
            return $obj;
        }

        /**
         * @desc Get module instance
         * @return |null
         */
        private static function getModule() {
            if (self::$_module) {
                return self::$_module;
            }
            self::$_module = Module::getInstanceByName('corecookie');
            return self::$_module;
        }

        /**
         * @desc Get list cookie by paginated
         */
        public static function byPaginated() {
            $module = self::getModule();
            $count_sql = "SELECT COUNT(c.`id`) FROM ".self::getModule()::getPrefixTable()."cookies c
            JOIN `".self::getModule()::getPrefixTable()."cookies_shop` cookiess ON (cookiess.`id` = c.`id`)
            JOIN `".self::getModule()::getPrefixTable()."cookies_lang` cookiesl ON (cookiesl.`id` = c.`id`)
            WHERE cookiess.`id_shop` = {$module->_shop->id} AND cookiesl.`id_lang` = {$module->id_lang}";
            
            $sql = "SELECT * FROM ".self::getModule()::getPrefixTable()."cookies c
            JOIN `".self::getModule()::getPrefixTable()."cookies_shop` cookiess ON (cookiess.`id` = c.`id`)
            JOIN `".self::getModule()::getPrefixTable()."cookies_lang` cookiesl ON (cookiesl.`id` = c.`id`)
            WHERE cookiess.`id_shop` = {$module->_shop->id} AND cookiesl.`id_lang` = {$module->id_lang}";
            if (Tools::isSubmit('q')) {
                $sql .= " AND cookiesl.`keywords` LIKE '%".pSQL($module->purifyString(Tools::getValue('q')))."%' ";
            }
            $sql .= " ORDER BY c.`id` DESC ";

            $total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($count_sql);
            self::getModule()->name::$config['_CLIENT_CLASS_NAME']::pageData('total_count', $total);
            $sql .= self::getModule()->name::$config['_TEMPLATE_CLASS_NAME']::paginated(self::$ITEM_PER_PAGE);
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        }

        public static function getDynamicCss($context, $settings) {
            $module = self::getModule();
            $tpl = _PS_MODULE_DIR_.$module->name.'/views/templates/front/dynamic.css.cookie.tpl';
            $context->smarty->assign(array(
                'settings' => $settings
            ));
            $html = $context->smarty->fetch($tpl);
            $html = str_replace("\n<!-- begin $tpl -->\n", "", $html);
            $html = str_replace("\n<!-- end $tpl -->\n", "", $html);
            return $html;
        }

        public static function showCookieConsentCss($context) {
            $module = self::getModule();
            $tpl = _PS_MODULE_DIR_.$module->name.'/views/templates/front/dynamic.css.cookie.tpl';
            $settings = json_decode($module->getConfiguration('COOKIE_BAR', null, $module->id_shop_group, $module->id_shop), true);
            $settings['design']['reopen_btn_offset_y'] = !$settings['design']['reopen_btn_offset_y'] ? 20 : $settings['design']['reopen_btn_offset_y'];
            $settings['design']['reopen_btn_offset_x'] = !$settings['design']['reopen_btn_offset_x'] ? 20 : $settings['design']['reopen_btn_offset_x'];
            $settings['design']['reopen_btn_background'] = $settings['design']['reopen_btn_use_transparent'] ? 'transparent' : $settings['design']['reopen_btn_background'];
            
            $context->smarty->assign(array(
                'settings' => $settings
            ));
            $html = $context->smarty->fetch($tpl);
            $html = str_replace("\n<!-- begin $tpl -->\n", "", $html);
            $html = str_replace("\n<!-- end $tpl -->\n", "", $html);
            $link_css = _PS_MODULE_DIR_ . $module->name . '/views/css/front/core.cookie.css';
            @file_put_contents($link_css, $html);
            $context->controller->addCSS($link_css);
        }

        public static function all() {
            $module = self::getModule();
            $sql = "SELECT c.`id`, c.`name`, cookiesl.`content`, c.`category`, c.`status` FROM ".self::getModule()::getPrefixTable()."cookies c
            JOIN `".self::getModule()::getPrefixTable()."cookies_shop` cookiess ON (cookiess.`id` = c.`id`)
            JOIN `".self::getModule()::getPrefixTable()."cookies_lang` cookiesl ON (cookiesl.`id` = c.`id`)
            WHERE cookiess.`id_shop` = {$module->_shop->id} AND cookiesl.`id_lang` = {$module->id_lang}
            AND status = '".pSQL(self::$STATUS_ACTIVE)."'";
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
        
        public static function showCookieHeader($context) {
            $module = self::getModule();
            $languages = $module->getLanguages();
            $cookie_consent_text = [];
            $privacy_policy_text = [];
            $preferences_button_text = [];
            $reject_button_text = [];
            $accept_button_text = [];
            $privacy_policy_link = [];
    
            $preferences_popup_header_title = [];
            $preferences_popup_header_desc = [];
            $strict_cookie_title = [];
            $strict_cookie_desc = [];
            $analytics_cookie_title = [];
            $analytics_cookie_desc = [];
            $marketing_cookie_title = [];
            $marketing_cookie_desc = [];
            $functional_cookie_title = [];
            $functional_cookie_desc = [];
            $accept_selected_button = [];
            $accept_all_selected_button = [];
    
            $label_reopen_btn = [];
            foreach ($languages as $lang){
                $cookie_consent_text[(int)$lang['id_lang']] = $module->getConfiguration("COOKIE_CONSENT_TEXT", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $privacy_policy_text[(int)$lang['id_lang']] = $module->getConfiguration("PRIVACY_POLICY_TEXT", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $preferences_button_text[(int)$lang['id_lang']] = $module->getConfiguration("PREFERENCES_BUTTON_TEXT", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $reject_button_text[(int)$lang['id_lang']] = $module->getConfiguration("REJECT_BUTTON_TEXT", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $accept_button_text[(int)$lang['id_lang']] = $module->getConfiguration("ACCEPT_BUTTON_TEXT", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $privacy_policy_link[(int)$lang['id_lang']] = $module->getConfiguration("PRIVACY_POLICY_LINK", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
        
                $preferences_popup_header_title[(int)$lang['id_lang']] = $module->getConfiguration("PREFERENCES_POPUP_HEADER_TITLE", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $preferences_popup_header_desc[(int)$lang['id_lang']] = $module->getConfiguration("PREFERENCES_POPUP_HEADER_DESC", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $strict_cookie_title[(int)$lang['id_lang']] = $module->getConfiguration("STRICT_COOKIE_TITLE", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $strict_cookie_desc[(int)$lang['id_lang']] = $module->getConfiguration("STRICT_COOKIE_DESC", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $analytics_cookie_title[(int)$lang['id_lang']] = $module->getConfiguration("ANALYTICS_COOKIE_TITLE", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $analytics_cookie_desc[(int)$lang['id_lang']] = $module->getConfiguration("ANALYTICS_COOKIE_DESC", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $marketing_cookie_title[(int)$lang['id_lang']] = $module->getConfiguration("MARKETING_COOKIE_TITLE", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $marketing_cookie_desc[(int)$lang['id_lang']] = $module->getConfiguration("MARKETING_COOKIE_DESC", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $functional_cookie_title[(int)$lang['id_lang']] = $module->getConfiguration("FUNCTIONAL_COOKIE_TITLE", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $functional_cookie_desc[(int)$lang['id_lang']] = $module->getConfiguration("FUNCTIONAL_COOKIE_DESC", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $accept_selected_button[(int)$lang['id_lang']] = $module->getConfiguration("ACCEPT_SELECTED_BUTTON", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
                $accept_all_selected_button[(int)$lang['id_lang']] = $module->getConfiguration("ACCEPT_ALL_SELECTED_BUTTON", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
    
                $label_reopen_btn[(int)$lang['id_lang']] = $module->getConfiguration("LABEL_REOPEN_BTN", (int)$lang['id_lang'], $module->id_shop_group, $module->id_shop);
            }
    
            $settings = json_decode($module->getConfiguration('COOKIE_BAR', null, $module->id_shop_group, $module->id_shop), true);
            $global_settings = json_decode($module->getConfiguration('GLOBAL_SETTINGS', null, $module->id_shop_group, $module->id_shop), true);
            $cache_cookies_key = 'core_cache_cookies_data';
            if (!Cache::isStored($cache_cookies_key)) {
                $cookies = self::all();
                Cache::store($cache_cookies_key, $cookies);
            } else {
                $cookies = Cache::retrieve($cache_cookies_key);
            }
    
            $settings['design']['reopen_btn_offset_y'] = !$settings['design']['reopen_btn_offset_y'] ? 20 : $settings['design']['reopen_btn_offset_y'];
            $settings['design']['reopen_btn_offset_x'] = !$settings['design']['reopen_btn_offset_x'] ? 20 : $settings['design']['reopen_btn_offset_x'];
            $settings['design']['reopen_btn_background'] = $settings['design']['reopen_btn_use_transparent'] ? 'transparent' : $settings['design']['reopen_btn_background'];
            $settings = array_merge($settings, [
                'cookie_consent_text' => $cookie_consent_text,
                'privacy_policy_text' => $privacy_policy_text,
                'privacy_policy_link' => $privacy_policy_link,
                'preferences_button_text' => $preferences_button_text,
                'reject_button_text' => $reject_button_text,
                'accept_button_text' => $accept_button_text,
                'preferences_popup_header_title' => $preferences_popup_header_title,
                'preferences_popup_header_desc' => $preferences_popup_header_desc,
                'strict_cookie_title' => $strict_cookie_title,
                'strict_cookie_desc' => $strict_cookie_desc,
                'analytics_cookie_title' => $analytics_cookie_title,
                'analytics_cookie_desc' => $analytics_cookie_desc,
                'marketing_cookie_title' => $marketing_cookie_title,
                'marketing_cookie_desc' => $marketing_cookie_desc,
                'functional_cookie_title' => $functional_cookie_title,
                'functional_cookie_desc' => $functional_cookie_desc,
                'accept_selected_button' => $accept_selected_button,
                'accept_all_selected_button' => $accept_all_selected_button,
                'label_reopen_btn' => $label_reopen_btn,
                'lang_id' => $context->language->id,
                'global_settings' => $global_settings,
                'visitor_ip' => Tools::getRemoteAddr(),
                'tab_setting_name' => $module->l('Categories', 'cookie'),
                'tab_declaration_name' => $module->l('Cookie declaration', 'cookie'),
                'cookies' => $cookies,
                'link_process' => $context->link->getModuleLink($module->name, 'cookie'),
                'id_customer' =>  $context->customer->id,
                'global_settings' => json_decode($module->getConfiguration('GLOBAL_SETTINGS', null, $module->id_shop_group, $module->id_shop), true)
            ]);
    
            $context->smarty->assign([
                'settings' => json_encode($settings),
                'client' => json_encode([
                    'base_link' => $module->getBaseLink(),
                    'name_module' => $module->name
                ]),
            ]);
            return $context->smarty->fetch(_PS_MODULE_DIR_.$module->name.'/views/templates/hook/core.cookie.header.tpl');
        }

        /**
         * @desc Should show cookie consent
         */
        public static function shouldShow() {
            $module = self::getModule();
            $settings = json_decode($module->getConfiguration('GLOBAL_SETTINGS', null, $module->id_shop_group, $module->id_shop), true);
            if (!$settings['show_cookie_bar']) {
                return false;
            }

            $ip_address = Tools::getRemoteAddr();
            if ($settings['sandbox_mode'] && !(is_array($settings['whitelist_ips'])
                && in_array($ip_address, $settings['whitelist_ips']))) {
                return false;
            }

            if ($settings['enable_specific_regions'] == 'all_countries') {
                return true;
            }
            $visitor = self::detectCountryVisitor($ip_address);
            if (!$visitor) {
                return false;
            }
            $enable_specific_regions = explode(",", $settings['enable_specific_regions']);
            if (in_array('GDPR', $enable_specific_regions) && in_array($visitor['countryCode'], self::$eu_countries)) {
                return true;
            }
            if (in_array('ccpa', $enable_specific_regions) && $visitor['regionName'] == 'California') {
                return true;
            }
            if (in_array('pipeda', $enable_specific_regions) && $visitor['countryCode'] == 'CA') {
                return true;
            }
            if (in_array('appi', $enable_specific_regions) && $visitor['countryCode'] == 'JP') {
                return true;
            }
            return false;
        }

        private function detectCountryVisitor($ip) {
            $key = 'core_cookie_consent_'.$ip;
            if (Cache::isStored($key)) {
                return Cache::retrieve($key);
            }

            try {
                $visitor = json_decode(Tools::file_get_contents("http://ip-api.com/json/".$ip), true);
            } catch (Exception $e) {
                return false;
            }

            Cache::store($key, $visitor);
            return $visitor;
        }

        public static function initDataCookies($context) {
            $module = self::getModule();
            $employee_id = $context->employee->id;
            $data = [
                [
                    'name' => 'PHPSESSID',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_STRICT,
                    'content' => 'Cookie generated by applications based on the PHP language. This is a general purpose identifier used to maintain user session variables. It is normally a random generated number, how it is used can be specific to the site, but a good example is maintaining a logged-in status for a user between pages.'
                ],
                [
                    'name' => 'cf_use_ob',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_STRICT,
                    'content' => 'Cloudflare sets this cookie to improve page load times and to disallow any security restrictions based on the visitor IP address.'
                ],
                [
                    'name' => 'core_cookieconsent_status',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_STRICT,
                    'content' => "This cookie is associated with the app GDPR/CCPA + Cookie Management and is used for storing the customer consent."
                ],
                [
                    'name' => 'core_cookieconsent_preferences_disabled',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_STRICT,
                    'content' => "This cookie is associated with the app GDPR/CCPA + Cookie Management and is used for storing the customer consent."
                ],
                [
                    'name' => '__cf_bm',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_FUNCTIONAL,
                    'content' => "This cookie is used to distinguish between humans and bots. This is beneficial for the website, in order to make valid reports on the use of their website."
                ],
                [
                    'name' => '_cs_c',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_FUNCTIONAL,
                    'content' => "The cookie is used by Content Square to save the user consent to be tracked."
                ],
                [
                    'name' => '_gcl_au',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_REPORT_ANALYTICS,
                    'content' => "Provided by Google Tag Manager to experiment advertisement efficiency of websites using their services."
                ],
                [
                    'name' => 'test_cookie',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_REPORT_ANALYTICS,
                    'content' => "The test_cookie is set by doubleclick.net and is used to determine if the user browser supports cookies."
                ],
                [
                    'name' => '_ga',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_REPORT_ANALYTICS,
                    'content' => "The _ga cookie, installed by Google Analytics, calculates visitor, session and campaign data and also keeps track of site usage for the site analytics report. The cookie stores information anonymously and assigns a randomly generated number to recognize unique visitors."
                ],
                [
                    'name' => '_gid',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_REPORT_ANALYTICS,
                    'content' => "Installed by Google Analytics, _gid cookie stores information on how visitors use a website, while also creating an analytics report of the website performance. Some of the data that are collected include the number of visitors, their source, and the pages they visit anonymously."
                ],
                [
                    'name' => '_gat_UA-*',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_REPORT_ANALYTICS,
                    'content' => "Google Analytics sets this cookie for user behaviour tracking."
                ],
                [
                    'name' => '_cs_id',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_REPORT_ANALYTICS,
                    'content' => "This cookie is used to store the ContentSquare user identifier ID. This is a persistent cookie and expires after 13 months."
                ],
                [
                    'name' => '_cs_s',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_REPORT_ANALYTICS,
                    'content' => "This cookie is used to store the number of page viewed by a visitor within the session for ContentSquare solution."
                ],
                [
                    'name' => '_cs_s',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_REPORT_ANALYTICS,
                    'content' => "This cookie is used to store the number of page viewed by a visitor within the session for ContentSquare solution."
                ],
                [
                    'name' => '_gads',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_MARKETING,
                    'content' => ""
                ],
                [
                    'name' => 'IDE',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_MARKETING,
                    'content' => "This domain is owned by Doubleclick (Google). The main business activity is: Doubleclick is Googles real time bidding advertising exchange"
                ],
                [
                    'name' => 'PREF',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_MARKETING,
                    'content' => "This cookie, which may be set by Google or Doubleclick, may be used by advertising partners to build a profile of interests to show relevant ads on other sites."
                ],
                [
                    'name' => 'BizoID',
                    'user_id' => $employee_id,
                    'status' =>  self::$STATUS_ACTIVE,
                    'category' => self::$CATEGORY_MARKETING,
                    'content' => "This is a Microsoft MSN 1st party cookie to enable user-based content."
                ]
            ];
            $ts = time();
            $languages = Language::getLanguages(false);
    
            $res = true;
            foreach ($data as $cookie) {
                $cookie_obj = new self;
                $cookie_obj->name = $cookie['name'];
                $cookie_obj->user_id = $cookie['user_id'];
                $cookie_obj->status = $cookie['status'];
                $cookie_obj->category = $cookie['category'];
                $cookie_obj->date_add = date("Y-m-d");
                $cookie_obj->since = $ts;
                $cookie_obj->last_update = $ts;
    
                foreach($languages as $lang) {
                    $cookie_obj->content[$lang['id_lang']] = $cookie['content'];
                    $keywords = str_replace(' ', '', $module->purifyString($cookie['name'].$cookie['category'].$cookie['status'].$cookie['content']));
                    $cookie_obj->keywords[$lang['id_lang']] = $keywords;
                }
                $res &= $cookie_obj->add();
            }
            
            return $res;
        }

        public static function getPrivacyCmsLink() {
            $privacy_link = '';
            $lang_id = Context::getContext()->language->id;
            $shop_id = Context::getContext()->shop->id;
            $sql = 'SELECT `id_cms` FROM `' . _DB_PREFIX_ . 'cms_lang`
			WHERE `link_rewrite` = "' . pSQL("terms-and-conditions-of-use") . '" AND `id_lang` = ' . (int) $lang_id . '
			AND `id_shop` = ' . (int) $shop_id;
            $cmc_privacy = Db::getInstance()->getRow($sql);
            if (is_array($cmc_privacy) && count($cmc_privacy) > 0) {
                $privacy_link = Context::getContext()->link->getCMSLink(new CMS((int) $cmc_privacy['id_cms']), null, null, $lang_id, $shop_id);
            }
            return $privacy_link;
        }
    }
?>
