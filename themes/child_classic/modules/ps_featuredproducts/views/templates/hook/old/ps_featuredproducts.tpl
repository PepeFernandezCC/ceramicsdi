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

<section class="featured-products clearfix">

    {if $language.id == 1}

        [creativeslider id="48"]

        [creativeslider id="11"]

    {elseif $language.id == 2}

        [creativeslider id="18"]

        [creativeslider id="16"]

    {elseif $language.id == 3}

        [creativeslider id="25"]

        [creativeslider id="26"]

    {elseif $language.id == 4}

        [creativeslider id="35"]

        [creativeslider id="43"]

    {elseif $language.id == 5}

        [creativeslider id="42"]

        [creativeslider id="45"]

    {elseif $language.id == 6}

        [creativeslider id="41"]

        [creativeslider id="44"]

    {/if}

        {assign var="blanco_url" value=$link->getCategoryLink(41)}
        {assign var="amarillo_url" value=$link->getCategoryLink(84)}
        {assign var="verde_url" value=$link->getCategoryLink(79)}
        {assign var="antracita_url" value=$link->getCategoryLink(47)}
        {assign var="gris_url" value=$link->getCategoryLink(43)}
        {assign var="rojo_url" value=$link->getCategoryLink(85)}
        {assign var="marron_url" value=$link->getCategoryLink(78)}
        {assign var="negro_url" value=$link->getCategoryLink(44)}        
        {assign var="beige_url" value=$link->getCategoryLink(42)}
        {assign var="verdeClaro_url" value=$link->getCategoryLink(83)}
        {assign var="azul_url" value=$link->getCategoryLink(77)}
        {assign var="multicolor_url" value=$link->getCategoryLink(86)}

    <h2>

        {l s='Select By Color' d='Shop.Theme.Global'}

    </h2>


<div class="mw-container">
    <div class="mw-box row">
        <div class="col-xs-6 col-sm-6 col-lg-3">
            <div class="mw-link">
                <a href="{$blanco_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_BLANCO.webp" loading="lazy" style="width:100%" alt="products by color: {l s='White' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='White' d='Shop.Theme.Global'}</span>
                </a>
            </div>
            <div class="mw-link">
                <a href="{$gris_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_GRIS.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Grey' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Grey' d='Shop.Theme.Global'}</span>
                </a>
            </div>
            <div class="mw-link">
                <a href="{$beige_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_BEIGE.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Beige' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Beige' d='Shop.Theme.Global'}</span>
                </a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-3">
            <div class="mw-link">
                <a href="{$amarillo_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_AMARILLO.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Yellow' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Yellow' d='Shop.Theme.Global'}</span>
                </a>
            </div>
            <div class="mw-link">
                <a href="{$rojo_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_ROJO.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Red' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Red' d='Shop.Theme.Global'}</span>
                </a>
            </div>
            <div class="mw-link">
                <a href="{$verdeClaro_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_VERDE_CLARO.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Light Green' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Light Green' d='Shop.Theme.Global'}</span>
                </a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-3">
            <div class="mw-link">
                <a href="{$verde_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_VERDE.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Green' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Green' d='Shop.Theme.Global'}</span>
                </a>
            </div>
            <div class="mw-link">
                <a href="{$marron_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_MARRON.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Brown' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Brown' d='Shop.Theme.Global'}</span>
                </a>
            </div>
            <div class="mw-link">
                <a href="{$azul_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_AZUL.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Blue' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Blue' d='Shop.Theme.Global'}</span>
                </a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-3">
            <div class="mw-link">
                <a href="{$antracita_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_ANTRACITA.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Antraciet' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Antraciet' d='Shop.Theme.Global'}</span>
                </a>
            </div>
            <div class="mw-link">
                <a href="{$negro_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_NEGRO.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Black' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Black' d='Shop.Theme.Global'}</span>
                </a>
            </div>
            <div class="mw-link">
                <a href="{$multicolor_url}">
                    <img data-src="/themes/child_classic/assets/img/web/COLOR_MULTICOLOR.webp" loading="lazy" style="width:100%" alt="products by color: {l s='Multicolor' d='Shop.Theme.Global'}"/>
                    <span style="padding-top:5px">{l s='Multicolor' d='Shop.Theme.Global'}</span>
                </a>
            </div>
        </div>
    </div>
</div>


</section>

