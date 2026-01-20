/* =========================
   Características
========================= */
document.addEventListener('DOMContentLoaded', function () {
  console.log('Papi está aquí...');
  const root = document.getElementById('pslanding-characteristics');
  if (!root) return;

  const cfg = JSON.parse(root.getAttribute('data-config') || '{}');
  const items = Array.isArray(cfg.items) ? cfg.items : [];
  const languages = Array.isArray(cfg.languages) ? cfg.languages : [];
  const defaultLang = cfg.default_lang || (languages[0] && languages[0].id_lang) || 1;

  const list = document.getElementById('pslanding-characteristics-list');
  const btnAdd = document.getElementById('pslanding-add-characteristic');
  const hidden = document.querySelector('input[name="characteristics_json"]');

  function escapeHtml(s) {
    return (s || '').replace(/[&<>"']/g, m => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[m]));
  }

  function syncHidden() {
    if (!hidden) return;
    hidden.value = JSON.stringify(items);
  }

  function render() {
    list.innerHTML = '';

    items.forEach((it, idx) => {
      const card = document.createElement('div');
      card.className = 'panel panel-default pslanding-char-card';
      card.dataset.index = idx;

      let tabs = '';
      let panes = '';

      languages.forEach((l) => {
        const idLang = l.id_lang;
        const active = (idLang === defaultLang) ? 'active' : '';
        tabs += `<li class="${active}"><a href="#pslc_${idx}_${idLang}" data-toggle="tab">${escapeHtml(l.iso_code || l.name || idLang)}</a></li>`;

        const t = (it.title && it.title[idLang]) ? it.title[idLang] : '';
        const x = (it.text && it.text[idLang]) ? it.text[idLang] : '';

        panes += `
          <div class="tab-pane ${active}" id="pslc_${idx}_${idLang}">
            <div class="form-group">
              <label>Título</label>
              <input type="text" class="form-control pslanding-char-title" data-lang="${idLang}" value="${escapeHtml(t)}">
            </div>
            <div class="form-group">
              <label>Texto</label>
              <textarea class="form-control pslanding-char-text" data-lang="${idLang}" rows="3">${escapeHtml(x)}</textarea>
            </div>
          </div>
        `;
      });

      card.innerHTML = `
        <div class="panel-heading">
          <strong>Característica #${idx + 1}</strong>
          <div class="pull-right">
            <button type="button" class="btn btn-xs btn-danger pslanding-remove">Eliminar</button>
          </div>
          <div style="clear:both"></div>
        </div>
        <div class="panel-body">
          <ul class="nav nav-tabs">${tabs}</ul>
          <div class="tab-content" style="padding-top:10px">${panes}</div>
        </div>
      `;

      card.querySelector('.pslanding-remove').addEventListener('click', function () {
        items.splice(idx, 1);
        render();
        syncHidden();
      });

      card.querySelectorAll('.pslanding-char-title').forEach(inp => {
        inp.addEventListener('input', () => {
          const lang = parseInt(inp.dataset.lang, 10);
          it.title = it.title || {};
          it.title[lang] = inp.value;
          syncHidden();
        });
      });

      card.querySelectorAll('.pslanding-char-text').forEach(inp => {
        inp.addEventListener('input', () => {
          const lang = parseInt(inp.dataset.lang, 10);
          it.text = it.text || {};
          it.text[lang] = inp.value;
          syncHidden();
        });
      });

      list.appendChild(card);
    });

    syncHidden();
  }

  if (btnAdd) {
    btnAdd.addEventListener('click', function () {
      items.push({ title: {}, text: {} });
      render();
    });
  }

  render();
});


/* =========================
   Slides / Carousel
   - landing-default: producto
   - landing-simple : categoría
   - IMAGEN POR IDIOMA (tabs)
   - SIN textos
========================= */
document.addEventListener('DOMContentLoaded', function () {
  const root = document.getElementById('pslanding-slides');
  if (!root) return;

  const cfg = JSON.parse(root.getAttribute('data-config') || '{}');
  const list = document.getElementById('pslanding-slides-list');
  const btnAdd = document.getElementById('pslanding-add-slide');
  const inputJson = document.querySelector('input[name="slides_json"]');

  const languages = Array.isArray(cfg.languages) ? cfg.languages : [];
  const defaultLang = cfg.default_lang || (languages[0] && languages[0].id_lang) || 1;
  const templateSelect = document.querySelector('select[name="template"]');

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  function getTemplate() {
    return templateSelect ? templateSelect.value : (cfg.template || 'landing-default');
  }
  function isSimpleTemplate() {
    return getTemplate() === 'landing-simple';
  }

  function normalizeImagesMap(v) {
    // queremos {id_lang: filename}
    if (v && typeof v === 'object' && !Array.isArray(v)) return v;
    return {};
  }

  // Normaliza slides: images por idioma
  let slides = Array.isArray(cfg.items)
    ? cfg.items.map((s, i) => ({
        idx: i + 1,
        active: s.active ? 1 : 0,
        id_product: s.id_product || '',
        product_name: s.product_name || '',
        id_category: s.id_category || '',
        category_name: s.category_name || '',
        images: normalizeImagesMap(s.images), // <<--- viene del PHP getSlides()
      }))
    : [];

  function renderLinkBlock(s) {
    if (isSimpleTemplate()) {
      return `
        <label><strong>Categoría</strong></label>
        <div style="display:flex;gap:8px;align-items:center;">
          <input type="text" class="form-control" placeholder="Buscar categoría..." value="${escapeHtml(s.category_name)}"
            data-action="category-search" data-idx="${s.idx}">
          <input type="hidden" data-k="id_category" value="${escapeHtml(s.id_category)}">
          <button type="button" class="btn btn-default" data-action="clear-category" data-idx="${s.idx}">Limpiar</button>
        </div>
        <div class="help-block">Selecciona una categoría (se guardará el id_category).</div>
        <div data-action="results-category" data-idx="${s.idx}"></div>
      `;
    }

    return `
      <label><strong>Producto</strong></label>
      <div style="display:flex;gap:8px;align-items:center;">
        <input type="text" class="form-control" placeholder="Buscar producto..." value="${escapeHtml(s.product_name)}"
          data-action="product-search" data-idx="${s.idx}">
        <input type="hidden" data-k="id_product" value="${escapeHtml(s.id_product)}">
        <button type="button" class="btn btn-default" data-action="clear-product" data-idx="${s.idx}">Limpiar</button>
      </div>
      <div class="help-block">Selecciona un producto (se guardará el id_product).</div>
      <div data-action="results-product" data-idx="${s.idx}"></div>
    `;
  }

  function renderImageTabs(s) {
    if (!languages.length) {
      // Fallback: sin idiomas, usa defaultLang
      const old = (s.images && s.images[defaultLang]) ? s.images[defaultLang] : '';
      const preview = old
        ? `<img src="/modules/pslanding/uploads/${escapeHtml(old)}" style="max-width:180px;max-height:120px;display:block;margin-bottom:6px;">`
        : `<em>No hay imagen</em>`;

      return `
        <hr style="margin:12px 0;">
        <label><strong>Imagen del slide</strong></label>
        <div style="margin-top:6px;">
          ${preview}
          <input type="file" name="slide_image_${s.idx}_${defaultLang}" accept="image/*">
          <input type="hidden" data-k="image_${defaultLang}" value="${escapeHtml(old)}">
          <div class="help-block">Si no subes nada, se mantiene la imagen actual.</div>
        </div>
      `;
    }

    let tabs = '';
    let panes = '';

    languages.forEach((l) => {
      const idLang = l.id_lang;
      const active = (idLang === defaultLang) ? 'active' : '';
      const label = escapeHtml(l.iso_code || l.name || idLang);

      tabs += `<li class="${active}">
        <a href="#psls_img_${s.idx}_${idLang}" data-toggle="tab">${label}</a>
      </li>`;

      const old = (s.images && s.images[idLang]) ? s.images[idLang] : '';
      const preview = old
        ? `<img src="/modules/pslanding/uploads/${escapeHtml(old)}" style="max-width:180px;max-height:120px;display:block;margin-bottom:6px;">`
        : `<em>No hay imagen</em>`;

      panes += `
        <div class="tab-pane ${active}" id="psls_img_${s.idx}_${idLang}">
          <div style="margin-top:6px;">
            ${preview}
            <input type="file" name="slide_image_${s.idx}_${idLang}" accept="image/*">
            <input type="hidden" data-k="image_${idLang}" value="${escapeHtml(old)}">
            <div class="help-block">Idioma ${label}: si no subes nada, se mantiene la imagen actual.</div>
          </div>
        </div>
      `;
    });

    return `
      <hr style="margin:12px 0;">
      <label><strong>Imágenes por idioma</strong></label>
      <ul class="nav nav-tabs">${tabs}</ul>
      <div class="tab-content" style="padding-top:10px">${panes}</div>
    `;
  }

  function render() {
    list.innerHTML = slides.map((s) => {
      return `
        <div class="panel" style="padding:12px;margin-bottom:10px;" data-idx="${s.idx}">
          <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
            <div style="flex:1;min-width:360px;">
              ${renderLinkBlock(s)}
              ${renderImageTabs(s)}
            </div>

            <div style="min-width:180px;">
              <label><strong>Activo</strong></label>
              <div>
                <label style="display:inline-flex;gap:6px;align-items:center;">
                  <input type="checkbox" data-action="toggle-active" data-idx="${s.idx}" ${s.active ? 'checked' : ''}>
                  Mostrar
                </label>
              </div>

              <div style="margin-top:10px;">
                <button type="button" class="btn btn-danger" data-action="remove" data-idx="${s.idx}">Eliminar slide</button>
              </div>
            </div>
          </div>
        </div>
      `;
    }).join('');

    syncJson();
  }

  function syncJson() {
    const panels = list.querySelectorAll('.panel');
    const out = [];

    panels.forEach((panel, i) => {
      const idx = panel.dataset.idx ? parseInt(panel.dataset.idx, 10) : (i + 1);
      const active = panel.querySelector('input[data-action="toggle-active"]')?.checked ? 1 : 0;

      // Link target según template
      let id_product = null;
      let id_category = null;

      if (isSimpleTemplate()) {
        const raw = panel.querySelector('input[data-k="id_category"]')?.value || '';
        id_category = raw ? parseInt(raw, 10) : null;
      } else {
        const raw = panel.querySelector('input[data-k="id_product"]')?.value || '';
        id_product = raw ? parseInt(raw, 10) : null;
      }

      // Images map por idioma (desde hidden inputs)
      const images = {};
      const hiddenImgs = panel.querySelectorAll('input[type="hidden"][data-k^="image_"]');
      hiddenImgs.forEach(h => {
        const m = (h.dataset.k || '').match(/^image_(\d+)$/);
        if (!m) return;
        const lang = parseInt(m[1], 10);
        images[lang] = h.value || '';
      });

      out.push({
        idx,
        active,
        id_product,
        id_category,
        images
      });
    });

    if (inputJson) inputJson.value = JSON.stringify(out);
  }

  async function searchProducts(q) {
    const url = new URL(cfg.ajax_url, window.location.origin);
    url.searchParams.set('ajax', '1');
    url.searchParams.set('action', 'searchProducts');
    url.searchParams.set('q', q);
    const res = await fetch(url.toString(), { credentials: 'same-origin' });
    return await res.json();
  }

  async function searchCategories(q) {
    const url = new URL(cfg.ajax_url, window.location.origin);
    url.searchParams.set('ajax', '1');
    url.searchParams.set('action', 'searchCategories');
    url.searchParams.set('q', q);
    const res = await fetch(url.toString(), { credentials: 'same-origin' });
    return await res.json();
  }

  if (btnAdd) {
    btnAdd.addEventListener('click', function () {
      const nextIdx = slides.length ? Math.max(...slides.map(s => s.idx)) + 1 : 1;
      slides.push({
        idx: nextIdx,
        active: 1,
        id_product: '',
        product_name: '',
        id_category: '',
        category_name: '',
        images: {}, // sin imágenes al crear
      });
      render();
    });
  }

  // Inputs: búsquedas
  list.addEventListener('input', async function (e) {
    const el = e.target;
    if (!el || !el.dataset.action) return;

    const action = el.dataset.action;
    const idx = el.dataset.idx ? parseInt(el.dataset.idx, 10) : null;
    const q = (el.value || '').trim();

    if (action === 'product-search') {
      const box = list.querySelector(`[data-action="results-product"][data-idx="${idx}"]`);
      if (!box) return;

      if (q.length < 2) {
        box.innerHTML = '';
        syncJson();
        return;
      }

      const results = await searchProducts(q);
      box.innerHTML = `
        <div class="list-group" style="margin-top:6px;">
          ${results.map(r => `
            <button type="button" class="list-group-item" data-action="pick-product"
              data-idx="${idx}" data-id="${r.id_product}" data-name="${escapeHtml(r.name)}">
              #${r.id_product} - ${escapeHtml(r.name)}
            </button>
          `).join('')}
        </div>
      `;
      syncJson();
      return;
    }

    if (action === 'category-search') {
      const box = list.querySelector(`[data-action="results-category"][data-idx="${idx}"]`);
      if (!box) return;

      if (q.length < 2) {
        box.innerHTML = '';
        syncJson();
        return;
      }

      const results = await searchCategories(q);
      box.innerHTML = `
        <div class="list-group" style="margin-top:6px;">
          ${results.map(r => `
            <button type="button" class="list-group-item" data-action="pick-category"
              data-idx="${idx}" data-id="${r.id_category}" data-name="${escapeHtml(r.name)}">
              ${escapeHtml(r.label || r.name)}
            </button>
          `).join('')}
        </div>
      `;
      syncJson();
      return;
    }
  });

  // Clicks: pick/clear/remove/toggle
  list.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;

    const action = btn.dataset.action;
    const idx = btn.dataset.idx ? parseInt(btn.dataset.idx, 10) : null;

    if (action === 'remove' && idx != null) {
      slides = slides.filter(s => s.idx !== idx);
      render();
      return;
    }

    if (action === 'toggle-active') {
      syncJson();
      return;
    }

    // PRODUCT
    if (action === 'clear-product' && idx != null) {
      const panel = btn.closest('.panel');
      const idInput = panel.querySelector('input[data-k="id_product"]');
      const txtInput = panel.querySelector('input[data-action="product-search"]');
      if (idInput) idInput.value = '';
      if (txtInput) txtInput.value = '';
      const box = panel.querySelector('[data-action="results-product"]');
      if (box) box.innerHTML = '';
      syncJson();
      return;
    }

    if (action === 'pick-product' && idx != null) {
      const id = btn.dataset.id;
      const name = btn.dataset.name;
      const panel = btn.closest('.panel');
      panel.querySelector('input[data-k="id_product"]').value = id;
      panel.querySelector('input[data-action="product-search"]').value = name;
      const box = panel.querySelector('[data-action="results-product"]');
      if (box) box.innerHTML = '';
      syncJson();
      return;
    }

    // CATEGORY
    if (action === 'clear-category' && idx != null) {
      const panel = btn.closest('.panel');
      const idInput = panel.querySelector('input[data-k="id_category"]');
      const txtInput = panel.querySelector('input[data-action="category-search"]');
      if (idInput) idInput.value = '';
      if (txtInput) txtInput.value = '';
      const box = panel.querySelector('[data-action="results-category"]');
      if (box) box.innerHTML = '';
      syncJson();
      return;
    }

    if (action === 'pick-category' && idx != null) {
      const id = btn.dataset.id;
      const name = btn.dataset.name;
      const panel = btn.closest('.panel');
      panel.querySelector('input[data-k="id_category"]').value = id;
      panel.querySelector('input[data-action="category-search"]').value = name;
      const box = panel.querySelector('[data-action="results-category"]');
      if (box) box.innerHTML = '';
      syncJson();
      return;
    }
  });

  // Cambia template -> repinta y limpia campos incompatibles (no toca imágenes)
  if (templateSelect) {
    templateSelect.addEventListener('change', () => {
      if (isSimpleTemplate()) {
        slides = slides.map(s => ({ ...s, id_product: '', product_name: '' }));
      } else {
        slides = slides.map(s => ({ ...s, id_category: '', category_name: '' }));
      }
      render();
    });
  }

  // Submit -> JSON actualizado
  const form = root.closest('form');
  if (form) {
    form.addEventListener('submit', function () {
      syncJson();
    });
  }

  render();
});


/* =========================
   Mostrar/Ocultar campos según plantilla
========================= */
document.addEventListener('DOMContentLoaded', () => {
  const templateSelect = document.querySelector('select[name="template"]');
  if (!templateSelect) return;

  const applyVisibility = () => {
    const tpl = templateSelect.value;
    const isDefault = tpl === 'landing-default';
    const isSimple  = tpl === 'landing-simple';

    document.querySelectorAll('.js-tpl').forEach(el => {
      if (el.classList.contains('tpl-default')) {
        el.style.display = isDefault ? '' : 'none';
      } else if (el.classList.contains('tpl-simple')) {
        el.style.display = isSimple ? '' : 'none';
      }
    });
  };

  templateSelect.addEventListener('change', applyVisibility);
  applyVisibility();
});
