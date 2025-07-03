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

    class CorecookiePersonalDataModuleFrontController extends ModuleFrontController {
        public $auth = true;
        public function __construct() {
            $this->context = Context::getContext();
            $this->display_column_left = false;
            $this->display_column_right = false;
            parent::__construct();
        }

        public function initContent() {
            parent::initContent();

            if(Tools::isSubmit('form_request')) {
                if(!in_array(Tools::getValue('metatype'), CoreCookieConsentLog::$LIST_METATYPE)) {
                    $this->context->controller->errors[] = $this->module->l('Invalid metatype.', 'request');
                }
                $file = $_FILES['file'];
                $allow_ext = [
                    'text/plain' => 'txt',
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'application/pdf' => 'pdf',
                    'application/msword' => 'doc',
                    'application/vnd.ms-office' => 'doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
                ];
                if($file['size'] && !isset($allow_ext[$file['type']])) {
                    $this->context->controller->errors[] = $this->module->l('Invalid file.', 'request');
                }
                if($file['size'] && $file['size'] > Tools::getMaxUploadSize(5242880)) {
                    $this->context->controller->errors[] = $this->module->l('Maximum file size is 5MB.', 'request');
                }
                if(!Validate::isMessage(Tools::getValue('content'))) {
                    $this->context->controller->errors[] = $this->module->l('Invalid content.', 'request');
                }
                if(!count($this->context->controller->errors) && CoreCookieConsentLog::checkMetaTypePendingExists(Tools::getValue('metatype'), $this->context->customer->id)) {
                    $this->context->controller->errors[] = $this->module->l('Your request is pending, please try again later.', 'request');
                }
                if(!count($this->context->controller->errors)) {
                    $file_name = '';
                    if($file['size']) {
                        $file_name = md5(uniqid(Tools::getValue('metatype')). time()). '.' . $allow_ext[$file['type']];
                        $path_upload = _PS_MODULE_DIR_.$this->module->name.'/views/img/module/' . $file_name;
                        move_uploaded_file($file['tmp_name'], $path_upload);
                    }
                    $consent_log = new CoreCookieConsentLog();
                    $consent_log->email = $this->context->customer->email;
                    $consent_log->customer_id = $this->context->customer->id;
                    $consent_log->metatype = Tools::getValue('metatype');
                    $consent_log->content = Tools::getValue('content');
                    $consent_log->files = $file_name;
                    $consent_log->status = CoreCookieConsentLog::$STATUS_DRAFT;
                    $consent_log->request_source = Tools::getValue('source_page');
                    $consent_log->customer_ip_address = Tools::getRemoteAddr();
                    $consent_log->since = time();
                    $consent_log->last_update = time();
                    $consent_log->add();
                    CoreCookieMail::send('customer', $consent_log, 'confirm');
                    return Tools::redirectLink($this->context->link->getModuleLink($this->module->name, 'confirm', ['success' => 1, 'meta_type' => Tools::getValue('metatype')], true, $this->context->language->id, $this->context->shop->id));
                }
            }
            $request = json_decode(base64_decode(Tools::getValue('request')), true);
            $meta_type = CoreCookieConsentLog::$METATYPE_GDPR_REQUEST;
            $source_page = CoreCookieConsentLog::$SOURCE_OF_REQUEST_GDPR;
            if( isset($request['source_page']) &&
                isset($request['metatype_request']) &&
                in_array($request['metatype_request'], CoreCookieConsentLog::$LIST_METATYPE) &&
                in_array($request['source_page'], CoreCookieConsentLog::$LIST_SOURCE_OF_REQUEST)
            ) {
                $meta_type = $request['metatype_request'];
                $source_page = $request['source_page'];
            }
            $this->context->smarty->assign([
                'meta_type' => $meta_type,
                'source_page' => $source_page,
            ]);
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/personal.data.tpl');
        }
    }
?>
