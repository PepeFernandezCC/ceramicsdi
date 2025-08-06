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

 {assign var="videoProductRoute" value=Product::videoProductRoute($product.reference, $product.description_short)}

<div class="owl-carousel-image-products-mobile owl-carousel owl-theme hidden-sm-up">
    {foreach from=$product.images item=image name=productImages}

        {if $smarty.foreach.productImages.iteration == 2 and $videoProductRoute != false}
            <div class="product-cover product-video">
                <video id="productVideoMobile" autoplay loop muted playsinline preload="auto" style="max-width: 100%; height: 99.7%;" >
                    {if $videoProductRoute.webm}
                        <source src="{$videoProductRoute.sourceMp4}" type="{$videoProductRoute.typeMp4}">
                        <source src="{$videoProductRoute.sourceWebm}" type="{$videoProductRoute.typeWebm}">
                    {else}
                        <source src="{$videoProductRoute.sourceMp4}" type="{$videoProductRoute.typeMp4}">
                    {/if}
                    Tu navegador no soporta la etiqueta de video.
                </video>
            </div>
        {else}
            {if $smarty.foreach.productImages.iteration == 2 and $product.description_short|strstr:'<iframe'}
                <div class="item video-item" >
                    <div data-reference="{$product.reference}" class="product-cover product-video {if $smarty.foreach.productImages.last}product-cover-last{elseif $smarty.foreach.productImages.total - $smarty.foreach.productImages.iteration == 1}product-cover-penultimate{/if}">
                        {$product.description_short|replace:'<p>':''|replace:'</p>':'' nofilter}
                    </div>
                </div>
            {/if}
        {/if}
        <div class="item product-cover">
            <img
                    class="js-qv-product-cover img-fluid img-custom-lightbox"
                    src="{$image.bySize.medium_default.url}"
                    {if !empty($image.legend)}
                        alt="{$image.legend}"
                        title="{$image.legend}"
                    {else}
                        alt="{$product.name}"
                    {/if}
                    loading="lazy"
                    width="{$image.bySize.medium_default.width}"
                    height="{$image.bySize.medium_default.height}"
                    data-lightbox="{$image.bySize.medium_default.url}"
            >
        </div>
    {/foreach}
</div>

<div class="images-container js-images-container hidden-xs-down">
    {block name='product_cover'}
        {* PLANATEC *}
        {assign var="withVideo" value=false}
        {assign var="firstIteration" value=true}
        {foreach from=$product.images item=image name=productImages}
            {if $smarty.foreach.productImages.iteration == 2 and $videoProductRoute != false}
                <div class="product-cover product-video">
                    <video id="productVideo" autoplay loop muted playsinline preload="auto" style="max-width: 100%; height: 99.7%;">
                        {if $videoProductRoute.webm}
                            <source src="{$videoProductRoute.sourceWebm}" type="{$videoProductRoute.typeWebm}">
                            <source src="{$videoProductRoute.sourceMp4}" type="{$videoProductRoute.typeMp4}">
                        {else}
                            <source src="{$videoProductRoute.sourceMp4}" type="{$videoProductRoute.typeMp4}">
                        {/if}
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                </div>
            {else}
                {if $smarty.foreach.productImages.iteration == 2 and $product.description_short|strstr:'<iframe'}
                    <div class="product-cover product-video {if $smarty.foreach.productImages.last}product-cover-last{elseif $smarty.foreach.productImages.total - $smarty.foreach.productImages.iteration == 1}product-cover-penultimate{/if}">
                        {$product.description_short|replace:'<p>':''|replace:'</p>':'' nofilter}
                    </div>
                    {$withVideo = true}
                {/if}
            {/if}
            <div class="product-cover {if $smarty.foreach.productImages.iteration == 2 && $withVideo}border-video-one-column{/if} {if $smarty.foreach.productImages.iteration == 3 && $withVideo}border-video-two-columns{/if} {if $smarty.foreach.productImages.last}product-cover-last{elseif $smarty.foreach.productImages.total - $smarty.foreach.productImages.iteration == 1}product-cover-penultimate{/if}">
                <img
                        class="js-qv-product-cover img-fluid"
                        src="{$image.bySize.medium_default.url}"
                        {if !empty($image.legend)}
                            alt="{$image.legend}"
                            title="{$image.legend}"
                        {else}
                            alt="{$product.name}"
                        {/if}
                        {if $firstIteration == false}
                            loading="lazy"
                        {/if}
                        width="{$image.bySize.medium_default.width}"
                        height="{$image.bySize.medium_default.height}"
                >
                <div class="layer {* PLANATEC hidden-sm-down *}" data-toggle="modal" data-target="#product-modal"
                     data-iteration="{$smarty.foreach.productImages.iteration}">
                    <i class="material-icons zoom-in">search</i>
                </div>
            </div>
            {assign var="firstIteration" value=false}
        {/foreach}
        {* END PLANATEC *}
    {/block}
    {hook h='displayAfterProductThumbs' product=$product}
</div>
