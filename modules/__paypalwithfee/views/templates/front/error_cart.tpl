{*
* 2025 4webs
*
* DEVELOPED By 4webs Prestashop Platinum Partner
*
* @author    4webs
* @copyright 4webs 2025
* @version 5.4.6
* @category payment_gateways
* @license 4webs
*}
{extends file='page.tpl'}

{block name="page_content"}
    <h3>Error!</h3>
    <p style="border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 15px;color: #D8000C;background-color: #FFBABA;">{l s='The products in your cart have changed.' mod='paypalwithfee'}</p>
    <p style="border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 15px;color: #00529B;background-color: #BDE5F8;">{l s='Please contact the store indicating the following reference: ' mod='paypalwithfee'}<strong>{$id}</strong></p>
{/block}