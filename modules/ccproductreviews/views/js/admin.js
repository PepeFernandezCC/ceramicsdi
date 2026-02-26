document.addEventListener('DOMContentLoaded', function () {
  var lb = document.getElementById('ccpr_admin_lightbox');
  var lbImg = document.getElementById('ccpr_admin_lightbox_img');
  if (!lb || !lbImg) return;

  function openLightbox(src) {
    lbImg.src = src;
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

  // Click en miniaturas
  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('.js-ccpr-admin-lightbox');
    if (!a) return;
    e.preventDefault();
    openLightbox(a.getAttribute('data-full') || a.getAttribute('href'));
  });

  // Cerrar (backdrop o botón)
  lb.addEventListener('click', function (e) {
    if (e.target && e.target.hasAttribute('data-ccpr-close')) {
      closeLightbox();
    }
  });

  // ESC
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && lb.classList.contains('is-open')) {
      closeLightbox();
    }
  });
});