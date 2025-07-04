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

{if $product.show_price}
    <div class="product-prices js-product-prices">
        {block name='product_discount'}
            {if $product.has_discount}
                <div class="product-discount">
                    {hook h='displayProductPriceBlock' product=$product type="old_price"}
                    {* PLANATEC
                    <span class="regular-price">{$product.regular_price}</span>
                    *}
                    <span class="regular-price" style="margin-right: 0;">
                        {include file='catalog/_partials/product-calculate-price.tpl' regular_price=true}
                    </span>
                </div>
            {/if}
        {/block}

        {block name='product_price'}
            <div class="product-price h5 {if $product.has_discount}has-discount{/if}">
                <div class="current-price">
                  <span class='current-price-value' content="{$product.rounded_display_price}">
                      {include file='catalog/_partials/product-calculate-price.tpl'}
                  </span>

                    {if $product.has_discount}
                        {if $product.discount_type === 'percentage'}
                            <span class="discount discount-percentage">{l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $product.discount_percentage_absolute]}</span>
                        {else}
                            <span class="discount discount-amount">
                                {l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.discount_to_display]}
                            </span>
                        {/if}
                    {/if}

                    {*<span class="tax-message">({l s='Tax included' d='Admin.Global'})</span>*}
                </div>

                {block name='product_unit_price'}
                    {if $displayUnitPrice}
                        <p class="product-unit-price sub">{l s='(%unit_price%)' d='Shop.Theme.Catalog' sprintf=['%unit_price%' => $product.unit_price_full]}</p>
                    {/if}
                {/block}
            </div>
        {/block}

        {block name='product_without_taxes'}
            {if $priceDisplay == 2}
                <p class="product-without-taxes">{l s='%price% tax excl.' d='Shop.Theme.Catalog' sprintf=['%price%' => $product.price_tax_exc]}</p>
            {/if}
        {/block}

        {block name='product_pack_price'}
            {if $displayPackPrice}
                <p class="product-pack-price">
                    <span>{l s='Instead of %price%' d='Shop.Theme.Catalog' sprintf=['%price%' => $noPackPrice]}</span>
                </p>
            {/if}
        {/block}

        {block name='product_ecotax'}
            {if $product.ecotax.amount > 0}
                <p class="price-ecotax">{l s='Including %amount% for ecotax' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.ecotax.value]}
                    {if $product.has_discount}
                        {l s='(not impacted by the discount)' d='Shop.Theme.Catalog'}
                    {/if}
                </p>
            {/if}
        {/block}

        {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}

        <div class="tax-shipping-delivery-label">
            {* PLANATEC

            {if !$configuration.taxes_enabled}
                {l s='No tax' d='Shop.Theme.Catalog'}
            {elseif $configuration.display_taxes_label}
                {$product.labels.tax_long}
            {/if}
            *}
            {hook h='displayProductPriceBlock' product=$product type="price"}
            {hook h='displayProductPriceBlock' product=$product type="after_price"}
            {if $product.is_virtual    == 0}
                {if $product.additional_delivery_times == 1}
                    {if $product.delivery_information}
                        <span class="delivery-information">{$product.delivery_information}</span>
                    {/if}
                {elseif $product.additional_delivery_times == 2}
                    {if $product.quantity > 0}
                        <span class="delivery-information">{$product.delivery_in_stock}</span>
                        {* Out of stock message should not be displayed if customer can't order the product. *}
                    {elseif $product.quantity <= 0 && $product.add_to_cart_url}
                        <span class="delivery-information">{$product.delivery_out_stock}</span>
                    {/if}
                {/if}
            {/if}
        </div>
    </div>
{/if}
