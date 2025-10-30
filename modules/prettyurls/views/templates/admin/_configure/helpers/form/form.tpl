{*
* Product Quotation
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @author    FMM Modules
* @copyright Copyright 2021 © FMM Modules All right reserved
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @category  front_office_features
* @package   productquotation
*}
{extends file="helpers/form/form.tpl"}
{block name="input"}
{if $input.name == 'pages'}
		<div class="col-lg-12" id="advanceseo_chk_blk">
			{if (empty($store_metas))} 
				<p>{l s='No pages found' mod='prettyurls'}</p>
			{else}
			<ul class="advseo_selector">
				<li><a href="javascript:void(0);" onclick="selectAll();">{l s='Select All' mod='prettyurls'}</a></li>
				<li><a href="javascript:void(0);" onclick="UnSelectAll();">{l s='Unselect All' mod='prettyurls'}</a></li>
			</ul>
			<ul id="advseo_pages_excl">
				{foreach  from=$store_metas item=store_meta}
				<li>
					<input type="checkbox" class="gsitemap_metas" name="gsitemap_meta[]" value="{$store_meta['id_meta']|escape:'htmlall':'UTF-8'}" id="exc_{$store_meta['id_meta']|escape:'htmlall':'UTF-8'}" /> <label for="exc_{$store_meta['id_meta']|escape:'htmlall':'UTF-8'}">{$store_meta['title']|escape:'htmlall':'UTF-8'} [ {$store_meta['page']|escape:'htmlall':'UTF-8'} ]</label>
				</li>
				{/foreach}
			</ul>
			<p class="advseo_warning">{l s='Warning: These pages list only applies if you select All URLs from sitemap to generate.' mod='prettyurls'}</p>
			{/if}
		</div>
	{elseif $input.name == 'opts'}
	<div class="col-lg-6 col-xs-12">
		<ul class="advseo_extra_opts">
			<li><input type="checkbox" id="images_included" name="images_included" value="1" /> <label for="images_included">{l s='Include images also from Server' mod='prettyurls'}</label></li>
			<li><input type="checkbox" id="images_included_cdn" name="images_included_cdn" value="1"{$cdn_use_html|cleanHtml nofilter} /> <label for="images_included_cdn">{l s='Using CDN for images?' mod='prettyurls'}</label></li>
			<li><input type="checkbox" id="images_included_ping_se" name="images_included_ping_se" value="1"{$ping_se_use_html|cleanHtml nofilter} /> <label for="images_included_ping_se">{l s='Ping Search Engines for new sitemap?' mod='prettyurls'}</label></li>
		</ul>
	</div>
	{elseif $input.name == 'cms'}
	<div id="cms-pages">
			<div class="col-lg-9">
		      <table cellspacing="0" cellpadding="0" class="table panel" style="{if $version < 1.6}width:500px;{/if}">
		        <tr>
		        	<td>&nbsp;</td>
		        	<td><b>{l s='ID' mod='prettyurls'}</b></td>
		        	<td><b>{l s='Page Name' mod='prettyurls'}</b></td>
		        </tr>
		        {foreach from=$cms_pages item=page}
			        <tr>
			          <td>
			          	<input type="radio" class="cms_pages" name="cms_pages" id="cms_page_{$page.id_cms|escape:'htmlall':'UTF-8'}" value="{$page.id_cms|escape:'htmlall':'UTF-8'}" {if isset($fields_value.REDIRECT_404) AND isset($fields_value.REDIRECT_404_CMS) AND $fields_value.REDIRECT_404 == 2 AND $page.id_cms == $fields_value.REDIRECT_404_CMS}checked="checked"{/if}/>
			          </td>
			          <td>
			          	{$page.id_cms|escape:'htmlall':'UTF-8'}
			          </td>
			          <td>
			          	<span for="{$page.id_cms|escape:'htmlall':'UTF-8'}" class="t">
			          		{$page.meta_title|escape:'htmlall':'UTF-8'}
			          	</span>
			          </td>
			        </tr>
		        {/foreach}
		      </table>
			  <p class="help-block">{l s='In case you have selected " Redirect 404" as CMS Page in above settings.' mod='prettyurls'}</p>
			</div>
			<div id="fmm_pretty_urlredirects_admin_blk">
					<ul>
						<li>
							<a href="{$manage_link_urlredirects|escape:'htmlall':'UTF-8'}" class="button_pretty" target="_blank">
								<i class="material-icons">mediation</i>
								{l s='Manage URL Redirects' mod='prettyurls'}
							</a>
						</li>
						<li>
							<a href="{$manage_link_urlredirects_404|escape:'htmlall':'UTF-8'}" class="button_pretty" target="_blank">
								<i class="material-icons">report_problem</i>
								{l s='Manage 404 Pages' mod='prettyurls'}
							</a>
						</li>
						<li>
							<a href="{$manage_link_urlredirects_reports|escape:'htmlall':'UTF-8'}" class="button_pretty" target="_blank">
								<i class="material-icons">reorder</i>
								{l s='Manage 404 Reports' mod='prettyurls'}
							</a>
						</li>
					</ul>
			</div>
		</div>
		<div class="clearfix"></div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}