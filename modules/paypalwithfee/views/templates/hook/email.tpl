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
{if $fee > 0}
<div style="-webkit-text-size-adjust:none;background-color:#fff;width:650px;font-family:Open-sans, sans-serif;color:#555454;font-size:13px;line-height:18px;margin:auto" >
    <table class="table table-mail" style="width:100%;margin-top:10px;-moz-box-shadow:0 0 5px #afafaf;-webkit-box-shadow:0 0 5px #afafaf;-o-box-shadow:0 0 5px #afafaf;box-shadow:0 0 5px #afafaf;filter:progid:DXImageTransform.Microsoft.Shadow(color=#afafaf,Direction=134,Strength=5)">
        <tbody>
            <tr>
                <th style="background-color: #4d4d4d; color: #ffffff"><strong>{l s='Concept' mod='paypalwithfee'}{*{l s='Concept' d='Modules.Paypalwithfee.Shop'}*}</strong></th>
                <th style="background-color: #4d4d4d; color: #ffffff">{l s='Fee' mod='paypalwithfee'}{*{l s='Fee' d='Modules.Paypalwithfee.Shop'}*}</th>
            </tr>
            <tr>
                <td style="background-color: #dddddd;">{l s='PayPal with Fee' mod='paypalwithfee'}{*{l s='PayPal with Fee' d='Modules.Paypalwithfee.Shop'}*}</td>
                <td style="background-color: #dddddd;">{$fee|escape:'html':'UTF-8'}</td>
            </tr>
        </tbody>
        <tfoot>
            {l s='* Fee has been added into shipping cost.' mod='paypalwithfee'}{*{l s='* Fee has been added into shipping cost.' d='Modules.Paypalwithfee.Shop'}*}
        </tfoot>
        </tr>
    </table>
</div>
{/if}        