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
                            <div class="col-xs-12 title">
                                <h3 class="h3">{l s='Advantages' d='Shop.Theme.Global'}</h3>
                            </div>
                            {$cms.content nofilter}
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 p-right">
                        <div class="row">
                            <div class="col-xs-12 title">
                                <h3 class="h3">{l s='Form' d='Shop.Theme.Global'}</h3>
                            </div>
                            <section class="col-xs-12 contact-form">
                                <form action="{$urls.pages.contact}" method="post">
                                    {if $smarty.get.success}
                                        <div class="col-xs-12 alert {if $smarty.get.success == '0'}alert-danger{else}alert-success{/if}">
                                            El formulario se ha enviado correctamente.
                                        </div>
                                    {/if}

                                    <section class="form-fields">
                                        <input type="hidden" name="form_type" value="profesional">

                                        <div class="block-fields">
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
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="activity_type">
                                                        <strong style="text-transform: uppercase;">{l s='Activity type' d='Shop.Forms.Labels'}</strong>
                                                        &nbsp;{l s='Check all that apply' d='Shop.Forms.Labels'}
                                                    </label>
                                                </div>
                                                <div class="col-md-12" style="margin-top: 10px;">
                                                    <div class="row">
                                                        <div class="col-md-4">
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
                                                        <div class="col-md-4">
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
                                                        <div class="col-md-4">
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
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
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
                                                        <div class="col-md-4">
                                                            <label>
                                                                <input
                                                                        id="activity_type"
                                                                        class="form-control activity_type"
                                                                        name="activity_type[]"
                                                                        type="checkbox"
                                                                        value="{l s='Store' d='Shop.Forms.Labels'}"
                                                                        required
                                                                >
                                                                {l s='Store' d='Shop.Forms.Labels'}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="name">{l s='Company´s name' d='Shop.Forms.Labels'}
                                                        *</label>
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
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="cif">{l s='Identification number' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="cif"
                                                            class="form-control"
                                                            name="cif"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            {if $language.id != 1}
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <label class="form-control-label"
                                                               for="intra_community">
                                                            <input
                                                                    id="intra_community"
                                                                    class="form-control"
                                                                    name="intra_community"
                                                                    type="checkbox"
                                                                    style="width: auto; display: inline-flex;"
                                                            >
                                                            {l s='I am intra community operator () Bill exempt VAT by article 21 of the law 37/1992, date the 28th december of value added tax' d='Shop.Forms.Labels'}
                                                            *</label>
                                                    </div>
                                                </div>
                                            {/if}

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="address">{l s='Address' d='Shop.Forms.Labels'} *</label>
                                                    <input
                                                            id="address"
                                                            class="form-control"
                                                            name="address"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="postal_code">{l s='Postal code' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="postal_code"
                                                            class="form-control"
                                                            name="postal_code"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="city">{l s='City' d='Shop.Forms.Labels'} *</label>
                                                    <input
                                                            id="city"
                                                            class="form-control"
                                                            name="city"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="province">{l s='Province' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="province"
                                                            class="form-control"
                                                            name="province"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="email">{l s='Email' d='Shop.Forms.Labels'} *</label>
                                                    <input
                                                            id="email"
                                                            class="form-control"
                                                            name="from"
                                                            type="email"
                                                            value="{$contact.email}"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">

                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="phone">{l s='Phone' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="phone"
                                                            class="form-control"
                                                            name="phone"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">

                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="contact_person_name">{l s='Contact person' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="contact_person_name"
                                                            class="form-control"
                                                            name="contact_person_name"
                                                            type="text"
                                                            required
                                                    >
                                                </div>
                                            </div>

                                            <div class="form-group row">

                                                <div class="col-md-12">
                                                    <label class="form-control-label"
                                                           for="commercial_web">{l s='Website address' d='Shop.Forms.Labels'}
                                                        *</label>
                                                    <input
                                                            id="commercial_web"
                                                            class="form-control"
                                                            name="commercial_web"
                                                            type="text"
                                                            required
                                                    >
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
                                                                <input id="suscribe_newsletter"
                                                                       name="suscribe_newsletter" type="checkbox"
                                                                       value="1"
                                                                       required>
                                                                <span><i class="material-icons rtl-no-flip checkbox-checked psgdpr_consent_icon"></i></span>
                                                                <span>{l s='I would like to receive information and offers for professionals' d='Shop.Forms.Labels'}</span>
                                                            </label>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            {*
                                            <div class="form-group row" style="margin-top: 30px;">
                                                <div class="col-xs-12 col-xl-9">
                                                    <p class="small" style="text-align: justify;">
                                                        <strong style="text-transform: uppercase">{l s='Basic information on Data Protection' d='Shop.Forms.Labels'}</strong>:
                                                        {l s='Ceramic Connection AIE will process your data to attend to your query and, if you give your consent, to
                send you information and offers. we won\'t give in your data to third parties except by legal obligation.
                You can exercise your rights of access, rectification, deletion and others, as well as withdraw your
                consent and obtain additional information about the treatment of your data in our' d='Shop.Forms.Labels'}
                                                        <a href="/contenido/politica-de-privacidad"
                                                           target="_blank">{l s='Privacy Policy' d='Shop.Forms.Labels'}</a>.</p>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <table class="data-protection-info">
                                                    <thead>
                                                    <tr>
                                                        <td colspan="2" >{l s='Basic information on Data Protection' d='Shop.Forms.Labels'}</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>{l s='Responsible' d='Shop.Forms.Labels'}</td>
                                                        <td>CERAMIC CONNECTION AIE</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{l s='Purpose' d='Shop.Forms.Labels'}</td>
                                                        <td>{l s='Address your query and, if you accept it, send you information and offers' d='Shop.Forms.Labels'}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{l s='Legitimation' d='Shop.Forms.Labels'}</td>
                                                        <td>{l s='Contractual relationship / Consent' d='Shop.Forms.Labels'}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{l s='Recipients' d='Shop.Forms.Labels'}</td>
                                                        <td>{l s='No data will be transferred to third parties, except legal obligation' d='Shop.Forms.Labels'}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{l s='Rights' d='Shop.Forms.Labels'}</td>
                                                        <td>{l s='Access, rectify and delete the data, withdraw your consent, as well as exercise other rights, as explained in the additional information' d='Shop.Forms.Labels'}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{l s='Additional information' d='Shop.Forms.Labels'}</td>
                                                        <td>{l s='You can consult additional and detailed information on Data Protection at: ' d='Shop.Forms.Labels'}<a href="/contenido/politica-de-privacidad" target="_blank">https://ceramicconnection.com/contenido/politica-de-privacidad</a></td>
                                                    </tr>
                                                    </tbody>
                                                </table>

                                                <style>
                                                    .data-protection-info {
                                                        width: 50%;
                                                        margin-left: 15px;
                                                        margin-right: 15px;
                                                        font-size: .7em;
                                                    }

                                                    .data-protection-info td {
                                                        border: 1px solid black;
                                                        padding: 2px 5px;
                                                    }
                                                </style>
                                            </div>
                                            *}

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
                                    <footer class="form-footer text-sm-left" style="padding-left: 0;">
                                        <div>
                                            <style>
                                                input[name=url] {
                                                    display: none !important;
                                                }
                                            </style>
                                            <input type="text" name="url" value=""/>
                                            <input type="hidden" name="token" value="{$token}"/>
                                            <input id="send-professional-button" class="btn btn-primary" type="submit"
                                                   name="submitMessage"
                                                   value="{l s='Send' d='Shop.Theme.Actions'}">
                                        </div>
                                    </footer>
                                </form>
                            </section>
                        </div>
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
