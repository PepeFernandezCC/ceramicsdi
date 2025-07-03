{include file='./header.tpl'}

<div class="correos-content-gdpr">
    <h4 class="correos-title-gdpr">
        {l s='To use our module you must accept our conditions.' mod='correosoficial'}
    </h4>
    <form id="correos-form-gdpr" method="post" class="correos-form-gdpr">
        <div class="col-6 col-xs-6 px-4 correos-checks-gdpr">
            <div class="input-group correos-check-gdpr">
                <input type="checkbox" id="correos-gdpr-check" name="correos-gdpr-check" class="correos-input-gdpr" required>
                <label for="correos-gdpr-check" class="correos-text-gdpr">
                    {l s='I have read and accept the ' mod='correosoficial'}
                    <a href="{$co_base_dir}views/gdpr/condiciones_servicio.pdf" target="_blank">
                        {l s='terms and conditions' mod='correosoficial'}
                    </a>
                </label>
            </div>
            <div class="input-group correos-check-gdpr">
                <input type="checkbox" id="correos-dataProtect-check" name="correos-dataProtect-check" class="correos-input-gdpr" required>
                <label for="correos-betatester-check" class="correos-text-gdpr">
                {l s='I have read and accept the ' mod='correosoficial'}
                    <a href="{$co_base_dir}views/gdpr/proteccion_datos.pdf" target="_blank">
                        {l s='data protection policy.' mod='correosoficial'}
                    </a>
                </label>
            </div>
        </div>
        <div class="col-6 col-xs-6 correos-checks-button-gdpr">
            <button type="submit" class="btn btn-lg correos-button-gdpr">
                {l s='I ACCEPT' mod='correosoficial'}
            </button>
        </div>
    </form>
</div>