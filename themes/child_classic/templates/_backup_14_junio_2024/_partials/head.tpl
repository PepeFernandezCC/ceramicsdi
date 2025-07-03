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
{block name='head_charset'}
    <meta charset="utf-8">
{/block}
{block name='head_ie_compatibility'}
    <meta http-equiv="x-ua-compatible" content="ie=edge">
{/block}

{block name='head_seo'}
    <title>{block name='head_seo_title'}{$page.meta.title}{/block}</title>
    {block name='hook_after_title_tag'}
        {hook h='displayAfterTitleTag'}
    {/block}
    <meta name="description" content="{block name='head_seo_description'}{$page.meta.description}{/block}">
    {* PLANATEC
    <meta name="keywords" content="{block name='head_seo_keywords'}{$page.meta.keywords}{/block}">
    END PLANATEC *}
    {block name='robots_tags'}
        {if strpos($urls.current_url, '?q=') !== false || strpos($urls.current_url, '&q=') !== false}
            <meta name="robots" content="noindex, nofollow">
        {else}

            {if $page.meta.robots !== 'index'}
                <meta name="robots" content="{$page.meta.robots}">
            {/if}
            
        {/if}
    {/block}
    {if $page.canonical}
        {assign var="clean_url" value=$page.canonical|regex_replace:"/\?.*/":""}      
        <link rel="canonical" href="{$clean_url}">
    {else}
        {assign var="clean_url" value=$urls.current_url|regex_replace:"/\?.*/":""}
        <link rel="canonical" href="{$clean_url}">
    {/if}
    {block name='head_hreflang'}
        {foreach from=$urls.alternative_langs item=pageUrl key=code}
            <link rel="alternate" href="{$pageUrl}" hreflang="{$code}">
        {/foreach}
    {/block}

    {block name='head_microdata'}
        {include file="_partials/microdata/head-jsonld.tpl"}
    {/block}

    {block name='head_microdata_special'}{/block}

    {block name='head_pagination_seo'}
        {include file="_partials/pagination-seo.tpl"}
    {/block}

    {block name='head_open_graph'}
        <meta property="og:title" content="{$page.meta.title}"/>
        <meta property="og:description" content="{$page.meta.description}"/>
        <meta property="og:url" content="{$urls.current_url}"/>
        <meta property="og:site_name" content="{$shop.name}"/>
        {if !isset($product) && $page.page_name != 'product'}
            <meta property="og:type" content="website"/>
            {if $page.page_name == 'index'}
                <meta name="robots" content="follow, index, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
                <meta property="og:image" content="https://ceramicconnection.com/img/tmp/category_2.jpg"/>
            {/if}
            {if isset($category.image) }
                <meta property="og:image" content="{$category.image.large.url}"/>
            {/if}
        {/if}
    {/block}
{/block}

{block name='head_viewport'}
    <meta name="viewport" content="width=device-width, initial-scale=1">
{/block}

{* PLANATEC *}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
      integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
      crossorigin="anonymous" referrerpolicy="no-referrer"/>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"
/>

<meta name="google-site-verification" content="HF7Xbw70Tgsx_UgvCUvM1qrZhVFZlwAKXVQpd8pErUo" />
{* END PLANATEC *}

{block name='head_icons'}
    <link rel="icon" type="image/vnd.microsoft.icon" href="{$shop.favicon}?{$shop.favicon_update_time}">
    <link rel="shortcut icon" type="image/x-icon" href="{$shop.favicon}?{$shop.favicon_update_time}">
{/block}

{block name='stylesheets'}
    {include file="_partials/stylesheets.tpl" stylesheets=$stylesheets}
{/block}

{block name='javascript_head'}
    {include file="_partials/javascript.tpl" javascript=$javascript.head vars=$js_custom_vars}
{/block}

{block name='hook_header'}
    {$HOOK_HEADER nofilter}
{/block}

{block name='hook_extra'}{/block}

<!--  Código de seguimiento de Hotjar de https://ceramicconnection.com -->
{literal}
<script>
   (function(h,o,t,j,a,r){
      h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
      h._hjSettings={hjid:3568517,hjsv:6};
      a=o.getElementsByTagName('head')[0];
      r=o.createElement('script');r.async=1;
      r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
      a.appendChild(r);
   })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
{/literal}
<!-- End Hotjar -->



<!-- CockieFirst Code -->
<script src="https://consent.cookiefirst.com/sites/ceramicconnection.com-3b74c651-6ba5-4c11-bf9c-efea4461d982/consent.js"></script>
<!-- End CookieFirst Code -->


<!-- Pinterest -->
{literal}
    <meta name="p:domain_verify" content="2ee8ef23e9e9c2a88e798381b813fbef"/>
    <meta name="p:domain_verify" content="e527066424d84fb6a3fbd35c0fdbdbda"/>
{/literal}
<!-- End Pinterest -->

<!-- Meta -->
{literal}
    <meta name="facebook-domain-verification" content="wvb7wl1z71nfjwx1lt3deakhgn2lsw" />
{/literal}
<!-- End Meta -->

<!-- Meta Pixel Code -->
{literal}
<script>
   !function(f,b,e,v,n,t,s)
           {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
           n.callMethod.apply(n,arguments):n.queue.push(arguments)};
           if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
           n.queue=[];t=b.createElement(e);t.async=!0;
           t.src=v;s=b.getElementsByTagName(e)[0];
           s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
   fbq('init', '1144247227008709');
   fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=1144247227008709&ev=PageView&noscript=1"
    /></noscript>
{/literal}
<!-- End Meta Pixel Code -->