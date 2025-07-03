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

                    {assign var="normalized_title" value=$category.meta_title|lower}

                    <div>

                        <h1 class="category-title" style="font-size: 16px;">

                            {if $category.meta_title == ''}

                                {$category.name}

                            {else}
                                    
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
                        <h2 class="product-list-h2" style="text-transform:uppercase">{l s='list of' d='Shop.Theme.Catalog'}  {$normalized_title|replace:"| ceramic connection":""}</h2>
                    </div>

                </div>

            </div>

        </div>

    </div>
         