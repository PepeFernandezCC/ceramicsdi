
{assign var="colorArray" value=[41, 42, 43, 47, 44, 77, 78, 79, 83, 84, 85, 86]}
{assign var="lookArray" value=[20, 19, 26, 4, 18, 25, 27, 30, 31, 32, 46, 48, 89]}
{assign var="mostWantedArray" value=[103, 105, 40, 39]}
{assign var="otherMaterialsArray" value=[81, 82, 88]}
{assign var="installationArray" value=[36, 94]}
{if isset($categories) && $categories}

    <ul class="ets_mm_categories">
        {foreach from=$categories item='category'}
            {assign var="categoryFilter" value ="ets_category_normal"}
            {if in_array($category.id_category, $colorArray)}
                {assign var="categoryFilter" value ="ets_category_color"}
            {elseif in_array($category.id_category, $lookArray)}
                {assign var="categoryFilter" value ="ets_category_look"}
            {elseif in_array($category.id_category, $otherMaterialsArray)}
                {assign var="categoryFilter" value ="ets_category_other_material"}
            {elseif in_array($category.id_category, $installationArray)}
                {assign var="categoryFilter" value ="ets_category_installation"}
            {elseif in_array($category.id_category, $mostWantedArray)}
                {assign var="categoryFilter" value ="ets_category_most_wanted"}
            {/if}

            <li class="{$categoryFilter} hover_menu">
                <a class="ets_mm_url" href="{$link->getCategoryLink($category.id_category|intval)}">    
                    <div class="ets_mm_thumbnail">
                        {if $link->getMenuThumbnailImages($category.id_category)}
                            <img data-src="{$link->getMenuThumbnailImages($category.id_category)}" 
                            class="menu-image {if $categoryFilter eq 'ets_category_other_material'}ets_mm_thumbnail_other_materials{elseif $categoryFilter eq 'ets_category_installation'}ets_mm_thumbnail_installation{else}ets_mm_thumbnail_size{/if}"
                            loading="lazy" 
                            alt = "artículos de color: {$category.name|escape:'html':'UTF-8'}"
			  		        />

                        {/if}
                    </div>
                    {if ((in_array($category.id_category, $otherMaterialsArray) && !$link->getMenuThumbnailImages($category.id_category))
                        || !in_array($category.id_category, $otherMaterialsArray))
                        && ((in_array($category.id_category, $installationArray) && !$link->getMenuThumbnailImages($category.id_category))
                        || !in_array($category.id_category, $installationArray))}
                        <div class="ets_mm_description">
                            {$category.name|escape:'html':'UTF-8'}
                        </div>
                    {/if}
                </a>
            </li>
        {/foreach}
    </ul>
{/if}
