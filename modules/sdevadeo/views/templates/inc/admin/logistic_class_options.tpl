{foreach $logisticClass as $class}
    <option value="{$class['code']}" {if $class['code'] == 'INIT'} selected>{l s='(default) ' mod='sdevadeo'}{else}>{/if}{$class['label']}</option>
{/foreach}