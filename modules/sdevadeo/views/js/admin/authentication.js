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

SDEVADEO.controller.admin.authentication = {
    /**
     * Update the module's configuration.
     */
    update: function () {
        SDEVADEO.handleButtons(true);
        const $this = this;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'save',
                params: {
                    API_ENV: document.querySelector('[id="test-mode-switch"] > input:checked').value,
                    API_KEY: document.querySelector('input[id="api_key"]').value
                }
            },
            success: function (response) {
                document.querySelector('[id="save-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    document.querySelector('[id="save-notification"]').classList.add('conf', 'alert', 'alert-success');
                    document.querySelector('#shop-information-update').classList.remove('hidden');
                } else {
                    document.querySelector('[id="save-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="save-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function (response) {
                document.querySelector('[id="connection-notification"]').className = '';
                document.querySelector('[id="connection-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    $this.updateShopInformation();
                }
                SDEVADEO.handleButtons(false);
            },
        });
    },

    /**
     * Check the API connection.
     */
    test: function () {
        SDEVADEO.handleButtons(true);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'testConnection',
            },
            success: function (response) {
                document.querySelector('[id="connection-notification"]').innerHTML = '';
                if (response['testError'] === false) {
                    document.querySelector('[id="connection-notification"]').classList.add('conf', 'alert', 'alert-success');

                } else {
                    document.querySelector('[id="connection-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="connection-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                document.querySelector('[id="save-notification"]').className = '';
                document.querySelector('[id="save-notification"] > p').innerHTML = '';
                SDEVADEO.handleButtons(false);
            },
        });
    },

    /**
     * Get information from API
     */
    updateShopInformation: function () {
        SDEVADEO.handleButtons(true);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'updateShopInformation',
            },
            success: function (response) {
                document.querySelector('[id="connection-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    document.querySelector('[id="connection-notification"]').classList.add('conf', 'alert', 'alert-success');
                } else {
                    document.querySelector('[id="connection-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="connection-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                document.querySelector('[id="save-notification"]').className = '';
                document.querySelector('[id="save-notification"] > p').innerHTML = '';
                SDEVADEO.handleButtons(false);
            },
        });
    }
};