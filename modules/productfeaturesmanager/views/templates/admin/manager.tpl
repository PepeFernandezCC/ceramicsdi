{*
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-list"></i>
        {l s='Gestor de Características de Productos' mod='productfeaturesmanager'}
    </div>

    <div class="panel-body">
        <form method="get" action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" class="form-horizontal" id="feature-selector-form">
            <input type="hidden" name="controller" value="AdminProductFeaturesManager" />
            <input type="hidden" name="token" value="{$smarty.get.token|escape:'html':'UTF-8'}" />

            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Característica' mod='productfeaturesmanager'}
                </label>
                <div class="col-lg-4">
                    <select name="id_feature" id="id_feature" class="form-control" onchange="this.form.submit()">
                        <option value="0">{l s='-- Selecciona una característica --' mod='productfeaturesmanager'}</option>
                        {foreach from=$features item=feature}
                            <option value="{$feature.id_feature|intval}" {if $selected_feature == $feature.id_feature}selected{/if}>
                                {$feature.name|escape:'html':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-lg-3">
                    <select name="id_lang" id="id_lang" class="form-control" onchange="this.form.submit()">
                        {foreach from=$languages item=lang}
                            <option value="{$lang.id_lang|intval}" {if $selected_lang == $lang.id_lang}selected{/if}>
                                {$lang.name|escape:'html':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </form>

        {if $selected_feature > 0}
        <div style="margin-top: 20px;">
            <!-- Barra de búsqueda y acciones -->
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-search"></i>
                    {l s='Búsqueda y Acciones' mod='productfeaturesmanager'}
                </div>
                <div class="panel-body">
                    <form method="get" action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" class="form-inline">
                        <input type="hidden" name="controller" value="AdminProductFeaturesManager" />
                        <input type="hidden" name="token" value="{$smarty.get.token|escape:'html':'UTF-8'}" />
                        <input type="hidden" name="id_feature" value="{$selected_feature|intval}" />
                        <input type="hidden" name="id_lang" value="{$selected_lang|intval}" />
                        
                        <div class="form-group">
                            <label>{l s='Buscar' mod='productfeaturesmanager'}:</label>
                            <input type="text" name="search" value="{$search|escape:'html':'UTF-8'}" 
                                   class="form-control" placeholder="{l s='ID, Referencia, Nombre o Proveedor' mod='productfeaturesmanager'}" 
                                   style="width: 300px;" />
                        </div>
                        
                        <div class="form-group">
                            <label>{l s='Por página' mod='productfeaturesmanager'}:</label>
                            <select name="per_page" class="form-control">
                                <option value="50" {if $per_page == 50}selected{/if}>50</option>
                                <option value="100" {if $per_page == 100}selected{/if}>100</option>
                                <option value="200" {if $per_page == 200}selected{/if}>200</option>
                                <option value="500" {if $per_page == 500}selected{/if}>500</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-search"></i> {l s='Buscar' mod='productfeaturesmanager'}
                        </button>
                        
                        {if !empty($search)}
                        <a href="?controller=AdminProductFeaturesManager&token={$smarty.get.token|escape:'html':'UTF-8'}&id_feature={$selected_feature|intval}&id_lang={$selected_lang|intval}" 
                           class="btn btn-default">
                            <i class="icon-remove"></i> {l s='Limpiar' mod='productfeaturesmanager'}
                        </a>
                        {/if}
                        
                        <div class="pull-right">
                            <button type="button" class="btn btn-default" onclick="exportToCSV()">
                                <i class="icon-download"></i> {l s='Exportar CSV' mod='productfeaturesmanager'}
                            </button>
                            <button type="button" class="btn btn-default" onclick="showImportModal()">
                                <i class="icon-upload"></i> {l s='Importar CSV' mod='productfeaturesmanager'}
                            </button>
                        </div>
                    </form>
                    
                    <div style="margin-top: 10px;">
                        <span class="badge badge-info">
                            {l s='Total de productos' mod='productfeaturesmanager'}: {$total_products|intval}
                        </span>
                        {if !empty($search)}
                        <span class="badge badge-warning">
                            {l s='Búsqueda activa' mod='productfeaturesmanager'}: "{$search|escape:'html':'UTF-8'}"
                        </span>
                        {/if}
                    </div>
                </div>
            </div>
            
            {if !empty($products_data)}
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-table"></i>
                    {l s='Productos y Valores' mod='productfeaturesmanager'}
                    <span class="badge">{l s='Mostrando' mod='productfeaturesmanager'} {count($products_data)} {l s='productos' mod='productfeaturesmanager'}</span>
                </div>

                <div class="panel-body">
                    <!-- Paginación superior -->
                    {if $total_pages > 1}
                    <div class="pagination-wrapper" style="margin-bottom: 15px;">
                        {include file="./pagination.tpl"}
                    </div>
                    {/if}
                    
                    <table id="products-features-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="select-all" />
                                </th>
                                <th>{l s='ID' mod='productfeaturesmanager'}</th>
                                <th>{l s='Referencia' mod='productfeaturesmanager'}</th>
                                <th>{l s='Producto' mod='productfeaturesmanager'}</th>
                                <th>{l s='Proveedor' mod='productfeaturesmanager'}</th>
                                <th>{l s='Valor' mod='productfeaturesmanager'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$products_data item=product}
                            <tr data-product-id="{$product.id_product|intval}">
                                <td>
                                    <input type="checkbox" class="product-checkbox" value="{$product.id_product|intval}" />
                                </td>
                                <td>{$product.id_product|intval}</td>
                                <td>{$product.reference|escape:'html':'UTF-8'}</td>
                                <td>{$product.product_name|escape:'html':'UTF-8'}</td>
                                <td>{if $product.supplier_names}{$product.supplier_names|escape:'html':'UTF-8'}{else}--{/if}</td>
                                <td>
                                    <select class="form-control feature-value-select" 
                                            data-product-id="{$product.id_product|intval}"
                                            data-feature-id="{$selected_feature|intval}"
                                            data-original-value="{$product.id_feature_value|intval}">
                                        <option value="0">{l s='-- Sin valor --' mod='productfeaturesmanager'}</option>
                                        {foreach from=$feature_values item=fv}
                                            <option value="{$fv.id_feature_value|intval}" 
                                                    {if $product.id_feature_value == $fv.id_feature_value}selected{/if}>
                                                {$fv.value|escape:'html':'UTF-8'}
                                            </option>
                                        {/foreach}
                                    </select>
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>

                    <!-- Paginación inferior -->
                    {if $total_pages > 1}
                    <div class="pagination-wrapper" style="margin-top: 15px;">
                        {include file="./pagination.tpl"}
                    </div>
                    {/if}

                    <div style="margin-top: 15px;">
                        <button type="button" class="btn btn-primary" onclick="saveBulkChanges()">
                            <i class="icon-save"></i> {l s='Guardar cambios seleccionados' mod='productfeaturesmanager'}
                        </button>
                        <button type="button" class="btn btn-success" onclick="saveAllChanges()">
                            <i class="icon-save"></i> {l s='Guardar todos los cambios' mod='productfeaturesmanager'}
                        </button>
                    </div>
                </div>
            </div>
            {else}
            <div class="alert alert-info">
                <i class="icon-info-circle"></i>
                {if !empty($search)}
                    {l s='No se encontraron productos que coincidan con la búsqueda' mod='productfeaturesmanager'} "{$search|escape:'html':'UTF-8'}"
                {else}
                    {l s='No hay productos con esta característica' mod='productfeaturesmanager'}
                {/if}
            </div>
            {/if}
        </div>
        {/if}
    </div>
</div>

<!-- Modal para importar CSV -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{l s='Importar CSV' mod='productfeaturesmanager'}</h4>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_feature" value="{$selected_feature|intval}" />
                    <input type="hidden" name="importCSV" value="1" />
                    <input type="hidden" name="token" value="{$smarty.get.token|escape:'html':'UTF-8'}" />
                    
                    <div class="form-group">
                        <label>{l s='Archivo CSV' mod='productfeaturesmanager'}</label>
                        <input type="file" name="csv_file" accept=".csv" class="form-control" required />
                        <p class="help-block">
                            {l s='Formato requerido: El CSV debe tener al menos las columnas "ID Producto" (primera columna) e "ID Valor".' mod='productfeaturesmanager'}<br>
                            {l s='Las demás columnas (Referencia, Nombres de producto, Valores en otros idiomas) son opcionales.' mod='productfeaturesmanager'}<br>
                            <strong>{l s='Importante:' mod='productfeaturesmanager'}</strong> {l s='El separador debe ser punto y coma (;)' mod='productfeaturesmanager'}
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancelar' mod='productfeaturesmanager'}</button>
                    <button type="submit" class="btn btn-primary">{l s='Importar' mod='productfeaturesmanager'}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script>
var selectedFeature = {$selected_feature|intval};
var selectedLang = {$selected_lang|intval};
var searchFilter = '{$search|escape:'javascript':'UTF-8'}';
var token = '{$smarty.get.token|escape:'javascript':'UTF-8'}';

$(document).ready(function() {
    if ($('#products-features-table').length) {
        // Usamos paginación del lado del servidor, así que DataTables solo para ordenamiento
        $('#products-features-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            paging: false, // Desactivar paginación de DataTables (usamos la del servidor)
            searching: false, // Desactivar búsqueda de DataTables (usamos la del servidor)
            info: false, // Desactivar info de DataTables
            order: [[2, 'asc']] // Mantener ordenamiento
        });
    }

    // Select all checkbox
    $('#select-all').on('change', function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Auto-save on select change (opcional, comentado por ahora)
    // $('.feature-value-select').on('change', function() {
    //     saveSingleChange($(this));
    // });
});

function saveSingleChange(select) {
    var idProduct = select.data('product-id');
    var idFeature = select.data('feature-id');
    var idFeatureValue = select.val();

    $.ajax({
        url: window.location.href,
        method: 'POST',
        data: {
            updateFeatureValue: 1,
            id_product: idProduct,
            id_feature: idFeature,
            id_feature_value: idFeatureValue,
            ajax: 1,
            token: token
        },
        success: function(response) {
            var result = JSON.parse(response);
            if (result.success) {
                showNotification('{l s='Guardado correctamente' mod='productfeaturesmanager'}', 'success');
            }
        }
    });
}

function saveBulkChanges() {
    var updates = {};
    var checked = $('.product-checkbox:checked');

    if (checked.length === 0) {
        alert('{l s='Selecciona al menos un producto' mod='productfeaturesmanager'}');
        return;
    }

    checked.each(function() {
        var idProduct = $(this).val();
        var select = $('.feature-value-select[data-product-id="' + idProduct + '"]');
        updates[idProduct] = select.val();
    });

    saveUpdates(updates);
}

function saveAllChanges() {
    var updates = {};
    
    $('.feature-value-select').each(function() {
        var idProduct = $(this).data('product-id');
        var currentValue = $(this).val();
        var originalValue = $(this).data('original-value') || '';
        
        if (currentValue != originalValue) {
            updates[idProduct] = currentValue;
        }
    });

    if (Object.keys(updates).length === 0) {
        alert('{l s='No hay cambios para guardar' mod='productfeaturesmanager'}');
        return;
    }

    saveUpdates(updates);
}

function saveUpdates(updates) {
    $.ajax({
        url: window.location.href,
        method: 'POST',
        data: {
            bulkUpdateFeatures: 1,
            id_feature: selectedFeature,
            updates: updates,
            token: token
        },
        success: function() {
            showNotification('{l s='Cambios guardados correctamente' mod='productfeaturesmanager'}', 'success');
            setTimeout(function() {
                location.reload();
            }, 1000);
        }
    });
}

function exportToCSV() {
    // Mostrar mensaje si hay filtro activo
    if (searchFilter && searchFilter.trim() !== '') {
        if (!confirm('{l s='Se exportarán solo los productos filtrados por' mod='productfeaturesmanager'}: "' + searchFilter + '"\n\n{l s='¿Deseas continuar?' mod='productfeaturesmanager'}')) {
            return;
        }
    }
    
    var form = $('<form>', {
        method: 'POST',
        action: window.location.href
    });
    
    form.append($('<input>', {
        type: 'hidden',
        name: 'exportCSV',
        value: '1'
    }));
    form.append($('<input>', {
        type: 'hidden',
        name: 'id_feature',
        value: selectedFeature
    }));
    form.append($('<input>', {
        type: 'hidden',
        name: 'id_lang',
        value: selectedLang
    }));
    form.append($('<input>', {
        type: 'hidden',
        name: 'search',
        value: searchFilter
    }));
    form.append($('<input>', {
        type: 'hidden',
        name: 'token',
        value: token
    }));
    
    $('body').append(form);
    form.submit();
    form.remove();
}

function showImportModal() {
    $('#importModal').modal('show');
}

function showNotification(message, type) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var notification = $('<div>', {
        class: 'alert ' + alertClass + ' alert-dismissible',
        style: 'position: fixed; top: 20px; right: 20px; z-index: 9999;',
        html: '<button type="button" class="close" data-dismiss="alert">&times;</button>' + message
    });
    
    $('body').append(notification);
    setTimeout(function() {
        notification.fadeOut(function() {
            $(this).remove();
        });
    }, 3000);
}
</script>

