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
<div class="form_task_edit">
    <div class="header_form_task_edit">
        <a class="back_to_task_list"><i class="mic-long-arrow-alt-left-solid"></i>{l s='Add Cron Task' mod='exportproducts'}</a>
    </div>
    <div class="form_task_edit_conteiner">
        <div class="cron_tasks_descr cron_tasks_current_server_time">
            <div class="cron_tasks_link_block">
                <div class="cron_tasks_descr_header">{l s='Server time! All dates and times in the scheduler are measured according to the server\’s time, as the scheduler is run purely on the server-side.' mod='exportproducts'}</div>
                <span class="cron_tasks_link">{l s='Current server time:' mod='exportproducts'} {date(Context::getContext()->language->date_format_full)|escape:'htmlall':'UTF-8'}
                    (UTC) {date('P')|escape:'htmlall':'UTF-8'} UTC</span>
            </div>
        </div>

        <div class="form_task_left_column">
            <div class="export_one_line">
                <div class="export_field_label">{l s='Task DESCRIPTION:' mod='exportproducts'}</div>
                <input placeholder="{l s='Type Name Here' mod='exportproducts'}"
                       value="{if (isset($settings_scheduled_tasks['description']) && $settings_scheduled_tasks['description'])}{$settings_scheduled_tasks['description']|escape:'htmlall':'UTF-8'}{/if}"
                       class="task_description" name="task_description" type="text">
            </div>
            <div class="export_one_line">
                <div class="export_field_label">{l s='SAVED OPTION:' mod='exportproducts'}</div>
                <div class="task_settings_select">
                    <div class="mpm-fpe-select-wrapper">
                        <select class="task_settings">
                            {if (isset($settings) && $settings)}
                                {foreach  $settings as $k => $setting}
                                    <option {if ((!isset($settings_scheduled_tasks['id_configuration']) || !$settings_scheduled_tasks['id_configuration']) && $k == 0)} selected{/if} {if (isset($settings_scheduled_tasks['id_configuration']) && $settings_scheduled_tasks['id_configuration'] == $setting['id_configuration'])} selected{/if}
                                            value="{$setting['id_configuration']|escape:'htmlall':'UTF-8'}">{$setting['name']|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>
            </div>
            <div class="export_one_line_with_two_column ">
                <div class="task_one_line_left">
                    <div class="export_one_line export_one_line_one_shot">
                        <div class="export_field_label">{l s='ONE SHOT:' mod='exportproducts'}</div>
                        <div class="one_shot_line">
                            <div class="switch_myprestamodules">
                                <div class="switch_content">
                                    <input type="radio" class="switch-input" name="one_shot" value="1"
                                            id="switch-one_shot-yes" {if (isset($settings_scheduled_tasks['one_shot']) && $settings_scheduled_tasks['one_shot'] == 1)} checked{/if} >
                                    <label for="switch-one_shot-yes"
                                           class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                                    <input type="radio" class="switch-input" name="one_shot" value="0"
                                            id="switch-one_shot-no" {if (isset($settings_scheduled_tasks['one_shot']) && $settings_scheduled_tasks['one_shot'] == 0)} checked{/if} {if (!isset($settings_scheduled_tasks['one_shot']) || !$settings_scheduled_tasks['one_shot'])} checked{/if}>
                                    <label for="switch-one_shot-no"
                                           class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                                    <span class="switch-selection"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="export_one_line">
                        <div class="export_field_label">{l s='Email message:' mod='exportproducts'}</div>
                        <div class="email_message_line">
                            <div class="switch_myprestamodules">
                                <div class="switch_content">
                                    <input type="radio" class="switch-input" name="email_message" value="1"
                                           id="switch-email_message-yes" {if (isset($settings_scheduled_tasks['email_message']) && $settings_scheduled_tasks['email_message'] == 1)} checked{/if}>
                                    <label for="switch-email_message-yes"
                                           class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                                    <input type="radio" class="switch-input" name="email_message" value="0"
                                           id="switch-email_message-no" {if (isset($settings_scheduled_tasks['email_message']) && $settings_scheduled_tasks['email_message'] == 0) || !isset($settings_scheduled_tasks['email_message'])} checked{/if}>
                                    <label for="switch-email_message-no"
                                           class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                                    <span class="switch-selection"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="task_one_line_right">
                    <div class="export_one_line">
                        <div class="export_field_label">{l s='EXPORT ONLY NOT EXPORTED products:' mod='exportproducts'}</div>
                        <div class="export_not_exported_line">
                            <div class="switch_myprestamodules">
                                <div class="switch_content">
                                    <input type="radio" class="switch-input" name="export_not_exported" value="1"
                                           id="switch-export_not_exported-yes" {if (isset($settings_scheduled_tasks['export_not_exported']) && $settings_scheduled_tasks['export_not_exported'] == 1)} checked{/if} >
                                    <label for="switch-export_not_exported-yes"
                                           class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                                    <input type="radio" class="switch-input" name="export_not_exported" value="0"
                                           id="switch-export_not_exported-no" {if (isset($settings_scheduled_tasks['export_not_exported']) && $settings_scheduled_tasks['export_not_exported'] == 0)} checked{/if} {if (!isset($settings_scheduled_tasks['export_not_exported']) || !$settings_scheduled_tasks['export_not_exported'])} checked{/if}>
                                    <label for="switch-export_not_exported-no"
                                           class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                                    <span class="switch-selection"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="export_one_line">
                        <div class="export_field_label">{l s='ATTACH FILE:' mod='exportproducts'}</div>
                        <div class="attach_file_line">
                            <div class="switch_myprestamodules">
                                <div class="switch_content">
                                    <input type="radio" class="switch-input" name="attach_file" value="1"
                                           id="switch-attach_file-yes" {if (isset($settings_scheduled_tasks['attach_file']) && $settings_scheduled_tasks['attach_file'] == 1)} checked{/if} >
                                    <label for="switch-attach_file-yes"
                                           class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                                    <input type="radio" class="switch-input" name="attach_file" value="0"
                                           id="switch-attach_file-no" {if (isset($settings_scheduled_tasks['attach_file']) && $settings_scheduled_tasks['attach_file'] == 0) || !isset($settings_scheduled_tasks['attach_file']) } checked{/if}>
                                    <label for="switch-attach_file-no"
                                           class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                                    <span class="switch-selection"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clear_both"></div>
            </div>
            <div class="export_one_line export_one_line_messages_block {if (isset($settings_scheduled_tasks['email_message']) && $settings_scheduled_tasks['email_message'] == 1)} active{/if}">
                <div class="export_field_label">{l s='Emails For Products Export Report:' mod='exportproducts'}</div>
                <div class="export_emails_content">
                    <textarea placeholder="{l s='For Example:
admin@mail.com
username@mail.com
myprestamodules@gmail.com
                    ' mod='exportproducts'}"
                              class="export_emails">{if (isset($settings_scheduled_tasks['export_emails']) && $settings_scheduled_tasks['export_emails'])}{$settings_scheduled_tasks['export_emails']|escape:'htmlall':'UTF-8'}{/if}</textarea>
                </div>
            </div>
        </div>
        <div class="form_task_right_column">
            <div class="export_one_line frequencyDescriptionLine">
                <div class="export_field_label">{l s='TASK FREQUENCY:' mod='exportproducts'}</div>
                <div class="frequency_task_content">

                    <div class="frequency_task_input_content">
                        <input class="frequency_task" type="text" value="{$frequency|escape:'htmlall':'UTF-8'}">
                        <span class="frequency_task_error">{l s='Syntax Error!' mod='exportproducts'}</span>
                    </div>


                    <div class="next_run_info_block">
                        <div class="next_run_info_label">{l s='Will be started at:' mod='exportproducts'}</div>
                        <div class="next_run_info"></div>
                        <div class="clear_both"></div>
                    </div>
                    <div class="clear_both"></div>

                </div>
            </div>
            <div class="export_one_line "> {include file="{$path_tpl|escape:'htmlall':'UTF-8'}frequencyDescription.tpl" }</div>
            <div class="export_one_line"> {include file="{$path_tpl|escape:'htmlall':'UTF-8'}frequencyInfo.tpl" }</div>
        </div>
        <div class="clear_both"></div>
    </div>
    <div class="form_task_footer">
        <a data-id="{$id_task|escape:'htmlall':'UTF-8'}" class="save_task">{l s='Save' mod='exportproducts'}</a>
    </div>
</div>