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

    class AdminCookiebarController extends ModuleAdminController
    {
        public function __construct()
        {
            parent::__construct();
            $this->meta_title = $this->l('Cookie Bar');
            $this->display = 'view';
            $this->context = Context::getContext();
            $this->link_controller = $this->context->link->getAdminLink('AdminCookiebar', true);
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
            $this->toolbar_title[] = $this->l('Cookie Bar');
        }

        public function renderView()
        {
            $content = $this->navigation();
            return $this->module->layoutAdmin($content);
        }

        public function pageDefault()
        {
            $languages = $this->module->getLanguages();
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
            foreach ($languages as $lang){
                $cookie_consent_text[(int)$lang['id_lang']] = $this->module->getConfiguration("COOKIE_CONSENT_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $privacy_policy_text[(int)$lang['id_lang']] = $this->module->getConfiguration("PRIVACY_POLICY_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $preferences_button_text[(int)$lang['id_lang']] = $this->module->getConfiguration("PREFERENCES_BUTTON_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $reject_button_text[(int)$lang['id_lang']] = $this->module->getConfiguration("REJECT_BUTTON_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $accept_button_text[(int)$lang['id_lang']] = $this->module->getConfiguration("ACCEPT_BUTTON_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $privacy_policy_link[(int)$lang['id_lang']] = $this->module->getConfiguration("PRIVACY_POLICY_LINK", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);

                $preferences_popup_header_title[(int)$lang['id_lang']] = $this->module->getConfiguration("PREFERENCES_POPUP_HEADER_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $preferences_popup_header_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("PREFERENCES_POPUP_HEADER_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $strict_cookie_title[(int)$lang['id_lang']] = $this->module->getConfiguration("STRICT_COOKIE_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $strict_cookie_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("STRICT_COOKIE_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $analytics_cookie_title[(int)$lang['id_lang']] = $this->module->getConfiguration("ANALYTICS_COOKIE_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $analytics_cookie_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("ANALYTICS_COOKIE_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $marketing_cookie_title[(int)$lang['id_lang']] = $this->module->getConfiguration("MARKETING_COOKIE_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $marketing_cookie_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("MARKETING_COOKIE_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $functional_cookie_title[(int)$lang['id_lang']] = $this->module->getConfiguration("FUNCTIONAL_COOKIE_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $functional_cookie_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("FUNCTIONAL_COOKIE_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $accept_selected_button[(int)$lang['id_lang']] = $this->module->getConfiguration("ACCEPT_SELECTED_BUTTON", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $accept_all_selected_button[(int)$lang['id_lang']] = $this->module->getConfiguration("ACCEPT_ALL_SELECTED_BUTTON", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
            }
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('cookie_consent_text', $cookie_consent_text);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('privacy_policy_text', $privacy_policy_text);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('privacy_policy_link', $privacy_policy_link);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('preferences_button_text', $preferences_button_text);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('reject_button_text', $reject_button_text);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('accept_button_text', $accept_button_text);

            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('preferences_popup_header_title', $preferences_popup_header_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('preferences_popup_header_desc', $preferences_popup_header_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('strict_cookie_title', $strict_cookie_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('strict_cookie_desc', $analytics_cookie_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('analytics_cookie_title', $analytics_cookie_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('analytics_cookie_desc', $analytics_cookie_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('marketing_cookie_title', $marketing_cookie_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('marketing_cookie_desc', $marketing_cookie_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('functional_cookie_title', $functional_cookie_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('functional_cookie_desc', $functional_cookie_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('accept_selected_button', $accept_selected_button);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('accept_all_selected_button', $accept_all_selected_button);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('settings', json_decode($this->module->getConfiguration('COOKIE_BAR', null, $this->module->id_shop_group, $this->module->id_shop), true));
            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/cookie.bar/content.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/cookie.bar/content.core", "admin", $tpl);
        }

        public function contentBehaviorView() {
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('settings', json_decode($this->module->getConfiguration('COOKIE_BAR', null, $this->module->id_shop_group, $this->module->id_shop), true));
            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/cookie.bar/behavior.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/cookie.bar/behavior.core", "admin", $tpl);
        }

        public function contentDesignView() {
            $languages = $this->module->getLanguages();
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
                $cookie_consent_text[(int)$lang['id_lang']] = $this->module->getConfiguration("COOKIE_CONSENT_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $privacy_policy_text[(int)$lang['id_lang']] = $this->module->getConfiguration("PRIVACY_POLICY_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $preferences_button_text[(int)$lang['id_lang']] = $this->module->getConfiguration("PREFERENCES_BUTTON_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $reject_button_text[(int)$lang['id_lang']] = $this->module->getConfiguration("REJECT_BUTTON_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $accept_button_text[(int)$lang['id_lang']] = $this->module->getConfiguration("ACCEPT_BUTTON_TEXT", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $privacy_policy_link[(int)$lang['id_lang']] = $this->module->getConfiguration("PRIVACY_POLICY_LINK", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);

                $preferences_popup_header_title[(int)$lang['id_lang']] = $this->module->getConfiguration("PREFERENCES_POPUP_HEADER_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $preferences_popup_header_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("PREFERENCES_POPUP_HEADER_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $strict_cookie_title[(int)$lang['id_lang']] = $this->module->getConfiguration("STRICT_COOKIE_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $strict_cookie_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("STRICT_COOKIE_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $analytics_cookie_title[(int)$lang['id_lang']] = $this->module->getConfiguration("ANALYTICS_COOKIE_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $analytics_cookie_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("ANALYTICS_COOKIE_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $marketing_cookie_title[(int)$lang['id_lang']] = $this->module->getConfiguration("MARKETING_COOKIE_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $marketing_cookie_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("MARKETING_COOKIE_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $functional_cookie_title[(int)$lang['id_lang']] = $this->module->getConfiguration("FUNCTIONAL_COOKIE_TITLE", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $functional_cookie_desc[(int)$lang['id_lang']] = $this->module->getConfiguration("FUNCTIONAL_COOKIE_DESC", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $accept_selected_button[(int)$lang['id_lang']] = $this->module->getConfiguration("ACCEPT_SELECTED_BUTTON", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
                $accept_all_selected_button[(int)$lang['id_lang']] = $this->module->getConfiguration("ACCEPT_ALL_SELECTED_BUTTON", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
    
                $label_reopen_btn[(int)$lang['id_lang']] = $this->module->getConfiguration("LABEL_REOPEN_BTN", (int)$lang['id_lang'], $this->module->id_shop_group, $this->module->id_shop);
            }
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('cookie_consent_text', $cookie_consent_text);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('privacy_policy_text', $privacy_policy_text);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('privacy_policy_link', $privacy_policy_link);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('preferences_button_text', $preferences_button_text);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('reject_button_text', $reject_button_text);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('accept_button_text', $accept_button_text);

            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('preferences_popup_header_title', $preferences_popup_header_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('preferences_popup_header_desc', $preferences_popup_header_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('strict_cookie_title', $strict_cookie_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('strict_cookie_desc', $analytics_cookie_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('analytics_cookie_title', $analytics_cookie_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('analytics_cookie_desc', $analytics_cookie_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('marketing_cookie_title', $marketing_cookie_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('marketing_cookie_desc', $marketing_cookie_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('functional_cookie_title', $functional_cookie_title);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('functional_cookie_desc', $functional_cookie_desc);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('accept_selected_button', $accept_selected_button);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('accept_all_selected_button', $accept_all_selected_button);
            
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('label_reopen_btn', $label_reopen_btn);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('settings', json_decode($this->module->getConfiguration('COOKIE_BAR', null, $this->module->id_shop_group, $this->module->id_shop), true));
            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/cookie.bar/design.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/cookie.bar/design.core", "admin", $tpl);
        }

        private function updateCookieContent() {
            $languages = $this->module->getLanguages(false);
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

            foreach ($languages as $lang){
                $cookie_consent_text[$lang['id_lang']] = Tools::getValue('cookie_consent_text')[$lang['id_lang']];
                $privacy_policy_text[$lang['id_lang']] = Tools::getValue('privacy_policy_text')[$lang['id_lang']];
                $preferences_button_text[$lang['id_lang']] = Tools::getValue('preferences_button_text')[$lang['id_lang']];
                $reject_button_text[$lang['id_lang']] = Tools::getValue('reject_button_text')[$lang['id_lang']];
                $accept_button_text[$lang['id_lang']] = Tools::getValue('accept_button_text')[$lang['id_lang']];
                $privacy_policy_link[$lang['id_lang']] = Tools::getValue('privacy_policy_link')[$lang['id_lang']];

                $preferences_popup_header_title[$lang['id_lang']] = Tools::getValue('preferences_popup_header_title')[$lang['id_lang']];
                $preferences_popup_header_desc[$lang['id_lang']] = Tools::getValue('preferences_popup_header_desc')[$lang['id_lang']];
                $strict_cookie_title[$lang['id_lang']] = Tools::getValue('strict_cookie_title')[$lang['id_lang']];
                $strict_cookie_desc[$lang['id_lang']] = Tools::getValue('strict_cookie_desc')[$lang['id_lang']];
                $analytics_cookie_title[$lang['id_lang']] = Tools::getValue('analytics_cookie_title')[$lang['id_lang']];
                $analytics_cookie_desc[$lang['id_lang']] = Tools::getValue('analytics_cookie_desc')[$lang['id_lang']];
                $marketing_cookie_title[$lang['id_lang']] = Tools::getValue('marketing_cookie_title')[$lang['id_lang']];
                $marketing_cookie_desc[$lang['id_lang']] = Tools::getValue('marketing_cookie_desc')[$lang['id_lang']];
                $functional_cookie_title[$lang['id_lang']] = Tools::getValue('functional_cookie_title')[$lang['id_lang']];
                $functional_cookie_desc[$lang['id_lang']] = Tools::getValue('functional_cookie_desc')[$lang['id_lang']];
                $accept_selected_button[$lang['id_lang']] = Tools::getValue('accept_selected_button')[$lang['id_lang']];
                $accept_all_selected_button[$lang['id_lang']] = Tools::getValue('accept_all_selected_button')[$lang['id_lang']];
            }

            if (!$this->module->setConfiguration("COOKIE_CONSENT_TEXT", $cookie_consent_text, true, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update cookie content text'));
            }
            if (!$this->module->setConfiguration("PRIVACY_POLICY_TEXT", $privacy_policy_text, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update privacy policy text'));
            }
            if (!$this->module->setConfiguration("PREFERENCES_BUTTON_TEXT", $preferences_button_text, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update preferences button text'));
            }
            if (!$this->module->setConfiguration("REJECT_BUTTON_TEXT", $reject_button_text, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update reject button text'));
            }
            if (!$this->module->setConfiguration("ACCEPT_BUTTON_TEXT", $accept_button_text, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update accept button text'));
            }
            if (!$this->module->setConfiguration("PRIVACY_POLICY_LINK", $privacy_policy_link, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update privacy policy link'));
            }
            if (!$this->module->setConfiguration("PREFERENCES_POPUP_HEADER_TITLE", $preferences_popup_header_title, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update preferences popup header title'));
            }
            if (!$this->module->setConfiguration("PREFERENCES_POPUP_HEADER_DESC", $preferences_popup_header_desc, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update preferences popup header description'));
            }
            if (!$this->module->setConfiguration("STRICT_COOKIE_TITLE", $strict_cookie_title, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update strict cookie title'));
            }
            if (!$this->module->setConfiguration("STRICT_COOKIE_DESC", $strict_cookie_desc, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update strict cookie description'));
            }
            if (!$this->module->setConfiguration("ANALYTICS_COOKIE_TITLE", $analytics_cookie_title, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update analytics cookie title'));
            }
            if (!$this->module->setConfiguration("ANALYTICS_COOKIE_DESC", $analytics_cookie_desc, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update analytics cookie description'));
            }
            if (!$this->module->setConfiguration("MARKETING_COOKIE_TITLE", $marketing_cookie_title, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update marketing cookie title'));
            }
            if (!$this->module->setConfiguration("MARKETING_COOKIE_DESC", $marketing_cookie_desc, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update marketing cookie description'));
            }
            if (!$this->module->setConfiguration("FUNCTIONAL_COOKIE_TITLE", $functional_cookie_title, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update functional cookie title'));
            }
            if (!$this->module->setConfiguration("FUNCTIONAL_COOKIE_DESC", $functional_cookie_desc, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update functional cookie description'));
            }
            if (!$this->module->setConfiguration("ACCEPT_SELECTED_BUTTON", $accept_selected_button, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update accept selected button'));
            }
            if (!$this->module->setConfiguration("ACCEPT_ALL_SELECTED_BUTTON", $accept_all_selected_button, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update accept all selected button'));
            }
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        /**
         * @desc Update cookie behavior
         */
        private function updateCookieBehavior() {
            $initial_state_cookie_bar_array = [];
            $states_when_close_cookie_bar_array = [];
            $settings = json_decode($this->module->getConfiguration('COOKIE_BAR', null, $this->module->id_shop_group, $this->module->id_shop), true);
            $settings['regard_initial_state_when_accept_cookie_bar'] = Tools::getValue('regard_initial_state_when_accept_cookie_bar');
            $settings['references_display_cookie_categories'] = Tools::getValue('references_display_cookie_categories');
            if (Tools::isSubmit('initial_block_analytics_cookies_bac')) {
                $initial_state_cookie_bar_array[] = Tools::getValue('initial_block_analytics_cookies_bac');
            }
            if (Tools::isSubmit('initial_block_marketing_cookies_bmc')) {
                $initial_state_cookie_bar_array[] = Tools::getValue('initial_block_marketing_cookies_bmc');
            }
            if (Tools::isSubmit('initial_block_functionality_cookies_bfc')) {
                $initial_state_cookie_bar_array[] = Tools::getValue('initial_block_functionality_cookies_bfc');
            }
            if (Tools::isSubmit('initial_keep_all_store_cookies_kasc')) {
                $initial_state_cookie_bar_array = [Tools::getValue('initial_keep_all_store_cookies_kasc')];
            }

            if (Tools::isSubmit('wc_block_analytics_cookies_bac')) {
                $states_when_close_cookie_bar_array[] = Tools::getValue('wc_block_analytics_cookies_bac');
            }
            if (Tools::isSubmit('wc_block_marketing_cookies_bmc')) {
                $states_when_close_cookie_bar_array[] = Tools::getValue('wc_block_marketing_cookies_bmc');
            }
            if (Tools::isSubmit('wc_block_functionality_cookies_bfc')) {
                $states_when_close_cookie_bar_array[] = Tools::getValue('wc_block_functionality_cookies_bfc');
            }
            if (Tools::isSubmit('wc_keep_all_store_cookies_kasc')) {
                $states_when_close_cookie_bar_array = [Tools::getValue('wc_keep_all_store_cookies_kasc')];
            }
            $settings['initial_state_cookie_bar'] = $initial_state_cookie_bar_array;
            $settings['states_when_close_cookie_bar'] = $states_when_close_cookie_bar_array;

            if (!$this->module->setConfiguration("COOKIE_BAR", json_encode($settings), false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update cookie behavior'));
            }

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("settings", $settings);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        private function updateCookieDesign() {
            $settings = json_decode($this->module->getConfiguration('COOKIE_BAR', null, $this->module->id_shop_group, $this->module->id_shop), true);
            $settings['design']['theme'] = Tools::getValue('cookie_bar_theme');
            $settings['design']['desktop_display_type'] = Tools::getValue('desktop_display_type');
            $settings['design']['desktop_display_position'] = Tools::getValue('desktop_display_position');
            $settings['design']['mobile_display_position'] = Tools::getValue('mobile_display_position');
            $settings['design']['opacity'] = Tools::getValue('opacity');
            $settings['design']['font_size'] = Tools::getValue('font_size');
            $settings['design']['show_reject_btn'] = Tools::getValue('show_reject_btn');
            $settings['design']['show_close_btn'] = Tools::getValue('show_close_btn');
            $settings['design']['show_icon_desktop'] = Tools::getValue('show_icon_desktop');
            $settings['design']['show_icon_mobile'] = Tools::getValue('show_icon_mobile');
            $settings['design']['icon_color'] = Tools::getValue('icon_color');
            $settings['design']['background'] = Tools::getValue('background');
            $settings['design']['text_color'] = Tools::getValue('text_color');
            $settings['design']['button_background'] = Tools::getValue('button_background');
            $settings['design']['button_text_color'] = Tools::getValue('button_text_color');
            $settings['design']['checkbox_background'] = Tools::getValue('checkbox_background');
            $settings['design']['checkbox_ticked'] = Tools::getValue('checkbox_ticked');
            $settings['design']['custom_css'] = Tools::getValue('custom_css');
    
            $settings['design']['show_reopen_cookie_banner_btn'] = Tools::getValue('show_reopen_cookie_banner_btn');
            $settings['design']['hide_reopen_btn_when_accepted_cookies'] = Tools::getValue('hide_reopen_btn_when_accepted_cookies');
            $settings['design']['use_custom_reopen_btn'] = Tools::getValue('use_custom_reopen_btn');
            $settings['design']['reopen_btn_padding_x'] = CoreCookiePsTools::getInt('reopen_btn_padding_x');
            $settings['design']['reopen_btn_padding_y'] = CoreCookiePsTools::getInt('reopen_btn_padding_y');
            $settings['design']['reopen_btn_font_size'] = CoreCookiePsTools::getInt('reopen_btn_font_size');
            $settings['design']['reopen_btn_border_radius'] = CoreCookiePsTools::getInt('reopen_btn_border_radius');
            $settings['design']['reopen_btn_text_color'] = Tools::getValue('reopen_btn_text_color');
            $settings['design']['reopen_btn_background'] = Tools::getValue('reopen_btn_background');
            $settings['design']['reopen_btn_use_transparent'] = Tools::getValue('reopen_btn_use_transparent');
            $settings['design']['reopen_btn_active_box_shadow'] = Tools::getValue('reopen_btn_active_box_shadow');
            $settings['design']['reopen_btn_position'] = Tools::getValue('reopen_btn_position');
            $settings['design']['reopen_btn_offset_x'] = CoreCookiePsTools::getInt('reopen_btn_offset_x');
            $settings['design']['reopen_btn_offset_y'] = CoreCookiePsTools::getInt('reopen_btn_offset_y');
            $settings['design']['reopen_btn_use_image'] = CoreCookiePsTools::getInt('reopen_btn_use_image');
            
            $blob_prefix = "blob:";
            $cookie_icon_uploaded = Tools::getValue('cookie_icon_uploaded');
            if (substr($cookie_icon_uploaded, 0, strlen($blob_prefix)) === $blob_prefix) {
                $settings['design']['cookie_icon'] = $this->module->uploadBasicImages('cookie_icon');
            } else {
                $settings['design']['cookie_icon'] = $cookie_icon_uploaded;
            }
            $reopen_btn_image_uploaded = Tools::getValue('reopen_btn_image_uploaded');
            if (substr($reopen_btn_image_uploaded, 0, strlen($blob_prefix)) === $blob_prefix) {
                $settings['design']['reopen_btn_image'] = $this->module->uploadBasicImages('reopen_btn_image');
            }
            $languages = $this->module->getLanguages(false);
            $label_reopen_btn = [];
            foreach ($languages as $lang) {
                $label_reopen_btn[$lang['id_lang']] = Tools::getValue('label_reopen_btn')[$lang['id_lang']];
            }

            if (!$this->module->setConfiguration("COOKIE_BAR", json_encode($settings), false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update cookie design'));
            }
    
            if (!$this->module->setConfiguration("LABEL_REOPEN_BTN", $label_reopen_btn, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update label reopen button'));
            }

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("settings", $settings);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }
    
        /**
         * @desc Save preview cookie
         */
        private  function savePreviewCookie() {
            $settings_encoded = Tools::getValue('settings_encoded');
            if ($settings_encoded == "") {
	            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Preview Config is invalid'));
            }
            if (!$this->module->setConfiguration("PREVIEW_CONFIG_COOKIE", $settings_encoded, false, $this->module->id_shop_group, $this->module->id_shop)) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update cookie design'));
            }
	        $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("token", base64_encode(Tools::getRemoteAddr()));
	        $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Preview successfully'));
        }

        private function navigation()
        {
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::code(1);
            $action = Tools::getValue('_a');
            $content = "";
            switch ($action) {
                case "cbb_view":
                    $content = $this->contentBehaviorView();
                    break;
                case "cbd_view":
                    $content = $this->contentDesignView();
                    break;
                case "update_cookie_content":
                    $this->updateCookieContent();
                    break;
                case "update_cookie_behavior":
                    $this->updateCookieBehavior();
                    break;
                case "update_cookie_design":
                    $this->updateCookieDesign();
                    break;
                case "save_preview_cookie";
                    $this->savePreviewCookie();
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
    }
?>
