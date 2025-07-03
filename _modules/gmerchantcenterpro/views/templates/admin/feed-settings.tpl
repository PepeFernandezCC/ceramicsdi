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
{if !empty($sDisplay) && ($sDisplay == 'export' || $sDisplay == 'data')}
	<script type="text/javascript">
		{literal}
			var oFeedSettingsCallBack = [{
					'name': 'displayFeedList',
					'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
					'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedList.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedList.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=data',
					'toShow': 'bt_feed-list-settings-data',
					'toHide': 'bt_feed-list-settings-data',
					'bFancybox': false,
					'bFancyboxActivity': false,
					'sLoadbar': null,
					'sScrollTo': null,
					'oCallBack': {}
				},
				{
					'name': 'displayFeedListPromo',
					'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
					'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedList.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedList.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=promo',
					'toShow': 'bt_feed-list-settings-promo',
					'toHide': 'bt_feed-list-settings-promo',
					'bFancybox': false,
					'bFancyboxActivity': false,
					'sLoadbar': null,
					'sScrollTo': null,
					'oCallBack': {}
				},
				{
					'name': 'displayFeedListStock',
					'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
					'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedList.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedList.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=stock',
					'toShow': 'bt_feed-list-settings-stock',
					'toHide': 'bt_feed-list-settings-stock',
					'bFancybox': false,
					'bFancyboxActivity': false,
					'sLoadbar': null,
					'sScrollTo': null,
					'oCallBack': {}
				}
			];
		{/literal}
	</script>
{/if}

