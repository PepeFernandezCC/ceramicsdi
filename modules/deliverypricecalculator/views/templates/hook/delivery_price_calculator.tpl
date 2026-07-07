{**
 * Delivery Price Calculator hook template
 *}
<div id="deliveryPriceCalculator" class="form-group row scc-main">
    <div style="margin-bottom: 10px">
        <span class="scc-title">{l s='calculate your shipping costs' d='Shop.Theme.Checkout' mod='deliverypricecalculator'}</span>
    </div>

    <div class="deliveryPriceCalculatorFormBox">
        {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
            <input type="hidden" id="showTaxes" value="0">
        {else}
            <input type="hidden" id="showTaxes" value="1">
        {/if}
        <input type="hidden" id="language" value="{$language.id}">
        <input type="hidden" id="packageWeight" name="packageWeight" value="{$package_weight}" />
        <input type="hidden" id="cartId" name="cartId" value="{$cart_id}" />

        <div id="country-selector-box">
            <select id="field-id_country" class="form-control form-control-select scc-select" name="id_country">
                <option value="">{l s='Select a country' d='Shop.Theme.Checkout' mod='deliverypricecalculator'}</option>
                {foreach from=$countryList item=$country}
                    {if in_array($country.id_country, $VALID_COUNTRIES)}
                        <option value="{$country.id_country}">{$country.name}</option>
                    {/if}
                {/foreach}
            </select>
        </div>

        <div id="province-selector-box">
            <select id="field-id_state" class="form-control form-control-select scc-select" name="id_state">
                <option value="">{l s='Select a state' d='Shop.Theme.Checkout' mod='deliverypricecalculator'}</option>
            </select>
        </div>

        <div>
            <input type="text" name="postalzip" id="postalzip" inputmode="numeric" value=""
            class="input-group scc-postalcode-input" aria-label="Total" data-price="0" placeholder="{l s='Postal code' d='Shop.Forms.Labels' mod='deliverypricecalculator'}">
        </div>

    </div>
    <div class="deliveryPriceCalculatorResult d-flex scc-result" style="position:relative">

        <button id="calculateMyDeliveryButton" class="scc-button">{l s='Get shipping costs' d='Shop.Theme.Checkout' mod='deliverypricecalculator'}</button>

        <input type="number" name="euros" id="euros-input" inputmode="numeric" step="0.01" min="0.00" value="0.00"
        class="input-group boxInput cc-background-color-secondary" aria-label="Total" readonly="readonly" data-price="0"
        style="font-weight: bold; font-size: 17px; max-height:40px">
        <div class="scc-coin">€</div>
    </div>

    <div id="messageContainer" class="alert alert-danger" style="display: none">{l s='Please, complete all the form fields' d='Shop.Theme.Checkout' mod='deliverypricecalculator'}</div>
</div>
