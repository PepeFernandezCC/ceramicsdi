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

    class AdminCookiemanagementController extends ModuleAdminController
    {
        public function __construct()
        {
            parent::__construct();
            $this->meta_title = $this->l('Cookie Management');
            $this->display = 'view';
            $this->context = Context::getContext();
            $this->link_controller = $this->context->link->getAdminLink('AdminCookiemanagement', true);
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
            $this->toolbar_title[] = $this->l('Cookie Management');
        }

        public function renderView()
        {
            $content = $this->navigation();
            return $this->module->layoutAdmin($content);
        }

        public function pageDefault()
        {
            $cookies = CoreCookieCookie::byPaginated();

            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('cookies', $cookies);
            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/cookie.management/category.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/cookie.management/category.core", "admin", $tpl);
        }

        /**
         * @desc Remove cookie
         */
        private function remove() {
            $cookie = new CoreCookieCookie((int) Tools::getValue('id'));
            if (!$cookie->id) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Cookie is invalid'));
            }
            if (!$cookie->delete()) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not remove this cookie'));
            }

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("cookie", $cookie);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Removed successfully'));
        }

        private function edit() {
            $cookie = new CoreCookieCookie((int) Tools::getValue('id'));
            if (!$cookie->id) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Cookie is invalid'));
            }
            $name = Tools::getValue('cookie');
            if ($name == '') {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Cookie Name is required'));
            }
            $cookie->name = $name;
            $cookie->category = Tools::getValue('category');
            $cookie->status = Tools::getValue('status');
            $cookie->last_update = time();
    
            $languages = Language::getLanguages();
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $contents = Tools::getValue('content');
            $content_default = $contents[$id_lang_default];
            foreach($languages as $lang) {
                $content = isset($contents[$lang['id_lang']]) ? $contents[$lang['id_lang']] : $content_default;
                $cookie->content[$lang['id_lang']] = $content;
                $this->module->resetKeyword($cookie);
                $this->module->addKeyword($cookie, $cookie->name);
                $this->module->addKeyword($cookie, $cookie->status);
                $this->module->addKeyword($cookie, $cookie->category);
                $this->module->addKeyword($cookie, $content);
                $cookie->keywords[$lang['id_lang']] = $cookie->keywords;
            }
            

            if (!$cookie->save()) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update this cookie'));
            }

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("cookie", $cookie);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        private function create() {
            $cookie = new CoreCookieCookie();
            $name = Tools::getValue('cookie');
            if ($name == '') {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Cookie Name is required'));
            }
            $cookie->name = $name;
            $cookie->category = Tools::getValue('category');
            $cookie->status = Tools::getValue('status');
            $cookie->content = Tools::getValue('content');
            $cookie->since = time();
            $cookie->last_update = time();
            $this->module->resetKeyword($cookie);
            $this->module->addKeyword($cookie, $cookie->name);
            $this->module->addKeyword($cookie, $cookie->status);
            $this->module->addKeyword($cookie, $cookie->category);
            $this->module->addKeyword($cookie, $cookie->content);

            if (!$cookie->save()) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not create cookie'));
            }

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("cookie", $cookie);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Created successfully'));
        }

        private function displayCookieScanner() {
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('scanner_settings', json_decode($this->module->getConfiguration('SCANNER_SETTINGS'), true));
            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/cookie.management/scanner.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/cookie.management/scanner.core", "admin", $tpl);
        }

        private function scanCookie() {
            $scanner_settings = json_decode($this->module->getConfiguration('SCANNER_SETTINGS'), true);
            $scanner_settings['last_scanned'] = time();
            $scanner_settings['scanned_cookies'] = array_keys($_COOKIE);

            if (!$this->module->setConfiguration("SCANNER_SETTINGS", json_encode($scanner_settings))) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Can not update cookie scanner'));
            }

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("scanner_settings", $scanner_settings);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Scan cookies successfully'));
        }

        private function autoTransferScanned() {
            $cookies = CoreCookieCookie::all();
            $un_scan_cookies = [];
            $scanned__cookies = [];
            foreach ($cookies as $cookie) {
                $scanned__cookies[] = $cookie['name'];
            }
            foreach ($_COOKIE as $key => $cookie_name) {
                if (!in_array($key, $scanned__cookies)) {
                    $un_scan_cookies[] = $key;
                }
            }

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("cookies", $un_scan_cookies);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Scan cookies successfully'));
        }
        
        private function showCookie() {
            $cookie = new CoreCookieCookie(Tools::getValue('id'));
            if (!$cookie->id) {
                $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Cookie is invalid'), true);
            }
    
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("cookie", $cookie->release());
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Get data successfully'));
        }

        private function navigation()
        {
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::code(1);
            $action = Tools::getValue('_a');
            $content = "";
            switch ($action) {
                case "show":
                    $this->showCookie();
                    break;
                case "auto_transfer_scanned":
                    $this->autoTransferScanned();
                    break;
                case "scanner":
                    $this->scanCookie();
                    break;
                case "edit":
                    $this->edit();
                    break;
                case "remove":
                    $this->remove();
                    break;
                case "create":
                    $this->create();
                    break;
                case "cs_view":
                    $content = $this->displayCookieScanner();
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
