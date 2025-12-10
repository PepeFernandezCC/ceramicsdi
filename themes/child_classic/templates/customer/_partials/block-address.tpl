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
{block name='address_block_item'}
  <div id="address-{$address.id}" class="customer-address-data" data-id-address="{$address.id}">
    <div class="customer-address-owner">
        {if $type == "invoice" && $address.alias == 'COMPANY'}
          <div class="company-info"><strong>{$address.company}</strong> - {$address.dni}</div>
        {else}
          <div class="customer-info"><strong>{$address.firstname} {$address.lastname}</strong>{if $address.is_invoice != '0'} - {$address.dni}{/if}</div>
        {/if}
        <div class="address-info">{$address.address1}, {$address.postcode}, {$address.city}, {$address.state}, {$address.country}</div>
    </div>
  </div>
{/block}
