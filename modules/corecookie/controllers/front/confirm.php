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

require_once(_PS_MODULE_DIR_ . 'corecookie/required.php');

class CorecookieConfirmModuleFrontController extends ModuleFrontController {

    public $auth = false;

    public function __construct() {
        $this->context = Context::getContext();
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::__construct();
    }

    public function initContent() {
        parent::initContent();
        $success = $this->module->l('Submit request successfully.', 'request');
        $link_expired_message = $this->module->l('Link has expired. Please try again later.', 'confirm');
        if(!Tools::isSubmit('meta_type')) {
            $this->context->controller->errors[] = $link_expired_message;
        }
        if(Tools::isSubmit('success')) {
            $success = $this->module->l('We have sent a confirmation link to your email. Please check your email.', 'confirm');
        }

        if(Tools::isSubmit('token') && !count($this->context->controller->errors)) {
            $data = CoreCookieCryptor::getDataByLink(Tools::getValue('token'));
            if($data === false || CoreCookieConsentLog::checkMetaTypePendingExists(Tools::getValue('meta_type'), $data['id_customer'])) {
                $this->context->controller->errors[] = $link_expired_message;
            }

            if(!count($this->context->controller->errors)){
                $consent_log = new CoreCookieConsentLog((int)$data['id_request']);
                if($consent_log->customer_id != $data['id_customer']) {
                    $this->context->controller->errors[] = $link_expired_message;
                }else{
                    $consent_log->status = 'pending';
                    $consent_log->update();
                    CoreCookieMail::send('admin', $consent_log);
                    $success = $this->module->l('Your request has been sent successfully. We will review and notify you by email.', 'confirm');
                }
            }
        }

        if(!count($this->context->controller->errors)) {
            $this->context->controller->success[] = $success;
        }

        $this->setTemplate('module:'.$this->module->name.'/views/templates/front/confirm.tpl');
    }
}
?>
