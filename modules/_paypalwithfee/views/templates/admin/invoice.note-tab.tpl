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
{if isset($order_invoice->note) && $order_invoice->note}
    <tr>
        <td colspan="12" height="10">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="6" class="left">
            <table id="note-tab" style="width: 100%">
                <tr>
                    <td class="grey">{l s='Note' mod='paypalwithfee' pdf='true'}</td>
                </tr>
                <tr>
                    <td class="note">{$order_invoice->note|nl2br|escape:'htmlall':'UTF-8'}</td>
                </tr>
            </table>
        </td>
        <td colspan="1">&nbsp;</td>
    </tr>
{/if}
