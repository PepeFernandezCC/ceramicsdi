{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
 {*
		<table id="payment-tab" width="100%" cellpadding="4" cellspacing="0">
			<tr>
				<td class="payment center small grey bold" width="44%">{l s='Payment Method' d='Shop.Pdf' pdf='true'}</td>
				<td class="payment left white" width="56%">
					<table width="100%" border="0">
						{foreach from=$order_invoice->getOrderPaymentCollection() item=payment}
							<tr>
								<td class="right small">{$payment->payment_method}</td>
								<td class="right small">{displayPrice currency=$payment->id_currency price=$payment->amount}</td>
							</tr>
						{foreachelse}
							<tr>
								<td>{l s='No payment' d='Shop.Pdf' pdf='true'}</td>
							</tr>
						{/foreach}
					</table>
				</td>
			</tr>
		</table>
*}
{if isset($carrier)}
	<table id="carrier-tab" width="100%">
		<tr>
			<th class="header small" valign="middle">{l s='Carrier' d='Shop.Pdf' pdf='true'}</th>
		</tr>
		<tr>
			<td class="center small white">
				<span style="font-size:10px">
					{$carrier->name}
				</span>
			</td>
		</tr>
	</table>
{/if}

