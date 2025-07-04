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
<div class="module_4webs">
    <div class="row">
        <div class="col-md-12">
            <h2>{l s='Show widget "Pay Later"' mod='paypalwithfee'}</h2>
        </div>
    </div>
    <div class="row">
        <div class="fw_PPAL_PAY_LATER module-row-attribute form-group">
            <div>
                <span class="btn-switch">
                        <input type="radio" name="PPAL_PAY_LATER" id="PPAL_PAY_LATER_on" class="btn-switch__radio btn-switch__radio_yes" value="1" {if $pp_pay_later == 1}checked="checked"{/if}>
                        <input type="radio" name="PPAL_PAY_LATER" id="PPAL_PAY_LATER_off" class="btn-switch__radio btn-switch__radio_no" value="0" {if $pp_pay_later == 0}checked="checked"{/if}>
                        <label for="PPAL_PAY_LATER_on" class="btn-switch__label btn-switch__label_yes"><span class="btn-switch__txt">{l s='Yes' mod='paypalwithfee'}</span></label>
                        <label for="PPAL_PAY_LATER_off" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">{l s='No' mod='paypalwithfee'}</span></label>
                </span>
            </div>
        </div>
    </div>
</div>