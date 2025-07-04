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
<div class="ybc_blog_layout_{$blog_layout|escape:'html':'UTF-8'} ybc-blog-wrapper ybc-blog-wrapper-gallery">
<h1 class="page-heading">{l s='Photo gallery' mod='ybc_blog'}</h1>
{if isset($blog_galleries)}                   
    <ul class="ybc-gallery">
        {foreach from=$blog_galleries item='gallery'}            
            <li class="col-xs-12 col-sm-4 col-lg-{12/$per_row|intval}">
                <a class="gallery_item {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}"  {if $gallery.description} title="{strip_tags($gallery.description)|escape:'html':'UTF-8'}"{/if} rel="prettyPhotoGalleryPage[gallery]" href="{$gallery.image|escape:'html':'UTF-8'}">
                    <img src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$gallery.thumb|escape:'html':'UTF-8'}{/if}" title="{$gallery.title|escape:'html':'UTF-8'}" alt="{$gallery.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$gallery.thumb|escape:'html':'UTF-8'}" class="lazyload"{/if} />
                </a>                    
            </li>
        {/foreach}
    </ul>                    
    {if $blog_paggination}
        <div class="blog-paggination">
            {$blog_paggination nofilter}
        </div>
    {/if}
{else}
    <p class="alert alert-warning">{l s='No item found' mod='ybc_blog'}</p>
{/if}
</div>
<script type="text/javascript">    
prettySkinGalleryPage = '{$prettySkin|escape:'html':'UTF-8'}';
prettyAutoPlayGalleryPage = {$prettyAutoPlay|intval};                    
</script>