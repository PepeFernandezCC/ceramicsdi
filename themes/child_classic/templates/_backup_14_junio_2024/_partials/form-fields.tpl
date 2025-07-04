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
{if $field.type == 'hidden'}

    {block name='form_field_item_hidden'}
        <input type="hidden" name="{$field.name}" value="{$field.value}">
    {/block}

{else}
    <div class="form-group row {if !empty($field.errors)}has-error{/if}">
        <div class="col-md-12{if ($field.type === 'radio-buttons')} form-control-valign{/if}">
            {if $field.type !== 'checkbox'}
                <label class="form-control-label{if $field.required} required{/if}" for="field-{$field.name}">
                    {$field.label}{if $field.required} *{/if}
                </label><br>
            {/if}

            {if $field.type === 'select'}

                {block name='form_field_item_select'}
                    <select id="field-{$field.name}" class="form-control form-control-select" name="{$field.name}"
                            {if $field.required}required{/if}>
                        <option value disabled selected>{l s='Please choose' d='Shop.Forms.Labels'}</option>
                        {foreach from=$field.availableValues item="label" key="value"}
                            <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
                        {/foreach}
                    </select>
                {/block}

            {elseif $field.type === 'countrySelect'}

                {block name='form_field_item_country'}
                    <select
                            id="field-{$field.name}"
                            class="form-control form-control-select js-country"
                            name="{$field.name}"
                            {if $field.required}required{/if}
                    >
                        <option value disabled selected>{l s='Please choose' d='Shop.Forms.Labels'}</option>
                        {foreach from=$field.availableValues item="label" key="value"}
                            <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
                        {/foreach}
                    </select>
                {/block}

            {elseif $field.type === 'radio-buttons'}

                {block name='form_field_item_radio'}
                    {foreach from=$field.availableValues item="label" key="value"}
                        <label class="radio-inline" for="field-{$field.name}-{$value}">
              <span class="custom-radio">
                <input
                        name="{$field.name}"
                        id="field-{$field.name}-{$value}"
                        type="radio"
                        value="{$value}"
                        {if $field.required}required{/if}
                        {if $value eq $field.value} checked {/if}
                >
                <span></span>
              </span>
                            {$label}
                        </label>
                    {/foreach}
                {/block}

            {elseif $field.type === 'checkbox'}

                {block name='form_field_item_checkbox'}
                    <span class="custom-checkbox">
            <label>
              <input name="{$field.name}" type="checkbox" value="1"
                     {if $field.value}checked="checked"{/if} {if $field.required}required{/if}>
              <span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
              {$field.label nofilter}
            </label>
          </span>
                {/block}

            {elseif $field.type === 'date'}

                {block name='form_field_item_date'}
                    <input id="field-{$field.name}" name="{$field.name}" class="form-control" type="date"
                           value="{$field.value}"{*{if isset($field.availableValues.placeholder)} placeholder="{$field.availableValues.placeholder}"{/if}*}>
                    {if isset($field.availableValues.comment)}
                        <span class="form-control-comment">
              {$field.availableValues.comment}
            </span>
                    {/if}
                {/block}

            {elseif $field.type === 'birthday'}

                {block name='form_field_item_birthday'}
                    <div class="js-parent-focus">
                        {html_select_date
                        field_order=DMY
                        time={$field.value}
                        field_array={$field.name}
                        prefix=false
                        reverse_years=true
                        field_separator='<br>'
                        day_extra='class="form-control form-control-select"'
                        month_extra='class="form-control form-control-select"'
                        year_extra='class="form-control form-control-select"'
                        day_empty={l s='-- day --' d='Shop.Forms.Labels'}
                        month_empty={l s='-- month --' d='Shop.Forms.Labels'}
                        year_empty={l s='-- year --' d='Shop.Forms.Labels'}
                        start_year={'Y'|date}-100 end_year={'Y'|date}
                        }
                    </div>
                {/block}

            {elseif $field.type === 'password'}

                {block name='form_field_item_password'}
                    <div class="input-group js-parent-focus">
                        <input
                                id="field-{$field.name}"
                                class="form-control js-child-focus js-visible-password"
                                name="{$field.name}"
                                title="{l s='At least 5 characters long' d='Shop.Forms.Help'}"
                                aria-label="{l s='Password input of at least 5 characters' d='Shop.Forms.Help'}"
                                type="password"
                                {*placeholder="{$field.label}"*}
                                {if $field.autocomplete}autocomplete="{$field.autocomplete}"{/if}
                                value=""
                                pattern=".{literal}{{/literal}5,{literal}}{/literal}"
                                {if $field.required}required{/if}
                        >
                        <span class="input-group-btn">
              <button
                      class="btn"
                      type="button"
                      data-action="show-password"
                      data-text-show="{l s='Show' d='Shop.Theme.Actions'}"
                      data-text-hide="{l s='Hide' d='Shop.Theme.Actions'}"
              >
                {l s='Show' d='Shop.Theme.Actions'}
              </button>
            </span>
                    </div>
                    <span class="form-control-comment">
              {l s='At least 5 characters long' d='Shop.Forms.Help'}
            </span>
                {/block}

            {else}

                {block name='form_field_item_other'}
                    <input
                            id="field-{$field.name}"
                            class="form-control"
                            name="{$field.name}"
                            type="{$field.type}"
                            value="{$field.value}"
                            {if $field.autocomplete}autocomplete="{$field.autocomplete}"{/if}
                            {* PLANATEC {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder}"{/if}*}
                            {*placeholder="{$field.label}"*}
                            {if $field.maxLength}maxlength="{$field.maxLength}"{/if}
                            {if $field.required}required{/if}
                    >
                    {if isset($field.availableValues.comment)}
                        <span class="form-control-comment">
              {$field.availableValues.comment}
            </span>
                    {/if}
                {/block}

            {/if}

            {block name='form_field_errors'}
                {include file='_partials/form-errors.tpl' errors=$field.errors}
            {/block}

            {* <div class="form-control-comment">
                {block name='form_field_comment'}
                    {if (!$field.required && !in_array($field.type, ['radio-buttons', 'checkbox']))}
                        {l s='Optional' d='Shop.Forms.Labels'}
                    {/if}
                {/block}
            </div> *}

        </div>
    </div>
{/if}
