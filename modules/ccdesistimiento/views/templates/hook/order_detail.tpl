{if isset($cc_desistimiento_already_requested) && $cc_desistimiento_already_requested}
  <div class="alert alert-info">
    {$cc_desistimiento_already_requested_text|escape:'htmlall':'UTF-8'}
  </div>
{else}
  <div class="card mt-3 cc-desistimiento-box">
    <div class="card-body">
      <h3 class="h5">{$cc_desistimiento_title|escape:'htmlall':'UTF-8'}</h3>
      <p>{$cc_desistimiento_order_detail_text|escape:'htmlall':'UTF-8'}</p>
      <a class="cc-desistimiento-history-btn" href="{$cc_desistimiento_link|escape:'htmlall':'UTF-8'}">{$cc_desistimiento_button_label|escape:'htmlall':'UTF-8'}</a>
    </div>
  </div>
{/if}
