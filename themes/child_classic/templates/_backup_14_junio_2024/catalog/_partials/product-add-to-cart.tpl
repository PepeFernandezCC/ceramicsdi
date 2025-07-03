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

{assign var="categoriasProducto" value=Product::getProductCategories($product.id)}

{assign var="muestraEnCarrito" value=false}
{assign var="productoEnCarrito" value=false}
{foreach from=$cart.products item='cartProduct'}
    {if $product.id == $cartProduct.id}
        {assign var="encontrado" value=false}
        {foreach from=$cartProduct.attributes key="attribute" item="value"}
            {if $attribute == 'Muestra' and $value == 'Sí'}
                {$muestraEnCarrito = true}
                {$encontrado = true}
            {/if}
        {/foreach}

        {if !$encontrado}
            {$productoEnCarrito = true}
        {/if}
    {/if}
{/foreach}

{assign var="maxProductsInCart" value=false}
{if !$productoEnCarrito}
    {if $cart.products|count >= 10}
        {$maxProductsInCart = true}
        {foreach from=$cart.products item=cartProduct}
            {if $cartProduct.id == $product.id && !$muestraEnCarrito}
                {$maxProductsInCart = false}
            {/if}
        {/foreach}
    {/if}
{/if}

{if $productoEnCarrito && $cart.products|count >= 10}
    {* Realmente la muestra no está en el carrito, sí el producto, pero al haber llegado a 10 no debe dejarla *}
    {$muestraEnCarrito = true}
{/if}

{if $CATEGORY_INSTALACION_ID|in_array:$categoriasProducto || $CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto}
    {assign var="normalSell" value=true}
{else}
    {assign var="normalSell" value=false}
{/if}

