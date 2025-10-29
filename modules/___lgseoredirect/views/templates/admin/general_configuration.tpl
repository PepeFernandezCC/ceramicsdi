{**
 * Copyright 2025 LÍNEA GRÁFICA E.C.E S.L.
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

<div id="generalconfiguration" class="lgseoredirect-tabcontent">
    <form action="" method="post">
        <fieldset>
            <legend>
                {l s='General configuration' mod='lgseoredirect'}
                <a href="{$lg_path|escape:'htmlall':'UTF-8'}readme/readme_{l s='en' mod='lgseoredirect'}.pdf#page=6" target="_blank">
                    <img src="{$lg_path|escape:'htmlall':'UTF-8'}views/img/info.png">
                </a>
            </legend>
            <br>
            <h3>
                <label class="lgfloat">
                    &nbsp;&nbsp;{l s='Automatic redirections' mod='lgseoredirect'}&nbsp;&nbsp;
                </label>
                {* <span class="switch prestashop-switch fixed-width-lg lgfloat">
                    <input type="radio" name="lgseo_automatic_redirections" id="lgseo_automatic_redirections_on"
                           value="1" {if $LGSEO_AUTOMATIC_REDIRECTIONS}checked{/if} />
                    <label for="lgseo_automatic_redirections_on" class="lgbutton">{l s='Yes' mod='lgseoredirect'}</label>
                    <input type="radio" name="lgseo_automatic_redirections" id="lgseo_automatic_redirections_off"
                           value="0" {if !$LGSEO_AUTOMATIC_REDIRECTIONS}checked{/if} />
                    <label for="lgseo_automatic_redirections_off" class="lgbutton">{l s='No' mod='lgseoredirect'}</label>
                <a class="slide-button btn"></a>
                </span> *}
            </h3>
            <div class="lgclear"></div>
            <div class="alert alert-info">
                <u>{l s='Enable automatic redirection of 404 urls to your store\'s home page with 301 redirection.' mod='lgseoredirect'}</u>
                <br>
            </div>
            <button class="generalConfiguration button btn btn-default" type="submit" name="generalConfiguration">
                <i class="process-icon-save"></i> {l s='Save' mod='lgseoredirect'}
            </button>
        </fieldset>
    </form>
</div>
