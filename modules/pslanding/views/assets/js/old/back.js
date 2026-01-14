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
    return (s || '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
  }

  function render() {
    list.innerHTML = '';

    items.forEach((it, idx) => {
      const card = document.createElement('div');
      card.className = 'panel panel-default pslanding-char-card';
      card.dataset.index = idx;

      let tabs = '';
      let panes = '';

      languages.forEach((l, i) => {
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

      // events
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

  function syncHidden() {
    if (!hidden) return;
    hidden.value = JSON.stringify(items);
  }

  btnAdd.addEventListener('click', function () {
    items.push({ title: {}, text: {} });
    render();
  });

  render();
});


/* agregar diapositivas al carousel*/

document.addEventListener('DOMContentLoaded', function () {
  const root = document.getElementById('pslanding-slides');
  if (!root) return;

  const cfg = JSON.parse(root.getAttribute('data-config') || '{}');
  const list = document.getElementById('pslanding-slides-list');
  const btnAdd = document.getElementById('pslanding-add-slide');
  const inputJson = document.querySelector('input[name="slides_json"]');

  // normaliza items
  let slides = Array.isArray(cfg.items) ? cfg.items.map((s, i) => ({
    idx: i + 1, // idx estable para inputs file
    active: s.active ? 1 : 0,
    image: s.image || '',
    id_product: s.id_product || '',
    product_name: s.product_name || '',
  })) : [];

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  function render() {
    list.innerHTML = slides.map((s, pos) => {
      const preview = s.image ? `<img src="/modules/pslanding/uploads/${escapeHtml(s.image)}" style="max-width:120px;max-height:80px;display:block;margin-bottom:6px;">` : '';
      return `
        <div class="panel" style="padding:12px;margin-bottom:10px;">
          <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
            <div style="min-width:160px;">
              <label><strong>Imagen</strong></label>
              ${preview}
              <input type="file" name="slide_image_${s.idx}" accept="image/*">
              <input type="hidden" data-k="image" value="${escapeHtml(s.image)}">
            </div>

            <div style="flex:1;min-width:260px;">
              <label><strong>Producto</strong></label>
              <div style="display:flex;gap:8px;align-items:center;">
                <input type="text" class="form-control" placeholder="Buscar producto..." value="${escapeHtml(s.product_name)}"
                  data-action="product-search" data-idx="${s.idx}">
                <input type="hidden" data-k="id_product" value="${escapeHtml(s.id_product)}">
                <button type="button" class="btn btn-default" data-action="clear-product" data-idx="${s.idx}">Limpiar</button>
              </div>
              <div class="help-block">Selecciona un producto (se guardará el id_product).</div>
              <div data-action="results" data-idx="${s.idx}"></div>
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
    // Lee lo que hay en el DOM (por si el usuario cambió cosas)
    const panels = list.querySelectorAll('.panel');
    const out = [];

    panels.forEach((panel, i) => {
      const idxInput = panel.querySelector('input[type="file"]')?.name?.match(/slide_image_(\d+)/);
      const idx = idxInput ? parseInt(idxInput[1], 10) : (i + 1);

      const id_product = panel.querySelector('input[data-k="id_product"]')?.value || '';
      const image = panel.querySelector('input[data-k="image"]')?.value || '';
      const active = panel.querySelector('input[data-action="toggle-active"]')?.checked ? 1 : 0;

      out.push({
        idx,
        id_product: id_product ? parseInt(id_product, 10) : null,
        image,
        active
      });
    });

    inputJson.value = JSON.stringify(out);
  }

  async function searchProducts(q) {
    const url = new URL(cfg.ajax_url, window.location.origin);
    url.searchParams.set('ajax', '1');
    url.searchParams.set('action', 'searchProducts');
    url.searchParams.set('q', q);
    const res = await fetch(url.toString(), { credentials: 'same-origin' });
    return await res.json();
  }

  btnAdd.addEventListener('click', function () {
    const nextIdx = slides.length ? Math.max(...slides.map(s => s.idx)) + 1 : 1;
    slides.push({ idx: nextIdx, active: 1, image: '', id_product: '', product_name: '' });
    render();
  });

  // Delegación de eventos
  list.addEventListener('input', async function (e) {
    const el = e.target;
    if (el && el.dataset.action === 'product-search') {
      const idx = parseInt(el.dataset.idx, 10);
      const q = el.value.trim();
      const box = list.querySelector(`[data-action="results"][data-idx="${idx}"]`);
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
    }
  });

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

    if (action === 'clear-product' && idx != null) {
      const panel = btn.closest('.panel');
      panel.querySelector('input[data-k="id_product"]').value = '';
      panel.querySelector('input[data-action="product-search"]').value = '';
      const box = panel.querySelector('[data-action="results"]');
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
      const box = panel.querySelector('[data-action="results"]');
      if (box) box.innerHTML = '';
      syncJson();
      return;
    }

    if (action === 'toggle-active') {
      syncJson();
      return;
    }
  });

  // Al enviar el form, asegura JSON actualizado
  const form = root.closest('form');
  if (form) {
    form.addEventListener('submit', function () {
      syncJson();
    });
  }

  render();
});


/* Seleccionar campos según plantilla */
document.addEventListener('DOMContentLoaded', () => {
  const templateSelect = document.querySelector('select[name="template"]');
  if (!templateSelect) return;

  const applyVisibility = () => {
    const tpl = templateSelect.value; // "landing-default" / "landing-simple"
    // Mapea a clases
    const isDefault = tpl === 'landing-default';
    const isSimple  = tpl === 'landing-simple';

    document.querySelectorAll('.js-tpl').forEach(el => {
      // el es el form-group
      if (el.classList.contains('tpl-default')) {
        el.style.display = isDefault ? '' : 'none';
      } else if (el.classList.contains('tpl-simple')) {
        el.style.display = isSimple ? '' : 'none';
      } else {
        // si lo quieres "común" no le pongas js-tpl
      }
    });
  };

  templateSelect.addEventListener('change', applyVisibility);
  applyVisibility();
});

