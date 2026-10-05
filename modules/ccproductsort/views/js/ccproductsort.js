/**
 * CERAMIC CONNECTION - Product sort selector
 *
 * Builds the URL from the current location (so it keeps any facet filters
 * applied via AJAX), drops the page and lets ps_facetedsearch reload the
 * list through its "updateFacets" event. Falls back to a full reload.
 *
 * Listens in the capture phase on window and stops the event: on mobile the
 * selector lives inside the theme's #search_filters bar, and ps_facetedsearch
 * reacts to any "#search_filters select" change by requesting "?<form data>"
 * (an empty query, no form there). Both requests raced, one got cancelled and
 * the list stayed loading.
 */
window.addEventListener('change', function (event) {
  if (!event.target.classList || !event.target.classList.contains('js-ccproductsort-select')) {
    return;
  }

  event.stopPropagation();

  // The selector exists twice (filters bar on mobile + active filters bar):
  // keep every instance showing the same value.
  document.querySelectorAll('.js-ccproductsort-select').forEach(function (select) {
    if (select !== event.target) {
      select.value = event.target.value;
    }
  });

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
}, true);
