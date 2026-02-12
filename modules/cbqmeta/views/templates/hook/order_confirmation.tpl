<script>
{literal}
(function() {
  if (typeof cbq !== 'function') return;

  cbq('track', 'Purchase', {
    value: '{/literal}{$purchase_value|escape:'javascript'}{literal}',
    currency: '{/literal}{$purchase_currency|escape:'javascript'}{literal}',
    order_id: '{/literal}{$purchase_order_id|escape:'javascript'}{literal}'
  });
})();
{/literal}
</script>