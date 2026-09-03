{extends file='page.tpl'}

{block name='page_content'}
  <section id="cc-incidencias" class="cc-incidencias-form">
    <h1>{$cc_t.page_title|escape:'htmlall':'UTF-8'}</h1>

    {if $cc_success}
      <div class="alert alert-success cc-success">
        <p>{$cc_t.success_message|escape:'htmlall':'UTF-8'}</p>
        <a class="cc-desistimiento-history-btn" href="{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}">{$cc_t.back_home|escape:'htmlall':'UTF-8'}</a>
      </div>
    {else}

      <p class="cc-intro">{$cc_t.intro|escape:'htmlall':'UTF-8'}</p>

      {if $cc_errors|@count}
        <div class="alert alert-danger">
          <ul>
            {foreach from=$cc_errors item=cc_error}
              <li>{$cc_error|escape:'htmlall':'UTF-8'}</li>
            {/foreach}
          </ul>
        </div>
      {/if}

      {if $cc_photos_dropped}
        <div class="alert alert-warning">{$cc_t.fotos_too_big_notice|escape:'htmlall':'UTF-8'}</div>
      {/if}

      <div class="alert alert-danger cc-client-errors" style="display:none"><ul></ul></div>

      <form action="{$cc_action|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" class="cc-form" novalidate>
        <input type="hidden" name="cc_ts" value="{$cc_ts|intval}">
        <div class="cc-hp-wrap" aria-hidden="true">
          <label for="cc_web">{$cc_t.honeypot_label|escape:'htmlall':'UTF-8'}</label>
          <input type="text" id="cc_web" name="cc_web" value="" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-group cc-field">
          <label for="cc_tipo"><span class="cc-required">{$cc_t.required_mark|escape:'htmlall':'UTF-8'}</span> {$cc_t.label_tipo|escape:'htmlall':'UTF-8'}</label>
          <select id="cc_tipo" name="tipo" class="form-control" required>
            <option value="">{$cc_t.tipo_placeholder|escape:'htmlall':'UTF-8'}</option>
            {foreach from=$cc_tipo_options item=cc_tipo_row}
              <option value="{$cc_tipo_row.id_ccincidencias_tipo|intval}" data-require-photos="{$cc_tipo_row.require_photos|intval}"{if isset($cc_old.tipo) && $cc_old.tipo == $cc_tipo_row.id_ccincidencias_tipo} selected{/if}>{$cc_tipo_row.descripcion|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
          </select>
        </div>

        {if $cc_locked_order}
          <input type="hidden" name="id_order" value="{$cc_locked_id_order|intval}">
          <div class="form-group cc-field">
            <label>{$cc_t.label_referencia|escape:'htmlall':'UTF-8'}</label>
            <p class="form-control-static cc-locked-reference"><strong>{$cc_locked_reference|escape:'htmlall':'UTF-8'}</strong></p>
          </div>
        {else}
          <div class="form-group cc-field">
            <label for="cc_referencia"><span class="cc-required">{$cc_t.required_mark|escape:'htmlall':'UTF-8'}</span> {$cc_t.label_referencia|escape:'htmlall':'UTF-8'}</label>
            <input type="text" id="cc_referencia" name="referencia" class="form-control" maxlength="40"
                   placeholder="{$cc_t.referencia_placeholder|escape:'htmlall':'UTF-8'}"
                   value="{if isset($cc_old.referencia)}{$cc_old.referencia|escape:'htmlall':'UTF-8'}{/if}" required>
            <small class="cc-help cc-referencia-warning" style="display:none">{$cc_t.referencia_warning|escape:'htmlall':'UTF-8'}</small>
          </div>
        {/if}

        <div class="form-group cc-field">
          <label for="cc_seguimiento">{$cc_t.label_seguimiento|escape:'htmlall':'UTF-8'}</label>
          <input type="text" id="cc_seguimiento" name="seguimiento" class="form-control" maxlength="60"
                 value="{if isset($cc_old.seguimiento)}{$cc_old.seguimiento|escape:'htmlall':'UTF-8'}{/if}">
        </div>

        <div class="form-group cc-field">
          <label for="cc_telefono">{$cc_t.label_telefono|escape:'htmlall':'UTF-8'}</label>
          <input type="tel" id="cc_telefono" name="telefono" class="form-control" maxlength="30"
                 value="{if isset($cc_old.telefono)}{$cc_old.telefono|escape:'htmlall':'UTF-8'}{/if}">
        </div>

        <div class="form-group cc-field cc-checkbox">
          <label>
            <input type="checkbox" name="es_muestra" value="1"{if isset($cc_old.es_muestra)} checked{/if}>
            {$cc_t.label_es_muestra|escape:'htmlall':'UTF-8'}
          </label>
        </div>

        <div class="form-group cc-field">
          <label for="cc_descripcion"><span class="cc-required">{$cc_t.required_mark|escape:'htmlall':'UTF-8'}</span> {$cc_t.label_descripcion|escape:'htmlall':'UTF-8'}</label>
          <textarea id="cc_descripcion" name="descripcion" class="form-control" rows="6" maxlength="4000"
                    placeholder="{$cc_t.descripcion_placeholder|escape:'htmlall':'UTF-8'}" required>{if isset($cc_old.descripcion)}{$cc_old.descripcion|escape:'htmlall':'UTF-8'}{/if}</textarea>
        </div>

        <div class="form-group cc-field" id="cc_fotos_group">
          <div class="cc-fotos-requirements">
            <p class="cc-fotos-requirements-title">
              <span class="cc-required" id="cc_fotos_required" style="display:none">{$cc_t.required_mark|escape:'htmlall':'UTF-8'}</span>
               {$cc_t.fotos_requirements_title|escape:'htmlall':'UTF-8'}
            </p>
            <ul>
              <li>{$cc_t.fotos_requirements_item1|escape:'htmlall':'UTF-8'}</li>
              <li>{$cc_t.fotos_requirements_item2|escape:'htmlall':'UTF-8'}</li>
              <li>{$cc_t.fotos_requirements_item3|escape:'htmlall':'UTF-8'}</li>
            </ul>
          </div>
         
          <input type="file" id="cc_fotos" name="fotos[]" multiple
                 accept=".jpg,.jpeg,.png,.webp,.pdf,.heic,.heif,image/jpeg,image/png,image/webp,application/pdf,image/heic,image/heif">
          <small class="cc-help">{$cc_t.fotos_help|escape:'htmlall':'UTF-8'}</small>
        </div>

        <div class="form-group cc-field cc-checkbox">
          <label>
            <input type="checkbox" name="consentimiento" value="1" required>
            {$cc_t.consentimiento_prefix|escape:'htmlall':'UTF-8'}<a href="{$cc_privacy_url|escape:'htmlall':'UTF-8'}" target="_blank" rel="noopener">{$cc_t.consentimiento_link_text|escape:'htmlall':'UTF-8'}</a>
          </label>
        </div>

        <div class="form-group cc-submit">
          <button type="submit" name="submitCcIncidencia" class="btn btn-primary">{$cc_t.btn_submit|escape:'htmlall':'UTF-8'}</button>
        </div>
      </form>
    {/if}
  </section>
{/block}
