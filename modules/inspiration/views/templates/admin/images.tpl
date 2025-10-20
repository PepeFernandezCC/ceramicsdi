<div class="panel">
  <h3>{l s='Inspiraciones - Imágenes del producto' mod='inspiration'}</h3>
  <form method="post" action="{$save_url}">
    <input type="hidden" name="id_category" value="{$id_category}">
    <input type="hidden" name="id_product" value="{$id_product}">

    <div class="row">
      {foreach from=$thumbnails item=t}
        <div class="col-lg-2 col-md-3 col-sm-4" style="margin-bottom:15px;">
          <label style="display:block; text-align:center;">
            <img src="{$t.src}" alt="img {$t.id_image}" style="max-width:100%; border:1px solid #ddd; padding:4px;">
            <div style="margin-top:5px;">
              <input type="radio" name="id_image" value="{$t.id_image}" {if $t.selected}checked{/if}>
              <small>pos: {$t.position}</small>
            </div>
          </label>
        </div>
      {/foreach}
    </div>

    <button type="submit" name="saveImage" class="btn btn-primary">
      {l s='Guardar' mod='inspiration'}
    </button>
  </form>
</div>
