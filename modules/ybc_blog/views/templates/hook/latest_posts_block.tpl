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
{if $posts}
    <div class="block ybc_block_latest {$blog_config.YBC_BLOG_RTL_CLASS|escape:'html':'UTF-8'} {if isset($page) && $page}page_{$page|escape:'html':'UTF-8'}{else}page_blog{/if} {if isset($page) && $page=='home'}{if isset($blog_config.YBC_BLOG_HOME_POST_TYPE) && $blog_config.YBC_BLOG_HOME_POST_TYPE=='default' || count($posts)<=1} ybc_block_default{else} ybc_block_slider{/if}{else}{if isset($blog_config.YBC_BLOG_SIDEBAR_POST_TYPE) && $blog_config.YBC_BLOG_SIDEBAR_POST_TYPE=='default' || count($posts)<=1} ybc_block_default{else} ybc_block_slider{/if}{/if}">
        <h4 class="title_blog title_block">{l s='Latest posts' mod='ybc_blog'}</h4>
        {assign var='product_row' value=$blog_config.YBC_BLOG_HOME_PER_ROW|intval}
        <div class="block_content row">
            <ul class="owl-rtl {if count($posts)>1}{if isset($page) && $page=='home' && $blog_config.YBC_BLOG_HOME_POST_TYPE!='default'}owl-carousel{elseif (!isset($page)||(isset($page) && $page!='home')) && $blog_config.YBC_BLOG_SIDEBAR_POST_TYPE!='default'}owl-carousel{/if}{/if}">
                {foreach from=$posts item='post'}
                    <li {if $page=='home'}class="col-xs-12 col-sm-4 col-lg-{12/$product_row|intval}"{/if}> 
                        {if $post.thumb}
                            <a class="ybc_item_img" href="{$post.link|escape:'html':'UTF-8'}">
                                <img src="{$post.thumb|escape:'html':'UTF-8'}" alt="{$post.title|escape:'html':'UTF-8'}" title="{$post.title|escape:'html':'UTF-8'}" />
                            </a>
                        {/if}
                        <div class="ybc-blog-latest-post-content">
                            <a class="ybc_title_block" href="{$post.link|escape:'html':'UTF-8'}">{$post.title|escape:'html':'UTF-8'}</a>
                            <div class="ybc-blog-sidear-post-meta">
                                {if isset($post.categories) && $post.categories}
                                    <div class="ybc-blog-categories">
                                        {assign var='ik' value=0}
                                        {assign var='totalCat' value=count($post.categories)}                        
                                        <div class="be-categories">
                                            <span class="be-label">{l s='Posted in' mod='ybc_blog'}: </span>
                                            {foreach from=$post.categories item='cat'}
                                                {assign var='ik' value=$ik+1}                                        
                                                <a href="{$cat.link|escape:'html':'UTF-8'}">{ucfirst($cat.title)|escape:'html':'UTF-8'}</a>{if $ik < $totalCat}<span class="comma">, </span>{/if}
                                            {/foreach}
                                        </div>
                                    </div>
                                {/if}
                                <span class="post-date">{if isset($blog_config.YBC_BLOG_DATE_FORMAT)&&$blog_config.YBC_BLOG_DATE_FORMAT}{date($blog_config.YBC_BLOG_DATE_FORMAT, strtotime($post.datetime_added))|escape:'html':'UTF-8'}{else}{date('F jS Y', strtotime($post.datetime_added))|escape:'html':'UTF-8'}{/if}</span>
                            </div>
                            {if $allowComments || $show_views || $allow_like} 
                                <div class="ybc-blog-latest-toolbar">
                                    {if $show_views}
                                        <span class="ybc-blog-latest-toolbar-views">{$post.click_number|intval} {if $post.click_number!=1}<span>{l s='views' mod='ybc_blog'}</span>{else}<span>{l s='view' mod='ybc_blog'}</span>{/if}</span> 
                                    {/if}   
                                    {if $allowComments && $post.comments_num>0}
                                        <span class="ybc-blog-latest-toolbar-comments">{$post.comments_num|intval} {if $post.comments_num!=1}<span>
                                        {l s='comments' mod='ybc_blog'}</span>{else}<span>{l s='comment' mod='ybc_blog'}</span>{/if}</span> 
                                    {/if}                                 
                                    {if $allow_like}
                                        <span title="{if $post.liked}{l s='Liked' mod='ybc_blog'}{else}{l s='Like this post' mod='ybc_blog'}{/if}" class="ybc-blog-like-span ybc-blog-like-span-{$post.id_post|intval} {if $post.liked}active{/if}"  data-id-post="{$post.id_post|intval}">                        
                                            <span class="ben_{$post.id_post|intval}">{$post.likes|intval}</span>
                                            <span class="blog-post-like-text blog-post-like-text-{$post.id_post|intval}">{l s='Liked' mod='ybc_blog'}</span>
                                            
                                        </span> 
                                    {/if}
                                    
                                </div>
                            {/if} 
                            {if $display_desc}                          
                                {if $post.short_description}
                                    <div class="blog_description"><p>
                                        {if isset($blog_config.YBC_BLOG_POST_EXCERPT_LENGTH) && (int)$blog_config.YBC_BLOG_POST_EXCERPT_LENGTH>0}
                                            {$post.short_description|strip_tags:'UTF-8'|truncate:(int)$blog_config.YBC_BLOG_POST_EXCERPT_LENGTH:'...'|escape:'html':'UTF-8'}
                                        {else}
                                            {$post.short_description|strip_tags:'UTF-8'|escape:'html':'UTF-8'}
                                        {/if}</p>
                                    </div>
                                {elseif $post.description}
                                    <div class="blog_description">
                                        {if isset($blog_config.YBC_BLOG_POST_EXCERPT_LENGTH) && (int)$blog_config.YBC_BLOG_POST_EXCERPT_LENGTH>0}
                                            {$post.description|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}
                                        {else}
                                            {$post.description|strip_tags:'UTF-8'|escape:'html':'UTF-8'}
                                        {/if}                                
                                    </div>
                                {/if}
                            {/if}
                            <a class="read_more" href="{$post.link|escape:'html':'UTF-8'}">{if $blog_config.YBC_BLOG_TEXT_READMORE}{$blog_config.YBC_BLOG_TEXT_READMORE|escape:'html':'UTF-8'}{else}{l s='Read More' mod='ybc_blog'}{/if}</a>
                            
                            
                        </div>
                    </li>
                {/foreach}
            </ul>
            {if $blog_config.YBC_BLOG_DISPLAY_BUTTON_ALL_HOMEPAGE || $page!='home'}
                <div class="blog_view_all_button">
                    <a href="{$latest_link|escape:'html':'UTF-8'}" class="view_all_link">{l s='View all latest posts' mod='ybc_blog'}</a>
                </div>
            {/if}
        </div>
        <div class="clear"></div>
    </div>
    
{/if}