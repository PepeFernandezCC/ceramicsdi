{*
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*}
<div class="fail-hide" id="shippingCalculatorDeliveryBlock" style="margin-top:25px" >
{if isset($has_delivery_info) && $has_delivery_info && isset($estimated_delivery) && $estimated_delivery}
    {if isset($estimated_delivery.mode) && $estimated_delivery.mode == 'by_product'}
        {* Modo por producto: mostrar plazos individuales (similar diseño) *}
        {foreach from=$estimated_delivery.products item=product_delivery}
            <div class="shipping-estimate">
                <h4 class="shipping-estimate-title">{l s='PLAZO DE ENTREGA ESTIMADO' mod='shippingcalculator'}</h4>
                <div class="shipping-steps">
                        <div class="shipping-step">
                            <img src="{$module_dir}views/img/icon-preparation.png" alt="Preparación" class="shipping-icon">
                            <div class="shipping-meta">
                                <span class="shipping-label">{l s='Preparación:' mod='shippingcalculator'}</span>
                                <span class="shipping-value">{$product_delivery.preparation_days} {if $product_delivery.preparation_days == 1}{l s='día' mod='shippingcalculator'}{else}{l s='días' mod='shippingcalculator'}{/if}</span>
                            </div>
                        </div>
                    <div class="shipping-step">
                        <img src="{$module_dir}views/img/icon-shipping.png" alt="Envío" class="shipping-icon">
                        <div class="shipping-meta">
                            <span class="shipping-label">{l s='Envío:' mod='shippingcalculator'}</span>
                            <span class="shipping-value">{$product_delivery.shipping_days} {if $product_delivery.shipping_days == 1}{l s='día' mod='shippingcalculator'}{else}{l s='días' mod='shippingcalculator'}{/if}</span>
                        </div>
                    </div>
                </div>
                <div class="shipping-estimated-range">{l s='ENTREGA ESTIMADA:' mod='shippingcalculator'} <strong>{$product_delivery.start_date_formatted} - {$product_delivery.end_date_formatted}</strong></div>
                <p class="shipping-note">{l s='El plazo de entrega indicado es aproximado y puede sufrir variaciones.' mod='shippingcalculator'}</p>
            </div>
        {/foreach}
    {else}
        {* Modo combinado: diseño principal *}
        <div class="shipping-estimate">
            <h4 class="shipping-estimate-title">{l s='PLAZO DE ENTREGA ESTIMADO' mod='shippingcalculator'}</h4>
            <div class="shipping-steps">
                <div class="shipping-step">
                    <img src="{$module_dir}views/img/icon-preparation.png" alt="Preparación" class="shipping-icon">
                    <div class="shipping-meta">
                        <span class="shipping-label">{l s='Preparación:' mod='shippingcalculator'}</span>
                        <span class="shipping-value">{$estimated_delivery.preparation_days} {if $estimated_delivery.preparation_days == 1}{l s='día' mod='shippingcalculator'}{else}{l s='días' mod='shippingcalculator'}{/if}</span>
                    </div>
                </div>
                <div class="shipping-step">
                    <img src="{$module_dir}views/img/icon-shipping.png" alt="Envío" class="shipping-icon">
                    <div class="shipping-meta">
                        <span class="shipping-label">{l s='Envío:' mod='shippingcalculator'}</span>
                        <span class="shipping-value">{if isset($estimated_delivery.shipping_days_min) && isset($estimated_delivery.shipping_days_max)}{$estimated_delivery.shipping_days_min} - {$estimated_delivery.shipping_days_max}{else}{$estimated_delivery.shipping_days}{/if} {l s='días' mod='shippingcalculator'}</span>
                    </div>
                </div>
            </div>
            <div class="shipping-estimated-range">{l s='ENTREGA ESTIMADA:' mod='shippingcalculator'} <strong>{$estimated_delivery.start_date_formatted} - {$estimated_delivery.end_date_formatted}</strong></div>
            <p class="shipping-note">{l s='El plazo de entrega indicado es aproximado y puede sufrir variaciones.' mod='shippingcalculator'}</p>
        </div>
    {/if}

{/if}
</div>
