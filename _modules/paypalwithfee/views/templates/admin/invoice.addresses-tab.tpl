{*
* 2020 4webs
*
* DEVELOPED By 4webs Prestashop Platinum Partner
*
* @author    4webs
* @copyright 4webs 2017
* @version 5.1.4
* @category payment_gateways
* @license 4webs
*}
<table id="addresses-tab" cellspacing="0" cellpadding="0">
    <tr>
        <td width="33%"><span class="bold"> </span><br/><br/>
        {if isset($order_invoice)}{$order_invoice->shop_address|escape:'html':'UTF-8'}{/if}
    </td>
    <td width="33%">{if $delivery_address}<span class="bold">{l s='Delivery Address' mod='paypalwithfee' pdf='true'}</span><br/><br/>
        {$delivery_address|escape:'html':'UTF-8'}
        {/if}
        </td>
        <td width="33%"><span class="bold">{l s='Billing Address' mod='paypalwithfee' pdf='true'}</span><br/><br/>
            {$invoice_address|escape:'html':'UTF-8'}
        </td>
    </tr>
</table>