<div class="bootstrap">
	<form class="form-horizontal col-xs-12" method="post" id="bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form" name="bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form" {if $useJs == true}onsubmit="javascript: oGmcPro.form('bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, {if empty($sDisplay) || (!empty($sDisplay) && ($sDisplay == 'export' || $sDisplay == 'data'))}oFeedSettingsCallBack{else}null{/if}, 'Feed{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedDiv');return false;" {/if}>
		<input type="hidden" name="sAction" value="{$aQueryParams.feed.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.feed.type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sDisplay" id="sDisplay" value="{if !empty($sDisplay)}{$sDisplay|escape:'htmlall':'UTF-8'}{else}export{/if}" />

		{* USE CASE - Export *}
		{if !empty($sDisplay) && $sDisplay == 'export'}
			<h3 class="subtitle"><i class="fa fa-check-square"></i>&nbsp;{l s='Export method' mod='gmerchantcenterpro'}</h3>
			<div class="clr_10"></div>
			{if !empty($bUpdate)}
				{include file="`$sConfirmInclude`"}
			{elseif !empty($aErrors)}
				{include file="`$sErrorInclude`"}
			{/if}

			<div {if !empty($bExportMode)}style="display: none;" {/if}>
				{if $iMaxPostVars != false && $iShopCatCount > $iMaxPostVars}
					<div class="alert alert-warning">
						{l s='Warning: apparently the number of variables that can be sent via a form is limited by your server, and the total number of your categories is greater than this maximum number of possible variables.' mod='gmerchantcenterpro'} :<br />
						<strong>{$iShopCatCount|escape:'htmlall':'UTF-8'}</strong>&nbsp;{l s='categories' mod='gmerchantcenterpro'}</strong>&nbsp;{l s='out of' mod='gmerchantcenterpro'}&nbsp;<strong>{$iMaxPostVars|escape:'htmlall':'UTF-8'}</strong>&nbsp;{l s='possible variables (PHP directive => max_input_vars)' mod='gmerchantcenterpro'}<br /><br />
						<strong>{l s='Not all your categories may be exported. For more information, please read' mod='gmerchantcenterpro'}</strong>&nbsp;<a class="badge badge-warning text-white px-2 py-2" target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/59">{l s='our FAQ' mod='gmerchantcenterpro'}</a>
					</div>
				{/if}
			</div>

			<div class="form-group" id="optionplus">
				<label class="control-label col-xs-12 col-md-2 col-lg-2">
					<span class="label-tooltip" title="{l s='You can choose to export your products by categories or by brands.' mod='gmerchantcenterpro'}"><b>{l s='Select your export method' mod='gmerchantcenterpro'}</b></span> :
				</label>
				<div class="col-xs-12 col-md-3 col-lg-2">
					<select name="bt_export" id="bt_export">
						<option value="0" {if empty($bExportMode)}selected="selected" {/if}>{l s='Export by categories' mod='gmerchantcenterpro'}</option>
						<option value="1" {if !empty($bExportMode)}selected="selected" {/if}>{l s='Export by brands' mod='gmerchantcenterpro'}</option>
					</select>
				</div>
				<span class="icon-question-sign label-tooltip" title="{l s='You can choose to export your products by categories or by brands.' mod='gmerchantcenterpro'}"></span>
			</div>
			{* categories tree *}
			<div id="bt_categories" {if !empty($bExportMode)}style="display: none;" {/if}>
				<div class="form-group">
					<label class="control-label col-xs-12 col-md-2 col-lg-2">
						<span class="label-tooltip" title="{l s='Select the categories you want to export. You will be able to exclude some products from these selected categories in "Product exclusion rules" tab' mod='gmerchantcenterpro'}"><b>{l s='Categories' mod='gmerchantcenterpro'}</b></span> :
					</label>
					<div class="col-xs-12 col-md-5 col-lg-4">
						<div class="btn-actions">
							<div class="btn btn-default btn-mini" id="categoryCheck" onclick="return oGmcPro.selectAll('input.categoryBox', 'check');"><span class="icon-plus-square"></span>&nbsp;{l s='Check All' mod='gmerchantcenterpro'}</div> - <div class="btn btn-default btn-mini" id="categoryUnCheck" onclick="return oGmcPro.selectAll('input.categoryBox', 'uncheck');"><span class="icon-minus-square"></span>&nbsp;{l s='Uncheck All' mod='gmerchantcenterpro'}</div>
							<div class="clr_10"></div>
						</div>
						<table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped">
							{foreach from=$aFormatCat name=category key=iKey item=aCat}
								<tr class="alt_row">
									<td>
										{$aCat.id_category|escape:'htmlall':'UTF-8'}
									</td>
									<td>
										<input type="checkbox" name="bt_category-box[]" class="categoryBox" id="bt_category-box_{$aCat.iNewLevel|escape:'htmlall':'UTF-8'}" value="{$aCat.id_category|escape:'htmlall':'UTF-8'}" {if !empty($aCat.bCurrent)}checked="checked" {/if} />
									</td>
									<td>
										<span class="icon icon-folder{if !empty($aCat.bCurrent)}-open{/if}" style="margin-left: {$aCat.iNewLevel|escape:'htmlall':'UTF-8'}5px;"></span>&nbsp;&nbsp;<span style="font-size:12px;">{$aCat.name|escape:'htmlall':'UTF-8'}</span>
									</td>
								</tr>
							{/foreach}
						</table>
						<div class="clr_10"></div>
					</div>
				</div>
			</div>

			{* brands tree *}
			<div id="bt_brands" {if empty($bExportMode)}style="display: none;" {/if}>
				<div class="form-group">
					<label class="control-label col-xs-12 col-md-2 col-lg-2">
						<span class="label-tooltip" title="{l s='Select the brands you want to export. You will be able to exclude some products from these selected brands in "Product exclusion rules" tab' mod='gmerchantcenterpro'}"><b>{l s='Brands' mod='gmerchantcenterpro'}</b></span> :
					</label>
					<div class="col-xs-12 col-md-5 col-lg-4">
						<div class="btn-actions">
							<div class="btn btn-default btn-mini" id="brandCheck" onclick="return oGmcPro.selectAll('input.brandBox', 'check');"><i class="icon-plus-square"></i>&nbsp;{l s='Check All' mod='gmerchantcenterpro'}</div> - <div class="btn btn-default btn-mini" id="brandUnCheck" onclick="return oGmcPro.selectAll('input.brandBox', 'uncheck');"><i class="icon-minus-square"></i>&nbsp;{l s='Uncheck All' mod='gmerchantcenterpro'}</div>
							<div class="clr_10"></div>
						</div>
						<table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped" style="width: 100%;">
							{foreach from=$aFormatBrands name=brand key=iKey item=aBrand}
								<tr class="alt_row">
									<td>
										{$aBrand.id|escape:'htmlall':'UTF-8'}
									</td>
									<td>
										<input type="checkbox" name="bt_brand-box[]" class="brandBox" id="bt_brand-box_{$aBrand.id|escape:'htmlall':'UTF-8'}" value="{$aBrand.id|escape:'htmlall':'UTF-8'}" {if !empty($aBrand.checked)}checked="checked" {/if} />
									</td>
									<td>
										<i class="icon icon-folder{if !empty($aBrand.checked)}-open{/if}">&nbsp;&nbsp;<span style="font-size:12px;"></i><span>{$aBrand.name|escape:'htmlall':'UTF-8'}</span>
									</td>
								</tr>
							{/foreach}
						</table>
						<div class="clr_10"></div>
					</div>
				</div>
			</div>
		{/if}
		{* END - Export *}

		{* USE CASE - Exclusion *}
		{if !empty($sDisplay) && $sDisplay == 'exclusion'}
			<ul class="nav nav-tabs" id="myTab">
				<li class=" {if empty($aExclusionRules)}active{/if}">
					<a data-toggle="tab" href="#basic"><i class="fa fa-file-code-o"></i>&nbsp;{l s='General exclusion' mod='gmerchantcenterpro'}</a>
				</li>
				<li class="{if !empty($aExclusionRules)}active{/if}">
					<a data-toggle="tab" href="#advanced"><i class="fa fa-server"></i>&nbsp;{l s='Advanced exclusion' mod='gmerchantcenterpro'}</a>
				</li>
			</ul>

			<div class="clr_10"></div>

			{if !empty($bUpdate)}
				{include file="`$sConfirmInclude`"}
				<div class="clr_10"></div>
			{elseif !empty($aErrors)}
				{include file="`$sErrorInclude`"}
			{/if}

			<div class="tab-content" id="myTabContent">

				<div class="tab-pane {if empty($aExclusionRules)}active{/if}" id="basic">

					<div class="form-group">
						<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you select YES : all products, even those that are out of stock, will be exported. If you select NO : only the products that are in stock will be exported.' mod='gmerchantcenterpro'}"><b>{l s=' Do you want to export out of stock products ?' mod='gmerchantcenterpro'}</b></span></label>
						<div class="col-xs-12 col-md-5 col-lg-6">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_export-oos" id="bt_export-oos_on" value="1" {if !empty($bExportOOS)}checked="checked" {/if} onclick="oGmcPro.changeSelect('bt_div_product_oos', 'bt_div_product_oos', null, null, true, true);" />
								<label for="bt_export-oos_on" class="radioCheck">
									{l s='Yes' mod='gmerchantcenterpro'}
								</label>
								<input type="radio" name="bt_export-oos" id="bt_export-oos_off" value="0" {if empty($bExportOOS)}checked="checked" {/if} onclick="oGmcPro.changeSelect('bt_div_product_oos', 'bt_div_product_oos', null, null, true, false);" />
								<label for="bt_export-oos_off" class="radioCheck">
									{l s='No' mod='gmerchantcenterpro'}
								</label>
								<a class="slide-button btn"></a>
							</span>
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you select YES : all products, even those that are out of stock, will be exported. If you select NO : only the products that are in stock will be exported.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
							&nbsp;&nbsp;
							<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/213" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product availability' mod='gmerchantcenterpro'}</a>
						</div>
					</div>

					<div class="form-group" id="bt_div_product_oos" style="display: {if !empty($bExportOOS)} block{else} none{/if}">
						<label class="control-label col-xs-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you select YES : the products that are out of stock and authorized for orders will be exported. If you select NO : all out of stock products, even those that are denied for orders, will be exported.' mod='gmerchantcenterpro'}">
								<b>{l s='Do not export when you deny orders for out-of-stock products?' mod='gmerchantcenterpro'}</b>
							</span>
						</label>
						<div class="col-xs-12 col-md-5 col-lg-6">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_product-oos-order" id="bt_product-oos-order_on" value="1" {if !empty($bProductOosOrder)}checked="checked" {/if} />
								<label for="bt_product-oos-order_on" class="radioCheck">
									{l s='Yes' mod='gmerchantcenterpro'}
								</label>
								<input type="radio" name="bt_product-oos-order" id="bt_product-oos-order_off" value="0" {if empty($bProductOosOrder)}checked="checked" {/if} />
								<label for="bt_product-oos-order_off" class="radioCheck">
									{l s='No' mod='gmerchantcenterpro'}
								</label>
								<a class="slide-button btn"></a>
							</span>
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you select YES : the products that are out of stock and authorized for orders will be exported. If you select NO : all out of stock products, even those that are denied for orders, will be exported.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
							<a class="badge badge-info " href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/237" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about orders denied if no stock available' mod='gmerchantcenterpro'}</a>
						</div>
					</div>

					<div class="clr_10"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If Google is sending you numerous errors due to missing EAN, UPC or ISBN codes, you can activate this option and products that have neither EAN13 nor UPC nor ISBN will not be exported. This will ensure that you no longer receive these errors while you recover all your products\' GTIN codes from your suppliers.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to NOT export products without EAN13/JAN or UPC or ISBN ?' mod='gmerchantcenterpro'}</b></span></label>
						<div class="col-xs-12 col-md-5 col-lg-6">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_excl-no-ean" id="bt_excl-no-ean_on" value="1" {if !empty($bExcludeNoEan)}checked="checked" {/if} />
								<label for="bt_excl-no-ean_on" class="radioCheck">
									{l s='Yes' mod='gmerchantcenterpro'}
								</label>
								<input type="radio" name="bt_excl-no-ean" id="bt_excl-no-ean_off" value="0" {if empty($bExcludeNoEan)}checked="checked" {/if} />
								<label for="bt_excl-no-ean_off" class="radioCheck">
									{l s='No' mod='gmerchantcenterpro'}
								</label>
								<a class="slide-button btn"></a>
							</span>
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If Google is sending you numerous errors due to missing EAN, UPC or ISBN codes, you can activate this option and products that have neither EAN13 nor UPC nor ISBN will not be exported. This will ensure that you no longer receive these errors while you recover all your products\' GTIN codes from your suppliers.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
							&nbsp;&nbsp;
							<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/192" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about GTIN codes' mod='gmerchantcenterpro'}</a>
						</div>
					</div>

					<div class="clr_5"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you select YES : products without MPN (manufacturer) code will NOT be exported. This will get rid of the Google errors about missing MPN codes until you are able to get all your product codes from suppliers. If you select NO : even products without MPN code will be exported.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to NOT export products without a manufacturer (MPN) reference ?' mod='gmerchantcenterpro'}</b></span></label>
						<div class="col-xs-12 col-md-5 col-lg-6">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_excl-no-mref" id="bt_excl-no-mref_on" value="1" {if !empty($bExcludeNoMref)}checked="checked" {/if} />
								<label for="bt_excl-no-mref_on" class="radioCheck">
									{l s='Yes' mod='gmerchantcenterpro'}
								</label>
								<input type="radio" name="bt_excl-no-mref" id="bt_excl-no-mref_off" value="0" {if empty($bExcludeNoMref)}checked="checked" {/if} />
								<label for="bt_excl-no-mref_off" class="radioCheck">
									{l s='No' mod='gmerchantcenterpro'}
								</label>
								<a class="slide-button btn"></a>
							</span>
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you select YES : products without MPN (manufacturer) code will NOT be exported. This will get rid of the Google errors about missing MPN codes until you are able to get all your product codes from suppliers. If you select NO : even products without MPN code will be exported.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
							<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/198" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about MPN codes' mod='gmerchantcenterpro'}</a>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-md-3 col-lg-3">
							<span class="label-tooltip" title="{l s='Any product whose CURRENT price (taking specific prices into account) is lower than this value will be excluded from the feed. Cart rules are NOT taken into account, only specific prices. This allows you to exclude low margin products and not pay for clicks on them, making your Google ads campaigns more efficient and profitable.' mod='gmerchantcenterpro'}"><b>{l s='Do NOT export products with price lower than :' mod='gmerchantcenterpro'}</b></span></label>
						<div class="col-xs-12 col-md-1 col-lg-1">
							<input type="text" size="5" name="bt_min-price" value="{if !empty($iMinPrice)}{$iMinPrice|floatval}{/if}" />
						</div>{l s='Tax excluded' mod='gmerchantcenterpro'}
						&nbsp;
						<span class="icon-question-sign label-tooltip" title="{l s='Any product whose CURRENT price (taking specific prices into account) is lower than this value will be excluded from the feed. Cart rules are NOT taken into account, only specific prices. This allows you to exclude low margin products and not pay for clicks on them, making your Google ads campaigns more efficient and profitable.' mod='gmerchantcenterpro'}"></span>&nbsp;
						<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/22" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product exclusion' mod='gmerchantcenterpro'}</a>
					</div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-md-3 col-lg-3">
							<span class="label-tooltip" title="{l s='Any product whose CURRENT weight is higher than this value will be excluded from the feed.' mod='gmerchantcenterpro'}"><b>{l s='Do NOT export products with weight greater than :' mod='gmerchantcenterpro'}</b></span></label>
						<div class="col-xs-12 col-md-1 col-lg-1">
							<input type="text" size="5" name="bt_max-weight" value="{if !empty($iMaxWeight)}{$iMaxWeight|floatval}{/if}" />
						</div>
						&nbsp;
						<span class="icon-question-sign label-tooltip" title="{l s='Any product whose CURRENT weight is higher than this value will be excluded from the feed.' mod='gmerchantcenterpro'}"></span>
						<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/22" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product exclusion' mod='gmerchantcenterpro'}</a>
					</div>

					<div class="navbar navbar-default navbar-fixed-bottom text-center">
						<div class="col-xs-12">
							<button class="btn btn-submit" onclick="oGmcPro.form('bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, {if empty($sDisplay) || (!empty($sDisplay) && ($sDisplay == 'export' || $sDisplay == 'data'))}oFeedSettingsCallBack{else}null{/if}, 'Feed{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedDiv', false, 2);return false;"></i>{l s='Save' mod='gmerchantcenterpro'}</button>
						</div>
					</div>
				</div>
				<div class="tab-pane {if !empty($aExclusionRules)}active{/if}" id="advanced">
					<div class="alert alert-info pull-left">
						{l s='Use this tool to create personal and very specific exclusion rules. To know how to use it, don\'t hesitate to read our FAQ :' mod='gmerchantcenterpro'}&nbsp;&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/175" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='How to create advanced exclusion rules?' mod='gmerchantcenterpro'}</a>
					</div>
					<a id="handleExclusion" class="fancybox.ajax btn btn-lg btn-success pull-right" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRule.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRule.type|escape:'htmlall':'UTF-8'}"><span class="icon-plus-circle"></span>&nbsp;{l s='Add exclusion rule' mod='gmerchantcenterpro'}</a>
					<div class="clr_20"></div>
					<p class="alert alert-warning">
						{l s='Be careful: after having created custom rules, if you want to change the "About products with combinations" option value of the "Feed data option" tab, know that you will have to delete all the created rules and re-create them. Indeed, the exclusion management is different according to your choice to export or not by combination.' mod='gmerchantcenterpro'}
					</p>

					{if !empty($aExclusionRules)}
						<div class="form-group">
							<button type="button" class="btn btn-default" onclick="return oGmcPro.selectAll('input.RulesBox', 'check');">
								<i class="icon icon-plus-square"></i><span>&nbsp;{l s='Check All' mod='gmerchantcenterpro'}</span>
							</button>
							&nbsp;-&nbsp;
							<button type="button" class="btn btn-default" onclick="return oGmcPro.selectAll('input.RulesBox', 'uncheck');">
								<i class="icon icon-minus-square"></i><span>&nbsp;{l s='Unselect All' mod='gmerchantcenterpro'}</span>
							</button>
							&nbsp;-&nbsp;
							<button class="btn btn-success "
								onclick="check = confirm('{l s='Are you sure you want to activate the selected rules?' mod='gmerchantcenterpro'}');if(!check)return false;iRulesId = oGmcPro.getBulkCheckBox('bt_rules-box', false);$('#loadingGoogleDiv').show();oGmcPro.hide('bt_feed-settings-exclusion');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId='+iRulesId+'&sUpdateType=bulk&bActivate=1&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"><i
									class="icon icon-cogs"></i><span>&nbsp;{l s='Activate selection' mod='gmerchantcenterpro'}</span>
							</button>
							&nbsp;-&nbsp;
							<button class="btn btn-warning "
								onclick="check = confirm('{l s='Are you sure you want to deactivate the selected rules?' mod='gmerchantcenterpro'}');if(!check)return false;iRulesId = oGmcPro.getBulkCheckBox('bt_rules-box', false);$('#loadingGoogleDiv').show();oGmcPro.hide('bt_feed-settings-exclusion');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId='+iRulesId+'&sUpdateType=bulk&bActivate=0&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"><i
									class="icon icon-cogs"></i><span>&nbsp;{l s='Deactivate selection' mod='gmerchantcenterpro'}</span>
							</button>
							&nbsp;-&nbsp;
							<button class="btn btn-danger "
								onclick="check = confirm('{l s='Are you sure you want to delete the selected rules?' mod='gmerchantcenterpro'}');if(!check)return false;iRulesId = oGmcPro.getBulkCheckBox('bt_rules-box', false);$('#loadingGoogleDiv').show();oGmcPro.hide('bt_feed-settings-exclusion');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRuleDelete.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRuleDelete.type|escape:'htmlall':'UTF-8'}&iRuleId='+iRulesId+'&sDeleteType=bulk&sActionType=delete&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');">
								<i class="icon icon-trash"></i><span>&nbsp;{l s='Delete selection' mod='gmerchantcenterpro'}</span>
							</button>
						</div>
						{*Table for the rules saved*}
						<table class="table tables-striped">
							<thead class="bt_tr_header">
								<th class="center col-xs-1"></th>
								<th class="center">#</th>
								<th class="center"><b>{l s='Status' mod='gmerchantcenterpro'}</b></th>
								<th class="center"><b>{l s='Rule\'s name' mod='gmerchantcenterpro'}</b></th>
								<th class="center col-xs-2"><b>{l s='View affected products' mod='gmerchantcenterpro'}</b></th>
								<th class="center">
									<b>{l s='Actions' mod='gmerchantcenterpro'}</b>
								</th>
							</thead>
							<tbody>
								{foreach from=$aExclusionRules  key=key item=sRule}
									<tr class="">
										<td class="center"><input id="bt_rules-box_{$sRule.id|escape:'htmlall':'UTF-8'}" name="bt_rules-box" class="RulesBox" type="checkbox" value="{$sRule.id|escape:'htmlall':'UTF-8'}" /> </td>
										<td class="center">{$sRule.id|escape:'htmlall':'UTF-8'}</td>
										<td class="center">
											{if $sRule.status == 1}
												<a href="#"><i class="icon icon-2x icon-check-circle color_success" title="{l s='Deactivate' mod='gmerchantcenterpro'}"
														onclick="check = confirm('{l s='Are you sure you want to deactivate this rule?' mod='gmerchantcenterpro'}');if(!check)return false;$('#loadingGoogleDiv').show();oGmcPro.hide('bt_rules');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|escape:'htmlall':'UTF-8'}&sUpdateType=one&bActivate=0&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
											{else}
												<a href="#"><i class="icon icon-2x icon-check-circle color_danger" title="{l s='Activate' mod='gmerchantcenterpro'}"
														onclick="check = confirm('{l s='Are you sure you want to activate this rule?' mod='gmerchantcenterpro'}');if(!check)return false;$('#loadingGoogleDiv').show();oGmcPro.hide('bt_rules');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|escape:'htmlall':'UTF-8'}&sUpdateType=one&bActivate=1&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
											{/if}
										</td>
										<td class="center">{$sRule.name|escape:'htmlall':'UTF-8'}</td>
										<td class="center">
											<a id="handleExclusionProducts" class="fancybox.ajax btn btn-mini btn-info" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRuleProducts.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRuleProducts.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|escape:'htmlall':'UTF-8'}"><span class="fa fa-eye"></span></a>
										</td>
										<td class="center">
											<a id="handleExclusion" class="fancybox.ajax btn btn-info btn-min" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRule.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRule.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|escape:'htmlall':'UTF-8'}"><span class="icon icon-edit"></span></a>
											<a href="#"><i class="icon-trash btn btn-mini btn-danger" title="{l s='Delete' mod='gmerchantcenterpro'}"
													onclick="check = confirm('{l s='Are you sure you want to delete this rule?' mod='gmerchantcenterpro'} {l s='It will be definitely removed from your database' mod='gmerchantcenterpro'}');if(!check)return false;$('#loadingGoogleDiv').show();oGmcPro.hide('bt_rules');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRuleDelete.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRuleDelete.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|escape:'htmlall':'UTF-8'}&sDeleteType=one&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
											{if $sRule.status == 1}
												<a href="#"><i class="btn btn-warning btn-mini fa fa-remove" title="{l s='Deactivate' mod='gmerchantcenterpro'}"
														onclick="check = confirm('{l s='Are you sure you want to deactivate this rule?' mod='gmerchantcenterpro'}');if(!check)return false;$('#loadingGoogleDiv').show();oGmcPro.hide('bt_rules');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|escape:'htmlall':'UTF-8'}&sUpdateType=one&bActivate=0&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
											{else}
												<a href="#"><i class="btn btn-success btn-mini fa fa-check" title="{l s='Activate' mod='gmerchantcenterpro'}"
														onclick="check = confirm('{l s='Are you sure you want to activate this rule?' mod='gmerchantcenterpro'}');if(!check)return false;$('#loadingGoogleDiv').show();oGmcPro.hide('bt_rules');oGmcPro.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|escape:'htmlall':'UTF-8'}&sUpdateType=one&bActivate=1&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
											{/if}

										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					{else}
						{*<p class="alert alert-info">
						{l s='No exclusion rules added' mod='gmerchantcenterpro'}
					</p>*}
					{/if}
				</div>
			</div>

		{/if}
		{* END - Exclusion *}

		{* BEGIN - Feed data options *}
		{if !empty($sDisplay) && $sDisplay == 'data'}
			<h3 class="subtitle"><i class="fa fa-feed"></i>&nbsp;{l s='Feed data options' mod='gmerchantcenterpro'}</h3>
			<div class="clr_10"></div>
			{if !empty($bUpdate)}
				{include file="`$sConfirmInclude`"}
			{elseif !empty($aErrors)}
				{include file="`$sErrorInclude`"}
			{/if}

			<div class="alert alert-info">
				{l s='The more detailed information you provide to Google, the better your products will rank. Try to include as much information as possible. Please note that some fields are not appropriate for all products. See ' mod='gmerchantcenterpro'}
				<b><a href="https://support.google.com/merchants/answer/7052112?visit_id=1-636342381361070010-4017773094&rd=2&hl={$sCurrentIso|escape:'htmlall':'UTF-8'}" target="_blank">{l s='this Google documentation' mod='gmerchantcenterpro'}</a></b> {l s='for product data specification by country and details.' mod='gmerchantcenterpro'}
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3">
					<b>{l s='About products with combinations' mod='gmerchantcenterpro'}</b> :
				</label>
				<div class="col-xs-12 col-md-3 col-lg-3">
					<select name="bt_prod-combos" id="bt_prod-combos">
						<option value="0" {if empty($bProductCombos)}selected="selected" {/if}>{l s='Export all combinations in a single product' mod='gmerchantcenterpro'}</option>
						<option value="1" {if !empty($bProductCombos)}selected="selected" {/if}>{l s='Export each combination as a product in its own right' mod='gmerchantcenterpro'}</option>
					</select>
				</div>
			</div>
			<div id="bt_prod-combos-opts" style="display: {if !empty($bProductCombos)} block{else} hidden{/if}">

				<div class="form-group">
					<label class="control-label col-xs-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you don\'t want to show the attribute values in your product combination titles, select "NO"' mod='gmerchantcenterpro'}">
							<b>{l s='Include attribute values in product combination titles?' mod='gmerchantcenterpro'}</b>
						</span>
					</label>
					<div class="col-xs-12 col-md-5 col-lg-6">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="bt_include_attribute_values" id="bt_include_attribute_values_on" value="1" {if !empty($bIncludeAttributeValue)}checked="checked" {/if} />
							<label for="bt_include_attribute_values_on" class="radioCheck">
								{l s='Yes' mod='gmerchantcenterpro'}
							</label>
							<input type="radio" name="bt_include_attribute_values" id="bt_include_attribute_values_off" value="0" {if empty($bIncludeAttributeValue)}checked="checked" {/if} />
							<label for="bt_include_attribute_values_off" class="radioCheck">
								{l s='No' mod='gmerchantcenterpro'}
							</label>
							<a class="slide-button btn"></a>
						</span>
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you don\'t want to show the attribute values in your product combination titles, select "NO"' mod='gmerchantcenterpro'}"><span class="icon-question-sign"></span></span>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Activate this option if you have a URL rewriting module that prevents Google from arriving at the page corresponding to the targeted combination' mod='gmerchantcenterpro'}">
							<b>{l s='Include anchors in combination URLs?' mod='gmerchantcenterpro'}</b>
						</span>
					</label>
					<div class="col-xs-12 col-md-5 col-lg-6">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="bt_include_anchor" id="bt_include_anchor_on" value="1" {if !empty($bIncludeAnchor)}checked="checked" {/if} />
							<label for="bt_include_anchor_on" class="radioCheck">
								{l s='Yes' mod='gmerchantcenterpro'}
							</label>
							<input type="radio" name="bt_include_anchor" id="bt_include_anchor_off" value="0" {if empty($bIncludeAnchor)}checked="checked" {/if} />
							<label for="bt_include_anchor_off" class="radioCheck">
								{l s='No' mod='gmerchantcenterpro'}
							</label>
							<a class="slide-button btn"></a>
						</span>
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Activate this option if you have a URL rewriting module that prevents Google from arriving at the page corresponding to the targeted combination' mod='gmerchantcenterpro'}"><span class="icon-question-sign"></span></span>
						&nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/523" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='Should I activate this option?' mod='gmerchantcenterpro'}</a>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-md-3 col-lg-3">
						<b>{l s='Rewrite attribute numeric values with "," or "." in the combination URL?' mod='gmerchantcenterpro'}</b>
					</label>
					<div class="col-xs-12 col-md-5 col-lg-6">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="bt_rewrite-num-attr" id="bt_rewrite-num-attr_on" value="1" {if !empty($bRewriteNumAttrValues)}checked="checked" {/if} />
							<label for="bt_rewrite-num-attr_on" class="radioCheck">
								{l s='Yes' mod='gmerchantcenterpro'}
							</label>
							<input type="radio" name="bt_rewrite-num-attr" id="bt_rewrite-num-attr_off" value="0" {if empty($bRewriteNumAttrValues)}checked="checked" {/if} />
							<label for="bt_rewrite-num-attr_off" class="radioCheck">
								{l s='No' mod='gmerchantcenterpro'}
							</label>
							<a class="slide-button btn"></a>
						</span>
						&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/173" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='Should I activate this option?' mod='gmerchantcenterpro'}</a>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-md-3 col-lg-3">
						<b>{l s='Include the attribute ID into the combination URL?' mod='gmerchantcenterpro'}</b>
					</label>
					<div class="col-xs-12 col-md-5 col-lg-6">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="bt_incl-attr-id" id="bt_incl-attr-id_on" value="1" {if !empty($bUrlInclAttrId)}checked="checked" {/if} />
							<label for="bt_incl-attr-id_on" class="radioCheck">
								{l s='Yes' mod='gmerchantcenterpro'}
							</label>
							<input type="radio" name="bt_incl-attr-id" id="bt_incl-attr-id_off" value="0" {if empty($bUrlInclAttrId)}checked="checked" {/if} />
							<label for="bt_incl-attr-id_off" class="radioCheck">
								{l s='No' mod='gmerchantcenterpro'}
							</label>
							<a class="slide-button btn"></a>
						</span>
						&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/174" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='How to set this option?' mod='gmerchantcenterpro'}</a>
					</div>
				</div>
				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-md-3 col-lg-3">
						<span class="label-tooltip" title="{l s='The "feed id" of a product is built like this: "Shop prefix + Language + product id + separator + combination id". For example, the feed id BMFR17v32 corresponds to the combination of id 32 of the product of id 17 of the French feed of the BM shop. You can here choose the separator between product id and combination id. By default the separator is "v".' mod='gmerchantcenterpro'}"><b>{l s='Choose the separator between product id and combination id' mod='gmerchantcenterpro'}</b></span></label>
					<div class="col-xs-4 col-md-4 col-lg-2">
						<input type="text" name="bt_combo-separator" value="{$sComboSeparator|escape:'htmlall':'UTF-8'}" />
					</div>
					<span class="icon-question-sign label-tooltip" title="{l s='The "feed id" of a product is built like this: "Shop prefix + Language + product id + separator + combination id". For example, the feed id BMFR17v32 corresponds to the combination of id 32 of the product of id 17 of the French feed of the BM shop. You can here choose the separator between product id and combination id. By default the separator is "v".' mod='gmerchantcenterpro'}">&nbsp;</span>
				</div>
			</div>

			{if !empty($aProducts)}
				<div class="form-group">
					<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
					<div class="col-xs-12">
						<p class="alert alert-warning">
							{l s='Note : as it seems that you have defined some product exclusions, if you change the option above, you will have to define again the product exclusions (go in the previous \"Product exclusion rules\" tab to delete your list of products exclusion, save the page and make the list again). Indeed, if you want to export each combination as a product in its own right, the exclusions are to make by combination and no more by product.' mod='gmerchantcenterpro'}
						</p>
					</div>
				</div>
			{/if}

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3">
					<b>{l s='Which description type do you want to use ?' mod='gmerchantcenterpro'}</b>
				</label>
				<div class="col-xs-12 col-md-3 col-lg-3">
					<select name="bt_prod-desc-type">
						{foreach from=$aDescriptionType name=desc key=iKey item=sType}
							<option value="{$iKey|escape:'htmlall':'UTF-8'}" {if $iKey == $iDescType}selected="selected" {/if}>{$sType|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
				&nbsp;&nbsp;
				<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/196" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product description' mod='gmerchantcenterpro'}</a>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3">
					<b>{l s='About product availability' mod='gmerchantcenterpro'}</b> :
				</label>
				<div class="col-xs-12 col-md-3 col-lg-3">
					<select name="bt_incl-stock">
						<option value="1" {if $iIncludeStock == 1}selected="selected" {/if}>{l s='Only indicate products as available IF they are actually in stock' mod='gmerchantcenterpro'}</option>
						<option value="2" {if $iIncludeStock == 2}selected="selected" {/if}>{l s='Always indicate products as available, EVEN IF they are in fact out of stock' mod='gmerchantcenterpro'}</option>
					</select>
				</div>
				<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/213" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product availability' mod='gmerchantcenterpro'}</a>
			</div>

			<div class="form-group">
<label class="control-label col-xs-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Activate this option if, for certain products, you have filled in availability dates and want to indicate that they are available for pre-order ("preorder" tag) or will soon be back in stock ("backorder" tag).' mod='gmerchantcenterpro'}"><b>{l s='Take into account availability date ("preorder" and "backorder" tags)' mod='gmerchantcenterpro'}</b></span>
					</label>
				<div class="col-xs-12 col-md-6 col-lg-7">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="handle_backorder" id="handle_backorder_on" value="1" {if !empty($handleBackOrder)}checked="checked" {/if} />
						<label for="handle_backorder_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="handle_backorder" id="handle_backorder_off" value="0" {if empty($handleBackOrder)}checked="checked" {/if} />
						<label for="handle_backorder_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Activate this option if, for certain products, you have filled in availability dates and want to indicate that they are available for pre-order ("preorder" tag) or will soon be back in stock ("backorder" tag).' mod='gmerchantcenterpro'}"><span class="icon-question-sign"></span></span>&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/577" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about "preorder" and "backorder" tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip" title
						data-original-title="{l s='If you have both EAN13/JAN and UPC codes for some of your products, you can decide to let the module check and use one of these two types of codes in priority over the other. For example, if your shop uses mostly EAN13/JAN code (and uses UPC codes for only some products), you\'ll probably want the module to first check the EAN13/JAN code and use it if it\'s available. However if the EAN13/JAN value is empty, then the module will check and use the UPC code, if it\'s available.' mod='gmerchantcenterpro'}"><b>{l s='Determination of priority GTIN (EAN13/JAN or UPC or ISBN):' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-3 col-lg-3">
					<select name="bt_gtin-pref">
						<option value="ean" {if $sGtinPreference == 'ean'}selected="selected" {/if}>{l s='Check EAN13/JAN code first' mod='gmerchantcenterpro'}</option>
						<option value="upc" {if $sGtinPreference == 'upc'}selected="selected" {/if}>{l s='Check UPC code first' mod='gmerchantcenterpro'}</option>
						<option value="isbn" {if $sGtinPreference == 'isbn'}selected="selected" {/if}>{l s='Check ISBN code first' mod='gmerchantcenterpro'}</option>
					</select>
				</div>
				<div>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you have both EAN13/JAN and UPC codes for some of your products, you can decide to let the module check and use one of these two types of codes in priority over the other. For example, if your shop uses mostly EAN13/JAN code (and uses UPC codes for only some products), you\'ll probably want the module to first check the EAN13/JAN code and use it if it\'s available. However if the EAN13/JAN value is empty, then the module will check and use the UPC code, if it\'s available.' mod='gmerchantcenterpro'}"><span class="icon-question-sign"></span></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/192" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about GTIN codes' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="clr_10"></div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Use this tag for products that are for adults only. Select YES, save the form and then click "Configure the tag for each category"' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include adult tags ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-tag-adult" id="bt_incl-tag-adult_on" value="1" {if !empty($bIncludeTagAdult)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('tag_adult_link', 'tag_adult_link', null, null, true, true);" />
						<label for="bt_incl-tag-adult_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-tag-adult" id="bt_incl-tag-adult_off" value="0" {if empty($bIncludeTagAdult)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('tag_adult_link', 'tag_adult_link', null, null, true, false);" />
						<label for="bt_incl-tag-adult_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Use this tag for products that are for adults only. Select YES, save the form and then click "Configure the tag for each category"' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/222" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about adult tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="form-group" id="tag_adult_link" {if empty($bIncludeTagAdult)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-12 col-md-4 col-lg-4">

					{if !empty($bIncludeTagAdult)}
						<a class="btn btn-md btn-success" href="{$handleTagAdultLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12 col-xs-12">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
					{/if}

				</div>
			</div>

			<div class="clr_10"></div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "YES" if you want to export the cost of goods sold' mod='gmerchantcenterpro'}"><b>{l s='Do you want to export the cost of goods sold?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-tag-cost" id="bt_incl-tag-cost_on" value="1" {if !empty($bIncludeTagCost)}checked="checked" {/if} />
						<label for="bt_incl-tag-cost_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-tag-cost" id="bt_incl-tag-cost_off" value="0" {if empty($bIncludeTagCost)}checked="checked" {/if} />
						<label for="bt_incl-tag-cost_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "YES" if you want to export the cost of goods sold' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/238" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about cost of goods sold' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='In order to export product sizes in your data feed (hightly recommended for clothing), select what feature or(and) attribute(s) define the size of your products.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include product sizes ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<div class="col-xs-12 col-md-4 col-lg-6">
						<select name="bt_incl-size" id="inc_size">
							<option value="" {if $sIncludeSize == ''}selected="selected" {/if}>{l s='No' mod='gmerchantcenterpro'}</option>
							<option value="attribute" {if $sIncludeSize == 'attribute'}selected="selected" {/if}>{l s='Yes : select ATTRIBUTE(S) that define sizes' mod='gmerchantcenterpro'}</option>
							<option value="feature" {if $sIncludeSize == 'feature'}selected="selected" {/if}>{l s='Yes : select FEATURE that define sizes' mod='gmerchantcenterpro'}</option>
							<option value="both" {if $sIncludeSize == 'both'}selected="selected" {/if}>{l s='Yes : select ATTRIBUTE(S) AND FEATURE that define sizes' mod='gmerchantcenterpro'}</option>
						</select>
					</div>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='In order to export product sizes in your data feed (hightly recommended for clothing), select what feature or(and) attribute(s) define the size of your products.' mod='gmerchantcenterpro'}"><span class="icon-question-sign"></span></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/201" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product sizes' mod='gmerchantcenterpro'}</a>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-12 col-md-4 col-lg-3">
				</div>
			</div>

			<div class="form-group" id="div_size_opt_attr" style="display: none;">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-12 col-md-4 col-lg-3">
					<select name="bt_size-opt[attribute][]" multiple="multiple" size="8" id="size_opt_attr">
						<option value="" disabled="disabled" style="color: #aaa;font-weight: bold;">{l s='Attributes (multiple choice)' mod='gmerchantcenterpro'}</option>
						{foreach from=$aAttributeGroups name=attribute key=iKey item=aGroup}
							<option value="{$aGroup.id_attribute_group|escape:'htmlall':'UTF-8'}" {if !empty($aSizeOptions.attribute) && is_array($aSizeOptions.attribute) && in_array($aGroup.id_attribute_group, $aSizeOptions.attribute)}selected="selected" {/if} style="padding-left: 10px;font-weight: bold;">{$aGroup.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>

			<div class="form-group" id="div_size_opt_feat" style="display: none;">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-12 col-md-4 col-lg-3">
					<select name="bt_size-opt[feature][]" size="8" id="size_opt_feat">
						<option value="" disabled="disabled" style="color: #aaa;font-weight: bold;">{l s='Features (one choice)' mod='gmerchantcenterpro'}</option>
						{foreach from=$aFeatures name=feature key=iKey item=aFeature}
							<option value="{$aFeature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($aSizeOptions.feature) && is_array($aSizeOptions.feature) && in_array($aFeature.id_feature, $aSizeOptions.feature)}selected="selected" {/if} style="padding-left: 10px;font-weight: bold;">{$aFeature.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>

			{*use case color*}
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3">
					<span class="label-tooltip" title="{l s='In order to export product colors in your data feed (hightly recommended for clothing), select what feature or(and) attribute(s) define the color of your products.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include product colors ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-4 col-lg-3">
					<select name="bt_incl-color" id="inc_color">
						<option value="" {if $sIncludeColor == ''}selected="selected" {/if}>{l s='No' mod='gmerchantcenterpro'}</option>
						<option value="attribute" {if $sIncludeColor == 'attribute'}selected="selected" {/if}>{l s='Yes : select ATTRIBUTE(S) that define colors' mod='gmerchantcenterpro'}</option>
						<option value="feature" {if $sIncludeColor == 'feature'}selected="selected" {/if}>{l s='Yes : select FEATURE that define colors' mod='gmerchantcenterpro'}</option>
						<option value="both" {if $sIncludeColor == 'both'}selected="selected" {/if}>{l s='Yes : select ATTRIBUTE(S) AND FEATURE that define colors' mod='gmerchantcenterpro'}</option>
					</select>
				</div>
				<div>

					<span class="icon-question-sign label-tooltip" title="{l s='In order to export product colors in your data feed (hightly recommended for clothing), select what feature or(and) attribute(s) define the color of your products.' mod='gmerchantcenterpro'}"></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/199" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product colors' mod='gmerchantcenterpro'}</a>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-12 col-md-4 col-lg-3">
				</div>
			</div>

			<div class="form-group" id="div_color_opt_attr" style="display: none;">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-12 col-md-4 col-lg-3">
					<select name="bt_color-opt[attribute][]" multiple="multiple" size="8" id="color_opt_attr">
						<option value="" disabled="disabled" style="color: #aaa;font-weight: bold;">{l s='Attributes (multiple choice)' mod='gmerchantcenterpro'}</option>
						{foreach from=$aAttributeGroups name=attribute key=iKey item=aGroup}
							<option value="{$aGroup.id_attribute_group|escape:'htmlall':'UTF-8'}" {if !empty($aColorOptions.attribute) && is_array($aColorOptions.attribute) && in_array($aGroup.id_attribute_group, $aColorOptions.attribute)}selected="selected" {/if} style="padding-left: 10px;font-weight: bold;">{$aGroup.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>

			<div class="form-group" id="div_color_opt_feat" style="display: none;">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-12 col-md-4 col-lg-3">
					<select name="bt_color-opt[feature][]" size="8" id="color_opt_feat">
						<option value="" disabled="disabled" style="color: #aaa;font-weight: bold;">{l s='Features (one choice)' mod='gmerchantcenterpro'}</option>
						{foreach from=$aFeatures name=feature key=iKey item=aFeature}
							<option value="{$aFeature.id_feature|escape:'htmlall':'UTF-8'}" {if !empty($aColorOptions.feature) && is_array($aColorOptions.feature) && in_array($aFeature.id_feature, $aColorOptions.feature)}selected="selected" {/if} style="padding-left: 10px;font-weight: bold;">{$aFeature.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3">
					<span class="label-tooltip" title="{l s='A setting that allows you to provide the country from which your product will typically ship. Must be the country ISO code' mod='gmerchantcenterpro'}"><b>{l s='Ships from country' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-1 col-lg-1">
					<input type="text" size="5" name="bt_ships_from" value="{if !empty($shipsFrom)}{$shipsFrom|escape:'htmlall':'UTF-8'}{/if}" />
				</div>
				&nbsp;
				<span class="icon-question-sign label-tooltip" title="{l s='A setting that allows you to provide the country from which your product will typically ship. Must be the country ISO code' mod='gmerchantcenterpro'}"></span>&nbsp;
			</div>


		{/if}
		{* END - Feed data options *}

		{* BEGIN - Apparel *}
		{if !empty($sDisplay) && $sDisplay == 'apparel'}
			<h3 class="subtitle"> <i class="fa fa-bookmark"></i> {l s='Apparel feed options' mod='gmerchantcenterpro'}</h3>

			{if !empty($bUpdate)}
				{include file="`$sConfirmInclude`"}
			{elseif !empty($aErrors)}
				{include file="`$sErrorInclude`"}
			{/if}
			<div class="clr_10"></div>
			<div class="alert alert-info">
				<p><span class="highlight_element">
						<b>{l s='It is strongly recommended that apparel shops include these tags if the information is available.' mod='gmerchantcenterpro'}</b></span>
					{l s='But, these tags can also be useful for other sales areas. Please note that the more information you will provide about your products to Google, the better your products will be ranked in Google Shopping. So when it\'s possible, don\'t hesitate to attribute the following tags to your products.' mod='gmerchantcenterpro'}</p>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the material of the products that are in this category.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include material tags ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-material" id="bt_incl-material_on" value="1" {if !empty($bIncludeMaterial)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('material_link', 'material_link', null, null, true, true);" />
						<label for="bt_incl-material_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-material" id="bt_incl-material_off" value="0" {if empty($bIncludeMaterial)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('material_link', 'material_link', null, null, true, false);" />
						<label for="bt_incl-material_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the material of the products that are in this category.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/205" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about material tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="form-group" id="material_link" {if empty($bIncludeMaterial)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bIncludeMaterial)}
						<a class="btn btn-md btn-success" href="{$handleTagMaterialLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
					{/if}

				</div>
			</div>

			<div class="clr_30"></div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the pattern of the products that are in this category.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include pattern tags ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-pattern" id="bt_incl-pattern_on" value="1" {if !empty($bIncludePattern)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('pattern_link', 'pattern_link', null, null, true, true);" />
						<label for="bt_incl-pattern_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-pattern" id="bt_incl-pattern_off" value="0" {if empty($bIncludePattern)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('pattern_link', 'pattern_link', null, null, true, false);" />
						<label for="bt_incl-pattern_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the pattern of the products that are in this category.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/206" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about pattern tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="form-group" id="pattern_link" {if empty($bIncludePattern)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bIncludePattern)}
						<a class="btn btn-md btn-success" href="{$handleTagPatternLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
					{/if}

				</div>
			</div>

			<div class="clr_30"></div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to select, in the drop and down menu, which Google predefined "gender" value defines the gender for which the products of this category are reserved.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include gender tags ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-gender" id="bt_incl-gender_on" value="1" {if !empty($bIncludeGender)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('gender_link', 'gender_link', null, null, true, true);" />
						<label for="bt_incl-gender_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-gender" id="bt_incl-gender_off" value="0" {if empty($bIncludeGender)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('gender_link', 'gender_link', null, null, true, false);" />
						<label for="bt_incl-gender_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to select, in the drop and down menu, which Google predefined "gender" value defines the gender for which the products of this category are reserved.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/209" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about gender tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="form-group" id="gender_link" {if empty($bIncludeGender)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bIncludeGender)}
						<a class="btn btn-md btn-success" href="{$handleTagGenderLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
					{/if}

				</div>
			</div>

			<div class="clr_30"></div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to select, in the drop and down menu, which Google predefined "age group" value defines the age group for which the products of this category are reserved.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include age group tags ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-age" id="bt_incl-age_on" value="1" {if !empty($bIncludeAge)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('age_group_link', 'age_group_link', null, null, true, true);" />
						<label for="bt_incl-age_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-age" id="bt_incl-age_off" value="0" {if empty($bIncludeAge)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('age_group_link', 'age_group_link', null, null, true, false);" />
						<label for="bt_incl-age_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to select, in the drop and down menu, which Google predefined "age group" value defines the age group for which the products of this category are reserved.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/202" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about age group tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>


			<div class="form-group" id="age_group_link" {if empty($bIncludeAge)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bIncludeAge)}
						<a class="btn btn-md btn-success" href="{$handleTagAgeGroupeLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
					{/if}

				</div>
			</div>

			<div class="clr_30"></div>

			{*size type*}
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to select, in the drop and down menu, which Google predefined "size type" value defines the size type of the products that are in this category.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include size type tags ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-size_type" id="bt_incl-size_type_on" value="1" {if !empty($bSizeType)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('size_type', 'size_type', null, null, true, true);" />
						<label for="bt_incl-size_type_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-size_type" id="bt_incl-size_type_off" value="0" {if empty($bSizeType)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('size_type', 'size_type', null, null, true, false);" />
						<label for="bt_incl-size_type_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to select, in the drop and down menu, which Google predefined "size type" value defines the size type of the products that are in this category.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/220" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about size type tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="form-group" id="size_type" {if empty($bSizeType)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bSizeType)}
						<a class="btn btn-md btn-success" href="{$handleSizeType|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
					{/if}

				</div>
			</div>

			<div class="clr_30"></div>

			{* size system *}
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to select, in the drop and down menu, which Google predefined "size system" value defines the size system used for the products that are in this category.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include size system tags ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-size_system" id="bt_incl-size_system_on" value="1" {if !empty($bSizeSystem)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('size_system', 'size_system', null, null, true, true);" />
						<label for="bt_incl-size_system_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-size_system" id="bt_incl-size_system_off" value="0" {if empty($bSizeSystem)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('size_system', 'size_system', null, null, true, false);" />
						<label for="bt_incl-size_system_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to select, in the drop and down menu, which Google predefined "size system" value defines the size system used for the products that are in this category.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/221" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about size system tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>


			<div class="form-group" id="size_system" {if empty($bSizeSystem)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bSizeSystem)}
						<a class="btn btn-md btn-success" href="{$handleSizeSystem|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
					{/if}
					<div class="clr_15"></div>
				</div>
			</div>
		{/if}
		{* END - Apparel *}

		{* START ADVANCED TAG *}
		{if !empty($sDisplay) && $sDisplay == 'advanced'}
			<h3 class="subtitle"><i class="fa fa-cogs"></i>&nbsp; {l s='Advanced feed options' mod='gmerchantcenterpro'}</h3>

			{if !empty($bUpdate)}
				{include file="`$sConfirmInclude`"}
			{elseif !empty($aErrors)}
				{include file="`$sErrorInclude`"}
			{/if}

			<div class="clr_10"></div>
			<div class="alert alert-info">
				<p><span class="highlight_element">
						<b>{l s='Depending on your sales area, local laws or regulations, you may be required to provide the following tags.' mod='gmerchantcenterpro'}</b></span>
					{l s='In any case, please note that the more information you will provide about your products to Google, the better your products will be ranked in Google Shopping. So when it\'s possible, don\'t hesitate to attribute the following tags to your products.' mod='gmerchantcenterpro'}</p>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-4 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the features that define the certification attributes of the products that are in this category. Indicate the feature that gives the certification authority, the one that gives the certification name and finally the one that gives the certification code.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include certification tags?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-energy" id="bt_incl-energy_on" value="1" {if !empty($bIncludeEnergy)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('energy_link', 'energy_link', null, null, true, true);" />
						<label for="bt_incl-energy_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-energy" id="bt_incl-energy_off" value="0" {if empty($bIncludeEnergy)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('energy_link', 'energy_link', null, null, true, false);" />
						<label for="bt_incl-energy_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the features that define the certification attributes of the products that are in this category. Indicate the feature that gives the certification authority, the one that gives the certification name and finally the one that gives the certification code.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/232" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about certification tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="clr_10"></div>

			<div class="form-group" id="energy_link" {if empty($bIncludeEnergy)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bIncludeEnergy)}
						<a class="btn btn-md btn-success" href="{$handleTagEnergyLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
						<div class="clr_10"></div>
					{/if}

				</div>
			</div>


			<div class="form-group">
				<label class="control-label col-xs-12 col-md-4 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the shipping label of the products that are in this category.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include shipping label tags  ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl-shipping-label" id="bt_incl-shipping-label_on" value="1" {if !empty($bIncludeShippingLabel)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('shipping-label_link', 'shipping-label_link', null, null, true, true);" />
						<label for="bt_incl-shipping-label_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl-shipping-label" id="bt_incl-shipping-label_off" value="0" {if empty($bIncludeShippingLabel)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('shipping-label_link', 'shipping-label_link', null, null, true, false);" />
						<label for="bt_incl-shipping-label_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the shipping label of the products that are in this category.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/235" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about shipping label tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="clr_10"></div>

			<div class="form-group" id="shipping-label_link" {if empty($bIncludeShippingLabel)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bIncludeShippingLabel)}
						<a class="btn btn-md btn-success" href="{$handleTagShippingLabelLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
						<div class="clr_10"></div>
					{/if}

				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-4 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the unit pricing measure of the products that are in this category.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include unit pricing measure tags  ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl_unit_pricing_measure" id="bt_incl_unit_pricing_measure_on" value="1" {if !empty($bIncludeUnitpricingMeasure)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('unit_pricing_measure_link', 'unit_pricing_measure_link', null, null, true, true);oGmcPro.changeSelect('base_unit_price', 'base_unit_price', null, null, true, true);oGmcPro.changeSelect('unit_base_pricing_measure_link', 'unit_base_pricing_measure_link', null, null, true, false);oGmcPro.hide('base_unit_pricing_measure_alert');" />
						<label for="bt_incl_unit_pricing_measure_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl_unit_pricing_measure" id="bt_incl_unit_pricing_measure_off" value="0" {if empty($bIncludeUnitpricingMeasure)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('unit_pricing_measure_link', 'unit_pricing_measure_link', null, null, true, false);oGmcPro.changeSelect('base_unit_price', 'base_unit_price', null, null, true, false);oGmcPro.changeSelect('unit_base_pricing_measure_link', 'unit_base_pricing_measure_link', null, null, true, false);$('#bt_incl_unit_base_pricing_measure_off').prop('checked', true);" />
						<label for="bt_incl_unit_pricing_measure_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the unit pricing measure of the products that are in this category.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/241" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about unit pricing measure tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="clr_10"></div>

			<div class="form-group" id="unit_pricing_measure_link" {if empty($bIncludeUnitpricingMeasure)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bIncludeUnitpricingMeasure)}
						<a class="btn btn-md btn-success" href="{$handleUnitPriceMeasureLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
						<div class="clr_10"></div>
					{/if}

				</div>
			</div>

			<div class="form-group" id="base_unit_price" {if empty($bIncludeUnitpricingMeasure)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-4 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the unit pricing base measure of the products that are in this category.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include unit pricing base measure tags  ?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_incl_unit_base_pricing_measure" id="bt_incl_unit_base_pricing_measure_on" value="1" {if !empty($bIncludeUnitBasepricingMeasure)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('unit_base_pricing_measure_link', 'unit_base_pricing_measure_link', null, null, true, true);" />
						<label for="bt_incl_unit_base_pricing_measure_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_incl_unit_base_pricing_measure" id="bt_incl_unit_base_pricing_measure_off" value="0" {if empty($bIncludeUnitBasepricingMeasure)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('unit_base_pricing_measure_link', 'unit_base_pricing_measure_link', null, null, true, false);" />
						<label for="bt_incl_unit_base_pricing_measure_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the unit pricing base measure of the products that are in this category.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/241" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about unit pricing base measure tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="form-group" id="unit_base_pricing_measure_link" {if empty($bIncludeUnitBasepricingMeasure) ||  empty($bIncludeUnitpricingMeasure)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bIncludeUnitBasepricingMeasure)}
						<a class="btn btn-md btn-success" href="{$handleBaseUnitPricingMeasureLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="base_unit_pricing_measure_alert save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
					{/if}

				</div>
			</div>


			<div class="form-group">
				<label class="control-label col-xs-12 col-md-4 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Use this tag to prevent some products from appearing on certain advertising channels. Select YES, save the tab and then click to configure your tags.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include excluded destination tags?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_excl_dest" id="bt_excl_dest_on" value="1" {if !empty($bExcludedDest)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('excl_dest', 'excl_dest', null, null, true, true);" />
						<label for="bt_excl_dest_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_excl_dest" id="bt_excl_dest_off" value="0" {if empty($bExcludedDest)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('excl_dest', 'excl_dest', null, null, true, false);" />
						<label for="bt_excl_dest_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Use this tag to prevent some products from appearing on certain advertising channels. Select YES, save the tab and then click to configure your tags.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/318" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about excluded destination tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="clr_10"></div>

			<div class="form-group" id="excl_dest" {if empty($bExcludedDest)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bExcludedDest)}
						<a class="btn btn-md btn-success" href="{$handleExcludedDestinationLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
						<div class="clr_10"></div>
					{/if}

				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-4 col-lg-4"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Use this tag to prevent some products from appearing in certain countries. Select YES, save the tab and then click to configure your tags.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to include shopping ads excluded country tags?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_excl_country" id="bt_excl_country_on" value="1" {if !empty($bExcludedCountry)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('excl_country', 'excl_country', null, null, true, true);" />
						<label for="bt_excl_country_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_excl_country" id="bt_excl_country_off" value="0" {if empty($bExcludedCountry)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('excl_country', 'excl_country', null, null, true, false);" />
						<label for="bt_excl_country_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Use this tag to prevent some products from appearing in certain countries. Select YES, save the tab and then click to configure your tags.' mod='gmerchantcenterpro'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/386" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about excluded country tags' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="clr_10"></div>

			<div class="form-group" id="excl_country" {if empty($bExcludedCountry)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">

					{if !empty($bExcludedCountry)}
						<a class="btn btn-md btn-success" href="{$handleExcludedCountryLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='gmerchantcenterpro'}</a>
					{else}
						<div class="clr_10"></div>
						<span class="alert alert-danger col-xs-12" id="save_require">{l s='Please save this page before configuring the tag' mod='gmerchantcenterpro'}</span>
						<div class="clr_10"></div>
					{/if}
				</div>
			</div>

			<div class="clr_30"></div>

			<h3 class="subtitle"><i class="fa fa-pause"></i>&nbsp; {l s='Stop showing products for a short time' mod='gmerchantcenterpro'}</h3>

			<div class="alert alert-info">
				{l s='You can temporarily stop certain products from showing in your ads and free listings by using the configuration below. Please note that this pause cannot exceed 14 days (otherwise, please use the "excluded destination" tag above). To reinstate these products, simply delete them from the list below or deactivate the tag. To know more, please read' mod='gmerchantcenterpro'}&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/547" target="_blank">{l s='our FAQ' mod='gmerchantcenterpro'}</a>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-4">
					<span class="label-tooltip" title="{l s='Select your use of the "pause" tag. You can choose not to activate it, to pause the products selected below on all distribution channels (ads + free listings) or only in your ads.' mod='gmerchantcenterpro'}"><b>{l s='Do you want to pause showing certain products?' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-6 col-lg-5">
					<select name="bt_tag_pause">
						<option value="0" {if $tagPause == 0}selected="selected" {/if}>{l s='No : do not activate the "Pause" tag' mod='gmerchantcenterpro'}</option>
						<option value="all" {if $tagPause == 'all'}selected="selected" {/if}>{l s='Yes : pause the products below for all channels (ads + free listings)' mod='gmerchantcenterpro'}</option>
						<option value="ads" {if $tagPause == 'ads'}selected="selected" {/if}>{l s='Yes : pause the products below only for ads locations' mod='gmerchantcenterpro'}</option>
					</select>
				</div>
				<span class="icon-question-sign label-tooltip" title="{l s='Select your use of the "pause" tag. You can choose not to activate it, to pause the products selected below on all distribution channels (ads + free listings) or only in your ads.' mod='gmerchantcenterpro'}"></span>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-4">
					<span class="label-tooltip" title="{l s='Start typing a product name and select it in the list that appears' mod='gmerchantcenterpro'}"><b>{l s='Enter the products to be paused:' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-6 col-lg-3">
					<input type="text" size="5" id="bt_search-p-pause-tag" name="bt_search-p-pause-tag" value="" placeholder="{l s='Start typing a product name' mod='gmerchantcenterpro'}" />
				</div>

				<span class="icon-question-sign label-tooltip" title="{l s='Start typing a product name and select it in the list that appears' mod='gmerchantcenterpro'}"></span>
			</div>

			<input type="hidden" value="{if !empty($sProductPauseIds)}{$sProductPauseIds|escape:'htmlall':'UTF-8'}{else}{/if}" id="hiddenProductPauseIds" name="hiddenProductPauseIds" />
			<input type="hidden" value="{if !empty($sProductPauseNames)}{$sProductPauseNames|escape:'htmlall':'UTF-8'}{/if}" id="hiddenProductPauseNames" name="hiddenProductFeedNames" />

			<div class="clr_15"></div>

			<h4>{l s='Products to be paused:' mod='gmerchantcenterpro'}</h4>

			<div class="clr_hr"></div>
			<div class="clr_10"></div>

			<div class="col-xs-12 col-md-5 col-lg-4">
				<table id="bt_product-list-paused-products" border="0" cellpadding="2" cellspacing="2" class="table table-striped table-resposive">
					<thead>
						<tr>
							<th>{l s='Product(s)' mod='gmerchantcenterpro'}</th>
							<th>{l s='Delete' mod='gmerchantcenterpro'}</th>
						</tr>
					</thead>
					<tbody id="bt_paused-products">
						{if !empty($aProductsPaused)}
							{foreach name=product key=key item=aProduct from=$aProductsPaused}
								<tr>
									<td>{$aProduct.id|escape:'htmlall':'UTF-8'}{if isset($aProduct.attrId) && $aProduct.attrId != 0} (attr: {$aProduct.attrId|escape:'htmlall':'UTF-8'}){/if} - {$aProduct.name|escape:'htmlall':'UTF-8'}</td>
									<td><span class="icon-trash" style="cursor:pointer;" onclick="javascript: oGmcPro.deletePausedProduct('{$aProduct.stringIds|escape:'htmlall':'UTF-8'}');"></span></td>
								</tr>
							{/foreach}
						{else}
							<tr id="bt_paused-products-no-products">
								<td colspan="2">{l s='No products' mod='gmerchantcenterpro'}</td>
							</tr>
						{/if}
					</tbody>
				</table>
			</div>
		{/if}
		{* END ADVANCED TAG *}

		{* BEGIN - Taxes and shipping fees *}
		{if !empty($sDisplay) && $sDisplay == 'tax'}

			{if !empty($bUpdate)}
				{include file="`$sConfirmInclude`"}
			{elseif !empty($aErrors)}
				{include file="`$sErrorInclude`"}
			{/if}

			<ul class="nav nav-tabs" id="myTab">
				<li class=" active">
					<a data-toggle="tab" href="#tax"><i class="fa fa-dollar"></i>&nbsp;{l s='Tax management' mod='gmerchantcenterpro'}</a>
				</li>
				<li class="nav-item">
					<a data-toggle="tab" href="#shipping"><i class="fa fa-truck"></i>&nbsp;{l s='Shipping cost management' mod='gmerchantcenterpro'}</a>
				</li>
				<li class="nav-item">
					<a data-toggle="tab" href="#free-shipping"><i class="fa fa-truck"></i>&nbsp;{l s='Free shipping management' mod='gmerchantcenterpro'}</a>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane active" id="tax">
					<div class="clr_20"></div>
					<div class="form-group">
						<label class="control-label col-xs-12 col-md-4 col-lg-4">
							<span class="label-tooltip" title="{l s='Select the feeds for which you want product prices to be displayed INCLUDING taxes' mod='gmerchantcenterpro'}"><b>{l s='INCLUDE taxes in product prices for the following feeds:' mod='gmerchantcenterpro'}</b></span></label>
						<div class="col-xs-12 col-md-4 col-lg-3">
							{if !empty($aFeedTax)}
								<table border="0" cellpadding="2" cellspacing="2" class="table table-striped">
									<tr>
										<th>{l s='Language / country' mod='gmerchantcenterpro'}</th>
										<th>{l s=' ' mod='gmerchantcenterpro'}</th>
									</tr>
									{foreach from=$aFeedTax name=feed key=iKey item=aTax}
										<tr>
											<td>{$aTax.lang|escape:'htmlall':'UTF-8'}-{$aTax.country|escape:'htmlall':'UTF-8'}</td>
											<td class="center">
												<input type="hidden" id="bt_feed-tax-{$aTax.lang|lower|escape:'htmlall':'UTF-8'}_{$aTax.country|escape:'htmlall':'UTF-8'}" name="bt_feed-tax-hidden[]" value="{$aTax.lang|lower|escape:'htmlall':'UTF-8'}_{$aTax.country|escape:'htmlall':'UTF-8'}" {if !empty($aTax.tax)}checked="checked" {/if} />
												<input type="checkbox" id="bt_feed-tax-{$aTax.lang|lower|escape:'htmlall':'UTF-8'}_{$aTax.country|escape:'htmlall':'UTF-8'}" name="bt_feed-tax[]" value="{$aTax.lang|lower|escape:'htmlall':'UTF-8'}_{$aTax.country|escape:'htmlall':'UTF-8'}" {if !empty($aTax.tax)}checked="checked" {/if} />
											</td>
										</tr>
									{/foreach}
								</table>
							{else}
								<div class="alert alert-warning">
									{l s='Either you just updated your configuration by deactivating the advanced file security feature (in which case, please reload the page), or there are no files because no valid languages / currencies / countries are available according to the Google\'s requirements' mod='gmerchantcenterpro'}.
								</div>
							{/if}
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-md-3 col-lg-4">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Be careful, this will only work if you have a module that changes the tax applied according to geolocation, and if you have correctly filled in the tax rules in your PrestaShop back-office.' mod='gmerchantcenterpro'}"><b>{l s='Adjust tax according to geolocation?' mod='gmerchantcenterpro'}</b></span></label>
						<div class="col-xs-12 col-md-5 col-lg-6">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_geoloc" id="bt_geoloc_on" value="1" {if !empty($btGeoloc)}checked="checked" {/if} />
								<label for="bt_geoloc_on" class="radioCheck">
									{l s='Yes' mod='gmerchantcenterpro'}
								</label>
								<input type="radio" name="bt_geoloc" id="bt_geoloc_off" value="0" {if empty($btGeoloc)}checked="checked" {/if} />
								<label for="bt_geoloc_off" class="radioCheck">
									{l s='No' mod='gmerchantcenterpro'}
								</label>
								<a class="slide-button btn"></a>
							</span>
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Be careful, this will only work if you have a module that changes the tax applied according to geolocation, and if you have correctly filled in the tax rules in your PrestaShop back-office.' mod='gmerchantcenterpro'}"><span class="icon-question-sign"></span></span>&nbsp;&nbsp;&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/578" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about tax geolocation' mod='gmerchantcenterpro'}</a>
						</div>
					</div>

					<div class="navbar navbar-default navbar-fixed-bottom text-center">
						<div class="col-xs-12">
							<button class="btn btn-submit" onclick="oGmcPro.form('bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, {if empty($sDisplay) || (!empty($sDisplay) && ($sDisplay == 'export' || $sDisplay == 'data'))}oFeedSettingsCallBack{else}null{/if}, 'Feed{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedDiv');return false;"></i>{l s='Save' mod='gmerchantcenterpro'}</button>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="shipping">
					<div class="clr_20"></div>
					<div class="form-group">
						<label class="control-label col-xs-12 col-md-3 col-lg-4"><b>{l s='Do you want to include the dimensions of the package?' mod='gmerchantcenterpro'}</b></label>
						<div class="col-xs-12 col-md-5 col-lg-6">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_manage-dimension" id="bt_manage-dimension_on" value="1" {if !empty($bDimensionUse)}checked="checked" {/if} />
								<label for="bt_manage-dimension_on" class="radioCheck">
									{l s='Yes' mod='gmerchantcenterpro'}
								</label>
								<input type="radio" name="bt_manage-dimension" id="bt_manage-dimension_off" value="0" {if empty($bDimensionUse)}checked="checked" {/if} />
								<label for="bt_manage-dimension_off" class="radioCheck">
									{l s='No' mod='gmerchantcenterpro'}
								</label>
								<a class="slide-button btn"></a>
							</span>
							<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/452" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about the dimensions of the package' mod='gmerchantcenterpro'}</a>
						</div>
					</div>

					<div class="clr_20"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-md-4 col-lg-4"><b>{l s='Do you want the module to handle shipping fees?' mod='gmerchantcenterpro'}</b></label>
						<div class="col-xs-12 col-md-5 col-lg-3">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_manage-shipping" id="bt_manage-shipping_on" value="1" {if !empty($bShippingUse)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('bt_conf-shipping', 'bt_conf-shipping', null, null, true, true);" />
								<label for="bt_manage-shipping_on" class="radioCheck">
									{l s='Yes' mod='gmerchantcenterpro'}
								</label>
								<input type="radio" name="bt_manage-shipping" id="bt_manage-shipping_off" value="0" {if empty($bShippingUse)}checked="checked" {/if} onclick="javascript: oGmcPro.changeSelect('bt_conf-shipping', 'bt_conf-shipping', null, null, true, false);" />
								<label for="bt_manage-shipping_off" class="radioCheck">
									{l s='No' mod='gmerchantcenterpro'}
								</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>

					<div id="bt_conf-shipping" {if empty($bShippingUse)}style="display: none;" {/if}>
						<div class="alert alert-info">
							{l s='Please select below the corresponding default carrier for each country, check the box if you do not want to apply taxes on shipping costs or if you want to offer shipping costs. You also have the option to enter a minimum product price (including taxes) above which shipping is free.' mod='gmerchantcenterpro'}&nbsp;:
						</div>

						{if !empty($aShippingCarriers)}
							<table class="table">
								<thead>
									<th class="text-center bt_tr_header">{l s='Country' mod='gmerchantcenterpro'}</th>
									<th class="text-center bt_tr_header">{l s='Carrier' mod='gmerchantcenterpro'}</th>
									<th class="text-center bt_tr_header">{l s='Apply free shipping if the product price (incl. taxes) is higher than:' mod='gmerchantcenterpro'}</th>
									<th class="bt_tr_header center">{l s='Do not apply taxes on shipping costs' mod='gmerchantcenterpro'}</th>
									<th class="bt_tr_header center">{l s='Apply free shipping' mod='gmerchantcenterpro'}</th>
								</thead>
								<tbody>
									{foreach from=$aShippingCarriers name=shipping key=sCountry item=aShipping}
										<tr>
											<td class="text-center">{$sCountry|escape:'htmlall':'UTF-8'} </td>
											<td class="text-center">
												<select class="text-center col-xs-12" name="bt_ship-carriers[{$sCountry|escape:'htmlall':'UTF-8'}]">
													{foreach from=$aShipping.carriers name=carrier key=iKey item=aCarrier}
														<option {if $aCarrier.id_carrier == $aShipping.shippingCarrierId}selected=selected{/if} value="{$aCarrier.id_carrier|escape:'htmlall':'UTF-8'}">{$aCarrier.name|escape:'htmlall':'UTF-8'}</option>
													{/foreach}
												</select>
											</td>
											<td class="center">
												<input type="text" name="bt_ship-carriers_free_product_price[{$sCountry|escape:'htmlall':'UTF-8'}]" value="{$aShipping.productFree|escape:'htmlall':'UTF-8'}" placeholder="{l s='Enter the min price (incl. taxes)' mod='gmerchantcenterpro'}" />
											</td>
											<td class="center">
												<input type="checkbox" name="bt_ship-carriers_no_tax[{$sCountry|escape:'htmlall':'UTF-8'}]" {if !empty($aShipping.noTaxCarrier)} checked {/if} />
											</td>
											<td class="center">
												<input type="checkbox" name="bt_ship-carriers_free[{$sCountry|escape:'htmlall':'UTF-8'}]" {if !empty($aShipping.free)} checked {/if} />
											</td>
										</tr>
									{/foreach}
								</tbody>
							</table>
						{else}
							<div class="alert alert-warning">
								{l s='There isn\'t any carrier available' mod='gmerchantcenterpro'}
								<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/51" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='Click here to get more information' mod='gmerchantcenterpro'}</a>
							</div>
							<div class="clr_15"></div>
						{/if}
					</div>

					<div class="navbar navbar-default navbar-fixed-bottom text-center">
						<div class="col-xs-12">
							<button class="btn btn-submit" onclick="oGmcPro.form('bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, {if empty($sDisplay) || (!empty($sDisplay) && ($sDisplay == 'export' || $sDisplay == 'data'))}oFeedSettingsCallBack{else}null{/if}, 'Feed{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedDiv', );return false;"></i>{l s='Save' mod='gmerchantcenterpro'}</button>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="free-shipping">

					<div class="form-group">
						<div class="clr_10"></div>
						<div class="alert alert-info">
							{l s='If you want to apply free shipping to certain products, regardless of the country of shipment, please use the options below. To apply free shipping for a specific country, go to the previous tab : you will be able to manage the shipping costs by feed.' mod='gmerchantcenterpro'}
						</div>
						<label class="control-label col-xs-12 col-md-4 col-lg-4">
							<span class="label-tooltip" title="{l s='If the price of the product (excluding taxes and discounts) is higher than this value, shipping will be free.' mod='gmerchantcenterpro'}"><b>{l s='Apply free shipping, regardless of the country, if the product price (excl. tax) is higher than:' mod='gmerchantcenterpro'}</b></span></label>
						<div class="col-xs-12 col-md-1 col-lg-1">
							<input type="text" size="5" name="bt_free_shipping_price" value="{if !empty($freeShippingPrice)}{$freeShippingPrice|floatval}{/if}" />
						</div>
						<p>{l s='Excluding taxes and discounts' mod='gmerchantcenterpro'}&nbsp;&nbsp;<span class="icon-question-sign label-tooltip" title="{l s='If the price of the product (excluding taxes and discounts) is higher than this value, shipping will be free.' mod='gmerchantcenterpro'}"></span></p>
					</div>

					<div class="clr_20"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-md-4 col-lg-4">
							<span class="label-tooltip" title="{l s='Start typing a product name and select it in the list that appears' mod='gmerchantcenterpro'}"><b>{l s='Enter the products for which you want to offer free shipping, regardless of the country:' mod='gmerchantcenterpro'}</b></span></label>
						<div class="col-xs-12 col-md-3 col-lg-2">
							<input type="text" size="5" id="bt_search-p-free-shipping" name="bt_search-p-free-shipping" value="" placeholder="{l s='Start typing a product name' mod='gmerchantcenterpro'}" />
						</div>
						&nbsp;<span class="icon-question-sign label-tooltip" title="{l s='Start typing a product name and select it in the list that appears' mod='gmerchantcenterpro'}">&nbsp;</span>
					</div>

					<input type="hidden" value="{if !empty($sProductFreeShippingIds)}{$sProductFreeShippingIds|escape:'htmlall':'UTF-8'}{else}{/if}" id="hiddenProductFreeShippingIds" name="hiddenProductFreeShippingIds" />
					<input type="hidden" value="{if !empty($sProductFreeShippingNames)}{$sProductFreeShippingNames|escape:'htmlall':'UTF-8'}{/if}" id="hiddenProductFreeShippingNames" name="hiddenProductFeedNames" />

					<div class="clr_15"></div>

					<h4>{l s='Products with free shipping costs:' mod='gmerchantcenterpro'}</h4>

					<div class="clr_hr"></div>
					<div class="clr_10"></div>

					<div class="col-xs-12 col-md-5 col-lg-4">
						<table id="bt_product-list-free-shipping" cellpadding="2" cellspacing="2" class="table table-striped table-resposive">
							<thead>
								<tr>
									<th>{l s='Product(s)' mod='gmerchantcenterpro'}</th>
									<th>{l s='Delete' mod='gmerchantcenterpro'}</th>
								</tr>
							</thead>
							<tbody id="bt_free-shipping-products">
								{if !empty($aProductsFreeShipping)}
									{foreach name=product key=key item=aProduct from=$aProductsFreeShipping}
										<tr>
											<td>{$aProduct.id|escape:'htmlall':'UTF-8'}{if isset($aProduct.attrId) && $aProduct.attrId != 0} (attr: {$aProduct.attrId|escape:'htmlall':'UTF-8'}){/if} - {$aProduct.name|escape:'htmlall':'UTF-8'}</td>
											<td><span class="icon-trash" style="cursor:pointer;" onclick="javascript: oGmcPro.deleteProductFreeShipping('{$aProduct.stringIds|escape:'htmlall':'UTF-8'}');"></span></td>
										</tr>
									{/foreach}
								{else}
									<tr id="bt_free-shipping-no-products">
										<td colspan="2">{l s='No products' mod='gmerchantcenterpro'}</td>
									</tr>
								{/if}
							</tbody>
						</table>
					</div>

					<div class="navbar navbar-default navbar-fixed-bottom text-center">
						<div class="col-xs-12">
							<button class="btn btn-submit" onclick="oGmcPro.form('bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, {if empty($sDisplay) || (!empty($sDisplay) && ($sDisplay == 'export' || $sDisplay == 'data'))}oFeedSettingsCallBack{else}null{/if}, 'Feed{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedDiv');return false;"></i>{l s='Save' mod='gmerchantcenterpro'}</button>
						</div>
					</div>
				</div>
			</div>
		{/if}
		{* END - Taxes and shipping fees *}

		{if $sDisplay != 'exclusion' && $sDisplay != 'tax'}

			<div class="navbar navbar-default navbar-fixed-bottom text-center">
				<div class="col-xs-12">
					<button class="btn btn-submit" onclick="oGmcPro.form('bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, {if empty($sDisplay) || (!empty($sDisplay) && ($sDisplay == 'export' || $sDisplay == 'data'))}oFeedSettingsCallBack{else}null{/if}, 'Feed{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedDiv', false, 2);return false;"></i>{l s='Save' mod='gmerchantcenterpro'}</button>
				</div>
			</div>
		{/if}
	</form>
</div>
{literal}
	<script type="text/javascript">
		$(document).ready(function() {

		{/literal}
		{if !empty($sDisplay) && $sDisplay == 'tax'}
			{literal}
				oGmcPro.aParamsAutcomplete = {sInputSearch : '#bt_search-p-free-shipping', sExcludeNoProducts : '#bt_free-shipping-no-products', sExcludeProducts : '#bt_free-shipping-products', sHiddenProductNames : '#hiddenProductFreeShippingNames' , sHiddenProductIds : '#hiddenProductFreeShippingIds'};
				// autocomplete
				oGmcPro.autocomplete('{/literal}{$sURI|escape:'javascript':'UTF-8'}&sAction={$aQueryParams.searchProduct.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.searchProduct.type|escape:'htmlall':'UTF-8'}{literal}', '#bt_search-p-free-shipping');
				{/literal}{/if}{literal}

				//bootstrap components init
			{/literal}
			{if !empty($sDisplay) && $sDisplay == 'advanced'}
				{literal}
					oGmcPro.aParamsAutcomplete = {sInputSearch : '#bt_search-p-pause-tag', sExcludeNoProducts : '#bt_paused-products-no-products', sExcludeProducts : '#bt_paused-products', sHiddenProductNames : '#hiddenProductPauseNames' , sHiddenProductIds : '#hiddenProductPauseIds'};
					// autocomplete
					oGmcPro.autocompletePausedProducts('{/literal}{$sURI|escape:'javascript':'UTF-8'}&sAction={$aQueryParams.searchSimpleProduct.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.searchSimpleProduct.type|escape:'htmlall':'UTF-8'}{literal}', '#bt_search-p-pause-tag');
					{/literal}{/if}{literal}

					//bootstrap components init
				{/literal}
			});

			{if !empty($bAjaxMode)}
				{literal}
					$('.label-tooltip, .help-tooltip').tooltip();
					oGmcPro.runMainFeed();
					{/literal}{/if}{literal}

					// handle export type
					$("#bt_prod-combos").bind('change', function(event) {

						$("#bt_prod-combos option:selected").each(function() {
							switch ($(this).val()) {
								case '0':
									$("#bt_prod-combos-opts").hide();
									break;
								case '1':
									$("#bt_prod-combos-opts").show();
									break;
								default:
									$("#bt_prod-combos-opts").hide();
									break;
							}
						});
					}).change();
				</script>
			{/literal}