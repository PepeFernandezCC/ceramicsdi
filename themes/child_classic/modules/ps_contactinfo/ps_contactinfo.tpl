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

<div class="block-contact col-md-3 links wrapper">

    <p class="h4 text-uppercase block-contact-title hidden-sm-down">{l s='Store information' d='Shop.Theme.Global'}</p>

    <div id="contact-infos" >

        <p style="margin-bottom: 5px;">
            <a target="_BLANK" href="https://www.google.com/maps/place/Ceramic+Connection/@39.9523449,-0.1878193,840m/data=!3m2!1e3!4b1!4m6!3m5!1s0xd60099c1b0adb19:0xc3c8af6c0bbf8d47!8m2!3d39.9523449!4d-0.1878193!16s%2Fg%2F11m79wxtc_?entry=ttu&g_ep=EgoyMDI1MDUwNy4wIKXMDSoASAFQAw%3D%3D">
                {$contact_infos.address.address1}, {$contact_infos.address.postcode} {$contact_infos.address.city}
            </a>
        </p>
        {if $contact_infos.phone}
            <p style="margin-bottom: 5px;">
                <a target="_BLANK" href="https://wa.me/34623240148">
                    Tlf. {$contact_infos.phone}
                </a>
            </p>
        {/if}
        {if $contact_infos.email && $display_email}
            <p style="margin-bottom: 5px;text-decoration: underline">
                {mailto address=$contact_infos.email encode="javascript"}
            </p>
        {/if}

        {* END PLANATEC *}
    </div>

    <div class="payment-method-banner">
        <img loading="lazy" data-src="/themes/child_classic/assets/img/web/payment_methods.webp" alt="pay methods"/>
     </div>
</div>
