<div class="panel">
  <h3>
    <i class="icon-comment"></i>
    {l s='Review detail' mod='ccproductreviews'}
  </h3>

  <p>
    <strong>{l s='Product' mod='ccproductreviews'}:</strong>
    {$review.product_name|escape:'html':'UTF-8'}
  </p>

  <p>
    <strong>{l s='Customer' mod='ccproductreviews'}:</strong>
    {$review.customer_name|escape:'html':'UTF-8'}
    (ID {$review.id_customer|intval})
  </p>

  <p>
    <strong>{l s='Rating' mod='ccproductreviews'}:</strong>
    <span style="font-size:16px;color:#f5b301;">
      {for $i=1 to 5}
        {if $i <= $review.rating}★{else}☆{/if}
      {/for}
    </span>
    ({$review.rating|intval}/5)
  </p>

  <p>
    <strong>{l s='Visible' mod='ccproductreviews'}:</strong>
    {if $review.active}
      <span class="label label-success">{l s='Yes' mod='ccproductreviews'}</span>
    {else}
      <span class="label label-danger">{l s='No' mod='ccproductreviews'}</span>
    {/if}
  </p>

  <p>
    <strong>{l s='Date' mod='ccproductreviews'}:</strong>
    {$review.date_add|escape:'html':'UTF-8'}
  </p>

  <hr>

  <p><strong>{l s='Comment' mod='ccproductreviews'}:</strong></p>
  <div class="well" style="white-space:pre-wrap;">
    {$review.comment|escape:'html':'UTF-8'}
  </div>

  {if $images}
    <p><strong>{l s='Images' mod='ccproductreviews'}:</strong></p>
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
  <a class="btn btn-default" href="{$back_url|escape:'html':'UTF-8'}">
    <i class="icon-arrow-left"></i>
    {l s='Back to list' mod='ccproductreviews'}
  </a>
</div>