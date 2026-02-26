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
{block name='product_miniature_item'}
    <div class="js-product product{if !empty($productClasses)} {$productClasses}{/if} {if $productIteration % 4 == 0}product-four-columns-last product-two-columns-last{elseif $productIteration % 4 == 1}product-four-columns-first product-two-columns-first{elseif $productIteration % 2 == 0}product-two-columns-last{/if}">
        {* PLANATEC <a href="{$product.url}" content="{$product.url}"> *}
        {* PLANATEC *}
        <a href="{$product.link}" content="{$product.link}">
        {* END PLANATEC *}
            <article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}"
                     data-id-product-attribute="{$product.id_product_attribute}">
                <div class="thumbnail-container">
                    <div class="product-description">
                        <div>
                            {block name='product_name'}
                                {if $page.page_name == 'index'}
                                    <h3 class="h3 product-title"><span>{$product.name|truncate:30:'...'}</span>
                                    </h3>
                                {else}
                                    <h2 class="h3 product-title"><span>{$product.name|truncate:30:'...'}</span>
                                    </h2>
                                {/if}

                                {* PLANATEC *}
                                {foreach from=$product.features item='feature'}
                                    {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}
                                        <span class="product-feature-medida">{$feature.value}</span>
                                    {/if}
                                {/foreach}
                                {* END PLANATEC *}

                            {/block}
                        </div>
                    </div>
                    {assign var="firstImageId" value="0"}
                    {assign var="newImageId" value="0"}
                    {foreach from=Image::getImages($language.id, $product.id) item='img' name="imgIteration"}
                        {if $smarty.foreach.imgIteration.iteration == 1}
                            {$firstImageId = $img.id_image}
                        {/if}

                        {if $smarty.foreach.imgIteration.iteration == 2}
                            {$newImageId = $img.id_image}
                        {/if}
                    {/foreach}

                    {assign var="imageUrl" value=$product.cover.bySize.home_default.url}
                    {if $newImageId != "0"}
                        {$imageUrl = $imageUrl|replace:"/$firstImageId-":"/$newImageId-"}
                    {/if}

                    <div class="thumbnail-top"
                         style="background-image: url('{$imageUrl}')"></div>
                </div>
            </article>
        </a>
    </div>
{/block}
