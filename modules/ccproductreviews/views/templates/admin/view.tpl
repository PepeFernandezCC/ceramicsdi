<div class="panel">
  <h3>
    <i class="icon-comment"></i>
    {l s='Detalles de la Review' mod='ccproductreviews'}
  </h3>

  <p>
    <strong>{l s='Producto' mod='ccproductreviews'}: </strong>
    {$review.product_name|escape:'html':'UTF-8'}
  </p>

  <p>
    <strong>{l s='Cliente' mod='ccproductreviews'}: </strong>
    {$review.customer_name|escape:'html':'UTF-8'}
    (ID {$review.id_customer|intval})
  </p>

  <p>
    <strong>{l s='Valoración' mod='ccproductreviews'}: </strong>
    <span style="font-size:16px;color:#f5b301;">
      {for $i=1 to 5}
        {if $i <= $review.rating}★{else}☆{/if}
      {/for}
    </span>
    ({$review.rating|intval}/5)
  </p>

  <p>
    <strong>{l s='Visible' mod='ccproductreviews'}: </strong>
    {if $review.active}
      <span class="label label-success">{l s='Yes' mod='ccproductreviews'}</span>
    {else}
      <span class="label label-danger">{l s='No' mod='ccproductreviews'}</span>
    {/if}
  </p>

  <p>
    <strong>{l s='Fecha' mod='ccproductreviews'}: </strong>
    {$review.date_add|escape:'html':'UTF-8'}
  </p>

  <hr>

  <p><strong>{l s='Texto de la Review' mod='ccproductreviews'}:</strong></p>
  <div class="well" style="white-space:pre-wrap;">
    {$review.comment|escape:'html':'UTF-8'}
  </div>

  {if $images}
    <p><strong>{l s='Imagenes' mod='ccproductreviews'}:</strong></p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      {foreach from=$images item=img}
        <a href="{$img_base}{$img.file_name|escape:'url':'UTF-8'}" class="ccpr-admin-photo js-ccpr-admin-lightbox" 
          data-full="{$img_base}{$img.file_name|escape:'url':'UTF-8'}"target="_blank" rel="noopener">
            <img src="{$img_base}thumb_{$img.file_name|escape:'url':'UTF-8'}" style="max-width:160px;">
        </a>
      {/foreach}
    </div>
  {/if}
</div>

<div class="ccpr-admin-lightbox" id="ccpr_admin_lightbox" aria-hidden="true">
  <div class="ccpr-admin-lightbox__backdrop" data-ccpr-close></div>

  <div class="ccpr-admin-lightbox__dialog" role="dialog" aria-modal="true" aria-label="Imagen ampliada">
    <button type="button" class="ccpr-admin-lightbox__close" aria-label="Cerrar" data-ccpr-close>×</button>
    <img class="ccpr-admin-lightbox__img" id="ccpr_admin_lightbox_img" alt="">
  </div>
</div>

<div class="panel">
  <h3><i class="icon-cogs"></i> {l s='Acciones' mod='ccproductreviews'}</h3>

  <form method="post" action="{$action_url|escape:'html':'UTF-8'}" style="margin-bottom:15px;">
    <label class="control-label">
      {l s='Activar o Desactivar la Review' mod='ccproductreviews'}
    </label>
    <input type="hidden" name="id_review" value="{$review.id_review|intval}">
    <button type="submit" name="ccpr_toggle_view" class="btn btn-default">
      {if $review.active}
        <i class="icon-eye-slash" style="margin-right5:px"></i> {l s='Ocultar Review' mod='ccproductreviews'}
      {else}
        <i class="icon-eye" style="margin-right5:px"></i> {l s='Mostrar Review' mod='ccproductreviews'}
      {/if}
    </button>
  </form>

  <hr>

  <form method="post" action="{$action_url|escape:'html':'UTF-8'}">
    <input type="hidden" name="id_review" value="{$review.id_review|intval}">

    <div class="form-group">
      <label class="control-label">
        {l s='Mensaje Para el cliente' mod='ccproductreviews'}
      </label>
      <textarea name="ccpr_email_message" class="form-control" rows="5"
                placeholder="{l s='Escribe un mensaje al cliente...' mod='ccproductreviews'}"></textarea>
      <p class="help-block">
        {l s='Este mensaje enviará un eMail al cliente vinculado a esta reseña.' mod='ccproductreviews'}
      </p>
    </div>

    <button type="submit" name="ccpr_send_email" class="btn btn-primary">
      <i class="icon-envelope" style="margin-right5:px"></i> {l s='Enviar email' mod='ccproductreviews'}
    </button>
  </form>
</div>

<div class="panel">
  <a class="btn btn-default" href="{$back_url|escape:'html':'UTF-8'}">
    <i class="icon-arrow-left"></i>
    {l s='Volver al listado' mod='ccproductreviews'}
  </a>
</div>