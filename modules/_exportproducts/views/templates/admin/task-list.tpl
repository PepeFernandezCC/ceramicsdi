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
<div class="scheduled_tasks_header">
    <div class="scheduled_tasks_label">{l s='Scheduled Tasks' mod='exportproducts'}</div>
    <div class="new_scheduled_tasks"><a class="add_new_task"><i
                    class="mic-business-time-solid"></i>{l s='Add Scheduled Task' mod='exportproducts'}</a></div>
</div>
<div class="scheduled_tasks_content">
    {if isset($scheduled_tasks) && $scheduled_tasks}
        <div class="cron_tasks_descr">
            <div class="cron_tasks_link_block">
                <div class="cron_tasks_descr_header">{l s='Important Note! To execute your cron tasks, please insert the following line in your cron tasks manager: ' mod='exportproducts'}</div>
                <span class="cron_tasks_link">*/1 * * * * curl "{$schedule_url|escape:'htmlall':'UTF-8'}"</span>
            </div>
        </div>
        <div class="cron_tasks_descr cron_tasks_current_server_time">
            <div class="cron_tasks_link_block">
                <div class="cron_tasks_descr_header">{l s='Server time! All dates and times in the scheduler are measured according to the server\’s time, as the scheduler is run purely on the server-side.' mod='exportproducts'}</div>
                <span class="cron_tasks_link">{l s='Current server time:' mod='exportproducts'} {date(Context::getContext()->language->date_format_full)|escape:'htmlall':'UTF-8'}
                    (UTC) {date('P')|escape:'htmlall':'UTF-8'} UTC</span>
            </div>
        </div>
        <div class="last_reports_list">
            <div class="header_scheduled_tasks_list">
                <span class="header_scheduled_tasks_name">{l s='Name' mod='exportproducts'}</span>
                <span class="header_scheduled_tasks_options">{l s='Export Option' mod='exportproducts'}</span>
                <span class="header_scheduled_tasks_next_run">{l s='Next run' mod='exportproducts'}</span>
                <span class="header_scheduled_tasks_file">{l s='Exported File' mod='exportproducts'}</span>
                <span class="header_scheduled_tasks_last_executed">{l s='Last Executed' mod='exportproducts'}</span>
                <span class="header_scheduled_tasks_status">{l s='Status' mod='exportproducts'}</span>
                <span class="header_scheduled_tasks_buttons"></span>
                <div class="clear_both"></div>
            </div>
            <ul class="scheduled_tasks_list">
                {foreach  $scheduled_tasks as $scheduled_task}
                    <li data-id-task="{$scheduled_task['id_task']|escape:'htmlall':'UTF-8'}"
                        class="item_scheduled_task item_scheduled_task_{if isset($scheduled_task['id_task']) && $scheduled_task['id_task']}{$scheduled_task['id_task']|escape:'htmlall':'UTF-8'}{/if}">
                        <div class="scheduled_tasks_name">{if isset($scheduled_task['description']) && $scheduled_task['description']}{$scheduled_task['description']|escape:'htmlall':'UTF-8'}{else}---{/if}</div>
                        <div class="scheduled_tasks_options">{$scheduled_task['name']|escape:'htmlall':'UTF-8'}</div>
                        <div class="scheduled_tasks_next_run">
                                <span>{$scheduled_task['next_run']|escape:'htmlall':'UTF-8'}</span>
                                <span>{$scheduled_task['frequency']|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div class="scheduled_tasks_file"><a download
                                                            class="link_to_file {if isset($scheduled_task['download_file_path']) && $scheduled_task['download_file_path']}active{/if}"
                                                            {if !empty($scheduled_task['download_file_path'])}href="{$scheduled_task['download_file_path']|escape:'htmlall':'UTF-8'}"{/if} ><i
                                        class="mic-download-solid"></i>{l s='download file' mod='exportproducts'}</a>
                        </div>


                        <div class="last_executed_scheduled_tasks {if !empty($scheduled_task['start'])} data_isset{else}no_data{/if}">

                            <div class="execution_details_block">
                                <div class="execution_details_header">{l s='Execution Details' mod='exportproducts'}</div>
                                <div class="execution_details_time">
                                    <div class="execution_details_time_start">
                                        <label>{l s='Started at:' mod='exportproducts'}</label><span>{if !empty($scheduled_task['start'])}{$scheduled_task['start']|escape:'htmlall':'UTF-8'}{else}---{/if}</span>
                                    </div>
                                    <div class="execution_details_time_finish">
                                        <label>{l s='Ended:' mod='exportproducts'}</label><span>{if !empty($scheduled_task['finish'])}{$scheduled_task['finish']|escape:'htmlall':'UTF-8'}{else}---{/if}</span>
                                    </div>
                                </div>
                                <div class="execution_details_time_elapsed_block">
                                    <div class="execution_details_time_elapsed_label">{l s='Time Elapsed:' mod='exportproducts'}</div>
                                    <div class="execution_details_time_elapsed">{if isset($scheduled_task['time']) && $scheduled_task['time']}{$scheduled_task['time']|escape:'htmlall':'UTF-8'}{else}---{/if}</div>
                                </div>
                                <div class="execution_details_total_block">
                                    <div class="execution_details_total_label">{l s='Total Products Exported:' mod='exportproducts'}</div>
                                    <div class="execution_details_total">{if !empty($scheduled_task['progress']) && !empty($scheduled_task['num_of_products'])}{$scheduled_task['progress']|escape:'htmlall':'UTF-8'}{l s=' from ' mod='exportproducts'}{$scheduled_task['num_of_products']|escape:'htmlall':'UTF-8'}{else}---{/if}</div>
                                </div>
                            </div>

                            {if !empty($scheduled_task['start'])}{$scheduled_task['start']|escape:'htmlall':'UTF-8'}{else}{l s='No Data' mod='exportproducts'}{/if}
                        </div>

                        <div class="task-status {if !empty($scheduled_task['active'])}enabled{else}disabled{/if}">
                            <span data-status="{if isset($scheduled_task['active']) && $scheduled_task['active']}1{else}0{/if}"></span>
                        </div>
                        <div class="scheduled_tasks_buttons">
                            <a class="toggle_scheduled_tasks_buttons"><i class="mic-more"></i></a>
                            <div class="toggle_scheduled_tasks_content">
                                <ul class="assignments_lists_scheduled_tasks">
                                    <li class="assignment_item_task assignment_item_export"><a
                                                data-id="{if isset($scheduled_task['id_task']) && $scheduled_task['id_task']}{$scheduled_task['id_task']|escape:'htmlall':'UTF-8'}{/if}"
                                                class="export_now"><i
                                                    class="mic-play-solid"></i>{l s='Start Now' mod='exportproducts'}
                                        </a></li>
                                    <li class="assignment_item_task assignment_item_stop"><a
                                                data-id-task="{$scheduled_task['id_task']|escape:'htmlall':'UTF-8'}"
                                                data-id-export-process="{if isset($scheduled_task['id_export_process'])}{$scheduled_task['id_export_process']|escape:'htmlall':'UTF-8'}{else}0{/if}"
                                                class="stop_now"><i
                                                    class="mic-stop-solid"></i>{l s='Stop Cron Task' mod='exportproducts'}
                                        </a></li>
                                    <li class="assignment_item_task assignment_item_edit"><a
                                                data-id="{if isset($scheduled_task['id_task']) && $scheduled_task['id_task']}{$scheduled_task['id_task']|escape:'htmlall':'UTF-8'}{/if}"
                                                class="edit_option"><i
                                                    class="mic-cogs-solid"></i>{l s='Edit Cron Task' mod='exportproducts'}
                                        </a></li>
                                    <li class="assignment_item_task assignment_item_delete"><a
                                                data-id="{if isset($scheduled_task['id_task']) && $scheduled_task['id_task']}{$scheduled_task['id_task']|escape:'htmlall':'UTF-8'}{/if}"
                                                class="delete_option"><i
                                                    class="mic-minus-circle-solid"></i>{l s='Delete Cron Task' mod='exportproducts'}
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
</div>
<div class="no_saved_settings {if !isset($scheduled_tasks) || !$scheduled_tasks}active{/if}">
    <div class="no_last_reports">
        <div class="no_last_reports_img">
            <img src="{$img_folder|escape:'htmlall':'UTF-8'}svg/letter_empty.svg">
        </div>
        <div class="no_last_reports_descr">
            <span>{l s='There is no saved options yet.' mod='exportproducts'}</span>
            <span>{l s='Add a new scheduled task now.' mod='exportproducts'}</span>
        </div>
    </div>
</div>