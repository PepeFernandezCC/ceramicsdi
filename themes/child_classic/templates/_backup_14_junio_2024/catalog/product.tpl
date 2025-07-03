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



{block name='head' append}

    <meta property="og:type" content="product">

    {if $product.cover}

        <meta property="og:image" content="{$product.cover.large.url}">

    {/if}



    {if $product.show_price}

        <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">

        <meta property="product:pretax_price:currency" content="{$currency.iso_code}">

        <meta property="product:price:amount" content="{$product.price_amount}">

        <meta property="product:price:currency" content="{$currency.iso_code}">

    {/if}

    {if isset($product.weight) && ($product.weight != 0)}

        <meta property="product:weight:value" content="{$product.weight}">

        <meta property="product:weight:units" content="{$product.weight_unit}">

    {/if}

{/block}



{block name='head_microdata_special'}

    {include file='_partials/microdata/product-jsonld.tpl'}

{/block}



{block name='content'}

    <section id="main">

        <meta content="{$product.url}">



        <div class="row product-container js-product-container">

            <div class="col-md-6 col-xs-12" id="product-images-block">

                {block name='page_content_container'}

                    <section class="page-content" id="content">

                        {block name='page_content'}

                            {* PLANATEC

                            {include file='catalog/_partials/product-flags.tpl'}

                            *}



                            {block name='product_cover_thumbnails'}

                                {include file='catalog/_partials/product-cover-thumbnails.tpl'}

                            {/block}

                            <div class="scroll-box-arrows">

                                <i class="material-icons left">&#xE314;</i>

                                <i class="material-icons right">&#xE315;</i>

                            </div>

                        {/block}

                    </section>

                {/block}

            </div>

            <div class="col-md-6 col-xs-12" id="product-content-block">

                {include file='catalog/_partials/product-flags.tpl'}

                {* PLANATEC *}

                {*

                <div id="push-scroll-responsive" class="hidden-sm-up">

                    <i class="material-icons" style="margin-right: -2px;">expand_less</i>

                    <i class="material-icons" style="margin-left: -2px;">expand_more</i>

                </div>

                *}



                <div class="custom-content">

                    {* END PLANATEC *}



                    <div id="push-scroll-responsive-header">

                        {block name='page_header_container'}

                            {block name='page_header'}

                                <div class="row d-flex product-head">

                                    <div class="col-xs-8">

                                        <h1 class="h1">

                                            {block name='page_title'}
                                                {$product.name}
                                            {/block}

                                        </h1>

                                    </div>

                                    <div class="col-xs-4">

                                        {block name='product_prices'}

                                            {include file='catalog/_partials/product-prices.tpl'}

                                        {/block}

                                    </div>

                                </div>

                                <div class="row" style="padding-top: 10px">

                                    <div class="col-xs-8">

                                        {if isset($product.reference_to_display) && $product.reference_to_display neq ''}

                                            <div class="product-reference">

                                                <span>Ref: {$product.reference_to_display}</span>

                                            </div>

                                        {/if}

                                    </div>

                                    <div class="col-xs-4">

                                        <span class="tax-message" style="float: right;">

                                            {if $customer.id_default_group == 5}

                                                ({l s='Tax excluded' d='Admin.Global'})

                                            {else}

                                                ({l s='Tax included' d='Admin.Global'})

                                            {/if}

                                        </span>

                                    </div>

                                </div>

                            {/block}

                        {/block}

                        <hr>

                        {block name='product_availability'}
                            <span id="product-availability" class="js-product-availability">
                                {if $product.show_availability && $product.availability_message}
                                    {if $product.availability == 'available' and $product.quantity > 0}
                                        <i class="material-icons rtl-no-flip product-available">&#xE5CA;</i>
                                    {elseif $product.availability == 'last_remaining_items'}
                                        <i class="material-icons product-last-items">&#xE002;</i>
                                    {else}
                                        <i class="material-icons product-last-items">&#xE002;</i>
                                    {/if}
                                    {$product.availability_message}
                                {/if}
                            </span>
                        {/block}

                    </div>



                    {* PLANATEC *}

                    {assign var="otherMaterialsArray" value=[81, 82, 88]}
                    {assign var="isByPiece" value=false}

                    {foreach from=$product.features item='feature'}

                        {if $feature.id_feature === $FEATURE_M2_PIEZA_ID}

                            {assign var="isByPiece" value=true}

                        {/if}

                    {/foreach}

                    {* END PLANATEC *}



                    <div class="product-information">

                        {if $product.is_customizable && count($product.customizations.fields)}

                            {block name='product_customization'}

                                {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}

                            {/block}

                        {/if}

                        <div class="rowTitle">
                            {l s='Product Details' d='Shop.Theme.Catalog'}:
                        </div>

                        <div class="product-traits">
                           
                            <div class="trait">
                                <span> {l s='Format' d='Shop.Theme.Catalog'}:</span>
                                <br />                  
                                {foreach from=$product.features item='feature'}
                                    {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}
                                        <span style="font-weight: 600">{$feature.value}</span>
                                    {/if}
                                {/foreach}
                            </div>
                            
                            <div class="trait">
                                {foreach from=$product.features item='feature'}
                                    {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_ACABADO}
                                        <span> {$feature.name}:</span>
                                        <br />                  
                                        <span style="font-weight: 600">{$feature.value}</span>
                                    {/if}
                                {/foreach}
                            </div>
                                    
                            <div class="trait">
                                <span>{l s='Material' d='Shop.Theme.Catalog'}:</span>
                                <br />
                                {if !in_array($product.id_category_default, $otherMaterialsArray)}
                                    {foreach from=$product.features item='feature'} 
                                        {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MATERIAL}
                                            <span style="font-weight: 600">{$feature.value}</span>
                                        {/if}
                                    {/foreach}
                                {else}
                                    <span style="font-weight: 600">{$product.category_name}</span>
                                {/if}
                            </div>
                
                        </div>

                        <div class="rowTitle">
                            {l s='Price Calculator' d='Shop.Theme.Catalog'}:
                        </div>

                        <div class="product-actions js-product-actions">

                            {block name='product_buy'}

                                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">

                                    <input type="hidden" name="token" value="{$static_token}">

                                    <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">

                                    <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id" class="js-product-customization-id">

                                    {block name='product_pack'}

                                        {if $packItems}

                                            <section class="product-pack">

                                                <p class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</p>

                                                {foreach from=$packItems item="product_pack"}

                                                    {block name='product_miniature'}

                                                        {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack showPackProductsPrice=$product.show_price}

                                                    {/block}

                                                {/foreach}

                                            </section>

                                        {/if}

                                    {/block}

                                    {block name='product_discounts'}

                                        {include file='catalog/_partials/product-discounts.tpl'}

                                    {/block}

                                    {block name='product_add_to_cart'}

                                        {include file='catalog/_partials/product-add-to-cart.tpl'}

                                    {/block}

                                    {block name='product_additional_info'}

                                        {include file='catalog/_partials/product-additional-info.tpl'}

                                    {/block}

                                    {block name='product_refresh'}{/block}

                                </form>

                            {/block}

                        </div>

                        {block name='product_tabs'}

                            <div class="product-accordion">

                                <button class="accordion-button">

                                    {l s='Description' d='Shop.Theme.Catalog'}

                                </button>

                                <div class="panel">

                                    {block name='product_details'}

                                        {$product.description nofilter}


                                        <p class="product-feature-espesor font-weight-bold"></p>

                                    {/block}

                                </div>

                                <button class="accordion-button">

                                    {l s='Technical characteristics' d='Shop.Theme.Catalog'}

                                </button>

                                <div class="panel">

                                    {block name='product_details'}

                                        {include file='catalog/_partials/product-details.tpl'}

                                    {/block}

                                </div>

                                <button class="accordion-button">

                                    {if $category->id_category == $CATEGORY_INSTALACION_Y_MONTAJE_ID}

                                        {l s='How to use' d='Shop.Theme.Catalog'}

                                    {else}

                                        {l s='Use and maintenance' d='Shop.Theme.Catalog'}

                                    {/if}

                                </button>

                                <div class="panel">

                                    {$productUsoMantenimiento nofilter}

                                </div>



                                <button class="accordion-button">

                                    {l s='Shipping and returns' d='Shop.Theme.Catalog'}

                                </button>

                                <div class="panel">

                                    {$productEnviosDevoluciones nofilter}

                                </div>

                            </div>

                        {/block}

                    </div>

                </div>



                {* PLANATEC *}

                {if $accessories}

                    <section id="products" class="mobile-product-accessories clearfix hidden-sm-up">

                        <h2 class="h5 text-uppercase">{l s='Recommended combinations' d='Shop.Theme.Catalog'}</h2>
                        {assign var="minSlides" value=10}
                        {assign var="totalSlides" value=$accessories|count}

                        {if $totalSlides < $minSlides}
                            {assign var="iterations" value=($minSlides/$totalSlides)|ceil}
                            {assign var="newAccessories" value=[]}

                            {section name=repeat loop=$iterations}
                                {foreach from=$accessories item="product_accessory"}
                                    {$newAccessories[] = $product_accessory}
                                {/foreach}
                            {/section}

                            {assign var="accessories" value=$newAccessories|@array_slice:0:$minSlides}
                        {/if}

                        <div class="custom-featured-products container-fluid">
                               
                                <div class="outside-wrapper">
                                    <div class="inside-wrapper">
                                        <div class="products{if !empty($cssClass)} {$cssClass}{/if} swiper custom-featured-swiper">
                                            <div class="swiper-wrapper">

                                                {foreach from=$accessories item="product_accessory" key="position" name="productIteration"}
                                                    <div class="swiper-slide">
                                                        {include file='catalog/_partials/miniatures/related_product.tpl' product=$product_accessory position=$position productIteration=$smarty.foreach.productIteration.iteration productClasses="col-xs-6 col-md-4 col-xl-2" isAccessory=true totalAccessories=$accessories|count}
                                                    </div>
                                                {/foreach}
                                                
                                            </div>
                                            <div class="swiper-button-prev"></div>
                                            <div class="swiper-button-next"></div>
                                        </div>
                                    </div>
                                </div>

                        </div>

                    </section>

                {/if} 

                {* END PLANATEC *}

            </div>

        </div>

        {block name='product_accessories'}

            {if $accessories}

                <section id="related-products-desktop" class="product-accessories clearfix">

                    <h2 class="h5 text-uppercase">{l s='Recommended combinations' d='Shop.Theme.Catalog'}</h2>

                {assign var="minSlides" value=10}
                {assign var="totalSlides" value=$accessories|count}

                {if $totalSlides < $minSlides}
                    {assign var="iterations" value=($minSlides/$totalSlides)|ceil}
                    {assign var="newAccessories" value=[]}

                    {section name=repeat loop=$iterations}
                        {foreach from=$accessories item="product_accessory"}
                            {$newAccessories[] = $product_accessory}
                        {/foreach}
                    {/section}

                    {assign var="accessories" value=$newAccessories|@array_slice:0:$minSlides}
                {/if}

                    <div class="custom-featured-products container-fluid">
                            <div class="outside-wrapper">
                                <div class="inside-wrapper">
                                    <div class="products{if !empty($cssClass)} {$cssClass}{/if} swiper custom-featured-swiper">
                                        <div class="swiper-wrapper">
                                            {foreach from=$accessories item="product_accessory" key="position" name="productIteration"}
                                                <div class="swiper-slide">
                                                    {include file='catalog/_partials/miniatures/related_product.tpl' product=$product_accessory position=$position productIteration=$smarty.foreach.productIteration.iteration productClasses="col-xs-6 col-md-4 col-xl-2" isAccessory=true totalAccessories=$accessories|count}
                                                </div>
                                            {/foreach}
                                        </div>
                                        <div class="swiper-button-prev"></div>
                                        <div class="swiper-button-next"></div>
                                    </div>
                                </div>
                            </div>

                    </div>

                </section>


            {/if}

        {/block}


        {block name='product_footer'}

            {hook h='displayFooterProduct' product=$product category=$category}

        {/block}



        {block name='product_images_modal'}

            {include file='catalog/_partials/product-images-modal.tpl'}

        {/block}



        {block name='page_footer_container'}

            <footer class="page-footer">

                {block name='page_footer'}

                    <!-- Footer content -->

                {/block}

            </footer>

        {/block}

    </section>

{/block}

