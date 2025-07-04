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
<div class="form_preview_file">
    <div class="close_block"><i class="mic-times-solid"></i></div>
    <div class="form_preview_file_header">
        <i class="mic-table-solid"></i>{l s='Excel Table Preview' mod='exportproducts'}
    </div>
    <div class="form_preview_file_content">
        <table class="table_preview_file">
            <thead>
            <tr>
                {if isset($sheet_grid) && $sheet_grid }
                    <th class="preview_sheet_grid field_number"></th>
                    {foreach $sheet_grid as $val}
                        <th class="preview_sheet_grid">{$val|escape:'htmlall':'UTF-8'}</th>
                    {/foreach}
                {/if}
            </tr>
            </thead>
            <tbody>

            {if $display_header}
                <tr class="preview_rows">
                    <td class="field_number"><span>1</span></td>

                    {foreach $fields_names as $field_name}
                        <td class="preview_field_name">
                            <span>{$field_name|escape:'htmlall':'UTF-8'}</span>
                        </td>
                    {/foreach}
                </tr>
            {/if}

            {for $i = 0 to 10}
                <tr class="preview_rows">
                    {if $display_header}
                        <td class="field_number"><span>{($i + 2)|escape:'htmlall':'UTF-8'}</span></td>
                    {else}
                        <td class="field_number"><span>{($i + 1)|escape:'htmlall':'UTF-8'}</span></td>
                    {/if}

                    {if isset($demo_products[$i])}
                        {foreach $demo_products[$i] as $property_id => $property_value}
                            <td class="preview_field_value">
                                {if $property_id == 'image_cover'}
                                    <span class="preview_field_img">
                                    <img src="{$property_value|escape:'htmlall':'UTF-8'}">
                                </span>
                                {else}
                                    <span>{$property_value|escape:'htmlall':'UTF-8'}</span>
                                {/if}
                            </td>
                        {/foreach}
                    {else}
                        {for $j = 0 to ($num_of_columns - 1)}
                            <td class="preview_field_value">
                                <span></span>
                            </td>
                        {/for}
                    {/if}
                </tr>
            {/for}

            </tbody>
        </table>
    </div>
</div>