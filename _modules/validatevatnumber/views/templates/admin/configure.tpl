{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


{if $confirmation}
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
        <p>{$confirmation|escape:'htmlall':'UTF-8'}</p>
    </div>
{/if}
{if $errors}
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>

        {if count($errors) == 1}
            <p>{$errors[0]}</p>
        {else}
            <ol>
                {foreach $errors as $error}
                    <li>{$error|escape:'htmlall':'UTF-8'}</li>
                {/foreach}
            </ol>
        {/if}
    </div>
{/if}

<div id="validateVATTabs" class="VATTabs">
    <ul class="validateVATUl">
        <li data-tab-index="0" class="active"><a><i class="icon-cogs"></i> {l s='Settings' mod='validatevatnumber'}</a></li>
        <li data-tab-index="1" ><a><i class="icon-globe"></i> {l s='Countries' mod='validatevatnumber'}</a></li>
        <li data-tab-index="2"> <a><i class="icon-envelope"></i> {l s='E-mail Notification' mod='validatevatnumber'}</a></li>
        <li><a href={$vatNumberListController|escape:'htmlall':'UTF-8'}><i class="icon-list"></i> {l s='VAT Numbers' mod='validatevatnumber'}</a></li>
    </ul>
    <div class="validateVATTabsContent">
        <div data-tab-index="0" class="validateVATTab active">
            {$settingsForm} {*This is html content*}
        </div>
        <div data-tab-index="1" class="validateVATTab" style="display: none;">
            <form action="{$form_action|escape:'htmlall':'UTF-8'}" method="post" id="country_list_options">
                <div class="panel">
                    <div class="tab-pane" id="create_order_tracking_code">
                        <table class="table" id="order_tracking_list">
                            <thead>
                            <tr>
                                <th>
                                    <span class="title_box ">{l s='Country ID' mod='validatevatnumber'}</span>
                                </th>
                                <th>
                                    <span class="title_box ">{l s='Country Name' mod='validatevatnumber'}</span>
                                </th>
                                <th>
                                    <span class="title_box ">{l s='Default Customer Group' mod='validatevatnumber'}</span>
                                </th>
                                <th>
                                    <span class="title_box ">{l s='Save' mod='validatevatnumber'}</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$country_list key=k item=country}
                                <tr>
                                    <td>{$country.id_country|escape:'htmlall':'UTF-8'}</td>
                                    <td>{$country.name|escape:'htmlall':'UTF-8'}</td>
                                    <td>
                                        <select name="order_admin_select_carrier" id="order_admin_select_carrier_{$k|escape:"htmlall":"UTF-8"}">
                                            <option value='0'>{l s='- None -' mod='validatevatnumber'}</option>
                                            {foreach from=$customer_groups item=customer_group}
                                                <option {if $country.id_customer_group == $customer_group.id_group}selected="selected"{/if} value="{$customer_group.id_group|escape:'htmlall':'UTF-8'}">{$customer_group.name|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                    <td style="text-align: center;"><a href="javascript:void(0);" onclick='save_country_group_function({$k|escape:"htmlall":"UTF-8"},{$country.id_country|escape:"htmlall":"UTF-8"});' type="submit" class="btn btn-default" name="submitAddproductAndStay" value="update_legends"><i class="icon-random process-icon-save"></i> Update</a></td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
        <div data-tab-index="2" class="validateVATTab" style="display: none;">
            {$settingsForm2} {*THIS IS HTML CONTENT*}
        </div>
    </div>
</div>
