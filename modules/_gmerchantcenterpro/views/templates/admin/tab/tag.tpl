{*
*
* Dynamic Ads + Pixel
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}
<div id="gmcp bt_advanced-tag" class="col-xs-12 bootstrap">
    <form class="form-horizontal" method="post" id="bt_form-advanced-tag" name="bt_form-advanced-tag">
        <h1 class="text-center mb-3">{l s='Tag attribution table' mod='gmerchantcenterpro'}</h1>
        <hr />
        <div class="alert alert-warning col-xs-12">
            <p>{l s='WARNING : before starting, please note that the categories displayed below are the DEFAULT categories of your products. So, make sure that your products are correctly assigned to the right default category.' mod='gmerchantcenterpro'}</p>
        </div>
        <span class="mt-3"></span>
        <hr />

        {if !empty($useGender) && $currentTagHandle == 'gender'}
            <div class="card bg-light shadow-lg rounded border border-dark mb-3 mt-3 p-2">

                <div class="span alert alert-info mb-2">
                    {l s='Select how you want to assign the tag values to your products. Choose the first option to assign the same tag value to all the products in a given category. Choose the second option if the products in a category do not necessarily have the same tag value. In this case, you must first define a product feature corresponding to the tag, set the right feature value for each product to be exported and then come back here to select the corresponding feature for each category. To learn more, read our' mod='gmerchantcenterpro'}&nbsp;<a class="badge badge-info" href="https://faq.businesstech.fr/faq/209" target="_blank"><i
                            class="icon icon-link"></i>&nbsp;{l s='FAQ about gender tags' mod='gmerchantcenterpro'}</a>
                </div>

                <div class="form-group px-5 py-3 ">
                    <label for="set_tag_mode">{l s='Select the tag attribution mode:' mod='gmerchantcenterpro'}</label>
                    <select class="form-control" class="set_tag_mode" name="set_tag_mode" id="set_tag_mode">
                        <option value="bulk">{l s='Assign the same tag value to all products in a category' mod='gmerchantcenterpro'}</option>
                        <option value="product_data" {if $useGenderProduct == 1} selected {/if}>{l s='Use the values of a feature for each category' mod='gmerchantcenterpro'}</option>
                    </select>
                </div>
            </div>
        {/if}

        {if !empty($useAgegroup) && $currentTagHandle == 'agegroup'}
            <div class="card bg-light shadow-lg rounded border border-dark mb-3 mt-3 p-2">

                <div class="span alert alert-info mb-2">
                    {l s='Select how you want to assign the tag values to your products. Choose the first option to assign the same tag value to all the products in a given category. Choose the second option if the products in a category do not necessarily have the same tag value. In this case, you must first define a product feature corresponding to the tag, set the right feature value for each product to be exported and then come back here to select the corresponding feature for each category. To learn more, read our' mod='gmerchantcenterpro'}&nbsp;<a class="badge badge-info" href="https://faq.businesstech.fr/faq/202" target="_blank"><i
                            class="icon icon-link"></i>&nbsp;{l s='FAQ about age group tags' mod='gmerchantcenterpro'}</a>
                </div>

                <div class="form-group px-5 py-3 ">
                    <label for="set_tag_mode">{l s='Select the tag attribution mode:' mod='gmerchantcenterpro'}</label>
                    <select class="form-control" class="set_tag_mode" name="set_tag_mode" id="set_tag_mode">
                        <option value="bulk">{l s='Assign the same tag value to all products in a category' mod='gmerchantcenterpro'}</option>
                        <option value="product_data" {if $useAgeGroupProduct == 1} selected {/if}>{l s='Use the values of a feature for each category' mod='gmerchantcenterpro'}</option>
                    </select>
                </div>
            </div>
        {/if}

        {if !empty($useAdult) && $currentTagHandle == 'adult'}
            <div class="card bg-light shadow-lg rounded border border-dark mb-3 mt-3 p-2">

                <div class="span alert alert-info mb-2">
                    {l s='Select how you want to assign the tag values to your products. Choose the first option to assign the same tag value to all the products in a given category. Choose the second option if the products in a category do not necessarily have the same tag value. In this case, you must first define a product feature corresponding to the tag, set the right feature value for each product to be exported and then come back here to select the corresponding feature for each category. To learn more, read our' mod='gmerchantcenterpro'}&nbsp;<a class="badge badge-info" href="https://faq.businesstech.fr/faq/222" target="_blank"><i
                            class="icon icon-link"></i>&nbsp;{l s='FAQ about adult tags' mod='gmerchantcenterpro'}</a>
                </div>

                <div class="form-group px-5 py-3 ">
                    <label for="set_tag_mode">{l s='Select the tag attribution mode:' mod='gmerchantcenterpro'}</label>
                    <select class="form-control" class="set_tag_mode" name="set_tag_mode" id="set_tag_mode">
                        <option value="bulk">{l s='Assign the same tag value to all products in a category' mod='gmerchantcenterpro'}</option>
                        <option value="product_data" {if $useAdultProduct == 1} selected {/if}>{l s='Use the values of a feature for each category' mod='gmerchantcenterpro'}</option>
                    </select>
                </div>
            </div>
        {/if}

        <input type="hidden" class="set_tag" name="set_tag" id="set_tag" value="{$tagType|escape:'htmlall':'UTF-8'}">

        <div class="card bg-light shadow-lg rounded border border-dark" style="display:none;">
            <div class="form-group px-5 py-3">
                <label for="set_tag">{l s='Select which type of tags you want to set:' mod='gmerchantcenterpro'}</label>
                <select class="form-control" class="set_tag" name="set_tag" id="set_tag">
                    {if !empty($useMaterial)}
                        <option value="material">{l s='Set "material" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($usePattern)}
                        <option value="pattern">{l s='Set "pattern" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($useGender)}
                        <option value="gender">{l s='Set "gender" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($useAgegroup)}
                        <option value="agegroup">{l s='Set "age group" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($useAdult)}
                        <option value="adult">{l s='Set "for adults only" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($bSizeType)}
                        <option value="sizeType">{l s='Set "size type" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($bSizeSystem)}
                        <option value="sizeSystem">{l s='Set "size system" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($bEnergy)}
                        <option value="energy">{l s='Set "energy efficiency class" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($bShippingLabel)}
                        <option value="shipping_label">{l s='Set "shipping label" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($bUnitpricingMeasure)}
                        <option value="unit_pricing_measure">{l s='Set "unit pricing measure" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($bUnitBasepricingMeasure)}
                        <option value="base_unit_pricing_measure">{l s='Set "unit pricing base measure" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($bExcludedDest)}
                        <option value="excluded_destination">{l s='Set "excluded destination" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                    {if !empty($bExcludedCountry)}
                        <option value="excluded_country">{l s='Set "shopping ads excluded country" tags' mod='gmerchantcenterpro'}</option>
                    {/if}
                </select>
            </div>
        </div>

        <div class="bulk-actions">
            <div class="card shadow-sm" id="bulk_action_material">
                <p class="card-text text-center mt-3">{l s='Set MATERIAL tags : for each category, indicate the feature that defines the material of the products that are in this category. To assign the same feature to all categories, select it below and click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3">
                    <select name="set_material_bulk_action" class="set_material_bulk_action">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </p>
                <p class="text-center mt-3">
                    <span class="btn btn-lg btn-success" onclick="oGmcPro.doSet('.material', $('.set_material_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-lg btn-warning" onclick="oGmcPro.doSet('.material', 0);">{l s='Reset' mod='gmerchantcenterpro'}
                </p>
            </div>

            <div class="card" id="bulk_action_pattern">
                <p class="card-text text-center mt-3">{l s='Set PATTERN tags : for each category, indicate the feature that defines the pattern of the products that are in this category. To assign the same feature to all categories, select it below and click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3">
                    <select name="set_pattern_bulk_action mb-3" class="set_pattern_bulk_action">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                        {/foreach}
                    </select>
                </p>
                <p class="text-center mt-3">
                    <span class="btn btn-lg btn-success" onclick="oGmcPro.doSet('.pattern', $('.set_pattern_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-lg btn-warning" onclick="oGmcPro.doSet('.pattern', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_adult">
                <p class="card-text text-center mt-3 text-center">{l s='Set AGE GROUP tags : for each category, select the age group for which the products in the category are intended. To assign the same value to all categories, click on the relevant age group below.' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3">
                    <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.agegroup', 'adult');">{l s='Adults (>13y.o)' mod='gmerchantcenterpro'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.agegroup', 'kids');">{l s='Kids (5-13y.o)' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.agegroup', 'toddler');">{l s='Toddlers (1-5y.o)' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.agegroup', 'infant');">{l s='Infants (3-12m.o)' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.agegroup', 'newborn');">{l s='Newborns (<3m.o)' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-warning btn-lg" onclick="oGmcPro.doSet('.agegroup', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_adult_product">
                <p class="card-text text-center mt-3 text-center">{l s='Set AGE GROUP tags : for each category, indicate the feature that defines the age group for which each product in the category is intended. To assign the same feature to all categories, select it below and click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3">
                    <select name="set_adult_bulk_action mb-3" class="set_adult_bulk_action">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                        {/foreach}
                    </select>
                </p>
                <p class="text-center mt-3">
                    <span class="btn btn-lg btn-success" onclick="oGmcPro.doSet('.agegroup_product', $('.set_adult_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-lg btn-warning" onclick="oGmcPro.doSet('.agegroup_product', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_gender">
                <p class="card-text text-center mt-3 text-center">{l s='Set GENDER tags : for each category, select the gender for which the products in the category are intended. To assign the same value to all categories, click on the relevant gender below.' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3">
                    <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.gender', 'male');">{l s='Men (male)' mod='gmerchantcenterpro'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.gender', 'female');">{l s='Women (female)' mod='gmerchantcenterpro'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.gender', 'unisex');">{l s='Unisex' mod='gmerchantcenterpro'} </span>
                    - <span class="btn btn-warning btn-lg" onclick="oGmcPro.doSet('.gender', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_gender_product">
                <p class="card-text text-center mt-3">{l s='Set GENDER tags : for each category, indicate the feature that defines the gender for which each product in the category is intended. To assign the same feature to all categories, select it below and click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3">
                    <select name="bulk_action_gender_product mb-3" class="bulk_action_gender_product">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                        {/foreach}
                    </select>
                </p>
                <p class="text-center mt-3">
                    <span class="btn btn-lg btn-success" onclick="oGmcPro.doSet('.gender_product', $('.bulk_action_gender_product').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-lg btn-warning" onclick="oGmcPro.doSet('.gender_product', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_tagadult">
                <div class="card-body">
                    <p class="card-text text-center">{l s='Set ADULT tags : for each category, if the products of the category are for adult only, select the "true" value. To assign the "true" value to all categories, click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                    <p class="text-center mt-3">
                        <span class="btn btn-lg btn-success" onclick="oGmcPro.doSet('.adult', 'true');">{l s='Set for all categories' mod='gmerchantcenterpro'}</span>
                        - <span class="btn btn-lg btn-warning" onclick="oGmcPro.doSet('.adult', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span>
                    </p>
                </div>
            </div>

            <div class="card" id="bulk_action_tagadult_product">
                <p class="card-text text-center">{l s='Set ADULT tags : for each category, indicate the feature that specifies whether the products in this category are for adults only or not. To assign the same feature to all categories, select it below and click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3">
                    <select name="bulk_action_tagadult_product mb-3" class="bulk_action_tagadult_product">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                        {/foreach}
                    </select>
                </p>
                <p class="text-center mt-3">
                    <span class="btn btn-lg btn-success" onclick="oGmcPro.doSet('.tagadult_product', $('.bulk_action_tagadult_product').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-lg btn-warning" onclick="oGmcPro.doSet('.tagadult_product', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span>
                </p>
            </div>


            <div class="card" id="bulk_action_sizeType">
                <p class="card-text text-center">{l s='Set SIZE TYPE tags : for each category, select the size type of the products that are in this category. To assign the same value to all categories, click on the relevant size type below.' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3">
                    <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeType', 'maternity');">{l s='Maternity' mod='gmerchantcenterpro'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeType', 'regular');">{l s='Regular' mod='gmerchantcenterpro'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeType', 'petite');">{l s='Petite' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeType', 'plus');">{l s='Plus' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeType', 'tall');">{l s='Tall' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeType', 'big');">{l s='Big' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeType', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span>
                </p>
            </div>
            <div class="card" id="bulk_action_sizeSystem">
                <p class="card-text text-center">{l s='Set SIZE SYSTEM tags : for each category, select the size system used for the products that are in this category. To assign the same value to all categories, click on the relevant size system below.' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3"><span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'US');">{l s='US' mod='gmerchantcenterpro'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'UK');">{l s='UK' mod='gmerchantcenterpro'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'EU');">{l s='EU' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'DE');">{l s='DE' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'FR');">{l s='FR' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'JP');">{l s='JP' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'CN');">{l s='CN' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'IT');">{l s='IT' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'BR');">{l s='BR' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'MEX');">{l s='MEX' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 'AU');">{l s='AU' mod='gmerchantcenterpro'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.sizeSystem', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_energy">
                <p class="card-text text-center">{l s='Set CERTIFICATION tags : for each category, select the features that define the product certification attributes (certification authority, name and code). To assign the same feature to all categories, select it below and click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p class="text-center mt-3">
                <div class="row">
                    <div class="col-xs-2">
                        <label>&nbsp;&nbsp;{l s='Certification Authority:' mod='gmerchantcenterpro'}</label>
                    </div>
                    <div class="col-xs-2">
                        <select name="set_energy_bulk_action" class="set_energy_bulk_action">
                            {foreach from=$aFeatures item=feature}
                                <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-xs-8">
                        <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.energy', $('.set_energy_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span> - <div class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.energy', 0);">{l s='Reset' mod='gmerchantcenterpro'}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-2">
                        <label>&nbsp;&nbsp;{l s='Certification name:' mod='gmerchantcenterpro'}</label>
                    </div>
                    <div class="col-xs-2">
                        <select name="set_energy_min_bulk_action" class="set_energy_min_bulk_action">
                            {foreach from=$aFeatures item=feature}
                                <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-xs-8">
                        <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.energy_min', $('.set_energy_min_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span> - <div class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.energy_min', 0);">{l s='Reset' mod='gmerchantcenterpro'}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-2">
                        <label>&nbsp;&nbsp;{l s='Certification code:' mod='gmerchantcenterpro'}</label>
                    </div>
                    <div class="col-xs-2">
                        <select name="set_energy_max_bulk_action" class="set_energy_max_bulk_action">
                            {foreach from=$aFeatures item=feature}
                                <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-xs-8">
                        <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.energy_max', $('.set_energy_max_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span> - <div class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.energy_max', 0);">{l s='Reset' mod='gmerchantcenterpro'}</div>
                    </div>
                </div>
                </p>

            </div>
            <div class="card" id="bulk_action_shipping_label">
                <p class="card-text text-center">{l s='Set SHIPPING LABEL tags : for each category, indicate the feature that defines the shipping label of the products that are in this category. To assign the same feature to all categories, select it below and click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p>
                    <select name="set_shipping_label_bulk_action" class="set_shipping_label_bulk_action">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </p>
                <p><span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.shipping_label', $('.set_shipping_label_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span> - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.shipping_label', 0);">{l s='Reset' mod='gmerchantcenterpro'}</p>
            </div>

            <div class="card" id="bulk_action_unit_pricing_measure">
                <p class="card-text text-center">{l s='Set UNIT PRICING MEASURE tags : for each category, indicate the feature that defines the unit pricing measure of the products that are in this category. To assign the same feature to all categories, select it below and click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p>
                    <select name="set_unit_pricing_measure_bulk_action" class="set_unit_pricing_measure_bulk_action">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </p>
                <p><span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.unit_pricing_measure', $('.set_unit_pricing_measure_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span> - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.unit_pricing_measure', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span></p>
            </div>

            <div class="card" id="bulk_action_base_unit_pricing_measure">
                <p class="card-text text-center">{l s='Set UNIT PRICING BASE MEASURE tags : for each category, indicate the feature that defines the unit pricing base measure of the products that are in this category. To assign the same feature to all categories, select it below and click on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p>
                    <select name="set_base_unit_pricing_measure_bulk_action" class="set_base_unit_pricing_measure_bulk_action">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </p>
                <p><span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.base_unit_pricing_measure', $('.set_base_unit_pricing_measure_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span> - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.base_unit_pricing_measure', 0);">{l s='Reset' mod='gmerchantcenterpro'}</span></p>
            </div>

            <div class="card" id="bulk_action_excluded_destination">
                <p class="card-text text-center">{l s='Set EXCLUDED DESTINATION tags: for each category, select the advertising channel on which you DO NOT want products of this category to be displayed. You can select several channels by holding down the CTRL (or CMD) key. To assign the same value to all categories, click on the relevant advertising channel below and then on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p>
                    <select multiple name="set_excluded_destination_bulk_action" class="set_excluded_destination_bulk_action">
                        <option value="">{l s='--' mod='gmerchantcenterpro'}</option>
                        <option value="shopping">{l s='Shopping Ads' mod='gmerchantcenterpro'}</option>
                        <option value="display">{l s='Display Ads' mod='gmerchantcenterpro'}</option>
                        <option value="local">{l s='Local inventory ads' mod='gmerchantcenterpro'}</option>
                        <option value="free-listing">{l s='Free listings' mod='gmerchantcenterpro'}</option>
                        <option value="free-local-listing">{l s='Free local listings' mod='gmerchantcenterpro'}</option>
                    </select>
                <p>
                <p><span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.excluded_destination', $('.set_excluded_destination_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span> - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.excluded_destination', '');">{l s='Reset' mod='gmerchantcenterpro'}</span></p>
            </div>

            <div class="card" id="bulk_action_excluded_country">
                <p class="card-text text-center">{l s='Set SHOPPING ADS EXCLUDED COUNTRY tags: for each category, select the country in which you DO NOT want products of this category to be displayed. You can select several countries by holding down the CTRL (or CMD) key. To assign the same value(s) to all categories, click on the relevant country(ies) below and then on "Set for all categories".' mod='gmerchantcenterpro'}</p>
                <p>
                    <select multiple name="set_excluded_country_bulk_action" class="set_excluded_country_bulk_action">
                        {foreach from=$aCountries item=country}
                            <option value="{$country|escape:'htmlall':'UTF-8'}">{$country|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                <p>
                <p><span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.excluded_country', $('.set_excluded_country_bulk_action').val());">{l s='Set for all categories' mod='gmerchantcenterpro'}</span> - <span class="btn btn-info btn-lg" onclick="oGmcPro.doSet('.excluded_destination', '');">{l s='Reset' mod='gmerchantcenterpro'}</span></p>
            </div>

        </div>

        {if !empty($success)}
            <div class="col-xs-12 alert alert-success text-center mt-3" id="sucess_message">
                {l s='The tags have been successfully assigned.' mod='gmerchantcenterpro'}
            </div>
        {/if}

        {if !empty($error)}
            <div class="col-xs-12 alert alert-danger text-center mt-3" id="error_message">
                {l s='An error occurred during tag attribution.' mod='gmerchantcenterpro'}
            </div>
        {/if}

        <input type="hidden" name="sUseTag" value="{$useTag|escape:'htmlall':'UTF-8'}" id="default_tag" />
        {if isset($token) && $token}
            <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
        {/if}

        <table class="table">
            {foreach from=$aShopCategories item=cat}
                <tr>
                    <td class="label_tag_categories_value text-center font-weight-bold text-uppercase">{$cat.path|escape:'quotes':'UTF-8'}</td>
                    <td>
                        <div class="value_material">
                            <div class="col-xs-12">
                                <select name="material[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="material">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        {if !empty($cat.material)}
                                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if $cat.material == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                        {else}
                                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_pattern">
                            <div class="col-xs-12">
                                <select name="pattern[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="pattern">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        {if !empty($cat.material)}
                                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if $cat.pattern == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                        {else}
                                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_agegroup">
                            <div class="col-xs-12">
                                <select class="agegroup" name="agegroup[{$cat.id_category|escape:'htmlall':'UTF-8'}]" id="agegroup{$cat.id_category|escape:'htmlall':'UTF-8'}">
                                    {if !empty($cat.agegroup)}
                                        <option value="0" {if $cat.agegroup=="0"} selected{/if}>--</option>
                                        <option value="adult" {if $cat.agegroup=="adult"} selected{/if}>{l s='Adults (>13y.o)' mod='gmerchantcenterpro'}</option>
                                        <option value="kids" {if $cat.agegroup=="kids"} selected{/if}>{l s='Kids (5-13y.o)' mod='gmerchantcenterpro'}</option>
                                        <option value="toddler" {if $cat.agegroup=="toddler"} selected{/if}>{l s='Toddlers (1-5y.o)' mod='gmerchantcenterpro'}</option>
                                        <option value="infant" {if $cat.agegroup=="infant"} selected{/if}>{l s='Infants (3-12m.o)' mod='gmerchantcenterpro'}</option>
                                        <option value="newborn" {if $cat.agegroup=="newborn"} selected{/if}>{l s='Newborns (<3m.o)' mod='gmerchantcenterpro'}</option>
                                    {else}
                                        <option value="0">--</option>
                                        <option value="adult">{l s='Adults (>13y.o)' mod='gmerchantcenterpro'}</option>
                                        <option value="kids">{l s='Kids (5-13y.o)' mod='gmerchantcenterpro'}</option>
                                        <option value="toddler">{l s='Toddlers (1-5y.o)' mod='gmerchantcenterpro'}</option>
                                        <option value="infant">{l s='Infants (3-12m.o)' mod='gmerchantcenterpro'}</option>
                                        <option value="newborn">{l s='Newborns (<3m.o)' mod='gmerchantcenterpro'}</option>
                                    {/if}
                                </select>
                            </div>

                            <select name="agegroup_product[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="agegroup_product">
                                <option value="0">-----</option>
                                {foreach from=$aFeatures item=feature}
                                    <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($cat.agegroup_product) && $cat.agegroup_product == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="value_gender">
                            <div class="col-xs-12">
                                <select class="gender" name="gender[{$cat.id_category|escape:'htmlall':'UTF-8'}]" id="gender{$cat.id_category|escape:'htmlall':'UTF-8'}">
                                    <option value="0" {if !empty($cat.gender) && $cat.gender=="0"} selected{/if}>--</option>
                                    <option value="male" {if !empty($cat.gender) && $cat.gender=="male"} selected{/if}>{l s='Men (male)' mod='gmerchantcenterpro'}</option>
                                    <option value="female" {if !empty($cat.gender) && $cat.gender=="female"} selected{/if}>{l s='Women (female)' mod='gmerchantcenterpro'}</option>
                                    <option value="unisex" {if !empty($cat.gender) && $cat.gender=="unisex"} selected{/if}>{l s='Unisex' mod='gmerchantcenterpro'}</option>
                                </select>

                                <select name="gender_product[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="gender_product">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($cat.gender_product) && $cat.gender_product == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_tagadult">
                            <div class="col-xs-12">
                                <select class="adult" name="adult[{$cat.id_category|escape:'htmlall':'UTF-8'}]" id="adult{$cat.id_category|escape:'htmlall':'UTF-8'}">
                                    <option value="0" {if !empty($cat.adult) &&  $cat.adult=="0"} selected{/if}>--</option>
                                    <option value="true" {if !empty($cat.adult) && $cat.adult=="true"} selected{/if}>true</option>
                                </select>

                                <select name="adult_product[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="tagadult_product">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($cat.adult_product) && $cat.adult_product == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_sizeType">
                            <div class="col-xs-4">
                                <p class="label_tag_categories_value">{l s='Size type:' mod='gmerchantcenterpro'}</p>
                            </div>
                            <div class="col-xs-4">
                                <select class="sizeType" name="sizeType[{$cat.id_category|escape:'htmlall':'UTF-8'}]" id="sizeType{$cat.id_category|escape:'htmlall':'UTF-8'}">
                                    <option value="0" {if !empty($cat.sizeType) && $cat.sizeType=="0"} selected{/if}>--</option>
                                    <option value="regular" {if !empty($cat.sizeType) && $cat.sizeType=="regular"} selected{/if}>{l s='Regular' mod='gmerchantcenterpro'}</option>
                                    <option value="petite" {if !empty($cat.sizeType) && $cat.sizeType=="petite"} selected{/if}>{l s='Petite' mod='gmerchantcenterpro'}</option>
                                    <option value="plus" {if !empty($cat.sizeType) && $cat.sizeType=="plus"} selected{/if}>{l s='Plus' mod='gmerchantcenterpro'}</option>
                                    <option value="tall" {if !empty($cat.sizeType) && $cat.sizeType=="tall"} selected{/if}>{l s='Tall' mod='gmerchantcenterpro'}</option>
                                    <option value="big" {if !empty($cat.sizeType) && $cat.sizeType=="big"} selected{/if}>{l s='Big' mod='gmerchantcenterpro'}</option>
                                    <option value="maternity" {if !empty($cat.sizeType) && $cat.sizeType=="maternity"} selected{/if}>{l s='Maternity' mod='gmerchantcenterpro'}</option>
                                </select>
                            </div>
                        </div>
                        <div class="value_sizeSystem">
                            <div class="col-xs-4">
                                <p class="label_tag_categories_value">{l s='Size system:' mod='gmerchantcenterpro'}</p>
                            </div>
                            <div class="col-xs-4">
                                <select class="sizeSystem" name="sizeSystem[{$cat.id_category|escape:'htmlall':'UTF-8'}]" id="sizeSystem{$cat.id_category|escape:'htmlall':'UTF-8'}">
                                    <option value="0" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="0"} selected{/if}>--</option>
                                    <option value="US" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="US"} selected{/if}>US</option>
                                    <option value="UK" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="UK"} selected{/if}>UK</option>
                                    <option value="EU" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="EU"} selected{/if}>EU</option>
                                    <option value="DE" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="DE"} selected{/if}>DE</option>
                                    <option value="FR" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="FR"} selected{/if}>FR</option>
                                    <option value="JP" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="JP"} selected{/if}>JP</option>
                                    <option value="CN" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="CN"} selected{/if}>CN</option>
                                    <option value="IT" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="IT"} selected{/if}>IT</option>
                                    <option value="BR" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="BR"} selected{/if}>BR</option>
                                    <option value="MEX" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="MEX"} selected{/if}>MEX</option>
                                    <option value="AU" {if !empty($cat.sizeSystem) && $cat.sizeSystem=="AU"} selected{/if}>AU</option>
                                </select>
                            </div>
                        </div>
                        <div class="value_energy">
                            <div class="row">
                                <div class="col-xs-2">
                                    <p class="label_tag_categories_value">{l s='Certification Authority:' mod='gmerchantcenterpro'}</p>
                                </div>
                                <div class="col-xs-5">
                                    <select name="energy[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="energy">
                                        <option value="0">-----</option>
                                        {foreach from=$aFeatures item=feature}
                                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($cat.energy) && $cat.energy == $feature.id_feature} selected {/if}>{$feature.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-2">
                                    <p class="label_tag_categories_value">{l s='Certification name:' mod='gmerchantcenterpro'}</p>
                                </div>
                                <div class="col-xs-5">
                                    <select name="energy_min[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="energy_min">
                                        <option value="0">-----</option>
                                        {foreach from=$aFeatures item=feature}
                                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($cat.energy_min) && $cat.energy_min == $feature.id_feature} selected {/if}>{$feature.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-2">
                                    <p class="label_tag_categories_value">{l s='Certification code:' mod='gmerchantcenterpro'}</p>
                                </div>
                                <div class="col-xs-5">
                                    <select name="energy_max[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="energy_max">
                                        <option value="0">-----</option>
                                        {foreach from=$aFeatures item=feature}
                                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($cat.energy_max) && $cat.energy_max == $feature.id_feature} selected {/if}>{$feature.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="value_shipping_label">
                            <div class="col-xs-4">
                                <p class="label_tag_categories_value">{l s='Shipping label:' mod='gmerchantcenterpro'}</p>
                            </div>
                            <div class="col-xs-4">
                                <select name="shipping_label[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="shipping_label">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($cat.shipping_label) && $cat.shipping_label == $feature.id_feature} selected {/if}>{$feature.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_unit_pricing_measure">
                            <div class="col-xs-4">
                                <p class="label_tag_categories_value">{l s='Unit pricing measure:' mod='gmerchantcenterpro'}</p>
                            </div>
                            <div class="col-xs-4">
                                <select name="unit_pricing_measure[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="unit_pricing_measure">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($cat.unit_pricing_measure) && $cat.unit_pricing_measure == $feature.id_feature} selected {/if}>{$feature.name|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_base_unit_pricing_measure">
                            <div class="col-xs-4">
                                <p class="label_tag_categories_value">{l s='Unit pricing base measure:' mod='gmerchantcenterpro'}</p>
                            </div>
                            <div class="col-xs-4">
                                <select name="base_unit_pricing_measure[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="base_unit_pricing_measure">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($cat.base_unit_pricing_measure) && $cat.base_unit_pricing_measure == $feature.id_feature} selected {/if}>{$feature.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_excluded_destination">
                            <select multiple name="excluded_destination[{$cat.id_category|escape:'htmlall':'UTF-8'}][]" class="excluded_destination">
                                <option value="">{l s='--' mod='gmerchantcenterpro'}</option>
                                <option {if !empty($cat.excluded_destination) && in_array('shopping', $cat.excluded_destination)} selected {/if} value="shopping">{l s='Shopping Ads' mod='gmerchantcenterpro'}</option>
                                <option {if !empty($cat.excluded_destination) && in_array('display', $cat.excluded_destination)} selected {/if} value="display">{l s='Display Ads' mod='gmerchantcenterpro'}</option>
                                <option {if !empty($cat.excluded_destination) && in_array('local', $cat.excluded_destination)} selected {/if} value="local">{l s='Local inventory ads' mod='gmerchantcenterpro'}</option>
                                <option {if !empty($cat.excluded_destination) && in_array('free-listing', $cat.excluded_destination)} selected {/if} value="free-listing">{l s='Free listings' mod='gmerchantcenterpro'}</option>
                                <option {if !empty($cat.excluded_destination) && in_array('free-local-listing', $cat.excluded_destination)} selected {/if} value="free-local-listing">{l s='Free local listings' mod='gmerchantcenterpro'}</option>
                            </select>
                        </div>
                        <div class="value_excluded_country">
                            <select multiple name="excluded_country[{$cat.id_category|escape:'htmlall':'UTF-8'}][]" class="excluded_country">
                                {foreach from=$aCountries item=country}
                                    <option value="{$country|escape:'htmlall':'UTF-8'}" {if !empty($cat.excluded_country) && in_array($country, $cat.excluded_country)} selected {/if}> {$country|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </td>
                </tr>
            {/foreach}
        </table>
        <div class="navbar navbar-default navbar-fixed-bottom shadow px-3 py-3 border border-dark">
            <p class="pull-right">
                <button class="btn btn-primary btn-lg text-center" type="submit" name="save_btn">{l s='Save' mod='gmerchantcenterpro'}</button>
                <a class="btn btn-default btn-lg btn-lg" href="{$moduleUrl|escape:'htmlall':'UTF-8'}">{l s='Go back to module configuration' mod='gmerchantcenterpro'}</a>
            </p>
        </div>
    </form>
</div>

<script type="text/javascript">
    // instantiate object
    var oGmcPro = oGmcPro || new GmcPro('{$sModuleName|escape:'htmlall':'UTF-8'}');
    var oGmcProFeatureByCat = oGmcProFeatureByCat || new GmcProFeatureByCat('{$sModuleName|escape:'htmlall':'UTF-8'}');
    var oGmcProFeedList = oGmcProFeedList || new GmcProFeedList('{$sModuleName|escape:'htmlall':'UTF-8'}');
    var oGmcProLabel = oGmcProLabel || new GmcProCustomLabel('{$sModuleName|escape:'htmlall':'UTF-8'}');
</script>