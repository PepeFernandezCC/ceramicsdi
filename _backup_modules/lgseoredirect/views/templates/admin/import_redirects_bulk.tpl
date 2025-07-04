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

<div id="bulkredirects" class="lgseoredirect-tabcontent">
    <fieldset>
        <legend>
            {l s='Import redirects in bulk' mod='lgseoredirect'}
            <a href="../modules/{$module_name|escape:'htmlall':'UTF-8'}/readme/readme_{l s='en' mod='lgseoredirect'}.pdf#page=6" target="_blank">
                <img src="../modules/{$module_name|escape:'htmlall':'UTF-8'}/views/img/info.png">
            </a>
        </legend>
        <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
            <br>
            <h3 style="padding: 0 0 0 5px;">
                <label>
                    <i class="icon-exclamation-triangle"></i>
                    {l s='You must respect the following rules to upload the redirects correctly:' mod='lgseoredirect'}
                </label>
            </h3>
            <table class="table" style="text-align:center; width:50%;" border="1">
                <tr>
                    <th style="text-align:center;" class="lgupper">{l s='Column' mod='lgseoredirect'} A</th>
                    <th style="text-align:center;" class="lgupper">{l s='Column' mod='lgseoredirect'} B</th>
                    <th style="text-align:center;" class="lgupper">{l s='Column' mod='lgseoredirect'} C</th>
                    <th style="text-align:center;" class="lgupper">{l s='Column' mod='lgseoredirect'} D</th>
                <tr>
                </tr>
                <td>
                    <span class="toolTip3">
                        <a href="#csv_uploader">{l s='Old URI' mod='lgseoredirect'}</a>
                        <p class="tooltipDesc3">
                            {l s='In the column A of your CSV file, write the old URI.' mod='lgseoredirect'}
                            <br>
                            <span class="lgunder">{l s='It must start with "/".' mod='lgseoredirect'}</span>
                        </p>
                    </span>
                </td>
                <td>
                    <span class="toolTip3">
                        <a href="#csv_uploader">{l s='New URL' mod='lgseoredirect'}</a>
                        <p class="tooltipDesc3">
                            {l s='In the column B of your CSV file, write the new URL.' mod='lgseoredirect'}
                            <br>
                            <span class="lgunder">{l s='It must start with "http" or "https".' mod='lgseoredirect'}</span>
                        </p>
                    </span>
                </td>
                <td>
                    <span class="toolTip3">
                        <a href="#csv_uploader">{l s='Redirect type' mod='lgseoredirect'}</a>
                        <p class="tooltipDesc3">
                            {l s='In the column C of your CSV file, add the type of redirect.' mod='lgseoredirect'}
                            <br>
                            <span class="lgunder">{l s='It must be "301", "302" or "303".' mod='lgseoredirect'}</span>
                        </p>
                    </span>
                </td>
                <td>
                    <span class="toolTip3">
                        <a href="#csv_uploader">{l s='Shop ID' mod='lgseoredirect'}</a>
                        <p class="tooltipDesc3">
                            {l s='In the column D, add the shop ID for which you want' mod='lgseoredirect'}
                            {l s='the old URI to apply.' mod='lgseoredirect'}
                            <br>
                            <span class="lgunder">{l s='Use "1" if you don\'t use the multistore' mod='lgseoredirect'}</span>
                        </p>
                    </span>
                </td>
                </tr>
            </table>
            <br>
            <div class="alert alert-info">
                - {l s='Move your mouse over the table to get more information.' mod='lgseoredirect'}<br>
                </a>
                - <a href="../modules/{$module_name|escape:'htmlall':'UTF-8'}/csv/redirects.csv">
                    {l s='Click here to download an example of CSV file' mod='lgseoredirect'}
                    {l s='(you can write your redirects directly in it)' mod='lgseoredirect'}
                </a>
            </div>
            <br><br>
            <h3 style="padding: 0 0 0 5px;">
                <span class="lgfloat lgsubtitle">
                    <label>
                        <i class="icon-file-excel-o"></i>
                        {l s='Select your file' mod='lgseoredirect'}
                    </label>
                </span>
            </h3>
            <span class="lgfloat fixed-width-xl lgmargin">
                <input type="file" name="csv" id="csv" class="btn btn-default lgfloat"><br>
            </span>
            <div class="alert alert-info">
                {l s='The file must be in.csv format and respect the structure indicated above' mod='lgseoredirect'}
                {l s='(4 columns and one redirect per line).' mod='lgseoredirect'}
            </div>
            <div class="lgclear"></div><br><br>
            <h3 style="padding: 0 0 0 5px;">
                <span class="lgfloat lgsubtitle" style="margin-right: 20px;">
                    <label>
                        <i class="icon-scissors"></i>
                        {l s='Indicate the separator of your CSV file (important)' mod='lgseoredirect'}
                    </label>
                </span>
            </h3>
            <select id="separator" class="lgfloat fixed-width-xl lgmargin" name="separator">
                <option value="1">{l s='Semi-colon' mod='lgseoredirect'}</option>
                <option value="2">{l s='Comma' mod='lgseoredirect'}</option>
            </select>
            <div class="alert alert-info">
                {l s='Open your csv file with a text editor ("Notepad" for example)' mod='lgseoredirect'}
                {l s='and check if the elements are separated with a semi-colon or comma.' mod='lgseoredirect'}
            </div>
            <div class="lgclear"></div><br>
            <div>
                <label from="newCSV"></label>
                <button class="button btn btn-default" type="submit" name="newCSV" style="float:left;">
                    <i class="process-icon-import"></i> {l s='Import the redirects' mod='lgseoredirect'}
                </button>
                <label from="deleteAll"></label>
                <button class="button btn btn-default" type="submit" style="float:right; margin-left:5px;" onclick="return confirm('{l s='Confirm' mod='lgseoredirect'}')" name="deleteAll">
                    <i class="icon-trash"></i> {l s='Delete all redirects' mod='lgseoredirect'}
                </button>
                <label from="export"></label>
                <button class="button btn btn-default" type="submit" name="export" style="float:right; margin-left:5px;">
                    <i class="icon-cloud-download"></i> {l s='Export all redirects' mod='lgseoredirect'}
                </button>
                <label from="pagesNotFound"></label>
                <button class="button btn btn-default" type="submit" name="pagesNotFound" style="float:right; margin-left:5px;">
                    <i class="icon-frown-o"></i> {l s='Pages not found' mod='lgseoredirect'}
                </button>
                <div style="clear:both;"></div>
            </div>
        </form>
    </fieldset>
</div>
{if isset($lgseoredirect_file_uploaded) && $lgseoredirect_file_uploaded == 1}
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
{/if}