<div class="product-add-to-cart js-product-add-to-cart">
    {if !$configuration.is_catalog}
        {* PLANATEC

        <span class="control-label">{l s='Quantity' d='Shop.Theme.Catalog'}</span>
        *}
        {block name='product_quantity'}
            {* PLANATEC *}
            {assign var="m2_caja" value="0"}
            {assign var="piezas_caja" value="0"}
            {assign var="junta_recomendada" value="0"}
            {assign var="dias_plazo" value=""}
            {assign var="texto_muestra" value=""}
            {assign var="muestra_de_pago" value=""}
            {foreach from=$product.grouped_features item=feature}
                {if $FEATURE_M2_CAJA_ID === $feature.id_feature}
                    {assign var="m2_caja" value="{$feature.value}"}
                {elseif $FEATURE_PIEZAS_CAJA_ID === $feature.id_feature}
                    {assign var="piezas_caja" value="{$feature.value}"}
                {elseif $FEATURE_JUNTA_RECOMENDADA_ID === $feature.id_feature}
                    {assign var="junta_recomendada" value="{$feature.value}"}
                {elseif $FEATURE_DIAS_PLAZO_ENTREGA_ID === $feature.id_feature}
                    {assign var="dias_plazo" value="{$feature.value}"}
                {elseif $FEATURE_TEXTO_MUESTRA_ID === $feature.id_feature}
                    {assign var="texto_muestra" value="{$feature.value}"}
                {elseif $FEATURE_MUESTRA_DE_PAGO_ID === $feature.id_feature}
                    {assign var="muestra_de_pago" value="{$feature.value}"}
                {/if}
            {/foreach}
            {if !$normalSell}
                <div class="product-quantity-wrapper clearfix">
                    <div id="surface-wrapper" class="row">
                        <div class="col-xl-3 col-xs-12">
                            <label class="hidden-xl-up"
                                   for="surface-input">{l s='Introduce quantities' d='Shop.Theme.Actions'}{l s=': ' d='Shop.Theme.Catalog'}</label>
                            <label class="hidden-md-down"
                                   for="surface-input">{l s='By' d='Shop.Theme.Actions'} {l s='Surface' d='Shop.Theme.Actions'}{l s=': ' d='Shop.Theme.Catalog'}</label>
                        </div>
                        <div class="col-xl-4 col-xs-12">
                            <label class="surface-quantities hidden-md-down"
                                   for="surface-input">{l s='Enter quantities' d='Shop.Theme.Catalog'}</label>
                            <label class="surface-quantities hidden-xl-up" for="surface-input">
                                {l s='By' d='Shop.Theme.Actions'} {l s='Surface' d='Shop.Theme.Actions'}
                            </label>
                            <input
                                    type="number"
                                    name="surface"
                                    id="surface-input"
                                    inputmode="numeric"
                                    step="0.01"
                                    min="0.01"
                                    class="input-group"
                                    aria-label="{l s='Surface' d='Shop.Theme.Actions'}"
                                    data-m2-caja="{$m2_caja}"
                                    placeholder="{l s='m2 needed' d='Shop.Theme.Actions'}"
                            >
                        </div>
                        <!--<div class="col-xs-2 no-padding">
                        <span>m<sup>2</sup>&nbsp;{l s='needed' d='Shop.Theme.Actions'}</span>
                    </div>-->
                        <div class="col-xl-2 col-xs-6">
                            <input type="text" id="surface-real" disabled style="width: 100%;">
                        </div>
                        <div class="col-xl-3 col-xs-6 no-padding">
                            {assign var="isByPiece" value=false}
                            {foreach from=$product.features item='feature'}
                                {if $feature.id_feature === $FEATURE_M2_PIEZA_ID}
                                    {assign var="isByPiece" value=true}
                                {/if}
                            {/foreach}

                            {if $isByPiece}
                                <i><span>m<sup>2</sup>&nbsp;({l s='rounded up to pieces' d='Shop.Theme.Actions'})</span></i>
                            {else}
                                <i><span>m<sup>2</sup>&nbsp;({l s='rounded up to full boxes' d='Shop.Theme.Actions'})</span></i>
                            {/if}
                        </div>
                    </div>

                    <div id="pieces-wrapper" class="row">
                        <div class="col-xl-3 col-xs-12 hidden-md-down">
                            <label for="pieces-input">{l s='By' d='Shop.Theme.Actions'} {l s='Pieces' d='Shop.Theme.Actions'}{l s=': ' d='Shop.Theme.Catalog'}</label>
                        </div>
                        <div class="col-xl-4 col-xs-12">
                            <label class="surface-quantities hidden-xl-up" for="pieces-input">
                                {l s='By' d='Shop.Theme.Actions'} {l s='Pieces' d='Shop.Theme.Actions'}
                            </label>
                            <input
                                    type="number"
                                    name="pieces"
                                    id="pieces-input"
                                    inputmode="numeric"
                                    step="0.01"
                                    min="0.01"
                                    class="input-group"
                                    aria-label="{l s='Quantity' d='Shop.Theme.Actions'}"
                                    data-piezas-caja="{$piezas_caja}"
                                    placeholder="{l s='pieces needed' d='Shop.Theme.Actions'}"
                            >
                        </div>
                        <!--<div class="col-xs-2 no-padding">
                        <span>{l s='pieces needed' d='Shop.Theme.Actions'}</span>
                    </div>-->
                        <div class="col-xl-2 col-xs-6">
                            <input type="text" id="pieces-real" disabled style="width: 100%;">
                        </div>
                        <div class="col-xl-3 col-xs-6 no-padding">
                            <i><span>{l s='total pieces' d='Shop.Theme.Actions'}</span></i>
                        </div>
                    </div>

                    <div id="quantity-wrapper" class="row">
                        <div class="col-xl-3 col-xs-12">
                            <label class="font-weight-bold"
                                   for="quantity-input">{l s='Total' d='Shop.Theme.Actions'}{l s=': ' d='Shop.Theme.Catalog'}</label>
                        </div>
                        <div class="col-xl-6 col-xs-11">
                            <input
                                    type="number"
                                    name="qty"
                                    id="quantity-input"
                                    inputmode="numeric"
                                    step="1"
                                    min="0"
                                    value="0"
                                    class="input-group"
                                    aria-label="{l s='Total' d='Shop.Theme.Actions'}"
                                    readonly="readonly"
                                    style="display: none !important;"
                            >
                            <input
                                    type="number"
                                    name="euros"
                                    id="euros-input"
                                    inputmode="numeric"
                                    step="0.01"
                                    min="0.00"
                                    value="0.00"
                                    class="input-group"
                                    aria-label="{l s='Total' d='Shop.Theme.Actions'}"
                                    readonly="readonly"
                                    data-price="{$product.price_amount}"
                                    style="font-weight: bold"
                            >
                        </div>
                        <div class="col-xl-3 col-xs-1 no-padding font-weight-bold">€</div>
                    </div>

                    <div id="variants-wrapper" class="row">
                        {* PLANATEC *}
                        {block name='product_variants'}
                            {include file='catalog/_partials/product-variants.tpl'}
                        {/block}
                        {* END PLANATEC *}
                    </div>

                    <div id="add-wrapper" class="row">
                        <div class="col-xl-3 col-xs-12"></div>
                        <div class="col-xl-9 col-xs-12">
                            <div class="add">
                                {if $category->id_category != $CATEGORY_INSTALACION_Y_MONTAJE_ID}
                                    <div class="recommendation">
                                        {l s='It is recommended to order between 10 and 15% more material than needed' d='Shop.Theme.Global'}
                                        &nbsp;<a href="{Context::getContext()->link->getCMSLink(11)}"
                                                 style="text-decoration: underline;">?</a>
                                    </div>
                                {/if}

                                <button
                                        class="btn btn-primary add-to-cart"
                                        data-button-action="add-to-cart"
                                        type="submit"
                                        {if !$product.add_to_cart_url || $maxProductsInCart}
                                            disabled
                                        {/if}
                                >
                                    {* PLANATEC

                                    <i class="material-icons shopping-cart">&#xE547;</i>
                                    *}
                                    {l s='Add to cart' d='Shop.Theme.Actions'}
                                </button>
                            </div>
                        </div>

                        {* PLANATEC *}
                        <div class="col-xl-3 col-xs-12 hidden-xs-down"></div>
                        <div class="col-xl-9 col-xs-12">
                            <div class="add-sample">
                                <button
                                        class="btn btn-primary add-to-cart add-to-cart-sample"
                                        data-button-action="add-to-cart-sample"
                                        type="button"
                                        {if !$product.add_to_cart_url || $muestraEnCarrito || $maxProductsInCart}
                                            disabled
                                        {/if}
                                >
                                    {if $muestra_de_pago !== ''}
                                        {l s='Request sample' d='Shop.Theme.Actions'}
                                    {else}
                                        {l s='Request free sample' d='Shop.Theme.Actions'}
                                    {/if}
                                </button>
                            </div>

                            {if $maxProductsInCart}
                                <p style="text-align: center; font-weight: bold; color: red; margin-top: 10px; font-size: 12px; margin-bottom: 0;">
                                    {l s='You have reached the maximum number of products allowed in the same purchase, if necessary, contact customer service.' d='Shop.Theme.Global'}
                                </p>
                            {/if}
                        </div>
                        {* END PLANATEC *}
                    </div>

                    {* PLANATEC *}
                    <div id="transport-wrapper" class="row">
                        <div class="col-xl-3 col-xs-12"></div>
                        <div class="col-xl-9 col-xs-12">
                            <div class="product-transport">
                                {if $dias_plazo !== '' or $texto_muestra !== ''}
                                    {*
                                    <p style="text-transform: uppercase;"><strong>{l s='Transport' d='Shop.Theme.Catalog'}</strong></p>
                                    *}
                                    <table style="width: 100%;">
                                        <tbody>
                                        {if $dias_plazo !== ''}
                                            <tr>
                                                <td>
                                                    <strong>{l s='Delivery' d='Shop.Theme.Catalog'}{l s=': ' d='Shop.Theme.Catalog'}</strong>
                                                </td>
                                                <td>{$productTransport|replace:'{dias_plazo}':$dias_plazo nofilter}</td>
                                            </tr>
                                        {/if}
                                        {if $texto_muestra !== ''}
                                            <tr>
                                                <td>
                                                    <strong>{l s='Samples' d='Shop.Theme.Catalog'}{l s=': ' d='Shop.Theme.Catalog'}</strong>
                                                </td>
                                                <td>{$productTransportSamples|replace:'{texto_muestra}':$texto_muestra nofilter}</td>
                                            </tr>
                                        {/if}
                                        </tbody>
                                    </table>
                                {/if}
                            </div>
                        </div>
                    </div>

                    {$junta_recomendada_nombre = Product::getProductName($junta_recomendada)}
                    {if !empty($junta_recomendada) && !empty($junta_recomendada_nombre)}
                        <div id="recommended-board-wrapper" class="row">
                            <div class="col-xl-3 col-xs-12">
                                <label for="">{l s='Recommended board' d='Shop.Theme.Catalog'}{l s=':' d='Shop.Theme.Catalog'}</label>
                                <a class="hidden-xl-up"
                                   href="{Context::getContext()->link->getProductLink($junta_recomendada)}">
                                    {$junta_recomendada_nombre}
                                </a>
                            </div>
                            <div class="col-xl-9 col-xs-12 hidden-md-down">
                                <a href="{Context::getContext()->link->getProductLink($junta_recomendada)}">
                                    {$junta_recomendada_nombre}
                                </a>
                            </div>
                        </div>
                    {/if}

                    {if $product.attachments}
                        {foreach from=$product.attachments item=attachment}
                            {if $attachment.name == 'PDF Técnico' && $language.id == 1}
                                <div id="product-attachment-pdf-tecnico" class="row">
                                    <div class="col-xl-3 col-xs-12">
                                        <label for="">{l s='Technical PDF' d='Shop.Theme.Catalog'}{l s=':' d='Shop.Theme.Catalog'}</label>
                                        <a class="hidden-sm-up"
                                           href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                           target="_blank">
                                            {l s='Download' d='Shop.Theme.Actions'}
                                        </a>
                                    </div>
                                    <div class="col-xl-9 col-xs-12 hidden-xs-down">
                                        <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                           target="_blank">
                                            {l s='Download' d='Shop.Theme.Actions'}
                                        </a>
                                    </div>
                                </div>
                            {elseif $attachment.name == 'PDF technique' && $language.id == 2}
                                <div id="product-attachment-pdf-tecnico" class="row">
                                    <div class="col-xl-3 col-xs-12">
                                        <label for="">{l s='Technical PDF' d='Shop.Theme.Catalog'}{l s=':' d='Shop.Theme.Catalog'}</label>
                                        <a class="hidden-sm-up"
                                           href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                           target="_blank">
                                            {l s='Download' d='Shop.Theme.Actions'}
                                        </a>
                                    </div>
                                    <div class="col-xl-9 col-xs-12 hidden-xs-down">
                                        <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                           target="_blank">
                                            {l s='Download' d='Shop.Theme.Actions'}
                                        </a>
                                    </div>
                                </div>
                            {/if}
                        {/foreach}
                    {/if}
                    {* END PLANATEC *}
                </div>
            {/if}
            {* END PLANATEC *}
            {* PLANATEC
            <div class="product-quantity clearfix">
            *}

            {if $normalSell}
                <div class="product-quantity-wrapper clearfix">
                    <div id="variants-wrapper" class="row">
                        {* PLANATEC *}
                        {block name='product_variants'}
                            {include file='catalog/_partials/product-variants.tpl'}
                        {/block}
                        {* END PLANATEC *}
                    </div>

                    <div class="row">
                        <div class="col-xl-3">
                            <label for="pieces-input">{l s='Quantity' d='Shop.Theme.Actions'}{l s=': ' d='Shop.Theme.Catalog'}</label>
                        </div>
                        <div class="col-xl-9">
                            <input
                                    type="number"
                                    name="qty"
                                    id="quantity-input"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    {if $product.quantity_wanted}
                                        value="{$product.quantity_wanted}"
                                        min="{$product.minimal_quantity}"
                                    {else}
                                        value="1"
                                        min="1"
                                    {/if}
                                    class="input-group"
                                    aria-label="{l s='Quantity' d='Shop.Theme.Actions'}">
                        </div>
                    </div>
                    <div id="add-wrapper" class="row">
                        <div class="col-xl-3 col-xs-12"></div>
                        <div class="col-xl-9 col-xs-12">
                            <div class="add">
                                {if $category->id_category != $CATEGORY_INSTALACION_Y_MONTAJE_ID}
                                    <div class="recommendation">
                                        {l s='It is recommended to order between 10 and 15% more material than needed' d='Shop.Theme.Global'}
                                        &nbsp;<a href="{Context::getContext()->link->getCMSLink(11)}"
                                                 style="text-decoration: underline;">?</a>
                                    </div>
                                {/if}
                                <button
                                        class="btn btn-primary add-to-cart"
                                        data-button-action="add-to-cart"
                                        type="submit"
                                        {if !$product.add_to_cart_url}
                                            disabled
                                        {/if}
                                >
                                    {l s='Add to cart' d='Shop.Theme.Actions'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {* PLANATEC *}
                <div id="transport-wrapper" class="row" style="margin-top: 25px;">
                    <div class="col-xl-3 col-xs-12"></div>
                    <div class="col-xl-9 col-xs-12" style="padding-left: 7px;">
                        <div class="product-transport">
                            {if $dias_plazo !== '' or $texto_muestra !== ''}
                                {*
                                <p style="text-transform: uppercase;"><strong>{l s='Transport' d='Shop.Theme.Catalog'}</strong></p>
                                *}
                                <table style="width: 100%;">
                                    <tbody>
                                    {if $dias_plazo !== ''}
                                        <tr>
                                            <td>
                                                <strong>{l s='Delivery' d='Shop.Theme.Catalog'}{l s=': ' d='Shop.Theme.Catalog'}</strong>
                                            </td>
                                            <td>{$productTransport|replace:'{dias_plazo}':$dias_plazo nofilter}</td>
                                        </tr>
                                    {/if}
                                    {if $texto_muestra !== ''}
                                        <tr>
                                            <td>
                                                <strong>{l s='Samples' d='Shop.Theme.Catalog'}{l s=': ' d='Shop.Theme.Catalog'}</strong>
                                            </td>
                                            <td>{$productTransportSamples|replace:'{texto_muestra}':$texto_muestra nofilter}</td>
                                        </tr>
                                    {/if}
                                    </tbody>
                                </table>
                            {/if}
                        </div>
                    </div>
                </div>
                {$junta_recomendada_nombre = Product::getProductName($junta_recomendada)}
                {if !empty($junta_recomendada) && !empty($junta_recomendada_nombre)}
                    <div id="recommended-board-wrapper" class="row">
                        <div class="col-xl-3 col-xs-12">
                            <label for="">{l s='Recommended board' d='Shop.Theme.Catalog'}{l s=':' d='Shop.Theme.Catalog'}</label>
                            <a class="hidden-sm-up"
                               href="{Context::getContext()->link->getProductLink($junta_recomendada)}">
                                {$junta_recomendada_nombre}
                            </a>
                        </div>
                        <div class="col-xl-9 col-xs-12 hidden-xs-down">
                            <a href="{Context::getContext()->link->getProductLink($junta_recomendada)}">
                                {$junta_recomendada_nombre}
                            </a>
                        </div>
                    </div>
                {/if}

                {if $product.attachments}
                    {foreach from=$product.attachments item=attachment}
                        {if $attachment.name == 'PDF Técnico' && $language.id == 1}
                            <div id="product-attachment-pdf-tecnico" class="row">
                                <div class="col-xl-3 col-xs-12">
                                    <label for="">{l s='Technical PDF' d='Shop.Theme.Catalog'}{l s=':' d='Shop.Theme.Catalog'}</label>
                                    <a class="hidden-sm-up"
                                       href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                       target="_blank">
                                        {l s='Download' d='Shop.Theme.Actions'}
                                    </a>
                                </div>
                                <div class="col-xl-9 col-xs-12 hidden-xs-down">
                                    <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                       target="_blank">
                                        {l s='Download' d='Shop.Theme.Actions'}
                                    </a>
                                </div>
                            </div>
                        {elseif $attachment.name == 'PDF technique' && $language.id == 2}
                            <div id="product-attachment-pdf-tecnico" class="row">
                                <div class="col-xl-3 col-xs-12">
                                    <label for="">{l s='Technical PDF' d='Shop.Theme.Catalog'}{l s=':' d='Shop.Theme.Catalog'}</label>
                                    <a class="hidden-sm-up"
                                       href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                       target="_blank">
                                        {l s='Download' d='Shop.Theme.Actions'}
                                    </a>
                                </div>
                                <div class="col-xl-9 col-xs-12 hidden-xs-down">
                                    <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}"
                                       target="_blank">
                                        {l s='Download' d='Shop.Theme.Actions'}
                                    </a>
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                {/if}
                {* PLANATEC
                <div class="qty">
                    <input
                            type="number"
                            name="qty"
                            id="quantity_wanted"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            {if $product.quantity_wanted}
                                value="{$product.quantity_wanted}"
                                min="{$product.minimal_quantity}"
                            {else}
                                value="1"
                                min="1"
                            {/if}
                            class="input-group"
                            aria-label="{l s='Quantity' d='Shop.Theme.Actions'}"
                    >
                </div>
                <div class="add">
                    <button
                            class="btn btn-primary add-to-cart"
                            data-button-action="add-to-cart"
                            type="submit"
                            {if !$product.add_to_cart_url}
                                disabled
                            {/if}
                    >
                        <i class="material-icons shopping-cart">&#xE547;</i>
                        {l s='Add to cart' d='Shop.Theme.Actions'}
                    </button>
                </div>
                *}
            {/if}

            {hook h='displayProductActions' product=$product}
            {* PLANATEC
            </div>
            *}
        {/block}

    {/if}
</div>
