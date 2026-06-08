{extends file='page.tpl'}

{block name='page_content'}
  <section id="cc-desistimiento-request">
    <h1>Solicitar desistimiento</h1>

    {if isset($errors) && $errors}
      {foreach from=$errors item=error}
        <div class="alert alert-danger">{$error|escape:'htmlall':'UTF-8'}</div>
      {/foreach}
    {/if}

    {if isset($cc_order) && $cc_order}
      <p>Pedido: <strong>{$cc_order->reference|escape:'htmlall':'UTF-8'}</strong></p>
      <p>Cliente: <strong>{$cc_customer->firstname|escape:'htmlall':'UTF-8'} {$cc_customer->lastname|escape:'htmlall':'UTF-8'}</strong></p>

      <div class="alert alert-info">
        Esta solicitud solo comunica tu decision de ejercer el derecho de desistimiento. Ceramic Connection revisara la solicitud y el estado del material antes de tramitar el reembolso, si procede.
      </div>

      <form method="post" action="{$cc_action|escape:'htmlall':'UTF-8'}">
        <input type="hidden" name="token" value="{Tools::getToken(false)}">

        <h2 class="h4">Productos afectados</h2>
        {foreach from=$cc_products item=product}
          <div class="form-check">
            <label>
              <input type="checkbox" name="products[]" value="{$product.product_id|intval}-{$product.product_attribute_id|intval}" checked>
              {$product.product_name|escape:'htmlall':'UTF-8'} x {$product.product_quantity|intval}
            </label>
          </div>
        {/foreach}

        <div class="form-group mt-3">
          <label for="comment">Comentarios opcionales</label>
          <textarea id="comment" name="comment" class="form-control" rows="4"></textarea>
        </div>

        <div class="alert alert-warning mt-3">
          La devolucion del material debera hacerse a: <strong>{$cc_return_address|escape:'htmlall':'UTF-8'}</strong>. El coste y gestion del transporte de devolucion corren a cargo del cliente, salvo defecto del producto o error en el envio. Recomendamos paletizar y embalar correctamente el material.
        </div>

        <button type="submit" name="submitCcDesistimiento" class="btn btn-primary">
          Confirmar desistimiento
        </button>
        <a href="{$urls.pages.history|escape:'htmlall':'UTF-8'}" class="btn btn-secondary">Volver a mis pedidos</a>
      </form>
    {/if}
  </section>
{/block}
