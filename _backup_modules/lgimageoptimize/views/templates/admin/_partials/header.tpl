{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}

{if $active_function && $show_message}
<div class="alert alert-warning">
    {l s='Don\'t forget regenerate your images after active or setting your configuration' mod='lgimageoptimize'}
    <a href="{$url_regenerate_tools|escape:'html':'UTF-8'}">{l s='Click here to go to regenerate image tools' mod='lgimageoptimize'}</a>
</div>
{/if}
