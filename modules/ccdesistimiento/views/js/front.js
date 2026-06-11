document.addEventListener('DOMContentLoaded', function () {
  if (typeof ccDesistimientoHistory === 'undefined') {
    return;
  }

  if (!ccDesistimientoHistory.eligibleUrl) {
    return;
  }

  fetch(ccDesistimientoHistory.eligibleUrl, {
    credentials: 'same-origin'
  })
    .then(function (response) {
      return response.json();
    })
    .then(function (payload) {
      if (!payload || !payload.success || !payload.orders) {
        return;
      }

      var byReference = {};

      payload.orders.forEach(function (order) {
        byReference[String(order.reference).trim()] = order;
      });

      document.querySelectorAll('table.table-labeled tbody tr').forEach(function (row) {
        var refCell = row.querySelector('th[scope=row]');
        var actions = row.querySelector('.order-actions');

        if (!refCell || !actions) {
          return;
        }

        var order = byReference[String(refCell.textContent).trim()];

        if (!order) {
          return;
        }

        actions.appendChild(ccCreateWithdrawalNode(order));
      });

      document.querySelectorAll('.orders .order').forEach(function (block) {
        var refNode = block.querySelector('h3');

        if (!refNode) {
          return;
        }

        var order = byReference[String(refNode.textContent).trim()];

        if (!order) {
          return;
        }

        var target = block.querySelector('.col-xs-10') || block;
        target.appendChild(ccCreateWithdrawalNode(order));
      });
    })
    .catch(function () {});
});

function ccCreateWithdrawalNode(order) {
  if (order.already_requested) {
    var span = document.createElement('span');
    span.className = 'cc-desistimiento-history-badge';
    span.textContent = ccDesistimientoHistory.requestedText;
    return span;
  }

  var link = document.createElement('a');
  link.className = 'cc-desistimiento-history-btn';
  link.href = order.url;
  link.textContent = ccDesistimientoHistory.buttonText;

  return link;
}