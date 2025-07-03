<div class="correos_oficial">
    {if $params.carrier_type eq 'office'}
        {include file="module:correosoficial/views/templates/hook/helper/CarrierExtraContent_office.tpl"
        params=$params}
    {/if}
    {if $params.carrier_type eq 'citypaq'}
        {include file="module:correosoficial/views/templates/hook/helper/CarrierExtraContent_citypaq.tpl"
        params=$params}
    {/if}
    {if $params.carrier_type eq 'international'}
        {include file="module:correosoficial/views/templates/hook/helper/CarrierExtraContent_international.tpl"
        params=$params}
    {/if}

    {if $params.carrier_type eq 'homedelivery'}
        {include file="module:correosoficial/views/templates/hook/helper/CarrierExtraContent_homepaq.tpl"
        params=$params}
    {/if}
</div>

{if $params.onepagecheckout eq 'active' && $params.opc_counter >= 1}
    <script>
        {if $params.carrier_type eq 'office' or $params.carrier_type eq 'citypaq'}
            {include file="module:correosoficial/js/checkout.js"}
        {/if}
    </script>
{/if}
