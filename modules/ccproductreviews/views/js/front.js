document.addEventListener('DOMContentLoaded', function () {

  var btn = document.getElementById('ccpr_submit');
  if (!btn) return;

  // Leer config desde hidden inputs
  var idProductEl = document.getElementById('ccpr_id_product');
  var submitUrlEl = document.getElementById('ccpr_submit_url');
  var maxFilesEl  = document.getElementById('ccpr_max_files');
  var tokenEl     = document.getElementById('ccpr_token');

  if (!idProductEl || !submitUrlEl) {
    console.error('CCPR: missing hidden inputs (ccpr_id_product / ccpr_submit_url).');
    return;
  }

  var idProduct = idProductEl.value;
  var submitUrl = submitUrlEl.value;
  var maxFiles  = parseInt(maxFilesEl ? maxFilesEl.value : '3', 10) || 3;
  var token     = tokenEl ? tokenEl.value : '';

  btn.addEventListener('click', function () {

    showMessage('', '');

    var fd = new FormData();
    fd.append('id_product', idProduct);

    // rating radios (estrellas)
    var ratingEl = document.querySelector('input[name="ccpr_rating"]:checked');
    fd.append('rating', ratingEl ? ratingEl.value : '1');

    // comment
    var commentEl = document.querySelector('[name=ccpr_comment]');
    fd.append('comment', commentEl ? commentEl.value : '');

    // token
    if (token) fd.append('token', token);

    // photos
    var input = document.querySelector('[name=ccpr_photos]');
    var files = input ? input.files : null;

    if (files && files.length > maxFiles) {
      showMessage('Máximo ' + maxFiles + ' fotos.', 'error');
      return;
    }

    if (files) {
      for (var i = 0; i < files.length; i++) {
        fd.append('photos[]', files[i]);
      }
    }

    fetch(submitUrl, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (json) {
        if (json && json.ok) showMessage(json.message || 'Reseña enviada.', 'success');
        else showMessage((json && json.error) ? json.error : 'Error enviando reseña.', 'error');
      })
      .catch(function () {
        showMessage('Error enviando reseña.', 'error');
      });

  });

  function showMessage(msg, type) {
    var box = document.getElementById('ccpr_msg');
    if (!box) return;

    box.textContent = msg || '';
    box.classList.remove('is-success', 'is-error', 'is-info');

    if (!msg) {
      box.style.display = 'none';
      return;
    }

    box.style.display = 'block';
    if (type) box.classList.add('is-' + type);
  }

  // Lightbox
  (function () {
    var lb = document.getElementById('ccpr_lightbox');
    var lbImg = document.getElementById('ccpr_lightbox_img');
    if (!lb || !lbImg) return;

    function openLightbox(src, alt) {
      lbImg.src = src;
      lbImg.alt = alt || '';
      lb.classList.add('is-open');
      lb.setAttribute('aria-hidden', 'false');
      document.documentElement.style.overflow = 'hidden';
    }

    function closeLightbox() {
      lb.classList.remove('is-open');
      lb.setAttribute('aria-hidden', 'true');
      lbImg.src = '';
      document.documentElement.style.overflow = '';
    }

    // Delegación: cualquier click en una foto
    document.addEventListener('click', function (e) {
      var a = e.target.closest && e.target.closest('.js-ccpr-lightbox');
      if (!a) return;

      e.preventDefault();
      var src = a.getAttribute('data-full') || a.getAttribute('href');
      var img = a.querySelector('img');
      openLightbox(src, img ? img.alt : '');
    });

    // Cerrar por click en backdrop o botón
    lb.addEventListener('click', function (e) {
      if (e.target && e.target.hasAttribute('data-ccpr-close')) {
        closeLightbox();
      }
    });

    // Cerrar con ESC
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && lb.classList.contains('is-open')) {
        closeLightbox();
      }
    });
  })();

});