<!-- Modal HTML -->
<div id="myModal" class="modal fadee" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div id="modalDialog" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="myModalTitle" class="modal-title">{l s='Confirmation?' mod='correosoficial'}</h5>
            </div>
            <div id="myModalDescription" class="modal-body">
                <p>...</p>
            </div>
            <div class="modal-footer">
                <button id="myModalCancelButton" type="button" class="btn btn-danger"
                    data-bs-dismiss="modal">{l s='Cancel' mod='correosoficial'}</button>
                <button id="myModalActionButton" type="button"
                    class="myModalActionButton btn btn-primary">{l s='Action' mod='correosoficial'}</button>
            </div>
        </div>
    </div>
</div>

<div id="correos_oficial_main_container" class="correos-oficial col-lg-12">

    <div class="card card-custom detail-order-container">

        <div class="errorSender-screen" {if !$show_sender_modal} style="display:none" {/if}>
            <div class="errorSender-container">
                <div class="errorSender-body">
                    <div class="errorSender-text">
                        {if $senders}
                            <strong class="error_sender_name">{$error_sender_name}</strong>
                            {l s='does not have a contract assigned for' mod='correosoficial'}
                            <strong class="error_company_name">{$error_company_name}</strong>
                            {l s=', you must change the sender, or configure a sender with a valid contract' mod='correosoficial'}
                        {else}
                            {l s='You currently do not have any sender configured' mod='correosoficial'}
                        {/if}
                    </div>
                    <div class="errorSender-buttons">
                        {if $senders}
                            <button id="errorSender-change" class="btn btn-lg">
                                {l s='Change Sender' mod='correosoficial'}
                            </button>
                        {/if}
                        <button id="errorSender-edit" class="btn btn-lg">
                            <a href="{$co_url_settings}#sender-anchor">
                                {l s='Set a Sender' mod='correosoficial'}
                            </a>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-header card-header-oder">
            <img src="{$co_base_dir}views/img/logos/logo-order.png" alt="Correos" class="order-logo">
            <h2 class="order-title">
                {l s='REGISTRATION ORDER' mod='correosoficial'} #{$order_number}  
            </h2>
            <input type="hidden" id="co_ps_base_uri" name="co_ps_base_uri" value="{$co_base_dir}"/>
            <input type="hidden" id="id_order_hidden" name="id_order_hidden" value="{$order_id}"/>
            <input type="hidden" id="order_done_hidden" name="order_done_hidden" value="{$order_done}"/>
            <input type="hidden" id="order_exp_number_hidden" name="order_exp_number_hidden" value="{$correos_order['shipping_number']}"/>
            <input type="hidden" id="return_exp_number_hidden" name="return_exp_number_hidden" value="{$correos_return['shipping_number']}"/>
            <input type="hidden" id="pickup_code_hidden" name="pickup_code_hidden" value="{$correos_order['pickup_number']}"/>
            
            {if $saved_return_pickup}
            <input type="hidden" id="pickup_return_code_hidden" name="pickup_return_code_hidden" value="{$saved_return_pickup[0]->pickup_number}"/>
            {/if}
            <input type="hidden" id="require_customs_doc_hidden" name="require_customs_doc_hidden"  value="{$require_customs_doc}">
        </div>  

        <form id="comment_form" name="comment_form" class="needs-validation"></form>

        <div class="card-body">
            <form id="order_form" name="order_form" class="needs-validation">
                {include file='./adminOrderSender.tpl'}
                {include file='./adminOrderDestinatary.tpl'}
                {include file='./adminOrderShipping.tpl'}
                {include file='./adminOrderAddedValues.tpl'}
                {include file='./adminOrderShippingRegister.tpl'}
                {include file='./adminOrderReturns.tpl'}
            </form>
        </div>

    </div>
</div>

<script>
    var requiredCustomMessage = "{l s='Required field' mod='correosoficial'}";
    var wrongACCAndIBAN = "{l s='Please specify a valid Bank Account number/IBAN' mod='correosoficial'}";
    var invalidEmail = "{l s='Please enter a valid email address' mod='correosoficial'}";
    var invalidNumber = "{l s='Input a valid number without symbols or blank spaces' mod='correosoficial'}";
    var wrongDniCif = "{l s='Incorrect DNI/CIF number, please correct it before continuing' mod='correosoficial'}";
    var minLengthMessage = "{l s='Please enter at least' mod='correosoficial'}";
    var maxLengthMessage = "{l s='Please enter no more than' mod='correosoficial'}";
    var characters = "{l s='characters' mod='correosoficial'}";
    var valuesDimensionDefault= "{l s='The minimum dimensions of a shipment are 15 x 10 x 1 cm.' mod='correosoficial'}";

    var atention = "{l s='Atention' mod='correosoficial'}";
    var messageForCancelOfficeAndCityPaq= "{l s='Cancelling the order will delete the data from the office/CityPaq. They can be copied to the clipboard by pressing the button next to Change office/CityPaq' mod='correosoficial'}";
    var cancelOrderStr = "{l s='Cancel Order' mod='correosoficial'}";
    var cancelStr = "{l s='Cancel' mod='correosoficial'}";
    var messageWrongLabelFormat = "{l s='The selected format is only available for CEX' mod='correosoficial'}";
    var co_address = "{l s='Address: ' mod='correosoficial'}";
    var co_city = "{l s='City: ' mod='correosoficial'}"; 
    var co_cp = "{l s='CP: ' mod='correosoficial'}"; 
</script>
