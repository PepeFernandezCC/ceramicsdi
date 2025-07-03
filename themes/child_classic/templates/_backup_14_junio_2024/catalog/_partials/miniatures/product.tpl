{block name='product_miniature_item'}

  {assign var="installation_category_id" value=5}

    {if $isAccessory|default:false}
        {if ($totalAccessories / $productIteration) <= 1.0}
            {assign var="bottomElement" value="product-bottom-element"}
        {else}
            {assign var="bottomElement" value=""}
        {/if}
    {/if}

    <div style="padding: 0"
    class="js-product product{if !empty($productClasses)} {$productClasses}{/if} {$bottomElement|default:''} {if $productIteration % 4 == 0}product-four-columns-last product-two-columns-last{elseif $productIteration % 4 == 1}product-four-columns-first product-two-columns-first{elseif $productIteration % 2 == 0}product-two-columns-last{/if}">
        {if $product.has_discount}
            <div class="my-discount">
                <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
            </div>
        {/if}
       
            <article class="product-miniature js-product-miniature plr-15 article-height" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
                <div class="thumbnail-container">
                    <div class="product-description {if $isAccessory|default:false}product-accessory{/if}" style="height: 85px">
                        {if $product.has_discount}
                            <div>
                        {else}
                            <div class="product-miniatures-info">
                        {/if}
                        
                            {block name='product_name'}
                            <div>
                                <a href="{$product.link}" content="{$product.link}">
                                    {if $page.page_name == 'index'}
                                        <h2 class="h2 product-title mb-10" style="text-transform: capitalize;"><span>{$product.name|truncate:30:'...'}</span>
                                        </h2>
                                    {else}
                                        <h2 class="h2 product-title mb-10" style="text-transform: capitalize;"><span>{$product.name|truncate:30:'...'}</span>
                                        </h2>
                                    {/if}

                                    {if $product.id_category_default == $installation_category_id}                               
                                        {foreach from=$product.features item='feature'}
                                            {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}
                                                <span class="product-feature-medida">{$feature.value}</span>
                                            {/if}
                                        {/foreach}
                                    {/if}
                                </a>
                            </div>
                            {/block}

                            {block name='product_price_and_shipping'}
                                {if $product.show_price}
                                    <div class="product-price-and-shipping" style="text-align:right">

                                        {hook h='displayProductPriceBlock' product=$product type="before_price"}

                                        <span class="price miniature-price" aria-label="{l s='Price' d='Shop.Theme.Catalog'}">
                                            {include file='catalog/_partials/product-calculate-price.tpl'}
                                        </span>

                                        {if $product.has_discount}
                                            {hook h='displayProductPriceBlock' product=$product type="old_price"}
                                            <span class="regular-price" aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">
                                                {include file='catalog/_partials/product-calculate-price.tpl' regular_price=true}
                                            </span>
                                            {if $product.discount_type === 'percentage'}
                                                <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                                            {elseif $product.discount_type === 'amount'}
                                                <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
                                            {/if}
                                        {/if}


                                        {hook h='displayProductPriceBlock' product=$product type='unit_price'}

                                        {hook h='displayProductPriceBlock' product=$product type='weight'}
                                    </div>
                                {/if}
                            {/block}
                    </div>
                    <div class="share-product share-dropdown">
                        <span class="share-button" data-share-button><i class="fa-solid fa-share-nodes"></i></span>
                        <div class="share-menu" data-share-menu>
                            <a href="https://api.whatsapp.com/send?text={$product.link|escape:'url'}" target="_blank" class="share-option"><i class="fa-brands fa-whatsapp"></i></a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={$product.link|escape:'url'}" target="_blank" class="share-option"><i class="fa-brands fa-facebook"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={$product.link|escape:'url'}" target="_blank" class="share-option"><i class="fa fa-twitter"></i></a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={$product.link|escape:'url'}" target="_blank" class="share-option"><i class="fab fa-linkedin"></i></a>
                            <span class="share-option" data-share-id="{$product.link|escape:'url'}"><i class="fa-solid fa-link"></i></span>
                        </div>
                    </div>
                </div>
                              
                <a href="{$product.link}" content="{$product.link}">
                    <div class="thumbnail-top">
                        {block name='product_thumbnail'}
                            {if $product.cover}
                                <span class="thumbnail product-thumbnail">
                                    <img
                                        {if 1|array_key_exists:$product.images}class="main-image"{/if}
                                        src="{$product.cover.bySize.home_default.url}"
                                        alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                                        loading="lazy"
                                        data-full-size-image-url="{$product.cover.large.url}"
                                        width="{$product.cover.bySize.home_default.width}"
                                        height="{$product.cover.bySize.home_default.height}"
                                    />
                                    {if 1|array_key_exists:$product.images}
                                        <div class="image-overlay">
                                            <img
                                                src="{$product.images[1].bySize.home_default.url}"
                                                alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                                                loading="lazy"
                                                data-full-size-image-url="{$product.images[1].large.url}"
                                                width="{$product.images[1].bySize.home_default.width}"
                                                height="{$product.images[1].bySize.home_default.height}"
                                            />
                                        </div>
                                    {/if}
                                </span>
                            {else}
                                <span class="thumbnail product-thumbnail">
                                    <img
                                            src="{$urls.no_picture_image.bySize.home_default.url}"
                                            loading="lazy"
                                            width="{$urls.no_picture_image.bySize.home_default.width}"
                                            height="{$urls.no_picture_image.bySize.home_default.height}"
                                    />
                                </span>
                            {/if}
                        {/block}
                    </div>
                </a>
            </article>

        {if $product.id_category_default != $installation_category_id}
          {hook h='displayProductExtraInfo' product=$product cart=$cart}
        {/if}

    </div>


{/block}

