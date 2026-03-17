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

<div class="panel tab-pane fade{if $section == 'pay-by-bank-settings'} active in{/if}" 
     id="pay-by-bank-settings" 
     role="tabpanel" 
     aria-labelledby="pay-by-bank-settings-tab">

    <!-- Enable Pay by Bank -->
    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Enable Pay by bank' mod='revolutpayment'}
        </label>
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" 
                       name="REVOLUT_PAY_BY_BANK_METHOD_ENABLE" 
                       id="REVOLUT_PAY_BY_BANK_METHOD_ENABLE_on" 
                       value="1"
                       {if $REVOLUT_PAY_BY_BANK_METHOD_ENABLE|intval == 1}checked="checked"{/if} />
                <label for="REVOLUT_PAY_BY_BANK_METHOD_ENABLE_on">
                    {l s='Yes' mod='revolutpayment'}
                </label>

                <input type="radio" 
                       name="REVOLUT_PAY_BY_BANK_METHOD_ENABLE" 
                       id="REVOLUT_PAY_BY_BANK_METHOD_ENABLE_off"
                       value="0" 
                       {if $REVOLUT_PAY_BY_BANK_METHOD_ENABLE|intval == 0}checked="checked"{/if} />
                <label for="REVOLUT_PAY_BY_BANK_METHOD_ENABLE_off">
                    {l s='No' mod='revolutpayment'}
                </label>

                <a class="slide-button btn"></a>
            </span>
            <p class="help-block">
                {l s='Allows your customers to make instant payments using their bank of choice' mod='revolutpayment'}
            </p>
        </div>
    </div>

    <!-- Title -->
    <div class="form-group">
        <label class="control-label col-lg-3 required">
            {l s='Title' mod='revolutpayment'}
        </label>
        <div class="col-lg-9">
            <div class="form-group">
                {foreach from=$languages item=language}
                    <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}"
                         {if $language.id_lang != $id_current_lang}style="display: none;"{/if}>

                        <div class="col-lg-9">
                            <input type="text"
                                   name="REVOLUT_P_OPENBANKING_TITLE_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                                   id="REVOLUT_P_OPENBANKING_TITLE_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                                   value="{$REVOLUT_P_OPENBANKING_TITLE[$language.id_lang]|escape:'htmlall':'UTF-8'}"
                                   class=""
                                   size="20"
                                   required="required"/>
                        </div>

                        <div class="col-lg-2">
                            <button type="button" 
                                    class="btn btn-default dropdown-toggle" 
                                    tabindex="-1"
                                    data-toggle="dropdown">
                                {$language.iso_code|escape:'htmlall':'UTF-8'}
                                <i class="icon-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=lang}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" 
                                           tabindex="-1">
                                            {$lang.name|escape:'htmlall':'UTF-8'}
                                        </a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                {/foreach}
            </div>
            <p class="help-block">
                {l s='This controls the title which the user sees during checkout.' mod='revolutpayment'}
            </p>
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="panel-footer">
        <button type="submit" 
                value="1" 
                id="pay-by-bank-settings_submit_btn" 
                name="submitrevolutpayment" 
                class="btn btn-default pull-right">
            <i class="process-icon-save"></i> 
            {l s='Save' mod='revolutpayment'}
        </button>
    </div>
</div>
