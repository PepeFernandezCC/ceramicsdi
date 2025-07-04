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

<div class="card-block cart-summary-subtotals-container js-cart-summary-subtotals-container">

    {* PLANATEC *}
    <p>
        {l s='Total shipping weight' d='Shop.Theme.Checkout'}&nbsp;
        <span class="value" style="font-weight: bold;">{Context::getContext()->cart->getTotalWeight()|string_format:"%.2f"|replace:'.':','} {Configuration::get('PS_WEIGHT_UNIT')}</span>
    </p>
    {* END PLANATEC *}

    {foreach from=$cart.subtotals item="subtotal"}
        {if $subtotal && $subtotal.value|count_characters > 0 && $subtotal.type !== 'tax'}
            <div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-{$subtotal.type}">

        <span class="label">
            {$subtotal.label}
        </span>

                <span class="value">
          {if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value}
        </span>
            </div>
        {/if}
    {/foreach}

</div>

