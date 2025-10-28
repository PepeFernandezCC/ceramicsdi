{**
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
 *}

{extends file=$smarty.const._PS_MODULE_DIR_|cat:$module->name|cat:'/views/templates/extends/admin/base.tpl'}

{block 'SdevAdeoVarsAssignments'}
    {assign var='sdevadeoAdminPage' value='categories_mapping'}
{/block}

{block 'SdevAdeoAdminPageTitle'}
    <i class="icon-sitemap"></i>&nbsp;
    {l s='Categories mapping' mod='sdevadeo'}
{/block}

{block 'SdevAdeoAdminPageContent'}
    <div id="save-notification">
    </div>
    <div class="form-horizontal" data-form="config">
        <ul class="mapping-categories list-group">
            <li class="root-category list-group-item list-group-item-category">
                <div class="row" data-id-category="{$root->id|intval}">
                    <span class="col-lg-7">
                        <strong>[ID : {$root->id|intval}] {$root->name|escape:'htmlall':'UTF-8'}</strong>
                    </span>
                    <div class="col-lg-2">
                        <select>
                            <option value="0">{l s='No profile' mod='sdevadeo'}</option>
                            {if is_array($category_rules)}
                                {foreach $category_rules as $category_rule}
                                    <option value="{$category_rule.id|escape:'htmlall':'UTF-8'}"{if array_key_exists($root->id, $category_mapping) && $category_mapping[$root->id]['category_rule'] == $category_rule.id} selected="selected"{/if}>
                                        {$category_rule.name|escape:'htmlall':'UTF-8'}
                                    </option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <input class="checkboxCatRuleCategory" type="checkbox"
                               {if array_key_exists($root->id, $category_mapping)
                               && $category_mapping[$root->id]['active'] == 1
                               } checked{/if}
                        />
                        <label>{l s='Activate this category' mod='sdevadeo'}</label><br/>
                        <button type="button" class="btn btn-primary btn-xs legacy-button" onclick="SDEVADEO.controller.admin.categoriesMapping.applyAllSubcategory({$root->id})">
                            {l s='Apply to all subcategories' mod='sdevadeo'}
                        </button>
                    </div>
                </div>
            </li>
            {foreach $rootDirectChild as $category}
                <li class="root-category list-group-item list-group-item-category">
                    <div class="row" data-id-category="{$category['id_category']|intval}" data-parent-id="{$root->id|intval}">
                        <span class="col-lg-7">
                            <span></span>
                            {if $category['has_children']}
                                <button class="btn btn-default btn-xs expand-button" onclick="SDEVADEO.controller.admin.categoriesMapping.expandCategory({$category['id_category']})">
                                    <i class="fa fa-plus-square"></i>
                                </button>
                                <button class="btn btn-default btn-xs hidden retract-button" onclick="SDEVADEO.controller.admin.categoriesMapping.retractCategory({$category['id_category']})">
                                    <i class="fa fa-minus-square"></i>
                                </button>
                            {/if}
                            <strong>[ID : {$category['id_category']|intval}] {$category['name']|escape:'htmlall':'UTF-8'}</strong>
                        </span>
                        <div class="col-lg-2">
                            <select>
                                <option value="0">{l s='No profile' mod='sdevadeo'}</option>
                                {if is_array($category_rules)}
                                    {foreach $category_rules as $category_rule}
                                        <option value="{$category_rule.id|escape:'htmlall':'UTF-8'}"{if array_key_exists($category['id_category'], $category_mapping) && $category_mapping[$category['id_category']]['category_rule'] == $category_rule.id} selected="selected"{/if}>
                                            {$category_rule.name|escape:'htmlall':'UTF-8'}
                                        </option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <input class="checkboxCatRuleCategory" type="checkbox"
                                    {if array_key_exists($category['id_category'], $category_mapping)
                                    && $category_mapping[$category['id_category']]['active'] == 1
                                    } checked{/if}
                            />
                            <label>{l s='Activate this category' mod='sdevadeo'}</label><br/>
                            <button type="button" class="btn btn-primary btn-xs legacy-button"  onclick="SDEVADEO.controller.admin.categoriesMapping.applyAllSubcategory({$category['id_category']})">
                                {l s='Apply to all subcategories' mod='sdevadeo'}
                            </button>
                        </div>
                    </div>
                </li>
            {/foreach}
        </ul>
        <fieldset class="form-group">
            <div class="pull-right">
                <button
                        type="submit"
                        class="btn btn-primary"
                        title="{l s='Save the category mapping.' mod='sdevadeo'}"
                        onclick="SDEVADEO.controller.admin.categoriesMapping.saveMapping()"
                >
                    <i class="icon-save"></i>&nbsp;
                    {l s='Save' mod='sdevadeo'}
                </button>
            </div>
        </fieldset>
    </div>
{/block}
