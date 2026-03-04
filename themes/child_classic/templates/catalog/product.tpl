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

                    {assign var="id_cart" value=Context::getContext()->cart->id} 
                    {assign var="productColor" value="none"}
                    {assign var="validAspect" value=false}
                    {assign var="conversionRate" value=1}
                    {assign var="otherMaterialsArray" value=[81, 82, 88]}
                    {assign var="isByPiece" value=false}
                    {assign var="show_stock" value=false}
                    {assign var="categoriasProducto" value=Product::getProductCategories($product.id)}
                    {assign var="display_custom_stock_msg" value="-"}
                    {assign var="custom_stock_msg" value="-"}
                    {assign var="custom_out_of_stock_msg" value="-"}
                    {if $CATEGORY_INSTALACION_ID|in_array:$categoriasProducto || $CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto || $CATEGORY_ARTICULATIONS|in_array:$categoriasProducto}
                        {assign var="normalSell" value=true}
                    {else}
                        {assign var="normalSell" value=false}
                    {/if}

                    
                    {assign var="customerShowTax" value=customer::getCustomerShowTax($customer.id)}

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

                            {assign var="dias_plazo" value="{$feature.value}"}

                        {/if}

                        {if $feature.id_feature === $FEATURE_SHOW_STOCK && $feature.value == 1}
                                                                               
                            {assign var="show_stock" value=true}
                                      
                        {/if}
                        {if $feature.id_feature === $FEATURE_CUSTOM_STOCK}
                            {assign var="custom_stock_msg" value="{$feature.value}"}
                        {/if}
                        {if $feature.id_feature === $FEATURE_CUSTOM_OUT_OF_STOCK}
                            {assign var="custom_out_of_stock_msg" value="{$feature.value}"}
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

                                    <div class="col-md-6 col-xs-12 block-title">

                                        {assign var="product_mini_title_key" value=Product::getProductMinititleKey($product.id)}

                                        <div>
                                            <h1 class="h1 product-card-title">
                                                <div class="product-type" style="padding-bottom: 15px;">
                                                    {l s={$product_mini_title_key} d='Shop.Theme.Catalog'}
                                                </div>
                                                <div style="margin-bottom:10px; text-transform:uppercase">
                                                    {$product.name}
                                                </div>
                                            </h1>
                                        </div>
                                        
                                        {block name='page_title'}
                                            {if isset($product.reference_to_display) && $product.reference_to_display neq ''}
                                                <div class="product-reference">
                                                    <span>{$product.reference_to_display}</span>
                                                </div>
                                            {/if}
                                        {/block}

                                    </div>


                                    <div class="col-md-6 col-xs-12 block-pricing">
                                        <div>
                                            <div>
                                                {block name='product_prices'}
                                                    {include file='catalog/_partials/product-prices.tpl'}
                                                {/block}
                                            </div>
                                            {assign var="productType" value=Product::getProductUnit($product.id)}
                                            <div class="final-price-taxed">
                                                {if $productType != "UNIT"}
                                                    <div class="minimal-price">
                                                        
                                                        {assign var="minimalPrice" value=Product::getMinimalPriceTemplate($product.id, $customer.id)}
                                                        
                                                        <div style="padding-right: 10px">
                                                            <span>
                                                                {$minimalPrice} €/<span style="text-transform:capitalize">{if $productType == "PIECE"}{l s='piece' d='Shop.Theme.Catalog'}{else}{l s='box' d='Shop.Theme.Catalog'}{/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                {/if}
                                                <div class="tax-message-box alignText">
                                                    <span class="tax-message">

                                                        {if $customerShowTax}

                                                            {l s='Tax included' d='Admin.Global'}

                                                        {else}

                                                            {l s='Tax excluded' d='Admin.Global'}

                                                        {/if}

                                                    </span>
                                                </div>
                                            </div>
                                        
                                        </div>

                                    </div>

                                </div>



                            {/block}

                        {/block}

                        <div id="product-rating"> {hook h='displayProductAdditionalInfo' product=$product} </div>

                        <hr>



                        {block name='product_availability'}
                            
                            <span id="product-availability" class="js-product-availability" style="font-size:14px;">
                            
                                {if $product.show_availability && $product.availability_message}

                                    {if $product.availability == 'available' and $product.quantity > 0}
                                        <i class="material-icons rtl-no-flip product-available">&#xE5CA;</i> 
                                        {assign var="display_custom_stock_msg" value="{$custom_stock_msg}"}
                                    {elseif $product.availability == 'last_remaining_items'}
                                        <i class="material-icons product-last-items">&#xE002;</i>
                                        {assign var="display_custom_stock_msg" value="{$custom_stock_msg}"}
                                    {else}
                                        <i class="material-icons product-last-items">&#xE002;</i>
                                        {assign var="display_custom_stock_msg" value="{$custom_out_of_stock_msg}"}
                                    {/if}

                                    {if $display_custom_stock_msg != "-" && $display_custom_stock_msg != ""}
                                        {($display_custom_stock_msg|upper)}
                                    {else}
                                        {($product.availability_message|upper)}
                                    {/if}
                                    
                                    
                                {/if}
                           
                            

                                {if $show_stock}
                                                                               
                                    - <strong>{$result} {$productUnit}</strong>
                                      
                                {/if}
                         

                            </span>
                        {/block}

                    </div>

                    <div>
                        <i class="fa-regular fa-clock" style="font-size: 14px; padding-left: 2px; color:orange"></i> 
                        <span style="font-size:14px; padding-left:3px;">
                            <span style="text-transform: uppercase">{l s='estimated delivery' d='Shop.Theme.Catalog'}:</span>
                            <strong>{$dias_plazo nofilter}</strong>
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
                                           
                            <div class="product-traits-box" style="display:none">

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

                                     <h2 class="accordion-item-h2">{l s='Measurements' d='Shop.Theme.Catalog'}</h2>

                                </button>
                               <div class="panel">

                                    
                                    {block name='product_measures'}

                                        {include file='catalog/_partials/product-accordion-measures.tpl'}

                                    {/block}

                                </div>

                                <button class="accordion-button">

                                    {if $category->id_category == $CATEGORY_INSTALACION_Y_MONTAJE_ID}

                                         <h2 class="accordion-item-h2">{l s='How to use' d='Shop.Theme.Catalog'}</h2>

                                    {else}

                                         <h2 class="accordion-item-h2">{l s='Use and maintenance' d='Shop.Theme.Catalog'}</h2>

                                    {/if}

                                </button>

                                <div class="panel">

                                    {block name='product_use'}

                                        {include file='catalog/_partials/product-accordion-use.tpl'}

                                    {/block}

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

                                {if $product.attachments}
                                    <button class="accordion-button">
                                        <h2 class="accordion-item-h2">{l s='Downloadable content' d='Shop.Theme.Catalog'}</h2>
                                    </button>
                                    <div class="panel">
                                        {block name='downloadable_content'}
                                            <div class="tab-panel" id="product-accordion-downloads" role="tabpanel">
                                                {foreach from=$product.attachments item=attachment}
                                                    {if ($attachment.name == 'PDF Técnico' && $language.id == 1)
                                                        || ($attachment.name == 'PDF technique' && $language.id == 2)
                                                        || ($attachment.name == 'Technical PDF' && $language.id == 3)
                                                        || ($attachment.name == 'PDF Technical' && $language.id == 4)
                                                        || ($attachment.name == 'PDF Tecnico' && $language.id == 5)
                                                        || ($attachment.name == 'PDF-techniek' && $language.id == 6)
                                                    }
                                                        <div id="product-attachment-pdf-tecnico" style="padding-left: 30px; padding-bottom: 14px;">
                                                            <div>
                                                                <span style="color: #a3a3a3; font-size: large"><i class="fa-solid fa-file"></i></span>
                                                                <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                                                    target="_blank"
                                                                    style="text-transform: uppercase;font-weight: 500;font-size: 14px !important;">
                                                                       {* {l s='Download' d='Shop.Theme.Actions'}*} {l s='Technical PDF' d='Shop.Theme.Catalog'}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                {/foreach}
                                            </div>
                                        {/block}
                                    </div>
                                {/if}

                            </div>

                        {/block}

                    </div>

                </div>

                <div class="newCalculatorBox">

                    {if !($CATEGORY_INSTALACION_ID|in_array:$categoriasProducto) && !($CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto)}

                        <div class="rowTitleCalculator">
                            <div class="calculator-title">
                                <div><span class="calculator-icon"><i class="fa-solid fa-calculator"></i></span></div>
                                <div><span>{l s='Price Calculator' d='Shop.Theme.Catalog'}:</span></div>
                            </div>
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

                <div class="productExtraContent no-padding-desktop {if $normalSell}mt-125-desktop{else}mt-90-desktop{/if}">
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

                        <div class="row" style="display:flex;justify-content:center">
                            {assign var="samplesInCart" value=0}
                            {assign var="samplesInCart" value=Cart::getSamplesNumberInCartStatic($id_cart)}
                            {assign var="maxProductsInCart" value=false}
                            {if $samplesInCart >= 8}
                                {$maxProductsInCart = true}
                            {/if}
                            <div class ="col-xl-9 col-xs-12">
                                {if $maxProductsInCart}
                                    <p id="max-samples-reached" style="text-align: center; font-weight: bold; color: red; margin-top: 10px; font-size: 12px; margin-bottom: 0;">
                                {else}
                                    <p id="max-samples-reached" style="text-align: center; font-weight: bold; color: red; margin-top: 10px; font-size: 12px; margin-bottom: 0; display:none;">
                                {/if}
                                    {l s='You have reached the maximum number of samples allowed in the same purchase, if necessary, contact customer service.' d='Shop.Theme.Global'}
                                </p>
                            </div>
                        </div>

                        <div id="transport-wrapper" class="row mx-auto no-padding-desktop" style="margin-bottom:10px">
                            <div class="col-xl-12 col-xs-12 no-padding-desktop" style="padding-top:0">
                                <div class="product-transport">
                                    <div style="width: 100%;">
                                                      
                                        {if $texto_muestra !== ''}
                                                                
                                           <a href="#" id="openModal" data-toggle="modal" data-target="#sampleModal">
                                                <div class="modalBanner" style="font-weight:bold; padding-top:10px; padding-bottom:10px">
                                                    <div style="width: 50px;"> 
                                                        <span>
                                                            <img class="baner-icon" loading="lazy" src="/themes/child_classic/assets/img/web/icons/sample-ico.png" alt="icon sample"/>
                                                        </span> 
                                                    </div>
                                                    <div class="whyordersampletext">
                                                        <div>
                                                            {l s='Why request a sample?' d='Shop.Theme.Catalog'}
                                                        </div>
                                                        <div>
                                                            <span style="color:#eac133; font-size:30px"><i class="fa-solid fa-angle-right"></i></span>  
                                                        </div>
                                                    </div>    
                                                </div>
                                            </a>
                                      
                                        {/if}
                                        


                                        {if !empty($junta_recomendada) && $junta_recomendada != 0}
                                            {$cardBoard = Product::getProductCard($junta_recomendada)}
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

                                        
                                            <hr>
                                            {assign var="complements" value=Product::getComplementProducts($product.id)}
                                            <div id="board-section" style="padding:25px 0">
                                                <div class="recomendation-board {if empty($complements)}w100{/if}">
                                                    <div class="cards-slide">
                                                        <div class="pc-carousel-card">
                                                            <h2 class="product_H2">{l s='Recommended board' d='Shop.Theme.Catalog'}</h2>
                                                            <div class="board-card">
                                                                <div class="board-img-carousel" data-img-carousel>
                                                                    <button type="button" class="carousel-btn prev" data-img-prev aria-label="Previous">‹</button>

                                                                    <div class="carousel-track" data-img-track>
                                                                        {if $hasCover}
                                                                            <img loading="lazy" src="{$imageCoverUrl}" alt="{$cardBoard.name} - cover"/>
                                                                        {/if}
                                                                        {if $hasDust}
                                                                            <img loading="lazy" src="{$imageDustUrl}"  alt="{$cardBoard.name} - sample"/>
                                                                        {/if}
                                                                    </div>

                                                                    <button type="button" class="carousel-btn next" data-img-next aria-label="Next">›</button>
                                                                </div>

                                                                <div class="board-info">
                                                                    
                                                                    <div class="board-title">{$cardBoard.name}</div>
                                                                    <div class="product-reference"><span class="board-reference">Ref: {$cardBoard.ref}</span></div>
                                                                    <div>
                                                                        <a href="{Context::getContext()->link->getProductLink($junta_recomendada)}" style="font-size:.92rem;">
                                                                            <button class="board-button">{l s='View Product' d='Shop.Theme.Catalog'}</button>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>                                            
                                                </div>
                                                
                                                {if !empty($complements)}
                                                    <div id="complement-products-box" class="pc-cards-carousel" data-cards-carousel>
                                                        <button type="button" class="cards-btn prev" data-cards-prev aria-label="Previous">⟨</button>
                                                        <div class="cards-viewport">
                                                            <div><h2 class="product_H2">{l s='Producto Complementario' d='Shop.Theme.Catalog'}</h2></div>
                                                            <div class="cards-track">
                                                                
                                                                {foreach from=$complements item='complement'}  

                                                                    <div class="cards-slide">
                                                                        <div class="pc-carousel-card">
                                                                            <div class="board-card">
                                                                                <div class="esquina-img-carousel" data-img-carousel>
                                                                                    <button type="button" class="carousel-btn prev" data-img-prev aria-label="Previous">‹</button>

                                                                                    <div class="carousel-track" data-img-track>
                                                                                    {foreach from=$complement.images item='imageItem'}
                                                                                        <img loading="lazy" src="{$imageItem.url}"  alt="{$imageItem.legend}"/>
                                                                                    {/foreach}
                                                                                    </div>

                                                                                    <button type="button" class="carousel-btn next" data-img-next aria-label="Next">›</button>
                                                                                </div>

                                                                            
                                                                                <div class="board-info">
                                                                                    
                                                                                    <div class="board-title">{$complement.name}</div>
                                                                                    <div class="product-reference"><span class="board-reference">Ref: {$complement.reference}</span></div>
                                                                                    <div>
                                                                                    <a href="{Context::getContext()->link->getProductLink($complement.id)}" style="font-size:.92rem;">
                                                                                        <button class="board-button">{l s='View Product' d='Shop.Theme.Catalog'}</button>
                                                                                    </a>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                {/foreach}
                                                            </div>
                                                        </div>
                                                        <button type="button" class="cards-btn next" data-cards-next aria-label="Next">⟩</button>
                                                    </div>
                                                {/if}
                                            </div>
                                            
                                            <hr>

                                        {/if}

                                       
                                        <div class="modals-block">

                                            <h2 class="product_H2">{l s='Terms of Purchase' d='Shop.Theme.Catalog'}</h2>

                                           
                                            <a href="#" id="openModal" data-toggle="modal" data-target="#transportModal">
                                                <div class="modalBanner" style="padding: 15px">
                                                    <div style="width: 50px;"> 
                                                        <span style="font-size:30px">
                                                            <i class="fa-regular fa-file-lines"></i>
                                                        </span> 
                                                    </div>
                                                    <div style="font-weight:500">{l s='Sales Conditions' d='Shop.Theme.Catalog'} </div>
                                                </div>
                                            </a>
                                            
                                            <a href="#" id="openModal" data-toggle="modal" data-target="#refundModal">
                                                <div class="modalBanner">
                                                    <div style="width: 50px;"> 
                                                        <span>
                                                            <img loading="lazy" class="baner-icon" src="/themes/child_classic/assets/img/web/icons/refund-ico.png" alt="icon refund"/>
                                                        </span>
                                                    </div>
                                                    <div style="font-weight:500">{l s='Returns / issues' d='Shop.Theme.Catalog'} </div>                                              
                                                </div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    {* END PLANATEC *}


                    {block name='product_refresh'}{/block}
                </div>

                <div id="product-review-block">
                    {hook h='displayCcProductReviews' product=$product}
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

        {block name='sample_modal'}
            {include file='catalog/_partials/_modal-samples.tpl'}
        {/block}

        {block name='transport_modal'}
            {include file='catalog/_partials/_modal-transport.tpl'}
        {/block}

        {block name='refund_modal'}
            {include file='catalog/_partials/_modal-refund.tpl'}
        {/block}

        {block name='payment_modal'}
            {include file='catalog/_partials/_modal-payment.tpl'}
        {/block}


    </section>

{/block}

