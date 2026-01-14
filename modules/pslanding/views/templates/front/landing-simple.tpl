{extends file='page.tpl'}

{block name='page_content'}
  <div id="landing-template" class="landing landing-default">

    <div id="landing-slider"
        class="block-landing landing-hero
        {if $landing.hero_media_type == 'video'} has-video{/if}"
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
      <div class="presentation-landing-reverse">
        <div class="presentation-landing-reverse-info">
          <div class="presentation-landing-title">
            <h2>
              {if $landing.block3_title}
                {$landing.block3_title|escape:'html'}
              {else}
                {$landing.title|escape:'html'}
              {/if}
            </h2>
            <div class="presentation-landing-data">
              {if $landing.block3_text}
                  {$landing.block3_text nofilter}
              {/if}
            </div>
          </div>

          <div class="presentation-landing-reverse-image">
            {if $landing.block3_media_type == 'video' && $landing.block3_media_url}
              <video class="landing-media-reverse"
                    src="{$landing.block3_media_url|escape:'html':'UTF-8'}"
                    playsinline
                    muted
                    loop
                    autoplay
                    
                    preload="auto">
              </video>
            {elseif $landing.block3_media_type == 'image' && $landing.block3_media_url}
              <img src="{$landing.block3_media_url|escape:'html':'UTF-8'}" alt="landing image presentation block 3">
            {/if}

          </div>
        </div>
      </div>

      <div class="collage-landing-block">

        <div class="collage-landing-item">

          {if $landing_slides|@count}
            <section id="collage-landings" class="mb-3">
          
                <div id="collageBox-landing" class="row">
                 
                    {foreach from=$landing_slides item=productItem}
                      <a class="landing-collage-slide" href="{$productItem.product_url|escape:'html':'UTF-8'}">
                         <img class="collage-landing-img" src="{$productItem.image_url|escape:'html':'UTF-8'}" alt="{$landing.title|escape:'html':'UTF-8'}">
                      </a>
                    {/foreach}
 
                </div>

            </section>
          {/if}

        </div>
      </div>

    </div>

    <div class="block-landing">
      <div class="presentation-landing">
        <div class="presentation-landing-info">
          <div class="presentation-landing-title">
            <h2>
              {if $landing.block4_title}
                {$landing.block4_title|escape:'html'}
              {else}
                {$landing.title|escape:'html'}
              {/if}
            </h2>
            <div class="presentation-landing-data">
              {if $landing.block4_text}
                  {$landing.block4_text nofilter}
              {/if}
            </div>
          </div>

          <div class="presentation-landing-image">
            {if $landing.block4_media_type == 'video' && $landing.block4_media_url}
              <video class="landing-media"
                    src="{$landing.block4_media_url|escape:'html':'UTF-8'}"
                    playsinline
                    muted
                    loop
                    autoplay
                    preload="auto">
              </video>
            {elseif $landing.block4_media_type == 'image' && $landing.block4_media_url}
              <img src="{$landing.block4_media_url|escape:'html':'UTF-8'}" alt="landing image presentation">
            {/if}

          </div>
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
