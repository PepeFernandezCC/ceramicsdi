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
 * @author    AT Tech <attechteams@gmail.com>
 * @copyright 2022 AT Tech
 * @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

class CoreCookieMail
{
    private static $_module = null;

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
     * @param string $send_for ("admin" or "customer")
     * @param object $request CoreCookieConsentLog
     * @param string $option ("confirm" or "notify")
     * @return bool
     */
    public static function send($send_for = 'admin', $request, $option = 'confirm')
    {
        $setting = json_decode(self::getModule()::getConfiguration('GLOBAL_SETTINGS'), true);
        $link_confirm = '';
        $customer = new Customer((int)$request->customer_id);
        if($send_for == 'admin') {
            if(!$setting['receive_email_when_customer_requested']) {
                return true;
            }
            $email = $setting['add_custom_email_to_receive_notifications'];
            if(!Validate::isEmail($setting['add_custom_email_to_receive_notifications'])) {
                $email = Configuration::get('PS_SHOP_EMAIL');
            }
            $title = 'email_title_admin';
            $email_template_content = 'email_content_admin';
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }else{
            $email = $request->email;
            $title = 'email_title_customer_confirm';
            $email_template_content = 'email_content_customer_confirm';
            if($option == 'notify') {
                $title = 'email_title_customer_notify';
                $email_template_content = 'email_content_customer_notify';
            }
            $id_lang = (int)$customer->id_lang;
            $token = CoreCookieCryptor::encryptLink($customer->id, $customer->email, $request);
            $link_confirm = Context::getContext()->link->getModuleLink(self::getModule()->name, 'confirm', ['token' => $token, 'meta_type' => $request->metatype], true, $id_lang, $customer->id_shop);
        }

        $email_template_path = _PS_MODULE_DIR_ .'corecookie/mails/';
        $iso_code_lang = Language::getIsoById($id_lang);
        $title_path = $email_template_path.$iso_code_lang.'/'.$title.'.txt';
        $title = Tools::file_get_contents($title_path);

        $address = '';
        $shopCountry = trim(Configuration::get('PS_SHOP_COUNTRY'));
        $shopCity = trim(Configuration::get('PS_SHOP_CITY'));
        $shopCode = trim(Configuration::get('PS_SHOP_CODE'));
        $shopAdd1 = trim(Configuration::get('PS_SHOP_ADDR1'));
        $shopAdd2 = trim(Configuration::get('PS_SHOP_ADDR2'));
        if($shopAdd1 != ''){
            $address .= $shopAdd1.', ';
        }
        if($shopAdd2 != ''){
            $address .= $shopAdd2.', ';
        }
        if($shopCode != ''){
            $address .= $shopCode.' ';
        }
        if($shopCity != ''){
            $address .= $shopCity.', ';
        }
        if($shopCountry != ''){
            $address .= $shopCountry;
        }

        $customer_variable_email = self::getEmailCustomerVariable($request->metatype, $id_lang);
        $metatype = self::translateMetaType($request->metatype, $id_lang);

        $params = [
            '{customer_first_name}' => $customer->firstname,
            '{customer_last_name}' => $customer->lastname,
            '{customer_email}' => $customer->email,
            '{shop_address}' => $address,
            '{confirm_url}' => $link_confirm,
            '{customer_content_email}' => $customer_variable_email,
            '{meta_type_request}' => $metatype,
            '{customer_message}' => $request->content,
        ];

        return Mail::send(
            $id_lang,
            $email_template_content,
            $title,
            $params,
            $email,
            null,
            null,
            null,
            null,
            null,
            $email_template_path
        );
    }

    public static function createTemplateMails($module_name)
    {
        $language = Language::getLanguages(false);
        $data_email = self::getInitContentMail($module_name);
        foreach ($language as $lang) {
            self::createFolderEmail($module_name, $lang, 'email_title_admin', $data_email['email_title_admin'], true);
            self::createFolderEmail($module_name, $lang, 'email_content_admin', $data_email['email_content_admin'], true);
            self::createFolderEmail($module_name, $lang, 'email_content_admin', $data_email['email_content_admin']);
            self::createFolderEmail($module_name, $lang, 'email_title_customer_confirm', $data_email['email_title_customer_confirm'], true);
            self::createFolderEmail($module_name, $lang, 'email_content_customer_confirm', $data_email['email_content_customer_confirm'], true);
            self::createFolderEmail($module_name, $lang, 'email_content_customer_confirm', $data_email['email_content_customer_confirm']);
            self::createFolderEmail($module_name, $lang, 'email_title_customer_notify', $data_email['email_title_customer_notify'], true);
            self::createFolderEmail($module_name, $lang, 'email_content_customer_notify', $data_email['email_content_customer_notify'], true);
            self::createFolderEmail($module_name, $lang, 'email_content_customer_notify', $data_email['email_content_customer_notify']);
        }
        return true;
    }

    public static function getInitContentMail($module_name)
    {
        $template_path = _PS_MODULE_DIR_.$module_name.'/views/templates/admin/mails/';
        return [
            'email_title_admin' => Tools::file_get_contents($template_path. 'email_title_admin.txt'),
            'email_content_admin' => Tools::file_get_contents($template_path. 'email_content_admin.html'),
            'email_title_customer_confirm' => Tools::file_get_contents($template_path. 'email_title_customer_confirm.txt'),
            'email_content_customer_confirm' => Tools::file_get_contents($template_path. 'email_content_customer_confirm.html'),
            'email_title_customer_notify' => Tools::file_get_contents($template_path. 'email_title_customer_notify.txt'),
            'email_content_customer_notify' => Tools::file_get_contents($template_path. 'email_content_customer_notify.html'),
        ];
    }

