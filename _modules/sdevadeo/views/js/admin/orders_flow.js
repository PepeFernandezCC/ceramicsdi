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
$(document).ready(function() {
    $('th input.sdev-checkbox-accept-order').change(function(){
        let checked = this.checked;
        let inputs = $("td input[class^=\'sdev-checkbox-accept-order-\']");
        if (inputs.length > 0) {
            inputs.each(function () {
                $(this).attr('checked', checked);
            })
        }
    });
})

SDEVADEO.controller.admin.ordersFlow = {
    getOrders: function () {
        SDEVADEO.handleButtons(true);
        let module = document.querySelector('[data-module="sdevadeo"]');
        let body = module.querySelector('#display-orders tbody');
        module.querySelector('#error-notification').classList.add('hidden');
        module.querySelector('#success-notification').classList.add('hidden');
        module.querySelector('#error-notification').innerHTML = "";
        module.querySelector('#success-notification').innerHTML = "";
        module.querySelector('#getting-orders-loading i.fa-refresh').classList.add('fa-spin');
        module.querySelector('#getting-orders-loading').classList.remove('hidden');
        module.querySelector('#no-order-retrieve').classList.add('hidden');
        while (body.lastChild.id !== 'getting-orders-loading' && body.lastChild.id !== 'no-order-retrieve') {
            body.removeChild(body.lastChild);
        }
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'getOrders',
            },
            success: function (response) {
                if (response['error'] !== undefined) {
                    module.querySelector('#error-notification').classList.remove('hidden');
                    let pNode = document.createElement('p');
                    pNode.textContent = response['error'];
                    module.querySelector('#error-notification').appendChild(pNode);
                } else {
                    if (Object.keys(response).length > 0) {
                        Object.keys(response).forEach(function(element) {
                            element = response[element];
                            if (
                                element['order_id']
                                && element['created_date']
                                && element['customer']['firstname']
                                && element['customer']['lastname']
                                && element['order_lines']['list'].length > 0
                                && element['total_price']
                                && element['order_state']
                                && in_array(
                                    element['order_state'],
                                    [
                                        'WAITING_ACCEPTANCE',
                                        'WAITING_DEBIT_PAYMENT',
                                        'SHIPPING'
                                    ]
                                )
                            ) {
                                let orderTab = body;
                                let trNode = document.createElement('tr');
                                let tdNode = document.createElement('td');
                                if (element['order_state'] !== 'WAITING_DEBIT_PAYMENT') {
                                    let inputNode = document.createElement('input');
                                    inputNode.setAttribute('type', 'checkbox');
                                    inputNode.classList.add('sdev-checkbox-accept-order-' + element['order_id']);
                                    tdNode.appendChild(inputNode);
                                }
                                trNode.appendChild(tdNode);

                                // Handle marketplace order id
                                tdNode = document.createElement('td');
                                tdNode.textContent = element['order_id'];
                                trNode.appendChild(tdNode);

                                // Handle creation date
                                tdNode = document.createElement('td');
                                tdNode.textContent = new Date(element['created_date']).toString();
                                trNode.appendChild(tdNode);

                                // Handle customer name
                                tdNode = document.createElement('td');
                                tdNode.textContent = element['customer']['firstname'] + ' ' + element['customer']['lastname'];
                                trNode.appendChild(tdNode);

                                // Handle order lines
                                tdNode = document.createElement('td');
                                let ulNode = document.createElement('ul');
                                element['order_lines']['list'].forEach(function (orderLine) {
                                    let liNode = document.createElement('li');
                                    if (orderLine['quantity']) {
                                        liNode.textContent = orderLine['quantity'] + 'x';
                                    }
                                    if (orderLine['product_sku']) {
                                        liNode.textContent += ' (' + orderLine['offer_sku'] + ') ';
                                    }
                                    if (orderLine['product_title']) {
                                        liNode.textContent += orderLine['product_title'];
                                    }
                                    ulNode.appendChild(liNode);
                                })
                                tdNode.appendChild(ulNode);
                                trNode.appendChild(tdNode);

                                // Handle total price
                                tdNode = document.createElement('td');
                                tdNode.textContent = element['total_price'];
                                trNode.appendChild(tdNode);

                                // Handle order state
                                tdNode = document.createElement('td');
                                tdNode.textContent = stateName[element['order_state']];
                                tdNode.classList.add('mp_order_state');
                                trNode.appendChild(tdNode);

                                // Add 'tr' to list
                                if (element['order_state'] === 'WAITING_DEBIT_PAYMENT') {
                                    trNode.childNodes.forEach(function(node){
                                        node.classList.add('info');
                                    })
                                } else if (element['order_state'] === 'SHIPPING') {
                                    trNode.childNodes.forEach(function(node){
                                        node.classList.add('warning');
                                    })
                                } else if (element['order_state'] === 'WAITING_ACCEPTANCE'){
                                    trNode.childNodes.forEach(function(node){
                                        node.classList.add('success');
                                    })
                                }
                                trNode.setAttribute('mp_order_id', element['order_id']);
                                orderTab.appendChild(trNode);
                            }
                        })
                    } else {
                        module.querySelector('#no-order-retrieve').classList.remove('hidden');
                    }
                }
            },
            error: function (response) {
                module.querySelector('#error-notification').classList.remove('hidden');
                let pNode = document.createElement('p');
                pNode.textContent = response;
                module.querySelector('#error-notification').appendChild(pNode);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
                document.querySelector('[data-module="sdevadeo"] #getting-orders-loading i').classList.remove('fa-spin');
                document.querySelector('[data-module="sdevadeo"] #getting-orders-loading').classList.add('hidden');
            }
        })
    },

    importOrders: function () {
        SDEVADEO.handleButtons(true);
        let module = document.querySelector('[data-module="sdevadeo"]');
        module.querySelector('#error-notification').classList.add('hidden');
        module.querySelector('#success-notification').classList.add('hidden');
        module.querySelector('#error-notification').innerHTML = "";
        module.querySelector('#success-notification').innerHTML = "";
        let elements = [];
        module.querySelectorAll("td input[class^=\'sdev-checkbox-accept-order-\']:checked").forEach(function(element){
            elements.push(element.parentElement.nextSibling.textContent);
        });
        if (elements.length > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    action: 'acceptOrders',
                    params: elements
                },
                success: function (response) {
                    if (response['shipping'] !== undefined) {
                        if (response['shipping']['success'] !== undefined) {
                            module.querySelector('#success-notification').classList.remove('hidden');
                            response['shipping']['success'].forEach(function (mpOrder) {
                                let pNode = document.createElement('p');
                                pNode.textContent = '['+stateName['SUCCESS']+'] '+Object.keys(mpOrder)[0];
                                module.querySelector('#success-notification').appendChild(pNode);
                                document.querySelector('#display-orders [mp_order_id="'+Object.keys(mpOrder)[0]+'"]').remove();
                            })
                        }
                        if (response['shipping']['error'] !== undefined) {
                            module.querySelector('#error-notification').classList.remove('hidden');
                            response['shipping']['error'].forEach(function (mpOrder) {
                                let pNode = document.createElement('p');
                                pNode.textContent = '['+stateName['ERROR']+'] '+Object.keys(mpOrder)[0]+': '+mpOrder[Object.keys(mpOrder)[0]];
                                module.querySelector('#error-notification').appendChild(pNode);
                            })
                        }
                    }
                    if (response['accepted'] !== undefined) {
                        response['accepted'].forEach(function (mpOrder) {
                            document.querySelectorAll('#display-orders [mp_order_id="'+mpOrder+'"] td').forEach(function(element){
                                element.classList.remove('success');
                                element.classList.add('info');
                            });
                            document.querySelector('#display-orders [mp_order_id="'+mpOrder+'"] .mp_order_state').textContent = stateName['WAITING_DEBIT_PAYMENT'];
                        })
                    }
                },
                error: function (response) {
                    module.querySelector('#error-notification').classList.remove('hidden');
                    let pNode = document.createElement('p');
                    pNode.textContent = response;
                    module.querySelector('#error-notification').appendChild(pNode);
                },
                complete: function () {
                    SDEVADEO.handleButtons(false);
                }
            })
        } else {
            module.querySelector('#error-notification').classList.remove('hidden');
            let pNode = document.createElement('p');
            pNode.textContent = noElementNotification;
            module.querySelector('#error-notification').appendChild(pNode);
            SDEVADEO.handleButtons(false);
        }
    }
}
