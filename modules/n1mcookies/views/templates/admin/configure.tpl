{* views/templates/admin/configure.tpl *}

<div class="panel">
    <h3>{$module_displayName|escape:'html':'UTF-8'}</h3>


<form action="{url entity='admin_module_configure' args=['configure=n1mcookies']}" method="post" class="defaultForm form-horizontal">
    <div class="alert alert-info">
        <p>{l s='Para obtener el código AW-XXXXXXXXX, sigue estos pasos:' mod='n1mcookies'}</p>
        <ol>
            <li>{l s='Accede a tu cuenta de Google Ads.' mod='n1mcookies'}</li>
            <li>{l s='Ve a la sección de "Configuración" > "Configuración de seguimiento".' mod='n1mcookies'}</li>
            <li>{l s='Haz clic en "Configuración de consentimiento".' mod='n1mcookies'}</li>
            <li>{l s='Copia el código de seguimiento que comienza con "AW-" y pégalo en el campo de abajo.' mod='n1mcookies'}</li>
        </ol>
    </div>
</div>
    <div class="form-group">
        <label class="control-label col-lg-3" for="N1MCOOKIES_CODE">
            {l s='Código AW-XXXXXXXXX:' mod='n1mcookies'}
        </label>
        <div class="col-lg-3">
            <input type="text" id="N1MCOOKIES_CODE" name="N1MCOOKIES_CODE" value="{$n1mcookies_code|escape:'html':'UTF-8'}" class="form-control" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3" for="N1MCOOKIES_PALETTE">
            {l s='Tema:' mod='n1mcookies'}
        </label>
        <div class="col-lg-9">
            <select id="N1MCOOKIES_PALETTE" name="N1MCOOKIES_PALETTE" class="form-control">
                <option value="dark" {if $n1mcookies_palette == 'dark'}selected{/if}>{l s='Oscuro' mod='n1mcookies'}</option>
                <option value="light" {if $n1mcookies_palette == 'light'}selected{/if}>{l s='Claro' mod='n1mcookies'}</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3" for="N1MCOOKIES_LANGUAGE">
            {l s='Idioma:' mod='n1mcookies'}
        </label>
        <div class="col-lg-9">
            <select id="N1MCOOKIES_LANGUAGE" name="N1MCOOKIES_LANGUAGE" class="form-control">
                <option value="en" {if $n1mcookies_language == 'en'}selected{/if}>{l s='Inglés' mod='n1mcookies'}</option>
                <option value="es" {if $n1mcookies_language == 'es'}selected{/if}>{l s='Español' mod='n1mcookies'}</option>
                <option value="de" {if $n1mcookies_language == 'de'}selected{/if}>{l s='Alemán' mod='n1mcookies'}</option>
                <option value="fr" {if $n1mcookies_language == 'fr'}selected{/if}>{l s='Francés' mod='n1mcookies'}</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-9 col-lg-offset-3">
            <button type="submit" name="submitn1mcookies" class="btn btn-default">
                {l s='Guardar cambios' mod='n1mcookies'}
            </button>
        </div>
    </div>
</form>
