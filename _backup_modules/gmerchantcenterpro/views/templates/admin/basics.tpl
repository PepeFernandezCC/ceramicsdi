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
<script type="text/javascript">
	{literal}
		var oBasicCallBack = [{
				'name': 'displayFeedListData',
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
			},
			{
				'name': 'displayFeedExport',
				'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
				'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedDisplay.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedDisplay.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=export',
				'toShow': 'bt_feed-settings-export',
				'toHide': 'bt_feed-settings-export',
				'bFancybox': false,
				'bFancyboxActivity': false,
				'sLoadbar': null,
				'sScrollTo': null,
				'oCallBack': {}
			},
			{
				'name': 'displayFeedExclusion',
				'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
				'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedDisplay.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedDisplay.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=exclusion',
				'toShow': 'bt_feed-settings-exclusion',
				'toHide': 'bt_feed-settings-exclusion',
				'bFancybox': false,
				'bFancyboxActivity': false,
				'sLoadbar': null,
				'sScrollTo': null,
				'oCallBack': {}
			},
			{
				'name': 'displayFeedData',
				'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
				'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedDisplay.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedDisplay.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=data',
				'toShow': 'bt_feed-settings-data',
				'toHide': 'bt_feed-settings-data',
				'bFancybox': false,
				'bFancyboxActivity': false,
				'sLoadbar': null,
				'sScrollTo': null,
				'oCallBack': {}
			},
			{
				'name': 'displayFeedApparel',
				'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
				'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedDisplay.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedDisplay.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=apparel',
				'toShow': 'bt_feed-settings-apparel',
				'toHide': 'bt_feed-settings-apparel',
				'bFancybox': false,
				'bFancyboxActivity': false,
				'sLoadbar': null,
				'sScrollTo': null,
				'oCallBack': {}
			},
			{
				'name': 'displayFeedTax',
				'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
				'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedDisplay.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedDisplay.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=tax',
				'toShow': 'bt_feed-settings-tax',
				'toHide': 'bt_feed-settings-tax',
				'bFancybox': false,
				'bFancyboxActivity': false,
				'sLoadbar': null,
				'sScrollTo': null,
				'oCallBack': {}
			}
		];
	{/literal}
</script>

