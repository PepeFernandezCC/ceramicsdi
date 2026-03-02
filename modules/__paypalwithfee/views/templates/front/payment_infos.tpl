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

<section {if $fee_real['fee_with_tax'] == 0} style="display:none;"  {/if} >
  <p>{l s='Payment by paypal have a extra fee:' mod='paypalwithfee'}
    <dl>
      <dt id="ppalwf_tfee">{l s='Fee' mod='paypalwithfee'}</dt>
      <dd id="ppalwf_fee" fee_amount="{$fee|escape:'html':'UTF-8'}"  no_fee="{if $fee_real['fee_with_tax'] == 0}true{else}false{/if}">{$fee|escape:'html':'UTF-8'}</dd>
      <dt>{l s='Total Order' mod='paypalwithfee'}</dt>
      <dd id="ppalwf_total" total_amount="{$total_amount|escape:'html':'UTF-8'}">{$total_amount|escape:'html':'UTF-8'}</dd>
    </dl>
  </p>
</section>
{*{if {$cart['totals']['total']['amount']} >= 30 && {$cart['totals']['total']['amount']} <= 2000}*}
{*  <script src="https://www.paypal.com/sdk/js?client-id={$client_id}&currency={$currency}&components=messages,buttons&enable-funding=paylater"></script>*}
{*  *}{*<div id="paypal-button-container"></div>*}
{*  <script>*}
{*    paypal.Buttons({*}
{*      createOrder: function(data, actions) {*}
{*        return actions.order.create({*}
{*          purchase_units: [{*}
{*            amount: {*}
{*              value: '{$cart['totals']['total']['amount']}'*}
{*            }*}
{*          }]*}
{*        });*}
{*      }*}
{*    });*}
{*  </script>*}

{*  <div style="margin-top: 15px;" data-pp-message data-pp-amount="{$cart['totals']['total']['amount']}"></div>*}
{*{/if}*}