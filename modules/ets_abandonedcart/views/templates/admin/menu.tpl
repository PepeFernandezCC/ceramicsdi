{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}

<a class="ets_abancart_menu_title ets_abancart_title menu_{$menu.icon|escape:'html':'UTF-8'}" href="{$link->getAdminLink($slugTab|cat:$menu.class, true)|escape:'html':'UTF-8'}" data-slug="{$slugTab nofilter}" data-class="{$menu.class|escape:'html':'UTF-8'}">
	<img class="ets_abancart_icon {$menu.icon|escape:'html':'UTF-8'}" src="{$path|escape:'quotes':'UTF-8'}views/img/origin/{$menu.icon|escape:'html':'UTF-8'}.png" alt="{$menu.label|escape:'html':'UTF-8'}">
	<span class="ets_abancart_submenu_content">
		<span>{$menu.label|escape:'html':'UTF-8'}</span>
		{if isset($menu.desc) && $menu.desc}<span class="ets_abancart_help_block">{$menu.desc|escape:'html':'UTF-8'}</span>{/if}
		{if !empty($menu.sub_menus)}
		<span class="ets_abancart_menu_li_arrow"></span>
		{/if}
	</span>
</a>