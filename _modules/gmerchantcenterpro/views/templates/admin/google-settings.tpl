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
{if !empty($bUpdate)}
	{include file="`$sConfirmInclude`"}
{elseif !empty($aErrors)}
	{include file="`$sErrorInclude`"}
{/if}
<script type="text/javascript">
	{literal}
		var oGoogleSettingsCallBack = [{}];
	{/literal}
</script>

<div class="bootstrap">
	<form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_google-{$sDisplay|escape:'htmlall':'UTF-8'}-form" name="bt_google-{$sDisplay|escape:'htmlall':'UTF-8'}-form" {if $useJs == true}onsubmit="javascript: oGmcPro.form('bt_google-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_google-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_google-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, oGoogleSettingsCallBack, 'Google', 'loadingGoogleDiv');return false;" {/if}>
		<input type="hidden" name="sAction" value="{$aQueryParams.google.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.google.type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sDisplay" id="sGsDisplay" value="{if !empty($sDisplay)}{$sDisplay|escape:'htmlall':'UTF-8'}{else}categories{/if}" />

		{* USE CASE - Google categories *}
		{if !empty($sDisplay) && $sDisplay == 'categories'}
			<h3 class="subtitle"><i class="fa fa-copy"></i>&nbsp;{l s='Google Categories' mod='gmerchantcenterpro'}</h3>
			<div class="clr_10"></div>

			<div class="alert alert-info" id="info_export">
				<p><strong class="highlight_element">
						{l s='Each merchant has his own category names. But Google cannot manage all the possible and unimaginable names. So to solve this problem, Google created official category names and each merchant has to match his own categories with these. As each Google Shopping country has its own categories tawonomy, you have to match your categories for each country where you want to display Shopping campaigns.' mod='gmerchantcenterpro'}</strong></p><br />
				<p>{l s='However, please note that not all product types require a Google product category. Please visit ' mod='gmerchantcenterpro'} <b><a href="https://support.google.com/merchants/answer/6324436?visit_id=1-636353627563137693-1549157338&rd=1&hl={$sCurrentIso|escape:'htmlall':'UTF-8'}" target="_blank">{l s='this page' mod='gmerchantcenterpro'}</a></b>
					{l s='for more information' mod='gmerchantcenterpro'}.</p><br />
				<p>
				<ol>
					<li>{l s='Firstly, click on the reload icon' mod='gmerchantcenterpro'}&nbsp;<span class="icon-refresh">&nbsp;</span>{l s='to do a real-time update of the official Google categories list.' mod='gmerchantcenterpro'}</li>
					<li>{l s='Then, click on the pencil icon' mod='gmerchantcenterpro'}&nbsp;<span class="icon-pencil"></span>&nbsp;{l s='to match your own PrestaShop categories with the Google official ones.' mod='gmerchantcenterpro'}</li>
				</ol>
			</div>

			<div class="clr_20"></div>

			<div id="bt_google-cat-list">
				{include file="`$sGoogleCatListInclude`"}
			</div>

			<div class="clr_20"></div>
			<div id="loadingGoogleCatListDiv" style="display: none;">
				<div class="alert alert-info">
					<p style="text-align: center !important;"><img src="{$sLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
					<div class="clr_20"></div>
					<p style="text-align: center !important;">{l s='The update of the official Google categories matching is in progress...' mod='gmerchantcenterpro'}</p>
				</div>
			</div>
		{/if}
		{* END - Google categories *}

		{* USE CASE - Google analytics *}
		{if !empty($sDisplay) && $sDisplay == 'analytics'}
			<h3 class="subtitle"><i class="fa fa-file-code-o"></i>&nbsp;{l s='Google Analytics integration' mod='gmerchantcenterpro'}</h3>

			<div class="alert alert-info" id="info_export">
				<p><strong class="highlight_element">
						{l s='This section allows you to add some parameters in your product links (utm_campaign, utm_source and utm_medium) so that you can better track clicks and sales from your Google Ads product ads in your Google Analytics account.' mod='gmerchantcenterpro'}</strong></p><br />
				<p>{l s='If a parameter is left empty below, it will not be added. Please add alphanumerical characters ONLY, without spaces. You can use "-" or "_" sign however. For more information, please visit ' mod='gmerchantcenterpro'}
					<b><a href="https://support.google.com/analytics/answer/1033863?hl={$sCurrentIso|escape:'htmlall':'UTF-8'}" target="_blank">{l s='this Google Analytics help page' mod='gmerchantcenterpro'}</a></b>.
				</p><br />
				<p>{l s='Note : if you want to use this feature, please make sure that the utm_campaign, utm_source and utm_medium parameters are not disallowed in your robots.txt file.' mod='gmerchantcenterpro'}</p>
			</div>

			<div class="clr_20"></div>

			<div class="form-group ">
				<label class="control-label col-lg-3">
					<span><b>{l s='Value of utm_campaign parameter' mod='gmerchantcenterpro'}</b></span> :
				</label>
				<div class="col-xs-3">
					<input type="text" size="30" name="bt_utm-campaign" value="{$sUtmCampaign|escape:'htmlall':'UTF-8'}" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span><b>{l s='Value of utm_source parameter' mod='gmerchantcenterpro'}</b></span> :
				</label>
				<div class="col-xs-3">
					<input type="text" size="30" name="bt_utm-source" value="{$sUtmSource|escape:'htmlall':'UTF-8'}" />
				</div>
			</div>
			<div class="form-group ">
				<label class="control-label col-lg-3">
					<span><b>{l s='Value of utm_medium parameter' mod='gmerchantcenterpro'}</b></span> :
				</label>
				<div class="col-xs-3">
					<input type="text" size="30" name="bt_utm-medium" value="{$sUtmMedium|escape:'htmlall':'UTF-8'}" />
				</div>
			</div>

			<p class="alert alert-info">{l s='You can also add a "utm_content" parameter in your product links in order to know if the traffic comes from free or paid campaigns. For more info please visit our' mod='gmerchantcenterpro'}
				&nbsp;&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/389" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about the utm_content tag' mod='gmerchantcenterpro'}</a>
			</p>
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3">
					<span class="label-tooltip" title="{l s='Select "Yes" to add a utm_content parameter in product links' mod='gmerchantcenterpro'}"><b>{l s='Add a utm_content parameter?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_utm_content" id="bt_utm_content_on" value="1" {if !empty($bUtmContent)}checked="checked" {/if} />
						<label for="bt_utm_content_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_utm_content" id="bt_utm_content_off" value="0" {if empty($bUtmContent)}checked="checked" {/if} />
						<label for="bt_utm_content_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" data-placement="right" data-original-title="{l s='Select "Yes" to add a utm_content parameter in product links' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
				</div>
			</div>
		{/if}
		{* END - Google analytics *}

		{* USE CASE - Google custom label *}
		{if !empty($sDisplay) && $sDisplay == 'adwords'}

			<h3 class="subtitle"><i class="fa fa-bookmark-o"></i>&nbsp;{l s='Custom labels integration' mod='gmerchantcenterpro'}</h3>

			<div class="alert alert-info" id="info_export">
				<p><strong class="highlight_element">
						{l s='This section allows you to assign advanced custom labels to your products in order to subdivide your products and have a better Google Ads campaigns management. For more information, please visit' mod='gmerchantcenterpro'}&nbsp;<a href="https://support.google.com/adwords/answer/6275295?hl={$sCurrentIso|escape:'htmlall':'UTF-8'}" target="_blank">{l s='the Google official documentation about custom labels' mod='gmerchantcenterpro'}</a>.</strong></p>
				<p>{l s='You can visit also our' mod='gmerchantcenterpro'}&nbsp;<b><a href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/111" target="_blank">{l s='FAQ about custom labels creation' mod='gmerchantcenterpro'}</a></b></p>
				<div class="clr_10"></div>

				<p>{l s='Note : Google does not allow more than 5 labels per product. So, if one of your products has more than 5 custom labels, our module will select only the first 5 ones (in order of appearance below). You can change the sort order of the custom labels via drag and drop.' mod='gmerchantcenterpro'}</p>
			</div>

			<div class="alert alert-info" id="default_cat_explanation">
				{l s='By default, and for performance reasons, the module considers products to belong to just one category: their default category. If you want the module to search all categories associated with the product, disable the following option. Please note: this will require more performance from your server. In the event of slowdowns, please contact your web host.' mod='gmerchantcenterpro'}
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-6">
					<span>
						<b>{l s='Consider only the default product category for custom labels' mod='gmerchantcenterpro'}</b>
					</span>
				</label>
				<div class="col-xs-5 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="cl_default_cat" id="cl_default_cat_on" value="1" {if !empty($clDefaultCat)}checked="checked" {/if} />
						<label for="cl_default_cat_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="cl_default_cat" id="cl_default_cat_off" value="0" {if empty($clDefaultCat)}checked="checked" {/if} />
						<label for="cl_default_cat_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-6">
					<b>{l s='Operating mode for multiple conditions (see explanations below)' mod='gmerchantcenterpro'}</b>
				</label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_cl_mode_or_and" id="bt_cl_mode_or_and_off" value="0" {if empty($clModeOrAnd)}checked="checked" {/if} />
						<label for="bt_cl_mode_or_and_off" class="radioCheck">
							{l s='Extended mode (OR operator)' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_cl_mode_or_and" id="bt_cl_mode_or_and_on" value="1" {if !empty($clModeOrAnd)}checked="checked" {/if} />
						<label for="bt_cl_mode_or_and_on" class="radioCheck">
							{l s='Restricted mode (AND operator)' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="card-info">
				<h4>{l s='Selection of products to be labeled: multiple conditions' mod='gmerchantcenterpro'}</h4>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="mode-box">
							<h5><i class="icon-filter"></i> {l s='Restricted Mode' mod='gmerchantcenterpro'}</h5>
							<div class="mode-description">
								<p><strong>{l s='Label applied only if ALL conditions are true' mod='gmerchantcenterpro'}</strong></p>
								<div class="example-box">
									<p><strong>{l s='Example:' mod='gmerchantcenterpro'}</strong></p>
									<p>{l s='If you check the conditions:' mod='gmerchantcenterpro'}</p>
									<p>{l s='Category = Shoes and Brand = Nike' mod='gmerchantcenterpro'}</p>
									<p>
										{l s='Then the label will be applied' mod='gmerchantcenterpro'}
										<b>{l s='ONLY' mod='gmerchantcenterpro'}</b>
										{l s='if the product belongs to' mod='gmerchantcenterpro'}
										<b>{l s='BOTH' mod='gmerchantcenterpro'}</b>
										{l s='the "Shoes" category and the "Nike" brand.' mod='gmerchantcenterpro'}
									</p>

									<ul class="list-unstyled">
										<li><i class="icon-times text-danger"></i> {l s='The product belongs to the category "Shoes" but not to the brand "Nike" -> No label applied' mod='gmerchantcenterpro'}</li>
										<li><i class="icon-times text-danger"></i> {l s='The product belongs to the brand "Nike" but not to the category "Shoes" -> No label applied' mod='gmerchantcenterpro'}</li>
										<li><i class="icon-check text-success"></i> {l s='The product belongs to the category "Shoes" and the brand "Nike" -> Label applied' mod='gmerchantcenterpro'}</li>
									</ul>
									<p class="help-block"><i class="icon-info-circle"></i> {l s='Ideal for: creating specific product groups, highly targeted campaigns' mod='gmerchantcenterpro'}</p>
								</div>
							</div>
						</div>
					</div>

					<div class="col-xs-12 col-sm-6">
						<div class="mode-box">
							<h5><i class="icon-plus-circle"></i> {l s='Extended Mode' mod='gmerchantcenterpro'}</h5>
							<div class="mode-description">
								<p><strong>{l s='Label applied if AT LEAST ONE of the conditions is true' mod='gmerchantcenterpro'}</strong></p>
								<div class="example-box">
									<p><strong>{l s='Example:' mod='gmerchantcenterpro'}</strong></p>
									<p>{l s='If you check the conditions:' mod='gmerchantcenterpro'}</p>
									<p>{l s='Category = Shoes and Brand = Nike' mod='gmerchantcenterpro'}</p>
									<p>
										{l s='Then the label will be applied if the product belongs to the "Shoes" category' mod='gmerchantcenterpro'}
										<b>{l s='OR' mod='gmerchantcenterpro'}</b>
										{l s='the "Nike" brand' mod='gmerchantcenterpro'}
										<b>{l s='OR' mod='gmerchantcenterpro'}</b>
										{l s='to both.' mod='gmerchantcenterpro'}
									</p>

									<ul class="list-unstyled">
										<li><i class="icon-check text-success"></i> {l s='The product belongs to the category "Shoes" but not to the brand "Nike" -> Label applied' mod='gmerchantcenterpro'}</li>
										<li><i class="icon-check text-success"></i> {l s='The product belongs to the brand "Nike" but not to the category "Shoes" -> Label applied' mod='gmerchantcenterpro'}</li>
										<li><i class="icon-check text-success"></i> {l s='The product belongs to the category "Shoes" and the brand "Nike" -> Label applied' mod='gmerchantcenterpro'}</li>
									</ul>
									<p class="help-block"><i class="icon-info-circle"></i> {l s='Ideal for: reaching more products, campaigns targeting a wide range of products' mod='gmerchantcenterpro'}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-12">
				<div class="add_adwords text-center">
					<a id="handleGoogleAdwords" class="fancybox.ajax btn btn-lg btn-success pull-right" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8':'UTF-8'}&sAction={$aQueryParams.custom.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.custom.type|escape:'htmlall':'UTF-8':'UTF-8'}"><i class="icon icon-plus-square"></i>&nbsp;{l s='Add a custom label' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="clr_20"></div>

			{if !empty($aTags)}
				<div class="clr_15"></div>

				<div class="form-group">
					<button type="button" class="btn btn-default" onclick="return oGmcPro.selectAll('input.CustomLabelBox', 'check');">
						<i class="icon icon-plus-square"></i><span>&nbsp;{l s='Check All' mod='gmerchantcenterpro'}</span>
					</button>
					&nbsp;-&nbsp;
					<button type="button" class="btn btn-default" onclick="return oGmcPro.selectAll('input.CustomLabelBox', 'uncheck');">
						<i class="icon icon-minus-square"></i><span>&nbsp;{l s='Unselect All' mod='gmerchantcenterpro'}</span>
					</button>
					&nbsp;-&nbsp;

					<button class="btn btn-success "
						onclick="check = confirm('{l s='Are you sure you want to activate the selected custom label set(s)' mod='gmerchantcenterpro'} ?');if(!check)return false;iTagIds = oGmcPro.getBulkCheckBox('bt_custom_label-box');$('#loadingGoogleDiv').show();oGmcPro.hide('bt_google-settings');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customActivate.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customActivate.type|escape:'htmlall':'UTF-8'}&iTagIds='+iTagIds+'&sDeleteType=bulk&bActive=1&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
						<i class="icon icon-cogs"></i><span>&nbsp;{l s='Activate selection' mod='gmerchantcenterpro'}</span>
					</button>

					&nbsp;-&nbsp;

					<button class="btn btn-warning "
						onclick="check = confirm('{l s='Are you sure you want to deactivate the selected custom label set(s)' mod='gmerchantcenterpro'} ?');if(!check)return false;iTagIds = oGmcPro.getBulkCheckBox('bt_custom_label-box');$('#loadingGoogleDiv').show();oGmcPro.hide('bt_google-settings');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customActivate.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customActivate.type|escape:'htmlall':'UTF-8'}&iTagIds='+iTagIds+'&sDeleteType=bulk&bActive=0&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
						<i class="icon icon-cogs"></i><span>&nbsp;{l s='Deactivate selection' mod='gmerchantcenterpro'}</span>
					</button>

					&nbsp;-&nbsp;

					<button class="btn btn-danger "
						onclick="check = confirm('{l s='Are you sure you want to delete the custom label' mod='gmerchantcenterpro'} ?');if(!check)return false;iTagIds = oGmcPro.getBulkCheckBox('bt_custom_label-box');$('#loadingGoogleDiv').show();oGmcPro.hide('bt_google-settings');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customDelete.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customDelete.type|escape:'htmlall':'UTF-8'}&iTagIds='+iTagIds+'&sDeleteType=bulk&sActionType=delete&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
						<i class="icon icon-trash"></i><span>&nbsp;{l s='Delete Selection' mod='gmerchantcenterpro'}</span>
					</button>
				</div>

				<div class="form-group">
					<div class="col-xs-12">
						<div class="alert alert-info">
							{l s='You can drag and drop the lines to sort the custom labels sets, thanks to the little cross at the left of each line.' mod='gmerchantcenterpro'}
						</div>
						<div id="bt_save_reoder" class="col-xs-12 alert alert-success">
							{l s='Your custom labels are saved' mod='gmerchantcenterpro'}
						</div>
						<table id="diagnosis_list" class="table tags" data-toggle="table" data-url="data.json">
							<thead>
								<thead>
									<tr class="bt_tr_header">
										<th></th>
										<th></th>
										<th data-sortable="true" style="text-align: center"># &nbsp;<i class="icon icon-sort"></i></th>
										<th data-sortable="true" style="text-align: center">{l s='Custom labels set name' mod='gmerchantcenterpro'}&nbsp;<i class="icon icon-sort"></i></th>
										<th data-sortable="true" style="text-align: center">{l s='Number' mod='gmerchantcenterpro'}&nbsp;<i class="icon icon-sort"></i></th>
										<th data-sortable="true" style="text-align: center">{l s='Custom labels valid until' mod='gmerchantcenterpro'}&nbsp;<i class="icon icon-sort"></i></th>
										<th data-sortable="true" style="text-align: center">{l s='State' mod='gmerchantcenterpro'}&nbsp;<i class="icon icon-sort"></i></th>
										<th style="text-align: center;">{l s='Activate / Deactivate' mod='gmerchantcenterpro'}</th>
										<th style="text-align: center;">{l s='Edit' mod='gmerchantcenterpro'}</th>
										<th style="text-align: center;">{l s='Delete' mod='gmerchantcenterpro'}</th>
										<th style="text-align: center">{l s='Products labeled' mod='gmerchantcenterpro'}</th>
										<th style="text-align: center"><a id="handleGoogleAdwords" class="fancybox.ajax btn btn-success" style="float: right" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8':'UTF-8'}&sAction={$aQueryParams.custom.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.custom.type|escape:'htmlall':'UTF-8':'UTF-8'}"><i class="icon icon-plus-square"></i></a></th>
									</tr>
								</thead>
							<tbody>
								{foreach from=$aTags name=label key=iKey item=aTag}
									<tr {if $aTag.active == 1}class="success ui-state-default" {else}class="danger ui-state-default" {/if} style="text-align: center">
										<td><i class="icon icon-move"></i> </td>
										<td><input type="checkbox" name="bt_custom_label-box" class="CustomLabelBox" id="bt_custom_label-box_{$aTag.id_tag|escape:'htmlall':'UTF-8'}" value="{$aTag.id_tag|escape:'htmlall':'UTF-8'}" /></td>
										<td>
											<span class="gmcp_count_html"></span>
											<input type="hidden" class="priority" value="{$aTag.position|escape:'htmlall':'UTF-8'}" />
										</td>
										<td>{$aTag.name|escape:'htmlall':'UTF-8'}</td>
										<td>{$aTag.custom_label_set_postion|escape:'htmlall':'UTF-8'}</td>
										<td>{$aTag.end_date|escape:'htmlall':'UTF-8'} <input type="hidden" id="gmcp_date_custom_label" value="{$aTag.end_date|escape:'htmlall':'UTF-8'}" /></td>
										<td style="text-align: center">
											{if $aTag.active == 1}<i class="icon icon-check"></i>{else}<i class="icon icon-off"></i>{/if}
										</td>
										<td style="text-align: center">
											{if $aTag.active == 1}
												<button class="btn btn-warning btn-mini"
													onclick="check = confirm('{l s='Are you sure you want to deactivate this custom labels set' mod='gmerchantcenterpro'} ?');if(!check)return false;$('#loadingGoogleDiv').show();oGmcPro.hide('bt_google-settings');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customActivate.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customActivate.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|escape:'htmlall':'UTF-8'}&sDeleteType=one&bActive=0&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
													<i class="icon icon-off"></i>
												</button>
											{else}
												<button class="btn btn-success btn-mini" id="gmcp_process_activation"
													onclick="check = confirm('{l s='Are you sure you want to activate this custom labels set' mod='gmerchantcenterpro'} ?');if(!check)return false;$('#loadingGoogleDiv').show();oGmcPro.hide('bt_google-settings');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customActivate.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customActivate.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|escape:'htmlall':'UTF-8'}&sDeleteType=one&bActive=1&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
													<i class="icon icon-check"></i>
												</button>
											{/if}
										</td>
										<td>
											<a id="handleGoogleAdwordsEdit" class="fancybox.ajax btn btn-default btn-mini" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.custom.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.custom.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|escape:'htmlall':'UTF-8'}&sDisplay=button3"><i class="icon icon-edit"></i></a>
										<td>
											<button class="btn btn-danger btn-mini" id="gmcp_process_activation"
												onclick="check = confirm('{l s='Are you sure you want to delete this custom labels set' mod='gmerchantcenterpro'} ?');if(!check)return false;$('#loadingGoogleDiv').show();oGmcPro.hide('bt_google-settings');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customDelete.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customDelete.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|escape:'htmlall':'UTF-8'}&sDeleteType=one&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
												<i class="icon icon-trash"></i>
											</button>
										</td>
										<td>
											<a id="cutomLabelProducDetails" class="fancybox.ajax btn btn-mini btn-default" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customProduct.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customProduct.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|escape:'htmlall':'UTF-8'}&sDisplay=button3"><i class="icon icon-zoom-in"></i></a>
										</td>
										<td></td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			{/if}
		{/if}
		{* END - Google custom label *}

		{if !empty($sDisplay) && ($sDisplay == 'analytics' || $sDisplay == 'adwords')}
			<div class="navbar navbar-default navbar-fixed-bottom text-center">
				<div class="col-xs-12">
					<button class="btn btn-submit" onclick="oGmcPro.form('bt_google-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_google-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_google-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, oGoogleSettingsCallBack, 'Google', 'loadingGoogleDiv');return false;">{l s='Save' mod='gmerchantcenterpro'}</button>
				</div>
			</div>
		{/if}

	</form>
</div>

<div class="clr_20"></div>

{literal}
	<script type="text/javascript">
		$(document).ready(function() {
			//get date for CL
			oGmcPro.manageCustomLabelDate('#diagnosis_list', '#gmcp_date_custom_label', '#gmcp_process_activation');

			// all process for table sort management
			$(function() {
				$(".tags").tablesorter();
			});

			var fixHelperModified = function(e, tr) {
				var $originals = tr.children();
				var $helper = tr.clone();
				$helper.children().each(function(index) {
					$(this).width($originals.eq(index).width())
				});
				return $helper;
			};

			//Make diagnosis table sortable
			$("#diagnosis_list tbody").sortable({
				helper: fixHelperModified,
				stop: function(event,ui) {renumber_table('#diagnosis_list'),updateDataBase("#diagnosis_list")}
			}).disableSelection();

			// Function to update visual feedback
			function updateModeVisuals(isStrict) {
				if (isStrict) {
					// Restricted mode (AND) is selected
					$('.mode-box').eq(0).css({
						'background-color': 'rgba(40, 167, 69, 0.1)',
						'opacity': '1'
					});
					$('.mode-box').eq(1).css({
						'background-color': 'transparent',
						'opacity': '0.5'
					});
				} else {
					// Extended mode (OR) is selected
					$('.mode-box').eq(1).css({
						'background-color': 'rgba(40, 167, 69, 0.1)',
						'opacity': '1'
					});
					$('.mode-box').eq(0).css({
						'background-color': 'transparent',
						'opacity': '0.5'
					});
				}
			}

			// Initial state based on saved configuration
			updateModeVisuals($('#bt_cl_mode_or_and_on').is(':checked'));

			// Handle radio button changes
			$('input[name="bt_cl_mode_or_and"]').change(function() {
				updateModeVisuals($(this).val() === '1');
			});
		});

		//{$aTag.position|escape:'htmlall':'UTF-8'}
		$("#diagnosis_list tbody tr").each(function() {
			count = $(this).parent().children().index($(this)) + 1;
			if ($(this).find(".gmcp_count_html").html(count) == '') {
				$(this).find('.gmcp_count_html').html(count);
			}
			$(this).find('.priority').val(count);
		});

		function renumber_table(tableID) {
			$(tableID + " tr").each(function() {
				count = $(this).parent().children().index($(this)) + 1;
				$(this).find(".gmcp_count_html").html(count)
				$(this).find('.priority').val(count);
			});
		}

		function updateDataBase(tableID) {
			$(tableID + " tr").each(function() {
				//get value for request magement
				iTagIdMoveToNewPos = $(this).find(".CustomLabelBox").val();
				iNewPosition = $(this).find(".priority").val();
				iTagIdMoveToOldPos = $(this).parent().find(".CustomLabelBox").val();
				iOldPosition = $(this).parent().find(".priority").val();

				// construct data here
				sDataPrestashop = '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}{literal}';
				sDataModule = '&iTagIdMoveToNewPos=' + iTagIdMoveToNewPos + '&iNewPosition=' + iNewPosition + '&iTagIdMoveToOldPos=' + iTagIdMoveToOldPos + '&iOldPosition=' + iOldPosition;
				sData = sDataPrestashop + sDataModule;

				$.ajax({
					url : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
					type: 'POST',
					data: sData,
					dataType: 'json',
					async: true,
					complete: function(result) {
						$('#bt_save_reoder').slideDown();
					}
				});
			});
		}

		//bootstrap components init
	{/literal}
	{if !empty($bAjaxMode)}
		{literal}

			{/literal}{/if}{literal}
		</script>
	{/literal}

