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
{extends file='page.tpl'}

{block name="page_content"}
<h3>Error!</h3>
<p style="border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 15px;color: #D8000C;background-color: #FFBABA;">{l s='There was an error in the payment process by Paypal.' mod='paypalwithfee'}</p>
<p style="border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 15px;color: #00529B;background-color: #BDE5F8;">{l s='Kindly try again or contact your store.' mod='paypalwithfee'}</p>
<h4>{l s='Paypal request errors' mod='paypalwithfee'}</h4>
<pre>
    {$error_paypal|@print_r|escape:'html':'UTF-8'}
</pre>
<h4>{l s='Paypal response errors' mod='paypalwithfee'}</h4>
<pre>
    {$response_paypal|@print_r|escape:'html':'UTF-8'}
</pre>
{/block}