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

	if (!defined('_PS_VERSION_')) {
		exit;
	}

	require_once(_PS_MODULE_DIR_ . 'corecookie/required.php');

	use PrestaShop\PrestaShop\Core\Localization\Locale;

	class corecookie extends Module
	{
		public static $Initialization;
		public static $config = [];
		public $version17 = true;
		private $_languages = null;
		public $_shop = null;
		public $_already_run_hook = [];
        public $id_lang;
		public $id_shop_group;
		public $id_shop;
		
		
		public function __construct()
		{
			$this->name = 'corecookie';
			$this->tab = 'front_office_features';
			$this->version = '1.1.0';
			$this->author = 'Alpha Tech';
			$this->need_instance = 0;
			$this->bootstrap = 1;
			$this->module_key = '265620be710afb95944ea9306489a0b8';
			parent::__construct();

			$this->displayName = 'AT Cookie Management PRO - GDPR/CCPA/APPI/PIPEDA';
			$this->description = "Helps your website comply with data privacy laws by providing advanced cookie management, customizable cookie bar, compliance pages, 10+ pre-designed, and more, it's the complete solution for managing cookies and data subject requests.";
			$this->ps_versions_compliancy = array('min' => '1.6.1.1', 'max' => _PS_VERSION_);

			if (version_compare(_PS_VERSION_, '1.7', '<')) {
				$this->version17 = false;
			}
			self::$config = require_once(_PS_MODULE_DIR_ . $this->name . '/config.php');
			if (!is_array(self::$config)) {
				return;
			}
			self::$Initialization = new self::$config['_INIT_CLASS_NAME'](self::$config);

			$this->configModule(self::$config);
			if (self::$config['_CORE_ENV_']) {
				require_once(self::$config['_CORE_PATH_'] . 'ap.php');
				self::$config['_AP_CLASS_NAME']::loadFramework(self::$config);
			}
			$base_link = $this->getBaseLink();
			if ($base_link != trim(Tools::file_get_contents(_PS_MODULE_DIR_ . "{$this->name}/{$this->name}.txt"))) {
				self::$config['_CLIENT_CLASS_NAME']::$data['shop'] = 0;
			}
			self::$config['_CLIENT_CLASS_NAME']::$data['base_link'] = $this->getBaseLink();
			self::$config['_CLIENT_CLASS_NAME']::$data['current_lang_id'] = $this->context->language->id;
			self::$config['_CLIENT_CLASS_NAME']::$data['lang_default_id'] = (int)Configuration::get('PS_LANG_DEFAULT');
			$this->_shop = Context::getContext()->shop;
			self::$config['_CLIENT_CLASS_NAME']::$data['shop_id'] = (int)$this->_shop->id;
			self::$config['_CLIENT_CLASS_NAME']::$data['employee'] = $this->context->employee;
            $this->id_lang = (int)Context::getContext()->language->id;
			$this->id_shop_group = (int) Shop::getGroupFromShop($this->_shop->id, true);
			$this->id_shop = (int) $this->_shop->id;
		}

		public function install()
		{
			if (Shop::isFeatureActive()) {
				Shop::setContext(Shop::CONTEXT_ALL);
			}
			if (parent::install()
				&& self::$Initialization::installHook($this)
				&& self::$Initialization::installTable()
				&& self::$Initialization::installConfiguration()
				&& self::$Initialization::installTab()
				&& self::$Initialization::installOverrides($this)
                && CoreCookieCookie::initDataCookies($this->context)
                && CoreCookieMail::createTemplateMails($this->name)
			) {
				return true;
			}

			return false;
		}

		public function uninstall()
		{
			if (parent::uninstall()
				&& self::$Initialization::uninstallHook($this)
				&& self::$Initialization::uninstallConfiguration()
				&& self::$Initialization::uninstallTable()
				&& self::$Initialization::uninstallTab()
				&& self::$Initialization::uninstallOverrides($this)
			) {

				return true;
			}

			return false;
		}

		public function getContent()
		{
			if (Tools::isSubmit('_r')) {
				$action = Tools::getValue('_a');
				if ($action == "upload") {
					$res = $this->uploadImageEditor();
				}
				if ($action == "get_platform") {
					$res = $this->getPlatForm();
				}
				if ($action == "get_video_list") {
					$res = $this->getVideoList();
				}
				if ($action == "get_app_list") {
					$res = $this->getAppList();
				}
				if ($action == 'get_faqs') {
				    $res = $this->getFaqs();
                }
				die($res);
			}
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminCookiesetting'));
		}

		public function hookDisplayBackOfficeHeader($params)
		{
			if ($this->isAdminModule()) {
				self::$config['_TRANSLATE_CLASS_NAME']::loadDictionary($this);

				Media::addJsDef([
					'Client' => (object)self::$config['_CLIENT_CLASS_NAME']::release('admin')
				]);

				return $this->display(__FILE__, 'client.tpl');
			}
		}

		public function hookActionAdminControllerSetMedia($params)
		{
			if ($this->isAdminModule()) {
                $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/admin/suneditor.min.js');
                $this->context->controller->addCss(_PS_MODULE_DIR_ . $this->name . '/views/css/admin/suneditor.min.css');
                $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/js.cookie.min.js');
                self::$config['_STATIC_CLASS_NAME']::buildStatic(
					$this->context,
					[
						'js' => _PS_MODULE_DIR_ . $this->name . '/views/js/admin/ap.js',
						'css' => _PS_MODULE_DIR_ . $this->name . '/views/css/admin/ap.css',
					],
					'admin'
				);
			}
		}

		public function hookActionOutputHTMLBefore($params)
		{
            if (isset($this->_already_run_hook['hookActionOutputHTMLBefore'])
                || !(Module::isInstalled($this->name) && Module::isEnabled($this->name))) {
                return;
            }
            $this->_already_run_hook['hookActionOutputHTMLBefore'] = true;
            if (Tools::getValue('module') == $this->name) {
				$params['html'] = self::$config['_TEMPLATE_CLASS_NAME']::release($params['html']);
			}

            $preview_decode = null;
            $ip_address = Tools::getRemoteAddr();
            $is_preview = Tools::isSubmit('preview') && base64_decode(Tools::getValue('preview')) == $ip_address;
            if ($is_preview) {
                $preview_decode = json_decode(base64_decode($this->getConfiguration('PREVIEW_CONFIG_COOKIE')), true);
            }
            if ($is_preview && $preview_decode) {
                $this->context->smarty->assign([
                    'settings' => json_encode($preview_decode),
                    'client' => json_encode([
                        'base_link' => $this->getBaseLink(),
                        'name_module' => $this->name
                    ])
                ]);
                $params['html'] .= $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/core.cookie.tpl');
            }
			
		}
		
		public function hookActionObjectLanguageAddAfter($params) {
			if (!isset($params['object']) || !$params['object']->id) {
				return;
			}
			
			$new_lang_id = $params['object']->id;
			$default_lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
			
			$content_GDPR_page = $this->getConfiguration('CONTENT_GDPR_PAGE', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$content_CCPA_page = $this->getConfiguration('CONTENT_CCPA_PAGE', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$content_APPI_page = $this->getConfiguration('CONTENT_APPI_PAGE', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$content_PIPEDA_page = $this->getConfiguration('CONTENT_PIPEDA_PAGE', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$cookie_consent_text = $this->getConfiguration('COOKIE_CONSENT_TEXT', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$privacy_policy_text = $this->getConfiguration('PRIVACY_POLICY_TEXT', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$privacy_policy_link = $this->getConfiguration('PRIVACY_POLICY_LINK', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$preferences_button_text = $this->getConfiguration('PREFERENCES_BUTTON_TEXT', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$reject_button_text = $this->getConfiguration('REJECT_BUTTON_TEXT', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$accept_button_text = $this->getConfiguration('ACCEPT_BUTTON_TEXT', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$preferences_popup_header_title = $this->getConfiguration('PREFERENCES_POPUP_HEADER_TITLE', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$preferences_popup_header_desc = $this->getConfiguration('PREFERENCES_POPUP_HEADER_DESC', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$strict_cookie_title = $this->getConfiguration('STRICT_COOKIE_TITLE', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$strict_cookie_desc = $this->getConfiguration('STRICT_COOKIE_DESC', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$analytics_cookie_title = $this->getConfiguration('ANALYTICS_COOKIE_TITLE', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$analytics_cookie_desc = $this->getConfiguration('ANALYTICS_COOKIE_DESC', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$marketing_cookie_title = $this->getConfiguration('MARKETING_COOKIE_TITLE', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$marketing_cookie_desc = $this->getConfiguration('MARKETING_COOKIE_DESC', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$functional_cookie_title = $this->getConfiguration('FUNCTIONAL_COOKIE_TITLE', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$functional_cookie_desc = $this->getConfiguration('FUNCTIONAL_COOKIE_DESC', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$accept_selected_button = $this->getConfiguration('ACCEPT_SELECTED_BUTTON', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$label_reopen_btn = $this->getConfiguration('LABEL_REOPEN_BTN', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$accept_all_selected_button = $this->getConfiguration('ACCEPT_ALL_SELECTED_BUTTON', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_title_admin = $this->getConfiguration('EMAIL_TITLE_ADMIN', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_title_customer_confirm = $this->getConfiguration('EMAIL_TITLE_CUSTOMER_CONFIRM', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_title_customer_notify = $this->getConfiguration('EMAIL_TITLE_CUSTOMER_NOTIFY', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_content_admin = $this->getConfiguration('EMAIL_CONTENT_ADMIN', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_content_customer_confirm = $this->getConfiguration('EMAIL_CONTENT_CUSTOMER_CONFIRM', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_content_customer_notify = $this->getConfiguration('EMAIL_CONTENT_CUSTOMER_NOTIFY', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_variable_gdpr_request = $this->getConfiguration('EMAIL_VARIABLE_GDPR_REQUEST', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_variable_personal_information = $this->getConfiguration('EMAIL_VARIABLE_PERSONAL_INFORMATION', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_variable_report_request = $this->getConfiguration('EMAIL_VARIABLE_REPORT_REQUEST', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_variable_deletion_request = $this->getConfiguration('EMAIL_VARIABLE_DELETION_REQUEST', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_variable_ccpa_request = $this->getConfiguration('EMAIL_VARIABLE_CCPA_REQUEST', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_variable_do_not_sell_request = $this->getConfiguration('EMAIL_VARIABLE_DO_NOT_SELL_REQUEST', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_variable_appi_request = $this->getConfiguration('EMAIL_VARIABLE_APPI_REQUEST', $default_lang_id, $this->id_shop_group, $this->id_shop);
			$email_variable_pipeda_request = $this->getConfiguration('EMAIL_VARIABLE_PIPEDA_REQUEST', $default_lang_id, $this->id_shop_group, $this->id_shop);
			
			$res = true;
			$res &= $this->setConfiguration('CONTENT_GDPR_PAGE', [$new_lang_id => $content_GDPR_page], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('CONTENT_CCPA_PAGE', [$new_lang_id => $content_CCPA_page], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('CONTENT_APPI_PAGE', [$new_lang_id => $content_APPI_page], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('CONTENT_PIPEDA_PAGE', [$new_lang_id => $content_PIPEDA_page], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('COOKIE_CONSENT_TEXT', [$new_lang_id => $cookie_consent_text], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('PRIVACY_POLICY_TEXT', [$new_lang_id => $privacy_policy_text], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('PRIVACY_POLICY_LINK', [$new_lang_id => $privacy_policy_link], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('PREFERENCES_BUTTON_TEXT', [$new_lang_id => $preferences_button_text], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('REJECT_BUTTON_TEXT', [$new_lang_id => $reject_button_text], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('ACCEPT_BUTTON_TEXT', [$new_lang_id => $accept_button_text], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('PREFERENCES_POPUP_HEADER_TITLE', [$new_lang_id => $preferences_popup_header_title], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('PREFERENCES_POPUP_HEADER_DESC', [$new_lang_id => $preferences_popup_header_desc], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('STRICT_COOKIE_TITLE', [$new_lang_id => $strict_cookie_title], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('STRICT_COOKIE_DESC', [$new_lang_id => $strict_cookie_desc], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('ANALYTICS_COOKIE_TITLE', [$new_lang_id => $analytics_cookie_title], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('ANALYTICS_COOKIE_DESC', [$new_lang_id => $analytics_cookie_desc], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('MARKETING_COOKIE_TITLE', [$new_lang_id => $marketing_cookie_title], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('MARKETING_COOKIE_DESC', [$new_lang_id => $marketing_cookie_desc], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('FUNCTIONAL_COOKIE_TITLE', [$new_lang_id => $functional_cookie_title], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('FUNCTIONAL_COOKIE_DESC', [$new_lang_id => $functional_cookie_desc], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('ACCEPT_SELECTED_BUTTON', [$new_lang_id => $accept_selected_button], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('LABEL_REOPEN_BTN', [$new_lang_id => $label_reopen_btn], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('ACCEPT_ALL_SELECTED_BUTTON', [$new_lang_id => $accept_all_selected_button], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_TITLE_ADMIN', [$new_lang_id => $email_title_admin], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_TITLE_CUSTOMER_CONFIRM', [$new_lang_id => $email_title_customer_confirm], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_TITLE_CUSTOMER_NOTIFY', [$new_lang_id => $email_title_customer_notify], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_CONTENT_ADMIN', [$new_lang_id => $email_content_admin], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_CONTENT_CUSTOMER_CONFIRM', [$new_lang_id => $email_content_customer_confirm], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_CONTENT_CUSTOMER_NOTIFY', [$new_lang_id => $email_content_customer_notify], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_VARIABLE_GDPR_REQUEST', [$new_lang_id => $email_variable_gdpr_request], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_VARIABLE_PERSONAL_INFORMATION', [$new_lang_id => $email_variable_personal_information], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_VARIABLE_REPORT_REQUEST', [$new_lang_id => $email_variable_report_request], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_VARIABLE_DELETION_REQUEST', [$new_lang_id => $email_variable_deletion_request], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_VARIABLE_CCPA_REQUEST', [$new_lang_id => $email_variable_ccpa_request], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_VARIABLE_DO_NOT_SELL_REQUEST', [$new_lang_id => $email_variable_do_not_sell_request], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_VARIABLE_APPI_REQUEST', [$new_lang_id => $email_variable_appi_request], true, $this->id_shop_group, $this->id_shop);
			$res &= $this->setConfiguration('EMAIL_VARIABLE_PIPEDA_REQUEST', [$new_lang_id => $email_variable_pipeda_request], true, $this->id_shop_group, $this->id_shop);
			
			$sql = "SELECT id FROM ".self::getPrefixTable()."cookies ";
			$result = Db::getInstance()->executeS($sql);
			if (count($result) > 0) {
				foreach ($result as $value){
					$cookie = new CoreCookieCookie($value['id']);
					if(Validate::isLoadedObject($cookie)) {
						$keywords = str_replace(' ', '', $this->purifyString($cookie->name.$cookie->category.$cookie->status.$cookie->content[$default_lang_id]));
						$sql = "INSERT INTO ".self::getPrefixTable()."cookies_lang (`id`, `id_lang`, `keywords`, `content`)
						VALUES(".(int) $value['id'].", ".(int) $new_lang_id.", '".pSQL($keywords)."', '".pSQL($cookie->content[$default_lang_id])."')";
						$res &= Db::getInstance()->execute($sql);
					}
				}
			}
			
			return $res;
		}

        public function hookDisplayCustomerAccount($params) {
            if (isset($this->_already_run_hook['hookDisplayCustomerAccount'])
                || !(Module::isInstalled($this->name) && Module::isEnabled($this->name))) {
                return;
            }
            $this->_already_run_hook['hookDisplayCustomerAccount'] = true;
            $this->context->smarty->assign([
                'module_name' => $this->name,
                'link_personal_data' => $this->context->link->getModuleLink($this->name, 'personalData', [])
            ]);
            return $this->display(__FILE__, 'display.customer.account.tpl');
        }

        public function hookDisplayHeader($params)
		{
            if (isset($this->_already_run_hook['hookDisplayHeader'])
            || !(Module::isInstalled($this->name) && Module::isEnabled($this->name))) {
                return;
            }
            $this->_already_run_hook['hookDisplayHeader'] = true;
            $preview_decode = null;
			$ip_address = Tools::getRemoteAddr();
			$is_preview = Tools::isSubmit('preview') && base64_decode(Tools::getValue('preview')) == $ip_address;
            if ($is_preview) {
	            $preview_decode = json_decode(base64_decode($this->getConfiguration('PREVIEW_CONFIG_COOKIE')), true);
            }

            if ($is_preview && $preview_decode) {
                $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/js.cookie.min.js');
                $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/front/core.cookie.js');
                $link_css = _PS_MODULE_DIR_ . $this->name . '/views/css/front/cookie.preview/core.cookie.'.time().'.css';
                @file_put_contents($link_css, CoreCookieCookie::getDynamicCss($this->context, $preview_decode));
                $this->context->controller->addCSS($link_css);
            }

            if (!$preview_decode && CoreCookieCookie::shouldShow()) {
                $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/js.cookie.min.js');
                $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/front/core.cookie.js');
                CoreCookieCookie::showCookieConsentCss($this->context);
                return  CoreCookieCookie::showCookieHeader($this->context);
            }
		}

		public function layoutAdmin($content = "")
		{
			//Make header
			$tpl = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/layout/header.core';
			$header_html = self::$config['_TEMPLATE_CLASS_NAME']::loadHTML("admin/layout/header.core", "admin", $tpl);

			// Make layout
			$tpl = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/layout.tpl';
			$this->context->smarty->assign(
				[
					'header_html' => $header_html,
					'content' => $content,
				]
			);

			return self::$config['_TEMPLATE_CLASS_NAME']::release($this->context->smarty->fetch($tpl));
		}

		public function layoutFront($controller_obj, $content = "")
		{
			//Make header
			$tpl = _PS_MODULE_DIR_ . $this->name . '/views/templates/front/layout/header.tpl';
			$header_html = self::$config['_TEMPLATE_CLASS_NAME']::loadHTML("layout/header.tpl", "front", $tpl);
			// Make layout
			$this->context->smarty->assign(
				[
					'header_html' => $header_html,
					'content' => $content,
				]
			);
			$controller_obj->setTemplate('module:' . $this->name . '/views/templates/front/layout.tpl');
		}

		public function getBaseLink()
		{
			return $this->context->link->getBaseLink();
		}

		private function configModule($config)
		{
			$config['_STATIC_CLASS_NAME']::setConfig($config);
			$config['_TEMPLATE_CLASS_NAME']::setConfig($config);
			$config['_TRANSLATE_CLASS_NAME']::setConfig($config);
			$config['_CLIENT_CLASS_NAME']::setConfig($config);
			$config['_DEFINE_CLASS_NAME']::setConfig($config);
		}

		private function isAdminModule()
		{
			$controller = Tools::getValue('controller');

			return in_array(
				$controller,
				[
					'AdminCookiebar',
                    'AdminCookiefaq',
                    'AdminCookiemanagement',
                    'AdminCookierecord',
                    'AdminCookiesetting'
				]
			);
		}

		private function preparePriceSpecifications(Context $context)
		{
			/* @var Currency */
			$currency = $context->currency;
			/* @var PriceSpecification */
			$price_specification = $context->getCurrentLocale()->getPriceSpecification($currency->iso_code);
			if (empty($price_specification)) {
				return [];
			}
			return array_merge(
				['symbol' => $price_specification->getSymbolsByNumberingSystem(Locale::NUMBERING_SYSTEM_LATIN)->toArray()],
				$price_specification->toArray()
			);
		}

		private function prepareNumberSpecifications(Context $context)
		{
			/* @var NumberSpecification */
			$number_specification = $context->getCurrentLocale()->getNumberSpecification();
			if (empty($number_specification)) {
				return [];
			}
			return array_merge(
				['symbol' => $number_specification->getSymbolsByNumberingSystem(Locale::NUMBERING_SYSTEM_LATIN)->toArray()],
				$number_specification->toArray()
			);
		}

		private function uploadImageEditor()
		{
            $this->name::$config['_TEMPLATE_CLASS_NAME']::code(0);
            $extension = Tools::strtolower(strrchr($_FILES['file']['name'], "."));
            $allowed_ext = array(".jpg", ".jpeg", ".png", ".gif", ".webp");
            if (!in_array($extension, $allowed_ext)) {
                $this->name::$config['_TEMPLATE_CLASS_NAME']::extra("errorMessage", $this->l('Image Extension is invalid'));
                return $this->name::$config['_TEMPLATE_CLASS_NAME']::ajaxRelease();
            }
            $newNameImage = md5(rand() . time()) . $extension;
            $sourceImage = $_FILES['file']['tmp_name'];
            $targetImage = _PS_MODULE_DIR_ . $this->name . '/views/img/editor/' . $newNameImage;

            if (move_uploaded_file($sourceImage, $targetImage)) {
                $link_image = $this->getBaseLink() . 'modules/' . $this->name . '/views/img/editor/' . $newNameImage;
                $this->name::$config['_TEMPLATE_CLASS_NAME']::code(1);
                $this->name::$config['_TEMPLATE_CLASS_NAME']::extra("image", $link_image);
                $this->name::$config['_TEMPLATE_CLASS_NAME']::extra("result", [
                    (object) [
                        'url' => $link_image,
                        'name' => $newNameImage,
                        'size' => $_FILES['file']['size']
                    ]
                ]);
                return $this->name::$config['_TEMPLATE_CLASS_NAME']::ajaxRelease();
            }

            $this->name::$config['_TEMPLATE_CLASS_NAME']::extra("errorMessage", $this->l('Upload image fails'));
            return $this->name::$config['_TEMPLATE_CLASS_NAME']::ajaxRelease();
		}
		
		public static function getConfiguration($key, $id_lang = null, $id_shop_group = null, $id_shop = null, $default = false)
		{
			return Configuration::get(self::$config['_CORE_PREFIX_CONFIG_'] . '_' . $key, $id_lang, $id_shop_group, $id_shop, $default);
		}
		
		public static function setConfiguration($key, $values, $html = false, $id_shop_group = null, $id_shop = null)
		{
			if(!self::$config['_CORE_MULTI_SHOP_']) {
				return Configuration::updateValue(self::$config['_CORE_PREFIX_CONFIG_'] . '_' . $key, $values, $html, $id_shop_group, $id_shop);
			}
			$shop_groups_list = array();
			$shops = Shop::getContextListShopID();
			$shop_context = Shop::getContext();
			$res = true;
			foreach ($shops as $shop_id) {
				$shop_group_id = (int)Shop::getGroupFromShop((int)$shop_id, true);
				if (!in_array($shop_group_id, $shop_groups_list)) {
					$shop_groups_list[] = (int)$shop_group_id;
				}
				$res &= Configuration::updateValue(
					self::$config['_CORE_PREFIX_CONFIG_'] . '_' . $key,
					$values,
					$html,
					(int)$shop_group_id,
					(int)$shop_id
				);
			}
			switch ($shop_context) {
				case Shop::CONTEXT_ALL:
					$res &= Configuration::updateValue(
						self::$config['_CORE_PREFIX_CONFIG_'] . '_' . $key,
						$values,
						$html
					);
					if (count($shop_groups_list)) {
						foreach ($shop_groups_list as $shop_group_id) {
							$res &= Configuration::updateValue(
								self::$config['_CORE_PREFIX_CONFIG_'] . '_' . $key,
								$values,
								$html,
								(int)$shop_group_id
							);
						}
					}
					break;
				case Shop::CONTEXT_GROUP:
					if (count($shop_groups_list)) {
						foreach ($shop_groups_list as $shop_group_id) {
							$res &= Configuration::updateValue(
								self::$config['_CORE_PREFIX_CONFIG_'] . '_' . $key,
								$values,
								$html,
								(int)$shop_group_id
							);
						}
					}
					break;
			}
			return $res;
		}

		public static function getPrefixTable($get_prefix_core = true)
		{
			if ($get_prefix_core) {
				return _DB_PREFIX_ . self::$config['_CORE_PREFIX_DATABASE_'] . '_';
			}
			return self::$config['_CORE_PREFIX_DATABASE_'] . '_';
		}


		/**
		 * @desc Get all languages
		 * @return array|null
		 */
		public function getLanguages()
		{
			if ($this->_languages) {
				return $this->_languages;
			}
			$this->_languages = Language::getLanguages(false);
			return $this->_languages;
		}

		private function getFaqs() {
            $url = 'https://attechteam.com/faqs/'.$this->name.'.php';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $data = curl_exec($ch);
            curl_close($ch);
            $decoded_data = json_decode($data, true);
            foreach ($decoded_data as &$data) {
                foreach ($data as $lang => &$lang_data) {
                    $lang_data['content'] = htmlspecialchars_decode($lang_data['content']);
                }
            }
            return json_encode([
                'code' => 1,
                'faqs' => $decoded_data
            ]);
        }

		private function getAppList()
		{
			$url = 'https://attechteam.com/applist.php';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json'
			]);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$decoded_data = json_decode($data, true);

			return json_encode([
				'success' => 1,
				'app_list' => $decoded_data
			]);
		}

		private function getPlatForm()
		{
			$url = 'https://attechteam.com/plaform.php';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json'
			]);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$decoded_data = json_decode($data, true);

			return json_encode([
				'success' => 1,
				'platform' => $decoded_data
			]);
		}

		private function getVideoList()
		{
			$url = 'https://attechteam.com/api/videolist.php';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json'
			]);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$decoded_data = json_decode($data, true);

			return json_encode([
				'success' => 1,
				'videos' => $decoded_data
			]);
		}

		/**
		 * @desc Get all module by shop
		 */
		public function getModules()
		{
			$sql = "SELECT id_module FROM `" . _DB_PREFIX_ . "module_shop` WHERE id_shop = " . $this->_shop->id;
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			$module_ids = [];
			foreach ($result as $item) {
				$module_ids[] = $item['id_module'];
			}
			$sql = "SELECT * FROM `" . _DB_PREFIX_ . "module` WHERE id_module IN (" . implode(',', $module_ids) . ") AND active = 1";
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		}

		public function saveLog($message)
		{
			$path = _PS_MODULE_DIR_ . $this->name . '/logs/errors.txt';
			$content = Tools::file_get_contents($path);
			$write_fd = @fopen($path, 'wb');
			fwrite($write_fd, $content);
			fwrite($write_fd, $message . PHP_EOL);
			fclose($write_fd);
		}

		public function middlewareDemo()
		{
			$action = Tools::getValue('_a');
			if(self::$config['_CORE_DEMO_'] && in_array($action, self::$config['_CORE_ACTION_DEMO_'])){
				self::$config['_TEMPLATE_CLASS_NAME']::error($this->l('This is demo version. You do not have permission.'));
				die(self::$config['_CORE_NAME_MODULE_']::$config['_TEMPLATE_CLASS_NAME']::ajaxRelease());
			}
		}

        /**
         * @desc core
         * @param $obj
         * @return mixed
         */
        public function resetKeyword($obj) {
            $obj->keywords="";
            return $obj;
        }

        /**
         * @desc core
         * @param $obj
         * @param $keyword
         * @return mixed
         */
        public function addKeyword($obj, $keyword){
            if ($keyword){
                $obj->keywords = $obj->keywords."".$this->purifyString($keyword);
                $obj->keywords = Tools::substr($obj->keywords, 0, 255);
            }
            $obj->keywords = str_replace(' ', '', $obj->keywords);
            return $obj;
        }

        /**
         * @desc core
         * @param $string
         */
        public function purifyString($string) {
            return strtolower(preg_replace("/[^A-Za-z0-9\s ]/", '', Tools::htmlentitiesDecodeUTF8(html_entity_decode($string))));
        }

        /**
         * @desc core
         * @param $ts
         * @return int
         */
        public function beginOfDay($ts){
            if (!$ts){
                return 0;
            }
            return $this->user_mktime(0,0,0,date("m", $ts), date("d", $ts), date("Y", $ts));
        }

        /**
         * @desc core
         * @param null $ts
         * @return false|float|int
         */
        public function beginOfYear($ts=null){
            if ($ts===null){
                $ts=time();
            }

            return $this->beginOfDay(strtotime(date("Y-01-01", $ts)));
        }

        /**
         * @desc core
         * @param null $ts
         * @return false|float|int
         */
        public function endOfYear($ts=null){
            if ($ts===null){
                $ts=time();
            }

            return $this->midnight(strtotime(date("Y-12-31", $ts)));
        }

        /**
         * @desc core
         * @param null $ts
         * @return false|float|int
         */
        public function beginOfMonth($ts=null){
            if ($ts===null){
                $ts=time();
            }

            return $this->beginOfDay(strtotime(date("Y-m-01", $ts)));
        }

        /**
         * @desc core
         * @param null $ts
         * @return mixed
         */
        public function endOfMonth($ts=null){
            if ($ts===null){
                $ts=time();
            }

            return $this->midnight(strtotime(date("Y-m-t", $ts)));
        }

        /**
         * @desc core
         * @param null $ts
         */
        public function midnight($ts){
            if (!$ts){
                return 0;
            }
            return $this->user_mktime(23,59,59,date("m", $ts), date("d", $ts), date("Y", $ts));
        }

        /**
         * @desc core
         * @param $hour
         * @param $min
         * @param $second
         * @param $month
         * @param $day
         * @param $year
         * @return false|float|int
         */
        public function user_mktime($hour, $min, $second, $month, $day, $year){
            return mktime($hour, $min, $second, $month, $day, $year);
        }

        public function saveBase64Image($path_to_save, $base64_data){
            return file_put_contents($path_to_save, base64_decode($base64_data));
        }

        public function getBase64ImageType($base64_data){
            $img_data = base64_decode($base64_data);
            $f = finfo_open();
            return finfo_buffer($f, $img_data, FILEINFO_MIME_TYPE);
        }

        /**
         * @desc Upload images simple
         * @param $input_name
         * @param $is_single
         * @param string[] $allowed_ext
         * @return array
         */
        public function uploadBasicImages($input_name, $is_single = true, $allowed_ext = array(".jpg", ".jpeg", ".png", ".webp")) {
            $new_images = [];

            if ($is_single && !is_array($_FILES[$input_name]['name'])) {
                $file_name = $_FILES[$input_name]['name'];
                $extension = Tools::strtolower(strrchr($file_name, "."));
                if (!in_array($extension, $allowed_ext)) {
                    self::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Image is invalid'));
                    exit();
                }
                $new_name_image = md5(rand().time()).$extension;
                $source_image = $_FILES[$input_name]['tmp_name'];
                $target_image = _PS_MODULE_DIR_.$this->name.'/views/img/module/'.$new_name_image;

                if (!move_uploaded_file($source_image, $target_image)) {
                    self::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Upload fails'));
                    exit();
                }
                return 'module/'.$new_name_image;
            }
            foreach ($_FILES[$input_name]['name'] as $index => $file_name) {
                $extension = Tools::strtolower(strrchr($file_name, "."));
                if (!in_array($extension, $allowed_ext)) {
                    self::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Image is invalid'));
                    exit();
                }
                $new_name_image = md5(rand().time()).$extension;
                $source_image = $_FILES[$input_name]['tmp_name'][$index];
                $target_image = _PS_MODULE_DIR_.$this->name.'/views/img/module/'.$new_name_image;

                if (!move_uploaded_file($source_image, $target_image)) {
                    self::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Upload fails'));
                    exit();
                }
                $new_images[] = 'module/'.$new_name_image;
            }
            if (count($new_images) == 0) {
                self::$config['_TEMPLATE_CLASS_NAME']::error($this->l('Cannot upload image'));
                exit();
            }
            return $new_images;
        }
	}
?>
