{extends file='page.tpl'}

{block name='page_content'}
  <section id="cc-desistimiento-success">
    <h1>{$cc_t.success_title|escape:'htmlall':'UTF-8'}</h1>
    <div class="alert alert-success">
      {$cc_t.success_message|escape:'htmlall':'UTF-8'}
    </div>
    <a href="{$cc_history_link|escape:'htmlall':'UTF-8'}">
      <button class="btn btn-primary">
        {$cc_t.back_to_orders|escape:'htmlall':'UTF-8'}
      </button>    
    </a>
  </section>
{/block}
