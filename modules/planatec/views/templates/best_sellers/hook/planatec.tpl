
<section id="categories-list">

    <h2>{l s='Buy for space' mod='planatec'}</h2>



    {assign var="categories" value=Category::getSimpleCategoriesWithParentInfos($language.id)}
    {assign var="categoriesAreaArray" value=[12, 13, 14, 16, 38, 37]}

    <div class="container-fluid">

        <div class="row">
 

            {foreach from=$categories item="category"}

                {if in_array($category['id_category'], $categoriesAreaArray)}

                    {assign var="infoCategory" value=Category::getCategoryInformation([$category['id_category']], $language.id)}

                    <div class="categories-col">

                        <div class="category-item">

                            <a href="{$link->getCategoryLink($category['id_category'])|escape:'html':'UTF-8'}">

                                <img src="{$link->getCatImageLink($infoCategory[$category['id_category']]['link_rewrite'], $category['id_category'])}" loading="lazy" alt="items category: {$infoCategory[$category['id_category']]['name']}">

                                <div class="category-list-title">

                                    <h3>{$infoCategory[$category['id_category']]['name']}</h3>

                                </div>

                            </a>

                        </div>

                    </div>

                {/if}

            {/foreach}

        </div>

    </div>

</section>



<section id="section-home">

    {*<div class="container-fluid">

        <div class="row">

            <div class="col-lg-5 col-xs-12 section-text">

                {$contentSectionHome nofilter}

                <a class="btn btn-primary" href="{$buttonUrlSectionHome}">

                    {l s='Know more' mod='planatec'}

                </a>

            </div>

            <div class="col-lg-7 col-xs-12 section-img">

                <a href="{$buttonUrlSectionHome}">

                    <img src="{$imgSectionHome}" loading="lazy">

                </a>

            </div>

        </div>

    </div>*}

    {if $language.id == 1}

        <div class="hidden-md-down" style="padding: 100px; line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/ESP_PIDE_TU_MUESTRA_panoramico.mp4">

        </div>

        <div class="hidden-sm-up" style="line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/ESP_PIDE_TU_MUESTRA_cuadrado.mp4">

        </div>

    {elseif $language.id == 2}

        <div class="hidden-md-down" style="padding: 100px; line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/FR_PIDE_TU_MUESTRA_panoramico.mp4">

        </div>

        <div class="hidden-sm-up" style="line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/FR_PIDE_TU_MUESTRA_cuadrado.mp4">

        </div>

    {elseif $language.id == 3}

        <div class="hidden-md-down" style="padding: 100px; line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/ENG_PIDE_TU_MUESTRA_panoramico.mp4">

        </div>

        <div class="hidden-sm-up" style="line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/ENG_PIDE_TU_MUESTRA_cuadrado.mp4">

        </div>

    {elseif $language.id == 4}

        <div class="hidden-md-down" style="padding: 100px; line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/DE_PIDE_TU_MUESTRA_panoramico.mp4">

        </div>

        <div class="hidden-sm-up" style="line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/DE_PIDE_TU_MUESTRA_cuadrado.mp4">

        </div>

    {elseif $language.id == 5}

        <div class="hidden-md-down" style="padding: 100px; line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/PR_PIDE_TU_MUESTRA_panoramico.mp4">

        </div>

        <div class="hidden-sm-up" style="line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/PR_PIDE_TU_MUESTRA_cuadrado.mp4">

        </div>
    {elseif $language.id == 6}

        <div class="hidden-md-down" style="padding: 100px; line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/NL_PIDE_TU_MUESTRA_panoramico.mp4">

        </div>

        <div class="hidden-sm-up" style="line-height: 0;">

            <video autoplay muted loop playsinline width="100%" height="100%"

                   src="/themes/child_classic/assets/img/NL_PIDE_TU_MUESTRA_cuadrado.mp4">

        </div>

    {/if}

</section>



