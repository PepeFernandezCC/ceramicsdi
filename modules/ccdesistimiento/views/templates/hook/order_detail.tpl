{if isset($cc_desistimiento_already_requested) && $cc_desistimiento_already_requested}
  <div class="alert alert-info">
    Ya has solicitado el desistimiento de este pedido. Ceramic Connection revisara la solicitud.
  </div>
{else}
  <div class="card mt-3 cc-desistimiento-box">
    <div class="card-body">
      <h3 class="h5">Derecho de desistimiento</h3>
      <p>Si deseas desistir de este pedido dentro del plazo legal, puedes comunicarlo desde aqui.</p>
      <a class="btn btn-primary" href="{$cc_desistimiento_link|escape:'htmlall':'UTF-8'}">Solicitar desistimiento</a>
    </div>
  </div>
{/if}
