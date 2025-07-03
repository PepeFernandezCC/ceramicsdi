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
{extends file=$layout}

{block name='content'}
    <section id="main">
        <div class="cart-grid row">

            <!-- Left Block: cart product informations & shpping -->
            {* PLANATEC
            <div class="cart-grid-body col-xs-12 col-lg-8">
            *}
            <div class="cart-grid-body">

                <!-- cart products detailed -->
                <div class="card cart-container">
                    <div class="card-block">
                        <h1 class="h1">{l s='Shopping Cart' d='Shop.Theme.Checkout'}</h1>
                    </div>
                    <hr class="separator">
                    {block name='cart_overview'}
                        {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
                    {/block}
                </div>

                <!-- shipping informations -->
                {block name='hook_shopping_cart_footer'}
                    {hook h='displayShoppingCartFooter'}
                {/block}
            </div>

            <!-- Right Block: cart subtotal & cart total -->
            {* PLANATEC
            <div class="cart-grid-right col-xs-12 col-lg-4">
            *}
            <div class="cart-grid-right">

                {block name='cart_summary'}
                    <div class="card cart-summary">

                        {block name='hook_shopping_cart'}
                            {hook h='displayShoppingCart'}
                        {/block}

                        {block name='cart_totals'}
                            {include file='checkout/_partials/cart-detailed-totals.tpl' cart=$cart}
                        {/block}

                        {block name='cart_actions'}
                            {include file='checkout/_partials/cart-detailed-actions.tpl' cart=$cart}
                        {/block}

                    </div>
                {/block}

                {block name='hook_reassurance'}
                    {hook h='displayReassurance'}
                {/block}

            </div>
            {* PLANATEC *}
            <div class="clearfix"></div>
            <section id="products" class="product-accessories">
                <p class="h5 text-uppercase">{l s='Complete your order' d='Shop.Theme.Catalog'}</p>
                <div id="js-product-list">
                    <div class="products">
                        {foreach from=$suggestedProductsInCart item="productInstalacionMontaje" key="position" name="productIteration"}
                            {block name='product_miniature'}
                                {include file='catalog/_partials/miniatures/product.tpl' product=$productInstalacionMontaje position=$position productIteration=$smarty.foreach.productIteration.iteration productClasses="col-xs-6 col-lg-4 col-xl-3" isAccessory=true totalAccessories=$suggestedProductsInCart|count}
                            {/block}
                        {/foreach}
                    </div>
                </div>
            </section>
            {* END PLANATEC *}

        </div>
    </section>
{/block}
