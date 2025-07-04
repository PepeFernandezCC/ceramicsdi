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

<div id="menubar">
    <fieldset>
        <a id="buttonindividualredirect" class="lgseoredirect_menubarbutton button btn btn-default" style="width:280px;">
            <i class="icon-plus-square"></i>&nbsp;{l s='Create a redirect' mod='lgseoredirect'}
        </a>
        <a id="buttonbulkredirects" class="lgseoredirect_menubarbutton button btn btn-default" style="width:280px;">
            <i class="icon-cloud-upload"></i>&nbsp;{l s='Import redirects in bulk' mod='lgseoredirect'}
        </a>
        <a id="buttonlistredirects" class="lgseoredirect_menubarbutton button btn btn-default" style="width:280px;">
            <i class="icon-list"></i>&nbsp;{l s='List of created redirects' mod='lgseoredirect'} (<span class="lgseoredirect_total_products">{$countredirects|intval}</span>)
        </a>
        {if isset($lgseoredirect_pagesnotfoundenabled) && $lgseoredirect_pagesnotfoundenabled}
            <a id="buttonpagesnotfound" class="lgseoredirect_menubarbutton button btn btn-default" style="width:280px;">
                <i class="icon-list"></i>&nbsp;{l s='pages not found' mod='lgseoredirect'} (<span class="lgseoredirect_total_products">{$lgseoredirects_count_pages_not_found|intval}</span>)
            </a>
        {/if}
    </fieldset>
</div>
