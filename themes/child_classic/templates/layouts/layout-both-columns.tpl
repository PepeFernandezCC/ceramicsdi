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



{include file='_partials/helpers.tpl'}



<!doctype html>

<html lang="{$language.locale}">



<head>

    {block name='head'}

        {include file='_partials/head.tpl'}

    {/block}

</head>



<body id="{$page.page_name}" class="{$page.body_classes|classnames}">



{block name='hook_after_body_opening_tag'}

    {hook h='displayAfterBodyOpeningTag'}

{/block}



<main>

    {block name='product_activation'}

        {include file='catalog/_partials/product-activation.tpl'}

    {/block}



    <header id="header" class="mb-menu">

        {block name='header'}

            {include file='_partials/header.tpl'}

        {/block}

    </header>



    <section id="wrapper">

        {block name='notifications'}

            {include file='_partials/notifications.tpl'}

        {/block}
        {*
            {if $page.page_name == 'category'}

                {include file='_partials/info-category.tpl'}
                
            {/if}
        *}



        {hook h="displayWrapperTop"}

        {* PLANATEC *}

        {if $page.page_name == 'index'}

            <div class="main-title-home">

                {$cmsTitle = CMS::getCMSContent(19, $language.id)}

                {$cmsTitle['content'] nofilter}

            </div>

        {/if}

        {* END PLANATEC *}

        <div class="{if isset($cms) && $cms.id != 4 && $cms.id != 10 && $cms.id != 12}container container-single-blog{else}container-fluid{/if}">

            {*

            - PLANATEC: eliminar breadcrumbs



            {block name='breadcrumb'}

                {include file='_partials/breadcrumb.tpl'}

            {/block}

            *}



            {block name="left_column"}

                <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">

                    {if $page.page_name == 'product'}

                        {hook h='displayLeftColumnProduct'}

                    {else}

                        {hook h="displayLeftColumn"}

                    {/if}

                </div>

            {/block}



            {block name="content_wrapper"}

                <div id="content-wrapper" class="js-content-wrapper left-column right-column col-sm-4 col-md-6">

                    {hook h="displayContentWrapperTop"}

                    {block name="content"}

                        <p>Hello world! This is HTML5 Boilerplate.</p>

                    {/block}

                    {hook h="displayContentWrapperBottom"}

                </div>

            {/block}



            {block name="right_column"}

                <div id="right-column" class="col-xs-12 col-sm-4 col-md-3">

                    {if $page.page_name == 'product'}

                        {hook h='displayRightColumnProduct'}

                    {else}

                        {hook h="displayRightColumn"}

                    {/if}

                </div>

            {/block}

        </div>

        {hook h="displayWrapperBottom"}



        {* PLANATEC *}

        {if $page.page_name == 'index'}

            <div class="main-text-home">

                {$cmsTitle = CMS::getCMSContent(20, $language.id)}

                {$cmsTitle['content'] nofilter}

            </div>

            <div class="main-slider-home" >

                {if $language.id == 1}

                    [creativeslider id="6"]

                    [creativeslider id="52"]

                {elseif $language.id == 2}

                    [creativeslider id="53"]

                    [creativeslider id="58"]

                {elseif $language.id == 3}

                    [creativeslider id="54"]

                    [creativeslider id="59"]

                {* SLIDERS TRUST EN IDIOMAS NO DISPONIBLES POR EL MOMENTO

                {elseif $language.id == 4}

                    [creativeslider id="55"]

                    [creativeslider id="60"]

                {elseif $language.id == 5}

                    [creativeslider id="56"]

                    [creativeslider id="61"]

                {elseif $language.id == 6}

                    [creativeslider id="57"]

                    [creativeslider id="62"]
                
                *}

                {/if}


            </div>

        {/if}

        {* END PLANATEC *}

    </section>



    <footer id="footer" class="js-footer">

        {block name="footer"}

            {include file="_partials/footer.tpl"}

        {/block}

    </footer>



</main>



{block name='javascript_bottom'}

    {include file="_partials/javascript.tpl" javascript=$javascript.bottom}

{/block}



{block name='hook_before_body_closing_tag'}

    {hook h='displayBeforeBodyClosingTag'}

{/block}



<div id="custom-lightbox">

    <img src="" loading="lazy">

</div>



{literal}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

{/literal}

</body>



</html>

