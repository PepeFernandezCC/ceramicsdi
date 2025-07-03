{*
* 2020 4webs
*
* DEVELOPED By 4webs Prestashop Platinum Partner
*
* @author    4webs
* @copyright 4webs 2017
* @version 5.1.4
* @category payment_gateways
* @license 4webs
*}
<div class="tab-pane active" id="paypalwithfee">
    <h4 class="visible-print">{l s='Paypal with fee' mod='paypalwithfee'} <span class="badge rounded badge-dark">1</span></h4>
    <div class="form-horizontal">
        <div class="table-responsive">
            <div class="ppwf">
                <p>{l s='Paypal fee:' mod='paypalwithfee'} <strong>{$fee.price_parsed|escape:'htmlall':'UTF-8'}</strong></p>
                <p>{l s='Tax:' mod='paypalwithfee'} <strong>{$fee.tax_rate|escape:'html':'UTF-8'} %</strong></p>
                <p>{l s='Paypal Transaction ID:' mod='paypalwithfee'} <strong>{$fee.transaction_id|escape:'html':'UTF-8'}</strong></p>
                <p>{l s='Paypal Buyer ID:' mod='paypalwithfee'} <strong>{$fee.payer_id|escape:'html':'UTF-8'}</strong></p>
            </div>
        </div>
        {*{if Configuration::get('PS_INVOICE') && count($invoices_collection_) && $invoice_number_ && $form_go_ppwf_generatepdf }*}
            {*<div class="info-block">
                <a href="{$form_go_ppwf_refund|escape:'html':'UTF-8'}&submitAction=ppwf_pdf" class="btn btn-primary _blank" target="_blank">
                    <i class="icon-download"></i>
                    {l s='Download Invoice' mod='paypalwithfee'}
                </a>
            </div>*}
        {*{/if}*}
    </div>
    <hr style="margin-top: 0.75rem; margin-bottom: 0.75rem;"/>
    <div id="paypalwithfee_refund" class="">
    <h4 class="">
        {l s='Paypal with fee refund' mod='paypalwithfee'} <span class="badge rounded badge-dark">{$refund|@count|escape:'htmlall':'UTF-8'}</span>
    </h4>
    <form id="paypalwithfee_refund_form" class="info-block" method="post" action="{$form_go_ppwf_refund|escape:'html':'UTF-8'}" onSubmit="if (!confirm('{l s='This process not has turning back. Are you sure that do you want continue?' mod='paypalwithfee'}{*{l s='This process not has turning back. Are you sure that do you want continue?' d='Modules.Paypalwithfee.Admin'}*}'))
                return false;">
        {if $refund|@count > 0}
            <p>{l s='You can do a partial refund. Can not do a full refund after a partial refund.' mod='paypalwithfee'}</p>
            <label><input type='radio' id="ppwf_partial_refund" name='refund' value='0' checked> <span>Partial refund</span></label>
            {else}
            <p>{l s='You can do a partial refund or full refund of paypal payment.' mod='paypalwithfee'}</p>
            <label><input type='radio' id="ppwf_total_refund" name='refund' value='1'> <span>Full refund</span></label><br/>
            <label><input type='radio' id="ppwf_partial_refund" name='refund' value='0' checked> <span>Partial refund</span></label>
            {/if}
        <div id="ppwf_refund_content">
            <label for="ppwf_refund_amount">{l s='Amount to refund:' mod='paypalwithfee'}
                <input type="text" class="form-control" id="ppwf_refund_amount" max="{if $max_refund}{$fee.total_amount|string_format:'%.2f' - $max_refund|string_format:'%.2f'|escape:'htmlall':'UTF-8'}{else}{$fee.total_amount|string_format:'%.2f'|escape:'htmlall':'UTF-8'}{/if}" name="ppwf_refund_amount"></label> <input style="vertical-align: middle; margin-top: -4px;" class="btn btn-danger" type="submit" name="ppwf_refund" value="{l s='Refund' mod='paypalwithfee'}{*{l s='Refund' d='Modules.Paypalwithfee.Admin'}*}"/>
        </div>
    </form>
    {if isset($ppwfmessage_ok)}<p class="alert alert-success">{$ppwfmessage_ok|escape:'html':'UTF-8'}</p>{/if}
    {if isset($ppwfmessage_error)}<p class="alert alert-danger">{$ppwfmessage_error|escape:'html':'UTF-8'}</p>{/if}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><span class="title_box ">{l s='Date' mod='paypalwithfee'}</span></th>
                    <th><span class="title_box ">{l s='Amount' mod='paypalwithfee'}</span></th>
                    <th><span class="title_box ">{l s='Transaction ID' mod='paypalwithfee'}</span></th>
                </tr>
            </thead>
            <tbody>
                {if $refund|@count > 0}
                    {foreach from=$refund item=ref}
                        <tr>
                            <td>{$ref.date|escape:'html':'UTF-8'}</td>
                            <td>{$ref.amount|string_format:"%.2f"|replace:".":","|escape:'htmlall':'UTF-8'}</td>
                            <td>{$ref.transaction_id|escape:'html':'UTF-8'}</td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="3">{l s='No refund has made yet' mod='paypalwithfee'}</td>
                    </tr>
                {/if}
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#shipping').removeClass('active');
        $('#paypalwithfee_refund input[name="refund"]').change(function () {
            var how_refund = $(this).val();
            if (how_refund == 1) {
                $('#ppwf_refund_amount').val(parseFloat($('#ppwf_refund_amount').attr('max')).toFixed(2)).prop('disabled', true);
            } else {
                $('#ppwf_refund_amount').val('').prop('disabled', false);
            }
        });

        $('#ppwf_refund_amount').change(function () {
            //$(this).val(parseFloat($("ppwf_refund_amount").val(),10).toFixed(2));
        });
    });

    {literal}
        function changeOrderShippingAddress()
        {
            jQuery.ajax(
                {
                    url: "{/literal}{$change_address_endpoint|escape:'html':'UTF-8'}{literal}",
                    method: "POST",
                    data: {
                        idFee: {/literal}{$fee.id_ppwf|escape:'html':'UTF-8'}{literal},
                        token: "{/literal}{$ppwf_ajax_token|escape:'html':'UTF-8'}{literal}"
                    },
                    success: (data) =>
                    {
                        location.reload();
                    },
                    error: (error) =>
                    {
                        alert("Error processing, check the console for more details");
                        console.error(error);
                    }
                }
            )
        }
    {/literal}

</script>
</div>