{* CARRUSEL ANTIGUO

<section id="carousel-materials">

    <div class="container-fluid text-center px-0">

        <h2>{l s='Select by material' mod='planatec'}</h2>



        {assign var="materialCategories" value=Category::getSimpleCategoriesWithParentInfos($language.id)}

        <div id="recipeCarousel" class="row mx-auto my-auto justify-content-center">

            <div class="owl-carousel">

                {foreach from=$materialCategories item="materialCategory" name="count"}

                    {if $materialCategory['name'] == 'MATERIAL'}

                        {assign var='parentMaterialCategory' value=Category::getCategoryInformation([$materialCategory['id_parent']], $language.id)}

                        <a href="{$link->getCategoryLink($materialCategory['id_parent'])|escape:'html':'UTF-8'}">

                            <div class="material-img">

                                <img src="{$link->getCatImageLink($parentMaterialCategory[$materialCategory['id_parent']]['link_rewrite'], $materialCategory['id_parent'])}" alt="items material: {$parentMaterialCategory[$materialCategory['id_parent']]['name']}" loading="lazy">

                            </div>

                            <div class="material-title">

                                <h3 class="text-sm-center capitalize">{$parentMaterialCategory[$materialCategory['id_parent']]['name']}</h3>

                            </div>

                        </a>

                    {/if}

                {/foreach}

            </div>

        </div>

    </div>

</section>

*}



<section id="carousel-materials-v2">

    <div class="container-fluid text-center px-0">

        <h2>{l s='Select by material' mod='planatec'}</h2>



        {assign var="materialCategories" value=Category::getSimpleCategoriesWithParentInfos($language.id)}

        <div class="outside-wrapper">

            <div class="inside-wrapper">

                <div id="recipeCarousel" class="row mx-auto my-auto justify-content-center swiper recipeCarousel-swiper">

                    <div class="swiper-wrapper">

                        {foreach from=$materialCategories item="materialCategory" name="count"}

                            {if $materialCategory['name'] == 'MATERIAL'
                                || $materialCategory['name'] == 'MATÉRIAU'
                                || $materialCategory['name'] == 'MATERIAU'
                                || $materialCategory['name'] == 'MATERIAAL'
                                || $materialCategory['name'] == 'KERAMIEK'}

                                {assign var='parentMaterialCategory' value=Category::getCategoryInformation([$materialCategory['id_parent']], $language.id)}

                                <a class="swiper-slide"

                                   href="{$link->getCategoryLink($materialCategory['id_parent'])|escape:'html':'UTF-8'}">

                                    <div class="material-img">

                                        <img src="{$link->getCatImageLink($parentMaterialCategory[$materialCategory['id_parent']]['link_rewrite'], $materialCategory['id_parent'])}" alt="items material: {$parentMaterialCategory[$materialCategory['id_parent']]['name']}" loading="lazy">

                                    </div>

                                    <div class="material-title">

                                        <h3 class="text-sm-center capitalize">{$parentMaterialCategory[$materialCategory['id_parent']]['name']}</h3>

                                    </div>

                                </a>

                            {/if}

                        {/foreach}

                    </div>

                    <div class="swiper-button-prev"></div>

                    <div class="swiper-button-next"></div>

                </div>

            </div>

        </div>

    </div>

</section>



{*

<section id="carousel-materials">

    <div class="container-fluid text-center px-0">

        <h2>{l s='Select by material' mod='planatec'}</h2>



        {assign var="materialCategories" value=Category::getChildren($CATEGORY_PAGINA_DE_INICIO_ELIGE_POR_MATERIALES_ID, $language.id, false, false)}

        <div class="row mx-auto my-auto justify-content-center">

            <div id="recipeCarousel" class="carousel slide" data-ride="carousel">

                <div class="carousel-inner" role="listbox">

                    {foreach from=$materialCategories item="materialCategory" name="count"}

                        <div class="carousel-item {if $smarty.foreach.count.index === 0}active{/if}">

                            <div class="col-lg-3">

                                <div class="materia-card m-1">

                                    <a href="{$link->getCategoryLink(3)|escape:'html':'UTF-8'}?q=Look-{$materialCategory['name']}">

                                        <div class="material-img">

                                            <img src="{$link->getCatImageLink($materialCategory['link_rewrite'], $materialCategory['id_category'])}" loading="lazy" alt="items material: {$materialCategory['name']}">

                                        </div>

                                        <div class="material-title">

                                            <h3 class="text-sm-center capitalize">{$materialCategory['name']}</h3>

                                        </div>

                                    </a>

                                </div>

                            </div>

                        </div>

                    {/foreach}

                </div>

                <a class="carousel-control-prev bg-dark w-auto" href="#recipeCarousel" role="button" data-slide="prev">

                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>

                    <span class="sr-only">Previous</span>

                </a>

                <a class="carousel-control-next bg-dark w-auto" href="#recipeCarousel" role="button" data-slide="next">

                    <span class="carousel-control-next-icon" aria-hidden="true"></span>

                    <span class="sr-only">Next</span>

                </a>

            </div>

        </div>

    </div>

</section>

*}