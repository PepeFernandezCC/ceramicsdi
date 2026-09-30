{**
 * CERAMIC CONNECTION - Product sort selector (category listings)
 *}
<div class="ccproductsort">
    <label class="ccproductsort__label" for="ccproductsort-select">{l s='Sort by' mod='ccproductsort'}</label>
    <select id="ccproductsort-select" class="ccproductsort__select js-ccproductsort-select">
        {foreach from=$ccproductsort_options key=value item=label}
            <option value="{$value|escape:'html':'UTF-8'}"{if $value == $ccproductsort_current} selected{/if}>{$label|escape:'html':'UTF-8'}</option>
        {/foreach}
    </select>
</div>
