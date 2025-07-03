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

{* PLANATEC *}
{capture assign="productClasses"}{if !empty($productClass)}{$productClass}{else}col-xs-6 col-xl-4{/if}{/capture}

{*
<div class="products{if !empty($cssClass)} {$cssClass}{/if} owl-carousel">
    {foreach from=$products item="product" key="position" name="productIteration"}
        {include file="catalog/_partials/miniatures/product_home.tpl" product=$product position=$position productClasses=$productClasses productIteration=$smarty.foreach.productIteration.iteration}
    {/foreach}
</div>
*}

{*Script asegura minimo de 10 diapositivas para el correcto funcionamiento del carrousel*}

{assign var="minSlides" value=10}
{assign var="totalSlides" value=$products|count}

{if $totalSlides < $minSlides}
    {assign var="iterations" value=($minSlides/$totalSlides)|ceil}
    {assign var="newProducts" value=[]}

    {section name=repeat loop=$iterations}
        {foreach from=$products item="product"}
            {$newProducts[] = $product}
        {/foreach}
    {/section}

    {assign var="products" value=$newProducts|@array_slice:0:$minSlides}
{/if}

<div class="outside-wrapper">
    <div class="inside-wrapper">
        <div class="products{if !empty($cssClass)} {$cssClass}{/if} swiper custom-featured-swiper">
            <div class="swiper-wrapper">
                {foreach from=$products item="product" key="position" name="productIteration"}
                    <div class="swiper-slide">
                        {include file="catalog/_partials/miniatures/product_home.tpl" product=$product position=$position productClasses=$productClasses productIteration=$smarty.foreach.productIteration.iteration}
                    </div>
                {/foreach}
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</div>
{* END PLANATEC *}