/* AJAX PRODUCTOS / INSPIRACIÓN */
document.addEventListener('DOMContentLoaded', function () {
    console.log('cargando productos para las inspiraciones...');
    var root = document.getElementById('inspiration-products-block');
    if (!root) {
        return;
    }

    var config = {};
    try {
        config = JSON.parse(root.getAttribute('data-config') || '{}');
    } catch (e) {
        config = {};
    }

    var ajaxUrl = config.ajax_url || '';
    var items = Array.isArray(config.items) ? config.items : [];

    var input = document.getElementById('inspiration-product-search');
    var results = document.getElementById('inspiration-product-results');
    var list = document.getElementById('inspiration-products-list');
    var hidden = document.getElementById('products_json');

    function syncHidden() {
        hidden.value = JSON.stringify(items);
    }

    function isSelected(idProduct) {
        return items.some(function (item) {
            return parseInt(item.id_product, 10) === parseInt(idProduct, 10);
        });
    }

    function removeItem(idProduct) {
        items = items.filter(function (item) {
            return parseInt(item.id_product, 10) !== parseInt(idProduct, 10);
        });
        renderList();
        syncHidden();
    }

    function addItem(product) {
        if (isSelected(product.id_product)) {
            return;
        }

        items.push({
            id_product: parseInt(product.id_product, 10),
            name: product.name,
            product_type: 'suelo'
        });

        renderList();
        syncHidden();
    }

    function bindProductTypeEvents() {
        document.querySelectorAll('.related-product-type').forEach(function (select) {
            select.addEventListener('change', function () {
                var index = parseInt(this.getAttribute('data-index'), 10);
                if (typeof items[index] !== 'undefined') {
                    items[index].product_type = this.value;
                    syncHidden();
                }
            });
        });
    }

    function renderList() {
        list.innerHTML = '';

        if (!items.length) {
            list.innerHTML = '<p><em>No hay productos seleccionados.</em></p>';
            return;
        }

        items.forEach(function (item, index) {
            if (!item.product_type) {
                item.product_type = 'suelo';
            }

            var row = document.createElement('div');
            row.className = 'panel';
            row.style.marginBottom = '8px';
            
            row.innerHTML =
                '<div class="panel-body" style="display:flex;justify-content:space-between;align-items:center;gap:15px;">' +
                    '<div style="flex:1;">#' + item.id_product + ' - ' + item.name + '</div>' +
                    '<div style="width:140px;">' +
                        '<select class="form-control related-product-type" data-index="' + index + '">' +
                            '<option value="suelo"' + (item.product_type === 'suelo' ? ' selected' : '') + '>Suelo</option>' +
                            '<option value="pared"' + (item.product_type === 'pared' ? ' selected' : '') + '>Pared</option>' +
                            '<option value="ambas"' + (item.product_type === 'ambas' ? ' selected' : '') + '>Suelo y Pared</option>' +
                        '</select>' +
                    '</div>' +
                    '<button type="button" class="btn btn-default remove-related-product" data-id="' + item.id_product + '">' +
                        'Quitar' +
                    '</button>' +
                '</div>';

            list.appendChild(row);
        });

        bindProductTypeEvents();
        syncHidden();
    }

    input.addEventListener('keyup', function () {
        var q = input.value.trim();

        if (q.length < 2) {
            results.innerHTML = '';
            return;
        }

        fetch(ajaxUrl + '&ajax=1&action=SearchProducts&q=' + encodeURIComponent(q), {
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                results.innerHTML = '';

                if (!Array.isArray(data) || !data.length) {
                    results.innerHTML = '<p><em>Sin resultados.</em></p>';
                    return;
                }

                data.forEach(function (product) {
                    var row = document.createElement('div');
                    row.className = 'panel';
                    row.style.marginBottom = '6px';
                    row.innerHTML =
                        '<div class="panel-body" style="display:flex;justify-content:space-between;align-items:center;">' +
                            '<span>#' + product.id_product + ' - ' + product.name + '</span>' +
                            '<button type="button" class="btn btn-primary add-related-product">Añadir</button>' +
                        '</div>';

                    row.querySelector('.add-related-product').addEventListener('click', function () {
                        addItem(product);
                        input.value = '';
                        results.innerHTML = '';
                    });

                    results.appendChild(row);
                });
            });
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-related-product')) {
            e.preventDefault();
            removeItem(e.target.getAttribute('data-id'));
        }
    });

    renderList();
    syncHidden();
});

