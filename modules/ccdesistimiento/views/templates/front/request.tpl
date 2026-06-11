{extends file='page.tpl'}

{block name='page_content'}
  <section id="cc-desistimiento-request">
    <h1>{$cc_t.request_title|escape:'htmlall':'UTF-8'}</h1>

    {if isset($errors) && $errors}
      {foreach from=$errors item=error}
        <div class="alert alert-danger">{$error|escape:'htmlall':'UTF-8'}</div>
      {/foreach}
    {/if}

    {if isset($cc_order) && $cc_order}
      <p>{$cc_t.order_label|escape:'htmlall':'UTF-8'}: <strong>{$cc_order->reference|escape:'htmlall':'UTF-8'}</strong></p>
      <p>{$cc_t.customer_label|escape:'htmlall':'UTF-8'}: <strong>{$cc_customer->firstname|escape:'htmlall':'UTF-8'} {$cc_customer->lastname|escape:'htmlall':'UTF-8'}</strong></p>

      <div class="alert alert-info">
        {$cc_t.request_info|escape:'htmlall':'UTF-8'}
      </div>

      <form method="post" action="{$cc_action|escape:'htmlall':'UTF-8'}">
        <input type="hidden" name="token" value="{Tools::getToken(false)}">

        <h2 class="h4">{$cc_t.affected_products|escape:'htmlall':'UTF-8'}</h2>
        {foreach from=$cc_products item=product}
          <div class="form-check">
            <label>
              <input type="checkbox" name="products[]" value="{$product.product_id|intval}-{$product.product_attribute_id|intval}" checked>
              {$product.product_name|escape:'htmlall':'UTF-8'} x {$product.product_quantity|intval}
            </label>
          </div>
        {/foreach}

        <div class="form-group mt-3">
          <label for="comment">{$cc_t.optional_comments|escape:'htmlall':'UTF-8'}</label>
          <textarea id="comment" name="comment" class="form-control" rows="4"></textarea>
        </div>

        <div class="alert alert-warning mt-3">
          {$cc_t.return_warning|escape:'htmlall':'UTF-8'}
        </div>

        <button type="submit" name="submitCcDesistimiento" class="btn btn-dark">
          {$cc_t.confirm_withdrawal|escape:'htmlall':'UTF-8'}
        </button>
        <a href="{$urls.pages.history|escape:'htmlall':'UTF-8'}">
          <button  class="btn btn-primary">
            {$cc_t.back_to_orders|escape:'htmlall':'UTF-8'}
          </button>
        </a>
      </form>
    {/if}
  </section>
{/block}
