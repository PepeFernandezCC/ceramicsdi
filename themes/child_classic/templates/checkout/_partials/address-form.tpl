{extends file='customer/_partials/address-form.tpl'}

    {block name='form_field'}
        {if $field.name eq "alias" and $customer.is_guest}
            {* we don't ask for alias here if customer is not registered *}
        {else}
            {$smarty.block.parent}
        {/if}
    {/block}

    {block name="address_form_url"}
        <form
                method="POST"
                id="address-form"
                action="{url entity='order' params=['id_address' => $id_address]}"
                data-id-address="{$id_address}"
                data-refresh-url="{url entity='order' params=['ajax' => 1, 'action' => 'addressForm']}"
        >

    {/block}

    {block name='form_fields'}
        {* 1. hidden para saber qué tipo de dirección es (envío/factura) *}
        <input type="hidden" name="saveAddress" value="{$type}">
        {if ($type == "delivery")}
            <input type="hidden" name="is_invoice" value="0" id="is_invoice">
        {else}
            <input type="hidden" name="is_invoice" value="1" id="is_invoice">
        {/if}

        {* 2. radios particular / empresa (igual que en la plantilla base) *}
        <div class="form-group row" style="width: 100%;">
            <div class="col-md-12">
                <label class="radio-inline" for="field-particular"> 
                <span class="custom-radio">
                    <input
                            name="treatment"
                            id="field-particular"
                            type="radio"
                            value="particular"
                            required
                            checked
                    >
                    <span></span>
                </span>
                    {l s='individual' d='Shop.Theme.Checkout'}
                </label>
                <label class="radio-inline" for="field-empresa">
                <span class="custom-radio">
                    <input
                            name="treatment"
                            id="field-empresa"
                            type="radio"
                            value="empresa"
                            required
                    >
                    <span></span>
                </span>
                    {l s='company' d='Shop.Theme.Checkout'}
                </label>
            </div>
        </div>

        {* 3. SOLO PARA NUEVAS DIRECCIONES DE ENVÍO: switch debajo de los radios *}
        {if $type === "delivery"}
            {assign var="newAddress" value="0"}
            {if !$id_address }
                {assign var="newAddress" value="1"}
            {/if}

            <div id="newAddress" data-new="{$newAddress}" style="display:none"></div>

            <div class="form-group row" style="width:100%">
                <input type="hidden" name="use_same_address" value="1" id="use_same_address">
                <div id="switchUseSameFormDiv" class="col-md-12 d-flex" data-same="{$use_same_address}">
                    <div class="wasteSwitch">
                        <input class="toggleMin" type="checkbox" id="useDifferentAddress" name="useDifferentAddress" {if !$use_same_address} checked {/if}/>
                        <label class="switch" for="useDifferentAddress"></label>
                    </div>
                    <div class="checkUseSameForm" style="padding-left:10px">
                        <span>{l s='Billing address differs from shipping address' d='Shop.Theme.Checkout'}</span>
                    </div>
                </div>
            </div>
        {/if}

        {* 4. PARA FORMULARIO DE FACTURACIÓN: marcar que no usa misma dirección *}
        {if $type === "invoice"}
            <input type="hidden" name="confirm-addresses" value="1">
            <input type="hidden" name="use_same_address" value="0"> 
        {/if}

        {* 6. resto de campos del formulario *}
        
        {foreach from=$formFields item="field"}
                {block name='form_field'}
                    {form_field field=$field}
                {/block}
        {/foreach}
        
    {/block}

    {block name='form_buttons'}
        {if !$id_address}
            {if !$form_has_continue_button}
                <button id="confirmAddressButton" data-location="form" data-customer="{$customer.id}" type="submit" class="btn btn-primary float-xs-right">
                    {l s='Save' d='Shop.Theme.Actions'}
                </button>
                <div class="clearfix" data-continuebuttonfalse="{$form_has_continue_button}"></div>
                <a id="cancel-address-form" class="btn js-cancel-address cancel-address float-xs-right" href="{url entity='order' params=['cancelAddress' => {$type}]}">{l s='Cancel' d='Shop.Theme.Actions'}</a>
            {else}
                <form>
                    <button id="confirmAddressButton" data-location="form" data-customer="{$customer.id}" type="submit" class="continue btn btn-primary float-xs-right" name="confirm-addresses" value="1">
                        {l s='Continue' d='Shop.Theme.Actions'}
                    </button>
                    {if $customer.addresses|count > 0}
                        <div class="clearfix"data-continuebuttontrue="{$form_has_continue_button}"></div>
                        <a id="cancel-address-form" class="btn js-cancel-address cancel-address float-xs-right" href="{url entity='order' params=['cancelAddress' => {$type}]}">{l s='Cancel' d='Shop.Theme.Actions'}</a>
                    {/if}
                </form>
            {/if}
        {else}
            {if $type == "invoice"}
                <button id="confirmAddressButton" data-location="form" data-customer="{$customer.id}" type="submit" class="continue btn btn-primary float-xs-right" name="confirm-addresses" value="1">
                    {l s='Continue' d='Shop.Theme.Actions'}
                </button>
            {/if}
            <div class="clearfix" data-continuebuttontrue="{$form_has_continue_button}"></div>
            <a id="cancel-address-form" class="btn js-cancel-address cancel-address float-xs-right" href="{url entity='order' params=['cancelAddress' => {$type}]}">{l s='Cancel' d='Shop.Theme.Actions'}</a>
        {/if}
    {/block}
