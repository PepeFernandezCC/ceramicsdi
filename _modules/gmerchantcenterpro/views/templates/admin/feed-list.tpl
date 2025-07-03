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
<div class="bootstrap" id="gmcp">
	<form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_feedlist-{$sDisplay|escape:'htmlall':'UTF-8'}-form" name="bt_feedlist-{$sDisplay|escape:'htmlall':'UTF-8'}-form" {if $useJs == true}onsubmit="javascript: oGmcPro.form('bt_feedlist-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-list-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-list-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, null, 'FeedList{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedListDiv');return false;" {/if}>
		<input type="hidden" name="sAction" value="{$aQueryParams.feedListUpdate.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.feedListUpdate.type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sDisplay" id="sFeedListDisplay" value="{if !empty($sDisplay)}{$sDisplay|escape:'htmlall':'UTF-8'}{else}data{/if}" />

		{* BEGIN - classic product data feed *}
		{if !empty($sDisplay) && $sDisplay == 'data'}
			<h3 class="subtitle"><i class="fa fa-book"></i>&nbsp;{l s='Products data feed' mod='gmerchantcenterpro'}</h3>
			<div class="clr_10"></div>
			{if !empty($bUpdate)}
				{include file="`$sConfirmInclude`"}
			{elseif !empty($aErrors)}
				{include file="`$sErrorInclude`"}
			{/if}

			{if !empty($sGmcLink)}
				{if !empty($iTotalProductToExport)}
					{literal}
						<script type="text/javascript">
							var aDataFeedGenOptions = {
								'sURI' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
								'sParams' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.dataFeed.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.dataFeed.type|escape:'htmlall':'UTF-8'}{literal}',
								'iShopId' : {/literal}{$iShopId|escape:'htmlall':'UTF-8'}{literal},
								'sFilename': '',
								'iLangId': 0,
								'sLangIso': '',
								'sCountryIso': '',
								'sCurrencyIso': '',
								'iStep': 0,
								'iTotal' : {/literal}{$iTotalProductToExport|escape:'htmlall':'UTF-8'}{literal},
								'iProcess': 0,
								'sDisplayedCounter': '#regen_counter',
								'sDisplayedBlock': '#syncCounterDiv',
								'sDisplaySuccess': '#regen_xml',
								'sDisplayTotal': '#total_product_processed',
								'sLoaderBar': 'myBar',
								'sErrorContainer': 'AjaxFeed',
								'bReporting': 1,
								'sFeedType': 'product',
								'sDisplayReporting': '#handleGenerateReportingBox',
								'sResultText' : '{/literal}{l s='product(s) exported' mod='gmerchantcenterpro'}{literal}',
								'bExcludedProduct' : '{/literal}{$bExcludedProduct|escape:'htmlall':'UTF-8'}{literal}'
							};
						</script>
					{/literal}

					{* USE CASE - AVAILABLE FEED FILE LIST *}
					{if !empty($aFeedFileListProduct)}

						<div class="row">
							<div class="col-xs-12">
								<div class="alert alert-info">
									{l s='In this tab you will find the URLs of your product data feeds to be entered into your Google Merchant Center account, depending on the retrieval method you choose. To know how to import your products into your Google Merchant Center account, don\'t hesitate to read' mod='gmerchantcenterpro'}&nbsp;<a class="badge badge-info" target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}faq.php?id=94&lg={$sFaqLang|escape:'htmlall':'UTF-8'}"><i class="icon icon-book"></i>&nbsp;{l s='our FAQ' mod='gmerchantcenterpro'}</a>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
								<div class="jumbotron">
									{if $iTotalProduct <= 30000}
										<span class="badge badge-pill badge-warning float-left p-2 mr-5">{l s='Recommended' mod='gmerchantcenterpro'}</span>
									{/if}
									<h1 class="display-4">{l s='ON THE FLY OUTPUT' mod='gmerchantcenterpro'}</h1>
									<p class="lead w-75">{l s='This export method is recommended for catalogs with less than about 30000 products' mod='gmerchantcenterpro'}</p>
									<p class="lead text-center">
										<a id="btn-fly-product" class="btn btn-lg btn-primary w-25 mt-2 py-3">
											{l s='Use this solution' mod='gmerchantcenterpro'}
										</a>
									</p>
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
								<div class="jumbotron">
									{if $iTotalProduct > 30000}
										<span class="badge badge-pill badge-warning float-left p-2 mr-5">{l s='Recommended' mod='gmerchantcenterpro'}</span>
									{/if}
									<h1 class="display-4">{l s='XML + CRON' mod='gmerchantcenterpro'}</h1>
									<p class="lead w-75">{l s='This export method is recommended for catalogs with more than about 30000 products' mod='gmerchantcenterpro'}</p>
									<p class="lead text-center">
										<a id="btn-xml-product" class="btn btn-lg w-25 btn-primary mt-2 py-3">
											{l s='Use this solution' mod='gmerchantcenterpro'}
										</a>

									</p>
								</div>
							</div>
						</div>

						<div class="clr_50"></div>

						<div class="bt-fb-cron-product" style="display: none;">
							<ul class="nav nav-tabs" id="myTab">
								<li class="active">
									<a data-toggle="tab" href="#xml"><i class="fa fa-file-code-o"></i>&nbsp;{l s='Your XML files' mod='gmerchantcenterpro'}</a>
								</li>
								<li class="nav-item">
									<a data-toggle="tab" href="#cron"><i class="fa fa-server"></i>&nbsp;{l s='Your CRON URL\'s' mod='gmerchantcenterpro'}</a>
								</li>
							</ul>
							<div class="tab-content" id="myTabContent">
								{*Start first tab*}
								<div class="tab-pane active" id="xml">
									<div class="clr_50"></div>
									<div id="syncCounterDiv" style="display: none;" class="alert alert-success">
										<button type="button" class="close" onclick="$('#syncCounterDiv').hide();">×</button>
										<div class="row mb-3 h3">
											{l s='Export in progress' mod='gmerchantcenterpro'}
										</div>
										<hr />
										<div class="row">
											<b>{l s='Number of products exported:' mod='gmerchantcenterpro'}</b>&nbsp;
											<input size="5" name="bt_regen-counter" id="regen_counter" value="0" disabled />&nbsp;
											{l s='out of' mod='gmerchantcenterpro'}&nbsp;{$iTotalProduct|escape:'htmlall':'UTF-8'} ({l s='(total number of products in the shop)' mod='gmerchantcenterpro'})
										</div>
										<div class="row mt-2">
											<div class="mt-2"></div>
											<div class="progress col-xs-12" style="height: 20px;">
												<div class="progress-bar bg-success progress-bar-striped active" id="myBar"></div>
											</div>
										</div>
										<div class="row">
											<div id="{$sModuleName|escape:'htmlall':'UTF-8'}AjaxFeedError"></div>
										</div>
										<div class="clr_20"></div>
									</div>

									<table border="0" cellpadding="2" cellspacing="2" class="table">
										<tr class="bt_tr_header text-center">
											{*<th class="center col-xs-1">{l s='Regenerate during CRON' mod='gmerchantcenterpro'}</th>*}
											<th class="center">{l s='Country' mod='gmerchantcenterpro'}</th>
											<th class="center">{l s='Language' mod='gmerchantcenterpro'}</th>
											<th class="center">{l s='Taxonomy' mod='gmerchantcenterpro'}</th>
											<th class="center">{l s='Currency' mod='gmerchantcenterpro'}</th>
											<th class="center">{l s='Last update' mod='gmerchantcenterpro'}</th>
											<th class="center">{l s='Action' mod='gmerchantcenterpro'}</th>
										</tr>
										{foreach from=$aFeedFileListProduct name=feed key=iKey item=aFeed}
											<tr id="regen_xml_{$aFeed.lang|lower|escape:'htmlall':'UTF-8'}_{$aFeed.country|lower|escape:'htmlall':'UTF-8'}">
												{*<td class="center"><input type="checkbox" class="bt_export_feed" name="bt_cron-export[]" value="{$aFeed.lang|lower|escape:'htmlall':'UTF-8'}_{$aFeed.country|escape:'htmlall':'UTF-8'}_{$aFeed.currencyIso|escape:'htmlall':'UTF-8'}" {if !empty($aFeed.checked)}checked="checked" {/if} /></td>*}
												<td class="center">{$aFeed.countryName|escape:'htmlall':'UTF-8'} - {$aFeed.country|escape:'htmlall':'UTF-8'}</td>
												<td class="center">
													{$aFeed.langName|escape:'htmlall':'UTF-8'}
													{if empty($aFeed.is_default)}
														<span class="badge badge-sm badge-info ml-2">{l s='Custom feed' mod='gmerchantcenterpro'}</span>
													{/if}
												</td>
												<td class="center">{$aFeed.taxonomy|escape:'htmlall':'UTF-8'}</td>
												<td class="center">{$aFeed.currencySign|escape:'htmlall':'UTF-8'} - {$aFeed.currencyIso|escape:'htmlall':'UTF-8'}</td>
												<td class="center">{$aFeed.filemtime|escape:'htmlall':'UTF-8'}</td>
												<td class="center">
													<a class="label-tooltip btn btn-sm btn-default" title="{l s='Generate' mod='gmerchantcenterpro'}" href="javascript:void(0);" class="regenXML"
														onclick="if (oGmcPro.bGenerateXmlFlag){literal}{{/literal}alert('{l s='Your data feed is being created...' mod='gmerchantcenterpro'}'); return false;{literal}}{/literal}aDataFeedGenOptions.sLangIso='{$aFeed.lang|lower|escape:'htmlall':'UTF-8'}';aDataFeedGenOptions.sCountryIso='{$aFeed.country|lower|escape:'htmlall':'UTF-8'}';aDataFeedGenOptions.sCurrencyIso='{$aFeed.currencyIso|escape:'htmlall':'UTF-8'}';aDataFeedGenOptions.iLangId='{$aFeed.langId|escape:'htmlall':'UTF-8'}';aDataFeedGenOptions.sFilename='{$aFeed.filename|escape:'htmlall':'UTF-8'}';aDataFeedGenOptions.sFeedType='product';$('#syncCounterDiv').show();oGmcPro.generateDataFeed(aDataFeedGenOptions);"><span
															class="icon-refresh"></span></a>&nbsp;<div id="total_product_processed_{$aFeed.lang|lower|escape:'htmlall':'UTF-8'}_{$aFeed.country|lower|escape:'htmlall':'UTF-8'}" style="font-style: bold; display: none; margin-left:20px; vertical-align:text-top;"></div>
													<a class="label-tooltip btn btn-default btn-md" title="{l s='See' mod='gmerchantcenterpro'}" target="_blank" href="{$aFeed.link|escape:'htmlall':'UTF-8'}"><i class="fa fa-eye"></i></a>
													<a type="button" href="{$aFeed.link|escape:'htmlall':'UTF-8'}" download class="label-tooltip btn btn-md btn-default" title="{l s='Download' mod='gmerchantcenterpro'}">&nbsp;<i class="fa fa-download"></i></a>
													<a type="button" class="label-tooltip btn btn-md btn-default btn-copy js-tooltip js-copy" title="{l s='Copy URL' mod='gmerchantcenterpro'}" data-toggle="tooltip" data-placement="bottom" data-copy="{$aFeed.link|escape:'htmlall':'UTF-8'}">&nbsp;<i class="fa fa-copy"></i></a>
													<a style="display:none;" href="#theModal-{$aFeed.full|escape:'htmlall':'UTF-8'}" id="reporting-data-{$aFeed.full|escape:'htmlall':'UTF-8'}" onclick="oGmcPro.cleanModal('#theModal-{$aFeed.full|escape:'htmlall':'UTF-8'}')" class="nav-link" data-remote="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.reportingBox.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.reportingBox.type|escape:'htmlall':'UTF-8'}&lang={$aFeed.full|escape:'htmlall':'UTF-8'}" data-toggle="modal" data-target="#theModal-{$aFeed.full|escape:'htmlall':'UTF-8'}"><i class="fa fa-file fa-2x"></i></a>

													{if empty($aFeed.is_default)}
														<a href="#"><i class="icon-trash btn btn-mini btn-danger" title="{l s='Delete' mod='gmerchantcenterpro'}" onclick="check = confirm('{l s='Are you sure you want to delete this data feed?' mod='gmerchantcenterpro'} {l s='It will be definitely removed from your database' mod='gmerchantcenterpro'}');if(!check)return false;$('#loadingFeedListDiv').show();oGmcPro.hide('bt_rules');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.deleteFeed.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.deleteFeed.type|escape:'htmlall':'UTF-8'}&export_mode=xml&id_feed={$aFeed.id_feed|escape:'htmlall':'UTF-8'}', 'bt_feed-list-settings', 'bt_feed-list-settings', null, null, 'loadingFeedListDiv');"></i></a>
													{/if}

													<div class="modal fade" id="theModal-{$aFeed.full|escape:'htmlall':'UTF-8'}" tabindex="-1" role="dialog">
														<div class="modal-dialog modal-lg" style="width:80%;" role="document">
															<div class="modal-content">
																<div class="modal-header"></div>
																<div class="modal-body">
																	<div class="alert alert-info">
																		<p style="text-align: center !important;"><img src="{$sLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
																		<div class="clr_20"></div>
																		<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='gmerchantcenterpro'}</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</td>
											</tr>
										{/foreach}
									</table>

									<div class="clr_10"></div>

									<div class="navbar navbar-default navbar-fixed-bottom text-center">
										<div class="col-xs-12">
											<button class="btn btn-submit" onclick="oGmcPro.form('bt_feedlist-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-list-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-list-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, null, 'FeedList{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedListDiv');return false;"></i>{l s='Save' mod='gmerchantcenterpro'}</button>
										</div>
									</div>
								</div>
								{*start 2nd tab*}
								<div class="tab-pane fade" id="cron" role="tabpanel">
									<div class="clr_20"></div>
									<div class="alert alert-info form-group">
										<p>{l s='Please follow our FAQ to know' mod='gmerchantcenterpro'}&nbsp;&nbsp;<a class="badge badge-info" target="_blank" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/217"><i class="icon icon-link"></i>&nbsp;{l s='how to create a CRON task' mod='gmerchantcenterpro'}</a></p>
										<p><b>{l s='Be careful:' mod='gmerchantcenterpro'}</b>&nbsp;{l s='schedule your CRON task so that the XML files are up to date when Google will retreive them to update your data in Google Shopping.' mod='gmerchantcenterpro'}</p>
									</div>

									<div class="clr_15"></div>

									{if !empty($aCronListProduct)}
										<table border="0" cellpadding="2" cellspacing="2" class="table">
											<tr class="bt_tr_header text-center">
												<th class="center">{l s='Language' mod='gmerchantcenterpro'}</th>
												<th class="center">{l s='Country' mod='gmerchantcenterpro'}</th>
												<th class="center">{l s='Currency' mod='gmerchantcenterpro'}</th>
												<th class="center">{l s='Taxonomy' mod='gmerchantcenterpro'}</th>
												<th class="center">{l s='Action' mod='gmerchantcenterpro'}</th>
											</tr>
											{foreach from=$aCronListProduct name=feed key=iKey item=aCronFeed}
												<tr>
													<td class="center">{$aCronFeed.langName|escape:'htmlall':'UTF-8'}</td>
													<td class="center">{$aCronFeed.countryName|escape:'htmlall':'UTF-8'} - {$aCronFeed.country|escape:'htmlall':'UTF-8'}</td>
													<td class="center">{$aCronFeed.currencySign|escape:'htmlall':'UTF-8'} - {$aCronFeed.currencyIsoCron|escape:'htmlall':'UTF-8'}</td>
													<td class="center">{$aCronFeed.taxonomy|escape:'htmlall':'UTF-8'}</td>
													<td class="center">
														<a type="button" class="label-tooltip btn btn-md btn-default btn-copy js-tooltip js-copy" title="{l s='Copy' mod='gmerchantcenterpro'}" data-toggle="tooltip" data-placement="bottom" data-copy="{$aCronFeed.link|escape:'htmlall':'UTF-8'}">&nbsp;<i class="fa fa-copy"></i></a>
														<a class="label-tooltip btn btn-default btn-md" target="_blank" title="{l s='Execute' mod='gmerchantcenterpro'}" href="{$aCronFeed.link|escape:'htmlall':'UTF-8'}"><i class="fa fa-play-circle"></i></a>
													</td>
												</tr>
											{/foreach}
										</table>
									{/if}

								</div>
							</div>
						</div>
						{* USE CASE - NO AVAILABLE LANGUAGE : CURRENCY : COUNTRY *}
					{else}
						<div class="alert alert-warning">
							{l s='Either you just updated your configuration by deactivating the advanced file security feature (in which case, please reload the page), or, there are no file because of no valid languages / currencies / countries, according to the Google\'s requirements.' mod='gmerchantcenterpro'}
							<b><a target="_blank" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/52&lg={$sCurrentIso|escape:'htmlall':'UTF-8'}">{l s='See our FAQ about localization prerequisites.' mod='gmerchantcenterpro'}</a></b>
						</div>
					{/if}

					<div class="bt-fb-fly-product" style="display: none;">
						{* USE CASE - AVAILABLE FEED FILE LIST *}
						{if !empty($aFlyFileListProduct)}
							<p class="alert alert-info form-group">
								{l s='Please follow our FAQ to know' mod='gmerchantcenterpro'}&nbsp;&nbsp;<a class="badge badge-info" target="_blank" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/30#bt_fly"><i class="icon icon-link"></i>&nbsp;{l s='how to manage the on-the-fly output URL\'s' mod='gmerchantcenterpro'}</a>
								<br />
								<br />
								{l s='You can use the "on-the-fly output" URL\'s if your catalog is relatively small (30000 products maximum), if not, choose the solution of setting up a CRON task. However, if you are on a dedicated server, this one may also be able to process larger catalogs if you increase its PHP time-out and memory usage limits.' mod='gmerchantcenterpro'}
							</p>
							<div class="clr_5"></div>

							<table border="0" cellpadding="2" cellspacing="2" class="table ">
								<tr class="bt_tr_header text-center">
									<th class="center">{l s='Country' mod='gmerchantcenterpro'}</th>
									<th class="center">{l s='Language ' mod='gmerchantcenterpro'}</th>
									<th class="center">{l s='Currency' mod='gmerchantcenterpro'}</th>
									<th class="center">{l s='Taxonomy' mod='gmerchantcenterpro'}</th>
									<th class="center"></th>
								</tr>
								{foreach from=$aFlyFileListProduct name=feed key=iKey item=aFlyFeed}
									<tr>
										<td class="center">{$aFlyFeed.countryName|escape:'htmlall':'UTF-8'} - {$aFlyFeed.countryIso|escape:'htmlall':'UTF-8'}</td>
										<td class="center">
											{$aFlyFeed.langName|escape:'htmlall':'UTF-8'} - {$aFlyFeed.iso_code|escape:'htmlall':'UTF-8'}
											{if empty($aFlyFeed.is_default)}
												<span class="badge badge-sm badge-info ml-2">{l s='Custom feed' mod='gmerchantcenterpro'}</span>
											{/if}
										</td>
										<td class="center">{$aFlyFeed.currencySign|escape:'htmlall':'UTF-8'} - {$aFlyFeed.currencyIso|escape:'htmlall':'UTF-8'}</td>
										<td class="center">{$aFlyFeed.taxonomy|escape:'htmlall':'UTF-8'}</td>
										<td class="center">
											<a class="label-tooltip btn btn-default btn-md" title="{l s='See' mod='gmerchantcenterpro'}" target="_blank" href="{$aFlyFeed.link|escape:'htmlall':'UTF-8'}"><i class="fa fa-eye"></i></a>
											<a type="button" class="label-tooltip btn btn-md btn-default btn-copy js-tooltip js-copy" title="{l s='Copy' mod='gmerchantcenterpro'}" data-toggle="tooltip" data-placement="bottom" data-copy="{$aFlyFeed.link|escape:'htmlall':'UTF-8'}">&nbsp;<i class="fa fa-copy"></i></a>

											{if empty($aFlyFeed.is_default)}
												<a href="#"><i class="icon-trash btn btn-mini btn-danger" title="{l s='Delete' mod='gmerchantcenterpro'}" onclick="check = confirm('{l s='Are you sure you want to delete this data feed?' mod='gmerchantcenterpro'} {l s='It will be definitely removed from your database' mod='gmerchantcenterpro'}');if(!check)return false;$('#loadingFeedListDiv').show();oGmcPro.hide('bt_feed-list-settings');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.deleteFeed.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.deleteFeed.type|escape:'htmlall':'UTF-8'}&export_mode=fly&id_feed={$aFlyFeed.id_feed|escape:'htmlall':'UTF-8'}', 'bt_feed-list-settings', 'bt_feed-list-settings', null, null, 'loadingFeedListDiv');"></i></a>
											{/if}

										</td>
									</tr>
								{/foreach}
							</table>
							{* USE CASE - NO AVAILABLE LANGUAGE : CURRENCY : COUNTRY *}
						{else}
							<div class="alert alert-warning">
								{l s='There are no files because of no valid languages / currencies / countries according to the Google\'s requirements.' mod='gmerchantcenterpro'}
								<b><a target="_blank" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/52&lg={$sCurrentIso|escape:'htmlall':'UTF-8'}">{l s='See our FAQ about localization prerequisites.' mod='gmerchantcenterpro'}</a></b>
							</div>
						{/if}
					</div>
					{* USE CASE - NO CATEGORY OR BRAND HAVE BEEN SELECTED *}
				{else}
					<div class="clr_15"></div>
					<div class="alert alert-warning">
						{l s='No category or brand have been selected : please go to "Product feed management -> Export method" tab, and select at least one category (or brand). You also need to make sure that there is at least one product in each selected category (or brand). Remember : the categories used here are the products DEFAULT categories.' mod='gmerchantcenterpro'}
					</div>
				{/if}
				{* USE CASE - NO GOOGLE LINK HAS BEEN FILLED OUT *}
			{else}
				<div class="clr_15"></div>

				<div class="alert alert-warning">
					{l s='You must first update the module\'s configuration options before the files can be accessed.' mod='gmerchantcenterpro'}
				</div>
			{/if}
		{/if}
		{* END - classic product data feed *}

		{* BEGIN - promo product data feed *}
		{if !empty($sDisplay) && $sDisplay == 'promo'}
			<h3 class="subtitle"><i class="fa fa-bookmark-o"></i>&nbsp; {l s='Special offers data feed' mod='gmerchantcenterpro'}</h3>
			{* USE CASE - AVAILABLE FEED FILE LIST *}
			{if !empty($aFlyFileListDiscount)}
				<div class="clr_10"></div>
				<div class="alert alert-info form-group">
					{l s='Please follow our FAQ to know' mod='gmerchantcenterpro'}&nbsp;&nbsp;<a class="badge badge-info" target="_blank" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/110"><i class="icon icon-link"></i>&nbsp;{l s='how to configure your special offers feed' mod='gmerchantcenterpro'}</a>
				</div>

				<table border="0" cellpadding="2" cellspacing="2" class="table ">
					<tr class="bt_tr_header text-center">
						<th class="center">{l s='Language' mod='gmerchantcenterpro'}</th>
						<th class="center">{l s='Country' mod='gmerchantcenterpro'}</th>
						<th class="center"></th>
					</tr>
					{foreach from=$aFlyFileListDiscount name=feed key=iKey item=aFlyFeed}
						<tr>
							<td class="center">{$aFlyFeed.langName|escape:'htmlall':'UTF-8'} - {$aFlyFeed.iso_code|escape:'htmlall':'UTF-8'}</td>
							<td class="center">{$aFlyFeed.countryName|escape:'htmlall':'UTF-8'} - {$aFlyFeed.countryIso|escape:'htmlall':'UTF-8'}</td>
							<td class="center">
								<a class="btn btn-default btn-md" target="_blank" href="{$aFlyFeed.link|escape:'htmlall':'UTF-8'}"><i class="fa fa-eye"></i></a>
								<a type="button" class="btn btn-md btn-default btn-copy js-tooltip js-copy" data-toggle="tooltip" data-placement="bottom" data-copy="{$aFlyFeed.link|escape:'htmlall':'UTF-8'}">&nbsp;<i class="fa fa-copy"></i></a>
							</td>
						</tr>
					{/foreach}
				</table>
				{* USE CASE - NO AVAILABLE LANGUAGE : CURRENCY : COUNTRY *}
			{else}
				<div class="alert alert-warning">
					{l s='There are no files because of no valid languages / currencies / countries according to the Google\'s requirements.' mod='gmerchantcenterpro'}
					<b><a target="_blank" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/52">{l s='See our FAQ about localization prerequisites.' mod='gmerchantcenterpro'}</a></b>
					<div class="clr_10"></div>
					<h3 class="subtitle"><i class="icon-globe"></i>&nbsp;{l s='Locale prerequisites'  mod='gmerchantcenterpro'}</h3>
					<div class="alert alert-info">
						<strong class="highlight_element">
							{l s='Please note: a feed for a country will be generated if the country\'s language and official currency are installed and active on your shop, and if the country is part of those where Google Shopping is implemented. For more information, please read the' mod='gmerchantcenterpro'}&nbsp;<a href="https://support.google.com/merchants/answer/160637?hl={$sCurrentIso|escape:'htmlall':'UTF-8'}&visit_id=1-636342381361070010-4017773094&rd=1" target="_blank">{l s='Google official documentation.' mod='gmerchantcenterpro'}</a>
							<br>
						</strong>
						<br />
						{l s='If some countries do not appear in the list of your XML files or PHP URL\'s (in "My feeds" tab), you must check your country, language and currency ISO codes in your back-office ("Localization tab") and look if they respect for example uppercase or lowercase. Actually, you must write these codes EXACTLY how they are written in the table of the ' mod='gmerchantcenterpro'}
						<a href="https://support.google.com/merchants/answer/160637?hl={$sCurrentIso|escape:'htmlall':'UTF-8'}&visit_id=1-636342381361070010-4017773094&rd=1)" target="_blank"><b>{l s='Google official documentation.' mod='gmerchantcenterpro'}</b></a>
					</div>
				</div>
			{/if}
		{/if}
		{* END - promo product data feed *}

		{* BEGIN - product reviews data feed *}
		{if !empty($sDisplay) && $sDisplay == 'reviews'}
			<h3 class="subtitle"><i class="fa fa-star"></i>&nbsp;{l s='Product ratings data feed' mod='gmerchantcenterpro'}</h3>

			<div class="bt-fb-fly-reviews">
				{* USE CASE - AVAILABLE FEED FILE LIST *}
				<div class="clr_10"></div>
				<div class="alert alert-info form-group">
					{l s='Please follow our FAQ to know' mod='gmerchantcenterpro'}&nbsp;&nbsp;<a class="badge badge-info" target="_blank" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/156"><i class="icon icon-link"></i>&nbsp;{l s='how to configure your product ratings feed' mod='gmerchantcenterpro'}</a>
				</div>

				<table border="0" cellpadding="2" cellspacing="2" class="table ">
					<tr class="bt_tr_header text-center">
						<th class="center">{l s='Country' mod='gmerchantcenterpro'}</th>
						<th class="center">{l s='URL (copy this URL into your Google Merchant Center interface / planning)' mod='gmerchantcenterpro'}</th>
					</tr>
					{if !empty($aFlyFileListReviews)}
						{foreach from=$aFlyFileListReviews name=feed key=iKey item=aFlyFeed}
							<tr>
								<td class="center">{$aFlyFeed.countryName|escape:'htmlall':'UTF-8'} - {$aFlyFeed.countryIso|escape:'htmlall':'UTF-8'}</td>
								<td class="center"><a target="_blank" href="{$aFlyFeed.link|escape:'htmlall':'UTF-8'}">{$aFlyFeed.langName|escape:'htmlall':'UTF-8'}</a></td>
							</tr>
						{/foreach}
					{else}
						<tr>
							<td>
								<div class="alert alert-warning text-center">
									{l s='No review module compatible with Google merchant center PRO is installed' mod='gmerchantcenterpro'}
								</div>
							</td>
						</tr>
					{/if}
				</table>
				{* USE CASE - THE OUTPUT PHP FILE HASN'T BEEN COPIED *}
			</div>
		{/if}
		{* END - product reviews data feed *}
	</form>
	<div id="{$sModuleName|escape:'htmlall':'UTF-8'}FeedListError"></div>
</div>
{literal}
	<script type="text/javascript">
		oGmcProFeedList.dynamicDisplay();

		//bootstrap components init
	{/literal}
	{if !empty($bAjaxMode)}
		{literal}

			{/literal}{/if}{literal}
		</script>
	{/literal}