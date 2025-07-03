{*
* 2007-2020 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div data-label="{$label|escape:'htmlall':'UTF-8'}" data-type="{$type|escape:'htmlall':'UTF-8'}"
     data-field="{$name_field|escape:'htmlall':'UTF-8'}"
     class="filter_section section_{$name_field|escape:'htmlall':'UTF-8'}">
    <div class="filter_section_box">
        <div class="filter_section_remove"><i class="mic-minus-circle-solid"></i></div>
        <div class="filter_section_content">
            <div class="filter_section_header">
                {$label|escape:'htmlall':'UTF-8'}
            </div>
            <div class="filter_section_data">
                {if isset($type) && $type == 'checkbox'}
                    <div class="filter_option_line filter_option_line_checkbox">
                        <div class="filter_option_label">{l s='Filter option: ' mod='exportproducts'}</div>
                        <div class="filter_option">
                            <div class="filter_select">{l s='Choose from List' mod='exportproducts'}</div>
                        </div>
                        <div class="filter_checkbox_block">
                            <li class="filter_checkbox_item_search {if isset($name_field) && $name_field == 'customers'}active{/if}">
                                <input placeholder="{l s='Search' mod='exportproducts'}" class="checkbox_item_search"
                                       type="text">
                            </li>
                            <ul class="filter_checkbox_list">
                                {foreach  $list['values'] as $val}
                                    <li data-id="{$val[$list['key']]|escape:'htmlall':'UTF-8'}"
                                        data-label="{$val['name']|escape:'htmlall':'UTF-8'}"
                                        class="filter_checkbox_item filter_checkbox_item_{$val[$list['key']]|escape:'htmlall':'UTF-8'} {if isset($list['selected']) && $list['selected'] && in_array($val[$list['key']], $list['selected'])} active{/if}">
                                        {$val['name']|escape:'htmlall':'UTF-8'}
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                        <ul class="selected_checkbox_list">
                            {if isset($list['selected']) && $list['selected']}
                                {foreach  $list['values'] as $val}
                                    {if in_array($val[$list['key']], $list['selected'])}
                                        <li data-id="{$val[$list['key']]|escape:'htmlall':'UTF-8'}"
                                            class="selected_item_{$val[$list['key']]|escape:'htmlall':'UTF-8'}">{$val['name']|escape:'htmlall':'UTF-8'}
                                            <span><i class="mic-times-solid"></i></span></li>
                                    {/if}
                                {/foreach}
                            {/if}
                        </ul>
                    </div>
                {/if}
                {if isset($type) && $type == 'tree'}
                    <div class="filter_option_line">
                        <div class="filter_option_label">{l s='Filter option: ' mod='exportproducts'}</div>
                        <div class="filter_option">
                            <div class="filter_select">{l s='Choose from List' mod='exportproducts'}</div>
                        </div>
                        <ul class="selected_checkbox_list">
                            {if isset($list['selected']) && $list['selected']}
                                {foreach  $list['selected'] as $val}
                                    <li data-id="{$val['id_category']|escape:'htmlall':'UTF-8'}"
                                        class="selected_item_{$val['id_category']|escape:'htmlall':'UTF-8'}">{$val['name']|escape:'htmlall':'UTF-8'}
                                        <span><i class="mic-times-solid"></i></span></li>
                                {/foreach}
                            {/if}
                        </ul>
                    </div>
                {/if}
                {if isset($type) && $type == 'select'}
                    <div class="filter_option_line">
                        <div class="filter_option_label">{l s='Filter option: ' mod='exportproducts'}</div>
                        <div class="filter_option">
                            {include file="{$path_tpl|escape:'htmlall':'UTF-8'}filter_select.tpl" options=$options name_field=$name_field selected=$list['selected']}
                        </div>
                    </div>
                {/if}
                {if isset($type) && $type == 'number'}
                    <div class="filter_option_line">
                        <div class="filter_option_label">{l s='Filter option: ' mod='exportproducts'}</div>
                        <div class="filter_option">
                            {include file="{$path_tpl|escape:'htmlall':'UTF-8'}filter_select.tpl" options=$options name_field=$name_field selected=$list['selected']['type']}
                        </div>
                        <div class="filter_values">
                            <div class="filter_option_label">{l s='Custom value: ' mod='exportproducts'}</div>
                            <input placeholder="{l s='Chose value' mod='exportproducts'}"
                                   class="{$name_field|escape:'htmlall':'UTF-8'}_value" type="text"
                                   value="{if isset($list['selected']['value'])}{$list['selected']['value']|escape:'htmlall':'UTF-8'}{/if}">
                        </div>
                    </div>
                {/if}
                {if isset($type) && $type == 'string'}
                    <div class="filter_option_line">
                        <div class="filter_option_label">{l s='Filter option: ' mod='exportproducts'}</div>
                        <div class="filter_option">
                            {include file="{$path_tpl|escape:'htmlall':'UTF-8'}filter_select.tpl" options=$options name_field=$name_field selected=$list['selected']['type']}
                        </div>
                        <div class="filter_values">
                            <div class="filter_option_label">{l s='Custom value: ' mod='exportproducts'}</div>
                            <input placeholder="{l s='Chose value' mod='exportproducts'}"
                                   class="{$name_field|escape:'htmlall':'UTF-8'}_value" type="text"
                                   value="{if isset($list['selected']['value'])}{$list['selected']['value']|escape:'htmlall':'UTF-8'}{/if}">
                        </div>
                    </div>
                {/if}
                {if isset($type) && $type == 'date'}
                    <div class="filter_option_line filter_option_line_date">
                        <div class="filter_option_label">{l s='Filter option: ' mod='exportproducts'}</div>
                        <div class="filter_option">
                            {include file="{$path_tpl|escape:'htmlall':'UTF-8'}filter_select.tpl" options=$options name_field=$name_field selected=$list['selected']['type']}
                        </div>
                        <div class="filter_values filter_value_date_1 filter_date {if isset($list['selected']['type']) && ($list['selected']['type'] == 'before_date' || $list['selected']['type'] == 'after_date' || $list['selected']['type'] == 'period')}active{/if}">
                            <div class="filter_option_label">{l s='Custom value: ' mod='exportproducts'}</div>
                            <input placeholder="{l s='Chose Date' mod='exportproducts'}"
                                   class="{$name_field|escape:'htmlall':'UTF-8'}_value_1 " type="text"
                                   value="{if isset($list['selected']['val_1']) && $list['selected']['val_1']}{$list['selected']['val_1']|escape:'htmlall':'UTF-8'}{/if}">
                        </div>
                        <div class="filter_values filter_value_date_2 filter_date {if isset($list['selected']['type']) && $list['selected']['type'] == 'period'}active{/if}">
                            <div class="filter_option_label">{l s='Custom value: ' mod='exportproducts'}</div>
                            <input placeholder="{l s='Chose Date' mod='exportproducts'}"
                                   class="{$name_field|escape:'htmlall':'UTF-8'}_value_2 " type="text"
                                   value="{if isset($list['selected']['val_2']) && $list['selected']['val_2']}{$list['selected']['val_2']|escape:'htmlall':'UTF-8'}{/if}">
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>