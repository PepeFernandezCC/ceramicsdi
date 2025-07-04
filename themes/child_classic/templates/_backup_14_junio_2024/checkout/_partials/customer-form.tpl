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
{extends "customer/_partials/customer-form.tpl"}

{block "form_field"}
    {if $field.name === 'password' and $guest_allowed}
        <p class="form-informations">
            <span class="font-weight-bold form-informations-title">{l s='Create an account' d='Shop.Theme.Checkout'}</span> <span class="font-italic form-informations-option">{l s='(optional)' d='Shop.Theme.Checkout'}</span>
            <br>
            <span class="text-muted form-informations-subtitle">{l s='And save time on your next order!' d='Shop.Theme.Checkout'}</span>
        </p>
        {$smarty.block.parent}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block "form_buttons"}
    <button
            class="continue btn btn-primary float-xs-right"
            name="continue"
            data-link-action="register-new-customer"
            type="submit"
            value="1"
    >
        {l s='Continue' d='Shop.Theme.Actions'}
    </button>
{/block}
