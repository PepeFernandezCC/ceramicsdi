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

                    {assign var="productColor" value="none"}
                    {assign var="validAspect" value=false}
                    {assign var="conversionRate" value=1}
                    {assign var="otherMaterialsArray" value=[81, 82, 88]}
                    {assign var="isByPiece" value=false}
                    {assign var="showDays" value=true}
                    {foreach from=$product.features item='feature'}
                        {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_COLOR}
                            {assign var="productColor" value=$feature.value}
                        {/if}
                    
                        {if $feature.id_feature == 52 && in_array($feature.id_feature_value, [112067, 112063, 112066, 112061, 112068, 112062, 112060, 112064])} 
                            {assign var="validAspect" value=true}
                            {assign var="validAspectId" value=$feature.id_feature_value}
                            {assign var="validAspectName" value=$feature.value}

                        {/if}

                        {if isset($feature.id_feature) && ($feature.id_feature == $FEATURE_M2_PIEZA_ID || $feature.id_feature == $FEATURE_M2_CAJA_ID)}
                            {assign var="conversionRate" value=1}
                            {assign var="productUnit" value="m²"}

                            {if $feature.id_feature == $FEATURE_M2_PIEZA_ID}
                                {assign var="productUnit" value={l s='pieces' d='Shop.Theme.Actions'}}
                            {else}
                                {assign var="conversionRate" value=$feature.value|replace:',':'.'|floatval}
                            {/if}

                            {assign var="result" value=$product.quantity * $conversionRate}

                            {if $feature.id_feature == $FEATURE_M2_CAJA_ID}
                                {assign var="result" value=$result|number_format:2:',':'.'}
                            {/if}
                        {/if}

                        {if $feature.id_feature === $FEATURE_M2_PIEZA_ID}
                            {assign var="isByPiece" value=true}
                        {/if}

                        {if $FEATURE_DIAS_PLAZO_ENTREGA_ID === $feature.id_feature}
                            {if in_array($feature.id_feature_value, ['110413', '130590'])}
                                {assign var="showDays" value=false}
                            {/if}

                            {assign var="dias_plazo" value="{$feature.value}"}

                        {/if}

                    {/foreach} 

    <section id="main">

        <meta content="{$product.url}">

        <div class="row product-container js-product-container product-bc-container">

            <div id="bread-crumps-container" class="bread-crumps margin-bc-product bc-product-position" data-color="{$productColor}" data-location="product" data-category="{$category->id}"></div>


            <div class="col-md-6 col-xs-12" id="product-images-block">

                {block name='page_content_container'}

                    <section class="page-content fix-height-477-mobile" id="content">

                        {block name='page_content'}

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

                <div class="custom-content">

                    {include file='catalog/_partials/product-flags.tpl'}

                    {if $validAspect}
                        <div id="aspecto-link" style="display: none">
                            <a href="{$link->getCategoryLinkByIdFeatureValue($validAspectId|intval)}">{$validAspectName}</a>
                        </div>
                    {/if}

                    <div id="category-link" style="display: none">
                        <a href="{$link->getCategoryLink($category->id_category|intval)}">CATEGORY_LINK</a>
                    </div>
                    

                    <div id="push-scroll-responsive-header">
                        {assign var="title_material" value=""}
                        {foreach from=$product.features item='feature'} 
                            {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MATERIAL}
                                {assign var="title_material" value="{$feature.value}"}</span>
                            {/if}
                        {/foreach}

                        {block name='page_header_container'}

                            {block name='page_header'}

                                <div class="row product-head">

                                    <div class="col-xs-12">

                                        <h1 class="h1 product-card-title">

                                            {block name='page_title'}

                                                {assign var="product_mini_title_key" value=Product::getProductMinititleKey($product.id)}

                                                <div class="product-type">
                                                    <div style="padding-right:10px;">
                                                        {l s={$product_mini_title_key} d='Shop.Theme.Catalog'}
                                                    </div>

                                                    {if isset($product.reference_to_display) && $product.reference_to_display neq ''}
                                                        <div class="product-reference">
                                                            <span>Ref: {$product.reference_to_display}</span>
                                                        </div>
                                                    {/if}
                                                </div>
                                              
                                                <div style="margin-bottom:10px">
                                                    {$product.name}
                                                </div>
                                            {/block}

                                        </h1>

                                    </div>

                                    <div class="col-xs-12">

                                        {block name='product_prices'}

                                            {include file='catalog/_partials/product-prices.tpl'}

                                        {/block}

                                    </div>

                                </div>

                                <div class="row" style="padding-top: 10px">

                                    <div class="col-xs-12">

                                        <span class="tax-message">

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
                                    {($product.availability_message|lower)|capitalize}
                                {/if}
                           
                                {foreach from=$product.features item='feature'}

                                    {if $feature.id_feature === $FEATURE_SHOW_STOCK && $feature.value == 1}
                                                                               
                                         - <strong>{$result} {$productUnit|capitalize}</strong>
                                      
                                    {/if}

                                {/foreach}

                            </span>
                        {/block}

                    </div>

                    <div>
                        <i class="fas fa-truck" style="font-size: 15px; padding-left: 2px"></i> 
                        <span style="font-size:14px; padding-left:3px">
                            {l s='estimated delivery' d='Shop.Theme.Catalog'}:
                            <strong>{$dias_plazo nofilter}{if $showDays} {l s='laborable days' d='Shop.Theme.Catalog'}{/if}</strong>
                        </span>
                    </div>
                   



                    <div class="product-information">

                        {if $product.is_customizable && count($product.customizations.fields)}

                            {block name='product_customization'}

                                {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}

                            {/block}

                        {/if}

                        {assign var="categoriasProducto" value=Product::getProductCategories($product.id)}

                        {if !($CATEGORY_INSTALACION_ID|in_array:$categoriasProducto) && !($CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto)}
                                           
                            <div class="product-traits-box">

                                <hr>

                                <div class="rowTitle" style="font-size: 15px">
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

                            </div>
                        {/if}

                        {block name='product_tabs'}


                            <div class="product-accordion">

                                <button class="accordion-button">

                                     <h2 class="accordion-item-h2">{l s='Measures' d='Shop.Theme.Catalog'}</h2>

                                </button>

                                <div class="panel">

                                    alto, ancho, espesor y peso

                                </div>

                                <button class="accordion-button">

                                    {if $category->id_category == $CATEGORY_INSTALACION_Y_MONTAJE_ID}

                                         <h2 class="accordion-item-h2">{l s='How to use' d='Shop.Theme.Catalog'}</h2>

                                    {else}

                                         <h2 class="accordion-item-h2">{l s='Use and maintenance' d='Shop.Theme.Catalog'}</h2>

                                    {/if}

                                </button>

                                <div class="panel">

                                    {$productUsoMantenimiento nofilter}

                                </div>

                                <button class="accordion-button">

                                    <h2 class="accordion-item-h2">{l s='Description' d='Shop.Theme.Catalog'}</h2>

                                </button>

                                <div class="panel">

                                    {block name='product_details'}

                                        {$product.description nofilter}


                                        <p class="product-feature-espesor font-weight-bold"></p>

                                    {/block}

                                </div>

                                <button class="accordion-button">

                                     <h2 class="accordion-item-h2">{l s='Technical characteristics' d='Shop.Theme.Catalog'}</h2>

                                </button>

                                <div class="panel">

                                    {block name='product_details'}

                                        {include file='catalog/_partials/product-details.tpl'}

                                    {/block}

                                </div>

                            </div>

                        {/block}

                    </div>

                </div>

                <div class="newCalculatorBox">

                    {if !($CATEGORY_INSTALACION_ID|in_array:$categoriasProducto) && !($CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto)}
                        {block name='product_discounts'}

                            {include file='catalog/_partials/product-discounts.tpl'}

                        {/block}

                        <div class="rowTitle" style="font-size: 15px">
                            {l s='Price Calculator' d='Shop.Theme.Catalog'}:
                        </div>

                    {/if}

                    <div class="product-actions js-product-actions" style="padding-top:15px">

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



                                    {block name='product_add_to_cart'}

                                        {include file='catalog/_partials/product-add-to-cart.tpl'}

                                    {/block}

                            </form>

                        {/block}

                    </div>



                </div>

                <div class="productExtraContent">
                    {assign var="dias_plazo" value=""}
                    {assign var="texto_muestra" value=""}
                    {assign var="junta_recomendada" value="0"}
                    {assign var="hasSample" value=true}
                    {foreach from=$product.features item='feature'}

                        {if $feature.id_feature === $FEATURE_M2_PIEZA_ID}

                            {assign var="isByPiece" value=true}

                        {/if}
                        {if $feature.id_feature === $FEATURE_SAMPLE_AVAILABLE}

                            {assign var="hasSample" value=false}
                            {assign var="sampleTextWarning" value=$feature.value}

                        {/if}

                    {/foreach}
                    {foreach from=$product.grouped_features item=feature}
                        {if $FEATURE_JUNTA_RECOMENDADA_ID === $feature.id_feature}
                            {assign var="junta_recomendada" value="{$feature.value}"}
                        {elseif $FEATURE_DIAS_PLAZO_ENTREGA_ID === $feature.id_feature}
                            {assign var="dias_plazo" value="{$feature.value}"}
                        {elseif $FEATURE_TEXTO_MUESTRA_ID === $feature.id_feature}
                            {assign var="texto_muestra" value="{$feature.value}"}
                        {/if}
                    {/foreach}

                    {* PLANATEC *}
                        <div id="transport-wrapper" class="row mx-auto" style="margin-top:25px;margin-bottom:10px">
                            <div class="col-xl-12 col-xs-12">
                                <div class="product-transport">
                                    <div style="width: 100%;">
                                        {if $dias_plazo !== '' or $texto_muestra !== ''}
                                                        
                                            {if $texto_muestra !== ''}
                                                                
                                                <div>
                                                    <div style="text-transform: uppercase; font-weight: 500">
                                                        <span style="color: #a3a3a3; font-size: large "><i class="fa-solid fa-circle-exclamation"></i></span> {l s='Samples' d='Shop.Theme.Catalog'}
                                                    </div>
                                                    <div>
                                                        {if $hasSample}
                                                            {$productTransportSamples|replace:'{texto_muestra}':$texto_muestra nofilter}
                                                        {else}
                                                            <p>{$sampleTextWarning}<p>
                                                        {/if}
                                                    </div>
                                                </div>
                                            {/if}
                                            {if $dias_plazo !== ''}
                                                <div>
                                                    <div style="text-transform: uppercase; font-weight: 500">
                                                        <span style="color: #a3a3a3; font-size: large"><i class="fa-solid fa-truck"></i></span> {l s='Transport' d='Shop.Theme.Catalog'}
                                                    </div>
                                                    <div>{$productTransport|replace:'{dias_plazo}':$dias_plazo nofilter}</div>
                                                </div>
                                            {/if}
                                        {/if}
                                        {if $product.attachments}
                                            {foreach from=$product.attachments item=attachment}
                                                {if ($attachment.name == 'PDF Técnico' && $language.id == 1)
                                                    || ($attachment.name == 'PDF technique' && $language.id == 2)
                                                    || ($attachment.name == 'Technical PDF' && $language.id == 3)
                                                    || ($attachment.name == 'PDF Technical' && $language.id == 4)
                                                    || ($attachment.name == 'PDF Tecnico' && $language.id == 5)
                                                    || ($attachment.name == 'PDF-techniek' && $language.id == 6)
                                                }
                                                    <div id="product-attachment-pdf-tecnico">
                                                        <div>
                                                            <span style="color: #a3a3a3; font-size: large"><i class="fa-solid fa-file"></i></span>
                                                            <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                                                target="_blank"
                                                                style="text-transform: uppercase;font-weight: 500;font-size: 14px !important;">
                                                                    {l s='Download' d='Shop.Theme.Actions'} {l s='Technical PDF' d='Shop.Theme.Catalog'}
                                                            </a>
                                                        </div>
                                                    </div>
                                                {/if}
                                            {/foreach}
                                        {/if}

                                        {$junta_recomendada_nombre = Product::getProductName($junta_recomendada)}
                                        {$imageCoverUrl= Product::getImageByPosition(1, $junta_recomendada)}
                                        {$imageDustUrl= Product::getImageByPosition(2, $junta_recomendada)}
                                        {assign var='hasCover' value=true}
                                        {assign var='hasDust' value=true}
                                        {if strpos($imageCoverUrl, 'no-hay-cover') !== false}
                                            {assign var='hasCover' value=false}
                                        {/if}

                                        {if strpos($imageDustUrl, 'no-hay-cover') !== false}
                                            {assign var='hasDust' value=false}
                                        {/if}

                                        {if !empty($junta_recomendada) && !empty($junta_recomendada_nombre)}
                                            <div style="padding-top:25px">
                                                <div id="recommended-board-wrapper" style="text-transform: uppercase; font-weight: 500">
                                                    <span style="padding-right:10px">{l s='Recommended board' d='Shop.Theme.Catalog'}{l s=':' d='Shop.Theme.Catalog'}</span> 
                                                    <a href="{Context::getContext()->link->getProductLink($junta_recomendada)}" style="font-size:.92rem;">
                                                        {$junta_recomendada_nombre}
                                                    </a>
                                                </div>
                                                <div class="mobile-text-center">
                                                    <a href="{Context::getContext()->link->getProductLink($junta_recomendada)}">
                                                        {if $hasCover}
                                                            <img loading="lazy" src="{$imageCoverUrl}" style="max-width:155px" alt="{$junta_recomendada_nombre} - cover"/>
                                                        {/if}
                                                        {if $hasDust}
                                                            <img loading="lazy" src="{$imageDustUrl}" style="max-width:155px" alt="{$junta_recomendada_nombre} - sample"/>
                                                        {/if}
                                                    </a>
                                                </div>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>

                    {* END PLANATEC *}

                    {block name='product_additional_info'}

                                        {include file='catalog/_partials/product-additional-info.tpl'}

                    {/block}

                    {block name='product_refresh'}{/block}
                </div>

            </div>



        </div>

        {block name='product_accessories'}

            {if $accessories}

                <section id="related-products-desktop" class="mobile-product-accessories clearfix">

                    <h2 class="h5 text-uppercase">{l s='Recommended combinations' d='Shop.Theme.Catalog'}</h2>

                        <div class="custom-featured-products container-fluid">

                             {hook h='displayRelatedProducts' product=$product}

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

