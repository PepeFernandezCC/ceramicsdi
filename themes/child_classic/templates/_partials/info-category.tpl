    
    <div class="catalog-description-sticky">
        <div class="product-list-header " style="background: #FFF">

            <div class="info-category-header">

                <div class="row">

                    <div class="col-xl-12 col-xs-12">
                    {assign var="fatherCategory" value=Category::getFatherCategory($category.id_parent, $language.id)} 
                                        
                        {if $category.meta_title == ''}
                            {assign var="normalized_title" value=$category.name}
                        {else}
                            {assign var="normalized_title" value=$category.meta_title|lower|regex_replace:"/\|.*$/":""|trim}      
                        {/if}

                        {if $fatherCategory.meta_title == ''}
                            {assign var="normalized_father_title" value=$fatherCategory.name}
                        {else}
                            {assign var="normalized_father_title" value=$fatherCategory.meta_title|lower|regex_replace:"/\|.*$/":""|trim}        
                        {/if}

                        <div>
                            {$condition = false}

                            {if $category.id != $CATEGORY_CERAMICA_ID and $category.id != $CATEGORY_INSTALACION_Y_MONTAJE_ID and $category.id != $CATEGORY_AZULEJOS and $category.id != $CATEGORY_OTROS_MATERIALES_ID}

                                {$condition = true}

                            {/if}

                            {if $condition}
                                
                                <div id="bread-crumps-container" class="bread-crumps" data-father="{$normalized_father_title}" data-title="{$normalized_title}" data-color="none" data-location="category" data-dadcategory="{$category.id_parent}" style="padding-bottom:15px"></div> 
                            {/if}



                        </div>

                        

                        <div>

                            <h1 class="category-title" style="font-size: 16px;">

                                {$normalized_title}

                            </h1>

                        </div>

                    </div>

    

                    <div class="col-xl-12 col-xs-12">

                        <div class="category-description">
                            {$category.description nofilter}
                        </div>

                        <span class="read-more-btn" type="button">{l s='Read more' d='Shop.Theme.Catalog'}</span>
                        <span class="read-less-btn" type="button" style="display: none;">{l s='Read less' d='Shop.Theme.Catalog'}</span>

                        <div class="subcategories">
                            <h2 class="product-list-h2" style="text-transform:uppercase">{l s='list of' d='Shop.Theme.Catalog'}  {$normalized_title|regex_replace:"/\|.*$/":""|trim}</h2>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {block name='product_list_active_filters'}
            <div>
                {$listing.rendered_active_filters nofilter}
            </div>
        {/block}
    </div>