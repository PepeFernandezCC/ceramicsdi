<div class="panel">
  <h3>{l s='Inspiraciones - Categorías' mod='inspiration'}</h3>
  <p>{l s='Selecciona una categoría por nombre (ruta completa) y agréguela al módulo.' mod='inspiration'}</p>

  <div class="form-inline" style="display:block; max-width: 720px;">
    <select id="categorySelect" class="form-control" size="12" style="width:100%; max-width:720px;">
      <option value="">{l s='Selecciona una categoría' mod='inspiration'}</option>
      {foreach from=$categories item=cat}
        <option value="{$cat.id_category}">{$cat.path|escape:'html':'UTF-8'}</option>
      {/foreach}
    </select>
    <div style="margin-top:10px">
      <button id="confirmAddCat" class="btn btn-primary">
        {l s='Agregar categoría' mod='inspiration'}
      </button>
    </div>
  </div>
</div>

{literal}
<script>
$('#confirmAddCat').on('click', function(){
  var idc = $('#categorySelect').val();
  if(!idc) return;
  $.post('{/literal}{$add_category_ajax_url}{literal}', {"id_category": idc}, function(){ location.reload(); });
});
</script>
{/literal}
