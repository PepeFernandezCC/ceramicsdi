<div id="ccpe-app" data-config="{$ccpe_config|escape:'html':'UTF-8'}">
  <div class="panel">
    <div class="panel-heading"><i class="icon-filter"></i> Selección de productos</div>
    <form id="ccpe-filters" class="form-inline ccpe-filters" onsubmit="return false;">
      <div class="form-group">
        <label for="ccpe-lang">Idioma</label>
        <select id="ccpe-lang" name="id_lang" class="form-control">
          {foreach $ccpe_languages as $language}
            <option value="{$language.id_lang|intval}"{if $language.id_lang == $ccpe_id_lang} selected{/if}>{$language.name|escape:'html':'UTF-8'}</option>
          {/foreach}
        </select>
      </div>
      <div class="form-group">
        <label for="ccpe-mode">Productos</label>
        <select id="ccpe-mode" name="mode" class="form-control">
          <option value="all">Todos</option>
          <option value="category">Por categoría</option>
          <option value="range">Por rango de IDs</option>
          <option value="category_range">Rango de IDs dentro de una categoría</option>
        </select>
      </div>
      <div class="form-group ccpe-when-category">
        <label for="ccpe-category">Categoría</label>
        <select id="ccpe-category" name="id_category" class="form-control">
          {foreach $ccpe_categories as $category}
            <option value="{$category.id|intval}">{$category.label|escape:'html':'UTF-8'} (ID {$category.id|intval})</option>
          {/foreach}
        </select>
        <label class="ccpe-inline-check"><input type="checkbox" name="subcategories" value="1" checked> Incluir subcategorías</label>
      </div>
      <div class="form-group ccpe-when-range">
        <label for="ccpe-id-from">ID desde</label>
        <input id="ccpe-id-from" type="number" min="1" name="id_from" class="form-control ccpe-id-input">
        <label for="ccpe-id-to">hasta</label>
        <input id="ccpe-id-to" type="number" min="1" name="id_to" class="form-control ccpe-id-input">
      </div>
      <div class="form-group">
        <label for="ccpe-per-page">Por página</label>
        <select id="ccpe-per-page" name="per_page" class="form-control">
          {foreach $ccpe_per_page_options as $option}
            <option value="{$option|intval}"{if $option == 100} selected{/if}>{if $option}{$option|intval}{else}Todos{/if}</option>
          {/foreach}
        </select>
      </div>
      <button type="button" class="btn btn-primary" data-ccpe-action="load"><i class="icon-search"></i> Cargar productos</button>
    </form>
  </div>

  <div class="panel">
    <div class="panel-heading">
      <i class="icon-list"></i> Productos <span class="badge" id="ccpe-total">0</span>
      <span class="ccpe-dirty-count" id="ccpe-dirty-count"></span>
    </div>

    <div class="ccpe-toolbar">
      {if $ccpe_can_edit}
        <button type="button" class="btn btn-success" data-ccpe-action="save" disabled><i class="icon-save"></i> Guardar cambios</button>
      {/if}
      <button type="button" class="btn btn-default" data-ccpe-action="export"><i class="icon-download"></i> Exportar CSV</button>
      {if $ccpe_can_edit}
        <button type="button" class="btn btn-default" data-ccpe-action="import"><i class="icon-upload"></i> Importar CSV</button>
        <input type="file" id="ccpe-import-file" accept=".csv,text/csv" hidden>
      {/if}
      <div class="ccpe-pagination" id="ccpe-pagination"></div>
    </div>

    {if $ccpe_can_edit}
      <div class="ccpe-bulk" id="ccpe-bulk" hidden>
        <strong id="ccpe-bulk-count"></strong>
        <select id="ccpe-bulk-target" class="form-control input-sm" aria-label="Campo o característica a cambiar"></select>
        <select id="ccpe-bulk-mode" class="form-control input-sm" aria-label="Operación" hidden>
          <option value="replace">Reemplazar por</option>
          <option value="add">Añadir</option>
          <option value="remove">Quitar</option>
        </select>
        <span id="ccpe-bulk-value"></span>
        <button type="button" class="btn btn-primary btn-sm" data-ccpe-action="bulk-apply">Aplicar a los seleccionados</button>
        <button type="button" class="btn btn-link btn-sm" data-ccpe-action="bulk-clear">Quitar selección</button>
      </div>
    {/if}

    <div id="ccpe-messages"></div>

    <div class="ccpe-table-wrap">
      <table class="table ccpe-table" id="ccpe-table">
        <thead></thead>
        <tbody><tr><td class="ccpe-empty">Selecciona los productos y pulsa «Cargar productos».</td></tr></tbody>
      </table>
    </div>
  </div>
</div>
