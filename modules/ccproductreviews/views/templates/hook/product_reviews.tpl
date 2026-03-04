<div class="ccpr-block" data-client="{$clientId}">
  {if $ccpr_count > 0}
    <div class="ccpr-summary">
      <strong>{l s='Como lo puntúan otros clientes' d='Shop.Theme.Catalog'}: </strong>

      {assign var=avg value=$ccpr_avg|floatval}
      {assign var=pct value=($avg*20)} {* 0..100 *}

      <span class="ccpr-avg" aria-label="{$ccpr_avg|escape:'html':'UTF-8'} de 5">
        <span class="ccpr-avg__stars">
          <span class="ccpr-avg__fill" style="width: {$pct}%"></span>
        </span>
        <span class="ccpr-avg__num">{$ccpr_avg|escape:'html':'UTF-8'} de 5</span>
        <span class="ccpr-avg__count">({$ccpr_count|intval} reseñas)</span>
      </span>
    </div>
  {/if}
  <hr>

  <div class="ccpr-list">
    {if $ccpr_reviews|count}
      {foreach from=$ccpr_reviews item=r}
        <article class="ccpr-review-card">
          <header class="ccpr-review-card__header">
            <div class="ccpr-review-card__user">
              <div class="ccpr-avatar" aria-hidden="true">
                {$r.customer_name|escape:'html':'UTF-8'|truncate:1:"":true}
              </div>
              <div class="ccpr-user-meta">
                <div class="ccpr-user-name">
                  {$r.customer_name|escape:'html':'UTF-8'}
                </div>
                <div class="ccpr-user-date">
                  {$r.date_add|escape:'html':'UTF-8'}
                </div>
              </div>
            </div>

            <div class="ccpr-rating-badge" aria-label="{$r.rating|intval} de 5">
              <span class="ccpr-stars">
                {for $i=1 to 5}{if $i <= $r.rating}★{else}☆{/if}{/for}
              </span>
              <span class="ccpr-rating-num">{$r.rating|intval}/5</span>
            </div>
          </header>

          {if $r.comment}
            <div class="ccpr-review-card__body">
              <p class="ccpr-comment">
                {$r.comment|escape:'html':'UTF-8'|nl2br nofilter}
              </p>
            </div>
          {/if}

          {if $r.images|count}
            <div class="ccpr-review-card__images">
              {foreach from=$r.images item=img}
                {assign var=orig value="{$ccpr_img_base}{$r.id_review|intval}/{$img|escape:'url':'UTF-8'}"}
                {assign var=thumb value="{$ccpr_img_base}{$r.id_review|intval}/thumb_{$img|escape:'url':'UTF-8'}"}

                <a class="ccpr-photo js-ccpr-lightbox"
                  href="{$orig}"
                  data-full="{$orig}">
                  <img loading="lazy"
                      src="{$thumb}"
                      alt="Foto reseña"
                      onerror="this.onerror=null;this.src='{$orig}';" />
                </a>
              {/foreach}
            </div>
          {/if}
        </article>
      {/foreach}
    {else}
      <div class="ccpr-empty">
        Aún no hay reseñas.
      </div>
    {/if}
  </div>

  <div class="ccpr-lightbox" id="ccpr_lightbox" aria-hidden="true">
    <div class="ccpr-lightbox__backdrop" data-ccpr-close></div>

    <div class="ccpr-lightbox__dialog" role="dialog" aria-modal="true" aria-label="Imagen ampliada">
      <button type="button" class="ccpr-lightbox__close" aria-label="Cerrar" data-ccpr-close>×</button>
      <img class="ccpr-lightbox__img" id="ccpr_lightbox_img" alt="">
    </div>
  </div>

  <hr>

  {*if $ccpr_can_review*}
    <div class="ccpr-card ccpr-form">
      <div class="ccpr-card__header">
        <h4 class="ccpr-title">Escribe tu reseña</h4>
        <p class="ccpr-subtitle">Tu opinión ayuda a otros clientes.</p>
      </div>

      <div class="ccpr-card__body">
        <div class="ccpr-field ccpr-form">
          <label class="ccpr-label">{l s='Valoración' mod='ccproductreviews'}</label>

          <div class="ccpr-rating" aria-label="Valoración del producto">
            <input type="radio" id="ccpr_star_{$ccpr_id_product|intval}_5" name="ccpr_rating" value="5" >
            <label for="ccpr_star_{$ccpr_id_product|intval}_5" title="5 estrellas">★</label>

            <input type="radio" id="ccpr_star_{$ccpr_id_product|intval}_4" name="ccpr_rating" value="4">
            <label for="ccpr_star_{$ccpr_id_product|intval}_4" title="4 estrellas">★</label>

            <input type="radio" id="ccpr_star_{$ccpr_id_product|intval}_3" name="ccpr_rating" value="3">
            <label for="ccpr_star_{$ccpr_id_product|intval}_3" title="3 estrellas">★</label>

            <input type="radio" id="ccpr_star_{$ccpr_id_product|intval}_2" name="ccpr_rating" value="2">
            <label for="ccpr_star_{$ccpr_id_product|intval}_2" title="2 estrellas">★</label>

            <input type="radio" id="ccpr_star_{$ccpr_id_product|intval}_1" name="ccpr_rating" value="1" checked>
            <label for="ccpr_star_{$ccpr_id_product|intval}_1" title="1 estrella">★</label>
          </div>

          <small class="ccpr-help">Haz clic en las estrellas para puntuar.</small>
        </div>

        <div class="ccpr-field">
          <label class="ccpr-label" for="ccpr_comment">{l s='Comentario' mod='ccproductreviews'}</label>
          <textarea
            class="ccpr-textarea"
            id="ccpr_comment"
            name="ccpr_comment"
            rows="4"
            placeholder="¿Qué te ha parecido el producto?"
            maxlength="1000"
          ></textarea>
          <div class="ccpr-meta">
            <small class="ccpr-help">Máximo 1000 caracteres.</small>
            <small class="ccpr-counter"><span id="ccpr_comment_count">0</span>/1000</small>
          </div>
        </div>

        <div class="ccpr-field">
          <label class="ccpr-label" for="ccpr_photos">{l s='Fotos' mod='ccproductreviews'}</label>

          <div class="ccpr-upload">
            <input
              class="ccpr-file"
              type="file"
              id="ccpr_photos"
              name="ccpr_photos"
              multiple
              accept="image/jpeg,image/png,image/webp"
            >
            <div class="ccpr-upload__hint">
              <strong>Sube hasta 3 fotos</strong>
              <span>Formatos: JPG, PNG, WebP</span>
            </div>
          </div>
          <small class="ccpr-help" id="ccpr_files_hint">0/3 seleccionadas</small>
        </div>
        <div id="ccpr_root">
          <input type="hidden" id="ccpr_id_product" value="{$ccpr_id_product|intval}">
          <input type="hidden" id="ccpr_submit_url" value="{$ccpr_submit_url|escape:'html':'UTF-8'}">
          <input type="hidden" id="ccpr_max_files" value="{$ccpr_max_files|intval}">
          <input type="hidden" id="ccpr_token" value="{$ccpr_token|escape:'html':'UTF-8'}">
        </div>
        <div class="ccpr-actions">
          <button type="button" class="btn btn-primary ccpr-btn" id="ccpr_submit">
            Enviar reseña
          </button>
          <div class="ccpr-msg" id="ccpr_msg" aria-live="polite"></div>
        </div>
      </div>
    </div>
  {*/if*}
</div>