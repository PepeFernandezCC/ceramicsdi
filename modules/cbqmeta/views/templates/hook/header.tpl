<script>
{literal}
!function(a,h,e,v,n,t,s)
{if(a.cbq)return;n=a.cbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!a._cbq)a._cbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=h.createElement(e);t.async=!0;
t.src=v;s=h.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
{/literal}
'{$cbqmeta_sdk_url|escape:'javascript'}'
{literal}
);

cbq('setHost', '{/literal}{$cbqmeta_host|escape:'javascript'}{literal}');
cbq('init', '{/literal}{$cbqmeta_pixel_id|escape:'javascript'}{literal}');
cbq('track', 'PageView');

window.CBQMETA = {
  controller: '{/literal}{$cbqmeta_controller|escape:'javascript'}{literal}',
  currency: '{/literal}{$cbqmeta_currency|escape:'javascript'}{literal}'
};
{/literal}
</script>
