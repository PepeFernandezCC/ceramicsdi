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
{foreach $languages as $lang}
    <a class="ets-ac-desc-link-lead {if $idLangDefault != $lang['id_lang'] || !$formItem}hide{/if}"  target="_blank" data-lang="{$lang['id_lang']|escape:'html':'UTF-8'}" href="{$baseLinkLeadForm|escape:'quotes':'UTF-8'}{$lang['iso_code']|escape:'html':'UTF-8'}/lead/{if $formItem}{$formItem->alias[$lang['id_lang']]|escape:'quotes':'UTF-8'}{/if}">{$baseLinkLeadForm|escape:'quotes':'UTF-8'}{$lang['iso_code']|escape:'html':'UTF-8'}/lead/<span class="alias-link">{if $formItem}{$formItem->alias[$lang['id_lang']]|escape:'quotes':'UTF-8'}{/if}</span></a>
{/foreach}