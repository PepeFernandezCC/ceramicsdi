{block name='content'}
{if $orders|@count > 0}
    <form method="post" style="margin-bottom: 15px;">
        <button class="btn btn-success" type="submit" name="importAllOrders" value="1">
            Importar todos
        </button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Referencia ManoMano</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$orders item=order}
            <tr>
                <td>{$order.order_reference|escape:'html'}</td>
                <td>
                    {assign var="dt" value=$order.created_at|date_create}
                    {$dt|date_format:"%d/%m/%Y %H:%M"}
                </td>
                <td>{$order.customer.firstname|escape:'html'} {$order.customer.lastname|escape:'html'}</td>
                <td>{$order.total_price.amount} {$order.total_price.currency}</td>
                <td>{$order.status|escape:'html'}</td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="importOrder" value="1" />
                        <input type="hidden" name="order_payload" value="{$order|json_encode|escape:'html'}" />
                        <button class="btn btn-primary" type="submit">Importar a Prestashop</button>
                    </form>
                    
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

{else}
    <div style="background: white;padding: 2%;width: fit-content; border: aliceblue 1px solid;">
        <h2>No Hay Pedidos Para Importar</h2>
    </div>
{/if}

{/block}
