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

    class AdminCookiesettingController extends ModuleAdminController
    {
        public function __construct()
        {
            parent::__construct();
            $this->meta_title = $this->l('Global Settings');
            $this->display = 'view';
            $this->context = Context::getContext();
            $this->link_controller = $this->context->link->getAdminLink('AdminCookiesetting', true);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::$data['current_path'] = $this->link_controller;

            if (!$this->module->active){
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminDashboard'));
            }
            $this->module->middlewareDemo();
            if (Tools::isSubmit('_r')) {
                die($this->loadAction());
            }
        }

        public function initToolBarTitle()
        {
            $this->toolbar_title[] = $this->l('Global Settings');
        }

        public function renderView()
        {
            $content = $this->navigation();
            return $this->module->layoutAdmin($content);
        }

        public function pageDefault()
        {
            $languages = $this->module->getLanguages();
            $content_gdpr_page = [];
            $content_ccpa_page = [];
            $content_appi_page = [];
            $content_pipeda_page = [];

            $email_title_admin = [];
            $email_title_customer_confirm = [];
            $email_title_customer_notify = [];
            $email_content_admin = [];
            $email_content_customer_confirm = [];
            $email_content_customer_notify = [];

            $email_variable_gdpr_request = [];
            $email_variable_personal_information = [];
            $email_variable_report_request = [];
            $email_variable_deletion_request = [];
            $email_variable_ccpa_request = [];
            $email_variable_do_not_sell_request = [];
            $email_variable_appi_request = [];
            $email_variable_pipeda_request = [];

            foreach ($languages as $lang){
                $content_gdpr_page[(int)$lang['id_lang']] = $this->module->getConfiguration("CONTENT_GDPR_PAGE", (int)$lang['id_lang']);
                $content_ccpa_page[(int)$lang['id_lang']] = $this->module->getConfiguration("CONTENT_CCPA_PAGE", (int)$lang['id_lang']);
                $content_appi_page[(int)$lang['id_lang']] = $this->module->getConfiguration("CONTENT_APPI_PAGE", (int)$lang['id_lang']);
                $content_pipeda_page[(int)$lang['id_lang']] = $this->module->getConfiguration("CONTENT_PIPEDA_PAGE", (int)$lang['id_lang']);

                $email_title_admin[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_TITLE_ADMIN', (int)$lang['id_lang']);
                $email_title_customer_confirm[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_TITLE_CUSTOMER_CONFIRM', (int)$lang['id_lang']);
                $email_title_customer_notify[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_TITLE_CUSTOMER_NOTIFY', (int)$lang['id_lang']);
                $email_content_admin[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_CONTENT_ADMIN', (int)$lang['id_lang']);
                $email_content_customer_confirm[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_CONTENT_CUSTOMER_CONFIRM', (int)$lang['id_lang']);
                $email_content_customer_notify[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_CONTENT_CUSTOMER_NOTIFY', (int)$lang['id_lang']);

                $email_variable_gdpr_request[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_VARIABLE_GDPR_REQUEST', (int)$lang['id_lang']);
                $email_variable_personal_information[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_VARIABLE_PERSONAL_INFORMATION', (int)$lang['id_lang']);
                $email_variable_report_request[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_VARIABLE_REPORT_REQUEST', (int)$lang['id_lang']);
                $email_variable_deletion_request[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_VARIABLE_DELETION_REQUEST', (int)$lang['id_lang']);
                $email_variable_ccpa_request[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_VARIABLE_CCPA_REQUEST', (int)$lang['id_lang']);
                $email_variable_do_not_sell_request[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_VARIABLE_DO_NOT_SELL_REQUEST', (int)$lang['id_lang']);
                $email_variable_appi_request[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_VARIABLE_APPI_REQUEST', (int)$lang['id_lang']);
                $email_variable_pipeda_request[(int)$lang['id_lang']] = $this->module->getConfiguration('EMAIL_VARIABLE_PIPEDA_REQUEST', (int)$lang['id_lang']);
            }

            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('settings', json_decode($this->module->getConfiguration('GLOBAL_SETTINGS'), true));
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('content_gdpr_page', $content_gdpr_page);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('content_ccpa_page', $content_ccpa_page);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('content_appi_page', $content_appi_page);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('content_pipeda_page', $content_pipeda_page);

            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_title_admin', $email_title_admin);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_title_customer_confirm', $email_title_customer_confirm);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_title_customer_notify', $email_title_customer_notify);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_content_admin', $email_content_admin);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_content_customer_confirm', $email_content_customer_confirm);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_content_customer_notify', $email_content_customer_notify);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('ip_address', Tools::getRemoteAddr());

            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('link_gdpr_page', $this->context->link->getModuleLink($this->module->name, 'gdpr', []));
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('link_ccpa_page', $this->context->link->getModuleLink($this->module->name, 'ccpa', []));
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('link_appi_page', $this->context->link->getModuleLink($this->module->name, 'appi', []));
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('link_pipeda_page', $this->context->link->getModuleLink($this->module->name, 'pipeda', []));

            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_variable_gdpr_request', $email_variable_gdpr_request);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_variable_personal_information', $email_variable_personal_information);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_variable_report_request', $email_variable_report_request);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_variable_deletion_request', $email_variable_deletion_request);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_variable_ccpa_request', $email_variable_ccpa_request);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_variable_do_not_sell_request', $email_variable_do_not_sell_request);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_variable_appi_request', $email_variable_appi_request);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('email_variable_pipeda_request', $email_variable_pipeda_request);

            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/setting/index.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/setting/index.core", "admin", $tpl);
        }

        /**
         * @desc Update form
         */
        private function updateForm() {
            $languages = $this->module->getLanguages();
            $content_gdpr_page = [];
            $content_ccpa_page = [];
            $content_appi_page = [];
            $content_pipeda_page = [];
            foreach ($languages as $lang){
                $content_gdpr_page[(int)$lang['id_lang']] = Tools::getValue('gdpr_page')[$lang['id_lang']];
                $content_ccpa_page[(int)$lang['id_lang']] = Tools::getValue('ccpa_page')[$lang['id_lang']];
                $content_appi_page[(int)$lang['id_lang']] = Tools::getValue('appi_page')[$lang['id_lang']];
                $content_pipeda_page[(int)$lang['id_lang']] = Tools::getValue('pipeda_page')[$lang['id_lang']];
            }

            $settings = json_decode($this->module->getConfiguration('GLOBAL_SETTINGS'), true);
            $settings['sandbox_mode'] = Tools::getValue('sandbox_mode');
            $whitelist_ips = Tools::getValue('whitelist_ips');
            if ($whitelist_ips != '') {
                $whitelist_ips = explode(",", $whitelist_ips);
            }
            $settings['whitelist_ips'] = $whitelist_ips;
            $settings['show_cookie_bar'] = Tools::getValue('show_cookie_bar');
            $enable_specific_regions_array = [];
            if (Tools::isSubmit('enable_specific_regions_gdpr')) {
                $enable_specific_regions_array[] = Tools::getValue('enable_specific_regions_gdpr');
            }
            if (Tools::isSubmit('enable_specific_regions_ccpa')) {
                $enable_specific_regions_array[] = Tools::getValue('enable_specific_regions_ccpa');
            }
            if (Tools::isSubmit('enable_specific_regions_pipeda')) {
                $enable_specific_regions_array[] = Tools::getValue('enable_specific_regions_pipeda');
            }
            if (Tools::isSubmit('enable_specific_regions_appi')) {
                $enable_specific_regions_array[] = Tools::getValue('enable_specific_regions_appi');
            }
            if (Tools::isSubmit('enable_specific_regions_ac')) {
                $enable_specific_regions_array = [];
                $enable_specific_regions_array[] = Tools::getValue('enable_specific_regions_ac');
            }

            $settings['reload_page_after_accept_cookies'] = Tools::getValue('reload_page_after_accept_cookies');
            $settings['force_choice_accepting_cookies'] = Tools::getValue('force_choice_accepting_cookies');
            $settings['redirect_if_refuse_cookie'] = Tools::getValue('redirect_if_refuse_cookie');
            $settings['reject_redirect_url'] = Tools::getValue('reject_redirect_url');

            if (!filter_var($settings['reject_redirect_url'], FILTER_VALIDATE_URL)) {
                $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Redirect url is invalid'), true);
            }
            
            $settings['use_google_captcha'] = Tools::getValue('use_google_captcha');
            $settings['captcha_site_key'] = Tools::getValue('captcha_site_key');
            $settings['captcha_secret_key'] = Tools::getValue('captcha_secret_key');
            $settings['receive_email_when_customer_requested'] = Tools::getValue('receive_email_when_customer_requested');
            $settings['add_custom_email_to_receive_notifications'] = Tools::getValue('add_custom_email_to_receive_notifications');
            $settings['access_to_personal_data_expired'] = Tools::getValue('access_to_personal_data_expired');
            $settings['removal_of_personal_data_expired'] = Tools::getValue('removal_of_personal_data_expired');
            $settings['enable_specific_regions'] = implode(",", $enable_specific_regions_array);
            if (!$this->module->setConfiguration("GLOBAL_SETTINGS", json_encode($settings))) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update settings'));
            }
            if (!$this->module->setConfiguration("CONTENT_GDPR_PAGE", $content_gdpr_page, true)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update content of the GDPR Page'));
            }
            if (!$this->module->setConfiguration("CONTENT_CCPA_PAGE", $content_ccpa_page, true)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update content of the CCPA Page'));
            }
            if (!$this->module->setConfiguration("CONTENT_APPI_PAGE", $content_appi_page, true)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update content of the APPI Page'));
            }
            if (!$this->module->setConfiguration("CONTENT_PIPEDA_PAGE", $content_pipeda_page, true)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update content of the PIPEDA Page'));
            }
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("settings", $settings);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra('content_gdpr_page', $content_gdpr_page);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra('content_ccpa_page', $content_ccpa_page);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra('content_appi_page', $content_appi_page);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra('content_pipeda_page', $content_pipeda_page);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        private function resetCustomerConsent() {
            $settings = json_decode($this->module->getConfiguration('GLOBAL_SETTINGS'), true);
            $settings['reset_customer_consent'] = time();
            if (!$this->module->setConfiguration("GLOBAL_SETTINGS", json_encode($settings))) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update settings'));
            }
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("settings", $settings);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        private function genGdprContent() {
            $languages = Language::getLanguages(false);
            $content_GDPR_page = [];
            $content_GDPR_page_template = Tools::file_get_contents(_PS_MODULE_DIR_ . $this->module->name . '/content.gdpr.page.txt');
            $identity_link = Context::getContext()->link->getPageLink('identity');
            $order_history_link = Context::getContext()->link->getPageLink('history');
            $privacy_link = CoreCookieCookie::getPrivacyCmsLink();
            $link_personal = Context::getContext()->link->getModuleLink($this->module->name, 'personalData', []);
            $gdpr_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_APPI_REQUEST
            ]));
            $gdpr_personal_information = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_GDPR,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION
            ]));
            $gdpr_request_report = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_GDPR,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_REPORT_REQUEST
            ]));
            $gdpr_deletion_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_GDPR,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DELETION_REQUEST
            ]));
            $content_GDPR_page_template = str_replace([
                '{privacy_link}',
                '{order_link}',
                '{identity_link}',
                '{gdpr_request}',
                '{gdpr_personal_information}',
                '{gdpr_request_report}',
                '{gdpr_deletion_request}',
            ], [
                $privacy_link,
                $order_history_link,
                $identity_link,
                $gdpr_request,
                $gdpr_personal_information,
                $gdpr_request_report,
                $gdpr_deletion_request,
            ], $content_GDPR_page_template);
            foreach ($languages as $lang) {
                $content_GDPR_page[$lang['id_lang']] = $content_GDPR_page_template;
            }
            if (!$this->module->setConfiguration("CONTENT_GDPR_PAGE", $content_GDPR_page, true)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update content of the GDPR Page'));
            }
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("content_gdpr_page", $content_GDPR_page);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        private function genCcpaContent() {
            $languages = Language::getLanguages(false);
            $content_CCPA_page = [];
            $content_CCPA_page_template = Tools::file_get_contents(_PS_MODULE_DIR_ . $this->module->name . '/content.ccpa.page.txt');

            $identity_link = Context::getContext()->link->getPageLink('identity');
            $order_history_link = Context::getContext()->link->getPageLink('history');
            $privacy_link = CoreCookieCookie::getPrivacyCmsLink();
            $link_personal = Context::getContext()->link->getModuleLink($this->module->name, 'personalData', []);
            $ccpa_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_CCPA_REQUEST
            ]));
            $ccpa_personal_information = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION
            ]));
            $ccpa_request_report = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_REPORT_REQUEST
            ]));
            $ccpa_do_not_sell = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DO_NOT_SELL_REQUEST
            ]));
            $ccpa_deletion_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DELETION_REQUEST
            ]));
            $content_CCPA_page_template = str_replace([
                '{privacy_link}',
                '{order_link}',
                '{identity_link}',
                '{ccpa_request}',
                '{ccpa_personal_information}',
                '{ccpa_request_report}',
                '{ccpa_do_not_sell}',
                '{ccpa_deletion_request}',
            ], [
                $privacy_link,
                $order_history_link,
                $identity_link,
                $ccpa_request,
                $ccpa_personal_information,
                $ccpa_request_report,
                $ccpa_do_not_sell,
                $ccpa_deletion_request,
            ], $content_CCPA_page_template);

            foreach ($languages as $lang) {
                $content_CCPA_page[$lang['id_lang']] = $content_CCPA_page_template;
            }
            if (!$this->module->setConfiguration("CONTENT_CCPA_PAGE", $content_CCPA_page, true)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update content of the CCPA Page'));
            }
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("content_ccpa_page", $content_CCPA_page);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        private function genAppiContent() {
            $languages = Language::getLanguages(false);
            $content_APPI_page = [];
            $content_APPI_page_template = Tools::file_get_contents(_PS_MODULE_DIR_ . $this->module->name . '/content.appi.page.txt');

            $identity_link = Context::getContext()->link->getPageLink('identity');
            $order_history_link = Context::getContext()->link->getPageLink('history');
            $privacy_link = CoreCookieCookie::getPrivacyCmsLink();
            $link_personal = Context::getContext()->link->getModuleLink($this->module->name, 'personalData', []);
            $appi_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_GDPR,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_GDPR_REQUEST
            ]));
            $appi_personal_information = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION
            ]));
            $appi_request_report = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_REPORT_REQUEST
            ]));
            $appi_do_not_sell = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DO_NOT_SELL_REQUEST
            ]));
            $appi_deletion_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DELETION_REQUEST
            ]));
            $content_APPI_page_template = str_replace([
                '{privacy_link}',
                '{order_link}',
                '{identity_link}',
                '{appi_request}',
                '{appi_personal_information}',
                '{appi_request_report}',
                '{appi_do_not_sell}',
                '{appi_deletion_request}',
            ], [
                $privacy_link,
                $order_history_link,
                $identity_link,
                $appi_request,
                $appi_personal_information,
                $appi_request_report,
                $appi_do_not_sell,
                $appi_deletion_request,
            ], $content_APPI_page_template);

            foreach ($languages as $lang) {
                $content_APPI_page[$lang['id_lang']] = $content_APPI_page_template;
            }
            if (!$this->module->setConfiguration("CONTENT_APPI_PAGE", $content_APPI_page, true)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update content of the APPI Page'));
            }
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("content_appi_page", $content_APPI_page);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        private function genPipedaContent() {
            $languages = Language::getLanguages(false);
            $content_PIPEDA_page = [];
            $content_PIPEDA_page_template = Tools::file_get_contents(_PS_MODULE_DIR_ . $this->module->name . '/content.pipeda.page.txt');

            $identity_link = Context::getContext()->link->getPageLink('identity');
            $order_history_link = Context::getContext()->link->getPageLink('history');
            $privacy_link = CoreCookieCookie::getPrivacyCmsLink();
            $link_personal = Context::getContext()->link->getModuleLink($this->module->name, 'personalData', []);
            $pipeda_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_PIPEDA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PIPEDA_REQUEST
            ]));
            $pipeda_personal_information = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_PIPEDA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION
            ]));
            $pipeda_request_report = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_PIPEDA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_REPORT_REQUEST
            ]));
            $content_PIPEDA_page_template = str_replace([
                '{privacy_link}',
                '{order_link}',
                '{identity_link}',
                '{pipeda_request}',
                '{pipeda_personal_information}',
                '{pipeda_request_report}',
            ], [
                $privacy_link,
                $order_history_link,
                $identity_link,
                $pipeda_request,
                $pipeda_personal_information,
                $pipeda_request_report,
            ], $content_PIPEDA_page_template);

            foreach ($languages as $lang) {
                $content_PIPEDA_page[$lang['id_lang']] = $content_PIPEDA_page_template;
            }
            if (!$this->module->setConfiguration("CONTENT_PIPEDA_PAGE", $content_PIPEDA_page, true)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update content of the PIPEDA Page'));
            }
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("content_pipeda_page", $content_PIPEDA_page);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        private function navigation()
        {
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::code(1);
            $action = Tools::getValue('_a');
            $content = "";
            switch ($action) {
                case 'gen_pipeda_content':
                    $this->genPipedaContent();
                    break;
                case 'gen_appi_content':
                    $this->genAppiContent();
                    break;
                case 'gen_ccpa_content':
                    $this->genCcpaContent();
                    break;
                case 'gen_gdpr_content':
                    $this->genGdprContent();
                    break;
                case 'update_form':
                    $this->updateForm();
                    break;
                case 'reset_customer_consent':
                    $this->resetCustomerConsent();
                    break;
                case 'update_email_templates':
                    $this->updateEmailTemplates();
                    break;
                default:
                    $content = $this->pageDefault();
            }
            return $content;
        }

        private function loadAction()
        {
            try{
                $this->navigation();
            }catch (Exception $e){
                $this->module->saveLog(date("d-m-Y H:i:s").'; Line: '.$e->getLine().'; File: '.$e->getFile().'; '.$e->getMessage());
                $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->module->l('There is an error. Please try again.'));
            }
            $this->module->name::$config['_CLIENT_CLASS_NAME']::appData('current_path', $this->link_controller);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('meta_title', $this->meta_title);
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::ajaxRelease();
        }

        public function updateEmailTemplates()
        {
            $email_title_admin = [];
            $email_title_customer_confirm = [];
            $email_title_customer_notify = [];
            $email_content_admin = [];
            $email_content_customer_confirm = [];
            $email_content_customer_notify = [];

            $email_variable_gdpr_request = [];
            $email_variable_personal_information = [];
            $email_variable_report_request = [];
            $email_variable_deletion_request = [];
            $email_variable_ccpa_request = [];
            $email_variable_do_not_sell_request = [];
            $email_variable_appi_request = [];
            $email_variable_pipeda_request = [];

            $languages = $this->module->getLanguages();

            foreach ($languages as $lang){
                $email_title_admin[(int)$lang['id_lang']] = Tools::getValue('email_title_admin')[$lang['id_lang']];
                $email_title_customer_confirm[(int)$lang['id_lang']] = Tools::getValue('email_title_customer_confirm')[$lang['id_lang']];
                $email_title_customer_notify[(int)$lang['id_lang']] = Tools::getValue('email_title_customer_notify')[$lang['id_lang']];
                $email_content_admin[(int)$lang['id_lang']] = Tools::getValue('email_content_admin')[$lang['id_lang']];
                $email_content_customer_confirm[(int)$lang['id_lang']] = Tools::getValue('email_content_customer_confirm')[$lang['id_lang']];
                $email_content_customer_notify[(int)$lang['id_lang']] = Tools::getValue('email_content_customer_notify')[$lang['id_lang']];

                $email_variable_gdpr_request[(int)$lang['id_lang']] = Tools::getValue('email_variable_gdpr_request')[$lang['id_lang']];
                $email_variable_personal_information[(int)$lang['id_lang']] = Tools::getValue('email_variable_personal_information')[$lang['id_lang']];
                $email_variable_report_request[(int)$lang['id_lang']] = Tools::getValue('email_variable_report_request')[$lang['id_lang']];
                $email_variable_deletion_request[(int)$lang['id_lang']] = Tools::getValue('email_variable_deletion_request')[$lang['id_lang']];
                $email_variable_ccpa_request[(int)$lang['id_lang']] = Tools::getValue('email_variable_ccpa_request')[$lang['id_lang']];
                $email_variable_do_not_sell_request[(int)$lang['id_lang']] = Tools::getValue('email_variable_do_not_sell_request')[$lang['id_lang']];
                $email_variable_appi_request[(int)$lang['id_lang']] = Tools::getValue('email_variable_appi_request')[$lang['id_lang']];
                $email_variable_pipeda_request[(int)$lang['id_lang']] = Tools::getValue('email_variable_pipeda_request')[$lang['id_lang']];

                $email_variable_search = ['%7Bshop_url%7D', '%7Bshop_logo%7D', '%7Bmy_account_url%7D', '%7Bconfirm_url%7D'];
                $email_variable_replace = ['{shop_url}', '{shop_logo}', '{my_account_url}', '{confirm_url}'];

                $email_content_admin[(int)$lang['id_lang']] = str_replace($email_variable_search, $email_variable_replace, $email_content_admin[(int)$lang['id_lang']]);
                $email_content_customer_confirm[(int)$lang['id_lang']] = str_replace($email_variable_search, $email_variable_replace, $email_content_customer_confirm[(int)$lang['id_lang']]);
                $email_content_customer_notify[(int)$lang['id_lang']] = str_replace($email_variable_search, $email_variable_replace, $email_content_customer_notify[(int)$lang['id_lang']]);

                CoreCookieMail::createFolderEmail($this->module->name, $lang, 'email_title_admin', $email_title_admin[(int)$lang['id_lang']], true);
                CoreCookieMail::createFolderEmail($this->module->name, $lang, 'email_content_admin', $email_content_admin[(int)$lang['id_lang']], true);
                CoreCookieMail::createFolderEmail($this->module->name, $lang, 'email_content_admin', $email_content_admin[(int)$lang['id_lang']]);
                CoreCookieMail::createFolderEmail($this->module->name, $lang, 'email_title_customer_confirm', $email_title_customer_confirm[(int)$lang['id_lang']], true);
                CoreCookieMail::createFolderEmail($this->module->name, $lang, 'email_content_customer_confirm', $email_content_customer_confirm[(int)$lang['id_lang']], true);
                CoreCookieMail::createFolderEmail($this->module->name, $lang, 'email_content_customer_confirm', $email_content_customer_confirm[(int)$lang['id_lang']]);
                CoreCookieMail::createFolderEmail($this->module->name, $lang, 'email_title_customer_notify', $email_title_customer_notify[(int)$lang['id_lang']], true);
                CoreCookieMail::createFolderEmail($this->module->name, $lang, 'email_content_customer_notify', $email_content_customer_notify[(int)$lang['id_lang']], true);
                CoreCookieMail::createFolderEmail($this->module->name, $lang, 'email_content_customer_notify', $email_content_customer_notify[(int)$lang['id_lang']]);
            }

            $this->module->setConfiguration("EMAIL_TITLE_ADMIN", $email_title_admin, true);
            $this->module->setConfiguration("EMAIL_TITLE_CUSTOMER_CONFIRM", $email_title_customer_confirm, true);
            $this->module->setConfiguration("EMAIL_TITLE_CUSTOMER_NOTIFY", $email_title_customer_notify, true);
            $this->module->setConfiguration("EMAIL_CONTENT_ADMIN", $email_content_admin, true);
            $this->module->setConfiguration("EMAIL_CONTENT_CUSTOMER_CONFIRM", $email_content_customer_confirm, true);
            $this->module->setConfiguration("EMAIL_CONTENT_CUSTOMER_NOTIFY", $email_content_customer_notify, true);

            $this->module->setConfiguration("EMAIL_VARIABLE_GDPR_REQUEST", $email_variable_gdpr_request, true);
            $this->module->setConfiguration("EMAIL_VARIABLE_PERSONAL_INFORMATION", $email_variable_personal_information, true);
            $this->module->setConfiguration("EMAIL_VARIABLE_REPORT_REQUEST", $email_variable_report_request, true);
            $this->module->setConfiguration("EMAIL_VARIABLE_DELETION_REQUEST", $email_variable_deletion_request, true);
            $this->module->setConfiguration("EMAIL_VARIABLE_CCPA_REQUEST", $email_variable_ccpa_request, true);
            $this->module->setConfiguration("EMAIL_VARIABLE_DO_NOT_SELL_REQUEST", $email_variable_do_not_sell_request, true);
            $this->module->setConfiguration("EMAIL_VARIABLE_APPI_REQUEST", $email_variable_appi_request, true);
            $this->module->setConfiguration("EMAIL_VARIABLE_PIPEDA_REQUEST", $email_variable_pipeda_request, true);

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }
    }
?>
