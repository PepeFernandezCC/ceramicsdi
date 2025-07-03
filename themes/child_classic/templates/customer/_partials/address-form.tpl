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
{block name="address_form"}
    <div class="js-address-form">
        {include file='_partials/form-errors.tpl' errors=$errors['']}

        {block name="address_form_url"}
        <form
                method="POST"
                action="{url entity='address' params=['id_address' => $id_address]}"
                data-id-address="{$id_address}"
                data-refresh-url="{url entity='address' params=['ajax' => 1, 'action' => 'addressForm']}" 
        >
            {/block}

        <div id="delivery-address">
            <div class="js-address-form">

            {block name="address_form_fields"}
                <section class="form-fields">
                    {block name='form_fields'}
                        {* PLANATEC *}
                        <div class="form-group row" style="width: 100%;">
                            <div class="col-md-12">
                                <label class="radio-inline" for="field-empresa">
                                  <span class="custom-radio">
                                    <input
                                            name="treatment"
                                            id="field-empresa"
                                            type="radio"
                                            value="empresa"
                                            required
                                    >
                                    <span></span>
                                  </span>
                                    {l s='company' d='Shop.Theme.Checkout'}
                                </label>
                                <label class="radio-inline" for="field-particular">
                                  <span class="custom-radio">
                                    <input
                                            name="treatment"
                                            id="field-particular"
                                            type="radio"
                                            value="particular"
                                            required
                                            checked
                                    >
                                    <span></span>
                                  </span>
                                    {l s='individual' d='Shop.Theme.Checkout'}
                                </label>
                            </div>
                        </div>

                        <div class="form-group row" id="intracomunitary-identification">
                            <div class="intracomunitary-check-class">
                                <input name="intracomunitary-checkbox" id="intracomunitary-checkbox" type ="checkbox" value="0">
                                <label for="intracomunitary-checkbox" style="margin: 0">{l s='I identify myself as an INTRA-COMMUNITY OPERATOR and I am exempt from taxes' d='Shop.Theme.Checkout'}.</label>
                            </div>
                        </div>

                        {* END PLANATEC *}
                        {foreach from=$formFields item="field"}
                            {if $field.name != "firstname" && $field.name != "lastname"}
                                {block name='form_field'}
                                    {form_field field=$field}
                                {/block}
                            {/if}
                        {/foreach}
                    {/block}
                </section>
            {/block}

            {block name="address_form_footer"}
                <footer class="form-footer clearfix">
                    <input type="hidden" name="submitAddress" value="1">
                    {block name='form_buttons'}
                        <button class="btn btn-primary form-control-submit float-xs-right" type="submit">
                            {l s='Save' d='Shop.Theme.Actions'}
                        </button>
                    {/block}
                </footer>
            {/block}

            </div>
        </div>

        </form>
    </div>
{/block}
