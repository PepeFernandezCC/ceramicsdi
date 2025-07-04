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
<div class="exportproducts_block export_template_name_block">
    <div class="exportproducts_block_title">{l s='Export Template Name' mod='exportproducts'}</div>
    <div class="export_template_name_input">
        <div class="export_one_line">
            <div class="export_field_label">{l s='Export Template Name' mod='exportproducts'}</div>
            <input type="text" placeholder="{l s='Type Name Here' mod='exportproducts'}" class="export_template_name"
                   name="export_template_name"
                   value="{if isset($settings['name']) && $settings['name']}{$settings['name']|escape:'htmlall':'UTF-8'}{else}{l s='Export Template Name' mod='exportproducts'}{/if}">
        </div>
    </div>
</div>

<div class="exportproducts_block export_template_options_block">
    <div class="exportproducts_block_title">{l s='Export Template Options' mod='exportproducts'}</div>
    <div class="export_template_options">
        <div class="export_one_line">
            <div class="export_field_label">{l s='Choose a fileformat:' mod='exportproducts'}</div>
            <div class="select_file_format">
                <div class="select_format_button">
                    <div class="select_format_icon">
                        <div><img src="{$img_folder|escape:'htmlall':'UTF-8'}svg/xls.svg"></div>
                    </div>
                    <label for="format_file_xls" class="format_file_label">
                        <input type="radio" name="format_file" id="format_file_xls" class="format_file" value="xlsx"
                               {if (isset($settings['format_file']) && $settings['format_file'] == 'xlsx') || (!isset($settings['format_file']) || !$settings['format_file'])}checked{/if} ><label></label>
                        {l s='XLS File Format' mod='exportproducts'}
                    </label>
                </div>
                <div class="select_format_button">
                    <div class="select_format_icon">
                        <div><img src="{$img_folder|escape:'htmlall':'UTF-8'}svg/csv.svg"></div>
                    </div>
                    <label for="format_file_csv" class="format_file_label">
                        <input type="radio" name="format_file" id="format_file_csv" class="format_file" value="csv"
                               {if isset($settings['format_file']) && $settings['format_file'] == 'csv'}checked{/if}><label></label>
                        {l s='CSV File Format' mod='exportproducts'}
                    </label>
                </div>
                <div class="select_format_button">
                    <div class="select_format_icon">
                        <div><img src="{$img_folder|escape:'htmlall':'UTF-8'}svg/xml.svg"></div>
                    </div>
                    <label for="format_file_xml" class="format_file_label">
                        <input type="radio" name="format_file" id="format_file_xml" class="format_file" value="xml"
                               {if isset($settings['format_file']) && $settings['format_file'] == 'xml'}checked{/if}><label></label>
                        {l s='XML File Format' mod='exportproducts'}
                    </label>
                </div>
                <div class="select_format_button">
                    <div class="select_format_icon">
                        <div><img src="{$img_folder|escape:'htmlall':'UTF-8'}google_logo_22.png"></div>
                    </div>
                    <label for="format_file_gmf" class="format_file_label">
                        <input type="radio" name="format_file" id="format_file_gmf" class="format_file" value="gmf"
                               {if isset($settings['format_file']) && $settings['format_file'] == 'gmf'}checked{/if}><label></label>
                        {l s='Google Shopping' mod='exportproducts'}
                    </label>
                </div>
            </div>
        </div>
        <div class="delimiter_separator {if (isset($settings['format_file']) && $settings['format_file'] == 'csv')} active{/if}">
            <div class="delimiter_separator_column">
                <div class="export_one_line">
                    <div class="export_field_label">{l s='Delimiter' mod='exportproducts'}</div>
                    <div class="mpm-fpe-select-wrapper">
                        <select class="delimiter_csv">
                            <option {if (isset($settings['delimiter_csv']) && $settings['delimiter_csv'] == ",")} selected{/if}
                                    value=",">,
                            </option>
                            <option {if (isset($settings['delimiter_csv']) && $settings['delimiter_csv'] == ";")} selected{/if}
                                    value=";">;
                            </option>
                            <option {if (isset($settings['delimiter_csv']) && $settings['delimiter_csv'] == ":")} selected{/if}
                                    value=":">:
                            </option>
                            <option {if (isset($settings['delimiter_csv']) && $settings['delimiter_csv'] == ".")} selected{/if}
                                    value=".">.
                            </option>
                            <option {if (isset($settings['delimiter_csv']) && $settings['delimiter_csv'] == "/")} selected{/if}
                                    value="/">/
                            </option>
                            <option {if (isset($settings['delimiter_csv']) && $settings['delimiter_csv'] == "|")} selected{/if}
                                    value="|">|
                            </option>
                            <option {if (isset($settings['delimiter_csv']) && $settings['delimiter_csv'] == "_")} selected{/if}
                                    value="_">_
                            </option>
                            <option {if (isset($settings['delimiter_csv']) && $settings['delimiter_csv'] == "space")} selected{/if}
                                    value="space">space
                            </option>
                            <option {if (isset($settings['delimiter_csv']) && $settings['delimiter_csv'] == "tab")} selected{/if}
                                    value="tab">tab
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="delimiter_separator_column">
                <div class="export_one_line">
                    <div class="export_field_label">{l s='Separator' mod='exportproducts'}</div>
                    <div class="mpm-fpe-select-wrapper">
                        <select class="separator_csv">
                            <option {if (isset($settings['separator_csv']) && $settings['separator_csv'] == "1")} selected{/if}
                                    value="1">" "
                            </option>
                            <option {if (isset($settings['separator_csv']) && $settings['separator_csv'] == "2")} selected{/if}
                                    value="2">' '
                            </option>
                            <option {if (isset($settings['separator_csv']) && $settings['separator_csv'] == "3")} selected{/if}
                                    value="3">no
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="delimiter_separator_column">
                <div class="export_one_line">
                    <div class="export_field_label">{l s='Encoding:' mod='exportproducts'}</div>
                    <div class="mpm-fpe-select-wrapper">
                        <select class="encoding">
                            <option {if (isset($settings['encoding']) && $settings['encoding'] == "UTF-8-BOM")} selected{/if}
                                    value="UTF-8-BOM">UTF-8-BOM
                            </option>
                            <option {if (isset($settings['encoding']) && $settings['encoding'] == "UTF-8")} selected{/if}
                                    value="UTF-8">UTF-8
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="clear_both"></div>
        </div>

        <div class="multistore-lang-container export_one_line">
            {if is_int($all_shops)}
                <input type="hidden" name="id_shop" value="{$all_shops|escape:'htmlall':'UTF-8'}">
            {else}
                <div class="multistore_block">
                    <div class="export_one_line">
                        <div class="export_field_label">{l s='Select store:' mod='exportproducts'}</div>
                        <div class="multistore_select">
                            <div class="mpm-fpe-select-wrapper">
                                <select name="id_shop">
                                    {foreach $all_shops as $shop}
                                        <option {if (isset($settings['id_shop']) && $settings['id_shop'] == $shop['id_shop'])} selected{/if}
                                                value="{$shop['id_shop']|escape:'htmlall':'UTF-8'}">{$shop['name']|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}

            <div class="language-block">
                <div class="export_field_label">{l s='Select language:' mod='exportproducts'}</div>
                <div class="language_select">
                    <div class="mpm-fpe-select-wrapper">
                        <select name="id_lang">
                            {foreach  $all_languages as $language}
                                <option {if (isset($settings['id_lang']) && $settings['id_lang'] == $language['id_lang'])} selected{/if}
                                        value="{$language['id_lang']|escape:'htmlall':'UTF-8'}">{$language['name']|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="export_one_line">
            <div class="export_field_label">{l s='Feed Target' mod='exportproducts'}</div>
            <div class="feed_target_block">
                <div class="feed_target_column">
                    <label for="feed_target_1"
                           class="mpm_button_default {if (isset($settings['feed_target']) && $settings['feed_target'] == 'file_system') || (!isset($settings['feed_target']) || !$settings['feed_target'])}active{/if}">{l s='File System' mod='exportproducts'}
                        <input type="radio" id="feed_target_1" class="feed_target" name="feed_target"
                               value="file_system"
                               {if (isset($settings['feed_target']) && $settings['feed_target'] == 'file_system') || (!isset($settings['feed_target']) || !$settings['feed_target'])}checked{/if}>
                    </label>
                </div>
                <div class="feed_target_column">
                    <label for="feed_target_2"
                           class="mpm_button_default_second {if (isset($settings['feed_target']) && $settings['feed_target'] == 'ftp')}active{/if}">{l s='FTP' mod='exportproducts'}
                        <input type="radio" id="feed_target_2" class="feed_target" name="feed_target" value="ftp"
                               {if (isset($settings['feed_target']) && $settings['feed_target'] == 'ftp')}checked{/if}>
                    </label>
                </div>
                <div class="clear_both"></div>
            </div>
        </div>
        <div class="ftp_access_block {if (isset($settings['feed_target']) && $settings['feed_target'] == 'ftp')}active{/if}">
            <div class="export_one_line">
                <div class="export_field_label">{l s='TRANSFER PROTOCOL:' mod='exportproducts'}</div>
                <div class="ftp_protocol_productexport">
                    <div class="mpm-fpe-select-wrapper">
                        <select class="ftp_protocol">
                            <option {if isset($settings['ftp_protocol']) && $settings['ftp_protocol'] == 'ftp'} selected{/if} value="ftp">
                                {l s='FTP' mod='exportproducts'}
                            </option>
                            <option {if isset($settings['ftp_protocol']) && $settings['ftp_protocol'] == 'sftp'} selected{/if} value="sftp">
                                {l s='SFTP' mod='exportproducts'}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="export_one_line">
                <div class="export_field_label">{l s='FTP SERVER:' mod='exportproducts'}</div>
                <div class="ftp_server_block">
                    <input type="text" placeholder="{l s='192.168.0.1' mod='exportproducts'}" class="ftp_server"
                           name="ftp_server"
                           value="{if isset($settings['ftp_server']) && $settings['ftp_server']}{$settings['ftp_server']|escape:'htmlall':'UTF-8'}{/if}">
                </div>
            </div>

            <div class="export_one_line" id="ftp_authentication_type_input_group">
                <div class="export_field_label">{l s='AUTHENTICATION TYPE:' mod='ordersexport'}</div>
                <div class="ftp_authentication_type_ordersexport">
                    <div class="mpm-fpe-select-wrapper">
                        <select class="ftp_authentication_type">
                            <option {if isset($settings['ftp_authentication_type']) && $settings['ftp_authentication_type'] == 'password'} selected{/if} value="password">
                                {l s='Password' mod='ordersexport'}
                            </option>
                            <option {if isset($settings['ftp_authentication_type']) && $settings['ftp_authentication_type'] == 'key'} selected{/if} value="key">
                                {l s='Key File' mod='ordersexport'}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="export_one_line">
                <div class="export_field_label">{l s='FTP USERNAME:' mod='exportproducts'}</div>
                <div class="ftp_username_block">
                    <input type="text" placeholder="{l s='root' mod='exportproducts'}" class="ftp_username"
                           name="ftp_username"
                           value="{if isset($settings['ftp_username']) && $settings['ftp_username']}{$settings['ftp_username']|escape:'htmlall':'UTF-8'}{/if}">
                </div>
            </div>

            <div class="export_one_line">
                <div class="export_field_label">{l s='FTP PASSWORD:' mod='exportproducts'}</div>
                <div class="ftp_password_block">
                    <input type="password" placeholder="{l s='***********' mod='exportproducts'}" class="ftp_password"
                           name="ftp_password"
                           value="{if isset($settings['ftp_password']) && $settings['ftp_password']}{$settings['ftp_password']|escape:'htmlall':'UTF-8'}{/if}">
                </div>
            </div>

            <div class="export_one_line" id="ftp_key_path_input_group">
                <div class="export_field_label">{l s='SFTP KEY PATH:' mod='exportproducts'}</div>
                <div class="ftp_key_path_block">
                    <input type="text" class="ftp_key_path"
                           name="ftp_key_path"
                           placeholder="{l s='Path' mod='exportproducts'}"
                           value="{if isset($settings['ftp_key_path']) && $settings['ftp_key_path']}{$settings['ftp_key_path']|escape:'htmlall':'UTF-8'}{/if}">
                </div>
            </div>

            <div class="export_one_line">
                <div class="export_field_label">{l s='ABSOLUTE PATH TO A FOLDER:' mod='exportproducts'}</div>
                <div class="ftp_folder_path_block">
                    <input type="text" placeholder="{l s='public_html' mod='exportproducts'}"
                           class="ftp_folder_path" name="ftp_folder_path"
                           value="{if isset($settings['ftp_folder_path']) && $settings['ftp_folder_path']}{$settings['ftp_folder_path']|escape:'htmlall':'UTF-8'}{/if}">
                </div>
            </div>

            <div class="export_one_line">
                <div class="export_field_label">{l s='FTP PORT:' mod='exportproducts'}</div>
                <div class="ftp_port_block">
                    <input type="text" placeholder="{l s='8080' mod='exportproducts'}" class="ftp_port"
                           name="ftp_port"
                           value="{if isset($settings['ftp_port']) && $settings['ftp_port']}{$settings['ftp_port']|escape:'htmlall':'UTF-8'}{/if}">
                </div>
            </div>

            <div class="export_one_line no-sftp-field">
                <div class="export_field_label">{l s='FILE TRANSFER MODE:' mod='exportproducts'}</div>
                <div class="ftp_file_transfer_mode_productexport">
                    <div class="mpm-fpe-select-wrapper">
                        <select class="ftp_file_transfer_mode">
                            <option {if isset($settings['ftp_file_transfer_mode']) && $settings['ftp_file_transfer_mode'] == '1'} selected{/if} value="1">
                                {l s='FTP_ASCII' mod='exportproducts'}
                            </option>
                            <option {if isset($settings['ftp_file_transfer_mode']) && $settings['ftp_file_transfer_mode'] == '2'} selected{/if} value="2">
                                {l s='FTP_BINARY' mod='exportproducts'}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="export_one_line no-sftp-field">
                <div class="export_field_label">{l s='PASSIVE MODE:' mod='exportproducts'}</div>
                <div class="ftp_passive_mode_block">
                    <div class="switch_myprestamodules">
                        <div class="switch_content">
                            <input type="radio" class="switch-input" name="ftp_passive_mode" value="1"
                                   id="switch-yes"
                                   {if (isset($settings['ftp_passive_mode']) && $settings['ftp_passive_mode'])}checked{/if}>
                            <label for="switch-yes"
                                   class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                            <input type="radio" class="switch-input" name="ftp_passive_mode" value="0"
                                   id="switch-no"
                                   {if (isset($settings['ftp_passive_mode']) && !$settings['ftp_passive_mode']) || !isset($settings['ftp_passive_mode'])}checked{/if}>
                            <label for="switch-no"
                                   class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                            <span class="switch-selection"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="export_one_line">
            <div class="export_field_label">
                {l s='Name of exported file:' mod='exportproducts'}
                <span class="pre_defined_field_block">
                    <span class="pre_defined_field_icon"><i class="mic-question-circle-solid"></i></span>
                    <span class="pre_defined_field">
                        {l s='You can set name for file or name will be given by system.' mod='exportproducts'}
                    </span>
                </span>
            </div>
            <div class="export_file_name_block">
                <div class="export_file_name">
                    <input type="text"
                           class="file_name" name="file_name"
                           value="{if isset($settings['file_name']) && $settings['file_name']}{$settings['file_name']|escape:'htmlall':'UTF-8'}{else}{$export_template_name|escape:'htmlall':'UTF-8'}{/if}">

                    <div class="export-file-link-preview-container" data-export-files-path="{$export_files_path|escape:'htmlall':'UTF-8'}">
                        <span>{l s='The file will be available by the following link:' mod='exportproducts'}</span>
                        <a href="" target="_blank"></a>
                    </div>
                </div>
                <div class="export_file_name_descr">
                    {l s='To add current date/time to file name, put {DATE PATTERN} variable into name field and replace "DATE_PATTERN" with valid PHP date pattern."' mod='exportproducts'}
                    <span>{l s='Example: You type - "exported_product_{d-m-Y H:i:s}" and your file name will be "exported_product_17-09-2017 12:30:00".' mod='exportproducts'}</span>
                    {l s=' Learn more about PHP date on official documentation page:' mod='exportproducts'}
                    <a target="_blank"
                       href="http://php.net/manual/en/function.date.php">{l s='http://php.net/manual/en/function.date.php' mod='exportproducts'}</a>
                </div>
                <div class="clear_both"></div>
            </div>
        </div>

        <div class="another_settings">
            <div class="block_file_settings">
                <div class="export_one_line display_header_block">
                    <div class="export_field_label">
                        {l s='Display header:' mod='exportproducts'}
                        <span class="pre_defined_field_block">
                            <span class="pre_defined_field_icon"><i class="mic-question-circle-solid"></i></span>
                            <span class="pre_defined_field">
                                {l s='Add a first line in the file with columns names. You can modify the names with the translation tool' mod='exportproducts'}
                            </span>
                        </span>
                    </div>
                    <div class="display_header_productexport">
                        <div class="switch_myprestamodules">
                            <div class="switch_content">
                                <input type="radio" class="switch-input" name="display_header" value="1"
                                       id="switch-display-header-yes"
                                       {if isset($settings['display_header']) && $settings['display_header']}checked{/if} {if !isset($settings['display_header']) || !$settings['display_header']}checked{/if}>
                                <label for="switch-display-header-yes"
                                       class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                                <input type="radio" class="switch-input" name="display_header" value="0"
                                       id="switch-display-header-no"
                                       {if isset($settings['display_header']) && !$settings['display_header']}checked{/if}>
                                <label for="switch-display-header-no"
                                       class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                                <span class="switch-selection"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="export_one_line strip_tags_block">
                    <div class="export_field_label">
                        {l s='Strip tags:' mod='exportproducts'}
                    </div>
                    <div class="strip_tags_productexport">
                        <div class="switch_myprestamodules">
                            <div class="switch_content">
                                <input type="radio" class="switch-input" name="strip_tags" value="1"
                                       id="switch-strip-tags-yes"
                                       {if (!empty($settings['strip_tags']))}checked{/if}>
                                <label for="switch-strip-tags-yes"
                                       class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                                <input type="radio" class="switch-input" name="strip_tags" value="0"
                                       id="switch-strip-tags-no"
                                       {if (empty($settings['strip_tags']))}checked{/if}>
                                <label for="switch-strip-tags-no"
                                       class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                                <span class="switch-selection"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="export_one_line each_product_combinations_separate_line">
                    <div class="export_field_label">{l s='Each combination in a separate row:' mod='exportproducts'}
                        <span class="pre_defined_field_block">
                            <span class="pre_defined_field_icon"><i class="mic-question-circle-solid"></i></span>
                            <span class="pre_defined_field">
                                {l s='If activated, each product combination will be exported in separate row' mod='exportproducts'}
                            </span>
                        </span>
                    </div>
                    <div class="combinations_separate_line">
                        <div class="switch_myprestamodules">
                            <div class="switch_content">
                                <input type="radio" class="switch-input" name="separate" value="1"
                                       id="switch-separate-yes" {if (isset($settings['separate']) && $settings['separate'] == 1)} checked{/if} {if (!isset($settings['separate']) || !$settings['separate'])} checked{/if}>
                                <label for="switch-separate-yes"
                                       class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                                <input type="radio" class="switch-input" name="separate" value="0"
                                       id="switch-separate-no" {if (isset($settings['separate']) && $settings['separate'] == 0)} checked{/if}>
                                <label for="switch-separate-no"
                                       class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                                <span class="switch-selection"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="export_one_line style_spreadsheet_block">
                    <div class="export_field_label">{l s='Style Spreadsheet' mod='exportproducts'}
                        <span class="pre_defined_field_block">
                            <span class="pre_defined_field_icon"><i class="mic-question-circle-solid"></i></span>
                            <span class="pre_defined_field">
                                {l s='Enabling this option increases the export time.' mod='exportproducts'}
                            </span>
                        </span>
                    </div>

                    <div class="switch_myprestamodules">
                        <div class="switch_content">
                            <input type="radio" class="switch-input" name="style_spreadsheet" value="1"
                                   id="switch-style-spreadsheet-yes" {if (isset($settings['style_spreadsheet']) && $settings['style_spreadsheet'] == 1)} checked{/if} {if (!isset($settings['style_spreadsheet']) || !$settings['style_spreadsheet'])} checked{/if}>
                            <label for="switch-style-spreadsheet-yes"
                                   class="switch-label switch-label-on">{l s='Yes' mod='exportproducts'}</label>
                            <input type="radio" class="switch-input" name="style_spreadsheet" value="0"
                                   id="switch-style-spreadsheet-no" {if (isset($settings['style_spreadsheet']) && $settings['style_spreadsheet'] == 0)} checked{/if}>
                            <label for="switch-style-spreadsheet-no"
                                   class="switch-label switch-label-off">{l s='No' mod='exportproducts'}</label>
                            <span class="switch-selection"></span>
                        </div>
                    </div>
                </div>
                <div class="export_one_line products_per_iteration_block">
                    <div class="export_field_label">{l s='Products Per Iteration:' mod='exportproducts'}
                        <span class="pre_defined_field_block">
                            <span class="pre_defined_field_icon"><i class="mic-question-circle-solid"></i></span>
                            <span class="pre_defined_field">
                                {l s='A number of products that will be processed by one server request. If you experiencing server timeout issues - try to decrease this setting value. But notice, that it will slow down an overall export speed.' mod='exportproducts'}
                            </span>
                        </span>
                    </div>

                    <div class="products_per_iteration_productexport">
                        <input type="text" class="products_per_iteration" name="products_per_iteration"
                               value="{if !empty($settings['products_per_iteration'])}{$settings['products_per_iteration']|escape:'htmlall':'UTF-8'}{else}1000{/if}">
                    </div>
                </div>
                <div class="export_one_line date_format_block">
                    <div class="export_field_label">{l s='Date format:' mod='exportproducts'}</div>
                    <div class="date_farmat_productexport">
                        <div class="mpm-fpe-select-wrapper">
                            <select class="date_format">
                                {foreach  $date_format as $format}
                                    <option {if (isset($settings['date_format']) && $settings['date_format'] == $format['id'])} selected{/if}
                                            value="{$format['id']|escape:'htmlall':'UTF-8'}">{$format['name']|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="export_one_line currency_block">
                    <div class="export_field_label">{l s='Currency:' mod='exportproducts'}</div>
                    <div class="currency_productexport">
                        <div class="mpm-fpe-select-wrapper">
                            <select class="currency">
                                {foreach $all_currencies as $currency}
                                    <option {if isset($settings['currency']) && $settings['currency'] == $currency['id_currency']}
                                            selected
                                            {/if} value="{$currency['id_currency']|escape:'htmlall':'UTF-8'}">{$currency['name']|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="export_one_line separator_decimal_block">
                    <div class="export_field_label">{l s='Separator of decimal points:' mod='exportproducts'}</div>
                    <div class="date_farmat_productexport">
                        <div class="mpm-fpe-select-wrapper">
                            <select class="separator_decimal_points">
                                <option {if (isset($settings['separator_decimal_points']) && $settings['separator_decimal_points'] == 1)} selected{/if}
                                        value="1">{l s='Point (.)' mod='exportproducts'}</option>
                                <option {if (isset($settings['separator_decimal_points']) && $settings['separator_decimal_points'] == 2)} selected{/if}
                                        value="2">{l s='Comma (,)' mod='exportproducts'}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="export_one_line thousands_separator_block">
                    <div class="export_field_label">{l s='Thousands separator:' mod='exportproducts'}</div>
                    <div class="date_farmat_productexport">
                        <div class="mpm-fpe-select-wrapper">
                            <select class="thousands_separator">
                                <option {if (isset($settings['thousands_separator']) && $settings['thousands_separator'] == 1)} selected{/if}
                                        value="1">{l s='Space' mod='exportproducts'}</option>
                                <option {if (isset($settings['thousands_separator']) && $settings['thousands_separator'] == 2)} selected{/if}
                                        value="2">{l s='Point (.)' mod='exportproducts'}</option>
                                <option {if (isset($settings['thousands_separator']) && $settings['thousands_separator'] == 3)} selected{/if}
                                        value="3">{l s='Comma (,)' mod='exportproducts'}</option>
                                <option {if (isset($settings['thousands_separator']) && $settings['thousands_separator'] == 4)} selected{/if}
                                        value="4">{l s='None' mod='exportproducts'}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="export_one_line price_decoration_block">
                    <div class="export_field_label">{l s='Price decoration:' mod='exportproducts'}
                        <span class="pre_defined_field_block">
                            <span class="pre_defined_field_icon"><i class="mic-question-circle-solid"></i></span>
                            <span class="pre_defined_field">
                                {l s='Will be used in the prices. "[PRICE] USD" will give "13.46 USD", "$[PRICE]" will give "$13.46", live empty to have only number' mod='exportproducts'}
                            </span>
                        </span>
                    </div>

                    <div class="price_decoration_productexport">
                        <input type="text" class="price_decoration" name="price_decoration"
                               value="{if isset($settings['price_decoration'])}{$settings['price_decoration']|escape:'htmlall':'UTF-8'}{else}[PRICE]{/if}">
                    </div>
                </div>

                <div class="export_one_line number_decimal_block">
                    <div class="export_field_label">{l s='Number of decimal points:' mod='exportproducts'}
                        <span class="pre_defined_field_block">
                            <span class="pre_defined_field_icon"><i class="mic-question-circle-solid"></i></span>
                            <span class="pre_defined_field">
                                {l s='Will be used in the prices and size. You can choose to have 5.12 instead of 5.121123.' mod='exportproducts'}
                            </span>
                        </span>
                    </div>
                    <div class="date_farmat_productexport">
                        <div class="mpm-fpe-select-wrapper">
                            <select class="round_value">
                                <option {if (isset($settings['round_value']) && $settings['round_value'] == 0)} selected{/if}
                                        value="0">0
                                </option>
                                <option {if (isset($settings['round_value']) && $settings['round_value'] == 1)} selected{/if}
                                        value="1">1
                                </option>
                                <option {if !isset($settings['round_value']) || (isset($settings['round_value']) && $settings['round_value'] == 2)} selected{/if}
                                        value="2">2
                                </option>
                                <option {if (isset($settings['round_value']) && $settings['round_value'] == 3)} selected{/if}
                                        value="3">3
                                </option>
                                <option {if (isset($settings['round_value']) && $settings['round_value'] == 4)} selected{/if}
                                        value="4">4
                                </option>
                                <option {if (isset($settings['round_value']) && $settings['round_value'] == 5)} selected{/if}
                                        value="5">5
                                </option>
                                <option {if (isset($settings['round_value']) && $settings['round_value'] == 6)} selected{/if}
                                        value="6">6
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="export_one_line">
                    <div class="export_field_label">{l s='Image Type:' mod='exportproducts'}</div>
                    <div class="mpm-fpe-select-wrapper">
                        <select class="image_type">
                            <option value="original_size" {if !empty($settings['image_type']) && $settings['image_type'] == 'original'}selected{/if}>{l s='Original Size' mod='exportproducts'}</option>
                            {foreach $images_types as $image_type}
                                <option {if (!empty($settings['image_type']) && $settings['image_type'] == $image_type['name'])} selected{/if}
                                        value="{$image_type['name']|escape:'htmlall':'UTF-8'}">{$image_type['name']|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="export_one_line sort_by_block">
                    <div class="export_field_label">{l s='Sort by:' mod='exportproducts'}</div>
                    <div class="date_farmat_productexport">
                        <div class="mpm-fpe-select-wrapper">
                            <select class="sort_by">
                                {foreach  $sorts as $sort}
                                    <option {if (isset($settings['sort_by']) && $settings['sort_by'] == $sort['id'])} selected{/if}
                                            value="{$sort['id']|escape:'htmlall':'UTF-8'}">{$sort['name']|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="export_one_line order_way_block">
                    <div class="export_field_label">{l s='Sort Way:' mod='exportproducts'}</div>
                    <div class="radio_myprestamodules">
                        <div class="radio_content">
                            <input type="radio" class="radio-input" name="order_way" value="asc"
                                   id="radio-yes" {if (isset($settings['order_way']) && $settings['order_way'] == 'asc')} checked{/if} {if (!isset($settings['order_way']) || !$settings['order_way'])} checked{/if}>
                            <label for="radio-yes"
                                   class="radio-label radio-label-off">{l s='ASC' mod='exportproducts'}</label>
                            <input type="radio" class="radio-input" name="order_way" value="desc"
                                   id="radio-no" {if (isset($settings['order_way']) && $settings['order_way'] == 'desc')} checked{/if}>
                            <label for="radio-no"
                                   class="radio-label radio-label-on">{l s='DESC' mod='exportproducts'}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mpm_pe_google_categories_container">
        <div id="mpm_pe_shop_categories_tree"></div>
        <div id="mpm_pe_google_categories_association_container" class="opened">
            <div class="exportproducts_block_title">
                <span>{l s='Store Category Association With Google Categories' mod='exportproducts'}</span>
                <i class="mic-chevron-down-solid"></i>
            </div>
            <div class="association-block">
                <div id="mpm_pe_google_categories_association_tree_container">
                    {*Impossible to escape html because generated tree will be broken*}
                    {$shop_categories_tree|escape:'htmlall':'UTF-8'|unescape}
                    {*Impossible to escape html because generated tree will be broken*}
                </div>

                <div id="mpm_pe_google_categories_association" data-selected-shop-categories="{$shop_categories_associated_with_google}">
                    <div class="export_field_label">{l s='Categories Association With Google:' mod='exportproducts'}</div>

                    <div class="main-content">
                        {if !empty($saved_google_categories_data)}
                            {foreach $saved_google_categories_data as $google_category}
                                <div class="google-category-assoc-container" data-id-category="{$google_category['shop_category_id']|escape:'htmlall':'UTF-8'}">
                                    <div class="shop-category">{$google_category['shop_category_name']|escape:'htmlall':'UTF-8'}</div>
                                    <div class="mpm-fpe-select-wrapper fixed-search-enabled">
                                        <img src="{$img_folder|escape:'htmlall':'UTF-8'}google_logo.png">
                                        <select class="google-category">
                                            {if !empty($google_category['google_category_options'])}
                                                {foreach $google_category['google_category_options'] as $google_category_option}
                                                    <option value="{$google_category_option['id']|escape:'htmlall':'UTF-8'}" {if $google_category['selected_google_category_id'] == $google_category_option['id']}selected{/if}>{$google_category_option['title']|escape:'htmlall':'UTF-8'}</option>
                                                {/foreach}
                                            {/if}
                                        </select>
                                    </div>
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {include file="{$path_tpl|escape:'htmlall':'UTF-8'}buttons_block.tpl" }
</div>
