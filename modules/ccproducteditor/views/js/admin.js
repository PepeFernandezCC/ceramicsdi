/**
 * Editor masivo de productos.
 *
 * Los desplegables se pintan solo con la opción actual y se rellenan con todas las opciones
 * la primera vez que el usuario interactúa con ellos: con 100 productos x 70 características
 * pintar todas las opciones de golpe dejaría el navegador bloqueado.
 */
(function () {
  'use strict';

  // Columnas de campos que quedan fijas al hacer scroll horizontal (además de Activo e ID)
  var STICKY_FIELDS = ['reference', 'name'];

  var app;
  var config;
  var table;
  var state = {
    loaded: false,
    rows: {},          // id => fila recibida del servidor
    order: [],
    features: [],      // [[id_feature, nombre]]
    featureValues: {}, // id_feature => [[id_value, texto]]
    choices: {},       // lista => [[id, texto]]
    page: 1,
    pages: 1,
    dirty: {},         // id => {fields: {}, features: {}}
    selected: {},      // id => true (productos marcados para edición masiva)
    lastSelected: null // índice en state.order del último clic, para seleccionar rangos con Mayús
  };

  // Campos que identifican a cada producto: no tiene sentido darles el mismo valor a varios
  var BULK_EXCLUDED_FIELDS = ['reference', 'name', 'link_rewrite', 'ean13'];

  document.addEventListener('DOMContentLoaded', function () {
    app = document.getElementById('ccpe-app');
    if (!app) {
      return;
    }
    config = JSON.parse(app.getAttribute('data-config'));
    table = document.getElementById('ccpe-table');

    var mode = document.getElementById('ccpe-mode');
    mode.addEventListener('change', toggleFilterFields);
    toggleFilterFields();

    app.addEventListener('click', onClick);
    table.addEventListener('mousedown', onSelectInteraction);
    table.addEventListener('focusin', onSelectInteraction);
    table.addEventListener('input', onEdit);
    table.addEventListener('change', onEdit);

    document.getElementById('ccpe-lang').addEventListener('change', function () {
      if (state.loaded && confirmDiscard()) {
        loadProducts(1);
      }
    });

    var bulkTarget = document.getElementById('ccpe-bulk-target');
    if (bulkTarget) {
      bulkTarget.addEventListener('change', renderBulkValue);
      document.getElementById('ccpe-bulk-mode').addEventListener('change', renderBulkValue);
    }

    var importFile = document.getElementById('ccpe-import-file');
    if (importFile) {
      importFile.addEventListener('change', function () {
        if (importFile.files.length) {
          importCsv(importFile.files[0]);
        }
        importFile.value = '';
      });
    }

    window.addEventListener('beforeunload', function (event) {
      if (dirtyCount() > 0) {
        event.preventDefault();
        event.returnValue = '';
      }
    });
  });

  /* ---------- Filtros ---------- */

  function toggleFilterFields() {
    var mode = document.getElementById('ccpe-mode').value;
    var showCategory = mode === 'category' || mode === 'category_range';
    var showRange = mode === 'range' || mode === 'category_range';
    app.querySelectorAll('.ccpe-when-category').forEach(function (el) { el.hidden = !showCategory; });
    app.querySelectorAll('.ccpe-when-range').forEach(function (el) { el.hidden = !showRange; });
  }

  function filterParams() {
    var form = document.getElementById('ccpe-filters');
    var params = new URLSearchParams();
    ['id_lang', 'mode', 'id_category', 'id_from', 'id_to', 'per_page'].forEach(function (name) {
      params.set(name, form.elements[name].value);
    });
    params.set('subcategories', form.elements.subcategories.checked ? '1' : '0');

    return params;
  }

  function selectedLanguageName() {
    var select = document.getElementById('ccpe-lang');

    return select.options[select.selectedIndex].text;
  }

  /* ---------- Acciones ---------- */

  function onClick(event) {
    var button = event.target.closest('[data-ccpe-action]');
    if (button) {
      event.preventDefault();
      var action = button.getAttribute('data-ccpe-action');
      if (action === 'load' && confirmDiscard()) {
        loadProducts(1);
      } else if (action === 'page' && confirmDiscard()) {
        loadProducts(parseInt(button.getAttribute('data-page'), 10));
      } else if (action === 'save') {
        saveProducts();
      } else if (action === 'export') {
        exportCsv();
      } else if (action === 'import') {
        if (confirmDiscard()) {
          document.getElementById('ccpe-import-file').click();
        }
      } else if (action === 'bulk-apply') {
        applyBulk();
      } else if (action === 'bulk-clear') {
        setSelection(state.order, false);
      }
      return;
    }

    if (event.target.id === 'ccpe-select-all') {
      setSelection(state.order, event.target.checked);
      return;
    }

    if (event.target.classList.contains('ccpe-select')) {
      onSelectRow(event.target, event.shiftKey);
      return;
    }

    var assign = event.target.closest('.ccpe-assign');
    if (assign) {
      var td = assign.closest('td');
      td.innerHTML = featureSelectHtml(null) + addButtonHtml();
      var select = td.querySelector('select');
      populate(select);
      select.focus();
      refreshCell(td);
      return;
    }

    var add = event.target.closest('.ccpe-add');
    if (add) {
      add.insertAdjacentHTML('beforebegin', featureSelectHtml(null));
      var added = add.previousElementSibling;
      populate(added);
      added.focus();
    }
  }

  function request(params, body) {
    var url = config.url + '&ajax=1&' + params.toString();

    return fetch(url, { method: 'POST', body: body || new FormData(), credentials: 'same-origin' })
      .then(function (response) {
        // Si la sesión del admin ha caducado, PrestaShop redirige al login y fetch() sigue la redirección
        if (response.redirected && response.url.indexOf('controller=AdminLogin') !== -1) {
          throw new Error('Tu sesión del back-office ha caducado. Recarga la página (F5) o vuelve a iniciar sesión e inténtalo de nuevo.');
        }
        return response.text().then(function (text) {
          try {
            return JSON.parse(text);
          } catch (e) {
            throw new Error('Respuesta inesperada del servidor: ' + text.replace(/<[^>]+>/g, ' ').trim().substring(0, 500));
          }
        });
      });
  }

  /**
   * @param {number} page
   * @param {Array} [afterMessage] argumentos de showMessage() a mostrar tras recargar
   */
  function loadProducts(page, afterMessage) {
    var params = filterParams();
    params.set('action', 'loadProducts');
    params.set('page', page);
    setBusy(true, 'Cargando productos…');

    request(params).then(function (data) {
      state.loaded = true;
      state.rows = {};
      state.order = [];
      state.dirty = {};
      state.selected = {};
      state.lastSelected = null;
      state.features = data.features;
      state.featureValues = data.featureValues;
      state.choices = data.choices;
      state.page = data.page;
      state.pages = data.pages;
      data.rows.forEach(function (row) {
        state.rows[row.id] = row;
        state.order.push(row.id);
      });
      document.getElementById('ccpe-total').textContent = data.total;
      renderTable();
      renderPagination();
      renderBulkTargets();
      updateToolbar();
      if (afterMessage) {
        showMessage.apply(null, afterMessage);
      } else {
        clearMessages();
      }
    }).catch(function (error) {
      showMessage('danger', error.message);
    }).then(function () {
      setBusy(false);
    });
  }

  function saveProducts() {
    if (app.querySelector('tr.ccpe-row-dirty .ccpe-invalid')) {
      showMessage('danger', 'Hay campos con formato incorrecto (marcados en rojo). Corrígelos antes de guardar.');
      return;
    }
    var payload = Object.keys(state.dirty).map(function (id) {
      return { id: parseInt(id, 10), fields: state.dirty[id].fields, features: state.dirty[id].features };
    });
    if (!payload.length) {
      return;
    }

    var params = new URLSearchParams({ action: 'saveProducts', id_lang: document.getElementById('ccpe-lang').value });
    var body = new FormData();
    body.append('payload', JSON.stringify(payload));
    setBusy(true, 'Guardando ' + payload.length + ' productos…');

    request(params, body).then(function (data) {
      if (data.success) {
        state.dirty = {};
        loadProducts(state.page, ['success', data.message]);
      } else {
        showMessage('danger', data.message, data.errors);
        setBusy(false);
      }
    }).catch(function (error) {
      showMessage('danger', error.message);
      setBusy(false);
    });
  }

  function exportCsv() {
    var params = filterParams();
    params.set('action', 'exportCsv');
    window.location.href = config.url + '&ajax=1&' + params.toString();
  }

  function importCsv(file) {
    if (!window.confirm('Se importará «' + file.name + '» usando el idioma ' + selectedLanguageName()
        + ' para nombres, etiquetas, URL y valores de características.\n\n¿Continuar?')) {
      return;
    }
    var params = new URLSearchParams({ action: 'importCsv', id_lang: document.getElementById('ccpe-lang').value });
    var body = new FormData();
    body.append('csv', file);
    setBusy(true, 'Importando «' + file.name + '»… no cierres esta página.');

    request(params, body).then(function (data) {
      var details = data.summary ? [data.summary] : null;
      var link = data.logUrl ? { url: data.logUrl, text: 'Descargar informe' } : null;
      var message = [data.success ? 'success' : 'danger', data.message, details, link];
      setBusy(false);
      if (data.success && state.loaded) {
        state.dirty = {};
        loadProducts(state.page, message);
      } else {
        showMessage.apply(null, message);
      }
    }).catch(function (error) {
      setBusy(false);
      showMessage('danger', error.message);
    });
  }

  /* ---------- Render ---------- */

  function stickyClass(key) {
    return STICKY_FIELDS.indexOf(key) !== -1 ? 'ccpe-sticky ' : '';
  }

  function renderTable() {
    var head = '<tr>';
    if (config.canEdit) {
      head += '<th class="ccpe-sticky ccpe-col-select"><input type="checkbox" id="ccpe-select-all"'
        + ' title="Seleccionar todos los productos de esta página"></th>';
    }
    head += '<th class="ccpe-sticky ccpe-col-active">' + escapeHtml(config.fields.active.label) + '</th>';
    head += '<th class="ccpe-sticky ccpe-col-id">ID</th>';
    Object.keys(config.fields).forEach(function (key) {
      if (key !== 'active') {
        head += '<th class="' + stickyClass(key) + 'ccpe-col-' + key + '">' + escapeHtml(config.fields[key].label) + '</th>';
      }
    });
    state.features.forEach(function (feature) {
      head += '<th class="ccpe-col-feature" title="ID ' + feature[0] + '">' + escapeHtml(feature[1]) + '</th>';
    });
    head += '</tr>';
    table.tHead.innerHTML = head;
    table.classList.toggle('ccpe-selectable', config.canEdit);

    if (!state.order.length) {
      table.tBodies[0].innerHTML = '<tr><td class="ccpe-empty" colspan="99">No hay productos con estos filtros.</td></tr>';
      return;
    }

    var html = '';
    state.order.forEach(function (id) {
      html += rowHtml(state.rows[id]);
    });
    table.tBodies[0].innerHTML = html;
  }

  function rowHtml(row) {
    var disabled = config.canEdit ? '' : ' disabled';
    var html = '<tr data-id="' + row.id + '">';
    if (config.canEdit) {
      html += '<td class="ccpe-sticky ccpe-col-select"><input type="checkbox" class="ccpe-select"'
        + ' title="Seleccionar para edición masiva (Mayús + clic selecciona un rango)"></td>';
    }
    html += '<td class="ccpe-sticky ccpe-col-active" data-key="active"><label class="ccpe-switch">'
      + '<input type="checkbox" data-field="active"' + (row.active === '1' ? ' checked' : '') + disabled + '><span></span></label></td>';
    html += '<td class="ccpe-sticky ccpe-col-id"><a href="' + productLink(row.id) + '" target="_blank" rel="noopener">' + row.id + '</a></td>';

    Object.keys(config.fields).forEach(function (key) {
      if (key === 'active') {
        return;
      }
      var field = config.fields[key];
      var value = row[key];
      html += '<td' + (STICKY_FIELDS.indexOf(key) !== -1 ? ' class="ccpe-sticky ccpe-col-' + key + '"' : '') + ' data-key="' + key + '">';
      if (field.type === 'choice') {
        html += '<select class="form-control input-sm ccpe-lazy" data-field="' + key + '" data-list="' + field.choices + '"' + disabled + '>'
          + '<option value="' + escapeHtml(value) + '" selected>' + escapeHtml(choiceLabel(field.choices, value)) + '</option></select>';
      } else {
        var lock = disabled || (key === 'quantity' && row.has_combinations ? ' disabled title="Producto con combinaciones"' : '');
        var mode = field.type === 'decimal' ? ' inputmode="decimal"' : (field.type === 'int' ? ' inputmode="numeric"' : '');
        html += '<input type="text" class="form-control input-sm" data-field="' + key + '" value="' + escapeHtml(value) + '"'
          + mode + (field.max ? ' maxlength="' + field.max + '"' : '') + lock + '>';
      }
      html += '</td>';
    });

    state.features.forEach(function (feature) {
      var values = row.features[feature[0]] || [];
      html += '<td class="ccpe-feature" data-feature="' + feature[0] + '">';
      if (!values.length) {
        html += assignButtonHtml();
      } else {
        values.forEach(function (value) {
          html += featureSelectHtml(value);
        });
        html += addButtonHtml();
      }
      html += '</td>';
    });

    return html + '</tr>';
  }

  function featureSelectHtml(value) {
    var disabled = config.canEdit ? '' : ' disabled';
    var option = value
      ? '<option value="' + value[0] + '" selected>' + escapeHtml(value[1]) + (value[2] ? ' ✎' : '') + '</option>'
      : '<option value="" selected>— sin valor —</option>';

    return '<select class="form-control input-sm ccpe-lazy ccpe-fv"' + disabled + '>' + option + '</select>';
  }

  function assignButtonHtml() {
    return config.canEdit ? '<button type="button" class="btn btn-default btn-xs ccpe-assign">Asignar</button>' : '';
  }

  function addButtonHtml() {
    return config.canEdit ? '<button type="button" class="ccpe-add" title="Añadir otro valor a esta característica">+</button>' : '';
  }

  function renderPagination() {
    var container = document.getElementById('ccpe-pagination');
    if (state.pages <= 1) {
      container.innerHTML = '';
      return;
    }
    container.innerHTML =
      '<button type="button" class="btn btn-default btn-sm" data-ccpe-action="page" data-page="' + (state.page - 1) + '"'
        + (state.page <= 1 ? ' disabled' : '') + '>&laquo;</button>'
      + '<span>Página ' + state.page + ' de ' + state.pages + '</span>'
      + '<button type="button" class="btn btn-default btn-sm" data-ccpe-action="page" data-page="' + (state.page + 1) + '"'
        + (state.page >= state.pages ? ' disabled' : '') + '>&raquo;</button>';
  }

  /* ---------- Desplegables perezosos ---------- */

  function onSelectInteraction(event) {
    if (event.target.matches && event.target.matches('select.ccpe-lazy')) {
      populate(event.target);
    }
  }

  function populate(select) {
    if (select.getAttribute('data-populated')) {
      return;
    }
    var current = select.value;
    var options;
    if (select.classList.contains('ccpe-fv')) {
      var td = select.closest('td');
      var row = state.rows[select.closest('tr').getAttribute('data-id')];
      var idFeature = td.getAttribute('data-feature');
      // Valores personalizados del propio producto (✎) + valores predefinidos de la característica
      var custom = (row.features[idFeature] || []).filter(function (value) { return value[2]; })
        .map(function (value) { return [value[0], value[1] + ' ✎']; });
      options = [['', '— sin valor —']].concat(custom, state.featureValues[idFeature] || []);
    } else {
      options = state.choices[select.getAttribute('data-list')] || [];
    }

    select.innerHTML = optionsHtml(options);
    select.value = current;
    select.setAttribute('data-populated', '1');
  }

  function optionsHtml(options) {
    var html = '';
    options.forEach(function (option) {
      html += '<option value="' + escapeHtml(String(option[0])) + '">' + escapeHtml(option[1] === '' ? '—' : option[1]) + '</option>';
    });

    return html;
  }

  function choiceLabel(list, id) {
    var options = state.choices[list] || [];
    for (var i = 0; i < options.length; i++) {
      if (String(options[i][0]) === String(id)) {
        return options[i][1] === '' ? '—' : options[i][1];
      }
    }

    return id;
  }

  /* ---------- Edición y cambios pendientes ---------- */

  function onEdit(event) {
    var target = event.target;
    if (target.classList.contains('ccpe-select')) {
      return;
    }
    if (target.getAttribute('data-field') === 'link_rewrite' && target.value !== target.value.toLowerCase()) {
      var position = target.selectionStart;
      target.value = target.value.toLowerCase();
      target.setSelectionRange(position, position);
    }
    var td = target.closest('td');
    if (td && td.closest('tbody')) {
      refreshCell(td);
    }
  }

  function refreshCell(td) {
    var tr = td.closest('tr');
    var id = tr.getAttribute('data-id');
    var row = state.rows[id];
    var diff = state.dirty[id] || { fields: {}, features: {} };
    var changed;

    if (td.classList.contains('ccpe-feature')) {
      var idFeature = td.getAttribute('data-feature');
      var current = featureIds(td);
      var original = (row.features[idFeature] || []).map(function (value) { return value[0]; }).sort(numeric);
      changed = current.join(',') !== original.join(',');
      if (changed) {
        diff.features[idFeature] = current;
      } else {
        delete diff.features[idFeature];
      }
    } else {
      var input = td.querySelector('[data-field]');
      if (!input) {
        return;
      }
      var key = input.getAttribute('data-field');
      var value = input.type === 'checkbox' ? (input.checked ? '1' : '0') : input.value;
      td.classList.toggle('ccpe-invalid', !isValid(key, value));
      changed = value !== String(row[key]);
      if (changed) {
        diff.fields[key] = value;
      } else {
        delete diff.fields[key];
      }
    }

    td.classList.toggle('ccpe-cell-dirty', changed);
    if (Object.keys(diff.fields).length || Object.keys(diff.features).length) {
      state.dirty[id] = diff;
      tr.classList.add('ccpe-row-dirty');
    } else {
      delete state.dirty[id];
      tr.classList.remove('ccpe-row-dirty');
    }
    updateToolbar();
  }

  function featureIds(td) {
    var ids = [];
    td.querySelectorAll('select').forEach(function (select) {
      var value = parseInt(select.value, 10);
      if (value && ids.indexOf(value) === -1) {
        ids.push(value);
      }
    });

    return ids.sort(numeric);
  }

  function isValid(key, value) {
    var field = config.fields[key];
    value = value.trim();
    switch (field.type) {
      case 'decimal':
        return /^\d+(\.\d{1,6})?$/.test(value);
      case 'int':
        return /^-?\d+$/.test(value);
      case 'ean13':
        return /^\d{0,13}$/.test(value);
      case 'link_rewrite':
        return value !== '' && !/[\s\/?#&%"'<>]/.test(value);
      case 'text':
        return !field.required || value !== '';
      default:
        return true;
    }
  }

  function dirtyCount() {
    return Object.keys(state.dirty).length;
  }

  function confirmDiscard() {
    return dirtyCount() === 0
      || window.confirm('Hay ' + dirtyCount() + ' productos con cambios sin guardar que se perderán. ¿Continuar?');
  }

  function updateToolbar() {
    var count = dirtyCount();
    var save = app.querySelector('[data-ccpe-action="save"]');
    if (save) {
      save.disabled = count === 0;
    }
    document.getElementById('ccpe-dirty-count').textContent = count ? count + ' con cambios sin guardar' : '';
    updateBulk();
  }

  /* ---------- Selección y edición masiva ---------- */

  /*
   * La edición masiva no habla con el servidor: escribe el valor en las celdas de los productos
   * seleccionados y llama a refreshCell(), igual que si se hubiera editado a mano. Así el cambio
   * queda pendiente (amarillo), se valida y se guarda con «Guardar cambios» como cualquier otro.
   */

  function onSelectRow(checkbox, range) {
    var index = indexOfId(checkbox.closest('tr').getAttribute('data-id'));
    var ids = [state.order[index]];
    if (range && state.lastSelected !== null) {
      ids = state.order.slice(Math.min(index, state.lastSelected), Math.max(index, state.lastSelected) + 1);
    }
    state.lastSelected = index;
    setSelection(ids, checkbox.checked);
  }

  function setSelection(ids, selected) {
    ids.forEach(function (id) {
      var tr = rowElement(id);
      if (selected) {
        state.selected[id] = true;
      } else {
        delete state.selected[id];
      }
      tr.classList.toggle('ccpe-row-selected', selected);
      tr.querySelector('.ccpe-select').checked = selected;
    });
    updateBulk();
  }

  function selectedIds() {
    return state.order.filter(function (id) { return state.selected[id]; });
  }

  function updateBulk() {
    var bulk = document.getElementById('ccpe-bulk');
    if (!bulk) {
      return;
    }
    var count = selectedIds().length;
    bulk.hidden = count === 0;
    document.getElementById('ccpe-bulk-count').textContent = count === 1 ? '1 producto seleccionado' : count + ' productos seleccionados';
    bulk.querySelectorAll('[data-ccpe-action]').forEach(function (button) {
      button.disabled = app.classList.contains('ccpe-busy');
    });

    var all = document.getElementById('ccpe-select-all');
    if (all) {
      all.checked = count > 0 && count === state.order.length;
      all.indeterminate = count > 0 && count < state.order.length;
    }
  }

  function renderBulkTargets() {
    var select = document.getElementById('ccpe-bulk-target');
    if (!select) {
      return;
    }
    var previous = select.value;
    var html = '<option value="">— Campo o característica —</option><optgroup label="Campos">';
    Object.keys(config.fields).forEach(function (key) {
      if (BULK_EXCLUDED_FIELDS.indexOf(key) === -1) {
        html += '<option value="field:' + key + '">' + escapeHtml(config.fields[key].label) + '</option>';
      }
    });
    html += '</optgroup><optgroup label="Características">';
    state.features.forEach(function (feature) {
      html += '<option value="feature:' + feature[0] + '">' + escapeHtml(feature[1]) + '</option>';
    });
    select.innerHTML = html + '</optgroup>';
    // Se conserva la elección al recargar (p. ej. al guardar y pasar a la página siguiente)
    select.value = previous;
    if (select.selectedIndex === -1) {
      select.value = '';
    }
    renderBulkValue();
  }

  function bulkTarget() {
    var value = document.getElementById('ccpe-bulk-target').value;
    if (!value) {
      return null;
    }
    var parts = value.split(':');

    return { type: parts[0], key: parts[1], value: value };
  }

  /** Pinta el control del valor según el destino: desplegable para listas/características, texto para el resto */
  function renderBulkValue() {
    var target = bulkTarget();
    var mode = document.getElementById('ccpe-bulk-mode');
    var container = document.getElementById('ccpe-bulk-value');
    var previous = container.firstElementChild && target && container.getAttribute('data-target') === target.value
      ? container.firstElementChild.value
      : null;
    var html = '';

    mode.hidden = !target || target.type !== 'feature';
    if (target && target.type === 'feature') {
      var empty = mode.value === 'replace' ? [['', '— sin valor (quitar todos) —']] : [];
      html = '<select class="form-control input-sm">' + optionsHtml(empty.concat(state.featureValues[target.key] || [])) + '</select>';
    } else if (target) {
      var field = config.fields[target.key];
      if (field.type === 'bool') {
        html = '<select class="form-control input-sm">' + optionsHtml([['1', 'Sí'], ['0', 'No']]) + '</select>';
      } else if (field.type === 'choice') {
        html = '<select class="form-control input-sm">' + optionsHtml(state.choices[field.choices] || []) + '</select>';
      } else {
        html = '<input type="text" class="form-control input-sm" placeholder="Nuevo valor"'
          + (field.max ? ' maxlength="' + field.max + '"' : '') + '>';
      }
    }
    container.innerHTML = html;
    container.setAttribute('data-target', target ? target.value : '');

    var control = container.firstElementChild;
    if (control && previous !== null) {
      control.value = previous;
      if (control.tagName === 'SELECT' && control.selectedIndex === -1) {
        control.selectedIndex = 0;
      }
    }
  }

  function applyBulk() {
    var target = bulkTarget();
    var control = document.getElementById('ccpe-bulk-value').firstElementChild;
    if (!target || !control) {
      showMessage('warning', 'Elige el campo o la característica que quieres cambiar.');
      return;
    }
    var ids = selectedIds();
    var value = control.value;
    var label = document.getElementById('ccpe-bulk-target').selectedOptions[0].text;
    var skipped = 0;

    if (target.type === 'field') {
      if (!isValid(target.key, value)) {
        showMessage('danger', 'El valor «' + value + '» no es válido para «' + label + '».');
        return;
      }
      ids.forEach(function (id) {
        var td = rowElement(id).querySelector('td[data-key="' + target.key + '"]');
        var input = td.querySelector('[data-field]');
        if (input.disabled) {
          skipped++;
          return;
        }
        if (input.type === 'checkbox') {
          input.checked = value === '1';
        } else {
          if (input.tagName === 'SELECT') {
            populate(input);
          }
          input.value = value;
        }
        refreshCell(td);
      });
    } else {
      var mode = document.getElementById('ccpe-bulk-mode').value;
      if (value === '' && mode !== 'replace') {
        showMessage('warning', 'La característica «' + label + '» no tiene valores predefinidos.');
        return;
      }
      var text = control.selectedOptions[0].text;
      ids.forEach(function (id) {
        applyFeatureValue(rowElement(id).querySelector('td[data-feature="' + target.key + '"]'), mode, value, text);
      });
    }

    var applied = ids.length - skipped;
    showMessage('success', '«' + label + '» modificado en ' + applied + (applied === 1 ? ' producto' : ' productos')
      + (skipped ? ' (' + skipped + ' omitidos por tener combinaciones)' : '')
      + '. Revisa los cambios y pulsa «Guardar cambios» para guardarlos.');
  }

  function applyFeatureValue(td, mode, idValue, text) {
    if (mode === 'replace') {
      td.innerHTML = idValue ? featureSelectHtml([idValue, text]) + addButtonHtml() : assignButtonHtml();
    } else if (mode === 'add') {
      if (featureIds(td).indexOf(parseInt(idValue, 10)) !== -1) {
        return;
      }
      if (td.querySelector('select')) {
        td.querySelector('.ccpe-add').insertAdjacentHTML('beforebegin', featureSelectHtml([idValue, text]));
      } else {
        td.innerHTML = featureSelectHtml([idValue, text]) + addButtonHtml();
      }
    } else {
      td.querySelectorAll('select').forEach(function (select) {
        if (select.value === idValue) {
          select.remove();
        }
      });
      if (!td.querySelector('select')) {
        td.innerHTML = assignButtonHtml();
      }
    }
    refreshCell(td);
  }

  function rowElement(id) {
    return table.querySelector('tbody tr[data-id="' + id + '"]');
  }

  function indexOfId(id) {
    for (var i = 0; i < state.order.length; i++) {
      if (String(state.order[i]) === String(id)) {
        return i;
      }
    }

    return -1;
  }

  /* ---------- Utilidades ---------- */

  function setBusy(busy, text) {
    app.classList.toggle('ccpe-busy', busy);
    if (busy) {
      app.querySelectorAll('[data-ccpe-action]').forEach(function (button) { button.disabled = true; });
      showMessage('info', text);
      return;
    }
    // Al terminar se recalcula qué botones deben estar activos
    app.querySelectorAll('.ccpe-filters [data-ccpe-action], [data-ccpe-action="export"], [data-ccpe-action="import"]')
      .forEach(function (button) { button.disabled = false; });
    updateToolbar();
    renderPagination();
  }

  function showMessage(type, text, list, link) {
    var html = '<div class="alert alert-' + type + '">' + escapeHtml(text);
    if (list && list.length) {
      html += '<ul>' + list.map(function (item) { return '<li>' + escapeHtml(item) + '</li>'; }).join('') + '</ul>';
    }
    if (link) {
      html += ' <a href="' + escapeHtml(link.url) + '" class="alert-link">' + escapeHtml(link.text) + '</a>';
    }
    document.getElementById('ccpe-messages').innerHTML = html + '</div>';
  }

  function clearMessages() {
    document.getElementById('ccpe-messages').innerHTML = '';
  }

  function productLink(id) {
    return escapeHtml(config.productUrl.replace('{id}', id));
  }

  function numeric(a, b) {
    return a - b;
  }

  function escapeHtml(value) {
    return String(value === null || value === undefined ? '' : value)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
})();
