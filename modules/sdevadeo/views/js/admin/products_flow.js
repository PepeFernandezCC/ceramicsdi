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

SDEVADEO.controller.admin.productsFlow = {
    logs: {},
    nbProductsMax: 0,
    nbProductsFiltered: 0,
    nbProductsInError: 0,
    currentProductsNb: 0,
    generateProductFlow: function() {
        const $this = this;
        document.querySelector('[id="flux-prod-success"] .well').innerHTML = '';
        document.querySelector('[id="flux-prod-filtered"] .well').innerHTML = '';
        document.querySelector('[id="flux-prod-error"] .well').innerHTML = '';
        $this.logs = {};
        $this.nbProductsMax = 0;
        $this.nbProductsFiltered = 0;
        $this.nbProductsInError = 0;
        $this.currentProductsNb = 0;
        SDEVADEO.handleButtons(true);
        $this.handleProductBurst(0, 0);
    },

    handleProductBurst: function(currentProductsNb, nbProductsMax) {
        const $this = this;
        $this.currentProductsNb = currentProductsNb;
        document.querySelector('#flux-notification-error').innerHTML = '';
        document.querySelector('#flux-notification-error').classList.add('hidden');
        document.querySelector('#flux-notification-success').innerHTML = '';
        document.querySelector('#flux-notification-success').classList.add('hidden');
        let max;
        let value;
        let progressBar = document.querySelector('#product-flow progress');
        let progressMessage = document.querySelector('#product-flow #nbProductsProcessed');
        let percentProgressMessage = progressMessage.querySelector('#percent_value');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'generateProductFlow',
                params: {
                    'currentProductsNb': currentProductsNb,
                    'nbProductsMax': nbProductsMax
                }
            },
            success: function (response) {
                if (response['error'] !== undefined) {
                    document.querySelector('#flux-notification-error').classList.remove('hidden');
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = response['error'];
                    document.querySelector('[id="flux-notification-error"]').appendChild($childNode);
                } else {
                    document.querySelector('.report-product-flow').classList.remove('hidden');
                    $this.logs = response.logs;
                    $this.nbProductsMax = response.nbProductsMax;
                    $this.nbProductsFiltered += response.nbProductsFiltered;
                    $this.nbProductsInError += response.nbProductsInError;
                    if (response.logs) {
                        $this.currentProductsNb += response.logs.length;
                    }

                    document.querySelector('[aria-controls="flux-prod-success"] span').textContent = ($this.currentProductsNb - $this.nbProductsFiltered - $this.nbProductsInError).toString();
                    document.querySelector('[aria-controls="flux-prod-filtered"] span').textContent = $this.nbProductsFiltered.toString();
                    document.querySelector('[aria-controls="flux-prod-error"] span').textContent = $this.nbProductsInError.toString();

                    max = $this.nbProductsMax.toString()
                    value = $this.currentProductsNb.toString();
                    let percent;
                    if ((percent = ((value * 100) / max).toFixed(2)) > 100) {
                        percent = 100;
                    }
                    percentProgressMessage.querySelector('span').innerHTML = percent.toString();
                    progressMessage.querySelector('span').innerHTML = value + '/' + max;
                    progressBar.setAttribute('max', max);
                    progressBar.setAttribute('value', value);
                    progressBar.classList.remove('hidden');
                    progressMessage.classList.remove('hidden');

                    if ($this.logs.length > 0) {
                        $this.logs.forEach(function (log) {
                            let flux = document.querySelector('[id="flux-prod-' + log.error_type.toLowerCase() + '"] .well');
                            let $childNode = document.createElement('p');
                            $childNode.innerHTML = log.ref_product + ' | ID: ' + log.id_product + ' | Attribut: ' + log.id_product_attribute + ' | ' + log.error_message;
                            flux.appendChild($childNode);
                        })
                    }

                    if (percent < 100) {
                        $this.handleProductBurst($this.currentProductsNb, $this.nbProductsMax);
                    } else {
                        SDEVADEO.handleButtons(false);
                        document.querySelector('#product-flow button.download-flow-button').classList.remove('hidden');
                        document.querySelector('#product-logs-info').classList.remove('hidden');
                        document.querySelector('#no-product-logs-info').classList.add('hidden');
                    }
                }
            },
            error: function () {
                document.querySelector('#flux-notification-error').classList.remove('hidden');
                document.querySelector('#flux-notification-success').classList.add('hidden');

                let $childNode = document.createElement('p');
                $childNode.innerHTML = issueMessage;
                document.querySelector('[id="flux-notification-error"]').appendChild($childNode);
            },
            complete: function (response) {
                SDEVADEO.handleButtons(false);
            }
        })
    },

    sendProductFlow: function() {
        SDEVADEO.handleButtons(true);
        document.querySelector('[id="flux-prod-success"] .well').innerHTML = '';
        document.querySelector('[id="flux-prod-filtered"] .well').innerHTML = '';
        document.querySelector('[id="flux-prod-error"] .well').innerHTML = '';
        document.querySelector('#flux-notification-error').innerHTML = '';
        document.querySelector('#flux-notification-error').classList.add('hidden');
        document.querySelector('#flux-notification-success').innerHTML = '';
        document.querySelector('#flux-notification-success').classList.add('hidden');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'sendProductFlow'
            },
            success: function (response) {
                let notification;
                if (response['hasError'] === false) {
                    notification = document.querySelector('#flux-notification-success');
                    notification.classList.remove('hidden');
                } else {
                    notification = document.querySelector('#flux-notification-error');
                    notification.classList.remove('hidden');
                }
                response['errorMessage'].forEach(function(message) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = message;
                    notification.appendChild($childNode);
                })
            },
            error: function (response) {

            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            }
        })
    },

    generateOfferFlow: function ()
    {
        const $this = this;
        document.querySelector('[id="flux-offer-success"] .well').innerHTML = '';
        document.querySelector('[id="flux-offer-filtered"] .well').innerHTML = '';
        document.querySelector('[id="flux-offer-error"] .well').innerHTML = '';
        $this.logs = {};
        $this.nbProductsMax = 0;
        $this.nbProductsFiltered = 0;
        $this.nbProductsInError = 0;
        $this.currentProductsNb = 0;
        SDEVADEO.handleButtons(true);
        $this.handleOfferBurst(0, 0);
    },

    handleOfferBurst: function(currentOffersNb, nbOffersMax) {
        const $this = this;
        $this.currentOffersNb = currentOffersNb;
        let flowType = document.querySelector('select[name="update-type"]').value;
        document.querySelector('#flux-notification-error').innerHTML = '';
        document.querySelector('#flux-notification-error').classList.add('hidden');
        document.querySelector('#flux-notification-success').innerHTML = '';
        document.querySelector('#flux-notification-success').classList.add('hidden');
        let max;
        let value;
        let progressBar = document.querySelector('#offer-flow progress');
        let progressMessage = document.querySelector('#offer-flow #nbOffersProcessed');
        let percentProgressMessage = progressMessage.querySelector('#percent_value');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'generateOfferFlow',
                params: {
                    'currentOffersNb': currentOffersNb,
                    'nbOffersMax': nbOffersMax,
                    'flowType': flowType
                }
            },
            success: function (response) {
                if (response['error'] !== undefined) {
                    document.querySelector('#flux-notification-error').classList.remove('hidden');
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = response['error'];
                    document.querySelector('[id="flux-notification-error"]').appendChild($childNode);
                } else {
                    document.querySelector('.report-offer-flow').classList.remove('hidden');
                    $this.logs = response.logs;
                    $this.nbProductsMax = response.nbOffersMax;
                    $this.nbProductsFiltered += response.nbOffersFiltered;
                    $this.nbProductsInError += response.nbOffersInError;
                    $this.currentProductsNb += response.logs.length;

                    document.querySelector('[aria-controls="flux-offer-success"] span').textContent = ($this.currentProductsNb - $this.nbProductsFiltered - $this.nbProductsInError).toString();
                    document.querySelector('[aria-controls="flux-offer-filtered"] span').textContent = $this.nbProductsFiltered.toString();
                    document.querySelector('[aria-controls="flux-offer-error"] span').textContent = $this.nbProductsInError.toString();

                    max = $this.nbProductsMax.toString()
                    value = $this.currentProductsNb.toString();
                    let percent;
                    if (max === 0) {
                        percent = 100;
                    } else if ((percent = ((value * 100) / max).toFixed(2)) > 100) {
                        percent = 100;
                    }
                    percentProgressMessage.querySelector('span').innerHTML = percent.toString();
                    progressMessage.querySelector('span').innerHTML = value + '/' + max;
                    progressBar.setAttribute('max', max);
                    progressBar.setAttribute('value', value);
                    progressBar.classList.remove('hidden');
                    progressMessage.classList.remove('hidden');

                    if ($this.logs.length > 0) {
                        $this.logs.forEach(function (log) {
                            let flux = document.querySelector('[id="flux-offer-' + log.error_type.toLowerCase() + '"] .well');
                            let $childNode = document.createElement('p');
                            $childNode.innerHTML = log.ref_product + ' | ID: ' + log.id_product + ' | Attribut: ' + log.id_product_attribute + ' | ' + log.error_message;
                            flux.appendChild($childNode);
                        })
                    }

                    if (percent < 100) {
                        $this.handleOfferBurst($this.currentProductsNb, $this.nbProductsMax);
                    } else {
                        SDEVADEO.handleButtons(false);
                        document.querySelector('#offer-flow button.download-flow-button').classList.remove('hidden');
                        document.querySelector('#offer-logs-info').classList.remove('hidden');
                        document.querySelector('#no-offer-logs-info').classList.add('hidden');
                    }
                }
            },
            error: function () {
                document.querySelector('#flux-notification-error').classList.remove('hidden');
                document.querySelector('#flux-notification-success').classList.add('hidden');

                let $childNode = document.createElement('p');
                $childNode.innerHTML = issueMessage;
                document.querySelector('[id="flux-notification-error"]').appendChild($childNode);
            },
            complete: function (response) {
                SDEVADEO.handleButtons(false);
            }
        });
    },

    sendOfferFlow: function() {
        SDEVADEO.handleButtons(true);
        document.querySelector('[id="flux-offer-success"] .well').innerHTML = '';
        document.querySelector('[id="flux-offer-filtered"] .well').innerHTML = '';
        document.querySelector('[id="flux-offer-error"] .well').innerHTML = '';
        document.querySelector('#flux-notification-error').innerHTML = '';
        document.querySelector('#flux-notification-error').classList.add('hidden');
        document.querySelector('#flux-notification-success').innerHTML = '';
        document.querySelector('#flux-notification-success').classList.add('hidden');
        let flowType = document.querySelector('select[name="update-type"]').value;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'sendOfferFlow',
                params: {
                    'flowType': flowType
                }
            },
            success: function (response) {
                let notification;
                if (response['hasError'] === false) {
                    notification = document.querySelector('#flux-notification-success');
                    notification.classList.remove('hidden');
                } else {
                    notification = document.querySelector('#flux-notification-error');
                    notification.classList.remove('hidden');
                }
                response['errorMessage'].forEach(function(message) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = message;
                    notification.appendChild($childNode);
                })
            },
            error: function (response) {

            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            }
        })
    },

    updateOfferFlowReports: function () {
        SDEVADEO.handleButtons(true);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'updateOfferFlowReports'
            },
            success: function (response) {
                let table = document.querySelector('#offer-flow .sdev-offer-reports table');
                table.classList.remove('hidden');
                table.querySelector('tbody').innerHTML = '';
                if (response.length > 0) {
                    response.forEach(function (log) {
                        if (log['date'] !== undefined && log['date'] !== null) {
                            document.querySelector('#date-update-product-report').innerHTML = log['date_update'];
                        }
                        if (log['status'] !== undefined && log['status'] !== null) {
                            table.classList.remove('hidden');
                            let tr = document.createElement('tr');
                            let tableElement = [
                                log['date'],
                                log['id_import'],
                                log['nb_products_read'],
                                log['nb_products_pending'],
                                log['nb_products_treated'],
                                log['nb_products_error'],
                                log['nb_products_inserted'],
                                log['nb_products_deleted'],
                                log['status'],
                                log['mode'],
                                log['report_log']
                            ];
                            tableElement.forEach(function (element) {
                                let td = document.createElement('td');
                                if (element === log['report_log'] && element.startsWith('http')) {
                                    let a = document.createElement('a');
                                    a.href = element;
                                    a.innerHTML = '<i class="fa fa-download"></i>' + '\n\n' + errorButton;
                                    a.classList.add('btn', 'btn-default');
                                    td.appendChild(a);
                                } else {
                                    td.innerHTML = element;
                                }
                                tr.appendChild(td);
                            })
                            table.querySelector('tbody').appendChild(tr);
                        }
                    })
                }
            },
            error: function (response) {

            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            }
        })
    },
    updateProductFlowReports: function () {
        SDEVADEO.handleButtons(true);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'updateProductFlowReports'
            },
            success: function (response) {
                let table = document.querySelector('#product-flow .sdev-product-reports table');
                table.classList.remove('hidden');
                table.querySelector('tbody').innerHTML = '';
                response.forEach(function (log) {
                    if (log['date'] !== undefined && log['date'] !== null) {
                        document.querySelector('#date-update-product-report').innerHTML = log['date_update'];
                    }
                    if (log['status'] !== undefined && log['status'] !== null) {
                        table.classList.remove('hidden');
                        let tr = document.createElement('tr');
                        let error = (log['status'] === 'PENDING') ? 'n/a' : log['nb_products_error'];
                        let rejected = (log['status'] === 'PENDING') ? 'n/a' : log['nb_products_rejected'];
                        let accepted = (log['status'] === 'PENDING') ? 'n/a' : log['nb_products_treated'] - log['nb_products_rejected'];
                        let tableElement = [
                            log['date'],
                            log['id_import'],
                            log['nb_products_read'],
                            log['nb_products_treated'],
                            error,
                            rejected,
                            accepted,
                            log['status'],
                            log['report_log']
                        ];
                        tableElement.forEach(function (element) {
                            let td = document.createElement('td');
                            if (Array.isArray(element)) {
                                element.forEach(function (row) {
                                    if (row.errorReportUrl !== undefined) {
                                        let a = document.createElement('a');
                                        a.href = row.errorReportUrl;
                                        a.innerHTML = '<i class="fa fa-download"></i>'+'\n\n'+errorButton;
                                        a.classList.add('btn', 'btn-default');
                                        td.appendChild(a);
                                    }
                                    if (row.successReportUrl !== undefined) {
                                        let a = document.createElement('a');
                                        a.href = row.successReportUrl;
                                        a.innerHTML = '<i class="fa fa-download"></i>'+'\n\n'+successButton;
                                        a.classList.add('btn', 'btn-default');
                                        td.appendChild(a);
                                    }
                                })
                            } else {
                                td.innerHTML = element;
                            }
                            tr.appendChild(td);
                        })
                        table.querySelector('tbody').appendChild(tr);
                    }
                })
            },
            error: function (response) {

            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            }
        })
    },
}