{*
* Exportar Metadatos de Productos (CSV)
*}
<style>
    .pmc-wrapper {
        padding: 20px;
    }
    .pmc-field {
        margin-bottom: 20px;
        max-width: 320px;
    }
    .pmc-field label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
    }
</style>

<div class="panel">
    <div class="panel-heading">
        <i class="icon-file-text"></i>
        {l s='Exportar metadatos de productos' mod='productmetacsv'}
    </div>

    <div class="pmc-wrapper">
        <p>
            {l s='Selecciona un idioma y genera el CSV con los metadatos (Google/Facebook feed) de todos los productos activos en ese idioma.' mod='productmetacsv'}
        </p>

        <form method="post" action="">
            <div class="pmc-field">
                <label for="pmc_id_lang">{l s='Idioma' mod='productmetacsv'}</label>
                <select id="pmc_id_lang" name="pmc_id_lang" class="form-control">
                    {foreach from=$pmc_languages item=lang}
                        <option value="{$lang.id_lang}">{$lang.name}</option>
                    {/foreach}
                </select>
            </div>

            <button type="submit" name="submitPmcExport" class="btn btn-primary">
                <i class="icon-download"></i>
                {l s='Generar metadatos y descargar CSV' mod='productmetacsv'}
            </button>
        </form>
    </div>
</div>
