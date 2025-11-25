
{block name='cart_detailed_totals'}
    <div class="cart-detailed-totals js-cart-detailed-totals row">
        <div>
            <div class="col-lg-6 col-xs-12">
                {block name='cart_voucher'}
                    {include file='checkout/_partials/cart-voucher.tpl'}
                {/block}
            </div>

            <div class="col-lg-2 col-xs-12"></div>
            <div class="card-block cart-detailed-subtotals js-cart-detailed-subtotals col-lg-4 col-xs-12">
                {* PLANATEC *}
                <p>
                    {l s='Total shipping weight' d='Shop.Theme.Checkout'}&nbsp;
                    <span class="value" style="font-weight: bold;">{Context::getContext()->cart->getTotalWeight()|string_format:"%.2f"|replace:'.':','} {Configuration::get('PS_WEIGHT_UNIT')}</span>
                </p>
                {* END PLANATEC *}

                {foreach from=$cart.subtotals item="subtotal"}
                    {if $subtotal && $subtotal.value|count_characters > 0 && $subtotal.type !== 'tax'}
                        <div class="cart-summary-line" id="cart-subtotal-{$subtotal.type}">
                            <span class="label{if 'products' === $subtotal.type} js-subtotal{/if}">
                                {if 'products' == $subtotal.type}
                                    {$cart.summary_string}
                                {else}
                                    {$subtotal.label}
                                {/if}
                            </span>
                            <span class="value">

                                {assign var=free_fields value=["Gratis", "gratuit", "Free", "kostenlos", "Grátis", "Gratuit"]}
                                      
                                {if $subtotal.type === 'shipping'}

                                    {if ' ' == $subtotal.value || in_array($subtotal.value, $free_fields)}
                                        {l s='Pending' d='Shop.Theme.Checkout'}
                                    {else} 
                                        {$subtotal.value}  
                                    {/if}

                                {else}

                                    {if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value}

                                {/if}

                            </span>

                            {if $subtotal.type === 'shipping'}
                                <div>
                                    <small class="value">{hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}</small>
                                </div>
                            {/if}
                        </div>
                    {/if}
                {/foreach}

                {block name='cart_summary_tax'}
                    {if $cart.subtotals.tax}
                    <div class="cart-summary-line">
                        <span class="label sub">{l s='%label%:' sprintf=['%label%' => $cart.subtotals.tax.label] d='Shop.Theme.Global'}</span>
                        <span class="value sub">{$cart.subtotals.tax.value}</span>
                    </div>
                    {/if}
                {/block}

            </div>
        </div>
        <div>
            <div class="cart-detailed-totals-summary" style="padding: 15px;">
                <div class="col-lg-8 col-xs-12"></div>
                <div class="col-lg-4 col-xs-12">
                    {block name='cart_summary_totals'}
                        {include file='checkout/_partials/cart-summary-totals.tpl' cart=$cart}
                    {/block}
                </div>
            </div>
        </div>

    </div>

{/block}
