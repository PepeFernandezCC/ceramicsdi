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

	final class CookieStatic {
		private static $config = [];

		public static function setConfig($config) {
			static::$config = $config;
		}

		public static function buildStatic($context, $paths, $mode) {
			self::buildJS($context, $paths['js'], $mode);
			self::buildCSS($context, $paths['css'], $mode);
		}

		private static function buildJS($context, $path, $mode) {
			if (self::$config['_CORE_ENV_']) {
				static::$config['_JS_CLASS_NAME']::display($path, $mode);
			}
			return $context->controller->addJS($path);
		}

		private static function buildCSS($context, $path, $mode) {
			if (self::$config['_CORE_ENV_']) {
				static::$config['_CSS_CLASS_NAME']::display($path, $mode);
			}
			return $context->controller->addCss($path);
		}
	}
?>
