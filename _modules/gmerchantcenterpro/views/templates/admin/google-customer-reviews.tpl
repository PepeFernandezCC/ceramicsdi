{*
*
* Google merchant center Pro
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}

<div class="bootstrap">
    <form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_google_customer_reviews" name="bt_google_customer_reviews" {if $useJs == true}onsubmit="javascript: oGmcPro.form('bt_google_customer_reviews', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_google-customer-reviews-settings', 'bt_google-customer-reviews-settings', false, false, '', 'GoogleCustomerReviews', 'loadingGoogleCustomerReivewsDiv');return false;" {/if}>
        <input type="hidden" name="sAction" value="{$aQueryParams.googleCustomerReviews.action|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="sType" value="{$aQueryParams.googleCustomerReviews.type|escape:'htmlall':'UTF-8'}" />

        <h3 class="subtitle"><i class="icon-star"></i>&nbsp;{l s='Google Customer Reviews' mod='gmerchantcenterpro'}</h3>
        <div class="clr_10"></div>
        {if !empty($bUpdate)}
            {include file="`$sConfirmInclude`"}
        {elseif !empty($aErrors)}
            {include file="`$sErrorInclude`"}
        {/if}

        <div class="alert alert-info">
            {l s='Google Customer Reviews is a free service that allows you to collect valuable feedback from customers who have made a purchase on your site. After each purchase, your customers are invited to agree to receive an e-mail asking for their feedback on their shopping experience. When Google has collected enough reviews, you can display your average rating in a "Google Customer Reviews" reassurance badge on your site. Don\'t hesitate to read' mod='gmerchantcenterpro'}&nbsp;&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/541" target="_blank"><i
                    class="icon icon-link"></i>&nbsp;{l s='our FAQ' mod='gmerchantcenterpro'}</a>&nbsp;&nbsp;{l s='for more information.' mod='gmerchantcenterpro'}
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3">
                <span>
                    <b>{l s='Enable Google Customer Reviews feature' mod='gmerchantcenterpro'}</b>
                </span>
            </label>
            <div class="col-xs-5 col-md-5 col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="activate_gcr" id="activate_gcr_on" value="1" {if !empty($activateGcr)}checked="checked" {/if} />
                    <label for="activate_gcr_on" class="radioCheck">
                        {l s='Yes' mod='gmerchantcenterpro'}
                    </label>
                    <input type="radio" name="activate_gcr" id="activate_gcr_off" value="0" {if empty($activateGcr)}checked="checked" {/if} />
                    <label for="activate_gcr_off" class="radioCheck">
                        {l s='No' mod='gmerchantcenterpro'}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>

        <div id="gcr_option" style="display: {if !empty($activateGcr)} block{else} none{/if}">
            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" title="{l s='Enter your Google Merchant Center ID' mod='gmerchantcenterpro'}"><b>{l s='Your Google Merchant Center ID:' mod='gmerchantcenterpro'}</b></span>
                </label>
                <div class="col-xs-12 col-md-3 col-lg-2">
                    <input class="form-control" type="number" placeholder="123456789" name="bt_merchant-center-id" value="{$merchantCenterId|escape:'htmlall':'UTF-8'}" />
                </div>
                <span class="icon-question-sign label-tooltip" title="{l s='Enter your Google Merchant Center ID' mod='gmerchantcenterpro'}">&nbsp;</span>
                <a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/544" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about Google Merchant Center ID' mod='gmerchantcenterpro'}</a>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" title="{l s='A Google Customer Reviews badge will be displayed on your website homepage along with your average seller rating (if available, see our FAQ).' mod='gmerchantcenterpro'}"><b>{l s='Display the Google Customer Reviews badge' mod='gmerchantcenterpro'}</b></span>
                </label>
                <div class="col-xs-12 col-md-9 col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="activate_badge" id="activate_badge_on" value="1" {if !empty($activateBadge)}checked="checked" {/if} />
                        <label for="activate_badge_on" class="radioCheck">

                            {l s='Yes' mod='gmerchantcenterpro'}
                        </label>
                        <input type="radio" name="activate_badge" id="activate_badge_off" value="0" {if empty($activateBadge)}checked="checked" {/if} />
                        <label for="activate_badge_off" class="radioCheck">

                            {l s='No' mod='gmerchantcenterpro'}
                        </label>
                        <a class="slide-button btn"></a>
                    </span>
                    <span class="icon-question-sign label-tooltip" title="{l s='A Google Customer Reviews badge will be displayed on your website homepage along with your average seller rating (if available, see our FAQ).' mod='gmerchantcenterpro'}">&nbsp;</span>
                    <a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/545" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about the Google Customer Reviews badge' mod='gmerchantcenterpro'}</a>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" title="{l s='If you also want Google to collect product ratings, then you need to activate the sending of ordered product GTIN codes. Please note: the codes you send must be real GTIN codes, recognized by Google. See our FAQ for more information.' mod='gmerchantcenterpro'}"><b>{l s='Send GTIN codes of ordered products?' mod='gmerchantcenterpro'}</b>
                    </span>
                </label>
                <div class="col-xs-5 col-md-5 col-lg-6">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="use_product_gtin" id="use_product_gtin_on" value="1" {if !empty($useProductGtin)}checked="checked" {/if} />
                        <label for="use_product_gtin_on" class="radioCheck">

                            {l s='Yes' mod='gmerchantcenterpro'}
                        </label>
                        <input type="radio" name="use_product_gtin" id="use_product_gtin_off" value="0" {if empty($useProductGtin)}checked="checked" {/if} />
                        <label for="use_product_gtin_off" class="radioCheck">

                            {l s='No' mod='gmerchantcenterpro'}
                        </label>
                        <a class="slide-button btn"></a>
                    </span>
                    <span class="icon-question-sign label-tooltip" title="{l s='If you also want Google to collect product ratings, then you need to activate the sending of ordered product GTIN codes. Please note: the codes you send must be real GTIN codes, recognized by Google. See our FAQ for more information.' mod='gmerchantcenterpro'}">&nbsp;</span>
                    <a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/546" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about Google\'s product rating collection' mod='gmerchantcenterpro'}</a>
                </div>
            </div>

            <div class="clr_30"></div>

            <h3 class="subtitle"><i class="icon-shopping-cart"></i>&nbsp;{l s='Order status condition' mod='gmerchantcenterpro'}</h3>

            <div class="alert alert-info">
                {l s='Select the order status(es) for which your customers will see the Google survey opt-in request on the order confirmation page. We recommend that you select only those statuses considered valid, to avoid sending a review request for an order cancelled due to a payment error, for example.' mod='gmerchantcenterpro'}
            </div>

            {if empty($haveToSelectOrderState)}
                <div class="alert alert-danger">
                    {l s='You must select at least one order status' mod='gmerchantcenterpro'}
                </div>
            {/if}

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span>
                        <b>
                            {l s='Valid order status(es)' mod='gmerchantcenterpro'}</b>
                    </span>
                </label>
                <div class="col-xs-12 col-md-3 col-lg-2">
                    {foreach key=key item=status from=$orderStatuses}
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" {if in_array($status.id_order_state, $orderStatusSaved)} checked="checked" {/if} class="custom-control-input" id="statusCheck_{$status.id_order_state|escape:'htmlall':'UTF-8'}" name="bt_ok_order_states[]" value="{$status.id_order_state|escape:'htmlall':'UTF-8'}" />
                            <label class="custom-control-label" for="statusCheck_{$status.id_order_state|escape:'htmlall':'UTF-8'}">{$status.name|escape:'htmlall':'UTF-8'}</label>
                        </div>
                    {/foreach}
                </div>
            </div>

            <div class="clr_30"></div>

            <h3 class="subtitle mt-1"><i class="icon-truck"></i>&nbsp;{l s='Calculation of e-mail delivery time' mod='gmerchantcenterpro'}</h3>


            <div class="alert alert-info">
                {l s='The following fields will enable Google to estimate the time between your customer placing an order in your store and receiving it. This will enable it to calculate the date on which to send its review e-mail.' mod='gmerchantcenterpro'}
            </div>


            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" title="{l s='Select "Yes" if you ship orders the same day, provided they are placed before the time indicated below.' mod='gmerchantcenterpro'}"><b>
                            {l s='Shipment on day of order?' mod='gmerchantcenterpro'}
                        </b>
                    </span>
                </label>
                <div class="col-xs-5 col-md-5 col-lg-6">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="same_day_process" id="same_day_process_on" value="1" {if !empty($sameDayProcess)}checked="checked" {/if} />
                        <label for="same_day_process_on" class="radioCheck">

                            {l s='Yes' mod='gmerchantcenterpro'}
                        </label>
                        <input type="radio" name="same_day_process" id="same_day_process_off" value="0" {if empty($sameDayProcess)}checked="checked" {/if} />
                        <label for="same_day_process_off" class="radioCheck">

                            {l s='No' mod='gmerchantcenterpro'}
                        </label>
                        <a class="slide-button btn"></a>
                    </span>
                    <span class="icon-question-sign label-tooltip" title="{l s='Select "Yes" if you ship orders the same day, provided they are placed before the time indicated below.' mod='gmerchantcenterpro'}"></span>
                </div>
            </div>

            <div id="same_day_hours" style="display: {if !empty($sameDayProcess)} block{else} none{/if}">
                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <span class="label-tooltip" title="{l s='Specify the time by which a customer must place an order in order for it to be dispatched the same day.' mod='gmerchantcenterpro'}"><b>
                                {l s='Cut-off time for same-day shipment:' mod='gmerchantcenterpro'}</b>
                        </span>
                    </label>
                    <div class="col-xs-12 col-md-3 col-lg-6">
                        <input class="form-control col-xs-12 col-md-2" type="number" min="0" max="23" placeholder="18" name="cut_off_day_hour" value="{$cutOffHour|escape:'htmlall':'UTF-8'}" />
                        <input class="form-control col-xs-12 col-md-2" type="number" min="0" max="59" placeholder="45" name="cut_off_day_minute" value="{$cutOffMin|escape:'htmlall':'UTF-8'}" />
                        &nbsp;&nbsp;<span class="icon-question-sign label-tooltip" title="{l s='Specify the time by which a customer must place an order in order for it to be dispatched the same day.' mod='gmerchantcenterpro'}"></span>
                    </div>
                </div>
            </div>

            <div id="not_same_day_hours" style="display: {if empty($sameDayProcess)} block{else} none{/if}">
                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <span class="label-tooltip" title="{l s='If you don\'t ship orders the same day, indicate how many days it usually takes to ship an order after it has been placed on your site.  It is not advisable to enter a value greater than 3 days.' mod='gmerchantcenterpro'}"><b>
                                {l s='Average number of days before order shipment:' mod='gmerchantcenterpro'}</b>
                        </span>
                    </label>
                    <div class="col-xs-12 col-md-3 col-lg-2">
                        <input class="form-control" type="number" placeholder="2" name="bt_estimated_process" value="{$estimatedProcess|escape:'htmlall':'UTF-8'}" />
                    </div>
                    <span class="icon-question-sign label-tooltip" title="{l s='If you don\'t ship orders the same day, indicate how many days it usually takes to ship an order after it has been placed on your site.  It is not advisable to enter a value greater than 3 days.' mod='gmerchantcenterpro'}"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" title="{l s='Select the days of the week on which orders are NOT dispatched (for example, if your company is closed every Sunday, check "Sunday" opposite).' mod='gmerchantcenterpro'}"><b>
                            {l s='Orders NOT dispatched on the following days of the week:' mod='gmerchantcenterpro'}</b>
                    </span>
                </label>
                <div class="col-xs-12 col-md-3 col-lg-2">
                    {foreach key=key item=day from=$weekDays}
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" {if in_array($key, $closedDay)} checked="checked" {/if} class="custom-control-input" id="customCheck_{$key|escape:'htmlall':'UTF-8'}" name="closed_days[]" value="{$key|escape:'htmlall':'UTF-8'}" />
                            <label class="custom-control-label" for="customCheck_{$key|escape:'htmlall':'UTF-8'}">{$day|escape:'htmlall':'UTF-8'}</label>
                        </div>
                    {/foreach}
                </div>
                <span class="icon-question-sign label-tooltip" title="{l s='Select the days of the week on which orders are NOT dispatched (for example, if your company is closed every Sunday, check "Sunday" opposite).' mod='gmerchantcenterpro'}"></span>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" title="{l s='Indicate any additional days of the year on which orders are not dispatched, such as public holidays or your company\'s annual leave. DO NOT check the days of the week you have just checked above, as they are already taken into account through the previous setting.' mod='gmerchantcenterpro'}"><b>
                            {l s='Orders also NOT dispatched on the following days of the year:' mod='gmerchantcenterpro'}</b>
                    </span>
                </label>
                <div class="col-xs-12 col-md-3 col-lg-2">
                    <table class="table">
                        <tr>
                            {foreach key=key item=day from=$holidays}
                                <th>
                                    {$day.name|escape:'htmlall':'UTF-8'}
                                </th>
                            {/foreach}
                        </tr>
                        {foreach key=key item=day from=$holidays}
                            <td class="text-center">
                                {for $foo=1 to $day.nbDays}
                                    {assign var="savedData" value="{$day.month_number}_{$foo}"}
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" {if in_array($savedData, $closeHoliday)} checked="checked" {/if} class="custom-control-input" name="holidays[]" id="customCheck_{$day.name|escape:'htmlall':'UTF-8'}_{$foo|escape:'htmlall':'UTF-8'}" value="{$day.month_number|escape:'htmlall':'UTF-8'}_{$foo|escape:'htmlall':'UTF-8'}" />
                                        <label class="custom-control-label" for="customCheck_{$day.name|escape:'htmlall':'UTF-8'}_{$foo|escape:'htmlall':'UTF-8'}">{$foo|escape:'htmlall':'UTF-8'}</label>
                                    </div>
                                {/for}
                            </td>
                        {/foreach}
                    </table>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" title="{l s='For each carrier, indicate the usual transport time, i.e. the average time (in days) between dispatch of the order and its arrival with the customer.' mod='gmerchantcenterpro'}"><b>
                            {l s='Average transport time:' mod='gmerchantcenterpro'}
                        </b>
                    </span>
                </label>
                <div class="col-xs-12 col-md-3 col-lg-6">
                    <table class="table">
                        <tr>
                            <td class="text-center"><b>{l s='Carrier' mod='gmerchantcenterpro'}</b></td>
                            <td class="text-center"><b>{l s='Average transport time (in days)' mod='gmerchantcenterpro'}</b></td>
                        </tr>
                        {foreach key=key item=carrier from=$carriers}
                            <tr>
                                <td>{$carrier.name|escape:'htmlall':'UTF-8'}</td>
                                <td><input class="form-control" type="text" name="ship_time[{$carrier.id_reference|escape:'htmlall':'UTF-8'}]" value="{if !empty($shipTime[$carrier.id_reference|escape:'htmlall':'UTF-8'])}{$shipTime[$carrier.id_reference|escape:'htmlall':'UTF-8']}{/if}" /></td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>

        </div>
        <div class="navbar navbar-default navbar-fixed-bottom text-center">
            <div class="col-xs-12">
                <button class="btn btn-submit" onclick="oGmcPro.form('bt_google_customer_reviews', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_google-customer-reviews-settings', 'bt_google-customer-reviews-settings', false, false, '', 'GoogleCustomerReviews', 'loadingGoogleCustomerReivewsDiv', false, 1);return false;">
                    {l s='Save' mod='gmerchantcenterpro'}</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="{$moduleJsPath|escape:'htmlall':'UTF-8'}form.js"></script>