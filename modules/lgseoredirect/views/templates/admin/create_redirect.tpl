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

<div id="individualredirect" class="lgseoredirect-tabcontent">
    <fieldset>
        <legend>
            {l s='Create a redirect' mod='lgseoredirect'}
            &nbsp;
            <a href="../modules/{$module_name|escape:'htmlall':'UTF-8'}/readme/readme_{l s='en' mod='lgseoredirect'}.pdf#page=4" target="_blank">
                <img src="../modules/{$module_name|escape:'htmlall':'UTF-8'}/views/img/info.png">
            </a>
        </legend>
        <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
            <table class="table" style="width:95%">
                <tr>
                    <td style="width:15%">
                        <label from="url_old">{l s='Old URL:' mod='lgseoredirect'}</label>
                    </td>
                    <td style="width:20%">
                        <div style="line-height:25px;">{$lgseoredirect_shop_domain|escape:'htmlall':'UTF-8'}{$lgseoredirect_shop_uri|escape:'htmlall':'UTF-8'}</div>
                    </td>
                    <td style="width:65%">
                        <input type="text" name="url_old" id="url_old" value="" placeholder="{l s='/.../old-page' mod='lgseoredirect'}" style="width:99%">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label from="url_new">{l s='New URL:' mod='lgseoredirect'}</label>
                    </td>
                    <td colspan="2">
                        <input type="text" name="url_new" id="url_new" value="" placeholder="{l s='http://www.domain.com/.../new-page' mod='lgseoredirect'}" style="width:99%">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label from="url_new">{l s='Type:' mod='lgseoredirect'}</label>
                    </td>
                    <td colspan="2">
                        <select name="type">
                            <option value="301">
                                301 - {l s='URL moved PERMANENTLY' mod='lgseoredirect'}
                            </option>
                            <option value="302">
                                302 - {l s='URL moved TEMPORARILY' mod='lgseoredirect'}
                            </option>
                            <option value="303">
                                303 - {l s='GET method used to retrieve information' mod='lgseoredirect'}
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                </tr>
            </table>
            <br>
            <div>
                <label from="newRedirect"></label>
                <button class="button btn btn-default" type="submit" name="newRedirect" style="float:left;">
                    <i class="process-icon-new"></i> {l s='Create the redirect' mod='lgseoredirect'}
                </button>
                <label from="deleteAll"></label>
                <button class="button btn btn-default" type="submit" style="float:right; margin-left:5px;" onclick="return confirm('{l s='Confirm' mod='lgseoredirect'}')" name="deleteAll">
                    <i class="icon-trash"></i> {l s='Delete all redirects' mod='lgseoredirect'}
                </button>
                <label from="export"></label>
                <button class="button btn btn-default" type="submit" name="export" style="float:right; margin-left:5px;">
                    <i class="icon-cloud-download"></i> {l s='Export all redirects' mod='lgseoredirect'}
                </button>
                {if isset($lgseoredirect_pagesnotfoundenabled) && $lgseoredirect_pagesnotfoundenabled}
                <label from="pagesNotFound"></label>
                <button class="button btn btn-default" type="submit" name="pagesNotFound" style="float:right; margin-left:5px;">
                    <i class="icon-frown-o"></i> {l s='Pages not found' mod='lgseoredirect'}
                </button>
                {/if}
                <div style="clear:both;"></div>
            </div>
        </form>
    </fieldset>
</div>
