{extends file='page.tpl'}

{block name='page_title'}{/block}

{block name='page_content'}
<div class="inspirations-page" data-ajax-url="{$filter_ajax_url|escape:'html':'UTF-8'}">

  <section class="insp-top">
    <div class="insp-top__group">
      <p class="insp-top__title">{l s='Elige un espacio' d='Shop.Theme.Catalog'}.</p>
      <div class="insp-top__cards">

        <div class="insp-card" data-group="space" data-value="salon">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/salon.webp" alt="Salón">
          </div>
          <div class="insp-card__label">{l s='SALÓN' d='Shop.Theme.Catalog'}</div>
        </div>

        <div class="insp-card" data-group="space" data-value="cocina">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/cocina.webp" alt="Cocina">
          </div>
          <div class="insp-card__label">{l s='COCINA' d='Shop.Theme.Catalog'}</div>
        </div>

        <div class="insp-card" data-group="space" data-value="bano">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/bano.webp" alt="Baño">
          </div>
          <div class="insp-card__label">{l s='BAÑO' d='Shop.Theme.Catalog'}</div>
        </div>

        <div class="insp-card" data-group="space" data-value="dormitorio">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/dormitorio.webp" alt="Dormitorio">
          </div>
          <div class="insp-card__label">{l s='DORMITORIO' d='Shop.Theme.Catalog'}</div>
        </div>

        <div class="insp-card" data-group="space" data-value="exterior">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/exterior.webp" alt="Exterior">
          </div>
          <div class="insp-card__label">{l s='EXTERIOR' d='Shop.Theme.Catalog'}</div>
        </div>

        <div class="insp-card" data-group="space" data-value="piscina">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/piscina.webp" alt="Piscina">
          </div>
          <div class="insp-card__label">{l s='PISCINA' d='Shop.Theme.Catalog'}</div>
        </div>

      </div>
    </div>

    <div class="insp-top__group insp-top__group--usage">
      <p class="insp-top__title">{l s='Filtra por uso o idea' d='Shop.Theme.Catalog'}.</p>
      <div class="insp-top__cards">

        <div class="insp-card" data-group="space" data-value="suelo">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/suelo.webp" alt="suelo">
          </div>
          <div class="insp-card__label">{l s='SUELO' d='Shop.Theme.Catalog'}</div>
        </div>

        <div class="insp-card" data-group="space" data-value="pared">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/pared.webp" alt="pared">
          </div>
          <div class="insp-card__label">{l s='PARED' d='Shop.Theme.Catalog'}</div>
        </div>

        <div class="insp-card" data-group="space" data-value="moodboards">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/moodboard.webp" alt="moodboards">
          </div>
          <div class="insp-card__label">{l s='MOODBOARDS' d='Shop.Theme.Catalog'}</div>
        </div>

      </div>
    </div>
  </section>

  <section class="insp-filters">
    <div class="insp-filter-tabs">
      <div class="insp-filter-tab" role="button" data-tab="aspecto">
        <span>{l s='ASPECTO' d='Shop.Theme.Catalog'}</span>
      </div>
      <div class="insp-filter-tab" role="button" data-tab="color">
        <span>{l s='COLOR' d='Shop.Theme.Catalog'}</span>
      </div>
      <div class="insp-filter-tab" role="button" data-tab="tamano">
        <span>{l s='TAMAÑO' d='Shop.Theme.Catalog'}</span>
      </div>
      <div class="insp-filter-tab" role="button" data-tab="estilo">
        <span>{l s='ESTILO' d='Shop.Theme.Catalog'}</span>
      </div>
      <div class="insp-filter-tab" role="button" data-tab="producto">
        <span>{l s='PRODUCTO' d='Shop.Themes.Catalog'}</span>
      </div>
      <div class="fill-filter"></div>
    </div>

    <div class="insp-filter-panels">
      <div class="insp-filter-panel" data-panel="aspecto">
        <div class="insp-filter-values insp-filter-values--text">
          <div role="button" data-group="aspecto" data-value="barro" data-label="Barro">{l s='Barro' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="cemento" data-label="Cemento">{l s='Cemento' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="hidraulico" data-label="Hidráulico">{l s='Hidráulico' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="madera" data-label="Madera">{l s='Madera' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="marmol" data-label="Mármol">{l s='Mármol' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="metro" data-label="Metro">{l s='Metro' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="monocolor" data-label="Monocolor">{l s='Monocolor' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="piedra" data-label="Piedra">{l s='Piedra' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="pizarra" data-label="Pizarra">{l s='Pizarra' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="terrazo" data-label="Terrazo">{l s='Terrazo' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="aspecto" data-value="zellige" data-label="Zellige">{l s='Zellige' d='Shop.Theme.Catalog'}</div>
        </div>
      </div>

      <div class="insp-filter-panel" data-panel="color">
        <div class="insp-filter-values insp-filter-values--color">
          <div class="insp-color" role="button" data-group="color" data-value="blanco" data-label="Blanco">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_BLANCO.webp" alt="Blanco">
          </div>

          <div class="insp-color" role="button" data-group="color" data-value="gris" data-label="Gris">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_GRIS.webp" alt="Gris">
          </div>

          <div class="insp-color" role="button" data-group="color" data-value="beige" data-label="Beige">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_BEIGE.webp" alt="Beige">
          </div>

          <div class="insp-color" role="button" data-group="color" data-value="marron" data-label="Marrón">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_MARRON.webp" alt="Marrón">
          </div>

          <div class="insp-color" role="button" data-group="color" data-value="amarillo" data-label="Amarillo">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_AMARILLO.webp" alt="Amarillo">
          </div>

          <div class="insp-color" role="button" data-group="color" data-value="rojo" data-label="Rojo">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_ROJO.webp" alt="Rojo">
          </div>

          <div class="insp-color" role="button" data-group="color" data-value="verde" data-label="Verde">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_VERDE.webp" alt="Verde">
          </div>

          <div class="insp-color" role="button" data-group="color" data-value="azul" data-label="Azul">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_AZUL.webp" alt="Azul">
          </div>

          <div class="insp-color" role="button" data-group="color" data-value="negro" data-label="Negro">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_NEGRO.webp" alt="Negro">
          </div>

          <div class="insp-color" role="button" data-group="color" data-value="multicolor" data-label="Multicolor">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/COLOR_MULTICOLOR.webp" alt="Multicolor">
          </div>
        </div>
      </div>

      <div class="insp-filter-panel" data-panel="tamano">
        <div class="insp-filter-values insp-filter-values--text">
          <div role="button" data-group="tamano" data-value="Pequeño (hasta 30 cm)" data-label="Pequeño (hasta 30cm)">{l s='Pequeño (hasta 30cm)' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="tamano" data-value="Mediano (hasta 60 cm)" data-label="Mediano (hasta 60cm)">{l s='Mediano (hasta 60cm)' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="tamano" data-value="Grande (hasta 120 cm)" data-label="Grande (hasta 120cm)">{l s='Grande (hasta 120cm)' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="tamano" data-value="Mosaico enmallado" data-label="Mosaico enmallado">{l s='Mosaico enmallado' d='Shop.Theme.Catalog'}</div>
        </div>
      </div>

      <div class="insp-filter-panel" data-panel="estilo">
        <div class="insp-filter-values insp-filter-values--text">
          <div role="button" data-group="estilo" data-value="minimalista" data-label="Minimalista">{l s='Minimalista' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="estilo" data-value="industrial" data-label="Industrial">{l s='Industrial' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="estilo" data-value="vintage" data-label="Vintage">{l s='Vintage' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="estilo" data-value="rustico" data-label="Rústico">{l s='Rústico' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="estilo" data-value="nordico" data-label="Nórdico">{l s='Nórdico' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="estilo" data-value="mediterraneo" data-label="Mediterráneo">{l s='Mediterráneo' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="estilo" data-value="contemporaneo" data-label="Contemporáneo">{l s='Contemporáneo' d='Shop.Theme.Catalog'}</div>
        </div>
      </div>

      <div class="insp-filter-panel" data-panel="producto">
        <div class="insp-filter-values insp-filter-values--text">
          <div role="button" data-group="producto" data-value="azulejo" data-label="Azulejo">{l s='Azulejo' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="producto" data-value="piedra" data-label="Piedra">{l s='Piedra' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="producto" data-value="terracota" data-label="Terracota">{l s='Terracota' d='Shop.Theme.Catalog'}</div>
          <div role="button" data-group="producto" data-value="mosaico" data-label="Mosaico de vidrio">{l s='Mosaico de vidrio' d='Shop.Theme.Catalog'}</div>
        </div>
      </div>
    </div>
  </section>

  <section class="insp-active-filters" id="insp-active-filters"></section>

  <section id="insp-grid" class="insp-grid">
    {include file='module:inspirationcardsmodule/views/templates/front/_grid.tpl'}
  </section>

  <div class="insp-more">
    <div
      id="viewMoreButton"
      class="insp-more__btn"
      role="button"
      data-offset="{count($inspirations)}"
      data-limit="{$load_more_step|intval}"
    >
      {l s='VER MÁS' d='Shop.Theme.Catalog'}
    </div>
  </div>

</div>
{/block}