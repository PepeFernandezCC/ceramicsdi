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
{if isset($product_reports) && $product_reports}
    <div id="mpm_pe_saved_configurations" class="last_reports_list">
        <div class="header_saved_settings_list">
            <span class="header_saved_settings_name">{l s='Name' mod='exportproducts'}</span>
            <span class="header_saved_settings_file">{l s='Exported File' mod='exportproducts'}</span>
            <span class="header_saved_settings_last_executed">{l s='Last Executed' mod='exportproducts'}</span>
            <span class="header_saved_settings_status">{l s='Status' mod='exportproducts'}</span>
            <span class="header_saved_settings_buttons"></span>
            <div class="clear_both"></div>
        </div>
        <ul class="saved_settings_list">
            {foreach  $product_reports as $item_settings}
                <li data-id="{$item_settings['id_configuration']|escape:'htmlall':'UTF-8'}"
                    class="item_settings item_settings_{if isset($item_settings['id_configuration']) && $item_settings['id_configuration']}{$item_settings['id_configuration']|escape:'htmlall':'UTF-8'}{/if} {if isset($item_settings['id_configuration']) && $item_settings['id_configuration'] == $id_configuration}active{/if}">
                    <div class="saved_settings_name"><a
                                href="{$setting_url|escape:'htmlall':'UTF-8'}{$item_settings['id_configuration']|escape:'htmlall':'UTF-8'}">{$item_settings['name']|escape:'htmlall':'UTF-8'}</a>
                    </div>
                    <div class="saved_settings_file"><a download
                                                        class="link_to_file {if isset($item_settings['file_path']) && $item_settings['file_path']}active{/if}"
                                                        {if isset($item_settings['file_path']) && $item_settings['file_path']}href="{$item_settings['file_path']|escape:'htmlall':'UTF-8'}"{/if} ><i
                                    class="mic-download-solid"></i>{l s='download file' mod='exportproducts'}</a>
                    </div>
                    <div class="last_executed_settings {if !empty($item_settings['start'])} data_isset{else}no_data{/if}">

                        <div class="execution_details_block">
                            <div class="execution_details_header">{l s='Execution Details' mod='exportproducts'}</div>
                            <div class="execution_details_time">
                                <div class="execution_details_time_start">
                                    <label>{l s='Started at:' mod='exportproducts'}</label><span>{if !empty($item_settings['start'])}{$item_settings['start']|escape:'htmlall':'UTF-8'}{else}---{/if}</span>
                                </div>
                                <div class="execution_details_time_finish">
                                    <label>{l s='Ended:' mod='exportproducts'}</label><span>{if !empty($item_settings['finish'])}{$item_settings['finish']|escape:'htmlall':'UTF-8'}{else}---{/if}</span>
                                </div>
                            </div>
                            <div class="execution_details_time_elapsed_block">
                                <div class="execution_details_time_elapsed_label">{l s='Time Elapsed:' mod='exportproducts'}</div>
                                <div class="execution_details_time_elapsed">{if !empty($item_settings['time'])}{$item_settings['time']|escape:'htmlall':'UTF-8'}{else}---{/if}</div>
                            </div>
                            <div class="execution_details_total_block">
                                <div class="execution_details_total_label">{l s='Total Products Exported:' mod='exportproducts'}</div>
                                <div class="execution_details_total">{if !empty($item_settings['progress']) && !empty($item_settings['num_of_products'])}{$item_settings['progress']|escape:'htmlall':'UTF-8'}{l s=' from ' mod='exportproducts'}{$item_settings['num_of_products']|escape:'htmlall':'UTF-8'}{else}---{/if}</div>
                            </div>
                        </div>

                        {if !empty($item_settings['start'])}{$item_settings['start']|escape:'htmlall':'UTF-8'}{else}{l s='No Data' mod='exportproducts'}{/if}
                    </div>
                    <div class="status_settings">
                        <span>{if isset($item_settings['status']) && $item_settings['status']}{$item_settings['status']|escape:'htmlall':'UTF-8'}{else}{l s='NOT USED' mod='exportproducts'}{/if}</span>
                    </div>
                    <div class="settings_buttons">
                        <a class="toggle_settings_buttons"><i class="mic-more"></i></a>
                        <div class="toggle_settings_content">
                            <ul class="assignments_lists">
                                <li class="assignment_item assignment_item_export"><a
                                            data-id="{if isset($item_settings['id_configuration']) && $item_settings['id_configuration']}{$item_settings['id_configuration']|escape:'htmlall':'UTF-8'}{/if}"
                                            class="export_now"><i
                                                class="mic-play-solid"></i>{l s='Export Now' mod='exportproducts'}</a>
                                </li>
                                <li class="assignment_item assignment_item_stop"><a
                                            data-id="{if isset($item_settings['id_configuration']) && $item_settings['id_configuration']}{$item_settings['id_configuration']|escape:'htmlall':'UTF-8'}{/if}"
                                            class="stop_now"><i
                                                class="mic-stop-solid"></i>{l s='Stop Exporting' mod='exportproducts'}
                                    </a></li>
                                <li class="assignment_item assignment_item_edit"><a
                                            href="{$setting_url|escape:'htmlall':'UTF-8'}{$item_settings['id_configuration']|escape:'htmlall':'UTF-8'}"
                                            class="edit_option"><i
                                                class="mic-cogs-solid"></i>{l s='Edit Option' mod='exportproducts'}
                                    </a></li>
                                <li class="assignment_item assignment_item_download"><a
                                            data-id="{if isset($item_settings['id_configuration']) && $item_settings['id_configuration']}{$item_settings['id_configuration']|escape:'htmlall':'UTF-8'}{/if}"
                                            class="download_option"><i
                                                class="mic-download-solid"></i>{l s='Download Option' mod='exportproducts'}
                                    </a></li>
                                <li class="assignment_item assignment_item_delete"><a
                                            data-id-configuration="{if !empty($item_settings['id_configuration'])}{$item_settings['id_configuration']|escape:'htmlall':'UTF-8'}{/if}"
                                            class="delete_option"><i
                                                class="mic-minus-circle-solid"></i>{l s='Delete Option' mod='exportproducts'}
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="clear_both"></div>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}