/* AJAX CARACTERÍSTICAS / INSPIRACIÓN */
document.addEventListener('DOMContentLoaded', function () {
    console.log('cargando caracteríticas para las inspiraciones...');
    var root = document.getElementById('inspiration-features-block');
    if (!root) {
        return;
    }

    var config = {};
    try {
        config = JSON.parse(root.getAttribute('data-config') || '{}');
    } catch (e) {
        config = {};
    }

    var items = Array.isArray(config.items) ? config.items : [];
    var features = Array.isArray(config.features) ? config.features : [];
    var featureValues = config.feature_values || {};

    var list = document.getElementById('inspiration-features-list');
    var hidden = document.getElementById('features_json');
    var addBtn = document.getElementById('inspiration-add-feature');

    function syncHidden() {
        hidden.value = JSON.stringify(items);
    }

    function getFeatureOptions(selectedId) {
        var html = '<option value="">Seleccionar característica</option>';
        features.forEach(function (feature) {
            var selected = parseInt(selectedId, 10) === parseInt(feature.id_feature, 10) ? ' selected' : '';
            html += '<option value="' + feature.id_feature + '"' + selected + '>' + feature.name + '</option>';
        });
        return html;
    }

    function getFeatureValueOptions(featureId, selectedValueId) {
        var values = featureValues[featureId] || [];
        var html = '<option value="">Seleccionar valor</option>';

        values.forEach(function (value) {
            var selected = parseInt(selectedValueId, 10) === parseInt(value.id_feature_value, 10) ? ' selected' : '';
            html += '<option value="' + value.id_feature_value + '"' + selected + '>' + value.value + '</option>';
        });

        return html;
    }

    function renderList() {
        list.innerHTML = '';

        items.forEach(function (item, index) {
            var row = document.createElement('div');
            row.className = 'row';
            row.style.marginBottom = '15px';

            var featureId = item.id_feature || '';
            var valueId = item.id_feature_value || '';
            var customValue = item.custom_value || '';

            row.innerHTML =
                '<div class="col-lg-4">' +
                    '<label>Característica</label>' +
                    '<select class="form-control feature-select" data-index="' + index + '">' +
                        getFeatureOptions(featureId) +
                    '</select>' +
                '</div>' +
                '<div class="col-lg-4">' +
                    '<label>Valor predefinido</label>' +
                    '<select class="form-control feature-value-select" data-index="' + index + '">' +
                        getFeatureValueOptions(featureId, valueId) +
                    '</select>' +
                '</div>' +
                '<div class="col-lg-3">' +
                    '<label>O un valor personalizado</label>' +
                    '<input type="text" class="form-control feature-custom-value" data-index="' + index + '" value="' + customValue.replace(/"/g, '&quot;') + '">' +
                '</div>' +
                '<div class="col-lg-1" style="padding-top:25px;">' +
                    '<button type="button" class="btn btn-default remove-feature" data-index="' + index + '">🗑</button>' +
                '</div>';

            list.appendChild(row);
        });

        bindEvents();
        syncHidden();
    }

    function bindEvents() {
        list.querySelectorAll('.feature-select').forEach(function (select) {
            select.addEventListener('change', function () {
                var index = parseInt(this.getAttribute('data-index'), 10);
                items[index].id_feature = parseInt(this.value, 10) || 0;
                items[index].id_feature_value = 0;
                renderList();
            });
        });

        list.querySelectorAll('.feature-value-select').forEach(function (select) {
            select.addEventListener('change', function () {
                var index = parseInt(this.getAttribute('data-index'), 10);
                items[index].id_feature_value = parseInt(this.value, 10) || 0;
                syncHidden();
            });
        });

        list.querySelectorAll('.feature-custom-value').forEach(function (input) {
            input.addEventListener('input', function () {
                var index = parseInt(this.getAttribute('data-index'), 10);
                items[index].custom_value = this.value;
                syncHidden();
            });
        });

        list.querySelectorAll('.remove-feature').forEach(function (button) {
            button.addEventListener('click', function () {
                var index = parseInt(this.getAttribute('data-index'), 10);
                items.splice(index, 1);
                renderList();
            });
        });
    }

    addBtn.addEventListener('click', function () {
        items.push({
            id_feature: 0,
            id_feature_value: 0,
            custom_value: ''
        });
        renderList();
    });

    renderList();
});