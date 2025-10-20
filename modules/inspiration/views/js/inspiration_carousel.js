document.addEventListener('DOMContentLoaded', function () {
  // Inicializar Owl si existe jQuery y el plugin
  if (typeof $ !== 'undefined' && typeof $.fn.owlCarousel !== 'undefined') {
    $('.owl-carousel.inspiration-owl').owlCarousel({
    loop: true,
    margin: 12,
    nav: true,
    dots: false,
    center: true,          // 🔥 centra la imagen actual
    responsive: {
        0: {                 // móviles
        items: 1,
        stagePadding: 30,  // pequeño margen lateral
        margin: 10
        },
        576: { items: 2 },
        768: { items: 3 },
        992: { items: 4 },
        1200: { items: 5 }
    }
    });
  }

  // Lightbox/modal
  var modal = document.getElementById('inspirationModal');
  if (!modal) return;
  var modalImg = document.getElementById('inspirationModalImg');
  var closeBtn = modal.querySelector('.inspiration-modal__close');

  // Click en miniaturas: abrir modal con imagen grande
  document.querySelectorAll('.inspiration-thumb').forEach(function (el) {
    el.addEventListener('click', function () {
      var full = this.getAttribute('data-full');
      if (full) {
        modalImg.src = full;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
      }
    });
  });

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    modalImg.src = '';
  }
  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
});
