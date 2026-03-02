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
<table id="total-tab" width="100%">

    <tr>
        <td class="grey" width="70%">
            {l s='Total Products' mod='paypalwithfee' pdf='true'}
        </td>
        <td class="white" width="30%">
            {displayPrice currency=$order->id_currency price=$footer.products_before_discounts_tax_excl}
        </td>
    </tr>

    {if $footer.product_discounts_tax_excl > 0}

        <tr>
            <td class="grey" width="70%">
                {l s='Total Discounts' mod='paypalwithfee' pdf='true'}
            </td>
            <td class="white" width="30%">
                - {displayPrice currency=$order->id_currency price=$footer.product_discounts_tax_excl}
            </td>
        </tr>

    {/if}
    {if !$order->isVirtual()}
        <tr>
            <td class="grey" width="70%">
                {l s='Shipping Costs' mod='paypalwithfee' pdf='true'}
            </td>
            <td class="white" width="30%">
                {if $footer.shipping_tax_excl > 0}
                    {if is_array($fee) && $fee.fee > 0}
                        {displayPrice currency=$order->id_currency price=($footer.shipping_tax_excl - $fee.fee)}
                    {else}
                        {displayPrice currency=$order->id_currency price=$footer.shipping_tax_excl}
                    {/if}
                {else}
                    {l s='Free Shipping' mod='paypalwithfee' pdf='true'}
                {/if}
            </td>
        </tr>
    {/if}

    {if is_array($fee) && $fee.fee > 0}
        <tr>
            <td class="grey" width="70%">
                {l s='Paypal' mod='paypalwithfee'}
            </td>
            <td class="white" width="30%">
                {if $fee.tax_rate > 0}
                    {displayPrice currency=$order->id_currency price=($fee.fee / ( 1 + (0.01 * $fee.tax_rate)))}
                {else}
                    {displayPrice currency=$order->id_currency price=$fee.fee}
                {/if}
            </td>
        </tr>
    {/if}

    {if $footer.wrapping_tax_excl > 0}
        <tr>
            <td class="grey">
                {l s='Wrapping Costs' mod='paypalwithfee' pdf='true'}
            </td>
            <td class="white">{displayPrice currency=$order->id_currency price=$footer.wrapping_tax_excl}</td>
        </tr>
    {/if}

    <tr class="bold">
        <td class="grey">
            {l s='Total (Tax excl.)' mod='paypalwithfee' pdf='true'}
        </td>
        <td class="white">
            {if is_array($fee) && $fee.fee > 0}
                {if $fee.tax_rate > 0}
                    {displayPrice currency=$order->id_currency price=(($footer.total_paid_tax_excl) + ($fee.fee / ( 1 + (0.01 * $fee.tax_rate))))}
                {else}
                    {displayPrice currency=$order->id_currency price=($footer.total_paid_tax_excl) + $fee.fee}
                {/if}
            {else}
                {displayPrice currency=$order->id_currency price=$footer.total_paid_tax_excl}
            {/if}
        </td>
    </tr>
    {if $footer.total_taxes > 0}
        <tr class="bold">
            <td class="grey">
                {l s='Total Tax' mod='paypalwithfee' pdf='true'}
            </td>
            <td class="white">
                {if is_array($fee) && $fee.fee > 0}
                    {if $fee.tax_rate > 0}
                        {displayPrice currency=$order->id_currency price=(($footer.total_taxes - $fee.fee) + ($fee.fee - ($fee.fee / (1 + (0.01 * $fee.tax_rate)))))}
                    {else}
                        {displayPrice currency=$order->id_currency price=($footer.total_taxes - $fee.fee)}
                    {/if}
                {else}
                    {displayPrice currency=$order->id_currency price=$footer.total_taxes}
                {/if}

            </td>
        </tr>
    {/if}
    <tr class="bold big">
        <td class="grey">
            {l s='Total' mod='paypalwithfee' pdf='true'}
        </td>
        <td class="white">
            {displayPrice currency=$order->id_currency price=$footer.total_paid_tax_incl}
        </td>
    </tr>
</table>
