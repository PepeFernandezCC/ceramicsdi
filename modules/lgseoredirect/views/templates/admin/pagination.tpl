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

{if !$simple_header}
<div class="row">
    <div class="col-lg-6">
        {* Choose number of results per page *}
        <div class="pagination">
            {l s='Display' mod='lgseoredirect'}
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                {if !$lgseoredirect_ps16}<span style="display: inline-block;">{/if}{$selected_pagination|intval}{if !$lgseoredirect_ps16}</span>{/if}
                {if $lgseoredirect_ps16}<i class="icon-caret-down"></i>{else}<div style="display: inline-block;" class="lgseoredirects-arrow-down"></div>{/if}
            </button>
            <ul class="dropdown-menu">
                {foreach $pagination AS $value}
                    <li>
                        <a href="javascript:void(0);" class="pagination-items-page" data-items="{$value|intval}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">{$value|intval}</a>
                    </li>
                {/foreach}
            </ul>
            / {$list_total|intval} {l s='result(s)' mod='lgseoredirect'}
            <input type="hidden" class="{$list_id|escape:'htmlall':'UTF-8'}-pagination-items-page" name="{$list_id|escape:'htmlall':'UTF-8'}_pagination" value="{$selected_pagination|intval}" />
            <input type="hidden" class="{$list_id|escape:'htmlall':'UTF-8'}-pagination-page" name="{$list_id|escape:'htmlall':'UTF-8'}_page" value="{$page|intval}" />
        </div>
        {if !$simple_header && $list_total > $pagination[0]}
        <ul class="pagination pull-right">
            <li {if $page <= 1}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" data-page="1" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">
                    {if $lgseoredirect_ps16}<i class="icon-double-angle-left"></i>{else}&laquo;{/if}
                </a>
            </li>
            <li {if $page <= 1}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" data-page="{$page|intval - 1}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">
                    {if $lgseoredirect_ps16}<i class="icon-angle-left"></i>{else}&lt;{/if}
                </a>
            </li>
            {assign p 0}
            {while $p++ < $total_pages}
                {if $p < $page-2}
                    <li class="disabled">
                        <a href="javascript:void(0);" style="font-size: 9.5px;">&hellip;</a>
                    </li>
                    {assign p $page-3}
                {elseif $p > $page+2}
                    <li class="disabled">
                        <a href="javascript:void(0);" style="font-size: 9.5px;">&hellip;</a>
                    </li>
                    {assign p $total_pages}
                {else}
                    <li {if $p == $page}class="active"{/if}>
                        <a href="javascript:void(0);" class="pagination-link" data-page="{$p|intval}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}" style="font-size: 9.5px;">{$p|intval}</a>
                    </li>
                {/if}
            {/while}
            <li {if $page >= $total_pages}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" data-page="{$page|intval + 1}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">
                    {if $lgseoredirect_ps16}<i class="icon-angle-right"></i>{else}&gt;{/if}
                </a>
            </li>
            <li {if $page >= $total_pages}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" data-page="{$total_pages|intval}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">
                    {if $lgseoredirect_ps16}<i class="icon-double-angle-right"></i>{else}&raquo;{/if}
                </a>
            </li>
        </ul>
        {/if}
    </div>
</div>
{/if}
