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

    class CookieClient {
        public static $data = [], $app_data, $page_data, $client_data, $cache_data;
        public static $_js='';
		private static $config = [];

		public static function setConfig($config) {
            static::$config = $config;
        }

        final public static function release($mode){
			if ($mode == 'admin') {
				static::globalDataAdmin();
			}else {
				static::globalDataFront();
			}
			static::$data['paths']	=	static::loadPaths($mode);
			static::$data['clientData']	=	[];
			static::$data['data']	=	[];
			static::$data['pageData']	=	 [];
			static::$data['cache'] =	 [];

            $client_html = "";
			if (self::$app_data){
				foreach (static::$app_data as $k=>$v){
					static::$data[$k]=$v;
				}
			}

			if (static::$page_data){
				static::$data['pageData'] =	json_encode(static::$page_data);
			}

			if (static::$cache_data){
				static::$data['cache'] =	json_encode(static::$cache_data);
			}


            return static::$data;
		}

        final public static function pageData($key, $value){
			static::$page_data[$key] = $value;
		}

		final public static function appData($key, $value){
			static::$app_data[$key] = $value;
		}

		final public static function loadData() {
			$client_html = "";
			$tpl = _PS_MODULE_DIR_ . self::$config['_CORE_NAME_MODULE_'] . '/views/templates/admin/load.data.tpl';
			$params = [
				'page_data' => static::$page_data,
				'cache_data' => static::$cache_data,
			];
			Context::getContext()->smarty->assign($params);
			$client_html = Context::getContext()->smarty->fetch($tpl);
			return $client_html;
		}

		final public static function releaseAppData() {
            $text = "";
			$tpl = _PS_MODULE_DIR_ . self::$config['_CORE_NAME_MODULE_'] . '/views/templates/admin/release.app.data.tpl';
			$params = [
				'page_data' => static::$page_data,
				'app_data' => static::$app_data,
				'cache_data' => static::$cache_data,
			];
			Context::getContext()->smarty->assign($params);
			$text = Context::getContext()->smarty->fetch($tpl);

			return $text;
        }

		final private static function globalDataAdmin() {
			static::$data['name_module'] = static::$config['_CORE_NAME_MODULE_'];
			static::$data['langs'] = Language::getLanguages(false);
            static::$data['use_module_since'] = static::$config['_CORE_NAME_MODULE_']::getConfiguration('USE_MODULE_SINCE');
			static::$data['use_module_first_time'] = static::$config['_CORE_NAME_MODULE_']::getConfiguration('USING_MODULE_FIRST_TIME');
			static::$data['link_module'] = Context::getContext()->link->getAdminLink('AdminModules', true) . '&configure=' . static::$config['_CORE_NAME_MODULE_'];
		}

		final private static function globalDataFront() {
			static::$data['name_module'] = static::$config['_CORE_NAME_MODULE_'];
		}

		final private static function loadPaths($mode) {
			$paths	=	[
				'admin'	=>	[
					'setting_path' => Context::getContext()->link->getAdminLink('AdminCookiesetting'),
					'cookie_bar_path' => Context::getContext()->link->getAdminLink('AdminCookiebar'),
					'cookie_management_path' => Context::getContext()->link->getAdminLink('AdminCookiemanagement'),
					'cookie_record_path' => Context::getContext()->link->getAdminLink('AdminCookierecord'),
					'faq_path' => Context::getContext()->link->getAdminLink('AdminCookiefaq'),
					'dash_path' => Context::getContext()->link->getAdminLink('AdminDashboard'),
				],
				'front'	=>	[]
			];

			return $paths[$mode];
		}
    }
?>
