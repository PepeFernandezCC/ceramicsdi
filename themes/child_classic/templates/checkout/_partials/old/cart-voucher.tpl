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
{if $cart.vouchers.allowed}
    {block name='cart_voucher'}
        <div class="block-promo">
            <div class="cart-voucher js-cart-voucher">
                {if $cart.vouchers.added}
                    {block name='cart_voucher_list'}
                        <ul class="promo-name card-block">
                            {foreach from=$cart.vouchers.added item=voucher}
                                <li class="cart-summary-line">
                                    <span class="label">{$voucher.name}</span>
                                    <div class="float-xs-right">
                                        <span>{$voucher.reduction_formatted}</span>
                                        {if isset($voucher.code) && $voucher.code !== ''}
                                            <a href="{$voucher.delete_url}" data-link-action="remove-voucher"><i class="material-icons">&#xE872;</i></a>
                                        {/if}
                                    </div>
                                </li>
                            {/foreach}
                        </ul>
                    {/block}
                {/if}

                {assign var="countryList" value=country::getCountries($language.id)}
                
                <div id="deliveryPriceCalculator" class="form-group row scc-main" {if isset($psc_visible)}style="display:none"{/if}>
                    <div style="margin-bottom: 10px">
                        <span class="scc-title">{l s='calculate your shipping costs' d='Shop.Theme.Checkout'}</span>
                    </div>

                    <div class="deliveryPriceCalculatorFormBox">
                        {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
                            <input type="hidden" id="showTaxes" value="0">
                        {else}
                            <input type="hidden" id="showTaxes" value="1">
                        {/if}
                        <input type="hidden" id="language" value="{$language.id}">
                        <input type="hidden" id="packageWeight" name="packageWeight" value="{Context::getContext()->cart->getTotalWeight()}" />
                        <input type="hidden" id="cartId" name="cartId" value="{Context::getContext()->cart->id}" />

                        <div id="country-selector-box">
                            <select id="field-id_country" class="form-control form-control-select scc-select" name="id_country">
                                <option value="">{l s='Select a country' d='Shop.Theme.Checkout'}</option>
                                {foreach from=$countryList item=$country}
                                    {if in_array($country.id_country, $VALID_COUNTRIES)}
                                        <option value="{$country.id_country}">{$country.name}</option>
                                    {/if}
                                {/foreach}
                            </select>
                        </div>
                       
                        <div id="province-selector-box">
                            <select id="field-id_state" class="form-control form-control-select scc-select" name="id_state">
                                <option value="">{l s='Select a state' d='Shop.Theme.Checkout'}</option>
                            </select>
                        </div>
                        
                        <div>
                            <input type="number" name="postalzip" id="postalzip" inputmode="numeric" value="" 
                            class="input-group scc-postalcode-input" aria-label="Total" data-price="0" placeholder="{l s='Postal code' d='Shop.Forms.Labels'}">
                        </div>
                       
                    </div>
                    <div class="deliveryPriceCalculatorResult d-flex scc-result" style="position:relative">
                        
                        <button id="calculateMyDeliveryButton" class="scc-button">{l s='Get shipping costs' d='Shop.Theme.Checkout'}</button>

                        <input type="number" name="euros" id="euros-input" inputmode="numeric" step="0.01" min="0.00" value="0.00" 
                        class="input-group boxInput cc-background-color-secondary" aria-label="Total" readonly="readonly" data-price="0" 
                        style="font-weight: bold; font-size: 17px">
                        <div class="scc-coin">€</div>
                    </div>

                    <div id="messageContainer" class="alert alert-danger" style="display: none">{l s='Please, complete all the form fields' d='Shop.Theme.Checkout'}</div>
                </div>


                <div id="promo-code" class="{if $cart.discounts|count > 0} in{/if}">
                    <div class="promo-code">
                        <div class="row">
                            <div class="col-xs-12 col-lg-4 text-uppercase">
                                {l s='Promo code' d='Shop.Theme.Checkout'}
                            </div>
                            <div class="col-xs-12 col-lg-8">
                                {block name='cart_voucher_form'}
                                    <form action="{$urls.pages.cart}" data-link-action="add-voucher" method="post">
                                        <input type="hidden" name="token" value="{$static_token}">
                                        <input type="hidden" name="addDiscount" value="1">
                                        <input class="promo-input" type="text" name="discount_name" placeholder="{l s='Promo code' d='Shop.Theme.Checkout'}">
                                        <button type="submit" class="btn btn-primary"><span>{l s='Add' d='Shop.Theme.Actions'}</span></button>
                                    </form>
                                {/block}

                                {block name='cart_voucher_notifications'}
                                    <div class="alert alert-danger js-error" role="alert">
                                        <i class="material-icons">&#xE001;</i><span class="ml-1 js-error-text"></span>
                                    </div>
                                {/block}
                            </div>
                        </div>


                    </div>
                </div>

                {if $cart.discounts|count > 0}
                    <p class="block-promo promo-highlighted">
                        {l s='Take advantage of our exclusive offers:' d='Shop.Theme.Actions'}
                    </p>
                    <ul class="js-discount card-block promo-discounts">
                        {foreach from=$cart.discounts item=discount}
                            <li class="cart-summary-line">
                <span class="label">
                  <span class="code">{$discount.code}</span> - {$discount.name}
                </span>
                            </li>
                        {/foreach}
                    </ul>
                {/if}
            </div>
        </div>
    {/block}
{/if}
