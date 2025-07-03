{**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *}

{if !empty($redirects)}
    {foreach $redirects as $redirect}
    <tr id="{$redirect['id']|intval}">
        <td>
            <input type="checkbox" name="selected_redirects[]" value="{$redirect['id']|intval}">
        </td>
        <td>
            <span id="redid{$redirect['id']|intval}">{$redirect['id']|intval}</span>
        </td>
        <td style="direction: ltr !important;">
            <span id="oldurl{$redirect['id']|intval}">
            {if $redirect['error_startwith']}
            {* check if the old URI starts with a / *}
                <input type="hidden" name="wrongformat{$redirect['id']|intval}" class="wrongformat{$redirect['id']|intval}" value="2">
                <span class="toolTip1">
                    <img src="../modules/lgseoredirect/views/img/important.png" />
                    <p class="tooltipDesc1">{l s='Wrong format: the old URI must start with a "/"' mod='lgseoredirect'}</p>
                </span>
            {/if}
            {* check if the old URI is duplicated *}
            {if $redirect['error_checkduplicate'] > 1}
                    <input type="hidden" name="duplicate{$redirect['id']|intval}" class="duplicate{$redirect['id']|intval}" value="1">
                    <span class="toolTip2">
                        <img src="../modules/lgseoredirect/views/img/important2.png" />
                        <p class="tooltipDesc2">{l s='Duplicated redirects: several redirects exist for this old URI.' mod='lgseoredirect'}</p>
                    </span>
            </div>
            {/if}
            {* OLD URI *}
                <div class="lgseoredirect-origin-url-text">
                {if isset($lgseoredirect_is_rtl) AND $lgseoredirect_is_rtl}
                    <a href="{$lgseoredirect_shop_domain|escape:'htmlall':'UTF-8'}{$lgseoredirect_shop_uri|escape:'htmlall':'UTF-8'}{$redirect['url_old']|escape:'htmlall':'UTF-8'}" style="direction: ltr !important;" target="_blank">
                        <span style="font-weight: normal; color: #aaaaaa;">{$lgseoredirect_shop_domain|escape:'htmlall':'UTF-8'}{$lgseoredirect_shop_uri|escape:'htmlall':'UTF-8'}</span><span style="font-weight:bold;">{$redirect['url_old']|escape:'htmlall':'UTF-8'}</span>
                    </a>
                {else}
                    <a href="{$lgseoredirect_shop_domain|escape:'htmlall':'UTF-8'}{$lgseoredirect_shop_uri|escape:'htmlall':'UTF-8'}{$redirect['url_old']|escape:'htmlall':'UTF-8'}" target="_blank">
                        <span style="font-weight: normal; color: #aaaaaa;">{$lgseoredirect_shop_domain|escape:'htmlall':'UTF-8'}{$lgseoredirect_shop_uri|escape:'htmlall':'UTF-8'}</span><span style="font-weight:bold;">{$redirect['url_old']|escape:'htmlall':'UTF-8'}</span>
                    </a>
                {/if}
                </div>
                <div class="lgseoredirect-origin-url-edit-container" style="display: none; direction: ltr !important;">
                    <span style="font-weight: normal; color: #aaaaaa;">{$lgseoredirect_shop_domain|escape:'htmlall':'UTF-8'}{$lgseoredirect_shop_uri|escape:'htmlall':'UTF-8'}</span><input type="text" name="lgseoredirect-origin-url-input-{$redirect['id']|intval}" value="{$redirect['url_old']|escape:'htmlall':'UTF-8'}">
                </div>
            {if $redirect['error_checkduplicate'] > 1}
                <span class="autofilter" data-url="{$redirect['url_old']|escape:'htmlall':'UTF-8'}" style="cursor: pointer">
                    <img src="../modules/lgseoredirect/views/img/filter.png" />
                    <p class="tooltipDesc2">{l s='Duplicated redirects: several redirects exist for this old URI.' mod='lgseoredirect'}</p>
                </span>
            {/if}
            </span>
        </td>
        <td style="font-size:x-large;">{if isset($lgseoredirect_is_rtl) AND $lgseoredirect_is_rtl}&larr;{else}&rarr;{/if}</td>
        <td style="direction: ltr !important;">
            {* NEW URL *}
            <div class="lgseoredirect-target-url-text">
                <div style="display: inline-block;">
                    {* check if the new URL starts with a http or https *}
                    <span id="newurl{$redirect['id']|intval}">
                    {if $redirect['error_startwith2']}
                        <input type="hidden" name="wrongformat{$redirect['id']|intval}" class="wrongformat{$redirect['id']|intval}" value="2">
                        <span class="toolTip1" class="wrongformat{$redirect['id']|intval}">
                            <img src="../modules/lgseoredirect/views/img/important.png" />
                            <p class="tooltipDesc1">{l s='Wrong format: the new URL must start with a "http" or "https".' mod='lgseoredirect'}</p>
                        </span>
                    {/if}
                    {$redirect['url_new']|escape:'htmlall':'UTF-8'}
                    </span>
                </div>
            </div>
            <div class="lgseoredirect-target-url-edit-container" style="display: none;">
                <input type="text" name="lgseoredirect-target-url-input-{$redirect['id']|intval}" value="{$redirect['url_new']|escape:'htmlall':'UTF-8'}">
            </div>
        </td>
        <td>
            <input type="hidden" name="type{$redirect['id']|intval}" id="type{$redirect['id']|intval}" value="{$redirect['redirect_type']|escape:'htmlall':'UTF-8'}">
            {* check if the type of redirect starts is 301, 302 or 303*}
            {if $redirect['error_wrong_redirect_type']}
                <input type="hidden" name="wrongformat{$redirect['id']|intval}" class="wrongformat{$redirect['id']|intval}" value="2">
                <span class="toolTip1" class="wrongformat{$redirect['id']|intval}">
                    <img src="../modules/lgseoredirect/views/img/important.png" />
                    <p class="tooltipDesc1">{l s='Wrong format: the type of redirect must be "301", "302" or "303".' mod='lgseoredirect'}</p>
                </span>
            {/if}
            {* TYPE - DATE -DELETE *}
            <div class="lgseoredirect-target-type-text"{if !isset($redirect['url_old'])} style="display: none;"{/if}>
                {$redirect['redirect_type']|escape:'htmlall':'UTF-8'}
            </div>
            <div class="lgseoredirect-target-type-edit-container"{if isset($redirect['url_old'])} style="display: none;{/if}" data-old-value="{if isset($redirect['redirect_type'])}{$redirect['redirect_type']|escape:'htmlall':'UTF-8'}{/if}">
                <select name="lgseoredirect-target-type-select-{$redirect['id']|intval}">
                    <option value="0"{if !isset($lgseoredirects_filters['type'])} selected{/if}>---</option>
                    <option value="301"{if isset($redirect['redirect_type']) && $redirect['redirect_type'] == 301} selected{else}{if isset($lgseoredirects_filters['type']) && $lgseoredirects_filters['type'] == 301} selected{/if}{/if}>301</option>
                    <option value="302"{if isset($redirect['redirect_type']) && $redirect['redirect_type'] == 302} selected{else}{if isset($lgseoredirects_filters['type']) && $lgseoredirects_filters['type'] == 302} selected{/if}{/if}>302</option>
                    <option value="303"{if isset($redirect['redirect_type']) && $redirect['redirect_type'] == 303} selected{else}{if isset($lgseoredirects_filters['type']) && $lgseoredirects_filters['type'] == 303} selected{/if}{/if}>303</option>
                </select>
            </div>
        </td>
        <td>
            <span id="date{$redirect['id']|intval}">{$redirect['fecha']|escape:'htmlall':'UTF-8'}
        </td>
        <td>
            <button class="button btn btn-primary editRedirect" type="button" data-id="{$redirect['id']|intval}">
                <i class="icon-edit"></i> {l s='Edit' mod='lgseoredirect'}
            </button>
            <button class="button btn btn-success saveRedirect" type="button" data-id="{$redirect['id']|intval}" data-old-value="{$redirect['url_new']|escape:'htmlall':'UTF-8'}" data-old-origin-value="{$redirect['url_old']|escape:'htmlall':'UTF-8'}" style="display: none;">
                <i class="icon-check"></i> {l s='Save' mod='lgseoredirect'}
            </button>
            <button class="button btn btn-warning cancelRedirect" type="button" data-id="{$redirect['id']|intval}" data-old-value="{$redirect['url_new']|escape:'htmlall':'UTF-8'}" data-old-origin-value="{$redirect['url_old']|escape:'htmlall':'UTF-8'}"  data-old-type="{if isset($redirect['redirect_type'])}{$redirect['redirect_type']|escape:'htmlall':'UTF-8'}{/if}" style="display: none;">
                <i class="icon-rotate-left"></i> {l s='Cancel' mod='lgseoredirect'}
            </button>
            <button class="button btn btn-danger deleteRedirect" type="button" data-id="{$redirect['id']|intval}">
                <i class="icon-trash"></i> {l s='Delete' mod='lgseoredirect'}
            </button>
        </td>
    </tr>
    {/foreach}
{else}
    <tr><td colspan="8" class="lgseoredirects_no_results"><i class="icon-warning-sign"></i> &nbsp;{l s='No results found' mod='lgseoredirect'}</td></tr>
{/if}
