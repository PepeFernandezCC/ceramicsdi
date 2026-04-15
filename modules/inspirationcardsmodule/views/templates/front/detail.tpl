{extends file='page.tpl'}

{block name='page_title'}{/block}

{block name='page_content'}
<div class="inspiration-detail">
  <div class="inspiration-detail__container">

    <div class="inspiration-detail__top">
      <a href="{$back_url|escape:'html':'UTF-8'}" class="inspiration-detail__back">
        &#8249; {l s='Volver' d='Shop.Theme.Actions'}
      </a>
    </div>

    <div class="inspiration-detail__hero">
      <div class="inspiration-detail__image-col">
        <img
          src="{$urls.base_url}modules/inspirationcardsmodule/uploads/{$inspiration.image|escape:'html':'UTF-8'}"
          alt="{$inspiration.name|escape:'html':'UTF-8'}"
          class="inspiration-detail__main-image"
        >
      </div>

      <div class="inspiration-detail__products-col">
        <h2 class="inspiration-detail__title">
          {l s='Productos en este espacio' d='Shop.Theme.Catalog'}
        </h2>

        {if !empty($floor_related_products)}
          <div class="inspiration-detail__products floor_products">
            {foreach from=$floor_related_products item=product}
              <article class="inspiration-product">
                <div class="inspiration-product__image-wrap">
                  <img
                    src="{$product.image|escape:'html':'UTF-8'}"
                    alt="{$product.name|escape:'html':'UTF-8'}"
                    class="inspiration-product__image"
                  >
                </div>

                <div class="inspiration-product__content">
                 
                    <div class="inspiration-product__category">
                      {l s='Suelo' d='Shop.Theme.Catalog'}
                    </div>
                  
                    <div>

                      {if !empty($product.reference)}
                        <div class="inspiration-product__reference">
                          {$product.reference|escape:'html':'UTF-8'}
                        </div>
                      {/if}

                      <h3 class="inspiration-product__name">
                          {$product.name|escape:'html':'UTF-8'}
                      </h3>

                      {if !empty($product.dimensions)}
                        <div class="inspiration-product__dimensions">
                          {$product.dimensions|escape:'html':'UTF-8'}
                        </div>
                      {/if}

                    </div>

                  {if !empty($product.url)}
                    <div>
                        <a href="{$product.url|escape:'html':'UTF-8'}" class="inspiration-product__button" target="_BLANK">
                            {l s='Ver Producto' d='Shop.Theme.Actions'}
                        </a>
                    </div>
                  {/if}
                </div>
              </article>
            {/foreach}
          </div>
        {/if}
        
        {if !empty($wall_related_products)}
          <div class="inspiration-detail__products wall_products">
            {foreach from=$wall_related_products item=product}
              <article class="inspiration-product">
                <div class="inspiration-product__image-wrap">
                  <img
                    src="{$product.image|escape:'html':'UTF-8'}"
                    alt="{$product.name|escape:'html':'UTF-8'}"
                    class="inspiration-product__image"
                  >
                </div>

                <div class="inspiration-product__content">
                 
                    <div class="inspiration-product__category">
                      {l s='Pared' d='Shop.Theme.Catalog'}
                    </div>
                  
                    <div>

                      {if !empty($product.reference)}
                        <div class="inspiration-product__reference">
                          {$product.reference|escape:'html':'UTF-8'}
                        </div>
                      {/if}

                      <h3 class="inspiration-product__name">
                          {$product.name|escape:'html':'UTF-8'}
                      </h3>

                      {if !empty($product.dimensions)}
                        <div class="inspiration-product__dimensions">
                          {$product.dimensions|escape:'html':'UTF-8'}
                        </div>
                      {/if}

                    </div>

                  {if !empty($product.url)}
                    <div>
                        <a href="{$product.url|escape:'html':'UTF-8'}" class="inspiration-product__button" target="_BLANK">
                            {l s='Ver Producto' d='Shop.Theme.Actions'}
                        </a>
                    </div>
                  {/if}
                </div>
              </article>
            {/foreach}
          </div>
        {/if} 

        {if !empty($both_related_products)}
          <div class="inspiration-detail__products both_products">
            {foreach from=$both_related_products item=product}
              <article class="inspiration-product">
                <div class="inspiration-product__image-wrap">
                  <img
                    src="{$product.image|escape:'html':'UTF-8'}"
                    alt="{$product.name|escape:'html':'UTF-8'}"
                    class="inspiration-product__image"
                  >
                </div>

                <div class="inspiration-product__content">
                 
                    <div class="inspiration-product__category">
                      {l s='Suelo y Pared' d='Shop.Theme.Catalog'}
                    </div>
                  
                    <div>

                      {if !empty($product.reference)}
                        <div class="inspiration-product__reference">
                          {$product.reference|escape:'html':'UTF-8'}
                        </div>
                      {/if}

                      <h3 class="inspiration-product__name">
                          {$product.name|escape:'html':'UTF-8'}
                      </h3>

                      {if !empty($product.dimensions)}
                        <div class="inspiration-product__dimensions">
                          {$product.dimensions|escape:'html':'UTF-8'}
                        </div>
                      {/if}

                    </div>

                  {if !empty($product.url)}
                    <div>
                        <a href="{$product.url|escape:'html':'UTF-8'}" class="inspiration-product__button" target="_BLANK">
                            {l s='Ver Producto' d='Shop.Theme.Actions'}
                        </a>
                    </div>
                  {/if}
                </div>
              </article>
            {/foreach}
          </div>
        {/if} 

      </div>
    </div>

    {if !empty($more_inspirations)}
      <div class="inspiration-detail__more">
        <h2 class="inspiration-detail__more-title">

          {if $related_inspiration == 'Cocina'}
            {l s='Más cocinas.' d='Shop.Theme.Catalog'}
          {/if}

          {if $related_inspiration == 'Baño'}
            {l s='Más Baños.' d='Shop.Theme.Catalog'}
          {/if}

          {if $related_inspiration == 'Salón'}
            {l s='Más Salones.' d='Shop.Theme.Catalog'}
          {/if}

          {if $related_inspiration == 'Dormitorio'}
            {l s='Más Dormitorios.' d='Shop.Theme.Catalog'}
          {/if}

          {if $related_inspiration == 'Exterior'}
            {l s='Más Exteriores.' d='Shop.Theme.Catalog'}
          {/if}

          {if $related_inspiration == 'Piscina'}
            {l s='Más Piscinas.' d='Shop.Theme.Catalog'}
          {/if}

          {if $related_inspiration == 'Suelo'}
            {l s='Más Suelos.' d='Shop.Theme.Catalog'}
          {/if}

          {if $related_inspiration == 'Pared'}
            {l s='Más Paredes.' d='Shop.Theme.Catalog'}
          {/if}

          {if $related_inspiration == 'Moodboards'}
            {l s='Más Moodboards.' d='Shop.Theme.Catalog'}
          {/if}

        </h2>
        <div class="carousel-landing-item">
          <section id="carousel-landings" class="mb-3 carousel-materials-category">
            <div class="container-fluid text-center px-0">
              <div id="recipeCarousel" class="row mx-auto my-auto justify-content-center">
                <div class="owl-carousel inspiration-owl">
                  {foreach from=$more_inspirations item=item}
                    <a href="{$item.url|escape:'html':'UTF-8'}" class="inspiration-detail__more-item">
                      <img
                        src="{$urls.base_url}modules/inspirationcardsmodule/uploads/{$item.image|escape:'html':'UTF-8'}"
                        alt="{$item.name|escape:'html':'UTF-8'}" class="inspiration-detail__more-image" >
                    </a>
                  {/foreach}
                </div>
              </div>
            </div>
          </section>
      </div>
    {/if}

  </div>
</div>
{/block}