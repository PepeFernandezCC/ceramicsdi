{*
* CERAMIC CONNECTION - Delivery estimate saved with the order.
* Same markup as shippingcalculator (shopping_cart_delivery.tpl), whose front.css styles it.
*}
<div id="ccDeliveryEstimate" class="cc-delivery-estimate" style="margin:20px 0">
    <div class="shipping-estimate">
        <h4 class="shipping-estimate-title">{l s='PLAZO DE ENTREGA ESTIMADO' mod='ccdeliveryestimate'}</h4>
        <div class="shipping-steps">
            <div class="shipping-step">
                <img src="{$cc_delivery_img_dir}icon-preparation.png" alt="" class="shipping-icon">
                <div class="shipping-meta">
                    <span class="shipping-label">{l s='Preparación:' mod='ccdeliveryestimate'}</span>
                    <span class="shipping-value">{$cc_delivery_estimate.preparation_days} {if $cc_delivery_estimate.preparation_days == 1}{l s='día hábil' mod='ccdeliveryestimate'}{else}{l s='días hábiles' mod='ccdeliveryestimate'}{/if}</span>
                </div>
            </div>
            <div class="shipping-step">
                <img src="{$cc_delivery_img_dir}icon-shipping.png" alt="" class="shipping-icon">
                <div class="shipping-meta">
                    <span class="shipping-label">{l s='Envío:' mod='ccdeliveryestimate'}</span>
                    <span class="shipping-value">{if $cc_delivery_estimate.shipping_days_min != $cc_delivery_estimate.shipping_days_max}{$cc_delivery_estimate.shipping_days_min} - {/if}{$cc_delivery_estimate.shipping_days_max} {if $cc_delivery_estimate.shipping_days_max == 1}{l s='día hábil' mod='ccdeliveryestimate'}{else}{l s='días hábiles' mod='ccdeliveryestimate'}{/if}</span>
                </div>
            </div>
        </div>
        <div class="shipping-estimated-range">{l s='ENTREGA ESTIMADA:' mod='ccdeliveryestimate'} <strong>{$cc_delivery_estimate.delivery_date_min_formatted} - {$cc_delivery_estimate.delivery_date_max_formatted}</strong></div>
        {if $cc_delivery_estimate.products}
            <ul class="cc-delivery-estimate-products" style="list-style:none;padding:0;margin:10px 0 0;text-align:center;font-size:13px">
                {foreach from=$cc_delivery_estimate.products item=product}
                    <li>{$product.name} (x{$product.quantity}): <strong>{$product.delivery_date_min_formatted} - {$product.delivery_date_max_formatted}</strong></li>
                {/foreach}
            </ul>
        {/if}
        <p class="shipping-note">{l s='El plazo de entrega indicado es aproximado y puede sufrir variaciones.' mod='ccdeliveryestimate'}</p>
    </div>
</div>
