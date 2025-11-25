{if isset($bf_discount_amount) && $bf_discount_amount > 0}
<tr>
    <td class="right">{l s='Descuento envío Black Friday' mod='blackfriday'}</td>
    <td class="right">-{$bf_discount_amount|price}</td>
</tr>
{/if}
