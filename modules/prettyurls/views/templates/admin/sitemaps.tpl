{*
* 2007-2023 PrestaShop
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
*  @author    FMM Modules
*  @copyright 2023 FME Modules
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<div class="panel">
	<div class="panel-heading">
		<i class="material-icons">code</i>
		{l s='Sitemaps' mod='prettyurls'}
	</div>
	<div class="form-wrapper">
		{if (empty($gsitemap_links))} 
			<p>{l s='No sitemaps found' mod='prettyurls'}</p>
		{else}
		<p>{l s='Base Sitemap URL in your Google Webmaster account:' mod='prettyurls'}<br/>
		<a href="{$gsitemap_store_url|escape:'htmlall':'UTF-8'}{$gsitemap_store_url_shopid|escape:'htmlall':'UTF-8'}_index_sitemap.xml" target="_blank">
			<span style="color: #e50b70;">{$gsitemap_store_url|escape:'htmlall':'UTF-8'}{$gsitemap_store_url_shopid|escape:'htmlall':'UTF-8'}_index_sitemap.xml</span></a><br/><br/></p>
		<ul>
			{foreach  from=$gsitemap_links item=gsitemap_link}
			<li><a target="_blank" style="color: #e50b70;" href="{$gsitemap_store_url|escape:'htmlall':'UTF-8'}{$gsitemap_link['link']|escape:'htmlall':'UTF-8'}">{$gsitemap_link['link']|escape:'htmlall':'UTF-8'}</a></li>
			{/foreach}
		</ul>
		<br/>
		<p>{l s='Your last update was made on this date:' mod='prettyurls'} {$gsitemap_last_export|escape:'htmlall':'UTF-8'}</p>
		
		<br/>
		<p style="font-weight:bold;">{l s='You can use Cron Job Link:' mod='prettyurls'}
		<strong style="color:#e50b70">http://yourdomain.com/?fc=module&module=prettyurls&controller=cron&token={$advanceseo_token|escape:'htmlall':'UTF-8'}</strong></p>
		{/if}
		<div class="alert alert-info">{l s='Please read this' mod='prettyurls'}
		<a href="{$gsitemap_store_url|escape:'htmlall':'UTF-8'}modules/prettyurls/readme_en.pdf" target="_blank">{l s='USER GUIDE' mod='prettyurls'}</a> {l s='if you are having any difficulties.' mod='prettyurls'}</div>
	</div>
</div>