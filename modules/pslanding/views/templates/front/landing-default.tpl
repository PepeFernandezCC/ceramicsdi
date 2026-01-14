{extends file='page.tpl'}

{block name='page_content'}
  <div id="landing-template" class="landing landing-default">
    <div class="block-landing">
      <div class="slide-landing">
        <div class="landing-slide-title">
          <h1>
            {if $landing.hero_title}
              {$landing.hero_title|escape:'html'}
            {else}
              {$landing.title|escape:'html'}
            {/if}
          </h1>
        </div>
        <div class="landing-slide-subtitle">
          {if $landing.hero_subtitle}
            <span>
              {$landing.hero_subtitle nofilter}
            </span>
          {/if}
        </div>
      </div>
    </div>

    <div class="block-landing">
      <div class="presentation-landing">
        <div class="presentation-landing-info">
          <div class="presentation-landing-title">
            <h2>
              {if $landing.block2_title}
                {$landing.block2_title|escape:'html'}
              {else}
                {$landing.title|escape:'html'}
              {/if}
            </h2>
            <div class="presentation-landing-data">
              {if $landing.block2_text}
                  {$landing.block2_text nofilter}
              {/if}
            </div>
          </div>

          <div class="presentation-landing-image">

            {if $landing.block2_media_type == 'video' && $landing.block2_media_url}
              <video class="landing-media"
                    src="{$landing.block2_media_url|escape:'html':'UTF-8'}"
                    playsinline
                    muted
                    loop
                    autoplay
                    preload="auto">
              </video>
            {elseif $landing.block2_media_type == 'image' && $landing.block2_media_url}
              <img src="{$landing.block2_media_url|escape:'html':'UTF-8'}" alt="landing image presentation">
            {/if}
          </div>
        </div>
      </div>
    </div>

    <div class="block-landing">
      <div class="feature-landing-block">
        <div class="feature-landing-title">
          <h2>{l s='Features' d='Modules.Pslanding.Shop'}</h2>
        </div>

        <div class="features-landing-list">
          {if $characteristics|count}
            {foreach from=$characteristics item=feature}
              <div class="feature-landing-item">
                <div class="feature-item-title">
                  {$feature.title|escape:'html'}
                </div>
                <div class="feature-item-text">
                  {$feature.text nofilter}
                </div>
              </div>
            {/foreach}
          {else}
            {* opcional: nada si no hay características *}
          {/if}
        </div>
      </div>
    </div>


    <div class="block-landing">
      <div class="carousel-landing-block">
        <div class="carousel-landing-title">
          <h2>
            {l s='Atmosphere' d='Modules.Pslanding.Shop'}
          </h2>
        </div>
        <div class="carousel-landing-item">

          {if $landing_slides|@count}
            <section id="carousel-landings" class="mb-3 carousel-materials-category">
              <div class="container-fluid text-center px-0">
                <div id="recipeCarousel" class="row mx-auto my-auto justify-content-center">
                  <div class="owl-carousel inspiration-owl">
                    {foreach from=$landing_slides item=productItem}
                      <a class="landing-carousel-slide" href="{$productItem.product_url|escape:'html':'UTF-8'}">
                         <img src="{$productItem.image_url|escape:'html':'UTF-8'}" alt="{$landing.title|escape:'html':'UTF-8'}">
                      </a>
                    {/foreach}
                  </div>
                </div>
              </div>
            </section>
          {/if}

        </div>
      </div>
    </div>

  <div id="category" class="block-landing">
    <div id="content-wrapper" class="related-landing-block" style="height: auto">
      <div class="related-landing-title">
        <h2>{l s='related products' d='Modules.Pslanding.Shop'}</h2>
      </div>

      <section id="products" class="related-landing-list">
        <div id="js-product-list" class="landing-related-box">
          {include file="catalog/_partials/productlist.tpl"
            products=$related_products
            cssClass="row"
          }
        </div>
      </section>

      <div class="clearfix"></div>
    </div>
  </div>

    <div class="block-landing">
      <div class="faq-landing-block">
        <div class="faq-title">
          {l s='FAQ' d='Shop.Theme.Global'}
        </div>
        {include file='module:pslanding/views/templates/front/_partials/faq.tpl'}
      </div>
    </div>

  </div>
{/block}
