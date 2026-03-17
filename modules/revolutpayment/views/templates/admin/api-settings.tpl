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

<div class="panel tab-pane fade {if empty($section) || $section == 'api-settings'}active in{/if}" id="api-settings" role="tabpanel" aria-labelledby="api-settings-tab">
  <div>
    <div class="form-group">
      <label class="control-label col-lg-3">{l s='Mode' mod='revolutpayment'}</label>
      <div class="col-lg-9">
        <select name="REVOLUT_API_MODE" class="fixed-width-xl">
          <option value="live" {if $REVOLUT_API_MODE == 'live'}selected="selected"{/if}>Live</option>
          <option value="sandbox" {if $REVOLUT_API_MODE == 'sandbox'}selected="selected"{/if}>Test mode (sandbox)</option>
          {if $smarty.const.REVOLUT_DEV_MODE}
            <option value="dev" {if $REVOLUT_API_MODE == 'dev'}selected="selected"{/if}>Dev</option>
          {/if}
        </select>
        <p class="help-block">
          {l s='Place the payment gateway in Sandbox mode to test the integration. You can find information about how to do this' mod='revolutpayment'}
          <a target="_blank" href="https://developer.revolut.com/docs/merchant-api/#revolut-widget-sandbox-environment">
            {l s='here' mod='revolutpayment'}
          </a>
        </p>
      </div>
    </div>

    <div id="API_KEY_INPUT_SECTION">
      {* LIVE API KEY *}
      <div id="REVOLUT_SECRET_API_KEY_LIVE_content" {if $REVOLUT_API_MODE != 'live' || empty($REVOLUT_SECRET_API_KEY_LIVE)}style="display:none"{/if}>
        <div class="form-group">
          <label class="control-label col-lg-3">{l s='Production API Secret key' mod='revolutpayment'}</label>
          <div id="REVOLUT_API_KEY_FIELD_LIVE" class="col-lg-3">
            <input type="password" name="REVOLUT_SECRET_API_KEY_LIVE" id="REVOLUT_SECRET_API_KEY_LIVE_INPUT"
              value="{$REVOLUT_SECRET_API_KEY_LIVE|escape:'htmlall':'UTF-8'}" size="20" />
            <p class="help-block">{l s='API Key from your Merchant settings on Revolut.' mod='revolutpayment'}</p>
            {if empty($REVOLUT_SECRET_API_KEY_LIVE)}
              <a href="#" id="revolut-switch-to-oauth">{l s='Or connect your Revolut account' mod='revolutpayment'}</a>
            {/if}
          </div>
        </div>
      </div>

      <div id="REVOLUT_SECRET_API_KEY_SANDBOX_content" {if $REVOLUT_API_MODE != 'sandbox'}style="display:none"{/if}>
        <div class="form-group">
          <label class="control-label col-lg-3">{l s='API Key (sandbox mode)' mod='revolutpayment'}</label>
          <div class="col-lg-3">
            <input type="password" name="REVOLUT_SECRET_API_KEY_SANDBOX" id="REVOLUT_SECRET_API_KEY_SANDBOX_INPUT"
              value="{$REVOLUT_SECRET_API_KEY_SANDBOX|escape:'htmlall':'UTF-8'}" size="20" />
            <p class="help-block">{l s='Insert API Key from your merchant settings in Revolut Business, and click the save button' mod='revolutpayment'}</p>
          </div>
        </div>
      </div>

      {if $smarty.const.REVOLUT_DEV_MODE}
        <div id="REVOLUT_SECRET_API_KEY_DEV_content" {if $REVOLUT_API_MODE != 'dev' || empty($REVOLUT_SECRET_API_KEY_DEV)}style="display:none"{/if}>
          <div class="form-group">
            <label class="control-label col-lg-3">{l s='API secret key (DEV mode)' mod='revolutpayment'}</label>
            <div id="REVOLUT_API_KEY_FIELD_DEV" class="col-lg-3">
              <input type="password" name="REVOLUT_SECRET_API_KEY_DEV" id="REVOLUT_SECRET_API_KEY_DEV_INPUT"
                value="{$REVOLUT_SECRET_API_KEY_DEV|escape:'htmlall':'UTF-8'}" size="20" />
              <p class="help-block">{l s='API Key from your Merchant settings on Revolut.' mod='revolutpayment'}</p>
              {if empty($REVOLUT_SECRET_API_KEY_DEV)}
                <a href="#" id="revolut-switch-to-oauth">{l s='Or connect your Revolut account' mod='revolutpayment'}</a>
              {/if}
            </div>
          </div>
        </div>
      {/if}
    </div>

    {if empty($REVOLUT_SECRET_API_KEY_LIVE)}
      <div id="REVOLUT_OAUTH_CONNECTION_SECTION_LIVE" class="form-group" {if $REVOLUT_API_MODE != 'live' || !empty($REVOLUT_SECRET_API_KEY_LIVE)}style="display:none"{/if}>
        <label class="control-label col-lg-3">{l s='Connect to Revolut' mod='revolutpayment'}</label>
        <div class="col-lg-3" id="REVOLUT_OAUTH_CONNECT_BUTTON_LIVE"></div>
      </div>
    {/if}

    {if empty($REVOLUT_SECRET_API_KEY_DEV)}
      <div id="REVOLUT_OAUTH_CONNECTION_SECTION_DEV" class="form-group" {if $REVOLUT_API_MODE != 'dev' || !empty($REVOLUT_SECRET_API_KEY_DEV)}style="display:none"{/if}>
        <label class="control-label col-lg-3">{l s='Connect to Revolut (Dev)' mod='revolutpayment'}</label>
        <div class="col-lg-3" id="REVOLUT_OAUTH_CONNECT_BUTTON_DEV"></div>
      </div>
    {/if}

    <div class="form-group form-checkbox">
      <div class="col-lg-offset-3 col-lg-9">
        <input type="checkbox" value="1" name="REVOLUT_P_AUTHORIZE_ONLY" id="REVOLUT_P_AUTHORIZE_ONLY"
          {if $REVOLUT_P_AUTHORIZE_ONLY == 1}checked="checked"{/if} />
        <label for="REVOLUT_P_AUTHORIZE_ONLY">
          {l s='Enable "Authorize Only" mode. This allows the payment to be captured up to 7 days after the user has placed the order (e.g. when the goods are shipped or received). If unchecked, Revolut will try to authorize and capture all payments.' mod='revolutpayment'}
        </label>
      </div>
    </div>

    <div class="panel-footer">
      <button type="submit" value="1" id="api-settings-form_submit_btn" name="submitrevolutpayment"
        class="btn btn-default pull-right">
        <i class="process-icon-save"></i> {l s='Save' mod='revolutpayment'}
      </button>
    </div>
  </div>
</div>
