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
<table id="total-tab" width="100%">

  <tr>
    <td class="grey" width="50%">
      {l s='Total Products' d='Shop.Pdf' pdf='true'}
    </td>
    <td class="white" width="50%">
      {displayPrice currency=$order->id_currency price=$footer.products_before_discounts_tax_excl}
    </td>
  </tr>

  {if $footer.product_discounts_tax_excl > 0}

    <tr>
      <td class="grey" width="50%">
        {l s='Total Discounts' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white" width="50%">
        - {displayPrice currency=$order->id_currency price=$footer.product_discounts_tax_excl}
      </td>
    </tr>

  {/if}
  {if !$order->isVirtual()}
  <tr>
    <td class="grey" width="50%">
      {l s='Shipping Costs' d='Shop.Pdf' pdf='true'}
    </td>
    <td class="white" width="50%">
      {if $footer.shipping_tax_excl > 0}
        {displayPrice currency=$order->id_currency price=$footer.shipping_tax_excl}
      {else}
        {l s='Free Shipping' d='Shop.Pdf' pdf='true'}
      {/if}
    </td>
  </tr>
  {/if}

  {if $footer.wrapping_tax_excl > 0}
    <tr>
      <td class="grey">
        {l s='Wrapping Costs' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white">{displayPrice currency=$order->id_currency price=$footer.wrapping_tax_excl}</td>
    </tr>
  {/if}

  <tr class="bold">
    <td class="grey">
      {if $isTaxEnabled}
        {l s='Total (Tax excl.)' d='Shop.Pdf' pdf='true'}
      {else}
        {l s='Total' d='Shop.Pdf' pdf='true'}
      {/if}
    </td>
    <td class="white">
      {displayPrice currency=$order->id_currency price=$footer.total_paid_tax_excl}
    </td>
  </tr>
  {if $isTaxEnabled}
    {if $footer.total_taxes > 0}
      <tr class="bold">
        <td class="grey">
          {l s='Value Added Tax' d='Shop.Theme.Checkout'}
        </td>
        <td class="white">
          {displayPrice currency=$order->id_currency price=$footer.total_taxes}
        </td>
      </tr>
    {/if}
    <tr class="bold big">
      <td class="grey">
        {l s='Total' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white">
        {displayPrice currency=$order->id_currency price=$footer.total_paid_tax_incl}
      </td>
    </tr>

  {/if}
</table>

{* PLANATEC *}
{if '6'|in_array:Customer::getGroupsStatic($order->id_customer)}
  <br>
  <span class="small" style="text-align: center;">{l s='TRANSACTION EXEMPT FROM VAT ACCORDING TO ARTICLE 21 OF LAW 37/1992, OF 28 DECEMBER 1992, ON VALUE ADDED TAX' d='Shop.Pdf' pdf='true'}</span>
{/if}
{* END PLANATEC *}