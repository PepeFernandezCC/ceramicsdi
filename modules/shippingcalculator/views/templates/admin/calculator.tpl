{*
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-calculator"></i>
        {l s='Calculadora de Envíos y Plazos' mod='shippingcalculator'}
    </div>

    <div class="panel-body">
        <form method="post" action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" class="form-horizontal">
            <input type="hidden" name="calculateShipping" value="1" />

            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Productos' mod='shippingcalculator'}
                </label>
                <div class="col-lg-9">
                    <select name="selected_products[]" id="selected_products" multiple="multiple" class="chosen" style="width: 100%;">
                        {foreach from=$products item=product}
                            <option value="{$product.id_product|intval}" 
                                    {if isset($selected_products) && is_array($selected_products) && in_array($product.id_product|intval, $selected_products)}selected="selected"{/if}>
                                {$product.name|escape:'html':'UTF-8'} (Ref: {$product.reference|escape:'html':'UTF-8'})
                            </option>
                        {/foreach}
                    </select>
                    <p class="help-block">
                        {l s='Selecciona uno o más productos para calcular el plazo' mod='shippingcalculator'}
                    </p>
                </div>
            </div>

            <div class="form-group" id="product-quantities-container" style="display: none;">
                <label class="control-label col-lg-3">
                    {l s='Cantidades' mod='shippingcalculator'}
                </label>
                <div class="col-lg-9">
                    <div id="product-quantities-list">
                        {if isset($selected_products) && is_array($selected_products) && !empty($selected_products)}
                            {foreach from=$selected_products item=product_id}
                                {foreach from=$products item=product}
                                    {if $product.id_product == $product_id}
                                        <div class="form-group product-quantity-row" data-product-id="{$product.id_product|intval}" style="margin-bottom: 10px;">
                                            <label style="display: inline-block; width: 300px; margin-right: 10px;">
                                                {$product.name|escape:'html':'UTF-8'}
                                            </label>
                                            <input type="number" 
                                                   name="product_quantities[{$product.id_product|intval}]" 
                                                   value="{if isset($product_quantities[$product.id_product])}{$product_quantities[$product.id_product]|intval}{else}1{/if}"
                                                   min="1"
                                                   class="form-control" 
                                                   style="display: inline-block; width: 100px;" />
                                        </div>
                                    {/if}
                                {/foreach}
                            {/foreach}
                        {/if}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='País destino' mod='shippingcalculator'}
                </label>
                <div class="col-lg-9">
                    <select name="id_country" id="id_country" class="form-control" style="width: 300px;">
                        <option value="">{l s='-- Selecciona un país --' mod='shippingcalculator'}</option>
                        {foreach from=$countries item=country}
                            <option value="{$country.id_country|intval}" 
                                    {if isset($selected_country_id) && $selected_country_id == $country.id_country}selected="selected"{/if}>
                                {$country.name|escape:'html':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                    <p class="help-block">
                        {l s='Selecciona el país para ver sus provincias/estados' mod='shippingcalculator'}
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Provincia/Estado destino' mod='shippingcalculator'}
                </label>
                <div class="col-lg-9">
                    <select name="province_code" id="province_code" class="form-control">
                        <option value="">{l s='-- Selecciona una provincia/estado --' mod='shippingcalculator'}</option>
                        {foreach from=$states item=state}
                            <option value="{$state.iso_code|escape:'html':'UTF-8'}" 
                                    {if isset($selected_province) && $selected_province == $state.iso_code}selected="selected"{/if}>
                                {$state.name|escape:'html':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                    <p class="help-block" id="province-help" style="display: none;">
                        {l s='Primero selecciona un país para ver sus provincias/estados' mod='shippingcalculator'}
                    </p>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-calculator"></i>
                        {l s='Calcular plazo de entrega' mod='shippingcalculator'}
                    </button>
                </div>
            </div>
        </form>

        {if isset($calculation_result)}
        <div style="margin-top: 20px;">
            <h4>{l s='Resultado del cálculo' mod='shippingcalculator'}</h4>
            {if isset($calculation_result.mode) && $calculation_result.mode == 'by_product'}
                {* Modo por producto: mostrar tabla con plazos individuales *}
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{l s='Producto' mod='shippingcalculator'}</th>
                            <th>{l s='Cantidad' mod='shippingcalculator'}</th>
                            <th>{l s='Preparación' mod='shippingcalculator'}</th>
                            <th>{l s='Envío' mod='shippingcalculator'} ({l s='mín-máx' mod='shippingcalculator'})</th>
                            <th>{l s='Total' mod='shippingcalculator'} ({l s='mín-máx' mod='shippingcalculator'})</th>
                            <th>{l s='Entrega estimada' mod='shippingcalculator'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$calculation_result.products item=product_delivery}
                        <tr>
                            <td><strong>{$product_delivery.name|escape:'html':'UTF-8'}</strong></td>
                            <td>
                                <strong>{$product_delivery.quantity|intval}</strong>
                            </td>
                            <td>
                                {$product_delivery.preparation_days|intval} 
                                {if $product_delivery.preparation_days == 1}
                                    {l s='día' mod='shippingcalculator'}
                                {else}
                                    {l s='días' mod='shippingcalculator'}
                                {/if}
                            </td>
                            <td>
                                {$product_delivery.shipping_days_min|intval} - {$product_delivery.shipping_days_max|intval}
                                {l s='días' mod='shippingcalculator'}
                            </td>
                            <td>
                                {$product_delivery.total_days_min|intval} - {$product_delivery.total_days_max|intval}
                                {l s='días' mod='shippingcalculator'}
                            </td>
                            <td style="color: #333333;">
                                <strong>{$product_delivery.start_date|escape:'html':'UTF-8'}</strong> - 
                                <strong>{$product_delivery.end_date|escape:'html':'UTF-8'}</strong>
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5"><strong>{l s='Provincia:' mod='shippingcalculator'}</strong> {$calculation_result.province|escape:'html':'UTF-8'}</td>
                            <td class="{if isset($calculation_result.shipping_cost) && $calculation_result.shipping_cost > 0}success{else}info{/if}">
                                <strong>{l s='Coste de envío:' mod='shippingcalculator'}</strong> 
                                {if isset($calculation_result.shipping_cost)}
                                    <strong>{$calculation_result.shipping_cost_formatted|escape:'html':'UTF-8'}</strong>
                                {else}
                                    <em>{l s='No disponible' mod='shippingcalculator'}</em>
                                {/if}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            {else}
                {* Modo combinado: máximo o suma *}
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{l s='Concepto' mod='shippingcalculator'}</th>
                            <th>{l s='Valor' mod='shippingcalculator'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>{l s='Plazo de preparación' mod='shippingcalculator'}</strong></td>
                            <td>
                                {$calculation_result.preparation_days|intval} 
                                {if $calculation_result.preparation_days == 1}
                                    {l s='día' mod='shippingcalculator'}
                                {else}
                                    {l s='días' mod='shippingcalculator'}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{l s='Plazo de envío' mod='shippingcalculator'}</strong></td>
                            <td>
                                {$calculation_result.shipping_days_min|intval} - {$calculation_result.shipping_days_max|intval} {l s='días' mod='shippingcalculator'}
                                ({$calculation_result.province|escape:'html':'UTF-8'})
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{l s='Plazo total estimado' mod='shippingcalculator'}</strong></td>
                            <td>
                                {$calculation_result.total_days_min|intval} - {$calculation_result.total_days_max|intval} {l s='días' mod='shippingcalculator'}
                            </td>
                        </tr>
                        <tr class="info">
                            <td><strong>{l s='Entrega estimada' mod='shippingcalculator'}</strong></td>
                            <td style="font-size: 16px; color: #333333;">
                                {l s='Del' mod='shippingcalculator'} <strong>{$calculation_result.start_date|escape:'html':'UTF-8'}</strong> 
                                {l s='al' mod='shippingcalculator'} <strong>{$calculation_result.end_date|escape:'html':'UTF-8'}</strong>
                            </td>
                        </tr>
                        <tr class="{if isset($calculation_result.shipping_cost) && $calculation_result.shipping_cost > 0}success{else}info{/if}">
                            <td><strong>{l s='Coste de envío' mod='shippingcalculator'}</strong></td>
                            <td style="font-size: 16px; {if isset($calculation_result.shipping_cost) && $calculation_result.shipping_cost > 0}color: #28a745;{else}color: #6c757d;{/if}">
                                {if isset($calculation_result.shipping_cost)}
                                    <strong>{$calculation_result.shipping_cost_formatted|escape:'html':'UTF-8'}</strong>
                                {else}
                                    <em>{l s='No disponible' mod='shippingcalculator'}</em>
                                {/if}
                            </td>
                        </tr>
                    </tbody>
                </table>
            {/if}
        </div>
        {/if}
    </div>
</div>

<div class="panel" style="margin-top: 20px;">
    <div class="panel-heading">
        <i class="icon-cog"></i>
        {l s='Configuración' mod='shippingcalculator'}
    </div>

    <div class="panel-body">
        <form method="post" action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" class="form-horizontal">
            <input type="hidden" name="saveConfiguration" value="1" />

            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Modo de cálculo de preparación' mod='shippingcalculator'}
                </label>
                <div class="col-lg-9">
                    <select name="SHIPPING_CALCULATOR_PREP_MODE" class="form-control" style="width: 300px;">
                        <option value="0" {if $prep_mode == 0}selected{/if}>
                            {l s='Máximo: tomar el mayor plazo de preparación' mod='shippingcalculator'}
                        </option>
                        <option value="1" {if $prep_mode == 1}selected{/if}>
                            {l s='Suma: sumar todos los plazos de preparación' mod='shippingcalculator'}
                        </option>
                        <option value="2" {if $prep_mode == 2}selected{/if}>
                            {l s='Por producto: mostrar plazos individuales por producto' mod='shippingcalculator'}
                        </option>
                    </select>
                    <p class="help-block">
                        {l s='Elige cómo calcular el plazo cuando hay múltiples productos en el carrito' mod='shippingcalculator'}
                    </p>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-save"></i>
                        {l s='Guardar configuración' mod='shippingcalculator'}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel" style="margin-top: 20px;">
    <div class="panel-heading">
        <i class="icon-cog"></i>
        {l s='Configurar plazos y costes de envío por provincia' mod='shippingcalculator'}
    </div>

    <div class="panel-body">
        <form method="post" action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" class="form-horizontal">
            <input type="hidden" name="saveShippingDelays" value="1" />

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="control-label col-lg-3">
                    {l s='Filtrar por país' mod='shippingcalculator'}
                </label>
                <div class="col-lg-9">
                    <select id="filter_country" class="form-control" style="width: 300px;" required>
                        <option value="">{l s='-- Selecciona un país --' mod='shippingcalculator'}</option>
                        {foreach from=$countries item=country}
                            {if isset($states_by_country[$country.id_country])}
                                <option value="{$country.id_country|intval}" {if isset($filter_country_id) && $country.id_country == $filter_country_id}selected="selected"{/if}>
                                    {$country.name|escape:'html':'UTF-8'} ({count($states_by_country[$country.id_country].states)} {l s='provincias' mod='shippingcalculator'})
                                </option>
                            {/if}
                        {/foreach}
                    </select>
                    <p class="help-block">
                        {l s='Selecciona un país para ver solo sus provincias/estados' mod='shippingcalculator'}
                    </p>
                </div>
            </div>

            <!-- Campo para aplicar min/max global a las provincias del país seleccionado -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="control-label col-lg-3">
                    {l s='Aplicar min/máx. a todas las provincias del país' mod='shippingcalculator'}
                </label>
                <div class="col-lg-9">
                    <div class="form-inline">
                        <div class="form-group" style="margin-right:10px;">
                            <label class="sr-only">{l s='Días mín.' mod='shippingcalculator'}</label>
                            <input type="number" id="apply_days_min" class="form-control" placeholder="{l s='Días mín.' mod='shippingcalculator'}" min="0" style="width:120px;" />
                        </div>
                        <div class="form-group" style="margin-right:10px;">
                            <label class="sr-only">{l s='Días máx.' mod='shippingcalculator'}</label>
                            <input type="number" id="apply_days_max" class="form-control" placeholder="{l s='Días máx.' mod='shippingcalculator'}" min="0" style="width:120px;" />
                        </div>
                        <button type="button" id="apply_min_max_btn" class="btn btn-default">
                            <i class="icon-refresh"></i> {l s='Aplicar a provincias visibles' mod='shippingcalculator'}
                        </button>
                        <p class="help-block" style="margin-top:8px;">{l s='Rellena los valores y haz clic en Aplicar; esto afecta sólo a las provincias del país filtrado.' mod='shippingcalculator'}</p>
                    </div>
                </div>
            </div>

            <table class="table" id="shipping-delays-table">
                <thead>
                    <tr>
                        <th>{l s='País' mod='shippingcalculator'}</th>
                        <th>{l s='Provincia/Estado' mod='shippingcalculator'}</th>
                        <th>{l s='Días mín.' mod='shippingcalculator'}</th>
                        <th>{l s='Días máx.' mod='shippingcalculator'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$states_by_country item=country_data}
                        {foreach from=$country_data.states item=state}
                            <tr class="province-row" data-country-id="{$country_data.country.id_country|intval}">
                                <td><strong>{$country_data.country.name|escape:'html':'UTF-8'}</strong></td>
                                <td>{$state.name|escape:'html':'UTF-8'}</td>
                                <td>
                                    <input type="hidden" name="delays[{$state.iso_code|escape:'html':'UTF-8'}][name]" value="{$state.name|escape:'html':'UTF-8'}" />
                                    {assign var="delay_min" value=""}
                                    {if isset($shipping_delays[$state.iso_code]) && isset($shipping_delays[$state.iso_code].delivery_days_min) && $shipping_delays[$state.iso_code].delivery_days_min !== null}
                                        {assign var="delay_min" value=$shipping_delays[$state.iso_code].delivery_days_min|intval}
                                    {elseif isset($shipping_delays[$state.iso_code]) && isset($shipping_delays[$state.iso_code].delivery_days)}
                                        {assign var="delay_min" value=$shipping_delays[$state.iso_code].delivery_days|intval}
                                    {/if}
                                    <input type="number" 
                                           name="delays[{$state.iso_code|escape:'html':'UTF-8'}][days_min]" 
                                           value="{$delay_min}"
                                           min="0"
                                           max="365"
                                           class="form-control"
                                           placeholder="{l s='Mín.' mod='shippingcalculator'}"
                                           style="width: 100px;" 
                                           required />
                                </td>
                                <td>
                                    {assign var="delay_max" value=""}
                                    {if isset($shipping_delays[$state.iso_code]) && isset($shipping_delays[$state.iso_code].delivery_days_max) && $shipping_delays[$state.iso_code].delivery_days_max !== null}
                                        {assign var="delay_max" value=$shipping_delays[$state.iso_code].delivery_days_max|intval}
                                    {elseif isset($shipping_delays[$state.iso_code]) && isset($shipping_delays[$state.iso_code].delivery_days)}
                                        {assign var="delay_max" value=$shipping_delays[$state.iso_code].delivery_days|intval}
                                    {/if}
                                    <input type="number" 
                                           name="delays[{$state.iso_code|escape:'html':'UTF-8'}][days_max]" 
                                           value="{$delay_max}"
                                           min="0"
                                           max="365"
                                           class="form-control"
                                           placeholder="{l s='Máx.' mod='shippingcalculator'}"
                                           style="width: 100px;" 
                                           required />
                                </td>
                            </tr>
                    {/foreach}
                    {/foreach}
                    {if empty($states_by_country)}
                        <tr>
                            <td colspan="4" class="text-center">
                                <em>{l s='No hay provincias/estados configurados. Por favor, configura países y estados en Internacional > Países' mod='shippingcalculator'}</em>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>

            <div class="form-group">
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-success">
                        <i class="icon-save"></i>
                        {l s='Guardar plazos y costes' mod='shippingcalculator'}
                    </button>
                    <button type="button" class="btn btn-default" onclick="exportToCSV()">
                        <i class="icon-download"></i>
                        {l s='Exportar CSV' mod='shippingcalculator'}
                    </button>
                    <button type="button" class="btn btn-default" onclick="showImportModal()">
                        <i class="icon-upload"></i>
                        {l s='Importar CSV' mod='shippingcalculator'}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Estados por país para JavaScript - DEBE estar fuera de $(document).ready() para acceso global
window.statesByCountry = {};
{foreach from=$states_by_country item=country_data}
window.statesByCountry[{$country_data.country.id_country|intval}] = [
    {foreach from=$country_data.states item=state name=state_loop}
    {
        iso_code: '{$state.iso_code|escape:'javascript':'UTF-8'}',
        name: '{$state.name|escape:'javascript':'UTF-8'}'
    }{if !$smarty.foreach.state_loop.last},{/if}
    {/foreach}
];
{/foreach}

// Debug: mostrar en consola el objeto construido
console.log('=== CONSTRUCCIÓN DE statesByCountry ===');
console.log('statesByCountry construido (global):', window.statesByCountry);
console.log('Tipo:', typeof window.statesByCountry);
console.log('Es objeto:', window.statesByCountry instanceof Object);
console.log('Claves disponibles:', Object.keys(window.statesByCountry));
console.log('Total países:', Object.keys(window.statesByCountry).length);

// Verificar algunos países específicos
var testCountries = [6, 13, 14]; // España, Alemania, Francia
testCountries.forEach(function(countryId) {
    if (window.statesByCountry[countryId]) {
        console.log('País ' + countryId + ' tiene', window.statesByCountry[countryId].length, 'provincias');
    } else if (window.statesByCountry[countryId.toString()]) {
        console.log('País "' + countryId + '" (string) tiene', window.statesByCountry[countryId.toString()].length, 'provincias');
    } else {
        console.log('País ' + countryId + ' NO encontrado en statesByCountry');
    }
});

// Productos disponibles para JavaScript
window.productsData = {};
{foreach from=$products item=product}
window.productsData[{$product.id_product|intval}] = {
    id: {$product.id_product|intval},
    name: '{$product.name|escape:'javascript':'UTF-8'}',
    reference: '{$product.reference|escape:'javascript':'UTF-8'}'
};
{/foreach}

// Cantidades del servidor (después del POST)
window.serverQuantities = {};
{if isset($product_quantities) && is_array($product_quantities)}
{foreach from=$product_quantities key=prod_id item=qty}
window.serverQuantities[{$prod_id|intval}] = {$qty|intval};
{/foreach}
{/if}

// Función para actualizar campos de cantidad
function updateProductQuantities() {
    var selectedProducts = $('#selected_products').val() || [];
    var container = $('#product-quantities-container');
    var list = $('#product-quantities-list');
    
    // Guardar valores actuales antes de limpiar
    var savedQuantities = {};
    list.find('input[name^="product_quantities"]').each(function() {
        var name = $(this).attr('name');
        var match = name.match(/product_quantities\[(\d+)\]/);
        if (match) {
            var productId = match[1];
            savedQuantities[productId] = $(this).val() || 1;
        }
    });
    
    // Limpiar lista actual
    list.empty();
    
    if (selectedProducts.length === 0) {
        container.hide();
        return;
    }
    
    // Agregar campo de cantidad para cada producto seleccionado
    selectedProducts.forEach(function(productId) {
        var product = window.productsData[productId];
        if (product) {
            // Usar cantidad guardada si existe, sino usar valor del servidor, sino 1
            var quantity = 1;
            if (savedQuantities[productId]) {
                quantity = savedQuantities[productId];
            } else if (window.serverQuantities[productId]) {
                quantity = window.serverQuantities[productId];
            }
            
            var row = $('<div class="form-group product-quantity-row" data-product-id="' + productId + '" style="margin-bottom: 10px;"></div>');
            row.append('<label style="display: inline-block; width: 300px; margin-right: 10px;">' + product.name + '</label>');
            row.append('<input type="number" name="product_quantities[' + productId + ']" value="' + quantity + '" min="1" class="form-control" style="display: inline-block; width: 100px;" />');
            list.append(row);
        }
    });
    
    container.show();
}

$(document).ready(function() {
    // Inicializar select2 solo si está disponible
    if (typeof $.fn.select2 !== 'undefined') {
        $('#selected_products').select2({
            placeholder: "{l s='Selecciona productos' mod='shippingcalculator'|escape:'javascript':'UTF-8'}",
            allowClear: true
        });
        
        // Asegurar que los valores seleccionados se mantengan después del POST
        {if isset($selected_products) && is_array($selected_products) && count($selected_products) > 0}
        var selectedValues = [{foreach from=$selected_products item=prod_id name=prod_loop}{$prod_id|intval}{if !$smarty.foreach.prod_loop.last},{/if}{/foreach}];
        $('#selected_products').val(selectedValues).trigger('change');
        {/if}
        
        // Actualizar campos de cantidad cuando cambie la selección
        $('#selected_products').on('change', function() {
            updateProductQuantities();
        });
    } else {
        console.warn('Select2 no está disponible, usando select normal');
        // Si Select2 no está disponible, el select funciona normalmente
        // Asegurar que los valores seleccionados se mantengan después del POST
        {if isset($selected_products) && is_array($selected_products) && count($selected_products) > 0}
        var selectedValues = [{foreach from=$selected_products item=prod_id name=prod_loop}{$prod_id|intval}{if !$smarty.foreach.prod_loop.last},{/if}{/foreach}];
        $('#selected_products').val(selectedValues);
        {/if}
        
        // Actualizar campos de cantidad cuando cambie la selección
        $('#selected_products').on('change', function() {
            updateProductQuantities();
        });
    }
    
    // Inicializar campos de cantidad al cargar (después de un pequeño delay para asegurar que select2 se haya inicializado)
    setTimeout(function() {
        updateProductQuantities();
    }, 100);
    
    // Variable local para compatibilidad
    var statesByCountry = window.statesByCountry;
    
    // Función para actualizar provincias - disponible globalmente
    window.updateProvinceSelectWithCountry = function(countryIdNum, preserveSelectedValue) {
        console.log('=== updateProvinceSelectWithCountry INICIADO ===');
        console.log('País recibido:', countryIdNum, 'Tipo:', typeof countryIdNum);
        console.log('Preservar valor seleccionado:', preserveSelectedValue);
        
        var provinceSelect = $('#province_code');
        var provinceHelp = $('#province-help');
        
        console.log('Selector de provincias encontrado:', provinceSelect.length > 0);
        if (provinceSelect.length === 0) {
            console.error('ERROR: Selector #province_code no encontrado en el DOM');
            return;
        }
        
        // Guardar el valor seleccionado antes de limpiar (si se debe preservar)
        var selectedProvinceValue = '';
        if (preserveSelectedValue !== false) {
            // Leer el valor del selector actual
            selectedProvinceValue = provinceSelect.val();
            // Si no hay valor en el selector, intentar leer del template (Smarty)
            if (!selectedProvinceValue) {
                {if isset($selected_province) && $selected_province}
                selectedProvinceValue = '{$selected_province|escape:'javascript':'UTF-8'}';
                {/if}
            }
            console.log('Valor seleccionado a preservar:', selectedProvinceValue);
        }
        
        // Limpiar selector
        provinceSelect.html('<option value="">{l s='-- Selecciona una provincia/estado --' mod='shippingcalculator'|escape:'javascript':'UTF-8'}</option>');
        console.log('Selector limpiado');
        
        if (!countryIdNum || isNaN(countryIdNum) || countryIdNum <= 0) {
            console.warn('País no válido:', countryIdNum);
            if (provinceHelp.length) {
                provinceHelp.show();
            }
            return;
        }
        
        // Buscar estados del país
        var states = null;
        var statesObj = window.statesByCountry;
        
        if (!statesObj) {
            console.error('ERROR: window.statesByCountry no está definido');
            console.error('Tipo de window.statesByCountry:', typeof window.statesByCountry);
            return;
        }
        
        console.log('Buscando estados para país ID:', countryIdNum);
        console.log('Claves disponibles en statesByCountry:', Object.keys(statesObj));
        console.log('¿Existe como número?', countryIdNum in statesObj);
        console.log('¿Existe como string?', countryIdNum.toString() in statesObj);
        
        // Intentar con número primero
        states = statesObj[countryIdNum];
        console.log('Estados encontrados con número [' + countryIdNum + ']:', states ? (states.length + ' provincias') : 'null');
        
        // Si no funciona, intentar con string
        if (!states || !Array.isArray(states) || states.length === 0) {
            states = statesObj[countryIdNum.toString()];
            console.log('Estados encontrados con string ["' + countryIdNum + '"]:', states ? (states.length + ' provincias') : 'null');
        }
        
        // Agregar provincias
        if (states && Array.isArray(states) && states.length > 0) {
            console.log('✓ Agregando', states.length, 'provincias para país', countryIdNum);
            var addedCount = 0;
            $.each(states, function(index, state) {
                if (state && state.iso_code && state.name) {
                    var option = $('<option></option>')
                        .attr('value', state.iso_code)
                        .text(state.name);
                    
                    // Si el valor coincide con el seleccionado, marcarlo
                    if (selectedProvinceValue && selectedProvinceValue === state.iso_code) {
                        option.attr('selected', 'selected');
                        console.log('✓ Provincia seleccionada restaurada:', state.iso_code, state.name);
                    }
                    
                    provinceSelect.append(option);
                    addedCount++;
                }
            });
            console.log('✓ Provincias agregadas correctamente:', addedCount, 'de', states.length);
            console.log('Total opciones en selector:', provinceSelect.find('option').length);
            
            // Restaurar el valor seleccionado si existe y está disponible
            if (selectedProvinceValue && provinceSelect.find('option[value="' + selectedProvinceValue + '"]').length > 0) {
                provinceSelect.val(selectedProvinceValue);
                console.log('✓ Valor seleccionado restaurado:', selectedProvinceValue);
            }
            
            // Forzar actualización visual del selector
            provinceSelect.trigger('change');
            
            if (provinceHelp.length) {
                provinceHelp.hide();
            }
        } else {
            console.warn('✗ No se encontraron provincias para el país', countryIdNum);
            console.warn('Estados encontrados:', states);
            if (provinceHelp.length) {
                provinceHelp.show();
            }
        }
        
        console.log('=== updateProvinceSelectWithCountry FINALIZADO ===');
    };
    
    // SOLUCIÓN SIMPLE Y DIRECTA: Usar delegación de eventos en document
    // Esto funciona incluso si el selector se recrea dinámicamente
    $(document).on('change', '#id_country', function(e) {
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        var selectedValue = $(this).val();
        var selectedValueNum = parseInt(selectedValue);
        
        console.log('=== CAMBIO DE PAÍS DETECTADO ===');
        console.log('País seleccionado:', selectedValue, 'ID numérico:', selectedValueNum);
        console.log('Elemento:', this);
        console.log('updateProvinceSelectWithCountry disponible:', typeof window.updateProvinceSelectWithCountry !== 'undefined');
        console.log('statesByCountry disponible:', typeof window.statesByCountry !== 'undefined');
        
        if (window.updateProvinceSelectWithCountry && selectedValueNum > 0) {
            // Si el país cambió, no preservar el valor (porque las provincias serán diferentes)
            window.updateProvinceSelectWithCountry(selectedValueNum, false);
        } else {
            console.error('ERROR: No se puede actualizar provincias');
            console.error('  - updateProvinceSelectWithCountry:', typeof window.updateProvinceSelectWithCountry);
            console.error('  - selectedValueNum:', selectedValueNum);
        }
    });
    
    // También agregar listener directo como respaldo (después de un pequeño delay)
    setTimeout(function() {
        var countrySelect = document.getElementById('id_country');
        if (countrySelect) {
            console.log('✓ Agregando listener directo a #id_country');
            
            // Agregar listener con JavaScript vanilla
            countrySelect.addEventListener('change', function(e) {
                var selectedValue = this.value;
                var selectedValueNum = parseInt(selectedValue);
                
                console.log('=== CAMBIO DE PAÍS DETECTADO (Listener directo Vanilla JS) ===');
                console.log('País seleccionado:', selectedValue, 'ID:', selectedValueNum);
                
                if (window.updateProvinceSelectWithCountry && selectedValueNum > 0) {
                    window.updateProvinceSelectWithCountry(selectedValueNum);
                }
            }, true); // Usar capture phase para capturar antes que otros listeners
            
            // También con jQuery como respaldo adicional
            $('#id_country').off('change.provinceUpdate').on('change.provinceUpdate', function() {
                var selectedValue = $(this).val();
                var selectedValueNum = parseInt(selectedValue);
                
                console.log('=== CAMBIO DE PAÍS DETECTADO (jQuery directo) ===');
                console.log('País seleccionado:', selectedValue, 'ID:', selectedValueNum);
                
                if (window.updateProvinceSelectWithCountry && selectedValueNum > 0) {
                    window.updateProvinceSelectWithCountry(selectedValueNum);
                }
            });
        } else {
            console.warn('Selector #id_country no encontrado para listener directo');
        }
    }, 300);
    
    
    // Función de prueba accesible desde la consola
    window.testProvinceUpdate = function(countryId) {
        console.log('=== TEST MANUAL DE ACTUALIZACIÓN DE PROVINCIAS ===');
        console.log('País ID a probar:', countryId);
        console.log('window.statesByCountry:', window.statesByCountry);
        console.log('window.updateProvinceSelectWithCountry:', typeof window.updateProvinceSelectWithCountry);
        
        if (window.updateProvinceSelectWithCountry) {
            window.updateProvinceSelectWithCountry(countryId);
        } else {
            console.error('ERROR: updateProvinceSelectWithCountry no está disponible');
        }
    };
    
    // Test automático al cargar la página
    setTimeout(function() {
        console.log('=== TEST AUTOMÁTICO AL CARGAR ===');
        console.log('window.statesByCountry disponible:', typeof window.statesByCountry !== 'undefined');
        console.log('window.updateProvinceSelectWithCountry disponible:', typeof window.updateProvinceSelectWithCountry !== 'undefined');
        console.log('Selector #id_country existe:', document.getElementById('id_country') !== null);
        console.log('Selector #province_code existe:', document.getElementById('province_code') !== null);
        
        if (window.statesByCountry) {
            var keys = Object.keys(window.statesByCountry);
            console.log('Países disponibles en statesByCountry:', keys.length);
            if (keys.length > 0) {
                console.log('Primeros 5 países:', keys.slice(0, 5));
                var firstCountry = parseInt(keys[0]);
                console.log('Estados del primer país (' + firstCountry + '):', window.statesByCountry[firstCountry] ? window.statesByCountry[firstCountry].length : 0);
            }
        }
        
        // Si hay un país seleccionado, cargar sus provincias al inicio
        // PERO preservar el valor de provincia seleccionado si existe
        {if isset($selected_country_id) && $selected_country_id}
        var defaultCountry = document.getElementById('id_country');
        if (defaultCountry && defaultCountry.value) {
            var defaultCountryNum = parseInt(defaultCountry.value);
            console.log('Inicializando provincias para país por defecto:', defaultCountry.value, 'ID:', defaultCountryNum);
            if (!isNaN(defaultCountryNum) && defaultCountryNum > 0) {
                if (window.updateProvinceSelectWithCountry) {
                    // Preservar el valor seleccionado de provincia al inicializar
                    var selectedProvince = {if isset($selected_province) && $selected_province}'{$selected_province|escape:'javascript':'UTF-8'}'{else}''{/if};
                    console.log('Provincia seleccionada a preservar:', selectedProvince);
                    window.updateProvinceSelectWithCountry(defaultCountryNum, true);
                }
            }
        }
        {/if}
    }, 1000);
    
});
</script>

<script>
// Filtro de país - Solución única y definitiva
(function() {
    'use strict';
    
    function applyCountryFilter() {
        var filterSelect = document.getElementById('filter_country');
        var table = document.getElementById('shipping-delays-table');
        
        if (!filterSelect || !table) {
            return;
        }
        
        var selectedValue = filterSelect.value;
        var selectedCountryId = parseInt(selectedValue);
        var rows = table.querySelectorAll('tbody tr.province-row');
        var tbody = table.querySelector('tbody');
        var messageRow = document.getElementById('no-provinces-message');
        
        // Remover mensaje anterior
        if (messageRow) {
            messageRow.remove();
        }
        
        var visibleCount = 0;
        
        // Procesar cada fila
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var rowCountryIdAttr = row.getAttribute('data-country-id');
            var rowCountryId = parseInt(rowCountryIdAttr);
            
            // Si no hay país seleccionado, ocultar todas
            if (!selectedValue || selectedValue === '' || isNaN(selectedCountryId) || selectedCountryId === 0) {
                row.style.display = 'none';
            } else {
                // Comparar IDs numéricos - usar comparación estricta
                if (!isNaN(rowCountryId) && rowCountryId === selectedCountryId) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
        }
        
        // Mostrar mensaje si no hay filas visibles
        if (visibleCount === 0 && selectedCountryId > 0 && tbody) {
            var msg = document.createElement('tr');
            msg.id = 'no-provinces-message';
            msg.innerHTML = '<td colspan="4" class="text-center text-muted"><em>No hay provincias configuradas para este país</em></td>';
            tbody.appendChild(msg);
        }
    }
    
    function initCountryFilter() {
        var filterSelect = document.getElementById('filter_country');
        
        if (!filterSelect) {
            // Reintentar después de 100ms
            setTimeout(initCountryFilter, 100);
            return;
        }
        
        // Remover cualquier evento anterior
        var newSelect = filterSelect.cloneNode(true);
        filterSelect.parentNode.replaceChild(newSelect, filterSelect);
        
        // Asignar nuevo evento
        newSelect.addEventListener('change', function() {
            applyCountryFilter();
        });
        
        // Establecer valor por defecto
        var defaultCountryId = {if isset($filter_country_id) && $filter_country_id}{$filter_country_id|intval}{elseif isset($default_country_id) && $default_country_id}{$default_country_id|intval}{else}0{/if};
        
        if (defaultCountryId && defaultCountryId > 0) {
            newSelect.value = defaultCountryId.toString();
        } else {
            newSelect.value = '';
        }
        
        // Aplicar filtro inicial
        setTimeout(function() {
            applyCountryFilter();
        }, 50);
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCountryFilter);
    } else {
        // DOM ya está listo
        initCountryFilter();
    }
    
    // También ejecutar después de que todo esté cargado
    window.addEventListener('load', function() {
        setTimeout(initCountryFilter, 100);
    });
})();
</script>

<script>
// Función para aplicar los valores min/max globales a las filas visibles (provincias del país filtrado)
function applyGlobalMinMaxToVisibleProvinces() {
    var minVal = $('#apply_days_min').val();
    var maxVal = $('#apply_days_max').val();

    // Validar entradas
    if ((minVal === null || minVal === '') && (maxVal === null || maxVal === '')) {
        alert('Por favor, introduce al menos un valor en Días mín. o Días máx.');
        return;
    }

    // Recorrer filas visibles y aplicar valores
    $('#shipping-delays-table tbody tr.province-row:visible').each(function() {
        var $row = $(this);
        // Encontrar inputs y setear valores si se proporcionaron
        var $minInput = $row.find('input[name$="[days_min]"]');
        var $maxInput = $row.find('input[name$="[days_max]"]');
        if (minVal !== null && minVal !== '') {
            $minInput.val(minVal);
        }
        if (maxVal !== null && maxVal !== '') {
            $maxInput.val(maxVal);
        }
    });

    // Mensaje de confirmación eliminado (operación silenciosa)
}

$(document).ready(function() {
    $('#apply_min_max_btn').on('click', function() {
        applyGlobalMinMaxToVisibleProvinces();
    });
});
</script>

<!-- Modal para importar CSV -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{l s='Importar CSV' mod='shippingcalculator'}</h4>
            </div>
            <form method="post" enctype="multipart/form-data" action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}">
                <div class="modal-body">
                    <input type="hidden" name="importCSV" value="1" />
                    <input type="hidden" name="token" value="{$smarty.get.token|escape:'html':'UTF-8'}" />
                    
                    <div class="form-group">
                        <label>{l s='Archivo CSV' mod='shippingcalculator'}</label>
                        <input type="file" name="csv_file" accept=".csv" class="form-control" required />
                        <p class="help-block">
                            {l s='Formato requerido: El CSV debe tener las columnas:' mod='shippingcalculator'}<br>
                            <strong>province_code, province_name, delivery_days_min, delivery_days_max</strong><br>
                            {l s='El separador debe ser punto y coma (;)' mod='shippingcalculator'}
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancelar' mod='shippingcalculator'}</button>
                    <button type="submit" class="btn btn-primary">{l s='Importar' mod='shippingcalculator'}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function exportToCSV() {
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
        name: 'token',
        value: '{$smarty.get.token|escape:'javascript':'UTF-8'}'
    }));
    
    $('body').append(form);
    form.submit();
    form.remove();
}

function showImportModal() {
    $('#importModal').modal('show');
}
</script>

<script>



