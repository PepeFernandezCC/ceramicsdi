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

SDEVADEO.controller.admin.productFilter = {
    addFilter: function() {
        let panel = document.querySelector('[id="filter-panel"]>fieldset.form-horizontal.panel');
        panel.classList.remove('hidden');
        panel.querySelector('.add-filter').classList.remove('hidden');
        // reset values
        panel.querySelectorAll('input').forEach(function (inputElement) {
            inputElement.value = '';
        })
    },

    closeFilter: function(idFilter) {
        if (idFilter === undefined) {
            document.querySelector('[id="filter-panel"]>fieldset.form-horizontal.panel').classList.add('hidden');
        } else {
            if (document.querySelector('[data-edit-filter="'+idFilter+'"]')) {
                document.querySelector('[data-edit-filter="'+idFilter+'"]').classList.add('hidden');
            }
        }
    },

    saveFilter: function (idFilter) {
        SDEVADEO.handleButtons(true);
        document.querySelector('[id="filter-notification"]').className = '';
        document.querySelector('[id="filter-notification"]').innerHTML = '';

        let refNode;
        if (idFilter === undefined) {
            refNode = document.querySelector('[id="filter-panel"]>fieldset');
            idFilter = 0;
        } else {
            refNode = document.querySelector('[data-edit-filter="' + idFilter + '"] fieldset');
        }
        console.log(refNode);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'saveFilter',
                params: {
                    'idFilter': idFilter,
                    'filterType': refNode.querySelector('[id="filter-type"]').value,
                    'filterTarget': refNode.querySelector('[id="filter-target"]').value,
                    'filterValue': refNode.querySelector('[id="filter-value"]').value,
                }
            },
            success: function (response) {
                if (response['hasError'] === false) {
                    document.querySelector('[id="filter-notification"]').classList.add('conf', 'alert', 'alert-success');
                    if (response['idFilter'] !== undefined && idFilter === 0) {
                        let trNode = document.createElement('tr');
                        trNode.setAttribute('data-id-filter', response['idFilter']);
                        // filter id
                        let tdNode = document.createElement('td');
                        tdNode.className = 'filterId';
                        tdNode.innerHTML = response['idFilter'];
                        trNode.appendChild(tdNode);

                        tdNode = document.createElement('td');
                        tdNode.className = 'filterType';
                        tdNode.innerHTML = refNode.querySelector('[id="filter-type"] option:checked').innerHTML;
                        trNode.appendChild(tdNode);

                        tdNode = document.createElement('td');
                        tdNode.className = 'filterTarget';
                        tdNode.innerHTML = refNode.querySelector('[id="filter-target"] option:checked').innerHTML;
                        trNode.appendChild(tdNode);

                        tdNode = document.createElement('td');
                        tdNode.className = 'filterValue';
                        tdNode.innerHTML = refNode.querySelector('[id="filter-value"]').value;
                        trNode.appendChild(tdNode);
    
                        tdNode = document.createElement('td');
                        buttonElement = document.createElement('button');
                        buttonElement.classList.add('button', 'btn', 'btn-warning');
                        buttonElement.setAttribute('onClick', 'SDEVADEO.controller.admin.productFilter.editFilter(\''+response['idFilter']+'\')');
                        buttonElement.textContent = modifyTranslation;
                        tdNode.appendChild(buttonElement);
                        tdNode.append('\n');

                        buttonElement = document.createElement('button');
                        buttonElement.classList.add('button', 'btn', 'btn-danger');
                        buttonElement.setAttribute('onClick', 'SDEVADEO.controller.admin.productFilter.deleteFilter('+response['idFilter']+')');
                        buttonElement.textContent = deleteTranslation;
                        tdNode.appendChild(buttonElement);
                        trNode.appendChild(tdNode);
    
                        if (document.querySelector('[id="filter-panel"] tbody tr.no-filters')) {
                            document.querySelector('[id="filter-panel"] tbody').innerHTML = '';
                        }

                        document.querySelector('[id="filter-panel"] tbody').appendChild(trNode);
                    } else {
                         // Update
                         let filterTr = document.querySelector('[id="filter-panel"] [data-id-filter="'+idFilter+'"]');
                         filterTr.querySelector('[class="filterType"]').innerHTML = refNode.querySelector('[id="filter-type"] option:checked').innerHTML;
                         filterTr.querySelector('[class="filterTarget"]').innerHTML = refNode.querySelector('[id="filter-target"] option:checked').innerHTML;
                         filterTr.querySelector('[class="filterValue"]').innerHTML = refNode.querySelector('[id="filter-value"]').value;
                    }
                } else {
                    document.querySelector('[id="filter-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }

                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="filter-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                if (idFilter !== 0) {
                    document.querySelector('[data-edit-filter="'+idFilter+'"]').remove();
                } else {
                    document.querySelector('[id="filter-panel"] fieldset.form-horizontal.panel').classList.add('hidden');
                }
                SDEVADEO.handleButtons(false);
            }
        })

        this.closeFilter();
    },

    deleteFilter: function(idFilter) {
        if (!confirm(deleteMessage)) {
            return;
        } else {
            SDEVADEO.handleButtons(true);
        }
        document.querySelector('[id="filter-notification"]').classList.add('hidden');
        document.querySelector('[id="filter-notification"]').innerHTML = "";

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'deleteFilter',
                params: {
                    'idFilter': idFilter
                }
            },
            success: function (response) {
                document.querySelector('[id="filter-notification"]').className = '';
                if (response['hasError'] === true) {
                    document.querySelector('[id="filter-notification"]').classList.add('warn', 'alert', 'alert-danger');
                } else {
                    document.querySelector('[id="filter-notification"]').classList.add('conf', 'alert', 'alert-success');
                    document.querySelector('[id="filter-panel"] [data-id-filter="'+idFilter+'"]').remove();
                }
                response['errorMessage'].forEach(function (item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="filter-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            }
        })
    },

    editFilter: function (filterId) {
        SDEVADEO.handleButtons(true);

        document.querySelector('[id="filter-panel"]>fieldset.form-horizontal.panel').classList.add('hidden');
        document.querySelectorAll('[data-edit-filter]').forEach(function(node){
            node.classList.add('hidden');
        });
        let editPanel = document.querySelector('[data-edit-filter="' + filterId + '"]');
        if (editPanel) {
            if (editPanel.className == 'hidden') {
                editPanel.classList.remove('hidden');
            }
            SDEVADEO.handleButtons(false);
        } else {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    action: 'editFilter',
                    params: {
                        'filterId': filterId
                    }
                },
                success: function (response) {
                    let trElement = document.createElement('tr');
                    trElement.setAttribute('data-edit-filter', filterId);
                    let tdElement = document.createElement('td');
                    tdElement.setAttribute('colspan', "100%");
                    let editNode = document.querySelector('[id="filter-panel"]>fieldset.panel.form-horizontal').cloneNode(true);
                    tdElement.appendChild(editNode);
                    trElement.appendChild(tdElement);
                    let filterToEdit = document.querySelector('[data-id-filter="' + filterId + '"]');
                    filterToEdit.parentNode.insertBefore(trElement, filterToEdit.nextSibling);

                    // select edit window
                    editNode = document.querySelector('[data-edit-filter="' + filterId + '"]');

                    editNode.querySelector('.cancel-filter').setAttribute('onclick', 'SDEVADEO.controller.admin.productFilter.closeFilter(' + filterId + ')');
                    editNode.querySelector('.edit-filter').setAttribute('onclick', 'SDEVADEO.controller.admin.productFilter.saveFilter(' + filterId + ')');
                    editNode.querySelector('.add-filter').setAttribute('onclick', 'SDEVADEO.controller.admin.productFilter.saveFilter(' + filterId + ')');
                    editNode.querySelector('[name="filter-type"] option[value="' + response['filterType'] + '"]').selected = true;
                    editNode.querySelector('[name="filter-target"] option[value="' + response['filterTarget'] + '"]').selected = true;
                    editNode.querySelector('[id="filter-value"]').value = response['filterValue'];

                    editNode.querySelector('.add-filter').classList.add('hidden');
                    editNode.querySelector('.edit-filter').classList.remove('hidden');
                    document.querySelector('[data-edit-filter="' + filterId + '"] fieldset').classList.remove('hidden');
                },
                error: function (response) {
                    console.log(response);
                },
                complete: function () {
                    SDEVADEO.handleButtons(false);
                }
            });
        }
    },
}
