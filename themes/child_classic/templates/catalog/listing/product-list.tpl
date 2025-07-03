{**

 * Copyright since 2007 PrestaShop SA and Contributors

 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA

 *

 * NOTICE OF LICENSE

 *

 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)

 * that is bundled with this package in the file LICENSE.md.

 * It is also available through the world-wide-web at this URL:

 * https://opensource.org/licenses/AFL-3.0

 * If you did not receive a copy of the license and are unable to

 * obtain it through the world-wide-web, please send an email

 * to license@prestashop.com so we can send you a copy immediately.

 *

 * DISCLAIMER

 *

 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer

 * versions in the future. If you wish to customize PrestaShop for your

 * needs please refer to https://devdocs.prestashop.com/ for more information.

 *

 * @author    PrestaShop SA and Contributors <contact@prestashop.com>

 * @copyright Since 2007 PrestaShop SA and Contributors

 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)

 *}

{extends file=$layout}



{block name='head_microdata_special'}

    {include file='_partials/microdata/product-list-jsonld.tpl' listing=$listing}

{/block}



{block name='content'}

    <section id="main">

        {include file='_partials/info-category.tpl'}


        {hook h="displayHeaderCategory"}


        <section id="products">

            {if $listing.products|count}

                {block name='product_list_active_filters'}

                    <div class="">

                        {$listing.rendered_active_filters nofilter}

                    </div>

                {/block}



                {block name='product_list'}

                    {include file='catalog/_partials/products.tpl' listing=$listing productClass="col-xl-3 col-md-4 col-xs-6"}

                {/block}



                {block name='product_list_bottom'}

                    {include file='catalog/_partials/products-bottom.tpl' listing=$listing}

                {/block}



            {else}

                <div id="js-product-list">

                    {capture assign="errorContent"}

                        <h4>{l s='No products available yet' d='Shop.Theme.Catalog'}</h4>

                        <p>{l s='Stay tuned! More products will be shown here as they are added.' d='Shop.Theme.Catalog'}</p>

                    {/capture}



                    {include file='errors/not-found.tpl' errorContent=$errorContent}

                </div>

                <div id="js-product-list-bottom"></div>

            {/if}

            {assign var="notShowArray" value=[
                "STARTSEITE", 
                "Startseite", 
                "INICIO", 
                "Inicio", 
                "INÍCIO", 
                "Início", 
                "HOME", 
                "Home", 
                "ACCUEIL", 
                "Accueil", 
                "MATERIAL", 
                "Material"
            ]}

            {if $category.id == '11'}
                {assign var="categories" value=Category::getPopularCategoriesArray($language.id)}
            {else}
                {assign var="categories" value=Category::getSubCategoriesArray($category.id_parent, $language.id)}
            {/if}

            {if $categories|@count <= 5}
                <section id="carousel-materials" class="carousel-materials-category">

                    <div class="container-fluid text-center px-0">

                        <h2 style="color: black;">{l s='Other tile categories' d='Shop.Theme.Catalog'}</h2>

                        {assign var="categoriesAppend" value=[]}

                        <div class="row mx-auto my-auto justify-content-center" style="padding-top: 40px">

                            <div class="fixBrothers">

                                {foreach from=$categories item=cat}

                                    {assign var="showCategory" value=Category::getCategoryInformation([$cat.id_category], $language.id)}

                                    {if !empty($showCategory)}

                                        {if !in_array($cat.id_category, $categoriesAppend)}

                                            {if !in_array($showCategory[$cat.id_category]['name'], $notShowArray)}

                                                {assign var="imageCat" value=$link->getCatImageLink($showCategory[$cat.id_category]['link_rewrite'], $cat.id_category)}

                                                <div class="brotherCategory">
                                                    <a href="{$link->getCategoryLink($cat.id_category)|escape:'html':'UTF-8'}">
                                                        
                                                        <div class="material-img material-img-fix">
                                                            <img 
                                                                src="{$imageCat}" 
                                                                loading="lazy" 
                                                                alt="items material: {$showCategory[$cat.id_category]['name']}" 
                                                                data-show-category-name="{$showCategory[$cat.id_category]['name']}"
                                                                onerror="this.onerror=null; this.src='/themes/child_classic/assets/img/web/default.webp';"
                                                            >
                                                        </div>

                                                        <div class="material-title">
                                                            <h3 class="text-sm-center capitalize">{$showCategory[$cat.id_category]['name']}</h3>
                                                        </div>
                                                        
                                                    </a>
                                                </div>

                                                {append var="categoriesAppend" value=$cat.id_category}

                                            {/if}


                                        {/if}

                                    {/if}

                                {/foreach}

                            </div>

                        </div>

                    </div>

                </section>
            {else}
                <section id="carousel-materials" class="mb-3 carousel-materials-category">

                    <div class="container-fluid text-center px-0">

                        <h2 style="color: black;">{l s='Other tile categories' d='Shop.Theme.Catalog'}</h2>

                        {assign var="categoriesAppend" value=[]}

                        <div id="recipeCarousel" class="row mx-auto my-auto justify-content-center">

                            <div class="owl-carousel">

                                {foreach from=$categories item=cat}

                                    {assign var="showCategory" value=Category::getCategoryInformation([$cat.id_category], $language.id)}

                                    {if !empty($showCategory)}

                                        {if !in_array($cat.id_category, $categoriesAppend)}

                                            {if !in_array($showCategory[$cat.id_category]['name'], $notShowArray)}

                                                {assign var="imageCat" value=$link->getCatImageLink($showCategory[$cat.id_category]['link_rewrite'], $cat.id_category)}

                                                    <a href="{$link->getCategoryLink($cat.id_category)|escape:'html':'UTF-8'}">

                                                        <div class="material-img">
                                                            <img src="{$imageCat}" loading="lazy" alt="items material: {$showCategory[$cat.id_category]['name']}"
                                                            onerror="this.onerror=null; this.src='/themes/child_classic/assets/img/web/default.webp';"
                                                            data-show-category-name="{$showCategory[$cat.id_category]['name']}">
                                                        </div>

                                                        <div class="material-title">

                                                            <h3 class="text-sm-center capitalize">{$showCategory[$cat.id_category]['name']}</h3>

                                                        </div>

                                                    </a>

                                                    {append var="categoriesAppend" value=$cat.id_category}

                                            {/if}

                                        {/if}

                                    {/if}

                                {/foreach}

                            </div>

                        </div>

                    </div>

                </section>
                
            {/if}



            {if $categoria_contenido_extra !== ''}

                <div id="category-extra-content" class="container-fluid">

                    <div class="row">

                        <div class="col-xs-12">

                            {$categoria_contenido_extra nofilter}

                        </div>

                    </div>

                </div>

            {/if}

        </section>


        {hook h="displayFooterCategory"}



    </section>

{/block}

