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
    {if $CATEGORY_INSTALACION_ID|in_array:$categoriasProducto || $CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto}
        {assign var="normalSell" value=true}
    {/if}

    <!--  product line left content: image-->
    <div class="product-line-grid-left col-sm-6 col-xs-12">
    <span class="product-image media-middle">
      {if $product.default_image}
          <img src="{$product.default_image.bySize.home_default.url}" alt="{$product.name|escape:'quotes'}"
               loading="lazy">











{else}











          <img src="{$urls.no_picture_image.bySize.cart_default.url}" loading="lazy"/>
      {/if}
    </span>
    </div>

    {assign var="esMuestra" value=false}
    {foreach from=$product.attributes key="attribute" item="value"}
        {if $attribute == 'Muestra' and $value == 'Sí'}
            {$esMuestra = true}
        {/if}
        {* PLANATEC
        <div class="product-line-info {$attribute|lower}">
            <span class="label">{$attribute}:</span>
            <span class="value">{$value}</span>
        </div>
        *}
    {/foreach}

    <div class="product-line-grid-right {if $esMuestra}product-muestra{/if} product-line-actions col-sm-6 col-xs-12">
        <div class="row">
            <div class="col-xs-8 product-line-grid-right-left">
                <div class="product-line-info">
                    <a class="label product-title" href="{$product.url}"
                       data-id_customization="{$product.id_customization|intval}">{$product.name}</a>
                </div>

                {foreach from=Product::getFrontFeaturesStatic($language.id, $product.id) item='feature'}
                    {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}
                        <p class="feature-medida">
                            <span>{l s='Format' d='Shop.Theme.Catalog'}</span>&nbsp;{$feature.value}</p>
                    {/if}
                {/foreach}

                {foreach from=$product.attributes key="attribute" item="value"}
                    {if $attribute == 'Muestra' and $value == 'Sí'}
                        <div class="product-line-info {$attribute|lower}">
                            <span class="label">{$attribute}</span>
                        </div>
                    {/if}
                    {* PLANATEC
                    <div class="product-line-info {$attribute|lower}">
                        <span class="label">{$attribute}:</span>
                        <span class="value">{$value}</span>
                    </div>
                    *}
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

                {assign var="m2Caja" value="1"}
                {foreach from=Product::getFrontFeaturesStatic($language.id, $product.id) item='feature'}
                    {if $feature.id_feature === $FEATURE_M2_CAJA_ID}
                        {assign var="m2Caja" value="{$feature.value|replace:',':'.'}"}
                    {/if}
                {/foreach}

                {if !$normalSell}
                    <span class="label" {if $esMuestra}style="display: none;"{/if}>{l s='Quantity in meters' d='Shop.Theme.Catalog'}:&nbsp;<strong>{($product.quantity * $m2Caja)|replace:'.':','}m<sup>2</sup></strong></span>
                {/if}

                <div class="row" {if $esMuestra}style="display: none;"{/if}>
                    <div class="col-md-12 col-xs-12 qty" style="display: flex; align-items: center;">
                        <div>
                            {if $normalSell}
                                <span class="label">{l s='Units' d='Shop.Theme.Catalog'}:&nbsp;</span>
                            {else}
                                {if $isByPiece}
                                    <span class="label">{l s='Pieces' d='Shop.Theme.Catalog'}:&nbsp;</span>
                                {else}
                                    <span class="label">{l s='Boxes' d='Shop.Theme.Catalog'}:&nbsp;</span>
                                {/if}
                            {/if}
                        </div>
                        {if !empty($product.is_gift)}
                            <span class="gift-quantity">{$product.quantity}</span>
                        {else}
                            <input
                                    class="js-cart-line-product-quantity"
                                    type="number"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    {if $esMuestra}
                                        value="1"
                                    {else}
                                        value="{$product.quantity}"
                                    {/if}
                                    name="product-quantity-spin"
                                    aria-label="{l s='%productName% product quantity field' sprintf=['%productName%' => $product.name] d='Shop.Theme.Checkout'}"
                                    {if $esMuestra}
                                        readonly="readonly"
                                        min="1"
                                        max="1"
                                    {else}
                                        data-down-url="{$product.down_quantity_url}"
                                        data-up-url="{$product.up_quantity_url}"
                                        data-update-url="{$product.update_quantity_url}"
                                        data-product-id="{$product.id_product}"
                                    {/if}
                            />
                        {/if}
                    </div>
                </div>
            </div>
            <div class="col-xs-4 product-line-grid-right-right">
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

                    <div class="cart-line-product-actions" style="margin-top: 15px;">
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
        </div>

        <div class="clearfix"></div>
        <br>
        <br>

        {*
        <div class="row">
            <div class="col-xs-4 hidden-xs-up"></div>
            <div class="col-md-10 col-xs-12">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        {foreach from=Product::getFrontFeaturesStatic($language.id, $product.id) item='feature'}
                            {if $feature.id_feature === $FEATURE_M2_CAJA_ID}
                                {assign var="m2Caja" value="{$feature.value|replace:',':'.'}"}
                            {/if}
                        {/foreach}

                        {if !$normalSell}
                            <span class="label" {if $esMuestra}style="display: none;"{/if}>{l s='Quantity in meters' d='Shop.Theme.Catalog'}:&nbsp;<strong>{($product.quantity * $m2Caja)|replace:'.':','}m<sup>2</sup></strong></span>
                        {/if}
                    </div>
                </div>
                <div class="row" {if $esMuestra}style="display: none;"{/if}>
                    <div class="col-md-12 col-xs-12 qty" style="display: flex; align-items: center;">
                        <div>
                            {if $normalSell}
                                <span class="label">{l s='Units' d='Shop.Theme.Catalog'}:&nbsp;</span>
                            {else}
                                {if $isByPiece}
                                    <span class="label">{l s='Pieces' d='Shop.Theme.Catalog'}:&nbsp;</span>
                                {else}
                                    <span class="label">{l s='Boxes' d='Shop.Theme.Catalog'}:&nbsp;</span>
                                {/if}
                            {/if}
                        </div>
                        {if !empty($product.is_gift)}
                            <span class="gift-quantity">{$product.quantity}</span>
                        {else}
                            <input
                                    class="js-cart-line-product-quantity"
                                    type="number"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    value="{$product.quantity}"
                                    name="product-quantity-spin"
                                    aria-label="{l s='%productName% product quantity field' sprintf=['%productName%' => $product.name] d='Shop.Theme.Checkout'}"
                                    {if $esMuestra}
                                        readonly="readonly"
                                        min="1"
                                        max="1"
                                    {else}
                                        data-down-url="{$product.down_quantity_url}"
                                        data-up-url="{$product.up_quantity_url}"
                                        data-update-url="{$product.update_quantity_url}"
                                        data-product-id="{$product.id_product}"
                                    {/if}
                            />
                        {/if}
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-xs-2 text-xs-right">
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
        *}
    </div>

    <div class="clearfix"></div>
</div>
