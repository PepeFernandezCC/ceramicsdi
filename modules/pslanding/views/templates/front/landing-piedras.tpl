{extends file='page.tpl'}
{assign var="landing_title" value=$landing.title}
{if $landing.hero_title}
  {assign var="landing_title" value=$landing.hero_title}
{/if}

{assign var="end_title" value=$landing.title}
{if $landing.hero2_title}
  {assign var="end_title" value=$landing.hero2_title}
{/if}
{block name='page_content'}
<div id="landing-piedras">
  <div id="landing-template" class="landing landing-default">

    <div id="landing-slider" class="block-landing landing-hero {if $landing.hero_media_type == 'video'} has-video{/if}"
        style="border: 0; 
        {if $landing.hero_media_type == 'image' && $landing.hero_media_url}
          background-image:url('{$landing.hero_media_url|escape:'html':'UTF-8'}')
        {/if}">

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

    <div class="block-landing" style="border-top:0">
      <div class="carousel-landing-block">
        <div class="carousel-landing-title">
          <h2>
            {l s='El material define la piscina' d='Shop.Theme.Catalog'} 
          </h2>
        </div>
        <div class="carousel-landing-item">

          {if $landing_slides|@count}
            <section id="carousel-landings" class="mb-3 carousel-materials-category">
              <div class="container-fluid text-center px-0">
                <div id="recipeCarousel" class="row mx-auto my-auto justify-content-center">
                  <div class="owl-carousel inspiration-owl">
                    {foreach from=$landing_slides item=productItem}
                      {if $productItem.slot == "carousel_2"}
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

                        <a class="landing-carousel-slide" href="{$productItem.product_url|escape:'html':'UTF-8'}">
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

    <div id="landing-stone-green-block" class="block-landing">
      <div class="presentation-landing">
        <div class="presentation-landing-info">

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

        </div>
      </div>
    </div>

    <div class="block-landing">

      <div class="carousel-landing-title">
        <h2>
          {l s='Elegir el formato también es diseño' d='Shop.Theme.Catalog'} 
        </h2>
      </div>
      <div class="presentation-landing-double">

        <div class="presentation-block-left">
          <div class="block-double-image">

            {if $landing.block3_media_type == 'video' && $landing.block3_media_url}
              <video class="landing-media"
                    src="{$landing.block3_media_url|escape:'html':'UTF-8'}"
                    playsinline
                    muted
                    loop
                    autoplay
                    preload="auto">
              </video>
            {elseif $landing.block3_media_type == 'image' && $landing.block3_media_url}
              <img src="{$landing.block3_media_url|escape:'html':'UTF-8'}" alt="landing image presentation">
            {/if}

          </div>
          <div class="block-double-data">

            <h2>
              {if $landing.block3_title}
                {$landing.block3_title|escape:'html'}
              {else}
                {$landing.title|escape:'html'}
              {/if}
            </h2>
            <div class="block-double-text">
              {if $landing.block3_text}
                  {$landing.block3_text nofilter}
              {/if}
            </div>
          </div>

        </div>

        <div class="presentation-block-right">
          <div class="block-double-image">

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
          <div class="block-double-data">

            <h2>
              {if $landing.block4_title}
                {$landing.block4_title|escape:'html'}
              {else}
                {$landing.title|escape:'html'}
              {/if}
            </h2>
            <div class="block-double-text">
              {if $landing.block4_text}
                  {$landing.block4_text nofilter}
              {/if}
            </div>
          </div>

        </div>
        
      </div>
    </div>
    <div class="block-landing">

      <div class="carousel-landing-title">
        <h2>
          {l s='Explora otras piedras naturales' d='Shop.Theme.Catalog'} 
        </h2>
      </div>

      <div class="collage-landing-block">
        <div class="collage-landing-item">

          {if $landing_slides|@count}
            <section id="collage-landings">
              <div id="collageBox-landing" class="row">

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

                    <a class="landing-collage-slide" href="{$productItem.product_url|escape:'html':'UTF-8'}">
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
            </section>
          {/if}

        </div>
      </div>

      <div class="landing-piedras-category-button">
        <a href="{$landing.stones_category_url}" class="btn btn-landing-piedras"> {l s='Ver todas las piedras naturales' d='Shop.Theme.Catalog'} </a>
      </div>


    </div>


    <div class="end-hero block-landing landing-hero {if $landing.hero2_media_type == 'video'} has-video{/if}"
        {if $landing.hero2_media_type == 'image' && $landing.hero2_media_url}
          style="background-image:url('{$landing.hero2_media_url|escape:'html':'UTF-8'}')"
        {/if}>

      {if $landing.hero2_media_type == 'video' && $landing.hero2_media_url}
        <video class="landing-hero-video"
              src="{$landing.hero2_media_url|escape:'html':'UTF-8'}"
              autoplay
              muted
              loop
              playsinline
              preload="auto">
        </video>
      {/if}

      <div id="end-hero-slide-landing" class="slide-landing">
        <div class="end-slide-title">
            {$end_title nofilter}
            {if $landing.hero2_button}
              <p class="end-slide-button">
                  <a href="{$landing.hero2_product_url}" class="btn btn-landing-piedras"> {$landing.hero2_button} </a>
              </p>
            {/if}
        </div>
      </div>
      
    </div>

  </div>
</div>
{/block}

 