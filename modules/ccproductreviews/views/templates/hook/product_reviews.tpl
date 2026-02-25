<div class="ccpr-block">
  <div class="ccpr-summary">
    <strong>Valoración media:</strong> {$ccpr_avg|escape:'html':'UTF-8'} / 5
    <span>({$ccpr_count|intval} reseñas)</span>
  </div>

  <div class="ccpr-list">
    {if $ccpr_reviews|count}
      {foreach from=$ccpr_reviews item=r}
        <div class="ccpr-review">
          <div class="ccpr-head">
            <strong>{$r.customer_name|escape:'html':'UTF-8'}</strong>
            <span class="ccpr-stars">{for $i=1 to 5}{if $i <= $r.rating}★{else}☆{/if}{/for}</span>
            <small>{$r.date_add|escape:'html':'UTF-8'}</small>
          </div>
          {if $r.comment}
            <div class="ccpr-comment">{$r.comment|escape:'html':'UTF-8'}</div>
          {/if}

          {if $r.images|count}
            <div class="ccpr-images">
              {foreach from=$r.images item=img}
                <img src="{$urls.img_url}ccproductreviews/{$r.id_review|intval}/{$img|escape:'html':'UTF-8'}" alt="Foto reseña" />
              {/foreach}
            </div>
          {/if}
        </div>
      {/foreach}
    {else}
      <p>Aún no hay reseñas.</p>
    {/if}
  </div>

  {if $ccpr_can_review}
    <hr>
    <div class="ccpr-form">
      <h4>Escribe tu reseña</h4>

      <div>
        <label>Estrellas (1-5)</label>
        <input type="number" min="1" max="5" name="ccpr_rating" value="5">
      </div>

      <div>
        <label>Comentario</label>
        <textarea name="ccpr_comment" rows="4"></textarea>
      </div>

      <div>
        <label>Fotos (máx 3)</label>
        <input type="file" name="ccpr_photos" multiple accept="image/jpeg,image/png,image/webp">
      </div>

      <button type="button" class="btn btn-primary" id="ccpr_submit">Enviar reseña</button>
      <div id="ccpr_msg" style="margin-top:10px;"></div>
    </div>
  {/if}
</div>