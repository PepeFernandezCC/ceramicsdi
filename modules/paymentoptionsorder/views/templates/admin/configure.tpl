{*
 * CERAMIC CONNECTION - Payment options order
 * Backoffice drag & drop screen to reorder checkout payment options.
 *}
<div class="panel">
    <div class="panel-heading">
        <i class="icon-sort-by-order"></i> {l s='Orden de metodos de pago en el checkout' mod='paymentoptionsorder'}
    </div>
    {if $poo_items|count > 0}
        <p>{l s='Arrastra para reordenar. El orden se aplica de arriba (primero) a abajo (ultimo).' mod='paymentoptionsorder'}</p>

        <form action="{$poo_form_action|escape:'html':'UTF-8'}" method="post">
            <ul class="poo-list" id="poo-list">
                {foreach from=$poo_items item="poo_item"}
                    <li class="poo-item" draggable="true" data-key="{$poo_item.key|escape:'html':'UTF-8'}">
                        <span class="poo-handle">&#9776;</span>
                        <span class="poo-label">{$poo_item.label|escape:'html':'UTF-8'}</span>
                    </li>
                {/foreach}
            </ul>
            <input type="hidden" name="payment_options_order" id="poo-order-input" value="">
            <button type="submit" name="submitPaymentOptionsOrder" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Guardar' mod='paymentoptionsorder'}
            </button>
        </form>
    {else}
        <div class="alert alert-info">
            {l s='Todavia no se ha detectado ningun metodo de pago. Visita el checkout de la tienda (o completa un pedido de prueba) con cada metodo de pago activo para que aparezcan aqui.' mod='paymentoptionsorder'}
        </div>
    {/if}
</div>
