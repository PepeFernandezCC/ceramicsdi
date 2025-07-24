{extends file='page.tpl'}

{block name='page_content_container' prepend}
    {* PLANATEC *}
    <section id="content" class="section-confirmation">
        <div class="row checkout-grid">
            <div class="cart-grid-body col-xs-12 col-lg-7">
                {$tabsName = ['checkout-personal-information-step', 'checkout-addresses-step', 'checkout-delivery-step', 'checkout-payment-step', 'checkout-confirmation']}

                <div id="planatec-tabs">
                    <div id="planatec-step-title-1" class="planatec-step-title col-xs-12 col-md-2-5">
                        1.<br>{l s='Personal data' d='Shop.Theme.Checkout'}
                    </div>
                    <div id="planatec-step-title-2" class="planatec-step-title col-xs-12 col-md-2-5">
                        2.<br>{l s='Address' d='Shop.Theme.Checkout'}
                    </div>
                    <div id="planatec-step-title-3" class="planatec-step-title col-xs-12 col-md-2-5">
                        3.<br>{l s='Shipping method' d='Shop.Theme.Checkout'}
                    </div>
                    <div id="planatec-step-title-4" class="planatec-step-title col-xs-12 col-md-2-5">
                        4.<br>{l s='Payment' d='Shop.Theme.Checkout'}
                    </div>
                    <div id="planatec-step-title-5" class="planatec-step-title col-xs-12 col-md-2-5"
                         style="background-color: black; color: white;">
                        5.<br>{l s='Confirmation' d='Shop.Theme.Checkout'}
                    </div>
                </div>

                <section class="block-left">
                    {block name='order_confirmation_header'}
                        <h4 class="h4 card-title">
                            {l s='Your order is confirmed!' d='Shop.Theme.Checkout'}
                        </h4>
                    {/block}

                    <div>
                        <p>
                            {l s='An email has been sent to your mail address' d='Shop.Theme.Checkout'}&nbsp;<strong>{$customer.email}</strong>.
                            {if $order.details.invoice_url}
                                {* [1][/1] is for a HTML tag. *}
                                {l
                                s='You can also [1]download your invoice[/1]'
                                d='Shop.Theme.Checkout'
                                sprintf=[
                                '[1]' => "<a href='{$order.details.invoice_url}'>",
                                '[/1]' => "</a>"
                                ]
                                }.
                            {/if}
                        </p>
                    </div>

                    {block name='hook_order_confirmation'}
                        {$HOOK_ORDER_CONFIRMATION nofilter}
                    {/block}

                    {block name='order_details'}
                        <div id="order-details" class="row">
                            <div class="col-md-4 col-xs-12">
                                {l s='Order reference' d='Shop.Theme.Checkout'}:
                            </div>
                            <div class="col-md-8 col-xs-12">
                                {$order.details.reference}
                            </div>

                            <div class="col-md-4 col-xs-12">
                                {l s='Payment method' d='Shop.Theme.Checkout'}:
                            </div>
                            <div class="col-md-8 col-xs-12">
                                {$order.details.payment}
                            </div>

                            {if !$order.details.is_virtual}
                                <div class="col-md-4 col-xs-12">
                                    {l s='Shipping method' d='Shop.Theme.Checkout'}:
                                </div>
                                <div class="col-md-8 col-xs-12">
                                    {$order.carrier.name} <em>({$order.carrier.delay})</em>
                                </div>
                            {/if}
                        </div>
                    {/block}

                    {block name='hook_payment_return'}
                        {if ! empty($HOOK_PAYMENT_RETURN)}
                            <section id="content-hook_payment_return" class="card definition-list">
                                <div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            {$HOOK_PAYMENT_RETURN nofilter}
                                        </div>
                                    </div>
                                </div>
                            </section>
                        {/if}
                    {/block}

                    {block name='customer_registration_form'}
                        {if $customer.is_guest}
                            <div id="registration-form" class="card" style="width:100%">
                                <div class="card-block" style="padding: 0">
                                    <h4 class="h4">{l s='Save time on your next order, sign up now' d='Shop.Theme.Checkout'}</h4>
                                    {render file='customer/_partials/customer-form.tpl' ui=$register_form  step="confirmation"}
                                </div>
                            </div>
                        {/if}
                    {/block}

                    {block name='hook_order_confirmation_1'}
                        {hook h='displayOrderConfirmation1'}
                    {/block}
                </section>
            </div>

            <div class="cart-grid-right col-xs-12 col-lg-5">
                <section class="card js-cart">
                    <div id="planatec-summary">
                        <h3>{l s='Order summary' d='Shop.Theme.Customeraccount'}</h3>
                    </div>
                    <div class="card-block">
                        <div class="cart-summary-products js-cart-summary-products">
                            {block name='cart_summary_product_list'}
                                <div class="{*PLANATEC collapse *}" id="cart-summary-product-list">
                                    <ul class="media-list">
                                        {foreach from=$order.products item=product}
                                            <li class="media">{include file='checkout/_partials/cart-summary-product-line.tpl' product=$product}</li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/block}
                        </div>

                        <div class="card-block cart-summary-subtotals-container js-cart-summary-subtotals-container">
                            {foreach from=$order.subtotals item="subtotal"}
                                {if $subtotal && $subtotal.value|count_characters > 0 && $subtotal.type !== 'tax'}
                                    <div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-{$subtotal.type}">
                                        <span class="label">
                                            {$subtotal.label}
                                        </span>
                                        <span class="value">
                                          {if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value}
                                        </span>
                                    </div>
                                {/if}
                            {/foreach}
                        </div>
                </section>

                <div class="card-block cart-summary-totals js-cart-summary-totals">
                    {block name='cart_summary_total'}
                        {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
                            <div class="cart-summary-line">
                                <span class="label">{$order.totals.total.label}&nbsp;{$order.labels.tax_short}</span>
                                <span class="value">{$order.totals.total.value}</span>
                            </div>
                            <div class="cart-summary-line cart-total">
                                <span class="label">{$order.totals.total_including_tax.label}</span>
                                <span class="value">{$order.totals.total_including_tax.value}</span>
                            </div>
                        {else}
                            <div class="cart-summary-line cart-total">
                                <span class="label">{$order.totals.total.label}&nbsp;{if $configuration.taxes_enabled}{$order.labels.tax_short}{/if}</span>
                                <span class="value">{$order.totals.total.value}</span>
                            </div>
                        {/if}
                    {/block}

                    {block name='cart_summary_tax'}
                        {if $cart.subtotals.tax}
                            <div class="cart-summary-line">
                                <span class="label sub">{l s='%label%:' sprintf=['%label%' => $order.subtotals.tax.label] d='Shop.Theme.Global'}</span>
                                <span class="value sub">{$order.subtotals.tax.value}</span>
                            </div>
                        {/if}
                    {/block}
                </div>
            </div>
        </div>
    </section>
    {* END PLANATEC *}
    {* PLANATEC
    <section id="content-hook_order_confirmation" class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-md-12">

                    {block name='order_confirmation_header'}
                        <h3 class="h1 card-title">
                            <i class="material-icons rtl-no-flip done">&#xE876;</i>{l s='Your order is confirmed' d='Shop.Theme.Checkout'}
                        </h3>
                    {/block}

                    <p>
                        {l s='An email has been sent to your mail address %email%.' d='Shop.Theme.Checkout' sprintf=['%email%' => $customer.email]}
                        {if $order.details.invoice_url}
                            {* [1][/1] is for a HTML tag. *}
    {* PLANATEC {l
    s='You can also [1]download your invoice[/1]'
    d='Shop.Theme.Checkout'
    sprintf=[
    '[1]' => "<a href='{$order.details.invoice_url}'>",
    '[/1]' => "</a>"
    ]
    }
{/if}
</p>

{block name='hook_order_confirmation'}
{$HOOK_ORDER_CONFIRMATION nofilter}
{/block}

</div>
</div>
</div>
</section>*}
{/block}

{* PLANATEC *}
{block name='page_content_container'}
{/block}

{block name='page_footer_container'}
{/block}
{* END PLANATEC *}


{block name='page_content_container'}
    <section id="content" class="page-content page-order-confirmation card">
        <div class="card-block">
            <div class="row">

                {block name='order_confirmation_table'}
                    {include
                    file='checkout/_partials/order-confirmation-table.tpl'
                    products=$order.products
                    subtotals=$order.subtotals
                    totals=$order.totals
                    labels=$order.labels
                    add_product_link=false
                    }
                {/block}

                {block name='order_details'}
                    <div id="order-details" class="col-md-4">
                        <h3 class="h3 card-title">{l s='Order details' d='Shop.Theme.Checkout'}:</h3>
                        <ul>
                            <li id="order-reference-value">{l s='Order reference: %reference%' d='Shop.Theme.Checkout' sprintf=['%reference%' => $order.details.reference]}</li>
                            <li>{l s='Payment method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.details.payment]}</li>
                            {if !$order.details.is_virtual}
                                <li>
                                    {l s='Shipping method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.carrier.name]}
                                    <br>
                                    <em>{$order.carrier.delay}</em>
                                </li>
                            {/if}
                        </ul>
                    </div>
                {/block}

            </div>
        </div>
    </section>
    {block name='hook_payment_return'}
        {if ! empty($HOOK_PAYMENT_RETURN)}
            <section id="content-hook_payment_return" class="card definition-list">
                <div class="card-block">
                    <div class="row">
                        <div class="col-md-12">
                            {$HOOK_PAYMENT_RETURN nofilter}
                        </div>
                    </div>
                </div>
            </section>
        {/if}
    {/block}

    {block name='customer_registration_form'}
        {if $customer.is_guest}
            <div id="registration-form" class="card">
                <div class="card-block">
                    <h4 class="h4">{l s='Save time on your next order, sign up now' d='Shop.Theme.Checkout'}</h4>
                    {render file='customer/_partials/customer-form.tpl' ui=$register_form}
                </div>
            </div>
        {/if}
    {/block}

    {block name='hook_order_confirmation_1'}
        {hook h='displayOrderConfirmation1'}
    {/block}

    {block name='hook_order_confirmation_2'}
        <section id="content-hook-order-confirmation-footer">
            {hook h='displayOrderConfirmation2'}
        </section>
    {/block}
{/block}
*}