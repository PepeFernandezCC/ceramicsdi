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

    class AdminCookiefaqController extends ModuleAdminController
    {
        public function __construct()
        {
            parent::__construct();
            $this->meta_title = $this->l('FAQs');
            $this->display = 'view';
            $this->context = Context::getContext();
            $this->link_controller = $this->context->link->getAdminLink('AdminCookiefaq', true);
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
            $this->toolbar_title[] = $this->l('FAQs');
        }

        public function renderView()
        {
            $content = $this->navigation();
            return $this->module->layoutAdmin($content);
        }

        public function pageDefault()
        {
            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/faq.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/faq.core", "admin", $tpl);
        }

        private function navigation()
        {
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::code(1);
            $action = Tools::getValue('_a');
            $content = "";
            switch ($action) {
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
