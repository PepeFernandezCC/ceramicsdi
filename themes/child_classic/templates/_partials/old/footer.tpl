{**

 * Copyright since 2007 PrestaShop SA and Contributors

 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA

 *

 * NOTICE OF LICENSE

 *

 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)

 * that is bundled with this package in the file LICENSE.md.

 * It is also available through the world-wide-web at this URL:

 * https://opensource.org/licenses/AFL-3.0

 * If you did not receive a copy of the license and are unable to

 * obtain it through the world-wide-web, please send an email

 * to license@prestashop.com so we can send you a copy immediately.

 *

 * DISCLAIMER

 *

 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer

 * versions in the future. If you wish to customize PrestaShop for your

 * needs please refer to https://devdocs.prestashop.com/ for more information.

 *

 * @author    PrestaShop SA and Contributors <contact@prestashop.com>

 * @copyright Since 2007 PrestaShop SA and Contributors

 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)

 *}

<div class="footer-container">

    <div class="container-fluid">

        <h2 id="trusted_title">{l s='How our customers see us' d='Shop.Theme.Global'}</h2>

        <div class="row" style="border-bottom: 1px solid">

            {hook h='displayFooterBefore'}

        </div>

        <div class="row">

            <div class="col-md-3 col-xs-12 first-column">

                <div>{$cmsFooterColumn1 nofilter}</div>
                <div><img data-src="/themes/child_classic/assets/img/web/Ecommerce-Awards-Espana-Logo.webp" style="width:auto;padding-top:90px" alt="banner ecommerce awards"/></div>

            </div>



            {block name='hook_footer'}

                {hook h='displayFooter'}

            {/block}



            <div class="col-md-3 col-xs-12">

                {include file="themes/child_classic/modules/ps_socialfollow/ps_socialfollow.tpl" social_links=$socialLinksFooter}

                {hook h="displayRightColumn"}

                <div class="trusted-shop-banner">

                    {if $language.id == 1}
                        <a href="{$urls.base_url}es/contenido/condiciones-trusted-shops">
                            <img loading="lazy" data-src="/themes/child_classic/assets/img/web/trusted_shop_banner_es.webp" alt="banner trusted shop"/>
                        </a>
                    {elseif $language.id == 2}
                        <a loading="lazy" href="{$urls.base_url}fr/contenido/conditions-trusted-shops">
                            <img data-src="/themes/child_classic/assets/img/web/trusted_shop_banner_fr.webp" alt="banner trusted shop"/>
                        </a>
                    {elseif $language.id == 3}
                        <a loading="lazy" href="{$urls.base_url}en/contenido/conditions-trusted-shops">
                            <img data-src="/themes/child_classic/assets/img/web/trusted_shop_banner_en.webp" alt="banner trusted shop"/>
                        </a>
                    {elseif $language.id == 4}
                        <a loading="lazy" href="{$urls.base_url}de/contenido/bedingungen-trusted-shops">
                            <img data-src="/themes/child_classic/assets/img/web/trusted_shop_banner_de.webp" alt="banner trusted shop"/>
                        </a>
                    {elseif $language.id == 5}
                        <a loading="lazy" href="{$urls.base_url}pt/contenido/condicoes-trusted-shops">
                            <img data-src="/themes/child_classic/assets/img/web/trusted_shop_banner_pt.webp" alt="banner trusted shop"/>
                        </a>
                    {elseif $language.id == 6}
                        <a loading="lazy" href="{$urls.base_url}nl/contenido/voorwaarden-trusted-shops">
                            <img data-src="/themes/child_classic/assets/img/web/trusted_shop_banner_nl.webp" alt="banner trusted shop"/>
                        </a>
                    {/if}

                </div>


            </div>

   

        </div>

        <div class="row">

            {block name='hook_footer_after'}

                {hook h='displayFooterAfter'}

            {/block}

        </div>

        <div class="row {* PLANATEC *}copyright{* END PLANATEC *}">

            <div class="col-md-12 col-xs-12">

                <p class="text-sm-center">

                    {block name='copyright_link'}

                        {*

                        - PLANATEC: eliminado el enlace del copyright

                        <a href="https://www.prestashop.com" target="_blank" rel="noopener noreferrer nofollow">

                            {l s='ALL RIGHTS RESERVED %copyright% CERAMIC CONNECTION %year%' sprintf=['%prestashop%' => 'PrestaShop™', '%year%' => 'Y'|date, '%copyright%' => '©'] d='Shop.Theme.Global'}

                        </a>

                        *}



                        {l s='ALL RIGHTS RESERVED %copyright% CERAMIC CONNECTION %year%' sprintf=['%prestashop%' => 'PrestaShop™', '%year%' => 'Y'|date, '%copyright%' => '©'] d='Shop.Theme.Global'}

                    {/block}

                </p>

            </div>

        </div>

    </div>

</div>

