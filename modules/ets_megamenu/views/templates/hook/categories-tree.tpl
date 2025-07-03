{*
/**
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
 */
*}

{assign var="colorArray" value=[41, 42, 43, 47, 44, 77, 78, 79, 83, 84, 85, 86]}
{assign var="lookArray" value=[20, 19, 26, 4, 18, 25, 27, 30, 31, 32, 46, 48]}

{if isset($categories) && $categories}

    <ul class="ets_mm_categories">
        {foreach from=$categories item='category'}
        
            {assign var="categoryFilter" value ="ets_category_normal"}
            {if in_array($category.id_category, $colorArray)}
                {assign var="categoryFilter" value ="ets_category_color"}
            {elseif in_array($category.id_category, $lookArray)}
                {assign var="categoryFilter" value ="ets_category_look"}
            {/if}

            <li class="{$categoryFilter} {if isset($category.sub) && $category.sub}has-sub{/if}">
                <a class="ets_mm_url" href="{$link->getCategoryLink($category.id_category|intval)}">    
                    <div class="ets_mm_thumbnail">
                        {if $link->getMenuThumbnailImages($category.id_category)}
                            <img src="{$link->getMenuThumbnailImages($category.id_category)}" class="ets_mm_thumbnail_size" />
                        {/if}
                    </div>
                    <div class="ets_mm_description">
                        {$category.name|escape:'html':'UTF-8'}
                    </div>
                </a>
                {if isset($category.sub) && $category.sub}
                    <span class="arrow closed"></span>
                    {$category.sub nofilter}
                {/if}
            </li>
        {/foreach}
    </ul>
{/if}
