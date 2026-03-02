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
<table id="payment-tab" width="100%">
    <tr>
        <td class="payment center small grey bold" width="44%">{l s='Payment Method' mod='paypalwithfee' pdf='true'}</td>
        <td class="payment left white" width="56%">
            <table width="100%" border="0">
                {foreach from=$order_invoice->getOrderPaymentCollection() item=payment}
                    <tr>
                        <td class="right small">{$payment->payment_method|escape:'html':'UTF-8'}</td>
                        <td class="right small">{displayPrice currency=$payment->id_currency price=$payment->amount}</td>
                    </tr>
                {/foreach}
            </table>
        </td>
    </tr>
</table>
