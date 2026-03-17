{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Revolut
 * @copyright Since 2020 Revolut
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 *}

<form id="payByBankOptionForm" method="post" action="{$formActionValidation|escape:'htmlall':'UTF-8'}">
    <p>
       {l s='You will be able to select your bank of choice after pressing the place order button' mod='revolutpayment' }
    </p>
    <input type="hidden" name="public_id" value="" id="payByBankRevolutPublicId" />
           
    <input type="hidden" name="payment_method" id="isPayByBank" 
           value="pay_by_bank" />

    <input type="hidden" name="merchant_public_key" id="merchantPublicKey" 
           value="{$merchant_public_key|escape:'htmlall':'UTF-8'}" />
           
    <input type="hidden" id="revolutLocale" name="locale" 
           value="{$locale|escape:'htmlall':'UTF-8'}" />
           
    <input type="hidden" id="payByBankInstantPaymentsOnly" 
           name="payByBankInstantPaymentsOnly" 
           value="{$payByBankInstantPaymentsOnly|escape:'htmlall':'UTF-8'}" />
           
    <div class="error hidden"></div>
    <div id="dataBankBrands" data-bank-brands="{$bankBrands|escape:'htmlall':'UTF-8'}"/>
</form>