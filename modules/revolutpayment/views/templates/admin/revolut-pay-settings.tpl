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

<div class="panel tab-pane fade{if $section == 'revolut-pay-settings'} active in{/if}"
     id="revolut-pay-settings"
     role="tabpanel"
     aria-labelledby="revolut-pay-settings-tab">

            <!-- Enable Revolut Pay Switch -->
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Enable Revolut Pay?' mod='revolutpayment'}
                </label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio"
                               name="REVOLUT_PAY_METHOD_ENABLE"
                               id="REVOLUT_PAY_METHOD_ENABLE_on"
                               value="1"
                               {if $REVOLUT_PAY_METHOD_ENABLE == 1}checked="checked"{/if} />
                        <label for="REVOLUT_PAY_METHOD_ENABLE_on">
                            {l s='Yes' mod='revolutpayment'}
                        </label>

                        <input type="radio"
                               name="REVOLUT_PAY_METHOD_ENABLE"
                               id="REVOLUT_PAY_METHOD_ENABLE_off"
                               value="0"
                               {if $REVOLUT_PAY_METHOD_ENABLE == 0}checked="checked"{/if} />
                        <label for="REVOLUT_PAY_METHOD_ENABLE_off">
                            {l s='No' mod='revolutpayment'}
                        </label>

                        <a class="slide-button btn"></a>
                    </span>
                    <p class="help-block">
                        {l s='This controls whether or not "Revolut Pay Button" is enabled within Prestashop.' mod='revolutpayment'}
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="panel-footer">
                <button type="submit"
                        value="1"
                        id="revolut-pay-settings-form_submit_btn"
                        name="submitrevolutpayment"
                        class="btn btn-default pull-right">
                    <i class="process-icon-save"></i>
                    {l s='Save' mod='revolutpayment'}
                </button>
            </div>
</div>
