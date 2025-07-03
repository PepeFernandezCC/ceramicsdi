{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}

{if isset($product_select)}
    <div>
        <h4>{$product_select->name|escape:'htmlall':'UTF-8'}</h4>
        <img src="{$url_image|escape:'html':'UTF-8'}">
    </div>
    <div class="panel-footer">
        <button id="generate_demo" class="btn btn-default">
            <i class="process-icon-save icon-refresh"></i>
            {l s='Sample generator' mod='lgimageoptimize'}
        </button>
    </div>
{/if}
