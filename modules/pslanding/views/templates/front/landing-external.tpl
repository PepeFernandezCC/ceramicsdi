{extends file='page.tpl'}
{block name='page_content'}
  <div id="landing-template" class="landing landing-external">

    {if $landing.external_url}
      <div class="landing-external-iframe-wrapper">
        <iframe
          class="landing-external-iframe"
          src="{$landing.external_url|escape:'html':'UTF-8'}"
          title="{$landing.title|escape:'html':'UTF-8'}"
          loading="lazy"
          allowfullscreen>
        </iframe>
      </div>
    {else}
      {* Sin URL configurada para este idioma *}
    {/if}

  </div>
{/block}
