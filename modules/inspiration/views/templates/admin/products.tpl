<div class="panel">
  <h3>{l s='Inspiraciones - Productos' mod='inspiration'}</h3>
  <p><strong>{l s='Categoría:' mod='inspiration'}</strong> #{$id_category} - {$category_name}</p>

  <div class="form-inline">
    <select id="productSelect" class="form-control">
      <option value="">{l s='Selecciona un producto de la categoría' mod='inspiration'}</option>
      {foreach from=$products item=p}
        <option value="{$p.id_product}">#{$p.id_product} - {$p.name|escape:'html':'UTF-8'}</option>
      {/foreach}
    </select>
    <button id="btnAddProd" class="btn btn-primary" style="margin-left:8px;">
      {l s='Agregar producto' mod='inspiration'}
    </button>
  </div>
</div>

{literal}
<script>
$('#btnAddProd').on('click', function(){
  var idp = $('#productSelect').val();
  if(!idp) return;
  $.post('{/literal}{$add_product_ajax_url}{literal}', {"id_product": idp}, function(){ location.reload(); });
});
</script>
{/literal}
