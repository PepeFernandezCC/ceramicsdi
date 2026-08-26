/**
 * CERAMIC CONNECTION - Payment options order (backoffice)
 *
 * Vanilla HTML5 drag & drop reordering for the #poo-list <li> items.
 * Keeps the hidden #poo-order-input in sync with the current DOM order so
 * the form submits a comma-separated list of keys.
 */
document.addEventListener('DOMContentLoaded', function () {
    var list = document.getElementById('poo-list');
    var input = document.getElementById('poo-order-input');

    if (!list || !input) {
        return;
    }

    var dragging = null;

    function syncInput() {
        var keys = [];
        list.querySelectorAll('.poo-item').forEach(function (li) {
            keys.push(li.getAttribute('data-key'));
        });
        input.value = keys.join(',');
    }

    list.querySelectorAll('.poo-item').forEach(function (li) {
        li.addEventListener('dragstart', function () {
            dragging = li;
            li.classList.add('poo-dragging');
        });

        li.addEventListener('dragend', function () {
            dragging = null;
            li.classList.remove('poo-dragging');
            list.querySelectorAll('.poo-item').forEach(function (el) {
                el.classList.remove('poo-over');
            });
            syncInput();
        });

        li.addEventListener('dragover', function (e) {
            e.preventDefault();
            if (!dragging || dragging === li) {
                return;
            }
            li.classList.add('poo-over');
        });

        li.addEventListener('dragleave', function () {
            li.classList.remove('poo-over');
        });

        li.addEventListener('drop', function (e) {
            e.preventDefault();
            li.classList.remove('poo-over');
            if (!dragging || dragging === li) {
                return;
            }

            var items = Array.prototype.slice.call(list.querySelectorAll('.poo-item'));
            var draggingIndex = items.indexOf(dragging);
            var targetIndex = items.indexOf(li);

            if (draggingIndex < targetIndex) {
                li.parentNode.insertBefore(dragging, li.nextSibling);
            } else {
                li.parentNode.insertBefore(dragging, li);
            }

            syncInput();
        });
    });

    syncInput();
});
