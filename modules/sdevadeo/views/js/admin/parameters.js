/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

SDEVADEO.controller.admin.parameters = {
    saveGenerals: function() {
        SDEVADEO.handleButtons(true);
        let enabled_countries = [];
        document.querySelectorAll('[id="enabled_countries"] > option:checked').forEach(function (item) {
            enabled_countries.push(item.value);
        })
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'save',
                params: {
                    ENABLED_DISCOUNT: document.querySelector('[id="discount-switch"] > input:checked').value,
                    ENABLED_SALES: document.querySelector('[id="sales-switch"] > input:checked').value,
                    USED_DESCRIPTION: document.querySelector('[id="description_select"] > option:checked').value,
                    AUTO_VALIDATE: document.querySelector('[id="automatic-validation-switch"] > input:checked').value,
                    DISABL_PRODUCT: document.querySelector('[id="disabled-products-switch"] > input:checked').value,
                    DISABL_CATEG: document.querySelector('[id="disabled-categories-switch"] > input:checked').value,
                    ENABLED_COUNTRIES: enabled_countries,
                    SHIPPING_COUNTRY: document.querySelector('[id="shipping_country"] > option:checked').value,
                }
            },
            success: function (response) {
                document.querySelector('[id="generals-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    document.querySelector('[id="generals-notification"]').classList.add('conf', 'alert', 'alert-success');

                } else {
                    document.querySelector('[id="generals-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="generals-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            },
        });
    },

    saveProducts: function() {
        SDEVADEO.handleButtons(true);

        // retrieve taxes as array
        let mapped_taxes = [];
        let taxes = document.querySelector('[for=tax_mapping]').closest('fieldset');
        for (let i = 1; i < taxes.children.length; i++) {
            let select = taxes.children[i];
            let tax = select.children[0].innerHTML.trim();
            mapped_taxes[select.children[0].innerHTML.trim()] = select.querySelector('option:checked').value;
        }
        mapped_taxes = Object.assign({}, mapped_taxes);

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'save',
                params: {
                    PRODUCT_BURST: document.querySelector('[id="number_per_burst"]').value,
                    FLOW_TYPE: document.querySelector('[id="flow_type"]').value,
                    TAX_MAPPING: Object.assign({}, mapped_taxes)
                }
            },
            success: function (response) {
                document.querySelector('[id="products-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    document.querySelector('[id="products-notification"]').classList.add('conf', 'alert', 'alert-success');

                } else {
                    document.querySelector('[id="products-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="products-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            },
        });
    },
    saveOrders: function() {
        SDEVADEO.handleButtons(true);
        let shipped_states = [];
        document.querySelectorAll('[id="shipped_state"] > option:checked').forEach(function (item) {
            shipped_states.push(item.value);
        })

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'save',
                params: {
                    IMPORTED_STATE: document.querySelector('[id="imported_state"]').value,
                    SHIPPED_STATE: shipped_states,
                    SHIPPING_CRON: document.querySelector('[id="shipment-switch"] > input:checked').value,
                    LAST_SHIPPING: document.querySelector('[id="last_shipment_cron"]').value,
                }
            },
            success: function (response) {
                document.querySelector('[id="orders-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    document.querySelector('[id="orders-notification"]').classList.add('conf', 'alert', 'alert-success');
                } else {
                    document.querySelector('[id="orders-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="orders-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
            },
        });
        SDEVADEO.handleButtons(false);
    },
    saveCarriers: function() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'save',
                params: {
                    SHIPPING_COST: document.querySelector('[id="additional_shipping"]').value,
                }
            },
            success: function (response) {
                document.querySelector('[id="carriers-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    document.querySelector('[id="carriers-notification"]').classList.add('conf', 'alert', 'alert-success');
                } else {
                    document.querySelector('[id="carriers-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="carriers-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
            },
        });
    },
    updateCarrierList: function() {
        SDEVADEO.handleButtons(true);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'updateShippingMethods',
            },
            success: function (response) {
                document.querySelector('[id="carriers-notification"]').className = '';
                document.querySelector('[id="carriers-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    document.querySelector('[id="carriers-notification"]').classList.add('conf', 'alert', 'alert-success');
                    if (response['carriers'] !== undefined) {
                        let inputNode;
                        console.log(document.querySelectorAll('select#marketplace_carrier'));
                        document.querySelectorAll('select#marketplace_carrier').forEach(function(selectElement){
                            selectElement.innerHTML = '';
                            response['carriers'].forEach(function (carrier) {
                                inputNode = document.createElement('option');
                                inputNode.setAttribute('value', carrier['code']);
                                inputNode.textContent = carrier['label']
                                selectElement.appendChild(inputNode);
                            })
                        })
                    }
                } else {
                    document.querySelector('[id="carriers-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="carriers-notification"]').appendChild($childNode);
                })
                document.querySelector('[id="connection-panel"] table').classList.remove('hidden');
                if (document.querySelector('[id="connection-panel"] > button')) {
                    document.querySelector('[id="connection-panel"] > button').classList.add('hidden');
                }
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            },
        });
    },
    saveCarrierRule: function() {
        document.querySelector('[id="carriers-notification"]').className = '';
        document.querySelector('[id="carriers-notification"]').innerHTML = '';
        SDEVADEO.handleButtons(true);
        let carrier_rules = [];
        document.querySelectorAll('#carrier-rule-table tbody tr').forEach(function (trElement){
            let cms_carrier;
            if ((cms_carrier = trElement.querySelector('#cms_carrier').value) !== '-') {
                carrier_rules.push({
                    'marketplaceShippingCode': trElement.getAttribute('data-code-method'),
                    'internalCarrierId': cms_carrier
                });
            }
        })
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'saveCarrierRule',
                params: {
                    carrier_rules
                }
            },
            success: function (response) {
                console.log(response);
                document.querySelector('[id="carriers-notification"]').innerHTML = '';
                if (response['hasError']) {
                    document.querySelector('[id="carriers-notification"]').classList.add('warn', 'alert', 'alert-danger');
                } else {
                    document.querySelector('[id="carriers-notification"]').classList.add('conf', 'alert', 'alert-success');
                }
                response['errorMessage'].forEach(function(item) {
                    let childNode = document.createElement('p');
                    childNode.innerHTML = item;
                    document.querySelector('[id="carriers-notification"]').appendChild(childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            },
        });
    },

    addFiltered: function(filterType) {
        document.querySelectorAll('[id="filter_'+filterType+'"] option:checked').forEach(function(element) {
            document.querySelector('[id="disabled_'+filterType+'"]').appendChild(element.cloneNode(true));
            element.remove();
        })
    },

    removeFiltered: function(filterType) {
        document.querySelectorAll('[id="disabled_'+filterType+'"] option:checked').forEach(function(element) {
            document.querySelector('[id="filter_'+filterType+'"]').appendChild(element.cloneNode(true));
            element.remove();
        })
    },

    saveFilters : function() {
        SDEVADEO.handleButtons(true);
        let disabled_manufacturers = [];
        let disabled_suppliers = [];
        document.querySelectorAll('[id="disabled_manufacturer"] option:not([disabled])').forEach(function(element) {
            disabled_manufacturers.push(element.value);
        })
        document.querySelectorAll('[id="disabled_supplier"] option:not([disabled])').forEach(function(element) {
            disabled_suppliers.push(element.value);
        })
        if (disabled_manufacturers.length === 0) {
            disabled_manufacturers = 'empty';
        }
        if (disabled_suppliers.length === 0) {
            disabled_suppliers = 'empty';
        }

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'save',
                params: {
                    'EXCL_MANUFACT': disabled_manufacturers,
                    'EXCL_SUPPLIER': disabled_suppliers
                }
            },
            success: function (response) {
                document.querySelector('[id="filters-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    document.querySelector('[id="filters-notification"]').classList.add('conf', 'alert', 'alert-success');
                } else {
                    document.querySelector('[id="filters-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="filters-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            },
        });
    }
}