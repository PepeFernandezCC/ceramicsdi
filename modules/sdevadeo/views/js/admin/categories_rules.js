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

SDEVADEO.controller.admin.categoriesRules = {
    addCategoryRule: function() {
        document.querySelectorAll('[data-edit-rule]').forEach(function(node){
            node.classList.add('hidden');
        });
        let panel = document.querySelector('[id="category-rules-panel"]>fieldset.form-horizontal.panel');
        panel.querySelector('#disabled-categories-switch_1').checked = true;
        panel.classList.remove('hidden');
        panel.querySelector('.add-category-rule').classList.remove('hidden');
        panel.querySelector('fieldset').classList.add('hidden');
        // reset values
        panel.querySelector('[id="category-rule-logistic-class"] option[value="'+defaultLogisticClass+'"]').selected = true;
        panel.querySelector('[id="category-rule-free-carriers"]').value = '';
        panel.querySelectorAll('input').forEach(function (inputElement) {
            inputElement.value = '';
        })
        panel.querySelector('[id="disabled-categories-switch_1"]').value = '1';
        panel.querySelector('[id="disabled-categories-switch_0"]').value = '0';
    },
    closeCategoryRule: function(idRule) {
        if (idRule === undefined) {
            document.querySelector('[id="category-rules-panel"]>fieldset.form-horizontal.panel').classList.add('hidden');
        } else {
            if (document.querySelector('[data-edit-rule="'+idRule+'"]')) {
                document.querySelector('[data-edit-rule="'+idRule+'"]').classList.add('hidden');
            }
        }
    },
    deleteCategoryRule: function(idRule) {
        if (!confirm(deleteMessage)) {
            return;
        } else {
            SDEVADEO.handleButtons(true);
            this.closeCategoryRule(idRule);
        }
        document.querySelector('[id="categories-notification"]').classList.add('hidden');
        document.querySelector('[id="attributes-notification"]').innerHTML = '';
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'deleteCategoryRule',
                params: {
                    'idRule': idRule
                }
            },
            success: function (response) {
                document.querySelector('[id="attributes-notification"]').className = '';
                if (response['hasError'] === true) {
                    document.querySelector('[id="attributes-notification"]').classList.add('warn', 'alert', 'alert-danger');
                } else {
                    document.querySelector('[id="attributes-notification"]').classList.add('conf', 'alert', 'alert-success');
                    document.querySelector('[id="category-rules-panel"] [data-id-rule="'+idRule+'"]').remove();
                }
                response['errorMessage'].forEach(function (item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="attributes-notification"]').appendChild($childNode);
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
    saveCategoryRule: function (idRule) {
        SDEVADEO.handleButtons(true);
        document.querySelector('[id="categories-notification"]').innerHTML = '';
        document.querySelector('[id="categories-notification"]').className = '';
        let adjustmentApplied;
        let refNode;
        if (idRule === undefined) {
            refNode = document.querySelector('[id="category-rules-panel"]>fieldset');
            idRule = 0;
        } else {
            refNode = document.querySelector('[data-edit-rule="' + idRule + '"] fieldset');
        }
        // Check float value
        let error = false;
        let errorArray = [];
        let disclaimer;
        // Check price adjustment
        let priceAdjustment = refNode.querySelector('[id="category-rule-cost-adjustment"]').value;
        priceAdjustment = parseFloat(priceAdjustment.replace(',', '.'));
        if (priceAdjustment && isNaN(priceAdjustment)) {
            error = true;
            if (!refNode.querySelector('[for="category-rule-cost-adjustment"] span')) {
                disclaimer = document.createElement('strong');
                disclaimer.classList.add('text-danger');
                disclaimer.textContent = disclaimerMessage;
                refNode.querySelector('[for="category-rule-cost-adjustment"]').appendChild(disclaimer);
            }
        }
        // Check shipping cost
        let shippingCost = refNode.querySelector('[id="category-rule-shipping-cost"]').value;
        shippingCost = parseFloat(shippingCost.replace(',', '.'));
        if (shippingCost && isNaN(shippingCost)) {
            error = true;
            if (!refNode.querySelector('[for="category-rule-shipping-cost"] span')) {
                disclaimer = document.createElement('strong');
                disclaimer.classList.add('text-danger');
                disclaimer.textContent = disclaimerMessage;
                refNode.querySelector('[for="category-rule-shipping-cost"]').appendChild(disclaimer);
            }
        }
        // Check rule name
        if (!refNode.querySelector('[id="category-rule-name"]').value) {
            errorArray.push(nameFormatErrorMessage);
            error = true;
        }
        // check if shipping delay is integer
        var shippingDelay = parseFloat(refNode.querySelector('[name="category-rule-shipping-delay"]').value);
        if (
            refNode.querySelector('[name="category-rule-shipping-delay"]').value !== ''
            && refNode.querySelector('[name="category-rule-shipping-delay"]').value !== '0'
            && (isNaN(refNode.querySelector('[name="category-rule-shipping-delay"]').value) || (shippingDelay | 0) !== shippingDelay)
        ) {
            errorArray.push(shippingDelayErrorMessage);
            error = true;
        }
        let free_carriers = [];
        refNode.querySelectorAll('[id="category-rule-free-carriers"] > option:checked').forEach(function (item) {
            free_carriers.push(item.value);
        })
        if (refNode.querySelector('[id="category-rule-cost-applied"]').value === true) {
            adjustmentApplied = "0";
        } else {
            adjustmentApplied = "1";
        }
        if (error === true) {
            if (errorArray.length > 0) {
                let notificationNode;
                document.querySelector('[id="categories-notification"]').classList.add('warn', 'alert', 'alert-danger');
                document.querySelector('[id="categories-notification"]').classList.remove('hidden');
                errorArray.forEach(function (message) {
                    notificationNode = document.createElement('p');
                    notificationNode.textContent = message;
                    document.querySelector('[id="categories-notification"]').appendChild(notificationNode)
                });
            }
            if (SDEVADEO.handleButtons(false)) {
                return;
            }
        }
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'saveCategoryRule',
                params: {
                    'idRule': idRule,
                    'categoryRuleName': refNode.querySelector('[id="category-rule-name"]').value,
                    'shippingDelay': refNode.querySelector('[id="category-rule-shipping-delay"]').value,
                    'additionalShippingCost': refNode.querySelector('[id="category-rule-shipping-cost"]').value,
                    'freeCarriers': free_carriers,
                    'priceAdjustment': refNode.querySelector('[id="category-rule-cost-adjustment"]').value,
                    'adjustmentApplied': adjustmentApplied,
                    'logisticClass' : refNode.querySelector('[id="category-rule-logistic-class"] option:checked').value
                }
            },
            success: function (response) {
                document.querySelector('[id="categories-notification"]').innerHTML = '';
                if (response['hasError'] === false) {
                    document.querySelector('[id="categories-notification"]').classList.add('conf', 'alert', 'alert-success');
                    if (response['idRule'] !== undefined && idRule === 0) {
                        let trNode = document.createElement('tr');
                        trNode.setAttribute('data-id-rule', response['idRule']);
                        // Rule id
                        let tdNode = document.createElement('td');
                        tdNode.className = 'categoryRuleId';
                        tdNode.innerHTML = response['idRule'];
                        trNode.appendChild(tdNode);
                        // Rule name
                        tdNode = document.createElement('td');
                        tdNode.className = 'categoryRuleName';
                        tdNode.innerHTML = refNode.querySelector('[id="category-rule-name"]').value;
                        trNode.appendChild(tdNode);
                        // Rule shipping delay
                        tdNode = document.createElement('td');
                        let delay = refNode.querySelector('[id="category-rule-shipping-delay"]').value;
                        tdNode.className = 'categoryRuleShippingDelay';
                        tdNode.innerHTML = delay ? delay : '0';
                        trNode.appendChild(tdNode);
                        // Logistic Class rule
                        tdNode = document.createElement('td');
                        tdNode.className = 'categoryRuleLogisticClass';
                        tdNode.innerHTML = refNode.querySelector('[id="category-rule-logistic-class"] option:checked').innerHTML;
                        trNode.appendChild(tdNode);
                        // Price rule
                        tdNode = document.createElement('td');
                        tdNode.className = 'categoryRulePriceRule';
                        tdNode.innerHTML = noTranslation;
                        trNode.appendChild(tdNode);

                        tdNode = document.createElement('td');

                        let buttonElement = document.createElement('button');
                        buttonElement.classList.add('button', 'btn', 'btn-warning');
                        buttonElement.setAttribute('onClick', 'SDEVADEO.controller.admin.categoriesRules.editCategoryRule(\''+response['idRule']+'\')');
                        buttonElement.textContent = modifyTranslation;
                        tdNode.appendChild(buttonElement);
                        tdNode.append('\n');

                        buttonElement = document.createElement('button');
                        buttonElement.classList.add('button', 'btn', 'btn-danger');
                        buttonElement.setAttribute('onClick', 'SDEVADEO.controller.admin.categoriesRules.deleteCategoryRule('+response['idRule']+', \''+deleteRuleMessage+'\')');
                        buttonElement.textContent = deleteTranslation;
                        tdNode.appendChild(buttonElement);
                        trNode.appendChild(tdNode);

                        if (document.querySelector('[id="category-rules-panel"] tbody tr.no-categories')) {
                            document.querySelector('[id="category-rules-panel"] tbody').innerHTML = '';
                        }
                        document.querySelector('[id="category-rules-panel"] tbody').appendChild(trNode);
                    } else {
                        // Update
                        let ruleTr = document.querySelector('[id="category-rules-panel"] [data-id-rule="'+idRule+'"]');
                        ruleTr.querySelector('[class="categoryRuleName"]').innerHTML = refNode.querySelector('[id="category-rule-name"]').value;
                        ruleTr.querySelector('[class="categoryRuleLogisticClass"]').innerHTML = refNode.querySelector('[id="category-rule-logistic-class"] option:checked').innerHTML;
                        if (refNode.querySelector('[id="price-rule-panel"] tr')) {
                            ruleTr.querySelector('[class="categoryRulePriceRule"]').innerHTML = yesTranslation;
                        } else {
                            ruleTr.querySelector('[class="categoryRulePriceRule"]').innerHTML = noTranslation;
                        }
                    }
                } else {
                    document.querySelector('[id="categories-notification"]').classList.add('warn', 'alert', 'alert-danger');
                }
                response['errorMessage'].forEach(function(item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="categories-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                if (idRule !== 0) {
                    document.querySelector('[data-edit-rule="'+idRule+'"]').remove();
                } else {
                    document.querySelector('[id="categories"] fieldset.form-horizontal.panel').classList.add('hidden');
                }
                document.querySelectorAll('[id="categories"] button').forEach(function (button) {
                    button.removeAttribute('disabled');
                })
                SDEVADEO.handleButtons(false);
            }
        })
    },
    editCategoryRule: function (ruleId) {
        SDEVADEO.handleButtons(true);
        const $this = this;
        document.querySelector('[id="category-rules-panel"]>fieldset.form-horizontal.panel').classList.add('hidden');
        document.querySelectorAll('[data-edit-rule]').forEach(function(node){
            node.classList.add('hidden');
        });
        let editPanel = document.querySelector('[data-edit-rule="' + ruleId + '"]');
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
                    action: 'editCategoryRule',
                    params: {
                        'ruleId': ruleId
                    }
                },
                success: function (response) {
                    let trElement = document.createElement('tr');
                    trElement.setAttribute('data-edit-rule', ruleId);
                    let tdElement = document.createElement('td');
                    tdElement.setAttribute('colspan', "100%");
                    let editNode = document.querySelector('[id="category-rules-panel"]>fieldset.panel.form-horizontal').cloneNode(true);
                    tdElement.appendChild(editNode);
                    trElement.appendChild(tdElement);
                    let ruleToEdit = document.querySelector('[data-id-rule="' + ruleId + '"]');
                    ruleToEdit.parentNode.insertBefore(trElement, ruleToEdit.nextSibling);

                    // select edit window
                    editNode = document.querySelector('[data-edit-rule="' + ruleId + '"]');

                    editNode.querySelector('.cancel-edit-category').setAttribute('onclick', 'SDEVADEO.controller.admin.categoriesRules.closeCategoryRule(' + ruleId + ')');
                    editNode.querySelector('.edit-category-rule').setAttribute('onclick', 'SDEVADEO.controller.admin.categoriesRules.saveCategoryRule(' + ruleId + ')');
                    editNode.querySelector('.add-category-rule').setAttribute('onclick', 'SDEVADEO.controller.admin.categoriesRules.saveCategoryRule(' + ruleId + ')');
                    editNode.querySelector('[id="category-rule-name"]').value = response['name'];
                    editNode.querySelector('[id="category-rule-shipping-delay"]').value = response['shippingDelay'];
                    editNode.querySelector('[id="category-rule-shipping-cost"]').value = response['shippingCost'];
                    if (response['freeCarrierList'] && response['freeCarrierList'].length > 0) {
                        response['freeCarrierList'].forEach(function (carrierCode) {
                            editNode.querySelector('[name="category-rule-free-carriers"] option[value="' + carrierCode + '"]').selected = true;
                        });
                    }
                    // set logistic class value
                    if (response['logisticClass']) {
                        editNode.querySelector('[name="category-rule-logistic-class"] option[value="' + response['logisticClass'] + '"]').selected = true;
                    } else {
                        editNode.querySelector('[name="category-rule-logistic-class"] option[value="'+defaultLogisticClass+'"]').selected = true;
                    }
                    editNode.querySelector('[id="category-rule-cost-adjustment"]').value = response['additionalPrice'];
                    editNode.querySelector('[id="disabled-categories-switch_1"]').setAttribute('name', 'disabled-categories-switch-'+ruleId);
                    editNode.querySelector('[id="disabled-categories-switch_0"]').setAttribute('name', 'disabled-categories-switch-'+ruleId);
                    editNode.querySelector('[id="disabled-categories-switch_0"]').value = 0;
                    editNode.querySelector('[id="disabled-categories-switch_1"]').value = 1;

                    editNode.querySelector('[id="disabled-categories-switch_1"]').checked = true;
                    if (response['addIfForcedPrice'] !== "1") {
                        editNode.querySelector('[id="disabled-categories-switch_1"]').checked = false;
                        editNode.querySelector('[id="disabled-categories-switch_0"]').checked = true;
                    }
                    editNode.querySelector('fieldset.form-horizontal.panel>fieldset').classList.remove('hidden');
                    if (response['pricingRule'].length > 0) {
                        response['pricingRule'].forEach(function (pricingRule) {
                            $this.createPriceRuleTr(
                                pricingRule['id'],
                                pricingRule['minAmount'],
                                pricingRule['maxAmount'],
                                pricingRule['value'],
                                pricingRule['typePercent']
                            );
                        })
                    } else {
                        trElement = document.createElement('tr');
                        tdElement = document.createElement('td');
                        tdElement.innerHTML = noPricingRuleMessage;
                        tdElement.setAttribute('colspan', "100%");
                        tdElement.classList.add('text-center', 'no-price-rule');
                        trElement.appendChild(tdElement);
                        editNode.querySelector('[id="price-rule-panel"] thead').appendChild(trElement);
                    }
                    editNode.querySelector('.add-category-rule').classList.add('hidden');
                    editNode.querySelector('.edit-category-rule').classList.remove('hidden');
                    document.querySelector('[data-edit-rule="' + ruleId + '"] fieldset').classList.remove('hidden');
                },
                error: function (response) {
                    console.log(response);
                },
                complete: function () {
                    SDEVADEO.handleButtons(false);
                    document.querySelector('[id="categories"] tbody fieldset.panel.form-horizontal').classList.remove('hidden');
                    document.querySelectorAll('[id="categories"] button').forEach(function (button) {
                        button.removeAttribute('disabled');
                    })
                }
            });
        }
    },
    addPricingRule: function()
    {
        let pricePanel = document.querySelector('[data-edit-rule]:not(.hidden) [id="price-rule-panel"]');
        let tfoot = pricePanel.querySelector('tfoot');
        pricePanel.querySelectorAll('[data-edit-price-rule]:not(.hidden)').forEach(function (element) {
            element.classList.add('hidden');
        })
        pricePanel.querySelectorAll('[data-id-price-rule]').forEach(function (element) {
            element.classList.remove('hidden');
        })
        tfoot.classList.remove('hidden');
        tfoot.querySelector('.add-pricing-rule').classList.remove('hidden');
    },
    editPricingRule: function(priceRuleId)
    {
        // Close all panels
        let pricePanel = document.querySelector('[data-edit-rule]:not(.hidden) [id="price-rule-panel"]');
        pricePanel.querySelector('#pricing-rule-foot').classList.add('hidden');
        pricePanel.querySelectorAll('[data-edit-price-rule]:not(.hidden)').forEach(function (element) {
            element.classList.add('hidden');
        })
        pricePanel.querySelectorAll('[data-id-price-rule].hidden').forEach(function (element) {
            element.classList.remove('hidden');
        })
        if (pricePanel.querySelector('[data-edit-price-rule="'+priceRuleId+'"]')) {
            pricePanel.querySelector('[data-edit-price-rule="'+priceRuleId+'"]').classList.remove('hidden');
        } else {
            // Build the panel
            let editNode = pricePanel.querySelector('tfoot>tr').cloneNode(true);
            editNode.setAttribute('data-edit-price-rule', priceRuleId);
            editNode.querySelector('#min-price-rule').setAttribute('name', 'min-price-rule-' + priceRuleId);
            editNode.querySelector('#min-price-rule').value = pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"] [class="min-price-rule"]').innerHTML.substr(0, pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"] [class="min-price-rule"]').innerHTML.length-2);
            editNode.querySelector('#max-price-rule').setAttribute('name', 'max-price-rule-' + priceRuleId);
            editNode.querySelector('#max-price-rule').value = pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"] [class="max-price-rule"]').innerHTML.substr(0, pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"] [class="max-price-rule"]').innerHTML.length-2);
            editNode.querySelector('#value-price-rule').setAttribute('name', 'value-price-rule-' + priceRuleId);
            editNode.querySelector('#value-price-rule').value = pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"] [class="value-price-rule"]').innerHTML.substr(0, pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"] [class="value-price-rule"]').innerHTML.length-2);
            // Handle select
            editNode.querySelector('#type-price-rule').setAttribute('name', 'type-price-rule-' + priceRuleId);
            let value = pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"] [class="type-price-rule"]').getAttribute('data-type-value');
            editNode.querySelector('#type-price-rule option[value="'+value+'"]').selected = true;
            // un-hide the buttons and place the node
            editNode.querySelector('.edit-pricing-rule').classList.remove('hidden');
            editNode.querySelector('.add-pricing-rule').classList.add('hidden');
            editNode.classList.remove('hidden');
            pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"]').parentNode.insertBefore(editNode, pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"]').nextSibling);
        }
        pricePanel.querySelector('[data-id-price-rule="' + priceRuleId + '"]').classList.add('hidden');
    },
    closePricingRule: function()
    {
        let editNode;
        let pricePanel = document.querySelector('[data-edit-rule]:not(.hidden) [id="price-rule-panel"]');
        if (pricePanel.querySelector('#pricing-rule-foot:not(.hidden)')) {
            editNode = pricePanel.querySelector('#pricing-rule-foot');
        } else {
            editNode = pricePanel.querySelector('[data-edit-price-rule]:not(.hidden)');
            pricePanel.querySelector('[data-id-price-rule="'+editNode.getAttribute('data-edit-price-rule')+'"]').classList.remove('hidden');
        }
        editNode.classList.add('hidden');
    },
    deletePricingRule: function(pricingRuleId, message)
    {
        if (!confirm(message)) {
            SDEVADEO.handleButtons(false);
            return;
        } else {
            SDEVADEO.handleButtons(false);
        }
        document.querySelector('[id="price-rule-notification"]').innerHTML = '';
        document.querySelectorAll('[id="categories"] button').forEach(function (button) {
            button.setAttribute('disabled', '');
        })
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'deletePricingRule',
                params: {
                    'idRule': pricingRuleId
                }
            },
            success: function (response) {
                document.querySelector('[id="price-rule-notification"]').className = '';
                if (response['hasError'] === true) {
                    document.querySelector('[id="price-rule-notification"]').classList.add('warn', 'alert', 'alert-danger');
                } else {
                    document.querySelector('[id="price-rule-notification"]').classList.add('conf', 'alert', 'alert-success');
                    let pricePanel = document.querySelector('[data-edit-rule]:not(.hidden) [id="price-rule-panel"]');
                    pricePanel.querySelector('[data-id-price-rule="'+pricingRuleId+'"]').remove();
                }
                response['errorMessage'].forEach(function (item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="price-rule-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
                document.querySelectorAll('[id="categories"] button').forEach(function (button) {
                    button.removeAttribute('disabled');
                })
            }
        });
    },
    savePricingRule: function()
    {
        SDEVADEO.handleButtons(true);
        document.querySelector('[id="price-rule-notification"]').className = '';
        document.querySelector('[id="price-rule-notification"]').innerHTML = '';
        const $this = this;
        let idRule;
        let editNode;
        let pricePanel = document.querySelector('[data-edit-rule]:not(.hidden) [id="price-rule-panel"]');
        if (pricePanel.querySelector('#pricing-rule-foot:not(.hidden)')) {
            editNode = pricePanel.querySelector('#pricing-rule-foot');
            idRule = 0;
        } else {
            editNode = pricePanel.querySelector('[data-edit-price-rule]:not(.hidden)');
            let ruleTr = pricePanel.querySelector('[data-id-price-rule="'+editNode.getAttribute('data-edit-price-rule')+'"]');
            ruleTr.classList.remove('hidden');
            idRule = editNode.getAttribute('data-edit-price-rule');
        }
        let errorMessage = [];
        let minPrice = parseFloat((editNode.querySelector('#min-price-rule').value).replace(',', '.'));
        if (!minPrice || isNaN(minPrice)) {
            errorMessage.push(minPriceFormatErrorMessage);
        }
        let maxPrice = parseFloat((editNode.querySelector('#max-price-rule').value).replace(',', '.'));
        if (!maxPrice || isNaN(maxPrice)) {
            errorMessage.push(maxPriceFormatErrorMessage);
        }
        if (maxPrice < minPrice) {
            errorMessage.push(priceCompareErrorMessage);
        }
        let valuePrice = parseFloat((editNode.querySelector('#value-price-rule').value).replace(',', '.'));
        if (!valuePrice || isNaN(valuePrice)) {
            errorMessage.push(valueFormatErrorMessage);
        }
        let typePrice = editNode.querySelector('#type-price-rule').value;
        if (typePrice !== "0" && typePrice !== "1") {
            errorMessage.push(valueTypeErrorMessage);
        }

        if (errorMessage.length > 0) {
            let notificationNode;
            document.querySelector('[id="price-rule-notification"]').classList.add('warn', 'alert', 'alert-danger');
            document.querySelector('[id="price-rule-notification"]').classList.remove('hidden');
            errorMessage.forEach(function (message) {
                notificationNode = document.createElement('p');
                notificationNode.textContent = message;
                document.querySelector('[id="price-rule-notification"]').appendChild(notificationNode)
            });
            if (SDEVADEO.handleButtons(false)) {
                return;
            }
        }
        document.querySelectorAll('[id="categories"] button').forEach(function (button) {
            button.setAttribute('disabled', '');
        })

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'savePricingRule',
                params: {
                    'minPrice': minPrice,
                    'maxPrice': maxPrice,
                    'valuePrice': valuePrice,
                    'typePrice': typePrice,
                    'categoryRule': document.querySelector('[data-edit-rule]:not(.hidden)').getAttribute('data-edit-rule'),
                    'idRule': idRule
                }
            },
            success: function (response) {
                let ruleTr;
                if (response['hasError'] === true) {
                    document.querySelector('[id="price-rule-notification"]').classList.add('warn', 'alert', 'alert-danger');
                } else {
                    document.querySelector('[id="price-rule-notification"]').classList.add('conf', 'alert', 'alert-success');
                    if (document.querySelector('[data-edit-rule]:not(.hidden) [id="price-rule-panel"] [data-id-price-rule="' + response['idRule'] + '"]')) {
                        ruleTr = document.querySelector('[data-edit-rule]:not(.hidden) [id="price-rule-panel"] [data-id-price-rule="' + response['idRule'] + '"]');
                        ruleTr.querySelector('.min-price-rule').textContent = minPrice;
                        ruleTr.querySelector('.max-price-rule').textContent = maxPrice;
                        ruleTr.querySelector('.value-price-rule').textContent = valuePrice;
                        if (typePrice === "1") {
                            ruleTr.querySelector('.type-price-rule').textContent = percentTranslation;
                        } else {
                            ruleTr.querySelector('.type-price-rule').textContent = amountTranslation;
                        }
                    } else {
                        $this.createPriceRuleTr(
                            response['idRule'],
                            minPrice,
                            maxPrice,
                            valuePrice,
                            typePrice
                        );
                    }
                    if (document.querySelector('[data-edit-rule]:not(.hidden) [id="price-rule-panel"] .no-price-rule')) {
                        document.querySelector('[data-edit-rule]:not(.hidden) [id="price-rule-panel"] .no-price-rule').remove();
                    }
                    editNode.classList.add('hidden');
                }
                response['errorMessage'].forEach(function (item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="price-rule-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            }
        });
    },
    createPriceRuleTr: function(pricingRuleId, minAmount, maxAmount, value, type)
    {
        let trElement;
        let tdElement;
        let buttonElement;
        let editNode = document.querySelector('[data-edit-rule]:not(.hidden)');
        trElement = document.createElement('tr');
        trElement.setAttribute('data-id-price-rule', pricingRuleId);
        tdElement = document.createElement('td');
        tdElement.innerHTML = (Math.round(minAmount * 100) / 100).toFixed(6) + ' €';
        tdElement.classList.add('min-price-rule');
        trElement.appendChild(tdElement);
        tdElement = document.createElement('td');
        tdElement.innerHTML = (Math.round(maxAmount * 100) / 100).toFixed(6) + ' €';
        tdElement.classList.add('max-price-rule');
        trElement.appendChild(tdElement);
        tdElement = document.createElement('td');
        if (type !== "1") {
            tdElement.innerHTML = (Math.round(value * 100) / 100).toFixed(6) + ' €';
        } else {
            tdElement.innerHTML = (Math.round(value * 100) / 100).toFixed(6) + ' %';
        }
        tdElement.classList.add('value-price-rule');
        trElement.appendChild(tdElement);
        tdElement = document.createElement('td');
        tdElement.classList.add('type-price-rule');
        tdElement.setAttribute('data-type-value', type);
        if (type === "1") {
            tdElement.innerHTML = percentTranslation;
        } else {
            tdElement.innerHTML = amountTranslation;
        }
        trElement.appendChild(tdElement);
        tdElement = document.createElement('td');

        buttonElement = document.createElement('button');
        buttonElement.classList.add('button', 'btn', 'btn-warning');
        buttonElement.setAttribute('onClick', 'SDEVADEO.controller.admin.categoriesRules.editPricingRule(' + pricingRuleId + ')');
        buttonElement.textContent = modifyTranslation;
        tdElement.appendChild(buttonElement);
        tdElement.append('\n');

        buttonElement = document.createElement('button');
        buttonElement.classList.add('button', 'btn', 'btn-danger');
        buttonElement.setAttribute('onClick', 'SDEVADEO.controller.admin.categoriesRules.deletePricingRule(' + pricingRuleId + ', \'' + deleteRuleMessage + '\')');
        buttonElement.textContent = deleteTranslation;
        tdElement.appendChild(buttonElement);

        trElement.appendChild(tdElement);
        editNode.querySelector('[id="price-rule-panel"] thead').appendChild(trElement);
    },

    updateLogisticClasses: function() {
        SDEVADEO.handleButtons();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'updateLogisticClasses'
            },
            success: function (response) {
                if (response['hasError'] === true) {
                    document.querySelector('[id="categories-notification"]').classList.add('warn', 'alert', 'alert-danger');
                } else {
                    document.querySelector('[id="categories-notification"]').classList.add('conf', 'alert', 'alert-success');
                }
            },
            error: function () {
                document.querySelector('[id="categories-notification"]').classList.add('warn', 'alert', 'alert-danger');
            },
            complete: function (response) {
                document.querySelector('[id="categories-notification"]').innerHTML = '';
                response.responseJSON['errorMessage'].forEach(function (item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="categories-notification"]').appendChild($childNode);
                })
                if (document.querySelector('.logistic-class').classList.contains('hidden')) {
                    document.querySelector('.logistic-class').classList.remove('hidden');
                    document.querySelector('.no-logistic-class').classList.add('hidden');
                }
                SDEVADEO.handleButtons(false);
            }
        });
    }
}