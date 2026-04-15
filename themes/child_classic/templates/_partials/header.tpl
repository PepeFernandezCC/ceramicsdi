{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{*custom banner*}
{if $smarty.now|date_format:"%Y-%m-%d" >= '2025-12-10' && $smarty.now|date_format:"%Y-%m-%d" <= '2026-01-05'}
    
    <div id="bf-banner" class="bf-banner">
        <div class="bf-banner__text">
            <div><i class="fa-regular fa-snowflake"></i></div>
            <div style="padding: 0 10px">{l s='Desde el 22 de diciembre hasta el 5 de enero, los plazos de entrega se verán ampliados, disculpen las molestias.' d='Shop.Theme.Catalog'}</div>
            <div><i class="fa-regular fa-snowflake"></i></div>
        </div>
        <button class="bf-banner__close" type="button" aria-label="Cerrar aviso">
            &times;
        </button>
    </div>

{/if}

{block name='header_banner'}
    <div class="header-banner">
        {hook h='displayBanner'}
    </div>
{/block}

{block name='header_nav'}
    <nav class="header-nav header-sticky">
        <div class="container-fluid">
            <div class="row">
                <div class="hidden-sm-down">
                    <div class="col-md-3 col-xs-12">
                        {hook h='displayNav1'}
                    </div>
                    <div class="col-md-9 right-nav">
                        {hook h='displayNav2'}
                    </div>
                </div>
                <div class="hidden-md-up text-sm-center mobile">
                    <div class="float-xs-left" id="menu-icon">
                        <i class="material-icons d-inline">&#xE5D2;</i>
                    </div>
                    <div class="mobile-buttons">
                        <div class="float-xs-right" id="_mobile_cart"></div>
                        <div class="float-xs-right" id="_mobile_user_info"></div>
                    </div>
                    <div class="top-logo" id="_mobile_logo">
                        {* PLANATEC *}
                        {hook h='displayNav1'}
                        {* END PLANATEC *}
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div id="search-and-language-row">
        <div class="container-fluid">
            <div class="row">
                <div id="content-row">
                    <div id="search-wrapper-doofinder" style="width: 100%; cursor: pointer;">
                        <div id="search_widget_button">
                            <i class="fa fa-search"></i>
                            <span class="placeholder-text">
                            {l s='Search here' d='Shop.Theme.Catalog'}
                        </span>
                        </div>
                    </div>

                    {*
                    <div id="_desktop_language_selector">
                        <div class="language-selector-wrapper">
                            <div class="language-selector dropdown js-dropdown">
                                <select class="link" aria-labelledby="language-selector-label">
                                    {foreach from=Language::getLanguages(true) item=lang}
                                        <option
                                            value="{if isset($is_inspirations_page) && $is_inspirations_page && isset($inspiration_language_urls[$lang.id_lang])}{$inspiration_language_urls[$lang.id_lang]|escape:'html':'UTF-8'}{else}{url entity='language' id=$lang.id_lang}{/if}"
                                            {if $lang.id_lang == $language.id} selected="selected"{/if}
                                            data-iso-code="{$lang.iso_code}">
                                            {assign var="lang_position" value=$lang.name|strpos:" ("}
                                            {$lang.name|substr:0:$lang_position}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                    *}


                    <div id="_desktop_language_selector">
                        <div class="language-selector-wrapper">
                            <div class="language-selector dropdown js-dropdown">
                                <select class="link" aria-labelledby="language-selector-label">
                                    {if isset($header_languages) && $header_languages|@count}
                                        {foreach from=$header_languages item=lang}
                                            <option
                                                value="{$lang.url|escape:'html':'UTF-8'}"
                                                {if $lang.id_lang == $language.id} selected="selected"{/if}
                                                data-iso-code="{$lang.iso_code}">
                                                {assign var="lang_position" value=$lang.name|strpos:" ("}
                                                {$lang.name|substr:0:$lang_position}
                                            </option>
                                        {/foreach}
                                    {else}
                                        {foreach from=Language::getLanguages(true) item=lang}
                                            <option
                                                value="{url entity='language' id=$lang.id_lang}"
                                                {if $lang.id_lang == $language.id} selected="selected"{/if}
                                                data-iso-code="{$lang.iso_code}">
                                                {assign var="lang_position" value=$lang.name|strpos:" ("}
                                                {$lang.name|substr:0:$lang_position}
                                            </option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </nav>
{/block}

{block name='header_top'}
    <div class="header-top">
        <div class="container">
            <div class="row">
                {*
                - PLANATEC: eliminar logo del bloque del menú

                <div class="col-md-2 hidden-sm-down" id="_desktop_logo">
                    {if $shop.logo_details}
                        {if $page.page_name == 'index'}
                            <h1>
                                {renderLogo}
                            </h1>
                        {else}
                            {renderLogo}
                        {/if}
                    {/if}
                </div>
                *}

                <div class="header-top-right {* PLANATEC col-md-10 *} col-sm-12 position-static">
                    {hook h='displayTop'}
                </div>
            </div>
            {* un antiguo headmenu? 
            <div id="mobile_top_menu_wrapper" class="hidden-md-up" style="display:none;">
                <div class="js-top-menu mobile" id="_mobile_top_menu"></div>
                <div class="js-top-menu-bottom">
                    <div id="_mobile_currency_selector"></div>
                    <div id="_mobile_language_selector"></div>
                    <div id="_mobile_contact_link"></div>
                </div>
            </div>
            *}
        </div>
    </div>
    {hook h='displayNavFullWidth'}
{/block}
