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

    class AdminCookierecordController extends ModuleAdminController
    {
        public function __construct()
        {
            parent::__construct();
            $this->meta_title = $this->l('Records');
            $this->display = 'view';
            $this->context = Context::getContext();
            $this->link_controller = $this->context->link->getAdminLink('AdminCookierecord', true);
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
            $this->toolbar_title[] = $this->l('Records');
        }

        public function renderView()
        {
            $content = $this->navigation();
            return $this->module->layoutAdmin($content);
        }

        public function pageDefault()
        {
            $policy_acceptances = CoreCookiePolicyAcceptance::byPaginated();
            $customer_ids = [];
            foreach ($policy_acceptances as &$policy_acceptance) {
                $customer_ids[] = $policy_acceptance['customer_id'];
                $policy_acceptance['given_consent'] = json_decode($policy_acceptance['given_consent'], true);
            }
            $customers = [];
            if (count($customer_ids) > 0) {
                $sql = 'SELECT *
                FROM `' . _DB_PREFIX_ . 'customer`
                WHERE `id_customer` IN ('.pSQL(implode(",", $customer_ids)).')
                    ' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);

                $customers =  Db::getInstance()->executeS($sql);
            }
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('customers', $customers);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('policy_acceptances', $policy_acceptances);
            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/record/policy.acceptances.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/record/policy.acceptances.core", "admin", $tpl);
        }

        private function policyView() {
            $requests = CoreCookieConsentLog::byPaginated();
            $customer_ids = [];
            foreach ($requests as &$request) {
                $customer_ids[] = $request['customer_id'];
            }
            $customers = [];
            if (count($customer_ids) > 0) {
                $sql = 'SELECT *
                FROM `' . _DB_PREFIX_ . 'customer`
                WHERE `id_customer` IN ('.pSQL(implode(",", $customer_ids)).')
                    ' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);

                $customers =  Db::getInstance()->executeS($sql);
            }
            $customers = CoreCookieConsentLog::getAllCustomers();
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('customers', $customers);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('requests', $requests);
            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/record/request.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/record/request.core", "admin", $tpl);
        }

        private function deletionView() {
            $requests = CoreCookieConsentLog::byPaginated(CoreCookieConsentLog::$METATYPE_DELETION_REQUEST);
            $customer_ids = [];
            foreach ($requests as &$request) {
                $customer_ids[] = $request['customer_id'];
            }
            $customers = [];
            if (count($customer_ids) > 0) {
                $sql = 'SELECT *
                FROM `' . _DB_PREFIX_ . 'customer`
                WHERE `id_customer` IN ('.pSQL(implode(",", $customer_ids)).')
                    ' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);

                $customers =  Db::getInstance()->executeS($sql);
            }
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('customers', $customers);
            $this->module->name::$config['_CLIENT_CLASS_NAME']::pageData('requests', $requests);
            $tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/module/record/deletion.core';
            return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/module/record/deletion.core", "admin", $tpl);
        }

        public function removeRequest() {
            $request_log = new CoreCookieConsentLog(Tools::getValue('id'));
            if (!$request_log->id) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Request is invalid'));
            }
            if (!$request_log->delete()) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Cannot remove this request'));
            }

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("request", $request_log);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Removed successfully'));
        }

        public function markDone() {
            $request_log = new CoreCookieConsentLog(Tools::getValue('id'));
            if (!$request_log->id) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Request is invalid'));
            }
            $request_log->status = CoreCookieConsentLog::$STATUS_DONE;
            if (!$request_log->save()) {
                return $this->module->name::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Cannot mark as Done this request'));
            }
            CoreCookieMail::send('customer', $request_log, 'notify');

            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::extra("request", $request_log);
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::success($this->l('Updated successfully'));
        }

        private function navigation()
        {
            $this->module->name::$config['_TEMPLATE_CLASS_NAME']::code(1);
            $action = Tools::getValue('_a');
            $content = "";
            switch ($action) {
                case "mark_done":
                    $this->markDone();
                    break;
                case "remove":
                    $this->removeRequest();
                    break;
                case "dr_view":
                    $content = $this->deletionView();
                    break;
                case "policy_view":
                    $content = $this->policyView();
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
