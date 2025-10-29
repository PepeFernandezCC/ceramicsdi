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

<div id="listredirects" class="lgseoredirect-tabcontent">
    <fieldset>
        <legend>{l s='List of created redirects' mod='lgseoredirect'} (<span class="lgseoredirect_total_products">{$countredirects|intval}</span>)</legend>
        <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" id="lgseoredirect_list_form">
            <div>
                <table class="table" id="tableredirect" width="100%">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{l s='ID' mod='lgseoredirect'}</th>
                        <th>{l s='OLD URL' mod='lgseoredirect'}</th>
                        <th></th>
                        <th>{l s='NEW URL' mod='lgseoredirect'}</th>
                        <th>{l s='TYPE' mod='lgseoredirect'}</th>
                        <th>{l s='DATE' mod='lgseoredirect'}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>
                            <b>{l s='All' mod='lgseoredirect'}</b><br>
                            <input type="checkbox" id="lgseoredirect_checkall" value="1" name="4">
                        </th>
                        <th>
                            <input type="text" name="filterid" id="filterid" style="width:50px;">
                        </th>
                        <th>
                            <input type="text" name="filteroldurl" id="filteroldurl">
                        </th>
                        <th>
                        </th>
                        <th>
                            <input type="text" name="filternewurl" id="filternewurl">
                        </th>
                        <th>
                            <select name="filtertype" id="filtertype">
                                <option value="0"{if !isset($filters['type'])} selected{/if}>---</option>
                                <option value="301"{if isset($filters['type']) && $filters['type'] == 301} selected{/if}>301</option>
                                <option value="302"{if isset($filters['type']) && $filters['type'] == 302} selected{/if}>302</option>
                                <option value="303"{if isset($filters['type']) && $filters['type'] == 303} selected{/if}>303</option>
                            </select>
                        </th>
                        <th>
                            <input type="text" name="filterdate" id="filterdate">
                        </th>
                        <th>
                            <select name="filtererror" id="filtererror" style="width:120px;">
                                <option value="0" selected>---</option>
                                <option value="1">{l s='Duplicate redirects' mod='lgseoredirect'}</option>
                                <option value="2">{l s='Wrong redirects' mod='lgseoredirect'}</option>
                            </select>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {include './list_rows.tpl'}
                    </tbody>
                </table>
            </div>
            <div class="lgseoredirect_pagination">
                {include './pagination.tpl'}
            </div>
            <br>
            <label from="deleteSelected"></label>
            <button class="button btn btn-default" type="button" name="lgseoredirect_deleteSelected" style="float:left;">
                <i class="icon-trash"></i> {l s='Delete selection' mod='lgseoredirect'}
            </button>
            <input type="button" id="lgseoredirect_clear_selection" name="lgseoredirect_clear_selection" value="{l s='Clear selection' mod='lgseoredirect'}" class="button btn btn-default">
            <input type="button" id="lgseoredirect_select_all_redirects" name="lgseoredirect_select_all_redirects" value="{l s='Select all redirects' mod='lgseoredirect'}" class="button btn btn-default">
        </form>
    </fieldset>
</div>
