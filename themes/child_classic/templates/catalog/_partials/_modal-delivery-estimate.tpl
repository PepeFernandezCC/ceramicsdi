<!-- The Modal -->

<div class="modal fade p-0" id="deliveryEstimateModal" tabindex="-1" role="dialog" aria-labelledby="deliveryEstimateModalLabel" aria-hidden="true" style="padding-right:0px !important">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">

        <div class="modal-text">
            <div class="modal-text-title">
                <h2>{l s='Delivery Time Calculator' d='Shop.Theme.Catalog'}</h2>
            </div>
            <div class="modal-text-content">
                <p>{l s='Enter your shipping details to calculate the estimated preparation and delivery time for this product.' d='Shop.Theme.Catalog'}</p>

                <input type="hidden" id="deliveryEstimateIdProduct" value="{$product.id}">
                <input type="hidden" id="deliveryEstimateLanguage" value="{$language.id}">
                <input type="hidden" id="deliveryEstimateBoxes" value="2">

                <div class="scc-modal-form">

                    <div class="form-group">
                        <label for="deliveryEstimateCountry">{l s='Country' d='Shop.Theme.Catalog'}</label>
                        <select id="deliveryEstimateCountry" class="form-control">
                            <option value="">{l s='Select a country' d='Shop.Theme.Catalog'}</option>
                            {assign var="deliveryEstimateCountryList" value=Country::getCountries($language.id)}
                            {foreach from=$deliveryEstimateCountryList item=$deCountry}
                                {if in_array($deCountry.id_country, $VALID_COUNTRIES)}
                                    <option value="{$deCountry.id_country}">{$deCountry.name}</option>
                                {/if}
                            {/foreach}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="deliveryEstimateState">{l s='State / Province' d='Shop.Theme.Catalog'}</label>
                        <select id="deliveryEstimateState" class="form-control">
                            <option value="">{l s='Select a state' d='Shop.Theme.Catalog'}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="deliveryEstimatePostal">{l s='Postal code' d='Shop.Theme.Catalog'}</label>
                        <input type="text" inputmode="numeric" class="form-control" id="deliveryEstimatePostal">
                    </div>

                    <div id="deliveryEstimateMessage" class="alert alert-danger" style="display:none">
                        {l s='Please complete all the fields' d='Shop.Theme.Catalog'}
                    </div>

                    <button type="button" id="deliveryEstimateSubmit" class="scc-button" style="padding:10px; width:100%">
                        {l s='Calculate' d='Shop.Theme.Catalog'}
                    </button>

                </div>

                <div id="deliveryEstimateResults" style="text-align:left"></div>
                <div class="additionalDeliveryInfo">
                    <p> <i class="fa-solid fa-angle-right" style="color:#eac133; padding-right:5px"></i>{l s='Delivery times are approximate and ultimately depend on the carrier and the local delivery office.' d='Shop.Theme.Catalog'}</p>
                    <p> <i class="fa-solid fa-angle-right" style="color:#eac133; padding-right:5px"></i>{l s='For sample orders, the delivery time is 3 to 7 business days.' d='Shop.Theme.Catalog'}</p>
                </div>
            </div>
        </div>

    </div>
    </div>
</div>
</div>

<!-- End Modal -->
