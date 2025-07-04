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
	
	class CoreCookiePsTools {
		/**
		 * @desc Get input int
		 * @param $field
		 * @param bool $default_value
		 * @param bool $is_real
		 * @return bool|mixed|string|string[]
		 */
		public static function getInt($field, $default_value = false, $is_real = false) {
			$input = $is_real ? $field : Tools::getValue($field, $default_value);
			$input = str_replace(",","", $input);
			if (!Validate::isInt($input)) return false;
			return $input;
		}
		
		/**
		 * @desc Get input float
		 * @param $field
		 * @param bool $default_value
		 * @param bool $is_real
		 * @return bool|mixed|string|string[]
		 */
		public static function getFloat($field, $default_value = false, $is_real = false) {
			$input = $is_real ? $field : Tools::getValue($field, $default_value);
			$input = str_replace(",","", $input);
			if (!Validate::isFloat($input)) return false;
			return $input;
		}
	}
?>