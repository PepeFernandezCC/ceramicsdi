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
{extends file='page.tpl'}

{block name='page_title'}
    {$cms.meta_title}
{/block}

{block name='page_content_container'}
    <section id="content" class="page-content page-cms page-cms-{$cms.id}">

        {block name='cms_content'}
            {if $cms.id == 10}
                {$pinterestLinks = "</p>"|explode:$cms.content}
                <div>
                    {$title = ''}
                    {foreach from=$pinterestLinks item="pinterestLink"}
                        {$finalLink = ($pinterestLink|replace:'<p>':'')|trim}

                        {if $finalLink != ''}
                            {if filter_var($finalLink, FILTER_VALIDATE_URL)}
                                <div class="col-md-6 col-xs-12 embed-pinterest">
                                    <h5>{$title}</h5>
                                    <a data-pin-do="embedBoard" data-pin-lang="es" data-pin-board-width="1000"
                                       data-pin-scale-height="500"
                                       data-pin-scale-width="80" href="{$finalLink}"></a>
                                </div>
                            {else}
                                {$title = $finalLink}
                            {/if}
                        {/if}
                    {/foreach}
                </div>
                <script async defer src="//assets.pinterest.com/js/pinit.js"></script>
            {elseif $cms.id == 12}
                <section id="profesionales">
                    <h1 style="text-align: center;">{l s='Ceramic Connection Professionals' d='Shop.Theme.Global'}</h1>
                    <h2 style="text-align: center;">
                        {l s='At Ceramic Connection we are at your disposal for suggestions, personalized projects' d='Shop.Theme.Global'}
                        <br>
                        {l s='and technical support to improve or streamline your project.' d='Shop.Theme.Global'}
                    </h2>

                    <div class="col-md-6 col-xs-12 p-left">
                        <div class="row">
                            {$cms.content nofilter}
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 p-right">
                        <div class="row">
                            <div class="col-xs-12 title">
                                <h3 class="h3">{l s='Register like a Professional' d='Shop.Theme.Global'}</h3>
                            </div>
                            <section class="col-xs-12 contact-form">
                                <form action="{$urls.pages.contact}" method="post">
                                    {if isset($smarty.get.success)}
                                        {if $smarty.get.success == '0'}
                                            <div class="col-xs-12 alert alert-danger">
                                               Mail not send (debug mode on) 
                                            </div>
                                        {else}
                                            <div class="col-xs-12 alert alert-success">
                                                {l s='The form has been successfully submitted' d='Shop.Theme.Global'}
                                            </div>
                                        {/if}

                                    {/if}

                                    <section class="form-fields">
                                        <input type="hidden" name="form_type" value="profesional">

                                        <div class="professional-form-box">
                                            <div class="form-group row" style="display: none !important;">
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="id_contact">{l s='Subject' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <select name="id_contact" id="id_contact"
                                                            class="form-control form-control-select">
                                                        <option disabled selected value></option>
                                                        {foreach from=Contact::getContacts($language.id) item=contact_elt}
                                                            <option value="{$contact_elt.id_contact}"
                                                                    {if $contact_elt.id_contact == 1}selected{/if}>
                                                                {$contact_elt.name}
                                                            </option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row activity_type">

                                                <div class="col-md-12 activities-margin">
                                                    <div class="row">
                                                        <div>
                                                            <label>
                                                                <input
                                                                        id="activity_type"
                                                                        class="form-control activity_type"
                                                                        name="activity_type[]"
                                                                        type="checkbox"
                                                                        value="{l s='Architect' d='Shop.Forms.Labels'}"
                                                                        required
                                                                >
                                                                {l s='Architect' d='Shop.Forms.Labels'}
                                                            </label>
                                                        </div>
                                                        
                                                        <div>
                                                            <label>
                                                                <input
                                                                        id="activity_type"
                                                                        class="form-control activity_type"
                                                                        name="activity_type[]"
                                                                        type="checkbox"
                                                                        value="{l s='Renovation company' d='Shop.Forms.Labels'}"
                                                                        required
                                                                >
                                                                {l s='Renovation company' d='Shop.Forms.Labels'}
                                                            </label>
                                                        </div>

                                                        <div>
                                                            <label>
                                                                <input
                                                                        id="activity_type"
                                                                        class="form-control activity_type"
                                                                        name="activity_type[]"
                                                                        type="checkbox"
                                                                        value="{l s='Decorator or Interior Designer' d='Shop.Forms.Labels'}"
                                                                        required
                                                                >
                                                                {l s='Decorator or Interior Designer' d='Shop.Forms.Labels'}
                                                            </label>
                                                        </div>

                                                        <div>
                                                            <label>
                                                                <input
                                                                        id="activity_type"
                                                                        class="form-control activity_type"
                                                                        name="activity_type[]"
                                                                        type="checkbox"
                                                                        value="{l s='Construction company' d='Shop.Forms.Labels'}"
                                                                        required
                                                                >
                                                                {l s='Construction company' d='Shop.Forms.Labels'}
                                                            </label>
                                                        </div>

                                                        <div>
                                                            <label>
                                                                <input
                                                                        id="activity_type"
                                                                        class="form-control activity_type"
                                                                        name="activity_type[]"
                                                                        type="checkbox"
                                                                        value="{l s='Store' d='Shop.Forms.Labels'}"
                                                                        required
                                                                >
                                                                {l s='Construction material store' d='Shop.Forms.Labels'}
                                                            </label>
                                                        </div>

                                                        {if $language.id != 1}
                                                        
                                                            <div>
                                                                <label>
                                                                    <input
                                                                            id="intra_community activity_type"
                                                                            class="form-control"
                                                                            name="intra_community"
                                                                            type="checkbox"
                                                                            style="width: auto; display: inline-flex;"
                                                                    >
                                                                    {l s='I am intra community operator () Bill exempt VAT by article 21 of the law 37/1992, date the 28th december of value added tax' d='Shop.Forms.Labels'}
                                                                    *
                                                                </label>
                                                            </div>
                                                        
                                                        {/if}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-8">
                                                    <label class="form-control-label"
                                                           for="name">{l s='Company´s name' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="name"
                                                            class="form-control professional-input-height"
                                                            name="name"
                                                            type="text"
                                                            required
                                                    >
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-control-label"
                                                           for="cif">{l s='Identification number' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="cif"
                                                            class="form-control professional-input-height"
                                                            name="cif"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">

                                                <div class="col-md-8">
                                                    <label class="form-control-label"
                                                           for="contact_person_name">{l s='Contact person' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="contact_person_name"
                                                            class="form-control professional-input-height"
                                                            name="contact_person_name"
                                                            type="text"
                                                            required
                                                    >
                                                </div>


                                                <div class="col-md-4">
                                                    <label class="form-control-label"
                                                           for="phone">{l s='Phone' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="phone"
                                                            class="form-control professional-input-height"
                                                            name="phone"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">

                                                <div class="col-md-6">
                                                    <label class="form-control-label"
                                                           for="commercial_web">{l s='Website address' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="commercial_web"
                                                            class="form-control professional-input-height"
                                                            name="commercial_web"
                                                            type="text"
                                                            required
                                                    >
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-control-label"
                                                           for="email">{l s='Email' d='Shop.Forms.Labels'} *</label>
                                                    <input
                                                            id="email"
                                                            class="form-control professional-input-height"
                                                            name="from"
                                                            type="email"
                                                            value="{if isset($contact)}$contact.email{/if}"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">

                                                <div class="col-md-4">
                                                    <label class="form-control-label"
                                                           for="province">{l s='Province' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="province"
                                                            class="form-control professional-input-height"
                                                            name="province"
                                                            type="text"
                                                            required
                                                    >
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-control-label"
                                                           for="city">{l s='City' d='Shop.Forms.Labels'} *</label>
                                                    <input
                                                            id="city"
                                                            class="form-control professional-input-height"
                                                            name="city"
                                                            type="text"
                                                            required
                                                    >
                                                </div>



                                                <div class="col-md-4">
                                                    <label class="form-control-label"
                                                           for="postal_code">{l s='Postal code' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="postal_code"
                                                            class="form-control professional-input-height"
                                                            name="postal_code"
                                                            type="text"
                                                            required
                                                    >
                                                </div>


                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="address">{l s='Address' d='Shop.Forms.Labels'} *</label>
                                                    <input
                                                            id="address"
                                                            class="form-control professional-input-height"
                                                            name="address"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row" style="display:none">
                                                <div class="col-xl-9 col-xs-12">
                                                    <label class="form-control-label"
                                                        for="contactform-message">{l s='Message' d='Shop.Forms.Labels'} *</label>
                                                    <textarea
                                                            id="contactform-message"
                                                            class="form-control"
                                                            name="message"
                                                            {* placeholder="{l s='How can we help?' d='Shop.Forms.Help'} *" *}
                                                            rows="5"
                                                    > -. Formulario profesionales .- </textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p style="color: black;" class="text-sm-right small">
                                                        * {l s='Required fields' d='Shop.Forms.Help'}</p>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-xs-12">
                                                    {hook h='displayGDPRConsent' id_module=2}
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-xs-12">
                                                    <div class="custom-newsletter">
                                                        <span class="custom-checkbox">
                                                            <label class="psgdpr_consent_message">
                                                                <input id="suscribe_newsletter" name="suscribe_newsletter" type="hidden" value="1" required>
                                                                {*<span><i class="material-icons rtl-no-flip checkbox-checked psgdpr_consent_icon"></i></span>*}
                                                                <span style="font-weight:bold">* {l s='By registering as a professional, you agree to receive information and timely offers from Ceramic Connection' d='Shop.Theme.Global'}</span>
                                                            </label>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <div class="col-xs-12">
                                                    <a class="conditions-professionals"
                                                       href="{Context::getContext()->link->getCMSLink(13)}">
                                                        <i class="material-icons">arrow_forward</i>&nbsp;{l s='Conditions for professionals' d='Shop.Forms.Labels'}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </section>
                                    <footer class="form-footer text-sm-left professional-form-box" style="padding-left: 0;">
                                        <div>
                                            <style>
                                                input[name=url] {
                                                    display: none !important;
                                                }
                                            </style>
                                            <input type="text" name="url" value=""/>
                                            <input type="hidden" name="token" value="{$token}"/>
                                            <input type="hidden" name="profform" value="1"/>
                                            <input id="send-professional-button" class="btn btn-primary btn-professional" type="submit"
                                                   name="submitMessage"
                                                   value="{l s='Send' d='Shop.Theme.Actions'}">
                                        </div>
                                    </footer>
                                </form>
                            </section>
                        </div>
                        {if $language.id == 1}
                            <div id="catalogDownloads">
                                <div style="padding-bottom: 10px"><a style="color: black" href="{$urls.base_url}catalog/CC_CATALOGO-2025.pdf" rel="nofollow" download="CC_CATALOGO-2025.pdf">DESCARGAR CATALOGO PDF <i class="fas fa-file" style="padding:2px"></i></a></div>
                                <div><a style="color: black"href="https://ceramicconnection.com/pricelist.php" target="_BLANK" rel="nofollow">VER TARIFA DE PRECIOS</a></div>                         
                            </div>
                        {/if}
                    </div>
                </section>
            {else}
                {$cms.content nofilter}
            {/if}
        {/block}

        {if $cms.id == 9}
            <article id="faq">
                {foreach from=$faq item=$question}
                    <button class="accordion">
                        {$question.title}
                    </button>
                    <section style="display: none;">
                        {$question.answer nofilter}
                    </section>
                {/foreach}
            </article>
        {/if}

        {block name='hook_cms_dispute_information'}
            {hook h='displayCMSDisputeInformation'}
        {/block}

        {block name='hook_cms_print_button'}
            {hook h='displayCMSPrintButton'}
        {/block}
    </section>
{/block}
