    <div class="product-list-header" style="background: #FFF">

        <div class="info-category-header">

            <div class="row">

                <div class="col-xl-12 col-xs-12">

                    <div>
                        {$condition = false}

                        {if $category.id != $CATEGORY_CERAMICA_ID and $category.id != $CATEGORY_INSTALACION_Y_MONTAJE_ID and $category.id != $CATEGORY_AZULEJOS and $category.id != $CATEGORY_OTROS_MATERIALES_ID}

                            {$condition = true}

                        {/if}

                        {if $condition}
                            <div id="bread-crumps-container" class="bread-crumps" data-color="none" data-location="category" style="padding-bottom:15px"></div> 
                        {/if}



                    </div>

                    <div>

                        <h1 class="category-title" style="font-size: 18px;">

                            {if $category.meta_title == ''}

                                {$category.name}

                            {else}
                                    
                                {assign var="normalized_title" value=$category.meta_title|lower}
                                {$normalized_title|replace:"| ceramic connection":""}

                            {/if}

                        </h1>

                    </div>

                </div>

 

                <div class="col-xl-12 col-xs-12">

                    <div class="category-description hidden-md-down">
                        {$category.description nofilter}
                    </div>

                    <div class="subcategories">
                        {*
                            {assign var="areaArray" value=[12, 13, 16, 37, 38]}
                            {assign var="colorArray" value=[41, 42, 43, 47, 44, 77, 78, 79, 83, 84, 85, 86]} 
                            {assign var="subCategories" value=Category::getSubCategoriesArray($category.id, $language.id)}
                            {if in_array($category.id, $areaArray) 
                                || in_array($category.id_parent, $areaArray)
                                || in_array($category.id, $colorArray) 
                                || in_array($category.id_parent, $colorArray)}

                                        {if in_array($category.id_parent, $areaArray) || in_array($category.id_parent, $colorArray)}
                                            {assign var="subCategories" value=Category::getSubCategoriesArray($category.id_parent, $language.id)}               
                                        {/if}

                                    <h2 class="product-list-h2" style="text-transform: uppercase">
                                        {assign var="separator" value=""}
                                        
                                        {foreach $subCategories as $subCategory}
                                            {if Category::categoryProductsCountById($subCategory.id_category) > 0}
                                                {$separator}<a href="{$link->getCategoryLink($subCategory.id_category|intval)}" style="{if $category.id == $subCategory.id_category}font-weight:bold{/if}">{$subCategory.name}</a>
                                                {assign var="separator" value=" | "}
                                            {/if} 
                                        {/foreach}

                                    </h2>

                                {else}
                                    <h2 class="product-list-h2">{l s='Product List' d='Shop.Theme.Catalog'}</h2>
                            {/if}
                        *}
                        <h2 class="product-list-h2">{l s='Product List' d='Shop.Theme.Catalog'}</h2>
                    </div>

                </div>

            </div>

        </div>

    </div>
         