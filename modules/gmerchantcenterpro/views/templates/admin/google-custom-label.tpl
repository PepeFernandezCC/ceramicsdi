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
	{* USE CASE - edition add/edit custom label mode *}
{else}
	<div id="{$sModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
		<div id="bt_custom-tag" class="col-xs-12 bt_adwords">
			{if !empty($aTag)}
				<h3 class="text-center"><i class="fa fa-tags"></i>&nbsp; {l s='Update a custom label' mod='gmerchantcenterpro'}</h3>
			{else}
				<h3 class="text-center"><i class="fa fa-tags"></i>&nbsp; {l s='Create a custom label' mod='gmerchantcenterpro'}</h3>
			{/if}
			<div class="clr_hr"></div>
			<div class="clr_20"></div>

			<script type="text/javascript">
				{literal}
					var oCustomCallBack = [{
						'name': 'displayGoogleList',
						'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
						'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType={/literal}{$aQueryParams.google.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=adwords',
						'toShow': 'bt_google-settings-adwords',
						'toHide': 'bt_google-settings-adwords',
						'bFancybox': false,
						'bFancyboxActivity': false,
						'sLoadbar': null,
						'sScrollTo': null,
						'oCallBack': {}
					}];
				{/literal}
			</script>

			<form class="form-horizontal" method="post" id="bt_form-custom-tag" name="bt_form-custom-tag" {if $smarty.const._GSR_USE_JS == true}onsubmit="oGmcPro.form('bt_form-custom-tag', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_custom-tag', 'bt_custom-tag', false, true, oCustomCallBack, 'CustomTag', 'loadingCustomTagDiv');return false;" {/if}>
				<input type="hidden" name="{$sCtrlParamName|escape:'htmlall':'UTF-8'}" value="{$sController|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sAction" value="{$aQueryParams.customUpdate.action|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sType" value="{$aQueryParams.customUpdate.type|escape:'htmlall':'UTF-8'}" />
				{if !empty($aTag)}
					<input type="hidden" name="bt_tag-id" value="{$aTag.id_tag|escape:'htmlall':'UTF-8'}" id="tag_id" />
				{/if}

				<div class="alert alert-info">
					{l s='To help you in your custom labels creation, don\'t hesitate to read' mod='gmerchantcenterpro'}&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/111" target="_blank">{l s='our FAQ' mod='gmerchantcenterpro'}</a>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-2">
						<b>{l s='Do you want to activate these labels ?' mod='gmerchantcenterpro'}</b>
					</label>
					<div class="col-xs-3">
						<select name="bt_cl-statut" id="bt_cl-statut">
							<option value="1" {if $bActive == 1} selected="selected" {/if}>{l s='Yes' mod='gmerchantcenterpro'}</option>
							<option value="0" {if $bActive == 0} selected="selected" {/if}>{l s='No' mod='gmerchantcenterpro'}</option>
						</select>
					</div>
				</div>

				<div class="alert alert-warning">
					{l s='Give below a number to this set of custom labels. Be careful not to assign the same number to several active sets of labels! If two sets have the same number, their activation periods must not overlap. See' mod='gmerchantcenterpro'}&nbsp;<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/111" target="_blank">{l s='our FAQ' mod='gmerchantcenterpro'}</a>&nbsp;{l s='for more details.' mod='gmerchantcenterpro'}
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">
						<b>{l s='Custom label number' mod='gmerchantcenterpro'}</b>
					</label>
					<div class="col-xs-3">
						<select name="bt_cl_association" id="bt_cl-statut">
							{foreach from=$labelPosition key=myKey item=position}
								<option value="{$position|escape:'htmlall':'UTF-8'}" {if $customLabelSetPosition == $position} selected {/if}>{$position|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-2">
						<span><b>{l s='Configuration type' mod='gmerchantcenterpro'}</b></span>
					</label>
					<div class="col-xs-3">
						<select name="bt_cl-type" id="bt_cl-type">
							{foreach from=$aCustomLabelType key=myKey item=CustomLabelType}
								{if !empty($aTag.type)}
									<option value="{$myKey|escape:'htmlall':'UTF-8'}" {if $myKey == $aTag.type|escape:'htmlall':'UTF-8'} selected="selected" {/if}>{$CustomLabelType|escape:'htmlall':'UTF-8'}</option>
								{else}
									<option value="{$myKey|escape:'htmlall':'UTF-8'}">{$CustomLabelType|escape:'htmlall':'UTF-8'}</option>
								{/if}
							{/foreach}
						</select>
					</div>
				</div>

				<div class="form-group" id="optionplus">
					<label class="control-label col-xs-2">
						<b>{l s='Value' mod='gmerchantcenterpro'}</b>
					</label>
					<div class="col-xs-3">
						<div id="gmcp_infobox_dynamique_cat">
							<p class="alert alert-info col-xs-12">
								{l s='For each product, the value of the custom label will be its default category name.' mod='gmerchantcenterpro'}<br />
								{l s='The "Value" field below only allows you to give a name to the set of custom labels you\'re going to create.  It also allows you to locate this set in the custom labels list' mod='gmerchantcenterpro'}
								<b><a href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/111" target="_blank">{l s='(see our FAQ).' mod='gmerchantcenterpro'}</a></b>
							<div class="clr_20"></div>
							</p>
						</div>
						<input type="text" id="bt_label-name" name="bt_label-name" value="{if !empty($aTag)}{$aTag.name|escape:'htmlall':'UTF-8'}{/if}" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-2">
						<span><b>{l s='This custom label will be valid until :' mod='gmerchantcenterpro'}</b></span>
					</label>
					<div class="col-xs-3">
						<div class="col-xs-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon icon-calendar"></i> </span>
								<input type="text" name="bt_cl_date_end" id="bt_cl_date_end" class="date-picker" value="{$sDate|escape:'htmlall':'UTF-8'}" />
							</div>
						</div>
					</div>
				</div>

				<div id="bt_add_filter">

					<div class="row">

						<div class="col-xs-12" id="bt_cl_configure_new_products">

							<div class="form-group">
								<label class="control-label col-xs-2">
									<b>{l s='Select a add date from which a product is considered as "new"' mod='gmerchantcenterpro'}</b>
								</label>
								<div class="col-xs-2">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; </span>
										<input type="text" name="bt_cl_dyn_date_start" id="bt_cl_dyn_date_start" class="date-picker" value="{$sDateNewPoduct|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>
							</div>
						</div>

					</div>

					<p class="alert alert-info" id="gmcp_manual_info">{l s='Filters below can be combined.' mod='gmerchantcenterpro'}</p>

					<div>
						<table class="table">
							<thead>
								<tr class="bt_tr_header">
									<th id="bt_cl_configure_cat_header" style="border-right: 1px solid #FFFFFF" class="col-xs-3">
										<div class="row">
											<div class="col-xs-3">
												<b>
													<h4>{l s='Manage by categories' mod='gmerchantcenterpro'}</h4><b>
											</div>
											<div class="col-xs-8 pull-right">
												<span class="pull-right">
													<div class="btn btn-default btn-sm" id="categoryCheck" onclick="return oGmcPro.selectAll('input.categoryBoxLabel', 'check');"><i class="icon-plus-square"></i>&nbsp;{l s='Check All' mod='gmerchantcenterpro'}</div> - <div class="btn btn-default btn-sm" id="categoryUnCheck" onclick="return oGmcPro.selectAll('input.categoryBoxLabel', 'uncheck');"><i class="icon-minus-square"></i>&nbsp;{l s='Uncheck All' mod='gmerchantcenterpro'}</div>
												</span>
											</div>
										</div>
									</th>
									<th id="bt_cl_configure_brand_header" style="border-right: 1px solid #FFFFFF" class="col-xs-3">
										<div class="row">
											<div class="col-xs-3">
												<b>
													<h4>{l s='Manage by brands' mod='gmerchantcenterpro'}</h4><b>
											</div>
											<div class="col-xs-8 pull-right">
												<span class="pull-right">
													<div class="btn btn-default btn-sm" id="brandCheck" onclick="return oGmcPro.selectAll('input.brandBoxLabel', 'check');"><i class="icon-plus-square"></i>&nbsp;{l s='Check All' mod='gmerchantcenterpro'}</div> - <div class="btn btn-default btn-sm" id="brandUnCheck" onclick="return oGmcPro.selectAll('input.brandBoxLabel', 'uncheck');"><i class="icon-minus-square"></i>&nbsp;{l s='Uncheck All' mod='gmerchantcenterpro'}</div>
												</span>
											</div>
										</div>
									</th>
									<th id="bt_cl_configure_supplier_header" style="border-right: 1px solid #FFFFFF" class="col-xs-3">
										<div class="row">
											<div class="col-xs-3">
												<b>
													<h4>{l s='Manage by suppliers' mod='gmerchantcenterpro'}</h4><b>
											</div>
											<div class="col-xs-8 pull-right">
												<span class="pull-right">
													<div class="btn btn-default btn-sm" id="supplierCheck" onclick="return oGmcPro.selectAll('input.supplierBoxLabel', 'check');"><i class="icon-plus-square"></i>&nbsp;{l s='Check All' mod='gmerchantcenterpro'}</div> - <div class="btn btn-default btn-sm" id="supplierUnCheck" onclick="return oGmcPro.selectAll('input.supplierBoxLabel', 'uncheck');"><i class="icon-minus-square"></i>&nbsp;{l s='Uncheck All' mod='gmerchantcenterpro'}</div>
												</span>
											</div>
										</div>
									</th>
									<th id="bt_cl_configure_product_header">
										<div class="row">
											<div class="col-xs-6">
												<b>
													<h4>{l s='Manage by products (individually)' mod='gmerchantcenterpro'}</h4><b>
											</div>
										</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="bt_table_td" id="bt_cl_configure_cat">
										<div id="bt_cat_tree" class="col-xs-12 bt_select_product">
											<table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped" style="width: 100%;">
												{foreach from=$aFormatCat name=category key=iKey item=aCat}
													<tr class="alt_row">
														<td>
															{$aCat.id_category|escape:'htmlall':'UTF-8'}
														</td>
														<td>
															<input type="checkbox" name="bt_category-box[]" class="categoryBoxLabel" id="bt_category-box_{$aCat.iNewLevel|escape:'htmlall':'UTF-8'}" value="{$aCat.id_category|escape:'htmlall':'UTF-8'}" {if !empty($aCat.bCurrent)}checked="checked" {/if} />
														</td>
														<td>
															<i class="icon icon-folder{if !empty($aCat.bCurrent)}-open{/if}" style="margin-left: {$aCat.iNewLevel|escape:'htmlall':'UTF-8'}5px;"></i>&nbsp;<span style="font-size:12px;">{$aCat.name|escape:'htmlall':'UTF-8'}</span>
														</td>
													</tr>
												{/foreach}
											</table>
										</div>
									</td>
									<td class="bt_table_td" id="bt_cl_configure_brand">
										<div class="col-xs-12 bt_select_product">
											<table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped" style="width: 100%;">
												{foreach from=$aFormatBrands name=brand key=iKey item=aBrand}
													<tr class="alt_row">
														<td>
															{$aBrand.id|escape:'htmlall':'UTF-8'}
														</td>
														<td>
															<input type="checkbox" name="bt_brand-box[]" class="brandBoxLabel" id="bt_brand-box_{$aBrand.id|escape:'htmlall':'UTF-8'}" value="{$aBrand.id|escape:'htmlall':'UTF-8'}" {if !empty($aBrand.checked)}checked="checked" {/if} />
														</td>
														<td>
															<i class="icon icon-folder{if !empty($aBrand.checked)}-open{/if}"></i>&nbsp;&nbsp;<span style="font-size:12px;">{$aBrand.name|escape:'htmlall':'UTF-8'}</span>
														</td>
													</tr>
												{/foreach}
											</table>
										</div>
									</td>
									<td class="bt_table_td" id="bt_cl_configure_supplier">
										<div class="col-xs-12 bt_select_product">
											<table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped" style="width: 100%;">
												{foreach from=$aFormatSuppliers name=supplier key=iKey item=aSupplier}
													<tr class="alt_row">
														<td>
															{$aSupplier.id|escape:'htmlall':'UTF-8'}
														</td>
														<td>
															<input type="checkbox" name="bt_supplier-box[]" class="supplierBoxLabel" id="bt_supplier-box_{$aSupplier.id|escape:'htmlall':'UTF-8'}" value="{$aSupplier.id|escape:'htmlall':'UTF-8'}" {if !empty($aSupplier.checked)}checked="checked" {/if} />
														</td>
														<td>
															<i class="icon icon-folder{if !empty($aSupplier.checked)}-open{/if}"></i>&nbsp;&nbsp;<span style="font-size:12px;">{$aSupplier.name|escape:'htmlall':'UTF-8'}</span>
														</td>
													</tr>
												{/foreach}
											</table>
										</div>
									</td>
									<td class="bt_table_td" id="bt_cl_configure_product">
										<div class="col-xs-12">
											<div class="form-group bt_select_product">
												<div class="input-group">
													<span class="input-group-addon"><i class="icon icon-AdminCatalog"></i> </span>
													<input type="text" placeholder="{l s='Start to type a product name' mod='gmerchantcenterpro'}" size="5" id="bt_search-cl-p" name="bt_search-cl-p" value="" />
												</div>
											</div>


											<input type="hidden" value="{if !empty($sProductIds)}{$sProductIds|escape:'htmlall':'UTF-8'}{else}{/if}" id="hiddenProductIds-cl" name="hiddenProductIds-cl" />
											<input type="hidden" value="{if !empty($sProductNames)}{$sProductNames|escape:'htmlall':'UTF-8'}{/if}" id="hiddenProductNames-cl" name="hiddenProductNames-cl" />


											<h4>{l s='List of products :' mod='gmerchantcenterpro'}</h4>

											<div class="clr_hr"></div>
											<div class="clr_10"></div>

											<div class="col-xs-12">
												<table id="bt_product-list-cl" border="0" cellpadding="2" cellspacing="2" class="table table-striped">
													<thead>
														<tr>
															<th>{l s='Product(s)' mod='gmerchantcenterpro'}</th>
															<th>{l s='Delete' mod='gmerchantcenterpro'}</th>
														</tr>
													</thead>
													<tbody id="bt_excluded-products-cl">
														{if !empty($aProducts)}
															{foreach name=product key=key item=aProduct from=$aProducts}
																<tr>
																	<td><input type="hidden" name="selectProduct[]" value="{$aProduct.id|escape:'htmlall':'UTF-8'}">{$aProduct.id|escape:'htmlall':'UTF-8'} - {$aProduct.name|escape:'htmlall':'UTF-8'}</td>
																	<td><span class="icon-trash" style="cursor:pointer;" onclick="oGmcPro.deleteProduct({$aProduct.id|escape:'htmlall':'UTF-8'});"></span></td>
																</tr>
															{/foreach}
														{else}
															<tr id="bt_exclude-no-products-cl">
																<td colspan="2">{l s='No products' mod='gmerchantcenterpro'}</td>
															</tr>
														{/if}
													</tbody>
												</table>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="row">

						<div class="col-xs-12" id="bt_cl_configure_attribute">
							<div class="form-group">
								<label class="control-label col-xs-3">
									<span><b>{l s='Type of feature you want to use' mod='gmerchantcenterpro'}</b></span>
								</label>
								<div class="col-xs-3">
									<select name="dynamic_features_list" id="dynamic_features_list">
										<option value="0"> --- </option>
										{foreach from=$aFeatureAvailable item=feature}
											{if !empty($iFeatureId)}
												<option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if $feature.id_feature == $iFeatureId} selected="selected" {/if}>{$feature.name|escape:'htmlall':'UTF-8'}</option>
											{else}
												<option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
											{/if}
										{/foreach}
									</select>
								</div>
							</div>
						</div>

						<div class="col-xs-12" id="bt_cl_configure_last_order">
							<label class="control-label col-xs-2">
								<b>{l s='Set your order period' mod='gmerchantcenterpro'}</b>
							</label>

							<div class="col-xs-2">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='From' mod='gmerchantcenterpro'} </span>
									<input type="text" name="bt_dyn_last_order_start" id="bt_dyn_last_order_start" class="date-picker" value="{$sStartDateLastOrdered|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>

							<div class="col-xs-2">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='To' mod='gmerchantcenterpro'} </span>
									<input type="text" name="bt_dyn_last_order_end" id="bt_dyn_last_order_end" class="date-picker" value="{$sEndDateLastOrdered|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>
						</div>

						<div class="col-xs-12" id="bt_cl_configure_not_sell">
							<label class="control-label col-xs-2">
								<b>{l s='Set your order period' mod='gmerchantcenterpro'}</b>
							</label>

							<div class="col-xs-2">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='From' mod='gmerchantcenterpro'} </span>
									<input type="text" name="bt_dyn_not_sell_start" id="bt_dyn_not_sell_start" class="date-picker" value="{$startNotSell|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>

							<div class="col-xs-2">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='To' mod='gmerchantcenterpro'} </span>
									<input type="text" name="bt_dyn_not_sell_end" id="bt_dyn_not_sell_end" class="date-picker" value="{$endNotSell|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>
						</div>

						<div class="col-xs-12" id="bt_cl_configure_best_sales">
							<div class="form-group">
								<label class="control-label col-xs-3">
									<span><b>{l s='How do you want to define your best sales ?' mod='gmerchantcenterpro'}</b></span>
								</label>
								<div class="col-xs-3">
									<select name="dynamic_best_sales_unit" id="dynamic_best_sales_unit">
										{foreach from=$aCustomBestType key=myKey item=CustomBestType}
											{if !empty($sUnit)}
												<option value="{$myKey|escape:'htmlall':'UTF-8'}" {if $myKey == $sUnit} selected {/if}>{$CustomBestType|escape:'htmlall':'UTF-8'}</option>
											{else}
												<option value="{$myKey|escape:'htmlall':'UTF-8'}">{$CustomBestType|escape:'htmlall':'UTF-8'}</option>
											{/if}
										{/foreach}
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-xs-3">
									<b>{l s='Quantity / Amount from which the product is a best sale' mod='gmerchantcenterpro'}</b>
								</label>
								<div class="col-xs-3">
									<div class="input-group">
										<input type="text" name="bt_cl_dyn_amount" id="bt_cl_dyn_amount" value="{$fAmount|floatval}" />
										<span class="input-group-addon" id="cl_dyn_unit_help"></span>
									</div>
								</div>
							</div>

							<div class="form-group" id="bt_cl_best_sale_from">
								<div class="col-xs-12">
									<div class="alert alert-info">
										{l s='Set the dates below, knowing that:' mod='gmerchantcenterpro'}
										<br />
										<i class="icon icon-chevron-right"></i>&nbsp;{l s='If you select a start date and an end date, then you\'ll get the best sales over that period' mod='gmerchantcenterpro'}
										<br />
										<i class="icon icon-chevron-right"></i>&nbsp;{l s='If you only select a start date, you\'ll get the best sales from that date onwards' mod='gmerchantcenterpro'}
										<br />
										<i class="icon icon-chevron-right"></i>&nbsp;{l s='If you select an end date only, you\'ll get the best sales up to that date' mod='gmerchantcenterpro'}
									</div>
								</div>
							</div>


							<div class="form-group" id="bt_cl_best_sale_from">

								<label class="control-label col-xs-3">
									<b>{l s='Set your best sales period' mod='gmerchantcenterpro'}</b>
								</label>

								<div class="col-xs-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='From' mod='gmerchantcenterpro'} </span>
										<input type="text" name="bt_dyn_best_sale_start" id="bt_dyn_best_sale_start" class="date-picker" value="{$sStartDate|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>

								<div class="col-xs-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='To' mod='gmerchantcenterpro'} </span>
										<input type="text" name="bt_dyn_best_sale_end" id="bt_dyn_best_sale_end" class="date-picker" value="{$sEndDate|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-12" id="bt_cl_configure_price_range">

							<div class="alert alert-info">
								{l s='Set your price range (without TAX)' mod='gmerchantcenterpro'}
							</div>
							<label class="control-label col-xs-3"></label>

							<div class="col-xs-3">
								<div class="input-group">
									<span class="input-group-addon"> {l s='Min Price' mod='gmerchantcenterpro'} </span>
									<input type="text" name="bt_dyn_min_price" id="bt_dyn_min_price" value="{$fPriceMin|floatval}" />
								</div>
							</div>

							<div class="col-xs-3">
								<div class="input-group">
									<span class="input-group-addon">{l s='Max price' mod='gmerchantcenterpro'} </span>
									<input type="text" name="bt_dyn_max_price" id="bt_dyn_max_price" value="{$fPriceMax|floatval}" />
								</div>
							</div>
						</div>

						<div class="navbar navbar-default navbar-fixed-bottom text-center">
							<div class="col-xs-12">
								<button class="btn btn-success btn-lg" onclick="oGmcPro.form('bt_form-custom-tag', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_custom-tag', 'bt_custom-tag', false, true, oCustomCallBack, 'CustomTag', 'loadingCustomTagDiv');return false;">{if !empty($aTag)}{l s='Modify' mod='gmerchantcenterpro'}{else}{l s='Add' mod='gmerchantcenterpro'}{/if}</button>
								<button class="btn btn-danger btn-lg" value="{l s='Cancel' mod='gmerchantcenterpro'}" onclick="$.fancybox.close();return false;">{l s='Cancel' mod='gmerchantcenterpro'}</button>
							</div>
						</div>
					</div>
			</form>
		</div>
	</div>
	<div id="loadingCustomTagDiv" style="display: none;">
		<div class="alert alert-info">
			<p style="text-align: center !important;"><img src="{$sLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
			<div class="clr_20"></div>
			<p style="text-align: center !important;">{l s='Your configuration updating is in progress...' mod='gmerchantcenterpro'}</p>
		</div>
	</div>
{/if}

