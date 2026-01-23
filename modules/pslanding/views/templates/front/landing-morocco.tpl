{extends file='page.tpl'}
{assign var="landing_title" value=$landing.title}
{if $landing.hero_title}
  {assign var="landing_title" value=$landing.hero_title}
{/if}
{block name='page_content'}
  <div id="landing-template" class="landing landing-default">
  
    <div id="landing-slider" class="block-landing landing-hero {if $landing.hero_media_type == 'video'} has-video{/if}"
        {if $landing.hero_media_type == 'image' && $landing.hero_media_url}
          style="background-image:url('{$landing.hero_media_url|escape:'html':'UTF-8'}')"
        {/if}>

      {if $landing.hero_media_type == 'video' && $landing.hero_media_url}
        <video class="landing-hero-video"
              src="{$landing.hero_media_url|escape:'html':'UTF-8'}"
              autoplay
              muted
              loop
              playsinline
              preload="auto">
        </video>
      {/if}

      <div id="ml-slide-title" class="slide-landing">
        <div class="landing-slide-title">
          <h1>
            {$landing_title|escape:'html'}  
          </h1>
        </div>

        <div class="landing-slide-subtitle">
          {if $landing.hero_subtitle}
            <span>{$landing.hero_subtitle nofilter}</span>
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
          <h2>{l s='Features' d='Shop.Theme.Catalog'}</h2>
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
            {l s='Atmosphere' d='Shop.Theme.Catalog'} {$landing_title|escape:'html'}  
          </h2>
        </div>
        <div class="carousel-landing-item">
          {if $landing_slides|@count}
            <section id="carousel-landings" class="mb-3 carousel-materials-category">
              <div class="container-fluid text-center px-0">
                <div id="recipeCarousel" class="row mx-auto my-auto justify-content-center">
                  <div class="owl-carousel inspiration-owl">
                    {foreach from=$landing_slides item=productItem}
                      {if $productItem.slot == "carousel_1"}
                        {* Elegir filename por idioma *}
                          {assign var=slideFilename value=''}

                          {if isset($productItem.images) && isset($productItem.images[$language.id]) && $productItem.images[$language.id]}
                            {assign var=slideFilename value=$productItem.images[$language.id]}
                          {/if}

                          {* Construir URL final *}
                          {assign var=slideImgUrl value=''}

                          {if $slideFilename}
                            {assign var=slideImgUrl value="`$urls.base_url`modules/pslanding/uploads/`$slideFilename`"}
                          {elseif isset($productItem.image_url) && $productItem.image_url}
                            {* fallback si sigues generando image_url en PHP *}
                            {assign var=slideImgUrl value=$productItem.image_url}
                          {/if}

                        <a class="landing-carousel-slide" href="{$productItem.category_url|escape:'html':'UTF-8'}">
                          {if $slideImgUrl}
                            <img
                              class="collage-landing-img"
                              src="{$slideImgUrl|escape:'html':'UTF-8'}"
                              alt="{$landing.title|escape:'html':'UTF-8'}">
                          {/if}
                        </a>
                      {/if}
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
      <div class="related-landing-title" style="padding: 0">
        <h2>{l s='Catalog Collection' d='Shop.Theme.Catalog'}</h2>
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
      <div id="faq-landing-box" class="faq-landing-block">
        <div class="faq-title">
          {l s='FAQ' d='Shop.Theme.Global'}
        </div>
        {include file='module:pslanding/views/templates/front/_partials/faq.tpl'}
      </div>
    </div>

  </div>
{/block}
