{block name='product_miniature_item'}

  {assign var="installation_category_id" value=5}

    {if $isAccessory|default:false}
        {if ($totalAccessories / $productIteration) <= 1.0}
            {assign var="bottomElement" value="product-bottom-element"}
        {else}
            {assign var="bottomElement" value=""}
        {/if}
    {/if}

    <div style="padding: 0; overflow: hidden"
    class="product{if !empty($productClasses)} {$productClasses}{/if} {$bottomElement|default:''} {if $productIteration % 2 == 0}sm-bl-xs{else}sm-br-xs{/if}
    
    ">
        <a href="{$product.link}" content="{$product.link}"{if isset($position)} data-position={$position} {/if}>
            {if $product.has_discount}
                <div class="my-discount">
                    <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                </div>
            {/if}

                {assign var="maintanceCategoryArray" value=[5, 36, 94, 67]}
                {assign var="productMiniatureTitle" value="product-miniature-title"}

                {if in_array($product.id_category_default, $maintanceCategoryArray)}                               
                    {assign var="productMiniatureTitle" value="product-miniature-title-maintance"}
                {/if}       
        
                <article class="product-miniature js-product-miniature plr-10 article-height" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
                    <div class="product-miniature-title-box" data-category="{$product.id_category_default}" data-maintance="{$installation_category_id}">
                        <div class="product-description {if $isAccessory|default:false}product-accessory{/if}">
                            <div class="{if !$product.has_discount}product-miniatures-info{/if}">
                                {block name='product_name'}
                                <div class="{$productMiniatureTitle}">
                                
                                            <h3 class="h2 product-title mb-10" style="text-transform: capitalize;"><span>{$product.name|truncate:60:'...'}</span>
                                            </h3>
                                
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
                            {* Boton compartir *}
                            {*
                                <div class="share-product share-dropdown">
                                    <span class="share-button" data-share-button>
                                        <img loading="lazy" class="cc-share-icon" src="/themes/child_classic/assets/img/web/icons/cc-share-icon.svg" />
                                    </span>
                                    <div class="share-menu" data-share-menu>
                                        <a href="https://api.whatsapp.com/send?text={$product.link|escape:'url'}" target="_blank" class="share-option"><i class="fa-brands fa-whatsapp"></i></a>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={$product.link|escape:'url'}" target="_blank" class="share-option"><i class="fa-brands fa-facebook"></i></a>
                                        <a href="https://twitter.com/intent/tweet?url={$product.link|escape:'url'}" target="_blank" class="share-option"><i class="fa fa-twitter"></i></a>
                                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={$product.link|escape:'url'}" target="_blank" class="share-option"><i class="fab fa-linkedin"></i></a>
                                        <span class="share-option" data-share-id="{$product.link|escape:'url'}"><i class="fa-solid fa-link"></i></span>
                                    </div>
                                </div>
                            *}
                        </div>
                    </div>
                    <div class="block-image-product {if $productIteration % 2 == 0}block-image-product-r{else}block-image-product-l{/if}">                    
                        <div class="thumbnail-top">
                            {block name='product_thumbnail'}
                                {if $product.cover}
                                    <span class="thumbnail product-thumbnail">
                                        <img
                                            {if 1|array_key_exists:$product.images}class="main-image image-wh"{/if}
                                                
                                            {if isset($position) && $position > 3} 
                                                loading="lazy"
                                            {/if}
                                                
                                            src="{$product.cover.bySize.home_default.url}"
                                            alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                                            data-full-size-image-url="{$product.cover.large.url}"

                                        />
                                        {if 1|array_key_exists:$product.images}
                                            <div class="image-overlay">
                                                <img class="image-wh" 
                                                    {if isset($position) && $position > 3} 
                                                        loading="lazy"
                                                    {/if}
                                                        
                                                    src="{$product.images[1].bySize.home_default.url}"
                                                    alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                                                    data-full-size-image-url="{$product.images[1].large.url}"
                                                />
                                            </div>
                                        {/if}
                                    </span>
                                {else}
                                    <span class="thumbnail product-thumbnail">
                                        <img
                                            class="image-wh"
                                            src="{$urls.no_picture_image.bySize.home_default.url}"
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

