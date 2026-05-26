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
{extends file='page.tpl'}
{if $cms.id != 12}
    {block name='page_title'}
        
        {$cms.meta_title}

    {/block}
{/if}

{block name='page_content_container'}
    <section id="content" class="page-content page-cms page-cms-{$cms.id}">

        {block name='cms_content'}
            {if $cms.id == 10}
                {$pinterestLinks = "</p>"|explode:$cms.content}
                <div>
                    {$title = ''}
                    {foreach from=$pinterestLinks item="pinterestLink"}
                        {$finalLink = ($pinterestLink|replace:'<p>':'')|trim}

                        {if $finalLink != ''}
                            {if filter_var($finalLink, FILTER_VALIDATE_URL)}
                                <div class="col-md-6 col-xs-12 embed-pinterest">
                                    <h5>{$title}</h5>
                                    <a data-pin-do="embedBoard" data-pin-lang="es" data-pin-board-width="1000"
                                       data-pin-scale-height="500"
                                       data-pin-scale-width="80" href="{$finalLink}"></a>
                                </div>
                            {else}
                                {$title = $finalLink}
                            {/if}
                        {/if}
                    {/foreach}
                </div>
                <script async defer src="//assets.pinterest.com/js/pinit.js"></script>
            {elseif $cms.id == 12}
                <section id="profesionales">
                    <h1 style="text-align: center;">{l s='Tiles for professionals' d='Shop.Theme.Global'}</h1>
                    <p class="page-proffessional-description">
                        {l s='proffessional page description' d='Shop.Theme.Global'}
                    </p>

                    <div class="col-md-6 col-xs-12 p-left">
                        <div class="row">
                            {$cms.content nofilter}
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 p-right">
                   
                        <div class="row">
                            <div class="col-xs-12 title">
                                <h2 class="h3" style="padding:  0; font-weight: 600; font-size: 18px; ">
                                    {l s='Register like a Professional' d='Shop.Theme.Global'}
                                </h2>
                            </div>
                            {if $customer.is_logged}

                                {assign var="url_iframe_odoo" value="https://ceramic.sidoo.es/formulario-web-profesionales"}

                                {if $language.id == 2}
                                    {assign var="url_iframe_odoo" value="https://ceramic.sidoo.es/formulario-web-profesionales-fr"}
                                {/if}
                                {if $language.id == 3}
                                    {assign var="url_iframe_odoo" value="https://ceramic.sidoo.es/formulario-web-profesionales-en"}
                                {/if}
                                {if $language.id == 4}
                                    {assign var="url_iframe_odoo" value="https://ceramic.sidoo.es/formulario-web-profesionales-de"}
                                {/if}
                                {if $language.id == 5}
                                    {assign var="url_iframe_odoo" value="https://ceramic.sidoo.es/formulario-web-profesionales-pt"}
                                {/if}
                                {if $language.id == 6}
                                    {assign var="url_iframe_odoo" value="https://ceramic.sidoo.es/formulario-web-profesionales-nl"}
                                {/if}


                                <section class="col-xs-12 contact-form">
                                    <iframe 
                                        src= "{$url_iframe_odoo}"
                                        class="professionals-iframe-form"
                                        frameborder="0" 
                                        scrolling="no" 
                                        >
                                    </iframe>

                                </section>

                            {else}
                                <div class="col-xs-12 flex-center">
                                    <div class="contact-form-unlogged">
                                        <div>
                                            {l s='To access our professional form you must have an account and be logged in.' d='Shop.Theme.Global'}
                                        </div>
                                        <div class="professional-loggin">
                                            <a href="{$urls.pages.my_account}" rel="nofollow">{l s='Click here to log in or create an account.' d='Shop.Theme.Global'}</a>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>

                        {if $customer.is_logged}
                            <div id="catalogDownloads" style="margin-top: 0; padding-bottom:5%">
                                <div style="padding-bottom: 10px"><a style="color: black" href="{$urls.base_url}catalog/CC_CATALOGO-2025.pdf" rel="nofollow" download="CC_CATALOGO-2025.pdf">{l s='DOWNLOAD CATALOG PDF' d='Shop.Theme.Catalog'} <i class="fas fa-file" style="padding:2px"></i></a></div>
                                <div><a style="color: black"href="https://ceramicconnection.com/pricelist.php" target="_BLANK" rel="nofollow">{l s='VIEW PRICE LIST' d='Shop.Theme.Catalog'}</a></div>                         
                            </div>
                        {/if}
                    </div>
                </section>
            {else}
                {$cms.content nofilter}
            {/if}
        {/block}

        {if $cms.id == 9}
            <article id="faq">
                {foreach from=$faq item=$question}
                    <button class="accordion">
                        {$question.title}
                    </button>
                    <section style="display: none;">
                        {$question.answer nofilter}
                    </section>
                {/foreach}
            </article>
        {/if}

        {block name='hook_cms_dispute_information'}
            {hook h='displayCMSDisputeInformation'}
        {/block}

        {block name='hook_cms_print_button'}
            {hook h='displayCMSPrintButton'}
        {/block}
    </section>
{/block}
