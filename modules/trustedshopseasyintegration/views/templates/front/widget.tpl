{**
 * Copyright since 2022 Trusted shops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@202-ecommerce.com so we can send you a copy immediately.
 *
 * @author 202 ecommerce <tech@202-ecommerce.com>
 * @copyright 2022 Trusted shops
 * @license https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 *
 * This source file is loading the components connector.umd.js and eventsLib.js
 * (itself subject to the Trusted Shops EULA https://policies.etrusted.com/IE/en/plugin-licence.html)  to connect to Trusted Shops. For these components, you will find below a list of the open source libraries we use for our Services.
 * Please note that the following list may be subject to amendments and modifications, and does not thus claim (perpetual) exhaustiveness. You can always refer to the following website for up-to-date information on the open source software Trusted Shops uses:
 * https://policies.etrusted.com/IE/en/plugin-licence.html
 *
 * Name                Licence         Copyright Disclaimer
 * axios               MIT     Copyright (c) 2014-present (Matt Zabriskie)
 * babel               MIT     Copyright (c) 2014-present (Sebastian McKenzie and other Contributors)
 * follow-redirects    MIT     Copyright (c) 2014–present (Olivier Lalonde, James Talmage, Ruben Verborgh)
 * history             MIT     Copyright (c) 2016-2020 (React Training), Copyright (c) 2020-2021 (Remix Software)
 * hookform/resolvers  MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * inherits            ISC     Copyright (c) 2011-2022 (Isaac Z. Schlueter)
 * js-tokens           MIT     Copyright (c) 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021 (Simon Lydell)
 * lodash              MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * lodash-es           MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * loose-envify        MIT     Copyright (c) 2015 (Andreas Suarez)
 * nanoclone           MIT     Copyright (c) 2017 (Anton Kosykh)
 * path                MIT     Copyright (c) (Joyent, Inc. and other Node contributors.)
 * preact              MIT     Copyright (c) 2015-present (Jason Miller)
 * preact-router       MIT     Copyright (c) 2015 (Jason Miller)
 * process             MIT     Copyright (c) 2013 (Roman Shtylman)
 * property-expr       MIT     Copyright (c) 2014 (Jason Quense)
 * react-hook-form     MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * regenerator-runtime MIT     Copyright (c) 2014-present (Facebook, Inc.)
 * resolve-pathname    MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * tiny-invariant      MIT     Copyright (c) 2019 (Alexander Reardon)
 * tiny-warning        MIT     Copyright (c) 2019 (Alexander Reardon)
 * toposort            MIT     Copyright (c) 2012 (Marcel Klehr)
 * types/lodash        MIT     (none)
 * util                MIT     Copyright (c) (Joyent, Inc. and other Node contributors)
 * value-equal         MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * yup                 MIT     Copyright (c) 2014 (Jason Quense)
 * zustand             MIT     Copyright (c) 2019 (Paul Henschel)
 *}
{if $location == 'wdg-loc-ft'}
  <div class="row" style="max-width: 99% !important;margin: 0 auto;">
    {if isset($isPs17) && $isPs17 != false}
    <div class="col-xs-12">
    {/if}
    {if isset($isPs17) && $isPs17 == false}
      <section class="footer-block col-xs-12 col-sm-4">
    {/if}
{else}
  <div class="ts-{$location|escape:'htmlall':'UTF-8'}-wrapper" data-app="TrustedShopsApp">
      {if isset($isPs17) && $isPs17 == false && $location == 'wdg-loc-pp'}
          <h3 class="page-product-heading">{l s='Trusted Shops Reviews' mod='trustedshopseasyintegration'}</h3>
      {/if}
{/if}

{if !empty($widgetModels)}
  {foreach $widgetModels as $widgetModel}
    <!-- added by Trusted Shops app: Start -->
      {if !empty($widgetModel->getAttributes())}
        <{$widgetModel->getTag()|escape:'htmlall':'UTF-8'}
          {foreach $widgetModel->getAttributes() as $attributeName => $value}
              {$attributeName|escape:'htmlall':'UTF-8'}="{$value|escape:'htmlall':'UTF-8'}"
          {/foreach}
          {if $widgetModel->getType() == 'trusted_stars_service' || $widgetModel->getType() == 'product_star'}
            {if $location == 'wdg-loc-pl'}data-centered-productlist{/if}
          {/if} 
          class="widget_{$widgetModel->getType()|escape:'htmlall':'UTF-8'}"
        ></{$widgetModel->getTag()|escape:'htmlall':'UTF-8'}>
      {/if}
    <!-- End -->
  {/foreach}
{/if}
</div>
{if $location == 'wdg-loc-ft'}  
  {if isset($isPs17) && $isPs17 != false}
    </div>
  {/if}
  {if isset($isPs17) && $isPs17 == false}
    </section>
  {/if}
{/if}
