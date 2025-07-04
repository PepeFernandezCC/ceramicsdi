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
{if isset($aTmpRules) && !empty($aTmpRules)}
	<div id="gmc">

		<div class="clr_10"></div>
		<div class="clr_hr"></div>
		<div class="clr_10"></div>

		{assign var='nbItemsPerLine' value=3}

		<div class="panel-group">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#collapse1"><i class="fa fa-cogs"></i>&nbsp;<span class="badge badge-success">{if isset($aTmpRules)}{$aTmpRules|@count|escape:'htmlall':'UTF-8'}{/if}</span>  <b>{l s='exclusion rule(s) based on the element type(s) below - (Click to see detail)' mod='gmerchantcenterpro'}</b> </a>
					</h4>
				</div>
				<div id="collapse1" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="row">
							{if isset($aTmpRules)}
							{foreach from=$aTmpRules item=sTmpRules key=sKey name=Rules}
								{math equation="(iteration%perLine)" iteration=$smarty.foreach.Rules.iteration perLine=$nbItemsPerLine assign=totModulo}
								<div class="col-md-3">
									<div class="panel panel-btRules">
										<div class="panel-heading">
											<h3 class="panel-title">
											{if isset($sTmpRules.sType)}
											{assign var="type" value=$sTmpRules.sType}
												<i class="fa fa-thumb-tack text-success pull-left"></i>{if isset($sTmpRules.data.$type)}{$sTmpRules.data.$type|escape:'htmlall':'UTF-8'}{/if}
											{/if}
											</h3>
											<div class="clr_5"></div>
											<div class="clr_hr"></div>
											<div class="clr_5"></div>
											<h5 class="text-center">
												{if isset($sTmpRules.filter)}
												{foreach from=$sTmpRules.filter item=sDetail key=sKey name=RulesDetail}
													{if $sKey != 'iNumberOfProducts' && $sKey != 'iCheckedTreeElem' && $sKey != 'iCatId'
														&& $sKey != 'iManufacturerId' && $sKey != 'iSupplierId'  && $sKey != 'aCatName'
														&& $sKey != 'aManufacturerName' && $sKey != 'aSupplierName'}
													{/if}
												{/foreach}
												{/if}
											</h5>
											<div class="row">
												<div class="col-xs-6">
													{if isset($sTmpRules.filter)}
													{foreach from=$sTmpRules.filter item=sDetail key=sKey name=RulesDetail}
														{if $sKey == 'filter_2'}
															<span class="badge badge-success label-tooltip" title="{l s='Number of elements affected by this exclusion rule' mod='gmerchantcenterpro'}"><i class="fa fa-check-square-o"></i> {$sDetail|escape:'htmlall':'UTF-8'}</span>
														{/if}
														{*Use case for tree element*}
														{if $sKey == 'iCheckedTreeElem'}
															<span class="badge badge-success label-tooltip" title="{l s='Number of elements affected by this exclusion rule' mod='gmerchantcenterpro'}"><i class="fa fa-check-square-o"></i> {$sDetail|escape:'htmlall':'UTF-8'}</span>
														{/if}
													{/foreach}
													{/if}
												</div>
												<div class="col-xs-6">
													{if isset($sTmpRules.filter)}
													{foreach from=$sTmpRules.filter item=sDetail key=sKey name=RulesDetail}
														{if $sKey == 'iNumberOfProducts'}
															<span class="badge badge-info pull-right label-tooltip" title="{l s='Number of products affected by this exclusion rule' mod='gmerchantcenterpro'}"><i class="icon icon-AdminCatalog"></i> {if isset($aProducts)}{$aProducts|@count|escape:'htmlall':'UTF-8'}{/if} </span>
														{/if}
													{/foreach}
													{/if}
												</div>
											</div>

											<div class="col-xs-12">
												{if isset($sTmpRules.filter.aCatName) && isset($sTmpRules.filter.iCatId) && !empty($sTmpRules.filter.aCatName) && !empty($sTmpRules.filter.iCatId)}
													<div class="clr_5"></div>
													<table class="table">
														<tr>
															<td><b>{l s='Categories for this rule'  mod='gmerchantcenterpro'}</b></td>
														</tr>
														{foreach from=$sTmpRules.filter.aCatName item=sCatName key=sKey name=RulesDetail}
																<tr>
																	<td>{$sCatName|escape:'htmlall':'UTF-8'}</td>
																</tr>
														{/foreach}
													</table>
												{/if}

												{if isset($sTmpRules.filter.aManufacturerName) && isset($sTmpRules.filter.iManufacturerId) && !empty($sTmpRules.filter.aManufacturerName) && !empty($sTmpRules.filter.iManufacturerId)}
													<div class="clr_5"></div>
													<table class="table">
														<tr>
															<td><b>{l s='Manufacturers for this rule'  mod='gmerchantcenterpro'}</b></td>
														</tr>
														{foreach from=$sTmpRules.filter.aManufacturerName item=sManufacturerName key=sKey name=RulesDetail}
																<tr>
																	<td>{$sManufacturerName|escape:'htmlall':'UTF-8'}</td>
																</tr>
														{/foreach}
													</table>
												{/if}

												{if isset($sTmpRules.filter.aSupplierName) && isset($sTmpRules.filter.iSupplierId) && !empty($sTmpRules.filter.aSupplierName) && !empty($sTmpRules.filter.iSupplierId)}
													<div class="clr_5"></div>
													<table class="table">
														<tr>
															<td><b>{l s='Suppliers for this rule' mod='gmerchantcenterpro'}</b></td>
														</tr>
														{foreach from=$sTmpRules.filter.aSupplierName item=sSupplierName key=sKey name=RulesDetail}
																<tr>
																	<td>{$sSupplierName|escape:'htmlall':'UTF-8'}</td>
																</tr>
														{/foreach}
													</table>
												{/if}
											</div>

											<div class="clr_5"></div>
											<div class="clr_hr"></div>
											<div class="clr_5"></div>

											<div class="row">
												<a  class="btn btn-mini btn-success pull-left" onclick=" $('#bt-exclusion-type').val('{if isset($sTmpRules.sType)}{$sTmpRules.sType|escape:'htmlall':'UTF-8'}{/if}').trigger('onchange')"><i class="fa fa-edit"></i> </a>
												<a class="btn btn-mini btn-danger pull-right" onclick="$('#loadingGoogleRulesDiv').show();var iRuleId = {if isset($sTmpRules.id)}{$sTmpRules.id|escape:'htmlall':'UTF-8'}{/if};oGmcPro.ajax('{if isset($sURI)}{$sURI|escape:'htmlall':'UTF-8'}{/if}', '{if isset($sCtrlParamName)}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{/if}={if isset($sController)}{$sController|escape:'htmlall':'UTF-8'}{/if}&sAction={if isset($aQueryParams.rulesSummary.action)}{$aQueryParams.rulesSummary.action|escape:'htmlall':'UTF-8'}{/if}&sType={if isset($aQueryParams.rulesSummary.type)}{$aQueryParams.rulesSummary.type|escape:'htmlall':'UTF-8'}{/if}&sTmpRules=true&sDelete=true&iRuleId='+ iRuleId, 'rules', 'rules', null, null, 'loadingGoogleRulesDiv');" id="btn-add-condition"><i class="fa fa-trash"></i> </a>
											</div>
										</div>
									</div>
								</div>
								{if $nbItemsPerLine > 1 && !$smarty.foreach.Rules.last && $totModulo != 0}
									<div class="col-xs-1 text-center">
										<div class="clr_30"></div>
										<i class=" icon text-success icon-2x icon-plus-circle"></i>
									</div>
								{/if}
							{/foreach}
							{/if}
						</div>
					</div>
				</div>
			</div>
		</div>

		{if isset($aProducts) && !empty($aProducts)}
			<div class="panel-group">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" href="#collapse2"><i class="icon icon-AdminCatalog"></i>&nbsp;<span class="badge badge-success">{$aProducts|@count|escape:'htmlall':'UTF-8'}</span>&nbsp;<b>{l s='of distinct product(s) affected - (Click to see detail)' mod='gmerchantcenterpro'}</b></a>
						</h4>
					</div>
					<div id="collapse2" class="panel-collapse collapse">
						<div class="panel-body">
							<table class="table table-bordered">
								<thead>
								<th class="text-center"><b>{l s='id' mod='gmerchantcenterpro'}</b></th>
								<th class="text-center"><b>{l s='Product name' mod='gmerchantcenterpro'}</b></th>
								</thead>
								<tbody>
								{foreach from=$aProducts item=aProdcut key=sKey}
									<tr class="text-center">
										<td> {if isset($aProdcut.id)}{$aProdcut.id|escape:'htmlall':'UTF-8'}{/if} </td>
										<td> {if isset($aProdcut.name)}{$aProdcut.name|escape:'htmlall':'UTF-8'}{/if}</td>
									</tr>
								{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		{/if}
{/if}

<div class="clr_10"></div>
<div class="clr_hr"></div>
<div class="clr_10"></div>

<div class="row">
	<div class="col-xs-6">
		<div id="{if isset($sModuleName)}{$sModuleName|escape:'htmlall':'UTF-8'}{/if}ExclusionRulesError"></div>
	</div>
	<div class="col-xs-6">
		<input {if !isset($aTmpRules) || empty($aTmpRules)} disabled="disabled" {/if}  type="button" name="{if isset($sModuleName)}{$sModuleName|escape:'htmlall':'UTF-8'}{/if}CommentButton" class="pull-left btn btn-success btn-lg" value="{if isset($iRuleId) && !empty($iRuleId)}{l s='Modify' mod='gmerchantcenterpro'}{else}{l s='Validate' mod='gmerchantcenterpro'}{/if}" onclick="oGmcPro.form('bt_form-exclusion-rules', '{if isset($sURI)}{$sURI|escape:'htmlall':'UTF-8'}{/if}', null, 'bt_feed-exclusion-form', 'bt_feed-exclusion-form', false, true, oCustomCallBack, 'ExclusionRules', 'loadingCustomTagDiv');return false;" />
	</div>
</div>

{literal}
<script type="text/javascript">
	{/literal}{if isset($bAjaxMode) && !empty($bAjaxMode)}{literal}

	{/literal}{/if}{literal}
</script>
{/literal}