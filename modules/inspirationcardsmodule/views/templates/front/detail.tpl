{extends file='page.tpl'}

{block name='page_title'}{/block}

{block name='page_content'}
<div class="inspiration-detail">
  <div class="inspiration-detail__container">

    <div class="inspiration-detail__top">
      <a href="{$back_url|escape:'html':'UTF-8'}" class="inspiration-detail__back">
        &#8249; {l s='Volver' d='Shop.Themes.Actions'}
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
          {l s='Productos en este espacio' d='Modules.Inspirationcardsmodule.Shop'}
        </h2>

        {if !empty($related_products)}
          <div class="inspiration-detail__products">
            {foreach from=$related_products item=product}
              <article class="inspiration-product">
                <div class="inspiration-product__image-wrap">
                  <img
                    src="{$product.image|escape:'html':'UTF-8'}"
                    alt="{$product.name|escape:'html':'UTF-8'}"
                    class="inspiration-product__image"
                  >
                </div>

                <div class="inspiration-product__content">
                  {if !empty($product.category_name)}
                    <div class="inspiration-product__category">
                      {$product.category_name|escape:'html':'UTF-8'}
                    </div>
                  {/if}

                  {if !empty($product.reference)}
                    <div class="inspiration-product__reference">
                      {$product.reference|escape:'html':'UTF-8'}
                    </div>
                  {/if}
                    <div>
                        <h3 class="inspiration-product__name">
                            {$product.name|escape:'html':'UTF-8'}
                        </h3>
                    </div>


                  {if !empty($product.dimensions)}
                    <div class="inspiration-product__dimensions">
                      {$product.dimensions|escape:'html':'UTF-8'}
                    </div>
                  {/if}

                  {if !empty($product.url)}
                    <div>
                        <a href="{$product.url|escape:'html':'UTF-8'}" class="inspiration-product__button">
                            {l s='Añadir muestra' d='Modules.Inspirationcardsmodule.Shop'}
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
          {l s='Más cocinas.' d='Modules.Inspirationcardsmodule.Shop'}
        </h2>

        <div class="inspiration-detail__more-row">
          <button type="button" class="inspiration-detail__arrow inspiration-detail__arrow--prev">
            &#8249;
          </button>

          <div class="inspiration-detail__more-grid">
            {foreach from=$more_inspirations item=item}
              <a href="{$item.url|escape:'html':'UTF-8'}" class="inspiration-detail__more-item">
                <img
                  src="{$urls.base_url}modules/inspirationcardsmodule/uploads/{$item.image|escape:'html':'UTF-8'}"
                  alt="{$item.name|escape:'html':'UTF-8'}"
                  class="inspiration-detail__more-image"
                >
              </a>
            {/foreach}
          </div>

          <button type="button" class="inspiration-detail__arrow inspiration-detail__arrow--next">
            &#8250;
          </button>
        </div>
      </div>
    {/if}

  </div>
</div>
{/block}