{literal}
	<script type="text/javascript">
		// set all elements for autocomplete
		oGmcPro.aParamsAutcomplete = {sInputSearch : '#bt_search-cl-p', sExcludeNoProducts : '#bt_exclude-no-products-cl', sExcludeProducts : '#bt_excluded-products-cl', sHiddenProductNames : '#hiddenProductNames-cl' , sHiddenProductIds : '#hiddenProductIds-cl'};
		//autocomplete
		oGmcPro.autocomplete('{/literal}{$sURI|escape:'javascript':'UTF-8'}&sAction={$aQueryParams.searchProduct.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.searchProduct.type|escape:'htmlall':'UTF-8'}{literal}&isCustomLabel=1', '#bt_search-cl-p' );


		// var for dynamique title
		var sGmcpLabel = '{/literal}{l s='Filter:' mod='gmerchantcenterpro'}{literal}';
		var sCurreny = '{/literal}{$sCurrency|escape:'htmlall':'UTF-8'}{literal}';
		var sSelectElem = '';

		oGmcPro.initShow(aShow);
		oGmcProLabel.initForm('bt_cl-type', sGmcpLabel);
		oGmcProLabel.displayClElement('bt_cl-type', sGmcpLabel);


		$(".date-picker").datepicker({
			dateFormat: 'yy-mm-dd'
		});
	</script>
{/literal}