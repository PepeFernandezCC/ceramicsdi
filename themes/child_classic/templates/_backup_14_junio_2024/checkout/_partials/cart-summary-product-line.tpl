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
{block name='cart_summary_product_line'}
    {* PLANATEC *}
    {assign var="esMuestra" value=false}
    {foreach from=$product.attributes key="attribute" item="value"}
        {if $attribute == 'Muestra' and $value == 'Sí'}
            {$esMuestra = true}
        {/if}
    {/foreach}

    {assign var="categoriasProducto" value=Product::getProductCategories($product.id)}
    {assign var="normalSell" value=false}
    {if $CATEGORY_INSTALACION_ID|in_array:$categoriasProducto || $CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto}
        {assign var="normalSell" value=true}
    {/if}

    <div class="product-line-grid">
        <div class="product-line-grid-left col-xs-5">
        <span class="product-image media-middle">
            {if $product.default_image}
                <img class="media-object" src="{$product.default_image.large.url}" alt="{$product.name}" loading="lazy" style="max-width: 800px;">




{else}




                <img src="{$urls.no_picture_image.bySize.small_default.url}" loading="lazy"/>
            {/if}
        </span>
        </div>
        <div class="product-line-grid-right {if $product.total_wt == 0}product-muestra{/if} product-line-actions col-xs-7">
            <div class="row">
                <div class="col-xs-8 product-line-grid-right-left">
                    <div class="product-line-info">
                        <span class="product-title">{$product.name}</span>
                    </div>

                    {foreach from=$product.attributes key="attribute" item="value"}
                        <div class="product-line-info {$attribute|lower}">
                            <span class="label">{$attribute}:</span>
                            <span class="value">{$value}</span>
                        </div>
                    {/foreach}
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
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>
            <br>

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
                <div class="col-xs-4 hidden-md-up"></div>
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-12">
                            <span class="label">{l s='Quantity' d='Shop.Theme.Catalog'}:&nbsp;{$product.quantity}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {* END PLANATEC *}

    {* PLANATEC

    <div class="media-left">
        <a href="{$product.url}" title="{$product.name}">
            {if $product.default_image}
                <img class="media-object" src="{$product.default_image.small.url}" alt="{$product.name}" loading="lazy">
            {else}
                <img src="{$urls.no_picture_image.bySize.small_default.url}" loading="lazy"/>
            {/if}
        </a>
    </div>
    <div class="media-body">
    <span class="product-name">
        <a href="{$product.url}" target="_blank" rel="noopener noreferrer nofollow">{$product.name}</a>
    </span>
        <span class="product-quantity">x{$product.quantity}</span>
        <span class="product-price float-xs-right">{$product.price}</span>
        {hook h='displayProductPriceBlock' product=$product type="unit_price"}
        {foreach from=$product.attributes key="attribute" item="value"}
            <div class="product-line-info product-line-info-secondary text-muted">
                <span class="label">{$attribute}:</span>
                <span class="value">{$value}</span>
            </div>
        {/foreach}
        <br/>
    </div>
    *}
{/block}
