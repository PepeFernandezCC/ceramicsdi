{*
*
* Google merchant center Pro
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}
{if !empty($aErrors)}
	{include file="`$sErrorInclude`"}
{/if}
<table class="table">
	<tr class="bt_tr_header text-center">
		<th class="center">{l s='Google taxonomy code' mod='gmerchantcenterpro'}</th>
		<th class="center">{l s='Taxonomy file' mod='gmerchantcenterpro'}</th>
		<th class="center">{l s='Concerned countries' mod='gmerchantcenterpro'}</th>
		<th class="center">{l s='Match my categories' mod='gmerchantcenterpro'}</th>
		<th class="center">{l s='Synchronise from Google' mod='gmerchantcenterpro'}</th>
	</tr>
	{foreach from=$aCountryTaxonomies name=taxonomy key=sCode item=aTaxonomy}
		<tr>
			<td class="center">{$sCode|escape:'htmlall':'UTF-8'}</td>
			<td class="center"><a class="btn btn-sm btn-primary" target="_blank" href="https://www.google.com/basepages/producttype/taxonomy.{$sCode|escape:'htmlall':'UTF-8'}.txt"><i class="fa fa-file"></i> </a> </td>
			<td class="center">{$aTaxonomy.countryList|escape:'htmlall':'UTF-8'}</td>
			{if !empty($aTaxonomy.updated)}
				<td id="gcupd_{$sCode|escape:'htmlall':'UTF-8'}" class="center">
				<a href="{$taxonomyController|escape:'htmlall':'UTF-8'}&iLangId={$aTaxonomy.id_lang|escape:'htmlall':'UTF-8'}&sLangIso={$sCode|escape:'htmlall':'UTF-8'}" class="btn btn-info btn-lg"><span class="icon-pencil"></span></a>
				</td>
			{else}
				<td class="center text-warning" id="gcupd_{$sCode|escape:'htmlall':'UTF-8'}">{l s='Please synchronise first, click there -->' mod='gmerchantcenterpro'}</td>
			{/if}
			<td class="center">
				<a class="btn btn-sm btn-default" id="updateGoogleCategories" href="#" onclick="$('#loadingGoogleCatListDiv').show();oGmcPro.hide('bt_google-cat-list');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.googleCatSync.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.googleCatSync.type|escape:'htmlall':'UTF-8'}&iLangId={$aTaxonomy.id_lang|escape:'htmlall':'UTF-8'}&sLangIso={$sCode|escape:'htmlall':'UTF-8'}', 'bt_google-cat-list', 'bt_google-cat-list', null, null, 'loadingGoogleCatListDiv');"><i class="icon-refresh"></i></a>
				{if !empty($aTaxonomy.currentUpdated)}<i class="text-success fa fa-2x fa-check-circle-o"></i>{/if}
			</td>
		</tr>
	{/foreach}
</table>