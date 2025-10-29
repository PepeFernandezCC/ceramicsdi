{*
* 2023 4webs
*
* DEVELOPED By 4webs Prestashop Platinum Partner
*
* @author    4webs
* @copyright 4webs 2023
* @version 5.4.1
* @category payment_gateways
* @license 4webs
*}
{if $product_price >= 30 && $product_price <= 2000}
	{*<div id="paypal-button-container"></div>*}
	<script>
		paypal.Buttons({
			createOrder: function(data, actions) {
				return actions.order.create({
					purchase_units: [{
						amount: {
							value: '{$product_price|escape:'html':'UTF-8'}'
						}
					}]
				});
			}
		});
	</script>

	<div style="margin-top: 15px;" data-pp-message data-pp-amount="{$product_price|escape:'html':'UTF-8'}"></div>
{/if}