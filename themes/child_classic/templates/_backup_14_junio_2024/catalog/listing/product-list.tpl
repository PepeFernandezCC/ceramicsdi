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

        <div class="container-fluid hidden-sm-down">

            <div class="product-list-header row">

                {$condition = false}

                {if $category.id != $CATEGORY_CERAMICA_ID and $category.id != $CATEGORY_INSTALACION_Y_MONTAJE_ID and $category.id != $CATEGORY_AZULEJOS and $category.id != $CATEGORY_OTROS_MATERIALES_ID}

                    {$condition = true}

                {/if}



                <div class="{if $condition}col-xl-8 col-xs-6{else}col-xs-12{/if}">

                    {if $condition}

                        <h1 class="category-title" style="font-size: .9375rem;">

                            {*

                                <span class="hidden-sm-up" style="font-weight: normal;">

                                    {l s='Category' d='Admin.Global'}{l s=': ' d='Shop.Theme.Catalog'}

                                </span>


                            *}

                             
                                <!-- PLANATEC -->

                                {if $category.meta_title == ''}

                                    {$category.name}

                                {else}
                            
                                   {assign var="normalized_title" value=$category.meta_title|lower}
                                   {$normalized_title|replace:"| ceramic connection":""}

                                {/if}

                                <!-- END PLANATEC -->

                        </h1>

                    {/if}

                    <div class="category-description hidden-md-down">{$category.description nofilter}</div>

                </div>

                <div class="{if $condition}col-xl-4{/if} col-xs-6 d-flex">

                    {if $condition}

                        <a href="{$link->getCategoryLink($CATEGORY_CERAMICA_ID)|escape:'html':'UTF-8'}"

                           class="category-button btn btn-primary w-100">

                            {if $page.page_name == 'search'}

                                {l s='See all products' d='Shop.Theme.Catalog'}

                            {elseif !$categoria_texto_boton}

                                {l s='See all rooms' d='Shop.Theme.Catalog'}

                            {else}

                                {$categoria_texto_boton}

                            {/if}

                        </a>

                    {/if}

                </div>

            </div>

        </div>



        {*

        - PLANATEC: eliminado el bloque de la cabecera



        {block name='product_list_header'}

            <h1 id="js-product-list-header" class="h2">{$listing.label}</h1>

        {/block}

        *}



        {*

        - PLANATEC: eliminado el bloque de las subcategorías



        {block name='subcategory_list'}

            {if isset($subcategories) && $subcategories|@count > 0}

                {include file='catalog/_partials/subcategories.tpl' subcategories=$subcategories}

            {/if}

        {/block}

        *}



        {hook h="displayHeaderCategory"}



        <section id="products">

            {if $listing.products|count}



                {*

                - PLANATEC: eliminar la barra superior donde se indica el orden



                {block name='product_list_top'}

                    {include file='catalog/_partials/products-top.tpl' listing=$listing}

                {/block}

                *}



                {block name='product_list_active_filters'}

                    <div class="">

                        {$listing.rendered_active_filters nofilter}

                    </div>

                {/block}



                {block name='product_list'}

                    {include file='catalog/_partials/products.tpl' listing=$listing productClass="col-xs-6 col-md-6 col-xl-3"}

                {/block}



                {block name='product_list_bottom'}

                    {include file='catalog/_partials/products-bottom.tpl' listing=$listing}

                {/block}



            {else}

                {*

                - PLANATEC: eliminar la barra superior donde se indica el orden



                <div id="js-product-list-top"></div>

                *}

                <div id="js-product-list">

                    {capture assign="errorContent"}

                        <h4>{l s='No products available yet' d='Shop.Theme.Catalog'}</h4>

                        <p>{l s='Stay tuned! More products will be shown here as they are added.' d='Shop.Theme.Catalog'}</p>

                    {/capture}



                    {include file='errors/not-found.tpl' errorContent=$errorContent}

                </div>

                <div id="js-product-list-bottom"></div>

            {/if}



            <section id="carousel-materials" class="mb-3 carousel-materials-category">

                <div class="container-fluid text-center px-0">

                    <h2 style="color: black;">{l s='Other tile categories' d='Shop.Theme.Catalog'}</h2>



                    {assign var="categories" value=Category::getSimpleCategoriesWithParentInfos($language.id)}

                    {assign var="categoriesAppend" value=[]}

                    <div id="recipeCarousel" class="row mx-auto my-auto justify-content-center">

                        <div class="owl-carousel">

                            {while $categoriesAppend|count < 12}

                                {assign var="random" value=1|mt_rand:($categories|sizeof)}

                                {assign var="showCategory" value=Category::getCategoryInformation([$random], $language.id)}

                                {assign var="notShowArray" value=["STARTSEITE", "Startseite", "INICIO", "Inicio", "INÍCIO", "Início", "HOME", "Home", "ACCUEIL", "Accueil", "MATERIAL", "Material"]}

                                {if !empty($showCategory)}

                                    {if !in_array($random, $categoriesAppend)}

                                        {if !in_array($showCategory[$random]['name'], $notShowArray)}

                                            {assign var="imageCat" value=$link->getCatImageLink($showCategory[$random]['link_rewrite'], $random)}

                                            {assign var="imageCatHeaders" value=$imageCat|get_headers}

                                            {if !$imageCatHeaders[0]|strstr:"404" && !$imageCatHeaders[0]|strstr:"403"}

                                                <a href="{$link->getCategoryLink($random)|escape:'html':'UTF-8'}">

                                                    <div class="material-img">

                                                        <img src="{$imageCat}" loading="lazy">

                                                    </div>

                                                    <div class="material-title">

                                                        <h3 class="text-sm-center capitalize">{$showCategory[$random]['name']}</h3>

                                                    </div>

                                                </a>

                                                {append var="categoriesAppend" value=$random}

                                            {/if}

                                        {/if}

                                    {/if}

                                {/if}

                            {/while}

                        </div>

                    </div>

                </div>

            </section>



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

