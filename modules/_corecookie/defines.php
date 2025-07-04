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

	class CookieDefine
	{
		private static $config = [];

		public static function setConfig($config)
		{
			self::$config = $config;
		}

		public static function listHook()
		{
			return array(
				'actionAdminControllerSetMedia',
				'displayBackOfficeHeader',
				'DisplayHeader',
				'actionOutputHTMLBefore',
                'displayCustomerAccount'
			);
		}

		public static function listTab()
		{
			return array(
				array(
					'class' => 'AdminCookie',
					'name' => self::l('GDPR/CCPA + Cookie Management'),
					'active' => 1,
					'icon' => '',
					'sub' => array(
						array(
							'class' => 'AdminCookiesetting',
							'name' => self::l('Global Settings'),
							'active' => 1,
							'icon' => '',
						),
                        array(
                            'class' => 'AdminCookiebar',
                            'name' => self::l('Cookie Bar'),
                            'active' => 1,
                            'icon' => '',
                        ),
                        array(
                            'class' => 'AdminCookiemanagement',
                            'name' => self::l('Cookie Management'),
                            'active' => 1,
                            'icon' => '',
                        ),
                        array(
                            'class' => 'AdminCookierecord',
                            'name' => self::l('Records'),
                            'active' => 1,
                            'icon' => '',
                        ),
                        array(
                            'class' => 'AdminCookiefaq',
                            'name' => self::l('FAQs'),
                            'active' => 1,
                            'icon' => '',
                        ),
					),
				),
			);
		}

		public static function getConfigs()
		{
            $languages = Language::getLanguages(false);
            $global_settings = [
                'show_cookie_bar' => 0,
                'sandbox_mode' => 0,
                'whitelist_ips' => [],
                'enable_specific_regions' => 'all_countries',
                'use_google_captcha' => 0,
                'captcha_site_key' => '',
                'captcha_secret_key' => '',
                'receive_email_when_customer_requested' => true,
                'add_custom_email_to_receive_notifications' => '',
                'enable_expiration_request' => 0,
                'access_to_personal_data_expired' => 5,
                'removal_of_personal_data_expired' => 2,
            ];

            $cookie_bar = [
                'initial_state_cookie_bar' => ['block_analytics_cookies', 'block_marketing_cookies'],
                'regard_initial_state_when_accept_cookie_bar' => 0,
                'references_display_cookie_categories' => 0,
                'states_when_close_cookie_bar' => ['block_analytics_cookies', 'block_marketing_cookies', 'block_functionality_cookies'],
                'design' => [
                    'theme' => 'basic',
                    'desktop_display_type' => 'full_bar',
                    'desktop_display_position' => 'full_bar_bottom',
                    'mobile_display_position' => 'bottom',
                    'opacity' => 100,
                    'font_size' => 14,
                    'show_icon_desktop' => true,
                    'show_icon_mobile' => true,
                    'icon_color' => '#FFFFFF',
                    'show_reject_btn' => true,
                    'show_close_btn' => true,
                    'background' => '#D16E79',
                    'text_color' => '#FFFFFF',
                    'button_text_color' => '#D16E79',
                    'button_background' => '#FFFFFF',
                    'checkbox_background' => '#FFFFFF',
                    'checkbox_ticked' => '#9D0516',
                    'custom_css' => '',
                    'cookie_icon' => 'module/cookie.svg'
                ]
            ];

            $content_GDPR_page = [];
            $content_CCPA_page = [];
            $content_APPI_page = [];
            $content_PIPEDA_page = [];
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

			$data_email = CoreCookieMail::getInitContentMail(self::$config['_CORE_NAME_MODULE_']);

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

			$identity_link = Context::getContext()->link->getPageLink('identity');
			$order_history_link = Context::getContext()->link->getPageLink('history');
			$privacy_link = CoreCookieCookie::getPrivacyCmsLink();

            $link_personal = Context::getContext()->link->getModuleLink(self::$config['_CORE_NAME_MODULE_'], 'personalData', []);
            $appi_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_GDPR,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_GDPR_REQUEST
            ]));
            $gdpr_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_APPI_REQUEST
            ]));
            $ccpa_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_CCPA_REQUEST
            ]));
            $pipeda_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_PIPEDA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PIPEDA_REQUEST
            ]));

            $appi_personal_information = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION
            ]));
            $gdpr_personal_information = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_GDPR,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION
            ]));
            $ccpa_personal_information = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION
            ]));
            $pipeda_personal_information = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_PIPEDA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_PERSONAL_INFORMATION
            ]));

            $appi_request_report = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_REPORT_REQUEST
            ]));
            $gdpr_request_report = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_GDPR,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_REPORT_REQUEST
            ]));
            $ccpa_request_report = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_REPORT_REQUEST
            ]));
            $pipeda_request_report = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_PIPEDA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_REPORT_REQUEST
            ]));

            $appi_do_not_sell = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DO_NOT_SELL_REQUEST
            ]));
            $ccpa_do_not_sell = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DO_NOT_SELL_REQUEST
            ]));
            $appi_deletion_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_APPI,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DELETION_REQUEST
            ]));
            $gdpr_deletion_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_GDPR,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DELETION_REQUEST
            ]));
            $ccpa_deletion_request = $link_personal . "?request=".base64_encode(json_encode([
                'source_page' => CoreCookieConsentLog::$SOURCE_OF_REQUEST_CCPA,
                'metatype_request' => CoreCookieConsentLog::$METATYPE_DELETION_REQUEST
            ]));

            $content_GDPR_page_template = Tools::file_get_contents(_PS_MODULE_DIR_ . self::$config['_CORE_NAME_MODULE_'] . '/content.gdpr.page.txt');
            $content_CCPA_page_template = Tools::file_get_contents(_PS_MODULE_DIR_ . self::$config['_CORE_NAME_MODULE_'] . '/content.ccpa.page.txt');
            $content_APPI_page_template = Tools::file_get_contents(_PS_MODULE_DIR_ . self::$config['_CORE_NAME_MODULE_'] . '/content.appi.page.txt');
            $content_PIPEDA_page_template = Tools::file_get_contents(_PS_MODULE_DIR_ . self::$config['_CORE_NAME_MODULE_'] . '/content.pipeda.page.txt');

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
                $content_GDPR_page[$lang['id_lang']] = $content_GDPR_page_template;
                $content_CCPA_page[$lang['id_lang']] = $content_CCPA_page_template;
                $content_APPI_page[$lang['id_lang']] = $content_APPI_page_template;
                $content_PIPEDA_page[$lang['id_lang']] = $content_PIPEDA_page_template;
                $cookie_consent_text[$lang['id_lang']] = 'This website uses cookies to ensure you get the best experience on our website.';
                $privacy_policy_text[$lang['id_lang']] = 'Privacy Policy';
                $preferences_button_text[$lang['id_lang']] = 'Preferences';
                $reject_button_text[$lang['id_lang']] = 'Reject';
                $accept_button_text[$lang['id_lang']] = 'Accept';
                $privacy_policy_link[$lang['id_lang']] = '';

                $preferences_popup_header_title[$lang['id_lang']] = 'Choose Type of Cookies You Accept Using';
                $preferences_popup_header_desc[$lang['id_lang']] = '';
                $strict_cookie_title[$lang['id_lang']] = 'Strictly Required Cookies';
                $strict_cookie_desc[$lang['id_lang']] = 'These cookies are required for the website to run and cannot be switched off. Such cookies are only set in response to actions made by you such as language, currency, login session, privacy preferences. You can set your browser to block these cookies but this might affect the way our site is working.';
                $analytics_cookie_title[$lang['id_lang']] = 'Analytics and Statistics';
                $analytics_cookie_desc[$lang['id_lang']] = 'These cookies allow us to measure visitors traffic and see traffic sources by collecting information in data sets. They also help us understand which products and actions are more popular than others.';
                $marketing_cookie_title[$lang['id_lang']] = 'Marketing and Retargeting';
                $marketing_cookie_desc[$lang['id_lang']] = 'These cookies are usually set by our marketing and advertising partners. They may be used by them to build a profile of your interest and later show you relevant ads. If you do not allow these cookies you will not experience targeted ads for your interests.';
                $functional_cookie_title[$lang['id_lang']] = 'Functional Cookies';
                $functional_cookie_desc[$lang['id_lang']] = 'These cookies enable our website to offer additional functions and personal settings. They can be set by us or by third-party service providers that we have placed on our pages. If you do not allow these cookies, these or some of these services may not work properly.';
                $accept_selected_button[$lang['id_lang']] = 'Save my choice';
                $accept_all_selected_button[$lang['id_lang']] = 'Accept All';

				$email_title_admin[$lang['id_lang']] = $data_email['email_title_admin'];
				$email_title_customer_confirm[$lang['id_lang']] = $data_email['email_title_customer_confirm'];
				$email_title_customer_notify[$lang['id_lang']] = $data_email['email_title_customer_notify'];
				$email_content_admin[$lang['id_lang']] = $data_email['email_content_admin'];
				$email_content_customer_confirm[$lang['id_lang']] = $data_email['email_content_customer_confirm'];
				$email_content_customer_notify[$lang['id_lang']] = $data_email['email_content_customer_notify'];

				$email_variable_gdpr_request[$lang['id_lang']] = 'With GDPR Request, after you confirm this request via email. Your request will be sent to the administrator to process the request.';
				$email_variable_personal_information[$lang['id_lang']] = 'With the request to download your personal information, after you confirm, we will proceed to send your information via email.';
				$email_variable_report_request[$lang['id_lang']] = 'With Report Request, after you confirm this request via email. Your request will be sent to the administrator to process the request.';
				$email_variable_deletion_request[$lang['id_lang']] = 'With this request to delete your account, when you confirm, we will proceed to delete your account. So you should consider before confirming.';
				$email_variable_ccpa_request[$lang['id_lang']] = 'With CCPA Request, after you confirm this request via email. Your request will be sent to the administrator to process the request.';
				$email_variable_do_not_sell_request[$lang['id_lang']] = 'With Do Not Sell Request, after you confirm this request via email. Your request will be sent to the administrator to process the request.';
				$email_variable_appi_request[$lang['id_lang']] = 'With APPI Request, after you confirm this request via email. Your request will be sent to the administrator to process the request.';
				$email_variable_pipeda_request[$lang['id_lang']] = 'With PIPEDA Request, after you confirm this request via email. Your request will be sent to the administrator to process the request.';

            }
            $scanner_settings = [
                'last_scanned' => time(),
                'scanned_cookies' => array_keys($_COOKIE)
            ];

			return array(
				array(
					'name' => 'PREVIEW_CONFIG_COOKIE',
					'value' => '',
					'lang' => false,
				),
				array(
					'name' => 'USE_MODULE_SINCE',
					'value' => time(),
					'lang' => false,
				),
				array(
					'name' => 'USING_MODULE_FIRST_TIME',
					'value' => 1,
					'lang' => false,
				),
                array(
                    'name' => 'GLOBAL_SETTINGS',
                    'value' => json_encode($global_settings),
                    'lang' => false,
                ),
                array(
                    'name' => 'CONTENT_GDPR_PAGE',
                    'value' => $content_GDPR_page,
                    'lang' => true,
                ),
                array(
                    'name' => 'CONTENT_CCPA_PAGE',
                    'value' => $content_CCPA_page,
                    'lang' => true,
                ),
                array(
                    'name' => 'CONTENT_APPI_PAGE',
                    'value' => $content_APPI_page,
                    'lang' => true,
                ),
                array(
                    'name' => 'CONTENT_PIPEDA_PAGE',
                    'value' => $content_PIPEDA_page,
                    'lang' => true,
                ),
                array(
                    'name' => 'COOKIE_CONSENT_TEXT',
                    'value' => $cookie_consent_text,
                    'lang' => true,
                ),
                array(
                    'name' => 'PRIVACY_POLICY_TEXT',
                    'value' => $privacy_policy_text,
                    'lang' => true,
                ),
                array(
                    'name' => 'PRIVACY_POLICY_LINK',
                    'value' => $privacy_policy_link,
                    'lang' => true,
                ),
                array(
                    'name' => 'PREFERENCES_BUTTON_TEXT',
                    'value' => $preferences_button_text,
                    'lang' => true,
                ),
                array(
                    'name' => 'REJECT_BUTTON_TEXT',
                    'value' => $reject_button_text,
                    'lang' => true,
                ),
                array(
                    'name' => 'ACCEPT_BUTTON_TEXT',
                    'value' => $accept_button_text,
                    'lang' => true,
                ),
                array(
                    'name' => 'COOKIE_BAR',
                    'value' => json_encode($cookie_bar),
                    'lang' => false,
                ),
                array(
                    'name' => 'PREFERENCES_POPUP_HEADER_TITLE',
                    'value' => $preferences_popup_header_title,
                    'lang' => true,
                ),
                array(
                    'name' => 'PREFERENCES_POPUP_HEADER_DESC',
                    'value' => $preferences_popup_header_desc,
                    'lang' => true,
                ),
                array(
                    'name' => 'STRICT_COOKIE_TITLE',
                    'value' => $strict_cookie_title,
                    'lang' => true,
                ),
                array(
                    'name' => 'STRICT_COOKIE_DESC',
                    'value' => $strict_cookie_desc,
                    'lang' => true,
                ),
                array(
                    'name' => 'ANALYTICS_COOKIE_TITLE',
                    'value' => $analytics_cookie_title,
                    'lang' => true,
                ),
                array(
                    'name' => 'ANALYTICS_COOKIE_DESC',
                    'value' => $analytics_cookie_desc,
                    'lang' => true,
                ),
                array(
                    'name' => 'MARKETING_COOKIE_TITLE',
                    'value' => $marketing_cookie_title,
                    'lang' => true,
                ),
                array(
                    'name' => 'MARKETING_COOKIE_DESC',
                    'value' => $marketing_cookie_desc,
                    'lang' => true,
                ),
                array(
                    'name' => 'FUNCTIONAL_COOKIE_TITLE',
                    'value' => $functional_cookie_title,
                    'lang' => true,
                ),
                array(
                    'name' => 'FUNCTIONAL_COOKIE_DESC',
                    'value' => $functional_cookie_desc,
                    'lang' => true,
                ),
                array(
                    'name' => 'ACCEPT_SELECTED_BUTTON',
                    'value' => $accept_selected_button,
                    'lang' => true,
                ),
                array(
                    'name' => 'ACCEPT_ALL_SELECTED_BUTTON',
                    'value' => $accept_all_selected_button,
                    'lang' => true,
                ),
                array(
                    'name' => 'SCANNER_SETTINGS',
                    'value' => json_encode($scanner_settings),
                    'lang' => false,
                ),
				array(
					'name' => 'EMAIL_TITLE_ADMIN',
					'value' => $email_title_admin,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_TITLE_CUSTOMER_CONFIRM',
					'value' => $email_title_customer_confirm,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_TITLE_CUSTOMER_NOTIFY',
					'value' => $email_title_customer_notify,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_CONTENT_ADMIN',
					'value' => $email_content_admin,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_CONTENT_CUSTOMER_CONFIRM',
					'value' => $email_content_customer_confirm,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_CONTENT_CUSTOMER_NOTIFY',
					'value' => $email_content_customer_notify,
					'lang' => true,
				),

				array(
					'name' => 'EMAIL_VARIABLE_GDPR_REQUEST',
					'value' => $email_variable_gdpr_request,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_VARIABLE_PERSONAL_INFORMATION',
					'value' => $email_variable_personal_information,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_VARIABLE_REPORT_REQUEST',
					'value' => $email_variable_report_request,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_VARIABLE_DELETION_REQUEST',
					'value' => $email_variable_deletion_request,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_VARIABLE_CCPA_REQUEST',
					'value' => $email_variable_ccpa_request,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_VARIABLE_DO_NOT_SELL_REQUEST',
					'value' => $email_variable_do_not_sell_request,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_VARIABLE_APPI_REQUEST',
					'value' => $email_variable_appi_request,
					'lang' => true,
				),
				array(
					'name' => 'EMAIL_VARIABLE_PIPEDA_REQUEST',
					'value' => $email_variable_pipeda_request,
					'lang' => true,
				),
			);
		}

		public static function getDatabase()
		{
			$list_table = array();
			$list_table[] = array(
				'name' => 'cookies',
				'col' => '
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar(255),
                    `user_id` int,
                    `content` text,
                    `keywords` text,
                    `status` varchar(32),
                    `category` varchar(63),
                    `date_add` datetime NOT NULL,
                    `since` int,
                    `last_update` int,
                    PRIMARY KEY (`id`)
			    ',
			);

            $list_table[] = array(
                'name' => 'policyAcceptances',
                'col' => '
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `email` varchar(255),
                    `customer_id` int,
                    `accepted_page` text,
                    `given_consent` text,
                    `ip_address` varchar(63),
                    `interaction` varchar(63),
                    `since` int,
                    PRIMARY KEY (`id`)
			    ',
            );

            $list_table[] = array(
                'name' => 'requests',
                'col' => '
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `customer_id` int,
                    `email` varchar(63),
                    `metatype` varchar(63),
                    `status` varchar(63),
                    `request_source` varchar(63),
                    `customer_ip_address` varchar(63),
                    `files` text,
                    `content` text,
                    `since` int,
                    `last_update` int,
                    `date_add` datetime,
                    PRIMARY KEY (`id`)
                '
            );
			return $list_table;
		}

		public static function listConfiguration()
		{
			$listField = self::getConfigs();
			foreach ($listField as &$field) {
				$field['name'] = self::$config['_CORE_PREFIX_CONFIG_'] . '_' . $field['name'];
			}
			return $listField;
		}


		public static function listDatabase()
		{
			$listDatabase = self::getDatabase();
			foreach ($listDatabase as &$data) {
				$data['name'] = self::$config['_CORE_PREFIX_DATABASE_'] . '_' . $data['name'];
			}
			return $listDatabase;
		}


		public static function l($text)
		{
			return Translate::getModuleTranslation(self::$config['_CORE_NAME_MODULE_'], $text, pathinfo(__FILE__, PATHINFO_FILENAME));
		}
	}
?>
