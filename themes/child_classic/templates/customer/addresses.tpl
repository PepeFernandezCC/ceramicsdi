{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{extends file='customer/page.tpl'}
{* Filtrar direcciones según is_invoice *}
{assign var='delivery_addresses' value=[]}
{assign var='invoice_addresses' value=[]}
{foreach from=$customer.addresses item=address}

  {if $address.is_invoice == 0 || $address.is_invoice == 2}
    {append var='delivery_addresses' value=$address}
  {/if}

  {if $address.is_invoice == 1 || $address.is_invoice == 2}
    {append var='invoice_addresses' value=$address}
  {/if}
        
{/foreach}

{block name='page_title'}
  {l s='Your addresses' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  <div class="customer-addresses-body">
    <div class="customer-addresses-warning"> {l s='Puede gestionar sus direcciones desde el proceso de compra' d='Shop.Theme.Checkout'} </div>
    <div class="customer-addresses-box">
      <div class="customer-item-addresses">
        <div class="customer-addresses-type-title"><h2>{l s='Direcciones de envío' d='Shop.Theme.Checkout'}</h2></div>
        <div class="customer-address-item">
          {foreach $delivery_addresses as $address}    
              {block name='customer_address'}
                {include file='customer/_partials/block-address.tpl' 
                  address     = $address
                  type        = "delivery"
                }
              {/block}
          {/foreach}
        </div>
      </div>

      <div class="customer-item-addresses">
        <div class="customer-addresses-type-title"><h2>{l s='Direcciones de facturación' d='Shop.Theme.Checkout'}</div>
        <div class="customer-address-item">
          {foreach $invoice_addresses as $address}
              {block name='customer_address'}
                {include file='customer/_partials/block-address.tpl' 
                  address     = $address
                  type        = "invoice"
                }
              {/block}
          {/foreach}
        </div>
      </div>
    
    </div>

  </div>

{/block}
