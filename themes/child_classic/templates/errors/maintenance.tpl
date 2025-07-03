<!doctype html>
<html lang="">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    {block name='head_seo'}
      <title>{block name='head_seo_title'}{/block}</title>
      <meta name="description" content="{block name='head_seo_description'}{/block}">
      <meta name="keywords" content="{block name='head_seo_keywords'}{/block}">
    {/block}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
      #body {
        background-color: #f7f7f7
      }

      #maintenance-error {
          padding: 50px 15px;
          margin: 50px auto 0;
          text-align: center;
          background-color: #fff;
          box-shadow: 0 1px 3px rgba(0,0,0,.3);
      }

      #maintenance-error .logo {
          margin: 0 0 31px;
          text-align: center;
      }

      #maintenance-error h1 {
          padding: 0 0 14px;
          margin: 0 0 19px;
          font: 24px sans-serif;
          color: #333;
      }

      #maintenance-error h2 {
          padding: 0 0 14px;
          margin: 0 0 19px;
          font: 20px sans-serif;
          color: #333;
      }

      #maintenance-error .page-content {
          font: 16px sans-serif;
          color: #555454;
          text-align: left;
      }

      @media (min-width: 1200px) {
          #maintenance-error {
              width:666px;
              padding: 50px;
              margin: 126px auto 0;
          }
      }

      @media (min-width: 768px) {
          #maintenance-error {
              width:666px;
          }
      }

      @media (min-width: 992px) {
          #maintenance-error {
              width:666px;
          }
      }

      @media (min-width: 1200px) {
          #maintenance-error {
              padding-right: 25px;
              padding-left: 25px;
          }
      }

      @media (max-width: 450px) {
          #maintenance-error {
              width:90%;
          }
          
          #maintenance-error .logo img {
            width: 340px;
          }
      }
    </style>

  </head>

  <body>

    <div id="maintenance-error">
      <section id="main">

        {block name='page_header_container'}
          <header class="page-header">
            {block name='page_header_logo'}
              <div class="logo"><img src="{$shop.logo}" alt="logo" loading="lazy"></div>
            {/block}

            {block name='hook_maintenance'}
              {$HOOK_MAINTENANCE nofilter}
            {/block}

          </header>
        {/block}

        {block name='page_content_container'}
          <section id="content" class="page-content page-maintenance">
            {block name='page_content'}
              {$maintenance_text nofilter}
            {/block}
          </section>
        {/block}

        {block name='page_footer_container'}

        {/block}

      </section>

    </div>

  </body>

</html>

