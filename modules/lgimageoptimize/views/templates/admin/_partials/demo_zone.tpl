{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}

{foreach from=$images key="type" item="image"}
    <div class="panel row">
        <h3>{$type|escape:'htmlall':'UTF-8'} ({$image['original']['size']|escape:'htmlall':'UTF-8'})</h3>
        <div class="image_container">
            <div class="image_inner">
                <div class="image_original">
                    <div class="image_inside">
                        <h4>{l s='Generated by prestashop' mod='lgimageoptimize'}</h4>
                        <span>{l s='Size file:' mod='lgimageoptimize'} {$image['original']['weight']|escape:'htmlall':'UTF-8'}</span>
                        <img src="{$image['original']['url']|escape:'html':'UTF-8'}?{rand()|escape:'htmlall':'UTF-8'}">
                    </div>
                </div>
                <div class="image_optimized">
                    <div class="image_inside">
                        <h4>{l s='Generated by module' mod='lgimageoptimize'}</h4>
                        <span>{l s='Size file:' mod='lgimageoptimize'} {$image['optimized']['weight']|escape:'htmlall':'UTF-8'}</span>
                        <img src="{$image['optimized']['url']|escape:'html':'UTF-8'}?{rand()|escape:'htmlall':'UTF-8'}">
                    </div>
                </div>
            </div>
        </div>
    </div>
{/foreach}
