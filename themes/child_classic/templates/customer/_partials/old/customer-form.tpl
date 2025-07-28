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
{block name='customer_form'}
    {block name='customer_form_errors'}
        {include file='_partials/form-errors.tpl' errors=$errors['']}
    {/block}
    <form action="{block name='customer_form_actionurl'}{$action}{/block}" id="customer-form" class="js-customer-form"
          method="post">
        {if isset($step) && $step == "registration"}  
            <h3 class="sign-message">{l s='New clients' d='Shop.Theme.Customeraccount'}</h3>
        {/if}

        {if $errors|count}
            {foreach $errors as $error}
                {if !empty($error[0])}
                    <div class="alert alert-danger">{$error[0]|nl2br nofilter}</div>
                {/if}
            {/foreach}
        {/if}


        {if isset($step) && $step == "registration"}
            <div class="button" id="planatec-new-account">
                {l s='Create new account' d='Shop.Theme.Customeraccount'}
            </div>

            <div class="button" id="planatec-buy-guest">
                {l s='Buy as a guest' d='Shop.ThemeCustomeraccount'}
            </div>
        {/if}

        

        <div class="customer-fields">
            {block "form_fields"}
                {foreach from=$formFields item="field"}
                    {if $field.name != 'newsletter' && $field.name != 'company'}
                        {block "form_field"}
                            {form_field field=$field}
                        {/block}
                    {else}
                        {assign var="newsletter_field" value=$field}
                    {/if}
                {/foreach}
                {if isset($newsletter_field)}
                    {block "form_field"}
                        {form_field field=$newsletter_field}
                    {/block}
                {/if}
                {$hook_create_account_form nofilter}
            {/block}
        </div>

        {block name='customer_form_footer'}
            <footer class="form-footer clearfix">
                <input type="hidden" name="submitCreate" value="1">
                {block "form_buttons"}
                    <button class="btn btn-primary form-control-submit float-xs-right" data-link-action="save-customer"
                            type="submit">
                        {l s='Save' d='Shop.Theme.Actions'}
                    </button>
                {/block}
            </footer>
        {/block}

    </form>
{/block}
