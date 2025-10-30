{*
* PrettyURLs
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
*  @author    FME Modules
*  @copyright 2024 FME Modules All right reserved
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  @category  FMM Modules
*  @package   toppromobar
*}
{if $fmm_tsc_twitter_status eq '1'}
<meta name="twitter:card" content="{$twitter_card|escape:'htmlall':'UTF-8'}" />
<meta name="twitter:site" content="{$twiiter_site|escape:'htmlall':'UTF-8'}" />
<meta name="twitter:title" content="{$twitter_title|escape:'htmlall':'UTF-8'}" />
<meta name="twitter:description" content="{$twitter_description|escape:'htmlall':'UTF-8'}" />
{if $image != ""}
<meta name="twitter:image" content="{$image|escape:'htmlall':'UTF-8'}" />
<meta name="twitter:image:alt" content="{$twitter_imagealt|escape:'htmlall':'UTF-8'}" />
{else}
{if $version >= '1.7.0.0'}
<meta name="twitter:image" content="{$category.image.large.url|escape:'htmlall':'UTF-8'}" />
{else}
<meta name="twitter:image" content="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}" />
{/if}
<meta name="twitter:image:alt" content="{$twitter_imagealt|escape:'html':'UTF-8'}" />
{/if}
{else}
{/if}

{if $fmm_fg_facebook_status eq '1'} 
{if $version >= '1.7.0.0'}
{if $pagem eq 'product'}
{else}
<meta property="og:url" content="{$urls.current_url|escape:'html':'UTF-8'}" />
<meta property="og:type" content="{$type|escape:'html':'UTF-8'}" />
<meta property="og:title" content="{$title_fb|escape:'html':'UTF-8'}" />
<meta property="og:description" content="{$description_fb|escape:'html':'UTF-8'}" />
{if $pagem eq 'category'}
<meta property="og:image" content="{$category.image.large.url|escape:'html':'UTF-8'}" />
{else}
<meta property="og:image" content="{$image_fb|escape:'html':'UTF-8'}" />
{/if}
{/if}
{else}
{if $pagem eq 'product'}
<h1>{$request|escape:'htmlall':'UTF-8'}</h1>
<meta property="og:type" content="{$type|escape:'html':'UTF-8'}">
<meta property="og:url" content="{$request|escape:'html':'UTF-8'}" />
<meta property="og:title" content="{$title_fb|escape:'html':'UTF-8'}">
<meta property="og:site_name" content="{$shop_name|escape:'html':'UTF-8'}">
<meta property="og:description" content="{$description_fb|escape:'html':'UTF-8'}">
<meta property="og:image" content="{$image|escape:'htmlall':'UTF-8'}">
<meta property="product:pretax_price:amount" content="{$product_tax_exc|escape:'htmlall':'UTF-8'}">
<meta property="product:pretax_price:currency" content="{$currency->iso_code|escape:'htmlall':'UTF-8'}">
<meta property="product:price:amount" content="{$product_price_amount|escape:'htmlall':'UTF-8'}">
<meta property="product:price:currency" content="{$currency->iso_code|escape:'htmlall':'UTF-8'}">
{if isset($product->weight) && ($product->weight != 0)}
<meta property="product:weight:value" content="{$product->weight|escape:'htmlall':'UTF-8'}">
<meta property="product:weight:units" content="{$unit_weight|escape:'htmlall':'UTF-8'}">
{/if}
{else}
<meta property="og:url" content="{$request|escape:'html':'UTF-8'}" />
<meta property="og:type" content="{$type|escape:'html':'UTF-8'}" />
<meta property="og:title" content="{$title_fb|escape:'html':'UTF-8'}" />
<meta property="og:description" content="{$description_fb|escape:'html':'UTF-8'}" />
{if $pagem eq 'category'}
<meta property="og:image" content="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'htmlall':'UTF-8'}" />
{else}
<meta property="og:image" content="{$image_fb|escape:'html':'UTF-8'}" />
{/if}
{/if}
{/if}
{else}
{/if}