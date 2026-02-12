(function () {
  if (!window.CBQMETA) return;

  function safeTrack(eventName, payload) {
    try {
      if (typeof window.cbq === 'function') {
        window.cbq('track', eventName, payload || {});
      }
    } catch (e) {}
  }

  if (CBQMETA.controller === 'cart') {
    safeTrack('InitiateCheckout', { currency: CBQMETA.currency });
  }

  if (window.prestashop && typeof window.prestashop.on === 'function') {
    window.prestashop.on('updateCart', function (event) {
      var reason = event && event.reason ? event.reason : null;

      if (reason && (reason.linkAction === 'add-to-cart' || reason.action === 'add-to-cart')) {
        safeTrack('AddToCart', { currency: CBQMETA.currency });
      }
    });
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest('button.add-to-cart, .add-to-cart');
    if (btn) {
      safeTrack('AddToCart', { currency: CBQMETA.currency });
    }
  }, true);

})();