    public static function createFolderEmail($module_name, $lang, $filename, $contentEmail, $is_title = false)
    {
        $mails_dir = _PS_MODULE_DIR_ . $module_name.'/mails/';
        if (!is_dir($mails_dir . $lang['iso_code'] . '/')) {
            @mkdir($mails_dir . $lang['iso_code'] . '/', 0755);
        }
        if (!file_exists($mails_dir . $lang['iso_code'] . '/index.php')) {
            @copy(_PS_MODULE_DIR_ . $module_name .'/index.php', $mails_dir . $lang['iso_code'] . '/index.php');
        }
        if(!$is_title){
            $file = $mails_dir . $lang['iso_code'] . '/' . $filename . '.html';
            $handle = fopen($file, 'w+');
            fwrite($handle, $contentEmail);
            fclose($handle);
        }else{
            $file = $mails_dir . $lang['iso_code'] . '/' . $filename . '.txt';
            $handle = fopen($file, 'w+');
            fwrite($handle, preg_replace('/\s+\r+\n+/', "\n", strip_tags($contentEmail)));
            fclose($handle);
        }
        $dir = _PS_THEME_DIR_ . 'modules/'.$module_name;
        Tools::deleteDirectory($dir);
    }

    public static function getEmailCustomerVariable($metatype, $id_lang) {
        $setting = [
            'EMAIL_VARIABLE_GDPR_REQUEST' => self::getModule()::getConfiguration('EMAIL_VARIABLE_GDPR_REQUEST', (int)$id_lang),
            'EMAIL_VARIABLE_PERSONAL_INFORMATION' => self::getModule()::getConfiguration('EMAIL_VARIABLE_PERSONAL_INFORMATION', (int)$id_lang),
            'EMAIL_VARIABLE_REPORT_REQUEST' => self::getModule()::getConfiguration('EMAIL_VARIABLE_REPORT_REQUEST', (int)$id_lang),
            'EMAIL_VARIABLE_DELETION_REQUEST' => self::getModule()::getConfiguration('EMAIL_VARIABLE_DELETION_REQUEST', (int)$id_lang),
            'EMAIL_VARIABLE_CCPA_REQUEST' => self::getModule()::getConfiguration('EMAIL_VARIABLE_CCPA_REQUEST', (int)$id_lang),
            'EMAIL_VARIABLE_DO_NOT_SELL_REQUEST' => self::getModule()::getConfiguration('EMAIL_VARIABLE_DO_NOT_SELL_REQUEST', (int)$id_lang),
            'EMAIL_VARIABLE_APPI_REQUEST' => self::getModule()::getConfiguration('EMAIL_VARIABLE_APPI_REQUEST', (int)$id_lang),
            'EMAIL_VARIABLE_PIPEDA_REQUEST' => self::getModule()::getConfiguration('EMAIL_VARIABLE_PIPEDA_REQUEST', (int)$id_lang),
        ];
        $data = '';
        switch ($metatype) {
            case CoreCookieConsentLog::$METATYPE_GDPR_REQUEST :
                $data = $setting['EMAIL_VARIABLE_GDPR_REQUEST'];
                break;
            case CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION :
                $data = $setting['EMAIL_VARIABLE_PERSONAL_INFORMATION'];
                break;
            case CoreCookieConsentLog::$METATYPE_REPORT_REQUEST :
                $data = $setting['EMAIL_VARIABLE_REPORT_REQUEST'];
                break;
            case CoreCookieConsentLog::$METATYPE_DELETION_REQUEST :
                $data = $setting['EMAIL_VARIABLE_DELETION_REQUEST'];
                break;
            case CoreCookieConsentLog::$METATYPE_CCPA_REQUEST :
                $data = $setting['EMAIL_VARIABLE_CCPA_REQUEST'];
                break;
            case CoreCookieConsentLog::$METATYPE_DO_NOT_SELL_REQUEST :
                $data = $setting['EMAIL_VARIABLE_DO_NOT_SELL_REQUEST'];
                break;
            case CoreCookieConsentLog::$METATYPE_APPI_REQUEST :
                $data = $setting['EMAIL_VARIABLE_APPI_REQUEST'];
                break;
            case CoreCookieConsentLog::$METATYPE_PIPEDA_REQUEST :
                $data = $setting['EMAIL_VARIABLE_PIPEDA_REQUEST'];
                break;
        }
        return $data;
    }

    public static function translateMetaType($metatype, $id_lang)
    {
        $data = '';
        switch ($metatype) {
            case CoreCookieConsentLog::$METATYPE_GDPR_REQUEST :
                $data = self::getModule()->l('GDPR Request');
                break;
            case CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION :
                $data = self::getModule()->l('Personal Information');
                break;
            case CoreCookieConsentLog::$METATYPE_REPORT_REQUEST :
                $data = self::getModule()->l('Report Request');
                break;
            case CoreCookieConsentLog::$METATYPE_DELETION_REQUEST :
                $data = self::getModule()->l('Deletion Request');
                break;
            case CoreCookieConsentLog::$METATYPE_CCPA_REQUEST :
                $data = self::getModule()->l('CCPA Request');
                break;
            case CoreCookieConsentLog::$METATYPE_DO_NOT_SELL_REQUEST :
                $data = self::getModule()->l('Do Not Sell Request');
                break;
            case CoreCookieConsentLog::$METATYPE_APPI_REQUEST :
                $data = self::getModule()->l('APPI Request');
                break;
            case CoreCookieConsentLog::$METATYPE_PIPEDA_REQUEST :
                $data = self::getModule()->l('PIPEDA Request');
                break;
        }
        return $data;
    }

}