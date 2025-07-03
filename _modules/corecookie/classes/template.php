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

	final class CookieTemplate {
		public static $internal_js="";
		private static $flushed_text="";
		private static $code = [];
		private static $js_reset_function = 'reset_page';
		private static $js_update_function = 'update_page';
		private static $config = [];
		private static $query_data = [];

		public static function setConfig($config) {
			static::$config = $config;
		}

		public static function loadHTML($name_file, $mode, $content) {
			$file_cache = _PS_MODULE_DIR_.static::$config['_CORE_NAME_MODULE_']."/views/templates/tcache/".$mode."/".md5($name_file);
			if (!static::$config['_CORE_ENV_'] && file_exists($file_cache)) {
				$html = self::$flushed_text = Tools::file_get_contents($file_cache);
				static::$internal_js .= Tools::file_get_contents($file_cache.".cch");
				return $html;
			}
			return static::$config['_APTemplate_CLASS_NAME']::parseHTML($name_file, $mode, $content);
		}

		public static function release($html = '') {
			self::initQueryData();
			return $html . static::makeInternalJavascript();
		}

		public static function ajaxRelease() {
			self::initQueryData();

			$tpl = _PS_MODULE_DIR_ . self::$config['_CORE_NAME_MODULE_'] . '/views/templates/admin/ajax.release.tpl';
			$params = [
				'query_data' => static::$query_data,
				'internal_js' => static::$internal_js,
			];
			Context::getContext()->smarty->assign($params);
			$inpage_js = Context::getContext()->smarty->fetch($tpl);

			self::extra("_init", self::$js_reset_function);
			self::extra("_update", self::$js_update_function);
			self::extra("_html", self::getText());
			self::extra("_inpage_js", $inpage_js);
			self::extra("_page_data", static::$config['_CLIENT_CLASS_NAME']::releaseAppData());
			return json_encode(self::$code);
		}

		public static function extra($key, $value) {
			return self::$code[$key] = $value;
		}

		public static function code($status = 1) {
			return self::$code['code'] = $status;
		}
		
		public static function error($message, $exit = false) {
			self::code(0);
			self::message($message);
			if ($exit) {
				die(self::ajaxRelease());
			}
		}
		
		public static function success($message) {
			self::code(1);
			self::message($message);
		}

		public static function message($message) {
			return self::$code['message'] = $message;
		}

		public static function flush($text){
			self::$flushed_text.=$text;
		}

		public static function paginated($number_per_page = 30) {
			$page = Tools::getValue('page', 1);
			$offset = ($page - 1) * $number_per_page;

			return " LIMIT {$offset}, {$number_per_page} ";
		}

		private static function makeInternalJavascript() {
			$tpl = _PS_MODULE_DIR_ . self::$config['_CORE_NAME_MODULE_'] . '/views/templates/admin/make.internal.javascript.tpl';
			$params = [
				'query_data' => static::$query_data,
				'client_data' => static::$config['_CLIENT_CLASS_NAME']::loadData(),
				'internal_js'	=> static::$internal_js

			];
			Context::getContext()->smarty->assign($params);
			return Context::getContext()->smarty->fetch($tpl);
		}

		private static function getText(){
			return self::$flushed_text;
		}

		private static function initQueryData() {
			foreach ($_GET as $key=>$val){
				if ($key=="controller" || $key=="token" || $key=="controllerUri"){
					continue;
				}
				self::$query_data[$key]= $val;
			}
			return;
		}
	}
?>
