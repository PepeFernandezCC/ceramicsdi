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
<!--  TAX DETAILS -->
{if $tax_exempt}

    {l s='Exempt of VAT according to section 259B of the General Tax Code.' mod='paypalwithfee' pdf='true'}

{elseif (isset($tax_breakdowns) && $tax_breakdowns)}
    <table id="tax-tab" width="100%">
        <thead>
            <tr>
                <th class="header small">{l s='Tax Detail' mod='paypalwithfee' pdf='true'}</th>
                <th class="header small">{l s='Tax Rate' mod='paypalwithfee' pdf='true'}</th>
                    {if $display_tax_bases_in_breakdowns}
                    <th class="header small">{l s='Base price' mod='paypalwithfee' pdf='true'}</th>
                    {/if}
                <th class="header-right small">{l s='Total Tax' mod='paypalwithfee' pdf='true'}</th>
            </tr>
        </thead>
        <tbody>
            {assign var=has_line value=false}

            {foreach $tax_breakdowns as $label => $bd}
                {assign var=label_printed value=false}

                {foreach $bd as $line}
                    {if $line.rate == 0}
                        {continue}
                    {/if}
                    {assign var=has_line value=true}
                    <tr>
                        <td class="white">
                            {if !$label_printed}
                                {if $label == 'product_tax'}
                                    {l s='Products' mod='paypalwithfee' pdf='true'}
                                {elseif $label == 'shipping_tax'}
                                    {l s='Shipping' mod='paypalwithfee' pdf='true'}
                                {elseif $label == 'ecotax_tax'}
                                    {l s='Ecotax' mod='paypalwithfee' pdf='true'}
                                {elseif $label == 'wrapping_tax'}
                                    {l s='Wrapping' mod='paypalwithfee' pdf='true'}
                                {/if}
                                {assign var=label_printed value=true}
                            {/if}
                        </td>

                        <td class="center white">
                            {$line.rate|escape:'html':'UTF-8'} %
                        </td>

                        {if $display_tax_bases_in_breakdowns}
                            <td class="right white">
                                {if isset($is_order_slip) && $is_order_slip}- {/if}
                                {if $label=='shipping_tax' && is_array($fee) && $fee.fee > 0}
                                    {displayPrice currency=$order->id_currency price=($line.total_tax_excl - $fee.fee)}
                                {else}     
                                    {displayPrice currency=$order->id_currency price=$line.total_tax_excl}
                                {/if}
                            </td>
                        {/if}

                        <td class="right white">
                            {if isset($is_order_slip) && $is_order_slip}- {/if}
                            {displayPrice currency=$order->id_currency price=$line.total_amount}
                        </td>
                    </tr>
                {/foreach}
            {/foreach}

            {if !$has_line}
                <tr>
                    <td class="white center" colspan="{if $display_tax_bases_in_breakdowns}4{else}3{/if}">
                        {l s='No taxes' mod='paypalwithfee' pdf='true'}
                    </td>
                </tr>
            {/if}
            {if is_array($fee) && $fee.fee > 0}
                <tr>
                    <td class="white">
                        {l s='Paypal' mod='paypalwithfee'}
                    </td>
                    <td class="center white">
                        {$fee.tax_rate|escape:'html':'UTF-8'}%
                    </td>
                    {if $display_tax_bases_in_breakdowns}
                        <td class="right white">
                            {if $fee.tax_rate > 0}
                                {displayPrice currency=$order->id_currency price=($fee.fee / ( 1 + (0.01 * $fee.tax_rate)))}
                            {else}
                                {displayPrice currency=$order->id_currency price=$fee.fee}
                            {/if}   
                        </td>
                    {/if}
                    <td class="right white">
                        {if $fee.tax_rate > 0}
                            {displayPrice currency=$order->id_currency price=($fee.fee - ($fee.fee / (1 + (0.01 * $fee.tax_rate))))}
                        {else}
                            -
                        {/if}    
                    </td>
                </tr>
            {/if}

        </tbody>
    </table>

{/if}
<!--  / TAX DETAILS -->
