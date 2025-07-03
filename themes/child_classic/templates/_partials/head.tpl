
{block name='head_charset'}
    <meta charset="utf-8">
{/block}

{block name='head_ie_compatibility'}
    <meta http-equiv="x-ua-compatible" content="ie=edge">
{/block}

{block name='head_seo'}
    <title>
        {block name='head_seo_title'}
            {$page.meta.title}
        {/block}
    </title>
    {block name='hook_after_title_tag'}
        {hook h='displayAfterTitleTag'}
    {/block}
    <meta name="description" content="{block name='head_seo_description'}{$page.meta.description}{/block}">
    {assign var="noFollowPages" value=[
        "cart",
        "checkout",
        "my_account",
        "history",
        "discount",
        "order_slip",
        "order_follow",
        "address",
        "identity"
    ]}
    <meta name="p:domain_verify" content="7cc5c582fc3276c7b745f6581c4dfbbf"/>
    {block name='robots_tags'}
        {if strpos($urls.current_url, '?q=') !== false || strpos($urls.current_url, '&q=') !== false || in_array($page.page_name, $noFollowPages)}
            <meta name="robots" content="noindex, nofollow">
        {else}
            <meta name="robots" content="follow, index, max-snippet:-1, max-image-preview:large, max-video-preview:-1" /> 
        {/if}
    {/block}

{if $page.canonical}
    {assign var="clean_url" value=$page.canonical}
{else}
    {assign var="clean_url" value=$urls.current_url}
{/if}

{* Eliminar parámetros de consulta *}
{assign var="clean_url" value=$clean_url|regex_replace:"/\?.*$/" : ""}

{* Eliminar segmentos numéricos y evitar barras redundantes *}
{if strpos($clean_url, 'blog') !== false}
    {assign var="clean_url" value=$clean_url|regex_replace:"/\/[0-9]+\//" : "/"}  {* Eliminación de números entre barras y mantención de la parte restante *}
    {assign var="clean_url" value=$clean_url|regex_replace:"/\/[0-9]+/" : ""}  {* Eliminación de números al final *}
{/if}

<link rel="canonical" href="{$urls.current_url}">

    {block name='head_hreflang'}
        {foreach from=$urls.alternative_langs item=pageUrl key=code}
            <link rel="alternate" href="{$pageUrl}" hreflang="{$code}">
        {/foreach}
    {/block}

    

    {block name='head_microdata'}
        {if isset($listing)}
            {assign var="schemaCategoryData" value=Category::getSchemaCategoryData($category.id)}
            {include file="_partials/microdata/head-jsonld.tpl" schemaCategoryData=$schemaCategoryData}
        {else}
            {include file="_partials/microdata/head-jsonld.tpl"}
        {/if}
        
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


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer"/>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>


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


<!-- CockieFirst Code -->
<script src="https://consent.cookiefirst.com/sites/ceramicconnection.com-3b74c651-6ba5-4c11-bf9c-efea4461d982/consent.js"></script>
<!-- End CookieFirst Code -->
