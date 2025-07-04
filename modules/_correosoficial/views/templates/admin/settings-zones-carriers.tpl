<div class="accordion-body accordion-body">
    <form action="index.php?controller=AdminCorreosOficialZonesCarriers" id="CorreosZonesCarriersForm" name="CorreosZonesCarriersForm" method="POST">
        <div class="row">
            <div class="col-sm-12 ZonesAndCarriers">
                <div class="alert alert-secondary d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    <div>
                        {l s='Carriers with ** are carriers that are not active' mod='correosoficial'}.</br>
                        {l s='It is recommended to configure them for backward compatibility' mod='correosoficial'}.
                    </div>
                </div>
        
                <div class="input-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showAllCarriersCheck">
                        <label class="form-check-label form-check-label-color" for="showAllCarriersCheck">
                            {l s='Activate all carriers' mod='correosoficial'}
                        </label>
                    </div>
                    <div class="col-sm-12 ProductsAndCarriersList">
                        {foreach from=$zonesandcarriers item=zone}
                        <div>
                            {if !empty($zone['carriers'])}
                                <div class="zone-name">{$zone['zonename']}</div>
                                    {foreach from=$zone['carriers'] item=carrier}
                                        {if ($carrier['active'] == 1)}
                                            <div class="input-group mb-3">
                                        {else}
                                            <div class="input-group mb-3 hidden-product-option">
                                        {/if}
                                        <div class="input-group-addon input-group-text-custom">
                                            <span class="input-group-text input-group-text-color">
                                                {if ($carrier['active'] == 0)}**{/if}
                                                {$carrier['name']}
                                            </span>
                                        </div>
                                        <select class="co_dropdown scp_products" id="scp_{$zone['id_zone']}_{$carrier['id_carrier']}" name="scp_{$zone['id_zone']}_{$carrier['id_carrier']}">
                                            <option value=""></option>
                                            {if !empty($zone['products'])}
                                                {foreach from=$zone['products'] item=$product} 
                                                    <option value="{$product->id}" {if $product->product_type == "office" || $product->product_type == "citypaq"} disabled{/if} {if $product->id == $carrier['product_selected']} selected{/if}>
                                                        {$product->name}
                                                    </option>
                                                {/foreach}
                                            {/if}
                                        </select>
                                    </div>
                                    {/foreach}
                                {/if}
                            </div>
                    {/foreach}
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <input class="co_primary_button" name="ZonesCarriersSaveButton" id="ZonesCarriersSaveButton"
                        type="submit" value="{l s='SAVE ZONES AND CARRIERS' mod='correosoficial'}">
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    var zonesCarriersSaved = "{l s='Zones and carriers successfully saved' mod='correosoficial'}";
</script>
