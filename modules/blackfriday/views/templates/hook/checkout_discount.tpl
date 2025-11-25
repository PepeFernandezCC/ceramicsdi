{if isset($bf_discount_amount) && $bf_discount_amount > 0}

    {* Ocultar la línea estándar de transporte (#cart-subtotal-shipping) *}
    <style>
    #cart-subtotal-shipping {
        display: none !important;
    }
    </style>

    {* Transporte original (antes de aplicar el 50%) *}
    <div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-blackfriday-original">
        <span class="label">
            {l s='Transporte' mod='blackfriday'}
        </span>
        <span class="value">
            {str_replace('.', ',', $bf_original_shipping)} €
        </span>
    </div>

    {* Descuento aplicado por Black Friday *}
    <div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-blackfriday-discount">
        <span class="label" style="font-weight:bold">
            {l s='Descuento Black Friday' mod='blackfriday'}
        </span>
        <span class="value" style="color:#ff8827">
            -{str_replace('.', ',', $bf_discount_amount)} €
        </span>
    </div>

{/if}
