/**
 * CERAMIC CONNECTION - Product sort selector
 *
 * Builds the URL from the current location (so it keeps any facet filters
 * applied via AJAX), drops the page and lets ps_facetedsearch reload the
 * list through its "updateFacets" event. Falls back to a full reload.
 */
document.addEventListener('change', function (event) {
  if (!event.target.classList.contains('js-ccproductsort-select')) {
    return;
  }

  var url = new URL(window.location.href);

  if (event.target.value) {
    url.searchParams.set('order', event.target.value);
  } else {
    url.searchParams.delete('order');
  }
  url.searchParams.delete('page');

  if (window.prestashop && typeof window.prestashop.emit === 'function') {
    window.prestashop.emit('updateFacets', url.toString());
  } else {
    window.location.href = url.toString();
  }
});
