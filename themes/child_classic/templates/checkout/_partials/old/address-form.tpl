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

    {block name='form_fields' append}
        <input type="hidden" name="saveAddress" value="{$type}">
        {if $type === "delivery"}
            <div class="form-group row">
                <input type="hidden" name="use_same_address" value="1" id="use_same_address">
                <div id="switchUseSameFormDiv" class="col-md-9 col-md-offset-3"  data-same="{$use_same_address}">
                    <div class="wasteSwitch">
                        <input class="toggleMin" type="checkbox"  id="useDifferentAddress" name="useDifferentAddress"  {if !$use_same_address} checked {/if}/>
                        <label class="switch" for="useDifferentAddress"></label>
                    </div>
                    <div class="checkUseSameForm" style="padding-left:10px">
                        <span>{l s='Billing address differs from shipping address' d='Shop.Theme.Checkout'}</span>
                    </div>
                </div>
            </div>
        {elseif $type === "invoice"}
           
            <input type="hidden" name="confirm-addresses" value="1">
            <input type="hidden" name="use_same_address" value="0"> 

        {/if}
    {/block}

    {block name='form_buttons'}
        {if !$form_has_continue_button}
            <button id="confirmAddressButton" data-location="form" data-customer="{$customer.id}" type="submit" class="btn btn-primary float-xs-right">{l s='Save' d='Shop.Theme.Actions'}</button>
            <div class="clearfix"></div>
            <a id="cancel-address-form" class="btn js-cancel-address cancel-address float-xs-right" href="{url entity='order' params=['cancelAddress' => {$type}]}">{l s='Cancel' d='Shop.Theme.Actions'}</a>
        {else}
            <form>
                <button id="confirmAddressButton" data-location="form" data-customer="{$customer.id}" type="submit" class="continue btn btn-primary float-xs-right" name="confirm-addresses" value="1">
                    {l s='Continue' d='Shop.Theme.Actions'}
                </button>
                {if $customer.addresses|count > 0}
                    <div class="clearfix"></div>
                    <a id="cancel-address-form" class="btn js-cancel-address cancel-address float-xs-right" href="{url entity='order' params=['cancelAddress' => {$type}]}">{l s='Cancel' d='Shop.Theme.Actions'}</a>
                {/if}
            </form>
        {/if}
    {/block}
