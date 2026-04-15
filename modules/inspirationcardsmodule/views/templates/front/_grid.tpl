{foreach from=$inspirations item=inspiration name=inspLoop}
  <article class="insp-grid__item">
    <a href="{$inspiration.url|escape:'html':'UTF-8'}" class="insp-grid__link">
      <img
        src="{$urls.base_url}modules/inspirationcardsmodule/uploads/{$inspiration.image|escape:'html':'UTF-8'}"
        alt="{$inspiration.name|escape:'html':'UTF-8'}"
        class="insp-grid__image"
        {if $smarty.foreach.inspLoop.iteration > 4}loading="lazy"{/if}
        width="333"
        height="427"
      >
    </a>
  </article>
{/foreach}