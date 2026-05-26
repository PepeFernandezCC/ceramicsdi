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
<section class="contact-form">
    <form action="{$urls.pages.contact}" method="post"
          {if $contact.allow_file_upload}enctype="multipart/form-data"{/if}>
        {if $notifications}
            <div class="col-xs-12 alert {if $notifications.nw_error}alert-danger{else}alert-success{/if}">
                <ul>
                    {* PLANATEC *}
                    {assign var="repeat" value=""}
                    {* END PLANATEC *}
                    {foreach $notifications.messages as $notif}
                        {* PLANATEC *}
                        {if $notif != $repeat}
                            <li>{$notif}</li>
                        {/if}

                        {assign var="repeat" value=$notif}
                        {* END PLANATEC *}
                    {/foreach}
                </ul>
            </div>
        {/if}

        {if !$notifications || $notifications.nw_error}
            <section class="form-fields">

                <div class="form-group row title">
                    <div class="col-xs-12">
                        <h3>{l s='We are at your disposal' d='Shop.Forms.Title'}</h3>
                    </div>
                </div>

                <div class="block-fields">
                    <div class="row">
                        <div class="col-xl-9 col-xs-12">
                            <p style="color: black;">{l s='To better serve your needs, we ask you to select the reason for your contact and fill out the following form.' d='Shop.Forms.Help'}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xl-9 col-xs-12">
                            <label class="form-control-label" for="id_contact">{l s='Subject' d='Shop.Forms.Labels'}
                                *</label>
                            <select name="id_contact" id="id_contact" class="form-control form-control-select">
                                <option disabled selected value></option>
                                {foreach from=$contact.contacts item=contact_elt}
                                    <option value="{$contact_elt.id_contact}">{$contact_elt.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    {* PLANATEC *} 
                    <div class="form-group row">
                        <div class="col-xl-9 col-xs-12">
                            <label class="form-control-label" for="name">{l s='Name' d='Shop.Forms.Labels'} *</label>
                            <input
                                    id="name"
                                    class="form-control"
                                    name="name"
                                    type="text"
                                    required
                            >
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xl-9 col-xs-12">
                            <label class="form-control-label" for="surname">{l s='Surname' d='Shop.Forms.Labels'}
                                *</label>
                            <input
                                    id="surname"
                                    class="form-control"
                                    name="surname"
                                    type="text"
                                    {* placeholder="{l s='Surname' d='Shop.Forms.Help'} *" *}
                                    required
                            >
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xl-9 col-xs-12">
                            <label class="form-control-label" for="city">{l s='City' d='Shop.Forms.Labels'} *</label>
                            <input
                                    id="city"
                                    class="form-control"
                                    name="city"
                                    type="text"
                                    {* placeholder="{l s='City' d='Shop.Forms.Help'} *" *}
                                    required
                            >
                        </div>
                    </div>
                    {* END PLANATEC *}

                    <div class="form-group row">
                        <div class="col-xl-9 col-xs-12">
                            <label class="form-control-label" for="email">{l s='Email' d='Shop.Forms.Labels'} *</label>
                            <input
                                    id="email"
                                    class="form-control"
                                    name="from"
                                    type="email"
                                    value="{$contact.email}"
                                    {* placeholder="{l s='your@email.com' d='Shop.Forms.Help'} *" *}
                                    {* PLANATEC *}required{* END PLANATEC *}
                            >
                        </div>
                    </div>

                    {* PLANATEC *}
                    <div class="form-group row">

                        <div class="col-xl-9 col-xs-12">
                            <label class="form-control-label" for="occupation">{l s='Occupation' d='Shop.Forms.Labels'}
                                *</label>
                            <input
                                    id="occupation"
                                    class="form-control"
                                    name="occupation"
                                    type="text"
                                    required
                            >
                        </div>
                    </div>

                    {assign var='countries' value=Country::getCountries($language.id, false)}
                    <div class="form-group row">
                        <div class="col-xl-9 col-xs-12">
                            <label class="form-control-label" for="country">{l s='Country' d='Shop.Forms.Labels'}
                                *</label>
                            <select id="country" name="country" class="form-control form-control-select">
                                <option disabled selected value></option>
                                {foreach from=$countries item=country}
                                    <option value="{$country.id_country}">{$country.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    {* END PLANATEC *}

                    {if $contact.allow_file_upload}
                        <div class="form-group row">
                            <label class="form-control-label"
                                   for="file-upload">{l s='Choose file' d='Shop.Forms.Labels'} *</label>
                            <div class="col-xl-9 col-xs-12">
                                <input id="file-upload" type="file" name="fileUpload" class="filestyle"
                                       data-buttonText="{l s='Choose file' d='Shop.Theme.Actions'}">
                            </div>
                            <span class="col-md-3 form-control-comment">
                                {l s='optional' d='Shop.Forms.Help'}
                            </span>
                        </div>
                    {/if}

                    <div class="form-group row">
                        <div class="col-xl-9 col-xs-12">
                            <label class="form-control-label"
                                   for="contactform-message">{l s='Message' d='Shop.Forms.Labels'} *</label>
                            <textarea
                                    id="contactform-message"
                                    class="form-control"
                                    name="message"
                                    {* placeholder="{l s='How can we help?' d='Shop.Forms.Help'} *" *}
                                    rows="5"
                            >{if $contact.message}{$contact.message}{/if}
                            </textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-9 col-xs-12">
                            <p style="color: black;" class="text-sm-right small">
                                * {l s='Required fields' d='Shop.Forms.Help'}</p>
                        </div>
                    </div>

                    {if isset($id_module)}
                        <div class="form-group row">
                            <div class="col-xl-9 col-xs-12">
                                {hook h='displayGDPRConsent' id_module=$id_module}
                            </div>
                        </div>
                    {/if}

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <div class="custom-newsletter">
                                <span class="custom-checkbox">
                                    <label class="psgdpr_consent_message">
                                        <input id="suscribe_newsletter"
                                               name="suscribe_newsletter" type="checkbox"
                                               value="1">
                                        <span><i class="material-icons rtl-no-flip checkbox-checked psgdpr_consent_icon"></i></span>
                                        <span>{l s='I would like to receive information and offers' d='Shop.Forms.Labels'}</span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
            <footer class="form-footer text-sm-left">
                <div class="col-xl-9 col-xs-12">
                    <style>
                        input[name=url] {
                            display: none !important;
                        }
                    </style>
                    <input type="text" name="url" value=""/>
                    <input type="hidden" name="token" value="{$token}"/>
                    <input type="hidden" name="profform" value="0"/>
                    <input class="btn btn-primary" type="submit" name="submitMessage"
                           value="{l s='Send' d='Shop.Theme.Actions'}">
                </div>
            </footer>
        {/if}

    </form>
</section>
