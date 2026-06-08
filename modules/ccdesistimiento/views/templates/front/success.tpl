{extends file='page.tpl'}

{block name='page_content'}
  <section id="cc-desistimiento-success">
    <h1>Solicitud registrada</h1>
    <div class="alert alert-success">
      Hemos recibido tu solicitud de desistimiento. Te hemos enviado un email de confirmacion y Ceramic Connection revisara la solicitud.
    </div>
    <a class="btn btn-primary" href="{$cc_history_link|escape:'htmlall':'UTF-8'}">Volver a mis pedidos</a>
  </section>
{/block}
