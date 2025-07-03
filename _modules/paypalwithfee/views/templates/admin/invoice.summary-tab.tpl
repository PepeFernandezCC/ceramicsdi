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
<table id="summary-tab" width="100%">
    <tr>
        <th class="header small" valign="middle">{l s='Invoice Number' mod='paypalwithfee' pdf='true'}</th>
        <th class="header small" valign="middle">{l s='Invoice Date' mod='paypalwithfee' pdf='true'}</th>
        <th class="header small" valign="middle">{l s='Order Reference' mod='paypalwithfee' pdf='true'}</th>
        <th class="header small" valign="middle">{l s='Order date' mod='paypalwithfee' pdf='true'}</th>
            {if $addresses.invoice->vat_number}
            <th class="header small" valign="middle">{l s='VAT Number' mod='paypalwithfee' pdf='true'}</th>
            {/if}
    </tr>
    <tr>
        <td class="center small white">{$title|escape:'html':'UTF-8'}</td>
        <td class="center small white">{dateFormat date=$order->invoice_date full=0}</td>
        <td class="center small white">{$order->getUniqReference()|escape:'html':'UTF-8'}</td>
        <td class="center small white">{dateFormat date=$order->date_add full=0}</td>
        {if $addresses.invoice->vat_number}
            <td class="center small white">
                {$addresses.invoice->vat_number|escape:'html':'UTF-8'}
            </td>
        {/if}
    </tr>
</table>
