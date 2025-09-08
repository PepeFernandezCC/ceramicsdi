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

{assign var="m2_quantity" value="0"}       
{assign var="junta_recomendada" value="0"}
{assign var="feature_medida" value=""}
{assign var="is_sample" value=false}

{foreach from=Product::getFrontFeaturesStatic($language.id, $product.id) item='feature'}
    {if $FEATURE_M2_CAJA_ID === $feature.id_feature}
        {assign var="m2_quantity" value="{$feature.value}"}
    {elseif $FEATURE_JUNTA_RECOMENDADA_ID === $feature.id_feature}
        {assign var="junta_recomendada" value="{$feature.value}"}
    {elseif isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}
        {assign var="feature_medida" value="{$feature.value}"}
    {/if}
{/foreach}


{assign var="categoriasProducto" value=Product::getProductCategories($product.id)}

{assign var="normalSell" value=false}

{if $CATEGORY_INSTALACION_ID|in_array:$categoriasProducto || $CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto || $CATEGORY_ARTICULATIONS|in_array:$categoriasProducto}

    {assign var="normalSell" value=true}

{/if}



<div id="blockcart-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="padding-right: 0 !important">

    <div class="modal-dialog" role="document" style="max-width: 700px">

        <div class="modal-content">

            <div class="modal-header" style="border-bottom: 1px solid black !important">

                <button type="button" class="close" data-dismiss="modal"

                        aria-label="{l s='Close' d='Shop.Theme.Global'}">

                    <span aria-hidden="true"><i class="material-icons">close</i></span>

                </button>

                <div class="popup-header-title">
                    <div style="margin-right:10px">
                        <i class="material-icons rtl-no-flip">&#xE876;</i>
                    </div>
                    <div>
                        <h4 class="modal-title h6 text-sm-center" id="myModalLabel">
                            {l s='Product successfully added to your shopping cart' d='Shop.Theme.Checkout'}
                        </h4>
                    </div>
                </div>

            </div>

            <div class="modal-body" style="padding:15px !important">

                <div class="row">

                    <div class="col-md-6">

                        <div class="row">

                            <div class="col-md-6 col-xs-6">

                                {if $product.default_image}

                                    <img

                                            src="{$product.default_image.medium.url}"

                                            data-full-size-image-url="{$product.default_image.large.url}"

                                            title="{$product.default_image.legend}"

                                            alt="{$product.default_image.legend}"

                                            loading="lazy"

                                            class="product-image"

                                    >

                                {else}

                                    <img

                                            src="{$urls.no_picture_image.bySize.medium_default.url}"

                                            loading="lazy"

                                            class="product-image"

                                    />

                                {/if}

                            </div>

                            <div class="col-md-6 col-xs-6" style="margin-top: 25px; padding-left: 0;">

                                <p class="popup-reference">{$product.reference}</p>

                                <h6 class="h6 product-name">{$product.name}</h6>

                                <p class="feature-medida">{$feature_medida}</p>

                                    
                                {* PLANATEC *}

                                {foreach from=$product.attributes key="attribute" item="value"}

                                    {if ($attribute == 'Muestra' and $value == 'Sí')
                                        || ($attribute == 'Échantillon' and $value == 'Oui')
                                        || ($attribute == 'Sample' and $value == 'Yes')
                                        || ($attribute == 'Muster' and $value == 'Ja')
                                        || ($attribute == 'Amostra' and $value == 'Sim')
                                        || ($attribute == 'Voorbeeld' and $value == 'Ja')
                                    }
                                        {assign var="is_sample" value=true}

                                        <div class="product-line-info {$attribute|lower}">

                                            <span class="label">{$attribute}</span>

                                        </div>

                                    {/if}

                                {/foreach}

                                {* END PLANATEC *}

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="cart-content">

                            {*
                            {if $cart.products_count > 1}

                                <p class="cart-products-count">{l s='There are %products_count% items in your cart.' sprintf=['%products_count%' => $cart.products|count] d='Shop.Theme.Checkout'}</p>

                            {else}

                                <p class="cart-products-count">{l s='There is %products_count% item in your cart.' sprintf=['%products_count%' =>$cart.products|count] d='Shop.Theme.Checkout'}</p>

                            {/if}
                            *}

                            <p>
                            {if $normalSell}

                                    <span class="label">{l s='Units' d='Shop.Theme.Catalog'}:&nbsp;</span>

                            {else}
                                   
				                {if $isByPiece}

                                        <span class="label">{l s='Pieces' d='Shop.Theme.Catalog'}:</span>

                                {else}

                                        <span class="label">{l s='Boxes' d='Shop.Theme.Catalog'}:</span>

                                {/if}

                            {/if}

                                <span class="subtotal value">{$product.quantity}
                            {if $normalSell}

                                {l s='Units' d='Shop.Theme.Catalog'}
                            {else}
                                   
				                {if $isByPiece}

                                    {l s='Pieces' d='Shop.Theme.Catalog'}

                                {else}

                                    {l s='Boxes' d='Shop.Theme.Catalog'}

                                {/if}

                            {/if}</span>
                            </p>

                            {if !$normalSell && !$is_sample }
                                <p>
                                    <span class="label">{l s='Quantity' d='Shop.Theme.Catalog'}:</span>
                                    <span class="subtotal value">
                                        {if !$isByPiece}
                                            {math equation="x * y" x={$m2_quantity|replace:',':'.'|floatval} y=$product.quantity|floatval} m<sup>2</sup>
                                        {else}
                                            {$product.quantity} {l s='Pieces' d='Shop.Theme.Actions'}
                                        {/if}
                                    </span>

                                </p>
                            {/if}

                            <p>
                                <span class="label">{l s='Subtotal:' d='Shop.Theme.Checkout'}</span>
                                <span class="subtotal value">{$cart.subtotals.products.value}</span>
                            </p>

                            {if $cart.subtotals.shipping.value}

                                <p>
                                  
                                    <span class="label">{l s='Shipping:' d='Shop.Theme.Checkout'}</span>
                                    <span class="shipping value">
                                        {assign var=free_fields value=["Gratis", "gratuit", "Free", "kostenlos", "Grátis", "Gratuit"]}
                                        {if $cart.subtotals.shipping.value === ' ' || in_array($cart.subtotals.shipping.value, $free_fields)}
                                            {l s='Pending' d='Shop.Theme.Checkout'}
                                        {else}
                                            {$cart.subtotals.shipping.value} {hook h='displayCheckoutSubtotalDetails' subtotal=$cart.subtotals.shipping}
                                        {/if}
                                    </span>

                                </p>

                            {/if}



                            {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}

                                <p>

                                    <span>{$cart.totals.total.label}&nbsp;{$cart.labels.tax_short}</span>&nbsp;<span>{$cart.totals.total.value}</span>

                                </p>

                                <p class="product-total" style="background-color: #eee">

                                    <span class="label" style="font-size:16px">{$cart.totals.total_including_tax.label}</span>&nbsp;
                                    
                                    <span class="value" style="font-size:16px">{$cart.totals.total_including_tax.value}</span>
                                
                                </p>

                            {else}

                                <p class="product-total" style="background-color: #eee">
                                
                                    <span class="label" style="font-size:16px">{$cart.totals.total.label}&nbsp;{if $configuration.taxes_enabled}{$cart.labels.tax_short}{/if}</span>&nbsp;
                                    
                                    <span class="value" style="font-size:16px">{$cart.totals.total.value}</span>
                                
                                </p>

                            {/if}



                            {if $cart.subtotals.tax}

                                <p class="product-tax">
                                
                                    {l s='%label%:' sprintf=['%label%' => $cart.subtotals.tax.label] d='Shop.Theme.Global'}&nbsp;

                                    <span class="value">{$cart.subtotals.tax.value}</span>
                                
                                </p>

                            {/if}

                            {hook h='displayCartModalContent' product=$product}



                        </div>

                    </div>

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
                    <div class="clearfix" style="margin-bottom: 20px"></div>

                </div>

                {if !$is_sample}
                    <div class="row">
                    
                        {if !empty($junta_recomendada) && !empty($junta_recomendada_nombre)}
                            <div class="popup-joint-box">
                                <div class="popup-joint-view col-md-6 col-xs-12">
                                        
                                    {if $hasCover}
                                        <a href="{Context::getContext()->link->getProductLink($junta_recomendada)}" >
                                            <img class="popup-joint-image" src="{$imageCoverUrl}" alt="{$junta_recomendada_nombre} - cover"/>
                                        </a>
                                    {/if}
                                    {if $hasDust}
                                        <a href="{Context::getContext()->link->getProductLink($junta_recomendada)}" >
                                            <img class="popup-joint-image" src="{$imageDustUrl}" alt="{$junta_recomendada_nombre} - sample"/>
                                        </a>
                                    {/if}
                                            
                                </div>

                                <div class="popup-joint-cta col-md-6 col-xs-12">
                                    <p><span class="popup-joint-cta-title">¡{l s='Complete your project with the perfect joint' d='Shop.Theme.Checkout'}!</span></p>
                                    <p><span class="popup-joint-cta-text">{l s='At Ceramic Connection, we select the best joint for each tile' d='Shop.Theme.Checkout'}</span></p>
                                    <p>
                                        <a href="{Context::getContext()->link->getProductLink($junta_recomendada)}" class="black-link">
                                            <span>{$junta_recomendada_nombre}</span>
                                        </a>
                                    </p>
                                </div>
                                    
                            </div>
                        {/if}
                    </div>
                {/if}
            

                <div class="row">

                    <div class="popup-content-btn">

                        <div style="margin: 5px">
                            <a href="#" style="text-decoration: underline" data-dismiss="modal">
                                {l s='Continue shopping' d='Shop.Theme.Actions'}
                            </a>
                        </div>

                        <div style="margin: 5px">
                            <a href="{$cart_url}" class="btn btn-primary" style="padding: 10px 19px; color:white; background-color: black">
                                <i class="material-icons rtl-no-flip" style="font-size: 15px; font-weight:bolder; color:white">&#xE876;</i>{l s='Proceed to checkout' d='Shop.Theme.Actions'}
                            </a>
                        </div>

                    </div>
                </div>

            </div>
            {hook h='displayCartModalFooter' product=$product}
        </div>
    </div>
</div>

