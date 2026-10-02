{*
* CERAMIC CONNECTION - Delivery estimate saved with the order (admin order page).
*}
<div class="card mt-2" id="ccDeliveryEstimateAdmin">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="material-icons">local_shipping</i>
            {l s='Plazo de entrega estimado' mod='ccdeliveryestimate'}
        </h3>
    </div>
    <div class="card-body">
        <p class="mb-1">{l s='Preparación' mod='ccdeliveryestimate'}: <strong>{$cc_delivery_estimate.preparation_days} {l s='días hábiles' mod='ccdeliveryestimate'}</strong></p>
        <p class="mb-1">{l s='Envío' mod='ccdeliveryestimate'}: <strong>{if $cc_delivery_estimate.shipping_days_min != $cc_delivery_estimate.shipping_days_max}{$cc_delivery_estimate.shipping_days_min} - {/if}{$cc_delivery_estimate.shipping_days_max} {l s='días hábiles' mod='ccdeliveryestimate'}</strong>{if $cc_delivery_estimate.province} ({$cc_delivery_estimate.province}){/if}</p>
        <p class="mb-1">{l s='Entrega estimada' mod='ccdeliveryestimate'}: <strong>{$cc_delivery_estimate.delivery_date_min_formatted} - {$cc_delivery_estimate.delivery_date_max_formatted}</strong></p>
        {if $cc_delivery_estimate.products}
            <ul class="mb-1">
                {foreach from=$cc_delivery_estimate.products item=product}
                    <li>{$product.name} (x{$product.quantity}): {$product.preparation_days} {l s='días prep.' mod='ccdeliveryestimate'}, {$product.delivery_date_min_formatted} - {$product.delivery_date_max_formatted}</li>
                {/foreach}
            </ul>
        {/if}
        <small class="text-muted">{l s='Calculado al crear el pedido' mod='ccdeliveryestimate'}: {dateFormat date=$cc_delivery_estimate.date_add full=1}</small>
    </div>
</div>
