{block name='product_miniature_item'}

  {assign var="installation_category_id" value=5}

    {if $isAccessory|default:false}
        {if ($totalAccessories / $productIteration) <= 1.0}
            {assign var="bottomElement" value="product-bottom-element"}
        {else}
            {assign var="bottomElement" value=""}
        {/if}
    {/if}

    <div style="padding: 0; overflow:hidden"
    class="js-product product{if !empty($productClasses)} {$productClasses}{/if} {$bottomElement|default:''} {if $productIteration % 4 == 0}product-four-columns-last product-two-columns-last{elseif $productIteration % 4 == 1}product-four-columns-first product-two-columns-first{elseif $productIteration % 2 == 0}product-two-columns-last{/if}">
        <a href="{$product.link}" content="{$product.link}">
            {if $product.has_discount}
                <div class="my-discount-related">
                    <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                </div>
            {/if}
        
                <article class="product-miniature js-product-miniature plr-10 related-article-height" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
                    <div class="thumbnail-container" style="max-height:none">
                        <div class="product-description {if $isAccessory|default:false}product-accessory{/if}">
                            {if $product.has_discount}
                                <div>
                            {else}
                                <div class="product-miniatures-info">
                            {/if}
                            
                            
                                {block name='product_name'}
                                <div>
                                    
                                    
                                            <h3 class="h2 product-title mb-10" style="text-transform: capitalize;"><span>{$product.name|truncate:30:'...'}</span>
                                            </h3>

                                        {if $product.id_category_default == $installation_category_id}                               
                                            {foreach from=$product.features item='feature'}
                                                {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}
                                                    <span class="product-feature-medida">{$feature.value}</span>
                                                {/if}
                                            {/foreach}
                                        {/if}
                                    
                                </div>
                                {/block}

                                {block name='product_price_and_shipping'}
                                    {if $product.show_price}
                                        <div style="display:flex; flex-direction: column">

                                            {hook h='displayProductPriceBlock' product=$product type="before_price"}

                                            <span class="price miniature-price" aria-label="{l s='Price' d='Shop.Theme.Catalog'}">
                                                {include file='catalog/_partials/product-calculate-price.tpl'}
                                            </span>

                                            {if $product.has_discount}
                                                {hook h='displayProductPriceBlock' product=$product type="old_price"}
                                                <span class="regular-price" aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">
                                                    {include file='catalog/_partials/product-calculate-price.tpl' regular_price=true}
                                                </span>
                                            {/if}


                                            {hook h='displayProductPriceBlock' product=$product type='unit_price'}

                                            {hook h='displayProductPriceBlock' product=$product type='weight'}
                                        </div>
                                    {/if}
                                {/block}
                            </div>

                        </div>
                                
                        
                            <div class="thumbnail-top">
                                {block name='product_thumbnail'}
                                    {if $product.cover}
                                        <span class="thumbnail product-thumbnail">
                                        <img
                                                {if 1|array_key_exists:$product.images}class="main-image"{/if}
                                                data-src="{$product.cover.bySize.home_default.url}"
                                                alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                                                loading="lazy"
                                                data-full-size-image-url="{$product.cover.large.url}"
                                                width="{$product.cover.bySize.home_default.width}"
                                                height="{$product.cover.bySize.home_default.height}"
                                        />

                                    </span>
                                    {else}
                                        <span class="thumbnail product-thumbnail">
                                        <img
                                                data-src="{$urls.no_picture_image.bySize.home_default.url}"
                                                loading="lazy"
                                                width="{$urls.no_picture_image.bySize.home_default.width}"
                                                height="{$urls.no_picture_image.bySize.home_default.height}"
                                                alt="default image"
                                        />
                                    </span>
                                    {/if}
                                {/block}
                            </div>
                        
                    </div>
                </article>

            {if $product.id_category_default != $installation_category_id}
            {hook h='displayProductExtraInfo' product=$product cart=$cart}
            {/if}
        </a>
    </div>


{/block}

