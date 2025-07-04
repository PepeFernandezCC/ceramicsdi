{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<div class="panel ybc-blog-panel">
    <div class="panel-heading">{$title|escape:'html':'UTF-8'}
        {if isset($totalRecords) && $totalRecords}<span class="badge">{$totalRecords|intval}</span>{/if}
        <span class="panel-heading-action">
            {if in_array('add_new',$blog_config.YBC_BLOG_AUTHOR_PRIVILEGES) && $show_add_new}            
                <span class="add_new_post_blog">
                    <a href="{$link_addnew|escape:'html':'UTF-8'}" data-placement="top" data-html="true" data-original-title="{l s='Add new' mod='ybc_blog'}" class="label-tooltip" data-toggle="tooltip" title="">
        				<i class="process-icon-new"></i>
                        {l s='Add new' mod='ybc_blog'}
                    </a>
                </span>            
            {/if}
        </span>
    </div>
    {if $fields_list}
        <div class="table-responsive clearfix">
            <form method="post" action="{$currentIndex|escape:'html':'UTF-8'}">
                <table class="table configuration">
                    <thead>
                        <tr class="nodrag nodrop">
                            {foreach from=$fields_list item='field' key='index'}
                                <th class="{$index|escape:'html':'UTF-8'}">
                                    <span class="title_box">
                                        {$field.title|escape:'html':'UTF-8'}
                                        {if isset($field.sort) && $field.sort}
                                            <a href="{$field.sort|escape:'html':'UTF-8'}{$filter_params nofilter}" {if isset($sort)&& $sort==$index && isset($sort_type) && $sort_type=='asc'} class="active"{/if}><i class="fa fa-caret-up"></i></a>
                                            <a href="{$field.sort_desc|escape:'html':'UTF-8'}{$filter_params nofilter}" {if isset($sort)&& $sort==$index && isset($sort_type) && $sort_type=='desc'} class="active"{/if}><i class="fa fa-caret-down"></i></a>
                                        {/if}
                                    </span>
                                </th>                            
                            {/foreach}
                            {if $show_action}
                                <th style="text-align: center;">{l s='Action' mod='ybc_blog'}</th>
                            {/if}
                        </tr>
                        {if $show_toolbar}
                            <tr class="nodrag nodrop filter row_hover">
                                {foreach from=$fields_list item='field' key='index'}
                                    <th class="{$index|escape:'html':'UTF-8'}">
                                        {if isset($field.filter) && $field.filter}
                                            {if $field.type=='text'}
                                                <input class="filter" name="{$index|escape:'html':'UTF-8'}" type="text" {if isset($field.width)}style="width: {$field.width|intval}px;"{/if} {if isset($field.active)}value="{$field.active|escape:'html':'UTF-8'}"{/if}/>
                                            {/if}
                                            {if $field.type=='select' || $field.type=='active'}
                                                <select  {if isset($field.width)}style="width: {$field.width|intval}px;"{/if}  name="{$index|escape:'html':'UTF-8'}">
                                                    <option value=""> -- </option>
                                                    {if isset($field.filter_list.list) && $field.filter_list.list}
                                                        {assign var='id_option' value=$field.filter_list.id_option}
                                                        {assign var='value' value=$field.filter_list.value}
                                                        {foreach from=$field.filter_list.list item='option'}
                                                            <option {if $field.active!=='' && $field.active==$option.$id_option} selected="selected" {/if} value="{$option.$id_option|escape:'html':'UTF-8'}">{$option.$value|escape:'html':'UTF-8'}</option>
                                                        {/foreach}
                                                    {/if}
                                                </select>                                            
                                            {/if}
                                        {else}
                                           {l s=' -- ' mod='ybc_blog'}
                                        {/if}
                                    </th>
                                {/foreach}
                                {if $show_action}
                                    <th class="actions">
                                        <span class="pull-right">
                                            <input type="hidden" name="post_filter" value="yes" />
                                            {if $show_reset}<a  class="btn btn-warning"  href="{$currentIndex|escape:'html':'UTF-8'}"><i class="icon-eraser"></i> {l s='Reset' mod='ybc_blog'}</a>{/if}
                                            <button class="btn btn-default" name="ybc_submit_{$name|escape:'html':'UTF-8'}" id="ybc_submit_{$name|escape:'html':'UTF-8'}" type="submit">
            									<i class="fa fa-search"></i> {l s='Filter' mod='ybc_blog'}
            								</button>
                                        </span>
                                    </th>
                                {/if}
                            </tr>
                        {/if}
                    </thead>
                    <tbody>
                        {foreach from=$field_values item='row'}
                            <tr>
                                {foreach from=$fields_list item='field' key='key'}                                
                                    <td class="pointer {$key|escape:'html':'UTF-8'}">
                                        {if isset($field.rating_field) && $field.rating_field}
                                            {if isset($row.$key) && $row.$key > 0}
                                                {for $i=1 to (int)$row.$key}
                                                    <div class="star star_on"></div>
                                                {/for}
                                                {if (int)$row.$key < 5}
                                                    {for $i=(int)$row.$key+1 to 5}
                                                        <div class="star"></div>
                                                    {/for}
                                                {/if}
                                            {else}
                                                {l s=' -- ' mod='ybc_blog'}
                                            {/if}
                                        {elseif $field.type != 'active'}
                                            {if isset($row.$key) && !is_array($row.$key)}{if isset($field.strip_tag) && !$field.strip_tag}{$row.$key nofilter}{else}{$row.$key|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}{/if}{/if}
                                            {if isset($row.$key) && is_array($row.$key) && isset($row.$key.image_field) && $row.$key.image_field}
                                                <a class="ybc_fancy" href="{$row.$key.img_url|escape:'html':'UTF-8'}"><img style="{if isset($row.$key.height) && $row.$key.height}max-height: {$row.$key.height|intval}px;{/if}{if isset($row.$key.width) && $row.$key.width}max-width: {$row.$key.width|intval}px;{/if}" src="{$row.$key.img_url|escape:'html':'UTF-8'}" /></a>
                                            {/if} 
                                            {if $key=='subject' && isset($row.comment)}
                                            <div class="content-comment">
                                                {$row.comment|strip_tags:'UTF-8'|truncate:500:'...'|escape:'html':'UTF-8'}
                                            </div>
                                            {/if}                                       
                                        {else}                                            
                                            {if isset($row.$key) && $row.$key}
                                                {if $row.$key==-1}
                                                    <span><i class="fa fa-clock-o"></i></span>
                                                {else}
                                                    {if isset($row.edit_approved) && $row.edit_approved}
                                                        <a href="{$row.edit_approved|escape:'html':'UTF-8'}" title="{l s='Click to unapprove' mod='ybc_blog'}">
                                                    {/if}
                                                        <span {if !isset($row.edit_approved) || !$row.edit_approved }title="{l s='Approved' mod='ybc_blog'}"{/if}><i class="fa fa-check"></i></span>
                                                    {if isset($row.edit_approved) && $row.edit_approved}
                                                        </a>
                                                    {/if}
                                                {/if}
                                            {else}
                                                {if isset($row.edit_approved) && $row.edit_approved}
                                                    <a href="{$row.edit_approved|escape:'html':'UTF-8'}" title="{l s='Click to mark as approved' mod='ybc_blog'}">
                                                {/if}
                                                    <span {if !isset($row.edit_approved)}title="{l s='Unapproved' mod='ybc_blog'}"{/if}><i class="fa fa-remove"></i></span>
                                                {if isset($row.edit_approved) && $row.edit_approved}
                                                    </a>
                                                {/if}
                                            {/if}
                                        {/if}
                                    </td>
                                {/foreach}
                                {if $show_action}
                                    <td class="text-right">                                
                                            <div class="btn-group-action">
                                                <div class="btn-group pull-right">
                                                    {if isset($row.child_view_url) && $row.child_view_url}
                                                        <a class="btn btn-default" href="{$row.child_view_url|escape:'html':'UTF-8'}" title="{l s='View' mod='ybc_blog'}"><i class="fa fa-search-plus"></i> </a>
                                                    {else}
                                                        <a href="{$row.view_url|escape:'html':'UTF-8'}" title="{if isset($row.view_text) && $row.view_text} {$row.view_text|escape:'html':'UTF-8'}{else} {l s='Preview' mod='ybc_blog'}{/if}"><i class="fa fa-search-plus"></i></a>
                                                    {/if}
                                                    {if (isset($row.edit_url) && $row.edit_url)|| (isset($row.delete_url) && $row.delete_url)}
                                                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                                    						<i class="fa fa-caret-down"></i>
                                    					</button>
                                                        <ul class="dropdown-menu">
                                                            {if isset($row.child_view_url) && $row.child_view_url}
                                                                <a href="{$row.view_url|escape:'html':'UTF-8'}"><i class="fa fa-search-plus"></i> {if isset($row.view_text) && $row.view_text} {$row.view_text|escape:'html':'UTF-8'}{else} {l s='Preview' mod='ybc_blog'}{/if}</a>
                                                            {/if}
                                                            {if isset($row.edit_url) && $row.edit_url}
                                                                <li><a class="" href="{$row.edit_url|escape:'html':'UTF-8'}"><i class="fa fa-pencil"></i> {l s='Edit' mod='ybc_blog'}</a></li>
                                                                <li class="divider"></li>
                                                            {/if}
                                                            {if isset($row.delete_url) && $row.delete_url}
                                                                <li><a onclick="return confirm('{l s='Do you want to delete this item?' mod='ybc_blog'}');" class="" href="{$row.delete_url|escape:'html':'UTF-8'}"><i class="fa fa-trash"></i> {l s='Delete' mod='ybc_blog'}</a></li>
                                                            {/if}
                                                        </ul>
                                                    {/if}
                                                </div>
                                            </div>
                                     </td>
                                {/if}
                            </tr>
                        {/foreach}                    
                    </tbody>
                </table>
                {if $paggination}
                    <div class="ybc_paggination" style="margin-top: 10px;">
                        {$paggination nofilter}
                    </div>
                {/if}
            </form>
        </div>
    {/if}
</div>