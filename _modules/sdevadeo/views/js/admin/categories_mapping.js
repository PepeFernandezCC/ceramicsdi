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

SDEVADEO.controller.admin.categoriesMapping = {
    /**
     * Add children direct category
     */
    expandCategory: function(idCategory) {
        SDEVADEO.handleButtons(true);
        document.querySelector('[id="save-notification"]').className = '';
        document.querySelector('[id="save-notification"]').innerHTML = '';
        let categoryRow = document.querySelector('.mapping-categories [data-id-category="'+idCategory+'"]').parentNode;

        // Get the category child
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'getSubCategories',
                params: {
                    'idCategory': idCategory
                }
            },
            success: function (response) {
                console.log(response);
                if (response['hasError'] === true) {
                    document.querySelector('[id="save-notification"]').classList.add('warn', 'alert', 'alert-danger');
                    response['errorMessage'].forEach(function (item) {
                        let $childNode = document.createElement('p');
                        $childNode.innerHTML = item;
                        document.querySelector('[id="save-notification"]').appendChild($childNode);
                    })
                } else {
                    let subRow;
                    response.forEach(function(subCategory) {
                        if (document.querySelector('.row #category-id-'+ subCategory.id_category)) {
                            document.querySelector('.row #category-id-'+ subCategory.id_category).parentNode.classList.remove('hidden');
                        } else {
                            subRow = categoryRow.cloneNode(true);
                            categoryRow.parentNode.insertBefore(subRow, categoryRow.nextSibling);
                            subRow = categoryRow.nextSibling;
                            subRow.querySelector('.row').dataset.idCategory = subCategory.id_category;
                            subRow.querySelector('.row').dataset.parentId = idCategory;

                            // Set selected category rule, either legacy one or db one
                            if ('legacyCategoryRule' in categoryRow.querySelector('.row').dataset) {
                                subRow.querySelector("select").value = subRow.querySelector('.row').dataset.legacyCategoryRule = categoryRow.querySelector('.row').dataset.legacyCategoryRule;
                            } else if (subCategory.mapping !== undefined && subCategory.mapping.length > 0 && subCategory.mapping['0']['category_rule'] !== undefined) {
                                subRow.querySelector("select").value = subCategory.mapping['0']['category_rule'] ? subCategory.mapping['0']['category_rule'] : '0';
                            }
                            // Set if category rule is activated
                            if ('appliedActivated' in categoryRow.querySelector('.row').dataset) {
                                subRow.querySelector('.checkboxCatRuleCategory').checked = subRow.querySelector('.row').dataset.legacyActivatedCategory = (categoryRow.querySelector('.row').dataset.legacyActivatedCategory === "true");
                            } else if (subCategory.mapping !== undefined && subCategory.mapping.length > 0 && subCategory.mapping['0']['active'] !== undefined) {
                                subRow.querySelector('.checkboxCatRuleCategory').checked  = (subCategory.mapping['0']['active'] === '1');
                            }
                            subRow.querySelector('.row>span>span').textContent = subRow.querySelector('.row>span>span').textContent + '- - - - ';
                            subRow.querySelector('.row>span>strong').textContent = '[ID : ' + subCategory.id_category + '] ' + subCategory.name;
                            subRow.querySelector('.legacy-button').setAttribute('onclick', 'SDEVADEO.controller.admin.categoriesMapping.applyAllSubcategory(' + subCategory.id_category + ')')
                            if (subCategory.has_children === true) {
                                subRow.querySelector('.expand-button').setAttribute('onclick', 'SDEVADEO.controller.admin.categoriesMapping.expandCategory(' + subCategory.id_category + ')');
                                subRow.querySelector('.retract-button').setAttribute('onclick', 'SDEVADEO.controller.admin.categoriesMapping.retractCategory(' + subCategory.id_category + ')');
                            } else {
                                subRow.querySelectorAll('.row>span button').forEach(function(button) {
                                    button.remove();
                                });
                            }
                        }
                    })
                    categoryRow.querySelector('.expand-button').classList.add('hidden');
                    categoryRow.querySelector('.retract-button').classList.remove('hidden');
                }
            },
            error: function (response) {
                console.log(response);
                document.querySelector('[id="save-notification"]').classList.add('warn', 'alert', 'alert-danger');
                let $childNode = document.createElement('p');
                $childNode.innerHTML = response.status + ' - ' + response.statusText;
                document.querySelector('[id="save-notification"]').appendChild($childNode);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
            }
        });
    },
    /**
     * Retract the category, hiding child
     */
    retractCategory: function(idCategory) {
        const $this = this;
        document.querySelector('[id="save-notification"]').className = '';
        document.querySelector('[id="save-notification"]').innerHTML = '';
        document.querySelectorAll('.mapping-categories button').forEach(function (buttonNode) {
            buttonNode.setAttribute('disabled', '');
        });
        $this.recursiveSubCategory(idCategory, 'hide');
        document.querySelectorAll('.mapping-categories button').forEach(function (buttonNode) {
            buttonNode.removeAttribute('disabled');
        });
    },

    recursiveSubCategory: function(idCategory, action, level = 0) {
        const $this = this;
        level++;
        let rowNode = document.querySelector('.mapping-categories [data-id-category="'+idCategory+'"]');
        if (action === 'legacy' && level > 1) {
            let parentCategory = document.querySelector('.mapping-categories [data-id-category="'+rowNode.dataset.parentId+'"]');
            rowNode.querySelector('select').value = rowNode.dataset.legacyCategoryRule = parentCategory.dataset.legacyCategoryRule;
            rowNode.querySelector('.checkboxCatRuleCategory').checked = rowNode.dataset.legacyActivatedCategory = (parentCategory.dataset.legacyActivatedCategory === "true");
        }
        if (document.querySelector('.mapping-categories [data-parent-id="'+idCategory+'"]')) {
            document.querySelectorAll('.mapping-categories [data-parent-id="'+idCategory+'"]').forEach(function(subCategory) {
                $this.recursiveSubCategory(subCategory.getAttribute(['data-id-category']), action, level);
            })
            if (action === 'hide') {
                rowNode.querySelector('.retract-button').classList.add('hidden');
                rowNode.querySelector('.expand-button').classList.remove('hidden');
            }
        }
        if (action === 'hide' && level > 1) {
            rowNode.parentNode.classList.add('hidden');
        }
    },

    applyAllSubcategory: function(idCategory) {
        const $this = this;
        document.querySelector('[id="save-notification"]').className = '';
        document.querySelector('[id="save-notification"]').innerHTML = '';
        document.querySelectorAll('.mapping-categories button').forEach(function (buttonNode) {
            buttonNode.setAttribute('disabled', '');
        });
        let categoryRow = document.querySelector('.mapping-categories [data-id-category="'+idCategory+'"]');
        categoryRow.dataset.legacyCategoryRule = categoryRow.querySelector('select').value;
        categoryRow.dataset.legacyActivatedCategory = categoryRow.querySelector('.checkboxCatRuleCategory').checked;
        $this.recursiveSubCategory(idCategory, 'legacy');
        document.querySelectorAll('.mapping-categories button').forEach(function (buttonNode) {
            buttonNode.removeAttribute('disabled');
        });
    },

    saveMapping: function() {
        SDEVADEO.handleButtons(true);
        document.querySelectorAll('.mapping-categories button').forEach(function (buttonNode) {
            buttonNode.setAttribute('disabled', '');
        });
        document.querySelector('[id="save-notification"]').innerHTML = '';
        document.querySelector('[id="save-notification"]').className = '';
        const $this = this;
        let categoryList = $this.createBranch(document.querySelector('.mapping-categories [data-id-category]').dataset.idCategory);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'saveMapping',
                params: {
                    'categoryList': categoryList
                }
            },
            success: function (response) {
                if (response['hasError'] === true) {
                    document.querySelector('[id="save-notification"]').classList.add('warn', 'alert', 'alert-danger');
                } else {
                    document.querySelector('[id="save-notification"]').classList.add('conf', 'alert', 'alert-success');
                }
                response['errorMessage'].forEach(function (item) {
                    let $childNode = document.createElement('p');
                    $childNode.innerHTML = item;
                    document.querySelector('[id="save-notification"]').appendChild($childNode);
                })
            },
            error: function (response) {
                console.log(response);
                document.querySelector('[id="save-notification"]').classList.add('warn', 'alert', 'alert-danger');
                let $childNode = document.createElement('p');
                $childNode.innerHTML = response.status + ' - ' + response.statusText;
                document.querySelector('[id="save-notification"]').appendChild($childNode);
            },
            complete: function () {
                SDEVADEO.handleButtons(false);
                document.querySelectorAll('.mapping-categories button').forEach(function (buttonNode) {
                    buttonNode.removeAttribute('disabled');
                });
            }
        });
    },

    createBranch: function (idCategory) {
        const $this = this;
        let categoryNode;
        let childrenList = [];
        let legacyCategoryRule;
        let legacyActivatedCategory;
        categoryNode = document.querySelector('.mapping-categories [data-id-category="'+idCategory+'"]');
        if (categoryNode.dataset.legacyCategoryRule) {
            legacyCategoryRule = categoryNode.dataset.legacyCategoryRule;
        } else {
            legacyCategoryRule = 'false';
        }

        if (categoryNode.dataset.legacyActivatedCategory) {
            if (categoryNode.dataset.legacyActivatedCategory === 'true') {
                legacyActivatedCategory = 'activated';
            } else {
                legacyActivatedCategory = 'unactivated';
            }
        } else {
            legacyActivatedCategory = 'false';
        }
        let childNode = document.querySelectorAll('.mapping-categories [data-parent-id="'+idCategory+'"]');
        if (childNode.length > 0) {
            childNode.forEach(function(childNode) {
                childrenList.push($this.createBranch(childNode.dataset.idCategory));
            });
            return {
                'idCategory': categoryNode.dataset.idCategory,
                'categoryRule': categoryNode.querySelector('select').value,
                'categoryActivated': categoryNode.querySelector('.checkboxCatRuleCategory').checked,
                'legacyCategoryRule': legacyCategoryRule,
                'legacyActivatedCategory': legacyActivatedCategory,
                'childrenList': childrenList
            };
        } else {
            return {
                'idCategory': categoryNode.dataset.idCategory,
                'categoryRule': categoryNode.querySelector('select').value,
                'categoryActivated': categoryNode.querySelector('.checkboxCatRuleCategory').checked,
                'legacyCategoryRule': legacyCategoryRule,
                'legacyActivatedCategory': legacyActivatedCategory
            };
        }
    }
}