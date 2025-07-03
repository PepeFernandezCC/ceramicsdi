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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{block name='gdpr_checkbox'}
    <div class="gdpr_consent gdpr_module_{$psgdpr_id_module|escape:'htmlall':'UTF-8'}">
        <span class="custom-checkbox">
            <label class="psgdpr_consent_message">
                <input id="psgdpr_consent_checkbox_{$psgdpr_id_module|escape:'htmlall':'UTF-8'}"
                       name="psgdpr_consent_checkbox" type="checkbox" value="1"
                       class="psgdpr_consent_checkboxes_{$psgdpr_id_module|escape:'htmlall':'UTF-8'}">
                <span><i class="material-icons rtl-no-flip checkbox-checked psgdpr_consent_icon"></i></span>
                <span>{$psgdpr_consent_message nofilter}</span>{* html data *}
            </label>
        </span>
    </div>
    {*
    <div class="row" style="margin-top: 30px;">
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
    <div class="row" style="margin-top: 30px;">
        <table class="data-protection-info">
            <thead>
            <tr>
                <td colspan="2">{l s='Basic information on Data Protection' d='Shop.Forms.Labels'}</td>
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
                <td>{l s='You can consult additional and detailed information on Data Protection at: ' d='Shop.Forms.Labels'}
                    &nbsp;<a href="/contenido/politica-de-privacidad" target="_blank">https://ceramicconnection.com/contenido/politica-de-privacidad</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    *}
{/block}
{literal}
<script type="text/javascript">
   var psgdpr_front_controller = "{/literal}{$psgdpr_front_controller|escape:'htmlall':'UTF-8'}{literal}";
   psgdpr_front_controller = psgdpr_front_controller.replace( /\amp;/g, '' );
   var psgdpr_id_customer = "{/literal}{$psgdpr_id_customer|escape:'htmlall':'UTF-8'}{literal}";
   var psgdpr_customer_token = "{/literal}{$psgdpr_customer_token|escape:'htmlall':'UTF-8'}{literal}";
   var psgdpr_id_guest = "{/literal}{$psgdpr_id_guest|escape:'htmlall':'UTF-8'}{literal}";
   var psgdpr_guest_token = "{/literal}{$psgdpr_guest_token|escape:'htmlall':'UTF-8'}{literal}";

   document.addEventListener( 'DOMContentLoaded', function () {
      let psgdpr_id_module = "{/literal}{$psgdpr_id_module|escape:'htmlall':'UTF-8'}{literal}";
      let parentForm = $( '.gdpr_module_' + psgdpr_id_module ).closest( 'form' );

      let toggleFormActive = function () {
         let parentForm = $( '.gdpr_module_' + psgdpr_id_module ).closest( 'form' );
         let checkbox = $( '#psgdpr_consent_checkbox_' + psgdpr_id_module );
         let element = $( '.gdpr_module_' + psgdpr_id_module );
         let iLoopLimit = 0;

         // by default forms submit will be disabled, only will enable if agreement checkbox is checked
         if ( element.prop( 'checked' ) != true ) {
            element.closest( 'form' ).find( '[type="submit"]' ).attr( 'disabled', 'disabled' );
         }
         $( document ).on( "change", '.psgdpr_consent_checkboxes_' + psgdpr_id_module, function () {
            if ( $( this ).prop( 'checked' ) == true ) {
               $( this ).closest( 'form' ).find( '[type="submit"]' ).removeAttr( 'disabled' );
            } else {
               $( this ).closest( 'form' ).find( '[type="submit"]' ).attr( 'disabled', 'disabled' );
            }

         } );
      }

      // Triggered on page loading
      toggleFormActive();

      $( document ).on( 'submit', parentForm, function ( event ) {
         $.ajax( {
            type: 'POST',
            url: psgdpr_front_controller,
            data: {
               ajax: true,
               action: 'AddLog',
               id_customer: psgdpr_id_customer,
               customer_token: psgdpr_customer_token,
               id_guest: psgdpr_id_guest,
               guest_token: psgdpr_guest_token,
               id_module: psgdpr_id_module,
            },
            error: function ( err ) {
               console.log( err );
            }
         } );
      } );
   } );
</script>
{/literal}
