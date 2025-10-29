{foreach ['FR', 'IT', 'ES', 'PT'] as $country_iso}
    <label class="col-sm-2 control-label" for="additional_shipping">
        {l s='Carrier rules' mod='sdevadeo'} {$country_iso}:
    </label>

    <div class="col-sm-10" id="connection-panel">
        <table class="carrier-rule-table table table-hover{if $apiShippingUpdateDate == null} hidden{/if}">
            <thead>
                <tr>
                    <th>{l s='Marketplace\'s shipment mode' mod='sdevadeo'}</th>
                    <th>{l s='Internal\'s carrier' mod='sdevadeo'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $marketplace_shipping as $shipping_method}
                    <tr data-code-method="{$shipping_method['code']}" data-country-iso="{$country_iso}">
                        <td>
                            <p class="form-control">{$shipping_method['label']}</p>
                        </td>
                        <td>
                            <select id="cms_carrier" class="form-control">
                                <option selected>
                                    -
                                </option>
                                {foreach $internal_carriers as $cms_carrier}
                                    <option value="{$cms_carrier['id_reference']}"{if array_key_exists($country_iso, $carrier_rules) && array_key_exists($shipping_method['code'], $carrier_rules[$country_iso]) && $carrier_rules[$country_iso][$shipping_method['code']] == $cms_carrier['id_reference']} selected{/if}>
                                        {$cms_carrier['name']}
                                    </option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
{/foreach}
    <button
                type="submit"
                class="btn btn-primary pull-right"
                title="{l s='Save the general parameters.' mod='sdevadeo'}"
                onclick="SDEVADEO.controller.admin.parameters.saveCarrierRule()"
        >
            <i class="icon-save"></i>&nbsp;
            {l s='Save' mod='sdevadeo'}
        </button>