<div class="bootstrap">
	<form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_basics-form" name="bt_basics-form" {if $useJs == true}onsubmit="javascript: oGmcPro.form('bt_basics-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_basics-settings', 'bt_basics-settings', false, false, oBasicCallBack, 'Basics', 'loadingBasicsDiv');return false;" {/if}>
		<input type="hidden" name="sAction" value="{$aQueryParams.basic.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.basic.type|escape:'htmlall':'UTF-8'}" />

		<h3 class="subtitle"><i class="icon-heart"></i>&nbsp;{l s='Basic settings' mod='gmerchantcenterpro'}</h3>
		<div class="clr_10"></div>
		{if !empty($bUpdate)}
			{include file="`$sConfirmInclude`"}
		{elseif !empty($aErrors)}
			{include file="`$sErrorInclude`"}
		{/if}

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="{l s='Example: http://www.myshop.com - Even if your shop is located in a sub-directory (e.g. http://www.myshop.com/shop), you should still only enter the fully qualified domain name http://www.myshop.com - DO NOT include a trailing slash (/) at the end' mod='gmerchantcenterpro'}"><b>{l s='Your PrestaShop\'s URL :' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-4 col-md-4 col-lg-2">
				<input type="text" name="bt_link" value="{$sLink|escape:'htmlall':'UTF-8'}" />
			</div>
			<span class="icon-question-sign label-tooltip" title="{l s='Example: http://www.myshop.com - Even if your shop is located in a sub-directory (e.g. http://www.myshop.com/shop), you should still only enter the fully qualified domain name http://www.myshop.com - DO NOT include a trailing slash (/) at the end' mod='gmerchantcenterpro'}">&nbsp;</span>
			<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/204" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about shop\'s URL' mod='gmerchantcenterpro'}</a>
		</div>

		<div class="form-group {if !empty($isGremarketing)} hide {/if} " id="id_tag_product">
			<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Choose how you want the product IDs to be built in the feed' mod='gmerchantcenterpro'}"><b>{l s='Construction mode of the product IDs in the feed' mod='gmerchantcenterpro'}</b></span> :
			</label>
			<div class="col-xs-12 col-md-4 col-lg-3">
				<select name="bt_feed-tag-id" id="bt_feed-tag-id" class="col-xs-12 col-md-12 col-lg-12" onchange="javascript: oGmcPro.changeOptionIdPreferencies('bt_feed-tag-id','tag_id_lang_basic');">
					<option value="tag-id-basic" {if $feedTagId == 'tag-id-basic' || !empty($isGremarketing)}selected="selected" {/if}>
						{l s='Use the IDs of the products in the back-office' mod='gmerchantcenterpro'}</option>
					<option value="tag-id-product-ref" {if $feedTagId == 'tag-id-product-ref' && empty($isGremarketing)}selected="selected" {/if}>
						{l s='Use the product references' mod='gmerchantcenterpro'}
					</option>
					<option value="tag-id-ean" {if $feedTagId == 'tag-id-ean' && empty($isGremarketing)}selected="selected" {/if}>
						{l s='Use the EAN codes' mod='gmerchantcenterpro'}
					</option>
				</select>
			</div>
			<span class="icon-question-sign label-tooltip" title="{l s='Choose how you want the product IDs to be built in the feed' mod='gmerchantcenterpro'}">&nbsp;</span>
			<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/529" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product ID construction mode' mod='gmerchantcenterpro'}</a>
		</div>
		<div {if $feedTagId == 'tag-id-basic'} class="hide" {/if} id="tag_id_warning_not_basic">
			<div class="form-group" id="id_tag_product_warning">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">
					<div class="alert alert-warning">
						{l s='Be careful : if you want to use product references or EAN codes, make sure that this information is filled in for all products (or product combinations) to be exported, and is unique for each of them. If the information is missing, the module will use the ID of the product in the back-office. If you have several products or combinations that have the same reference or code, we recommend that you use the IDs of products in the back-office.' mod='gmerchantcenterpro'}
					</div>
				</div>
			</div>
		</div>

		<div {if $feedTagId != 'tag-id-basic'} class="hide" {/if} id="tag_id_lang_basic">
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "YES" to assign simple IDs (1, 2, 3, ...) to your products and "NO" if you want to add a customized prefix (see the option below) and the country code (e.g: BTUS1, BTUS2, BTUS3, ...)' mod='gmerchantcenterpro'}"><b>{l s='Do you want to have simple product IDs?' mod='gmerchantcenterpro'}</b></span> :</label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_simple_id" id="bt_simple_id_on" value="1" {if !empty($bSimpleId)}checked="checked" {/if} onclick="oGmcPro.changeSelect('bt_prefix_string', 'bt_prefix_string', null, null, true, false);$('#prefix-id').val('');" />
						<label for="bt_simple_id_on" class="radioCheck">
							{l s='Yes' mod='gmerchantcenterpro'}
						</label>
						<input type="radio" name="bt_simple_id" id="bt_simple_id_off" value="0" {if empty($bSimpleId)}checked="checked" {/if} onclick="oGmcPro.changeSelect('bt_prefix_string', 'bt_prefix_string', null, null, true, true);" />
						<label for="bt_simple_id_off" class="radioCheck">
							{l s='No' mod='gmerchantcenterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "YES" to assign simple IDs (1, 2, 3, ...) to your products and "NO" if you want to add a customized prefix (see the option below) and the country code (e.g: BTUS1, BTUS2, BTUS3, ...)' mod='gmerchantcenterpro'}">&nbsp;<span class="icon-question-sign"></span></span>
					<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/267" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about simple ID' mod='gmerchantcenterpro'}</a>
				</div>
			</div>

			<div class="form-group" id="bt_prefix_string">
				<label class="control-label col-xs-12 col-md-3 col-lg-3">
					<span class="label-tooltip" title="{l s='Enter a short prefix that represents your shop. For example, if your shop is called "Janes\'s Flowers", enter "jf". This prefix is mandatory and must be unique for each of your shops.' mod='gmerchantcenterpro'}"><b>
							{l s='Product ID prefix for your shop :' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-3 col-lg-2">
					<input type="text" id="prefix-id" name="bt_prefix-id" value="{$sPrefixId|escape:'htmlall':'UTF-8'}" />
				</div>
				<span class="icon-question-sign label-tooltip" title="
						{l s='Enter a short prefix that represents your shop. For example, if your shop is called "Janes\'s Flowers", enter "jf". This prefix is mandatory and must be unique for each of your shops.' mod='gmerchantcenterpro'}">&nbsp;</span>
				<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/194" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='FAQ about product ID' mod='gmerchantcenterpro'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
						{l s='This determines how many products are processed per AJAX / CRON cycle. Default is 200. Only increase this value if you have a large shop and run into problems with server limits. Otherwise, leave it at its default 200 value. It should not be higher than 1000 in any case.' mod='gmerchantcenterpro'}"><b>
						{l s='Number of products per cycle :' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-12 col-md-3 col-lg-2">
				<input type="text" name="bt_ajax-cycle" value="{$iProductPerCycle|escape:'htmlall':'UTF-8'}" />
			</div>
			<span class="icon-question-sign label-tooltip" title="
						{l s='This determines how many products are processed per AJAX / CRON cycle. Default is 200. Only increase this value if you have a large shop and run into problems with server limits. Otherwise, leave it at its default 200 value. It should not be higher than 1000 in any case.' mod='gmerchantcenterpro'}">&nbsp;</span>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
						{l s='Choose the largest image size available (such as thickbox). Google requires at least 250x250 and recommends at least 400x400 pixels.' mod='gmerchantcenterpro'}"><b>


							{l s='Image size for product photos :' mod='gmerchantcenterpro'}</b></span></label>
				<div class="col-xs-12 col-md-3 col-lg-2">
					<select name="bt_image-size">';



							{foreach from=$aImageTypes item=aImgType}
							<option value="{$aImgType.name|escape:'htmlall':'UTF-8'}"
								{if $aImgType.name == $sImgSize}selected="selected"
								{/if}>{$aImgType.name|escape:'htmlall':'UTF-8'}</option>



							{/foreach}
					</select>
				</div>
				<div>
					<span class="icon-question-sign label-tooltip" title="
								{l s='Choose the largest image size available (such as thickbox). Google requires at least 250x250 and recommends at least 400x400 pixels.' mod='gmerchantcenterpro'}">&nbsp;</span>
				<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/203" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='FAQ about image size' mod='gmerchantcenterpro'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="
				{l s='Indicate the position of the image you want to use as the cover image. Example: enter 2 if you want the 2nd image associated with the product (or combination) to be used as the main image.' mod='gmerchantcenterpro'}"><b>
						{l s='Position of image to be used as cover' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-12 col-md-3 col-lg-2">
				<input type="number" name="bt_image-cover-position" value="{$coverPosition|escape:'htmlall':'UTF-8'}" class="form-control" />
				<small class="form-text text-muted">
					{l s='Enter 1 to use default cover image' mod='gmerchantcenterpro'}</small>
			</div>
			<span class="label-tooltip" data-toggle="tooltip" title data-original-title="
			{l s='Indicate the position of the image you want to use as the cover image. Example: enter 2 if you want the 2nd image associated with the product (or combination) to be used as the main image.' mod='gmerchantcenterpro'}">&nbsp;<span class="icon-question-sign"></span>&nbsp;</span>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="
								{l s='If you want to export only the cover image select "NO"' mod='gmerchantcenterpro'}"><b>
						{l s='Do you want to export additional images?' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-5 col-md-5 col-lg-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="bt_add_images" id="bt_add_images_on" value="1" {if !empty($bAddImages)}checked="checked" {/if} />
					<label for="bt_add_images_on" class="radioCheck">

						{l s='Yes' mod='gmerchantcenterpro'}
					</label>
					<input type="radio" name="bt_add_images" id="bt_add_images_off" value="0" {if empty($bAddImages)}checked="checked" {/if} />
					<label for="bt_add_images_off" class="radioCheck">

						{l s='No' mod='gmerchantcenterpro'}
					</label>
					<a class="slide-button btn"></a>
				</span>
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="
								{l s='If you want to export only the cover image select "NO"' mod='gmerchantcenterpro'}">&nbsp;<span class="icon-question-sign"></span>&nbsp;</span>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3"><b>
					{l s='Do you want to include the dimensions of the product?' mod='gmerchantcenterpro'}</b></label>
			<div class="col-xs-12 col-md-5 col-lg-3">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="bt_manage_product_size" id="bt_manage_product_size_on" value="1" {if !empty($bUseProductSize)}checked="checked" {/if} />
					<label for="bt_manage_product_size_on" class="radioCheck">

						{l s='Yes' mod='gmerchantcenterpro'}
					</label>
					<input type="radio" name="bt_manage_product_size" id="bt_manage_product_size_off" value="0" {if empty($bUseProductSize)}checked="checked" {/if} />
					<label for="bt_manage_product_size_off" class="radioCheck">

						{l s='No' mod='gmerchantcenterpro'}
					</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
			<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/452" target="_blank"><i class="icon icon-link"></i>&nbsp;
				{l s='FAQ about the dimensions of the package' mod='gmerchantcenterpro'}</a>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="
								{l s='If you want to force the identifier_exists tag select YES' mod='gmerchantcenterpro'}"><b>
						{l s='Force the identifier_exists tag?' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-5 col-md-5 col-lg-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="bt_identifier_exist" id="bt_identifier_exist_on" value="1" {if !empty($bIdentifierExist)}checked="checked" {/if} />
					<label for="bt_identifier_exist_on" class="radioCheck">

						{l s='Yes' mod='gmerchantcenterpro'}
					</label>
					<input type="radio" name="bt_identifier_exist" id="bt_identifier_exist_off" value="0" {if empty($bIdentifierExist)}checked="checked" {/if} />
					<label for="bt_identifier_exist_off" class="radioCheck">

						{l s='No' mod='gmerchantcenterpro'}
					</label>
					<a class="slide-button btn"></a>
				</span>
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="
								{l s='If you want to force the identifier_exists tag select YES' mod='gmerchantcenterpro'}">&nbsp;<span class="icon-question-sign"></span>&nbsp;</span>
				<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}faq.php?id=72&lg={$sFaqLang|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='How to manage the identifier_exists tag?' mod='gmerchantcenterpro'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
								{l s='Please select the category that is the starting point of your tree view (it\'s usually your root or home category)' mod='gmerchantcenterpro'}"><b>


									{l s='Please select your "Home" category :' mod='gmerchantcenterpro'}</b></span>
					</label>
					<div class="col-xs-12 col-md-3 col-lg-2">
						<select name="bt_home-cat-id">';



									{foreach from=$aHomeCat item=aCat}
								<option value="{$aCat.id_category|escape:'htmlall':'UTF-8'}"
										{if $aCat.id_category == $iHomeCatId}selected="selected"
										{/if}>{$aCat.name|escape:'htmlall':'UTF-8'}</option>



									{/foreach}
						</select>
					</div>
					<span class="icon-question-sign label-tooltip" title="
										{l s='Please select the category that is the starting point of your tree view (it\'s usually your root or home category)' mod='gmerchantcenterpro'}">&nbsp;</span>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
										{l s='For example "Electronic" or "Clothing". In most cases, the product path will correctly be retreived. But, for security reasons, in case where the product parent category wouldn\'t be found, the module needs to have a replacement value to enter in place of it. This value will then allow you to easily find, in your Google Ads account, the products concerned.' mod='gmerchantcenterpro'}"><b>


											{l s='What type of products are you selling ?' mod='gmerchantcenterpro'}</b></span></label>
						<div id="homecat" class="col-xs-12 col-md-3 col-lg-2">



											{foreach from=$aLangs item=aLang}
								<div id="bt_home-cat-name_{$aLang.id_lang|escape:'htmlall':'UTF-8'}" class="translatable-field row lang-{$aLang.id_lang|escape:'htmlall':'UTF-8'}"
												{if $aLang.id_lang != $iCurrentLang}style="display:none"
												{/if}>
									<div class="col-xs-9 col-md-9 col-lg-10">
										<input type="text" id="bt_home-cat-name_{$aLang.id_lang|escape:'htmlall':'UTF-8'}" name="bt_home-cat-name_{$aLang.id_lang|escape:'htmlall':'UTF-8'}"
												{if !empty($aHomeCatLanguages)}
													{foreach from=$aHomeCatLanguages key=idLang item=sLangTitle}
														{if $idLang == $aLang.id_lang} value="{$sLangTitle|escape:'htmlall':'UTF-8'}"
														{/if}
													{/foreach}
												{/if} />
									</div>
									<div class="col-xs-12 col-md-3 col-lg-2">
										<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$aLang.iso_code|escape:'htmlall':'UTF-8'}&nbsp;<i class="icon-caret-down"></i></button>
										<ul class="dropdown-menu">



												{foreach from=$aLangs item=aLang}
												<li><a href="javascript:hideOtherLanguage({$aLang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$aLang.name|escape:'htmlall':'UTF-8'}</a></li>



												{/foreach}
										</ul>
									</div>
								</div>



											{/foreach}
						</div>
						<div>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<span class="icon-question-sign label-tooltip" title="
												{l s='For example "Electronic" or "Clothing". In most cases, the product path will correctly be retreived. But, for security reasons, in case where the product parent category wouldn\'t be found, the module needs to have a replacement value to enter in place of it. This value will then allow you to easily find, in your Google Ads account, the products concerned.' mod='gmerchantcenterpro'}">&nbsp;</span>
				<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/211" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='FAQ about product type' mod='gmerchantcenterpro'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="
												{l s='If your shop uses multiple currencies, you have to select "Yes" and modify the robot.txt file as explained in our FAQ (see link opposite)' mod='gmerchantcenterpro'}"><b>
						{l s='Does your shop use multiple currencies ?' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-5 col-md-5 col-lg-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="bt_add-currency" id="bt_add-currency_on" value="1" {if !empty($bAddCurrency)}checked="checked" {/if} />
					<label for="bt_add-currency_on" class="radioCheck">

						{l s='Yes' mod='gmerchantcenterpro'}
					</label>
					<input type="radio" name="bt_add-currency" id="bt_add-currency_off" value="0" {if empty($bAddCurrency)}checked="checked" {/if} />
					<label for="bt_add-currency_off" class="radioCheck">

						{l s='No' mod='gmerchantcenterpro'}
					</label>
					<a class="slide-button btn"></a>
				</span>
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="
												{l s='If your shop uses multiple currencies, you have to select "Yes" and modify the robot.txt file as explained in our FAQ (see link opposite)' mod='gmerchantcenterpro'}">&nbsp;<span class="icon-question-sign"></span>&nbsp;</span>
				<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/34" target="_blank"><i class="icon icon-link">&nbsp;</i>
					{l s='FAQ about robot.txt file' mod='gmerchantcenterpro'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
												{l s='In most cases, the product condition will correctly be retreived. But, for security reasons, in case where it wouldn\'t be found, the module needs to have a replacement value to enter in place of it. The products concerned will have this condition in your Google Ads account.' mod='gmerchantcenterpro'}"><b>
						{l s='In general, what\'s your products condition ?' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-12 col-md-3 col-lg-2">
				<select name="bt_product-condition">
					<option value="0" {if empty($sCondition)}selected="selected" {/if}>--</option>

					{foreach from=$aAvailableCondition item=aCondition key=sCondName}
					<option value="{$sCondName|escape:'htmlall':'UTF-8'}" {if $sCondition == $sCondName}selected="selected" {/if}>{$aCondition|escape:'htmlall':'UTF-8'}</option>

					{/foreach}
				</select>
			</div>
			<div>
				<span class="icon-question-sign label-tooltip" title="
												{l s='In most cases, the product condition will correctly be retreived. But, for security reasons, in case where it wouldn\'t be found, the module needs to have a replacement value to enter in place of it. The products concerned will have this condition in your Google Ads account.' mod='gmerchantcenterpro'}">&nbsp;</span>
				<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/195" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='FAQ about product condition' mod='gmerchantcenterpro'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<b>
					{l s='Product title' mod='gmerchantcenterpro'}</b> :
			</label>
			<div class="col-xs-12 col-md-3 col-lg-3">
				<select name="bt_prod-title" id="bt_prod-title">
					<option value="title" {if $sProductTitle == 'title'}selected="selected" {/if}>
						{l s='Use the product name' mod='gmerchantcenterpro'}</option>
					<option value="meta" {if $sProductTitle == 'meta'}selected="selected" {/if}>
						{l s='Use the product meta title' mod='gmerchantcenterpro'}</option>
				</select>
			</div>
			<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/210" target="_blank"><i class="icon icon-link"></i>&nbsp;
				{l s='FAQ about product titles' mod='gmerchantcenterpro'}</a>
		</div>

		<div class="form-group" id="bt_advanced-prod-name-div">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
												{l s='We advise you to add either the product category or the product brand in your product titles.'  mod='gmerchantcenterpro'}"><b>
						{l s='Advanced product title :' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-12 col-md-3 col-lg-3">
				<select name="bt_advanced-prod-name" id="bt_advanced-prod-name">
					<option value="0" {if $iAdvancedProductName == 0}selected="selected" {/if}>
						{l s='Just the normal product name' mod='gmerchantcenterpro'}</option>
					<option value="1" {if $iAdvancedProductName == 1}selected="selected" {/if}>
						{l s='Current category name + Product name' mod='gmerchantcenterpro'}</option>
					<option value="2" {if $iAdvancedProductName == 2}selected="selected" {/if}>
						{l s='Product name + Current category name' mod='gmerchantcenterpro'}</option>
					<option value="3" {if $iAdvancedProductName == 3}selected="selected" {/if}>
						{l s='Brand name + Product name' mod='gmerchantcenterpro'}</option>
					<option value="4" {if $iAdvancedProductName == 4}selected="selected" {/if}>
						{l s='Product name + Brand name' mod='gmerchantcenterpro'}</option>
					<option value="5" {if $iAdvancedProductName == 5}selected="selected" {/if}>
						{l s='Free field + Product name + Free field' mod='gmerchantcenterpro'}</option>
				</select>
				<br />
			</div>
			<span class="icon-question-sign label-tooltip" title="
												{l s='We advise you to add either the product category or the product brand in your product titles.' mod='gmerchantcenterpro'}">&nbsp;</span>
			<a class="badge badge-info" href="{$faqUrl|escape:'htmlall':'UTF-8'}{$sFaqLang|escape:'htmlall':'UTF-8'}/faq/178" target="_blank"><i class="icon icon-link"></i>&nbsp;
				{l s='FAQ about advanced product titles' mod='gmerchantcenterpro'}</a>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
												{l s='To avoid that some products are refused by Google because of forbidden terms in the title, you can ask the module to remove these terms from the titles in the feed. Enter the exact phrases one after the other by separating them with commas and WITHOUT including spaces between them (spaces within phrases are allowed). Example : word1 word2,word3 will exclude the "word1 word2" exact phrase and the word "word3"' mod='gmerchantcenterpro'}"><b>
						{l s='Exclude the following exact phrases from product titles:' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-4 col-md-4 col-lg-5">
				<textarea cols="20" rows="10" name="bt_excluded_words">
						{if !empty($excludedWords)}{$excludedWords|escape:'htmlall':'UTF-8'}
						{/if}</textarea>
			</div>
			<span class="icon-question-sign label-tooltip" title="
												{l s='To avoid that some products are refused by Google because of forbidden terms in the title, you can ask the module to remove these terms from the titles in the feed. Enter the exact phrases one after the other by separating them with commas and WITHOUT including spaces between them (spaces within phrases are allowed). Example : word1 word2,word3 will exclude the "word1 word2" exact phrase and the word "word3"' mod='gmerchantcenterpro'}">&nbsp;</span>
						</div>

						<div id="bt_info-title-free-field">

							<div class="form-group">
								<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
								<div class="col-xs-12 col-md-3 col-lg-8">

									<p class="alert alert-info">



													{l s='The fields below let you create custom product titles thanks to a free choice of words to be placed before and/or after the product name. Complete either the "Prefix" field or the "Suffix" field or both. You can enter one or several words in each field.' mod='gmerchantcenterpro'}
									</p>

									<div class="form-group">
										<label class="control-label col-xs-12 col-md-3 col-lg-1">
											<span><b>


													{l s='Prefix' mod='gmerchantcenterpro'}</b></span></label>
										<div class="col-xs-12 col-md-3 col-lg-5">



													{foreach from=$aLangs item=aLang}
												<div id="bt_advanced_prefix_name_{$aLang.id_lang|escape:'htmlall':'UTF-8'}" class="translatable-field row lang-{$aLang.id_lang|escape:'htmlall':'UTF-8'}"
														{if $aLang.id_lang != $iCurrentLang}style="display:none"
														{/if}>
													<div class="col-xs-9 col-md-9 col-lg-10">
														<input type="text" id="bt_advanced_prefix_name_{$aLang.id_lang|escape:'htmlall':'UTF-8'}" name="bt_advanced_prefix_name_{$aLang.id_lang|escape:'htmlall':'UTF-8'}"
														{if !empty($aProdNamePrefix)}
															{foreach from=$aProdNamePrefix key=idLang item=sLangTitle}
																{if $idLang == $aLang.id_lang} value="{$sLangTitle|escape:'htmlall':'UTF-8'}"
																{/if}
															{/foreach}
														{/if} />
													</div>
													<div class="col-xs-12 col-md-3 col-lg-2">
														<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$aLang.iso_code|escape:'htmlall':'UTF-8'}&nbsp;<i class="icon-caret-down"></i></button>
														<ul class="dropdown-menu">



														{foreach from=$aLangs item=aLang}
																<li><a href="javascript:hideOtherLanguage({$aLang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$aLang.name|escape:'htmlall':'UTF-8'}</a></li>



														{/foreach}
														</ul>
													</div>
												</div>



													{/foreach}
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-xs-12 col-md-3 col-lg-1">
											<span><b>


													{l s='Suffix' mod='gmerchantcenterpro'}</b></span></label>
										<div class="col-xs-12 col-md-3 col-lg-5">



													{foreach from=$aLangs item=aLang}
												<div id="bt_advanced_suffix_name_{$aLang.id_lang|escape:'htmlall':'UTF-8'}" class="translatable-field row lang-{$aLang.id_lang|escape:'htmlall':'UTF-8'}"
														{if $aLang.id_lang != $iCurrentLang}style="display:none"
														{/if}>
													<div class="col-xs-9 col-md-9 col-lg-10">
														<input type="text" id="bt_advanced_suffix_name_{$aLang.id_lang|escape:'htmlall':'UTF-8'}" name="bt_advanced_suffix_name_{$aLang.id_lang|escape:'htmlall':'UTF-8'}"
														{if !empty($aProdNameSuffix)}
															{foreach from=$aProdNameSuffix key=idLang item=sLangTitle}
																{if $idLang == $aLang.id_lang} value="{$sLangTitle|escape:'htmlall':'UTF-8'}"
																{/if}
															{/foreach}
														{/if} />
													</div>
													<div class="col-xs-12 col-md-3 col-lg-2">
														<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$aLang.iso_code|escape:'htmlall':'UTF-8'}&nbsp;<i class="icon-caret-down"></i></button>
														<ul class="dropdown-menu">



														{foreach from=$aLangs item=aLang}
																<li><a href="javascript:hideOtherLanguage({$aLang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$aLang.name|escape:'htmlall':'UTF-8'}</a></li>



														{/foreach}
														</ul>
													</div>
												</div>



													{/foreach}
										</div>

									</div>

									<p class="alert alert-warning">



													{l s='Be careful : Google requires your product titles to be NO MORE than 150 characters long. So, make sure your titles include less than 150 characters and if they don\'t, change the drag and drop menu value above.' mod='gmerchantcenterpro'}
									</p>

								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-xs-12 col-md-3 col-lg-3">
								<span class="label-tooltip" title="
														{l s='Be careful : Google will refuse your product feed if your product titles have too many UPPERCASE letters. So if it\'s the case, choose one of the two solutions suggested in the opposite drag and drop menu.'  mod='gmerchantcenterpro'}"><b>
						{l s='Do you have too many uppercases in titles ?' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-12 col-md-3 col-lg-3">
				<select name="bt_advanced-prod-title" id="bt_advanced-prod-title">
					<option value="0" {if $iAdvancedProductTitle == 0}selected="selected" {/if}>
						{l s='No' mod='gmerchantcenterpro'}</option>
					<option value="1" {if $iAdvancedProductTitle == 1}selected="selected" {/if}>
						{l s='Yes : Uppercase the first character of each title word' mod='gmerchantcenterpro'}</option>
					<option value="2" {if $iAdvancedProductTitle == 2}selected="selected" {/if}>
						{l s='Yes : Uppercase the title first character only' mod='gmerchantcenterpro'}</option>
				</select>
			</div>
			<span class="icon-question-sign label-tooltip" title="
														{l s='Be careful : Google will refuse your product feed if your product titles have too many UPPERCASE letters. So if it\'s the case, choose one of the two solutions suggested in the opposite drag and drop menu.' mod='gmerchantcenterpro'}">&nbsp;</span>
		</div>



		<div class="clr_30"></div>
		<h3 class="subtitle">
			{l s='Advanced file security' mod='gmerchantcenterpro'}</h3>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
														{l s='This is a security measure so that people from the outside cannot call your feed URL and view your data. For your convenience, we have already automatically generated this secure key during the module installation.' mod='gmerchantcenterpro'}"><b>
						{l s='Your secure token :' mod='gmerchantcenterpro'}</b></span></label>
			<div class="col-xs-12 col-md-3 col-lg-3">
				<input type="text" maxlength="32" name="bt_feed-token" id="bt_feed-token" value="{$sFeedToken|escape:'htmlall':'UTF-8'}" />
			</div>
			<span class="icon-question-sign label-tooltip" title="
														{l s='This is a security measure so that people from the outside cannot call your feed URL and view your data. For your convenience, we have already automatically generated this secure key during the module installation.' mod='gmerchantcenterpro'}">&nbsp;</span>
		</div>

		<div class="navbar navbar-default navbar-fixed-bottom text-center">
			<div class="col-xs-12">
				<button class="btn btn-submit" onclick="oGmcPro.form('bt_basics-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_basics-settings', 'bt_basics-settings', false, false, oBasicCallBack, 'Basics', 'loadingBasicsDiv', false, 1);return false;">
					{l s='Save' mod='gmerchantcenterpro'}</button>
			</div>
		</div>
	</form>
</div>

{literal}
<script type="text/javascript">
	//bootstrap components init
	// manage change value for advance protection
	//$("#bt_protection-mode").change(function() {
	$("input [name='bt_protection-mode']").bind($.browser.msie ? 'click' : 'change', function(event) {
		if ($(this).val() == "0") {
			$("#protection_off").show();
		} else {
			$("#protection_off").hide();
		}
	});


	{/literal}
	{if !empty($bSimpleId)}
	{literal}
	$('#bt_prefix_string').hide()
	$('#prefix-id').val('')

	{/literal}
	{/if}
	{literal}


	// Manage the option with meta title
	if ($("#bt_prod-title").val() == "meta") {
		$("#bt_advanced-prod-name-div").hide();
		$("#bt_info-title-free-field").hide();
	}

	$("#bt_prod-title").change(function() {
		if ($("#bt_prod-title").val() == "meta") {
			$("#bt_advanced-prod-name-div").hide();
			$("#bt_info-title-free-field").hide();
		} else {
			$("#bt_advanced-prod-name-div").show();
		}

		if ($("#bt_advanced-prod-name").val() == "5" && $("#bt_prod-title").val() != "meta") {
			$("#bt_info-title-free-field").show();
		}
	});

	//manage information for info title
	if ($("#bt_advanced-prod-name").val() == "0") {
		$("#bt_info-title-category").hide();
		$("#bt_info-title-brand").hide();
		$("#bt_info-title-free-field").hide();

	}
	if ($("#bt_advanced-prod-name").val() == "1" ||
		$("#bt_advanced-prod-name").val() == "2"
	) {
		$("#bt_info-title-category").show();
		$("#bt_info-title-brand").hide();
		$("#bt_info-title-free-field").hide();

	}
	if ($("#bt_advanced-prod-name").val() == "3" ||
		$("#bt_advanced-prod-name").val() == "4"
	) {
		$("#bt_info-title-category").hide();
		$("#bt_info-title-brand").show();
		$("#bt_info-title-free-field").hide();
	}
	if ($("#bt_advanced-prod-name").val() == "5" && $("#bt_prod-title").val() != "meta") {
		$("#bt_info-title-category").hide();
		$("#bt_info-title-brand").hide();
		$("#bt_info-title-free-field").show();
	}
	$("#bt_advanced-prod-name").change(function() {
		if ($(this).val() == "0") {
			$("#bt_info-title-category").hide();
			$("#bt_info-title-brand").hide();
			$("#bt_info-title-free-field").hide();

		}
		if ($(this).val() == "1" ||
			$(this).val() == "2"
		) {
			$("#bt_info-title-category").show();
			$("#bt_info-title-brand").hide();
			$("#bt_info-title-free-field").hide();
		}
		if ($(this).val() == "3" ||
			$(this).val() == "4"
		) {
			$("#bt_info-title-category").hide();
			$("#bt_info-title-brand").show();
			$("#bt_info-title-free-field").hide();
		}
		if ($(this).val() == "5") {
			$("#bt_info-title-category").hide();
			$("#bt_info-title-brand").hide();
			$("#bt_info-title-free-field").show();
						}
					});
				{/literal}
				{if !empty($bAjaxMode)}
					{literal}
						$('.label-tooltip, .help-tooltip').tooltip();
						{/literal}{/if}{literal}
					</script>
				{/literal}