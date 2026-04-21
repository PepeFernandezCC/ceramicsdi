{extends file='page.tpl'}

{block name='page_title'}{/block}

{block name='page_content'}
<div class="inspirations-page" data-ajax-url="{$filter_ajax_url|escape:'html':'UTF-8'}">

  <section class="insp-top">
    <div class="insp-top__group">
      <p class="insp-top__title">Elige un espacio.</p>
      <div class="insp-top__cards">

        <div class="insp-card" data-group="space" data-value="salon">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/salon.webp" alt="Salón">
          </div>
          <div class="insp-card__label">SALÓN</div>
        </div>

        <div class="insp-card" data-group="space" data-value="cocina">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/cocina.webp" alt="Cocina">
          </div>
          <div class="insp-card__label">COCINA</div>
        </div>

        <div class="insp-card" data-group="space" data-value="bano">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/bano.webp" alt="Baño">
          </div>
          <div class="insp-card__label">BAÑO</div>
        </div>

        <div class="insp-card" data-group="space" data-value="dormitorio">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/dormitorio.webp" alt="Dormitorio">
          </div>
          <div class="insp-card__label">DORMITORIO</div>
        </div>

        <div class="insp-card" data-group="space" data-value="exterior">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/exterior.webp" alt="Exterior">
          </div>
          <div class="insp-card__label">EXTERIOR</div>
        </div>

        <div class="insp-card" data-group="space" data-value="piscina">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/piscina.webp" alt="Piscina">
          </div>
          <div class="insp-card__label">PISCINA</div>
        </div>

      </div>
    </div>

    <div class="insp-top__group insp-top__group--usage">
      <p class="insp-top__title">Filtra por uso o idea.</p>
      <div class="insp-top__cards">

        <div class="insp-card" data-group="usage" data-value="suelo">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/piscina.webp" alt="suelo">
          </div>
          <div class="insp-card__label">SUELO</div>
        </div>

        <div class="insp-card" data-group="usage" data-value="pared">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/piscina.webp" alt="pared">
          </div>
          <div class="insp-card__label">PARED</div>
        </div>

        <div class="insp-card" data-group="usage" data-value="moodboards">
          <div class="insp-card__image" role="button">
            <img src="{$urls.base_url}modules/inspirationcardsmodule/views/images/piscina.webp" alt="moodboards">
          </div>
          <div class="insp-card__label">MOODBOARDS</div>
        </div>

      </div>
    </div>
  </section>

  <section class="insp-filters">
    <div class="insp-filter-tabs">
      <div class="insp-filter-tab" role="button" data-tab="aspecto">
        <span>ASPECTO</span>
      </div>
      <div class="insp-filter-tab" role="button" data-tab="color">
        <span>COLOR</span>
      </div>
      <div class="insp-filter-tab" role="button" data-tab="tamano">
        <span>TAMAÑO</span>
      </div>
      <div class="insp-filter-tab" role="button" data-tab="estilo">
        <span>ESTILO</span>
      </div>
      <div class="fill-filter"></div>
    </div>

    <div class="insp-filter-panels">
      <div class="insp-filter-panel" data-panel="aspecto">
        <div class="insp-filter-values insp-filter-values--text">
          <div role="button" data-group="aspecto" data-value="barro" data-label="Barro">Barro</div>
          <div role="button" data-group="aspecto" data-value="cemento" data-label="Cemento">Cemento</div>
          <div role="button" data-group="aspecto" data-value="hidraulico" data-label="Hidráulico">Hidráulico</div>
          <div role="button" data-group="aspecto" data-value="madera" data-label="Madera">Madera</div>
          <div role="button" data-group="aspecto" data-value="marmol" data-label="Mármol">Mármol</div>
          <div role="button" data-group="aspecto" data-value="metro" data-label="Metro">Metro</div>
          <div role="button" data-group="aspecto" data-value="monocolor" data-label="Monocolor">Monocolor</div>
          <div role="button" data-group="aspecto" data-value="piedra" data-label="Piedra">Piedra</div>
          <div role="button" data-group="aspecto" data-value="pizarra" data-label="Pizarra">Pizarra</div>
          <div role="button" data-group="aspecto" data-value="terrazo" data-label="Terrazo">Terrazo</div>
          <div role="button" data-group="aspecto" data-value="zellige" data-label="Zellige">Zellige</div>
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
          <div role="button" data-group="tamano" data-value="Pequeño (hasta 30 cm)" data-label="Pequeño (hasta 30cm)">Pequeño (hasta 30cm)</div>
          <div role="button" data-group="tamano" data-value="Mediano (hasta 60 cm)" data-label="Mediano (hasta 60cm)">Mediano (hasta 60cm)</div>
          <div role="button" data-group="tamano" data-value="Grande (hasta 120 cm)" data-label="Grande (hasta 120cm)">Grande (hasta 120cm)</div>
          <div role="button" data-group="tamano" data-value="Mosaico enmallado" data-label="Mosaico enmallado">Mosaico enmallado</div>
        </div>
      </div>

      <div class="insp-filter-panel" data-panel="estilo">
        <div class="insp-filter-values insp-filter-values--text">
          <div role="button" data-group="estilo" data-value="minimalista" data-label="Minimalista">Minimalista</div>
          <div role="button" data-group="estilo" data-value="industrial" data-label="Industrial">Industrial</div>
          <div role="button" data-group="estilo" data-value="vintage" data-label="Vintage">Vintage</div>
          <div role="button" data-group="estilo" data-value="rustico" data-label="Rústico">Rústico</div>
          <div role="button" data-group="estilo" data-value="nordico" data-label="Nórdico">Nórdico</div>
          <div role="button" data-group="estilo" data-value="mediterraneo" data-label="Mediterráneo">Mediterráneo</div>
          <div role="button" data-group="estilo" data-value="wabisabi" data-label="Wabi-Sabi">Wabi-Sabi</div>
          <div role="button" data-group="estilo" data-value="contemporaneo" data-label="Contemporáneo">Contemporáneo</div>
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
      VER MÁS
    </div>
  </div>

</div>
{/block}