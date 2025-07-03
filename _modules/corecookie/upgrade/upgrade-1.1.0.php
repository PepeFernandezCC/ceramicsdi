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
	
	if (!defined('_PS_VERSION_'))
		exit;
	
	function upgrade_module_1_1_0($module) {
		$prefix_config = 'COOKIE';
		$prefix_database = 'at_cookie';
		$id_shop = (int) Context::getContext()->shop->id;
		$id_shop_group = (int) Shop::getGroupFromShop($id_shop, true);
		
		$settings = json_decode(Configuration::get($prefix_config . '_' . 'GLOBAL_SETTINGS', null, $id_shop_group, $id_shop), true);
		$settings['reload_page_after_accept_cookies'] = 0;
		$settings['force_choice_accepting_cookies'] = 0;
		$settings['redirect_if_refuse_cookie'] = 0;
		$settings['reject_redirect_url'] = 'https://www.google.com/';
		
		$sql = "DELETE FROM "._DB_PREFIX_."{$prefix_database}_cookies WHERE 1";
		$module->name::$Initialization::installTable();
		
		$cookie_bar_settings = json_decode(Configuration::get($prefix_config . '_' . 'COOKIE_BAR', null, $id_shop_group, $id_shop), true);
		$cookie_bar_settings['design']['show_reopen_cookie_banner_btn'] = 0;
		$cookie_bar_settings['design']['hide_reopen_btn_when_accepted_cookies'] = 0;
		$cookie_bar_settings['design']['use_custom_reopen_btn'] = 0;
		$cookie_bar_settings['design']['reopen_btn_image'] = '';
		$cookie_bar_settings['design']['reopen_btn_padding_x'] = 12;
		$cookie_bar_settings['design']['reopen_btn_padding_y'] = 14;
		$cookie_bar_settings['design']['reopen_btn_font_size'] = 14;
		$cookie_bar_settings['design']['reopen_btn_border_radius'] = 1000;
		$cookie_bar_settings['design']['reopen_btn_text_color'] = '#000';
		$cookie_bar_settings['design']['reopen_btn_background'] = '#fff';
		$cookie_bar_settings['design']['reopen_btn_use_transparent'] = 0;
		$cookie_bar_settings['design']['reopen_btn_active_box_shadow'] = 1;
		$cookie_bar_settings['design']['reopen_btn_position'] = 'left';
		$cookie_bar_settings['design']['reopen_btn_offset_x'] = 0;
		$cookie_bar_settings['design']['reopen_btn_offset_y'] = 0;
		$cookie_bar_settings['design']['reopen_btn_use_image'] = 0;
		
		$res = Configuration::updateValue($prefix_config . '_' . 'GLOBAL_SETTINGS', json_encode($settings), false, $id_shop_group, $id_shop);
		$res &= Configuration::updateValue($prefix_config . '_' . 'COOKIE_BAR', json_encode($cookie_bar_settings), false, $id_shop_group, $id_shop);
		$res &= Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
		$res &= CoreCookieCookie::initDataCookies(Context::getContext());
		
		$languages = Language::getLanguages(false);
		$label_reopen_btn = [];
		foreach ($languages as $lang) {
			$label_reopen_btn[$lang['id_lang']] = 'RGPD';
		}
		$res &= Configuration::updateValue($prefix_config . '_' . 'LABEL_REOPEN_BTN', $label_reopen_btn, false, $id_shop_group, $id_shop);
		
		return $res;
	}
?>