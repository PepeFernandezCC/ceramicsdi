<div class="inspiration-page">
  <div class="inspiration-top-filters">
    <div class="filter-group">
      <div class="filter-title">Elige un espacio.</div>
      <div class="filter-options filter-options--spaces">
        {foreach from=$espacios item=espacio}
          <button type="button" class="filter-card">
            <span class="filter-card__label">{$espacio.name|escape:'html':'UTF-8'}</span>
          </button>
        {/foreach}
      </div>
    </div>

    <div class="filter-group">
      <div class="filter-title">Filtra por uso o idea.</div>
      <div class="filter-options filter-options--uses">
        {foreach from=$usos item=uso}
          <button type="button" class="filter-card">
            <span class="filter-card__label">{$uso.name|escape:'html':'UTF-8'}</span>
          </button>
        {/foreach}
      </div>
    </div>
  </div>

  <div class="inspiration-attribute-filters">
    <button type="button" class="attribute-filter">ASPECTO</button>
    <button type="button" class="attribute-filter">COLOR</button>
    <button type="button" class="attribute-filter">TAMAÑO</button>
    <button type="button" class="attribute-filter">ESTILO</button>
  </div>

  <div class="inspiration-grid">
    {foreach from=$inspirations item=inspiration}
      <article class="inspiration-card">
        {if $inspiration.image}
          <img
            src="{$urls.base_url}modules/inspirationcardsmodule/uploads/{$inspiration.image|escape:'html':'UTF-8'}"
            alt="{$inspiration.name|escape:'html':'UTF-8'}"
            class="inspiration-card__image"
          >
        {/if}
      </article>
    {/foreach}
  </div>

  <div class="inspiration-more">
    <button type="button" class="inspiration-more__btn">VER MÁS</button>
  </div>
</div>