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
<table id="shipping-tab" width="100%">
    <tr>
        <td class="shipping center small grey bold" width="44%">{l s='Carrier' mod='paypalwithfee' pdf='true'}</td>
        <td class="shipping center small white" width="56%">{$carrier->name|escape:'html':'UTF-8'}</td>
    </tr>
</table>
