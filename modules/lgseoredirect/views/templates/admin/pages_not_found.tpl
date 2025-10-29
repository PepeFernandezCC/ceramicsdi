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

<div id="pagesnotfound" class="lgseoredirect-tabcontent">
    <fieldset>
        <legend>{l s='List of pages not found' mod='lgseoredirect'} (<span class="lgseoredirect_total_products">{$lgseoredirects_count_pages_not_found|intval}</span>)</legend>
        <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" id="lgseoredirect_pnf_list_form">
            <div>
                <table class="table" id="tablepnf" width="100%">
                    <thead>
                    <tr>
                        {*
                        <th></th>
                        <th>{l s='ID' mod='lgseoredirect'}</th>
                        *}
                        <th>{l s='PAGE NOT FOUND URL' mod='lgseoredirect'}</th>
                        <th></th>
                        <th>{l s='NEW URL' mod='lgseoredirect'}</th>
                        <th>{l s='TYPE' mod='lgseoredirect'}</th>
                        {*<th>{l s='DATE' mod='lgseoredirect'}</th>*}
                        <th></th>
                    </tr>
                    <tr>
                        {*
                        <th>
                            <b>{l s='All' mod='lgseoredirect'}</b><br>
                            <input type="checkbox" id="lgseoredirect_checkall" value="1" name="4">
                        </th>
                        <th>
                            <input type="text" name="filterid" id="filterid" style="width:50px;">
                        </th>
                        *}
                        <th>
                            <input type="text" name="filter_pnf_oldurl" id="filter_pnf_oldurl">
                        </th>
                        <th>
                        </th>
                        <th>
                            <input type="text" name="filter_pnf_newurl" id="filter_pnf_newurl">
                        </th>
                        <th>
                            <select name="filter_pnf_type" id="filter_pnf_type">
                                <option value="0"{if !isset($lgseoredirects_pnf_filters['type'])} selected{/if}>---</option>
                                <option value="301"{if isset($lgseoredirects_pnf_filters['type']) && $lgseoredirects_pnf_filters['type'] == 301} selected{/if}>301</option>
                                <option value="302"{if isset($lgseoredirects_pnf_filters['type']) && $lgseoredirects_pnf_filters['type'] == 302} selected{/if}>302</option>
                                <option value="303"{if isset($lgseoredirects_pnf_filters['type']) && $lgseoredirects_pnf_filters['type'] == 303} selected{/if}>303</option>
                            </select>
                        </th>
                        {*<th>*}
                            {*<input type="text" name="filter_pnf_date" id="filter_pnf_date">*}
                        {*</th>*}
                        <th>
                            {*<select name="filter_pnf_error" id="filter_pnf_error" style="width:120px;">*}
                                {*<option value="0" selected>---</option>*}
                                {*<option value="1">{l s='Duplicate redirects' mod='lgseoredirect'}</option>*}
                                {*<option value="2">{l s='Wrong redirects' mod='lgseoredirect'}</option>*}
                            {*</select>*}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {include './pages_not_found_rows.tpl'}
                    </tbody>
                </table>
            </div>
            <div class="lgseoredirect_pagination">
                {include './pagination.tpl' selected_pagination=$lgseoredirects_pnf_selected_pagination pagination=$lgseoredirects_pnf_pagination list_id=$lgseoredirects_pnf_list_id selected_pagination=$lgseoredirects_pnf_selected_pagination page=$lgseoredirects_pnf_page list_total=$lgseoredirects_pnf_list_total total_pages=$lgseoredirects_pnf_total_pages}
            </div>
            <br>
        </form>
    </fieldset>
</div>
