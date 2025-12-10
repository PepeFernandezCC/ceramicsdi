
{extends file='checkout/_partials/steps/checkout-step.tpl'}
{* Filtrar direcciones según is_invoice *}
{assign var='delivery_addresses' value=[]}
{assign var='invoice_addresses' value=[]}
{foreach from=$customer.addresses item=address}

  {if $address.is_invoice == 0 || $address.is_invoice == 2}
    {append var='delivery_addresses' value=$address}
  {/if}

  {if $address.is_invoice == 1 || $address.is_invoice == 2}
    {append var='invoice_addresses' value=$address}
  {/if}
        
{/foreach}

{block name='step_content'}
  <div class="js-address-form">
    <!-- Loader -->
    <div id="loader-overlay" style="display:none;">
        <div class="loader"></div>
    </div>

    <form
      id="address-form"
      method="POST"
      action="{url entity='order' params=['id_address' => $id_address]}"
      data-refresh-url="{url entity='order' params=['ajax' => 1, 'action' => 'addressForm']}"
    >

      <div id="warning-incomplete-address" style="display:none">
         <p class="alert alert-danger js-address-error" name="alert-delivery">{l s="Your address is incomplete, please update it." d="Shop.Notifications.Error"}</p>
      </div>

      <div id="delivery-address-panel" data-same="{$use_same_address}">
        <div class="js-address-form">

          {assign var="copy_same_address" value=$use_same_address}
            
          <h2 class="h4">{l s='Shipping Address' d='Shop.Theme.Checkout'}</h2>

          {if $show_delivery_address_form}
          
            <div id="delivery-address">
              {render file                = 'checkout/_partials/address-form.tpl'
                ui                        = $address_form
                use_same_address          = $use_same_address
                type                      = "delivery"
                form_has_continue_button  = $form_has_continue_button
              }
            </div>

          {elseif $customer.addresses|count > 0}

            <div id="delivery-addresses" class="address-selector js-address-selector">

              {include  file        = 'checkout/_partials/address-selector-block.tpl'
                addresses   = $delivery_addresses
                name        = "id_address_delivery"
                selected    = $id_address_delivery
                type        = "delivery"
                interactive = !$show_delivery_address_form and !$show_invoice_address_form
              }

            </div>

            {if isset($delivery_address_error)}
              <p class="alert alert-danger js-address-error" name="alert-delivery" id="id-failure-address-{$delivery_address_error.id_address}">{$delivery_address_error.exception}</p>
            {else}
              <p class="alert alert-danger js-address-error" name="alert-delivery" style="display: none">{l s="Your address is incomplete, please update it." d="Shop.Notifications.Error"}</p>
            {/if}

            <p class="add-address">
              <a href="{$new_address_delivery_url}"><i class="material-icons">&#xE145;</i>{l s='New shipping address' d='Shop.Theme.Actions'}</a>
            </p>

          {/if}

          {if $customer.addresses|count > 0}
            <div id="invoice-addresses-panel" class="mt-65">

              <h2 class="h4">{l s='Your Invoice Address' d='Shop.Theme.Checkout'}</h2>

              {if $show_invoice_address_form}

                <div id="invoice-address">
                  {render file                = 'checkout/_partials/address-form.tpl'
                    ui                        = $address_form
                    use_same_address          = $use_same_address
                    type                      = "invoice"
                    form_has_continue_button  = $form_has_continue_button
                  }
                </div>

              {else}
                <div id="invoice-addresses" class="address-selector js-address-selector">

                  {include  file        = 'checkout/_partials/address-selector-block.tpl'
                    addresses   = $invoice_addresses
                    name        = "id_address_invoice"
                    selected    = $id_address_invoice
                    type        = "invoice"
                    interactive = !$show_delivery_address_form and !$show_invoice_address_form
                  }

                </div>

                {if isset($invoice_address_error)}
                  <p class="alert alert-danger js-address-error" name="alert-invoice" id="id-failure-address-{$invoice_address_error.id_address}">{$invoice_address_error.exception}</p>
                {else}
                  <p class="alert alert-danger js-address-error" name="alert-invoice" style="display: none">{l s="Your address is incomplete, please update it." d="Shop.Notifications.Error"}</p>
                {/if}

                <p class="add-address">
                  <a href="{$new_address_invoice_url}"><i class="material-icons">&#xE145;</i>{l s='New billing address' d='Shop.Theme.Actions'}</a>
                </p>
              {/if}

            </div>
          {/if}
          {assign var="addressControl" value='1'}
          {if $delivery_addresses|count < 1 || $invoice_addresses|count < 1}
            {assign var="addressControl" value='0'}
          {/if}
          {* BOTÓN CONTINUAR CUANDO NO HAY FORMULARIOS ABIERTOS *}
          {if !$show_delivery_address_form && !$show_invoice_address_form}
            <div class="clearfix">
            {if $addressControl == '1'}
              <button id="confirmAddressButton"
                      data-customer="{$customer.id}"
                      data-location="directions"
                      type="submit"
                      class="btn btn-primary continue float-xs-right"
                      name="confirm-addresses"
                      data-control="{$addressControl}"
                      value="1">
                {l s='Continue' d='Shop.Theme.Actions'}
              </button>
            {else}
              <div class="ac-error">{l s='Address requirement message' d='Shop.Theme.Actions'}</div>
            {/if}
            </div>
          {/if}

        </div>
      </div>
    </form>
  </div>
{/block}

