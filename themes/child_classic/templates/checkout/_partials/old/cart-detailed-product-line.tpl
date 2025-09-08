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

<div class="product-line-grid">



    {assign var="isByPiece" value=false}

    {foreach from=$product.features item='feature'}

        {if $feature.id_feature === $FEATURE_M2_PIEZA_ID}

            {assign var="isByPiece" value=true}

        {/if}

    {/foreach}



    {assign var="categoriasProducto" value=Product::getProductCategories($product.id)}

    {assign var="normalSell" value=false}

    {if $CATEGORY_INSTALACION_ID|in_array:$categoriasProducto || $CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto || $CATEGORY_ARTICULATIONS|in_array:$categoriasProducto}

        {assign var="normalSell" value=true}

    {/if}

    {assign var="esMuestra" value=false}

    {foreach from=$product.attributes key="attribute" item="value"}

        {if ($attribute == 'Muestra' and $value == 'Sí')
            || ($attribute == 'Échantillon' and $value == 'Oui')
            || ($attribute == 'Sample' and $value == 'Yes')
            || ($attribute == 'Muster' and $value == 'Ja')
            || ($attribute == 'Amostra' and $value == 'Sim')
            || ($attribute == 'Voorbeeld' and $value == 'Ja')
        }

            {$esMuestra = true}
            {assign var="sampleText" value=$attribute}

        {/if}

    {/foreach}



    <div class="product-line-grid-right {if $esMuestra}product-muestra{/if} product-line-actions {* PLANATEC col-sm-6 *} col-xs-12">
        
        <div class="row hide-phone fd-initial">

            <div class="col-md-4 col-xs-12 image-row">

                <span class="product-image media-middle">

                    {if $product.default_image}

                      <img src="{$product.default_image.bySize.home_default.url}" alt="{$product.name|escape:'quotes'}" loading="lazy">

                    {else}

                      <img src="{$urls.no_picture_image.bySize.cart_default.url}" loading="lazy" alg="default image"/>

                    {/if}

                </span>

                <div>

                    <span class="reference">{$product.reference_to_display}</span>

                    <div class="product-line-info">

                        <a class="label product-title" href="{$product.url}" data-id_customization="{$product.id_customization|intval}">{$product.name}</a>

                    </div>


                    {foreach from=Product::getFrontFeaturesStatic($language.id, $product.id) item='feature'}

                        {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}

                            <br>

                            <p class="feature-medida">

                                {$feature.value}

                            </p>

                        {/if}

                    {/foreach}

                    {if is_array($product.customizations) && $product.customizations|count}

                        <br>

                        {block name='cart_detailed_product_line_customization'}

                            {foreach from=$product.customizations item="customization"}

                                <a href="#" data-toggle="modal"

                                   data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>

                                <div class="modal fade customization-modal"

                                     id="product-customizations-modal-{$customization.id_customization}" tabindex="-1"

                                     role="dialog"

                                     aria-hidden="true">

                                    <div class="modal-dialog" role="document">

                                        <div class="modal-content">

                                            <div class="modal-header">

                                                <button type="button" class="close" data-dismiss="modal"

                                                        aria-label="{l s='Close' d='Shop.Theme.Global'}">

                                                    <span aria-hidden="true">&times;</span>

                                                </button>

                                                <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>

                                            </div>

                                            <div class="modal-body">

                                                {foreach from=$customization.fields item="field"}

                                                    <div class="product-customization-line row">

                                                        <div class="col-sm-3 col-xs-4 label">

                                                            {$field.label}

                                                        </div>

                                                        <div class="col-sm-9 col-xs-8 value">

                                                            {if $field.type == 'text'}

                                                                {if (int)$field.id_module}

                                                                    {$field.text nofilter}

                                                                {else}

                                                                    {$field.text}

                                                                {/if}

                                                            {elseif $field.type == 'image'}

                                                                <img src="{$field.image.small.url}" loading="lazy">

                                                            {/if}

                                                        </div>

                                                    </div>

                                                {/foreach}

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            {/foreach}

                        {/block}

                    {/if}

                </div>



            </div>

            <div class="col-md-1" {if !$esMuestra}style="padding-right: 0"{/if}>

                <div class="hidden-sm-up">

                    {l s='Boxes' d='Shop.Theme.Checkout'}

                </div>

                <div class="row">

                    <div class="col-md-12 col-xs-2 qty" style="display: flex; align-items: center;">

                        {if !empty($product.is_gift)}

                            <span class="gift-quantity">{$product.quantity}</span>

                        {else}

                            {if $esMuestra}

                                <span class="label"><i class="fa-solid fa-window-minimize"></i></span>

                            {/if}

                            <input

                                class="js-cart-line-product-quantity"

                                {if $esMuestra}

                                    type="hidden"

                                    value="1"

                                {else}

                                    type="number"

                                    value="{$product.quantity}"
                                        
                                {/if}
                                    
                                inputmode="numeric"

                                pattern="[0-9]*"

                                name="product-quantity-spin"

                                aria-label="{l s='%productName% product quantity field' sprintf=['%productName%' => $product.name] d='Shop.Theme.Checkout'}"

                                data-down-url="{$product.down_quantity_url}"

                                data-up-url="{$product.up_quantity_url}"

                                data-update-url="{$product.update_quantity_url}"

                                data-product-id="{$product.id_product}"

                            />

                        {/if}

                    </div>

                </div>

            </div>

            <div class="col-md-2">

                <div class="hidden-sm-up">

                    {l s='Quantity' d='Shop.Theme.Checkout'}

                </div>

                {assign var="m2Caja" value="1"}

                {if !$esMuestra}

                    {foreach from=Product::getFrontFeaturesStatic($language.id, $product.id) item='feature'}

                        {if $feature.id_feature === $FEATURE_M2_CAJA_ID}

                            {assign var="m2Caja" value="{$feature.value|replace:',':'.'}"}

                        {/if}

                    {/foreach}

                        {if !$normalSell}

                            <div class="label">{($product.quantity * $m2Caja)|replace:'.':','} m<sup>2</sup></div>

                        {/if}
                
                {else}

                    <div class="label" style="font-weight:700">{$sampleText}</div>

                {/if}

            </div>

            <div class="col-md-2">

                <div class="hidden-sm-up">

                    {l s='Price' d='Shop.Theme.Checkout'}

                </div>

                <div class="price">

                    <span class="product-price">

                        {include file='catalog/_partials/product-calculate-price.tpl'}

                    </span>

                </div>

            </div>

            <div class="col-md-1">

                <div class="hidden-sm-up">

                </div>

                <div class="cart-line-product-actions">

                    <a

                            class="remove-from-cart"

                            rel="nofollow"

                            href="{$product.remove_from_cart_url}"

                            data-link-action="delete-from-cart"

                            data-id-product="{$product.id_product|escape:'javascript'}"

                            data-id-product-attribute="{$product.id_product_attribute|escape:'javascript'}"

                            data-id-customization="{$product.id_customization|escape:'javascript'}"

                    >

                        {if empty($product.is_gift)}

                            <i class="material-icons float-xs-left">delete</i>

                        {/if}

                    </a>



                    {block name='hook_cart_extra_product_actions'}

                        {hook h='displayCartExtraProductActions' product=$product}

                    {/block}

                </div>

            </div>

            <div class="col-md-2">

                <div class="hidden-sm-up">

                    {l s='Total price' d='Shop.Theme.Checkout'}

                </div>

                <div class="price">

                    <span class="product-price">

                      <strong>

                        {if !empty($product.is_gift)}

                            <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>

                        {else}

                            {$product.total}

                        {/if}

                      </strong>

                    </span>

                </div>

            </div>

        </div>

        <div class="row hide-desktop">

            <div class="row fd-initial">

                    <div class="col-xs-10 image-row jc-initial">

                        <span class="product-image media-middle">

                            {if $product.default_image}

                            <img src="{$product.default_image.bySize.home_default.url}" alt="{$product.name|escape:'quotes'}" loading="lazy">

                            {else}

                            <img src="{$urls.no_picture_image.bySize.cart_default.url}" loading="lazy" alt="default image"/>

                            {/if}

                        </span>

                        <div>

                            <span class="reference">{$product.reference_to_display}</span>

                            <div class="product-line-info">

                                <a class="label product-title" href="{$product.url}" data-id_customization="{$product.id_customization|intval}">{$product.name}</a>

                            </div>


                            {foreach from=Product::getFrontFeaturesStatic($language.id, $product.id) item='feature'}

                                {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}

                                    <br>

                                    <p class="feature-medida">

                                        {$feature.value}

                                    </p>

                                {/if}

                            {/foreach}

                            {if is_array($product.customizations) && $product.customizations|count}

                                <br>

                                {block name='cart_detailed_product_line_customization'}

                                    {foreach from=$product.customizations item="customization"}

                                        <a href="#" data-toggle="modal"

                                        data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>

                                        <div class="modal fade customization-modal"

                                            id="product-customizations-modal-{$customization.id_customization}" tabindex="-1"

                                            role="dialog"

                                            aria-hidden="true">

                                            <div class="modal-dialog" role="document">

                                                <div class="modal-content">

                                                    <div class="modal-header">

                                                        <button type="button" class="close" data-dismiss="modal"

                                                                aria-label="{l s='Close' d='Shop.Theme.Global'}">

                                                            <span aria-hidden="true">&times;</span>

                                                        </button>

                                                        <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>

                                                    </div>

                                                    <div class="modal-body">

                                                        {foreach from=$customization.fields item="field"}

                                                            <div class="product-customization-line row">

                                                                <div class="col-sm-3 col-xs-4 label">

                                                                    {$field.label}

                                                                </div>

                                                                <div class="col-sm-9 col-xs-8 value">

                                                                    {if $field.type == 'text'}

                                                                        {if (int)$field.id_module}

                                                                            {$field.text nofilter}

                                                                        {else}

                                                                            {$field.text}

                                                                        {/if}

                                                                    {elseif $field.type == 'image'}

                                                                        <img src="{$field.image.small.url}" loading="lazy">

                                                                    {/if}

                                                                </div>

                                                            </div>

                                                        {/foreach}

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    {/foreach}

                                {/block}

                            {/if}

                        </div>



                    </div>

                    <div class="col-xs-2 trash-cart-action">

                        <div class="cart-line-product-actions">

                            <a

                                    class="remove-from-cart"

                                    rel="nofollow"

                                    href="{$product.remove_from_cart_url}"

                                    data-link-action="delete-from-cart"

                                    data-id-product="{$product.id_product|escape:'javascript'}"

                                    data-id-product-attribute="{$product.id_product_attribute|escape:'javascript'}"

                                    data-id-customization="{$product.id_customization|escape:'javascript'}"

                            >

                                {if empty($product.is_gift)}

                                    <i class="material-icons float-xs-left">delete</i>

                                {/if}

                            </a>



                            {block name='hook_cart_extra_product_actions'}

                                {hook h='displayCartExtraProductActions' product=$product}

                            {/block}

                        </div>

                    </div>

            </div>

            <div class="row" style="flex-direction: initial">

                <div class="col-xs-3 flex-column" {if !$esMuestra}style="padding-right: 0"{/if}>

                        <div class="hidden-sm-up mb-10">

                            {l s='Boxes' d='Shop.Theme.Checkout'}

                        </div>

                        <div class="row">

                            <div class="qty qty-phone">

                                {if !empty($product.is_gift)}

                                    <span class="gift-quantity">{$product.quantity}</span>

                                {else}

                                    {if $esMuestra}

                                        <span class="label qty-minus"><i class="fa-solid fa-window-minimize"></i></span>

                                    {/if}
                                    <div {if !$esMuestra} style="padding-left:22px" {/if}>
                                        <input

                                            class="js-cart-line-product-quantity"

                                            {if $esMuestra}

                                                type="hidden"

                                                value="1"

                                            {else}

                                                type="number"

                                                value="{$product.quantity}"
                                                    
                                            {/if}
                                                
                                            inputmode="numeric"

                                            pattern="[0-9]*"

                                            name="product-quantity-spin"

                                            aria-label="{l s='%productName% product quantity field' sprintf=['%productName%' => $product.name] d='Shop.Theme.Checkout'}"

                                            data-down-url="{$product.down_quantity_url}"

                                            data-up-url="{$product.up_quantity_url}"

                                            data-update-url="{$product.update_quantity_url}"

                                            data-product-id="{$product.id_product}"

                                        />
                                    </div>
                                {/if}

                            </div>

                        </div>

                </div>

                <div class="col-xs-3 flex-column">

                        <div class="hidden-sm-up mb-10">

                            {l s='Quantity' d='Shop.Theme.Checkout'}

                        </div>

                        {assign var="m2Caja" value="1"}

                        {if !$esMuestra}

                            {foreach from=Product::getFrontFeaturesStatic($language.id, $product.id) item='feature'}

                                {if $feature.id_feature === $FEATURE_M2_CAJA_ID}

                                    {assign var="m2Caja" value="{$feature.value|replace:',':'.'}"}

                                {/if}

                            {/foreach}

                                {if !$normalSell}

                                    <div class="product-price">{($product.quantity * $m2Caja)|replace:'.':','} m<sup>2</sup></div>

                                {/if}
                        
                        {else}

                            <div class="product-price" style="font-weight:700">{$sampleText}</div>

                        {/if}

                </div>

                <div class="col-xs-3 flex-column">

                        <div class="hidden-sm-up mb-10">

                            {l s='Price' d='Shop.Theme.Checkout'}

                        </div>

                        <div class="price">

                            <span class="product-price">

                                {include file='catalog/_partials/product-calculate-price.tpl'}

                            </span>

                        </div>

                </div>

                <div class="col-xs-3 flex-column">

                        <div class="hidden-sm-up mb-10">

                            {l s='Total price' d='Shop.Theme.Checkout'}

                        </div>

                        <div class="price">

                            <span class="product-price">

                            <strong>

                                {if !empty($product.is_gift)}

                                    <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>

                                {else}

                                    {$product.total}

                                {/if}

                            </strong>

                            </span>

                        </div>

                </div>
                
            </div>
            
        </div>

        <div class="clearfix"></div>

    </div>

    <div class="clearfix"></div>

</div>

