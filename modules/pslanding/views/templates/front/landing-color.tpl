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
<div id="landing-color">
  <div id="landing-template" class="landing landing-default">


    <div id="landing-slider" class="{if $landing.hasMobileMedia} display-desktop {/if}block-landing landing-hero {if $landing.hero_media_type == 'video'} has-video{/if}"
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
      </div>
      
    </div>

    {if $landing.hasMobileMedia}
        <div id="landing-slider" class="block-landing landing-hero-mobile display-mobile {if $landing.hero_media_mobile_type == 'video'} has-video{/if}"
          style="border: 0; 
          {if $landing.hero_media_mobile_type == 'image' && $landing.hero_media_mobile_url}
            background-image:url('{$landing.hero_media_mobile_url|escape:'html':'UTF-8'}')
          {/if}">

          {if $landing.hero_media_mobile_type == 'video' && $landing.hero_media_mobile_url}
            <video class="landing-hero-video"
                  src="{$landing.hero_media_mobile_url|escape:'html':'UTF-8'}"
                  autoplay
                  muted
                  loop
                  playsinline
                  preload="auto">
            </video>
          {/if}
        
        <div class="slide-landing">
        </div>
      
      </div>
    {/if}

    <div id="bloque-color-1" class="block-landing" style="padding-top:0">
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

    <div id="bloque-color-2" id="landing-color-blue-block" class="block-landing">
      <div class="presentation-landing">
        <div class="presentation-landing-title">
            <h2>
                {if $landing.block3_title}
                    {$landing.block3_title|escape:'html'}
                {else}
                    {$landing.title|escape:'html'}
                {/if}
            </h2>
        </div>
        <div class="presentation-landing-info">

            <div class="presentation-landing-image">

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

            <div class="presentation-landing-data">
              {if $landing.block3_text}
                  {$landing.block3_text nofilter}
              {/if}
            </div>
          
        </div>
      </div>
    </div>

    <div id="bloque-color-3" class="block-landing">

      <div class="presentation-landing-title">
        <h2>
          {l s='El Color de la junta lo cambia todo' d='Shop.Theme.Catalog'} 
        </h2>
        <p>{l s='El mismo azulejo. Tres maneras de sentirlo.' d='Shop.Theme.Catalog'} </p>
      </div>

      <div class="presentation-landing-triple">

        <div class="w25 presentation-block-left">
          <div class="block-triple-image">

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
          <div class="block-triple-data">

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

        <div class="w25 presentation-block-middle">
          <div class="block-triple-image">

            {if $landing.block5_media_type == 'video' && $landing.block5_media_url}
              <video class="landing-media"
                    src="{$landing.block5_media_url|escape:'html':'UTF-8'}"
                    playsinline
                    muted
                    loop
                    autoplay
                    preload="auto">
              </video>
            {elseif $landing.block5_media_type == 'image' && $landing.block5_media_url}
              <img src="{$landing.block5_media_url|escape:'html':'UTF-8'}" alt="landing image presentation">
            {/if}

          </div>
          <div class="block-triple-data">

            <h2>
              {if $landing.block5_title}
                {$landing.block5_title|escape:'html'}
              {else}
                {$landing.title|escape:'html'}
              {/if}
            </h2>
            <div class="block-double-text">
              {if $landing.block5_text}
                  {$landing.block5_text nofilter}
              {/if}
            </div>
          </div>

        </div>
        
        <div class="w25 presentation-block-right">
          <div class="block-triple-image">

            {if $landing.block6_media_type == 'video' && $landing.block6_media_url}
              <video class="landing-media"
                    src="{$landing.block6_media_url|escape:'html':'UTF-8'}"
                    playsinline
                    muted
                    loop
                    autoplay
                    preload="auto">
              </video>
            {elseif $landing.block6_media_type == 'image' && $landing.block6_media_url}
              <img src="{$landing.block6_media_url|escape:'html':'UTF-8'}" alt="landing image presentation">
            {/if}

          </div>
          <div class="block-triple-data">

            <h2>
              {if $landing.block6_title}
                {$landing.block6_title|escape:'html'}
              {else}
                {$landing.title|escape:'html'}
              {/if}
            </h2>
            <div class="block-double-text">
              {if $landing.block6_text}
                  {$landing.block6_text nofilter}
              {/if}
            </div>
          </div>

        </div>
      </div>
    </div>

    <div id="bloque-color-4" class="block-landing">
      <div class="presentation-landing">
        <div class="presentation-landing-title">
            <h2>
              {if $landing.block7_title}
                {$landing.block7_title|escape:'html'}
              {else}
                {$landing.title|escape:'html'}
              {/if}
            </h2>
        </div>

        <div class="presentation-landing-info">

            <div class="presentation-landing-data">
              {if $landing.block7_text}
                  {$landing.block7_text nofilter}
              {/if}
            </div>
          

          <div class="presentation-landing-image">

            {if $landing.block7_media_type == 'video' && $landing.block7_media_url}
              <video class="landing-media"
                    src="{$landing.block7_media_url|escape:'html':'UTF-8'}"
                    playsinline
                    muted
                    loop
                    autoplay
                    preload="auto">
              </video>
            {elseif $landing.block7_media_type == 'image' && $landing.block2_media_url}
              <img src="{$landing.block7_media_url|escape:'html':'UTF-8'}" alt="landing image presentation">
            {/if}

          </div>
        </div>
      </div>
    </div>

    <div id="end-slider-color" class="end-hero block-landing landing-hero {if $landing.hero2_media_type == 'video'} has-video{/if}"
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

 