{*
* 2020 4webs
*
* DEVELOPED By 4webs Prestashop Platinum Partner
*
* @author    4webs
* @copyright 4webs 2017
* @version 5.1.4
* @category payment_gateways
* @license 4webs
*}
<div class="module-panel module_4webs" id="">
{*{if isset($tabs) && $tabs|count}
<script type="text/javascript">
	var helper_tabs = {$tabs|json_encode};
	var unique_field_id = '';
</script>
{/if}*}
{block name="defaultForm"}
{if isset($identifier_bk) && $identifier_bk == $identifier}{capture name='identifier_count'}{counter name='identifier_count'}{/capture}{/if}
{assign var='identifier_bk' value=$identifier scope='parent'}
{if isset($table_bk) && $table_bk == $table}{capture name='table_count'}{counter name='table_count'}{/capture}{/if}
{assign var='table_bk' value=$table scope='root'}

<form id="{if isset($fields.form.form.id_form)}{$fields.form.form.id_form|escape:'html':'UTF-8'}{else}{if $table == null}configuration_form{else}{$table|escape:'html':'UTF-8'}_form{/if}{if isset($smarty.capture.table_count) && $smarty.capture.table_count}_{$smarty.capture.table_count|intval}{/if}{/if}" class="defaultForm form-horizontal{if isset($name_controller) && $name_controller} {$name_controller|escape:'html':'UTF-8'}{/if}"{if isset($current) && $current} action="{$current|escape:'html':'UTF-8'}{if isset($token) && $token}&amp;token={$token|escape:'html':'UTF-8'}{/if}"{/if} method="post" enctype="multipart/form-data"{if isset($style)} style="{$style|escape:'html':'UTF-8'}"{/if} novalidate>
	{if $form_id}
		<input type="hidden" name="{$identifier|escape:'html':'UTF-8'}" id="{$identifier|escape:'html':'UTF-8'}{if isset($smarty.capture.identifier_count) && $smarty.capture.identifier_count}_{$smarty.capture.identifier_count|intval}{/if}" value="{$form_id|escape:'html':'UTF-8'}" />
	{/if}
	{if !empty($submit_action)}
		<input type="hidden" name="{$submit_action|escape:'html':'UTF-8'}" value="1" />
	{/if}
	{foreach $fields as $f => $fieldset}
		{block name="fieldset"}
		{capture name='fieldset_name'}{counter name='fieldset_name'}{/capture}
		<div class="" id="fieldset_{$f|escape:'html':'UTF-8'}{if isset($smarty.capture.identifier_count) && $smarty.capture.identifier_count}_{$smarty.capture.identifier_count|intval}{/if}{if $smarty.capture.fieldset_name > 1}_{($smarty.capture.fieldset_name - 1)|intval}{/if}">
            {foreach $fieldset.form as $key => $field}
				{if $key == 'legend'}
					{block name="legend"}
                        <div class="tab-header">
			                <span class="tab-top-title">{if isset($field.title)}{$field.title|escape:'html':'UTF-8'}{/if}</span>
			                <span class="tab-bottom-title">{if isset($field.desc)}{$field.desc|escape:'html':'UTF-8'}{/if}</span>
                            {*{if isset($field.image) && isset($field.title)}<img src="{$field.image}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
							{if isset($field.icon)}<i class="{$field.icon}"></i>{/if}*}
                            {if isset($fieldset.form['tabs']) && $fieldset.form['tabs']|count}
                                <div class="tab-select">
                                    {$count_tab = 0}
                                    {foreach $fieldset.form['tabs'] as $key => $tab}
                                        <div open-tab="{$key|escape:'html':'UTF-8'}" class="_tab_select {if isset($selected_tab_input) && $selected_tab_input}{if $selected_tab_input == $key} module-tab-select-active{/if}{elseif $count_tab == 0} module-tab-select-active{/if}">
											{if isset($selected_tab_input) && $selected_tab_input}
												{if $selected_tab_input == $key}
													<input type="hidden" id="selected_tab_input" name="selected_tab_input" value="{$key|escape:'html':'UTF-8'}">
												{/if}
											{elseif $count_tab == 0}
												<input type="hidden" id="selected_tab_input" name="selected_tab_input" value="{$key|escape:'html':'UTF-8'}">
											{/if}
                                            <div class="tab">
                                                <a href="#"></a>
                                            </div>
                                            <span class="_tab_title">{$tab|escape:'html':'UTF-8'}</span>
                                        </div>
                                        {$count_tab = $count_tab + 1}
                                    {/foreach}
                                </div>
                            {/if}
		                </div>
					{/block}
				{elseif $key == 'description' && $field}
					<div class="alert alert-info">{$field|escape:'html':'UTF-8'}</div>
				{elseif $key == 'warning' && $field}
					<div class="alert alert-warning">{$field|escape:'html':'UTF-8'}</div>
				{elseif $key == 'success' && $field}
					<div class="alert alert-success">{$field|escape:'html':'UTF-8'}</div>
				{elseif $key == 'error' && $field}
					<div class="alert alert-danger">{$field|escape:'html':'UTF-8'}</div>
				{elseif $key == 'input'}
                    {if isset($fieldset.form['tabs']) && $fieldset.form['tabs']|count}
                    {$count_tab = 0}
                    {foreach $fieldset.form['tabs'] as $key => $tab}
					<div class="module-tab-container {if isset($selected_tab_input) && $selected_tab_input}{if $selected_tab_input == $key}module-tab-active{/if}{elseif $count_tab == 0} module-tab-active{/if}" {if isset($selected_tab_input) && $selected_tab_input}{if $selected_tab_input != $key}style="display: none;"{/if}{elseif $count_tab > 0}style="display: none;"{/if} id="{$key|escape:'html':'UTF-8'}">
                    <div class="module-tab-section">
					{foreach $field as $input}
						{if $input.name == 'PPAL_FEE_TEST'}
							<div class="create_account_info">
							    <span>
							        {l s='Para generar la credenciales, debes crear un cuenta en ' mod='paypalwithfee'}
							        <a target="_blank" href="https://developer.paypal.com/home" class="enlace">{l s='DEVELOPER PAYPAL' mod='paypalwithfee'}</a>
							        {l s=', si quieres más información revisa nuestro ' mod='paypalwithfee'}
							        <a target="_blank" href="https://www.4webs.es/faq/knowledgebase/configurar-api-rest-para-modulo-con-recargo-de-paypal/" class="enlace">{l s='FAQ.' mod='paypalwithfee'}</a>
							    </span>
							</div>
						{/if}
                        {if $input.tab == $key}
						{block name="input_row"}
						<div class="fw_{$input.name|escape:'html':'UTF-8'} module-row-attribute form-group{if isset($input.form_group_class)} {$input.form_group_class|escape:'html':'UTF-8'}{/if}{if $input.type == 'hidden' || isset($input.fw_hidden) && $input.fw_hidden} hide{/if}"{if $input.name == 'id_state'} id="contains_states"{if !$contains_states} style="display:none;"{/if}{/if}{if isset($tabs) && isset($input.tab)} data-tab-id="{$input.tab|escape:'html':'UTF-8'}"{/if}>
						{if $input.type == 'hidden'}
							<input type="hidden" name="{$input.name|escape:'html':'UTF-8'}" id="{$input.name|escape:'html':'UTF-8'}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
						{else}
							{block name="label"}
								{if isset($input.label)}
									{if isset($input.hidden_label) && $input.hidden_label}

									{else}
										<div class="col-lg-2 text-right">
										<span class="module-input-label {if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
											{$input.label|escape:'html':'UTF-8'}
										</span>
										</div>
									{/if}
								{/if}
							{/block}

							{block name="field"}
								<div class="col-lg-{if isset($input.col)}{$input.col|intval}{elseif $input.type=='html'}12{else}5{/if}">
								{block name="input"}
								{if $input.type == 'text' || $input.type == 'tags'}
									{if isset($input.lang) AND $input.lang}
									{if $languages|count > 1}
									<div class="form-group">
									{/if}
									{foreach $languages as $language}
										{assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
										{if $languages|count > 1}
										<div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
											<div class="col-lg-9">
										{/if}
												{if $input.type == 'tags'}
													{literal}
														<script type="text/javascript">
															$().ready(function () {
																var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:"html":"UTF-8"}_{$language.id_lang|escape:"html":"UTF-8"}{else}{$input.name|escape:"html":"UTF-8"}_{$language.id_lang|escape:"html":"UTF-8"}{/if}{literal}';
																$('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1 mod='paypalwithfee'}{literal}'});
																$({/literal}'#{$table|escape:"html":"UTF-8"}{literal}_form').submit( function() {
																	$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
																});
															});
														</script>
													{/literal}
												{/if}
												{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
												<div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
												{/if}
												{if isset($input.maxchar) && $input.maxchar}
												<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
													<span class="text-count-down">{$input.maxchar|intval}</span>
												</span>
												{/if}
												{if isset($input.prefix)}
													<span class="input-group-addon">
													  {$input.prefix|escape:'html':'UTF-8'}
													</span>
													{/if}
												<input type="text"
													id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
													name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
													class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
													value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
													onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
													{if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
													{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
													{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
													{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
													{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
													{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
													{if isset($input.required) && $input.required} required="required" {/if}
													{if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} />
													{if isset($input.suffix)}
													<span class="input-group-addon">
													  {$input.suffix|escape:'html':'UTF-8'}
													</span>
													{/if}
												{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
												</div>
												{/if}
										{if $languages|count > 1}
											</div>
											<div class="col-lg-2">
												<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
													{$language.iso_code|escape:'html':'UTF-8'}
													<i class="icon-caret-down"></i>
												</button>
												<ul class="dropdown-menu">
													{foreach from=$languages item=language}
													<li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
													{/foreach}
												</ul>
											</div>
										</div>
										{/if}
									{/foreach}
									{if isset($input.maxchar) && $input.maxchar}
									<script type="text/javascript">
									$(document).ready(function(){
									{foreach from=$languages item=language}
										countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter"));
									{/foreach}
									});
									</script>
									{/if}
									{if $languages|count > 1}
									</div>
									{/if}
									{else}
										{if $input.type == 'tags'}
											{literal}
											<script type="text/javascript">
												$().ready(function () {
													var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:"html":"UTF-8"}{else}{$input.name|escape:"html":"UTF-8"}{/if}{literal}';
													$('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' mod='paypalwithfee'}{literal}'});
													$({/literal}'#{$table|escape:"html":"UTF-8"}{literal}_form').submit( function() {
														$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
													});
												});
											</script>
											{/literal}
										{/if}
										{assign var='value_text' value=$fields_value[$input.name]}
										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
										<div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
										{/if}
										{if isset($input.maxchar) && $input.maxchar}
										<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval}</span></span>
										{/if}
										{if isset($input.prefix)}
										<span class="input-group-addon">
										  {$input.prefix|escape:'html':'UTF-8'}
										</span>
										{/if}
										<input type="text"
											name="{$input.name|escape:'html':'UTF-8'}"
											id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
											value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
											class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
											{if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
											{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
											{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
											{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
											{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
											{if isset($input.required) && $input.required } required="required" {/if}
											{if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}
											/>
										{if isset($input.suffix)}
										<span class="input-group-addon">
										  {$input.suffix|escape:'html':'UTF-8'}
										</span>
										{/if}

										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
										</div>
										{/if}
										{if isset($input.maxchar) && $input.maxchar}
										<script type="text/javascript">
										$(document).ready(function(){
											countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
										});
										</script>
										{/if}
									{/if}
								{elseif $input.type == 'textbutton'}
									{assign var='value_text' value=$fields_value[$input.name]}
									<div class="row">
										<div class="col-lg-9">
										{if isset($input.maxchar)}
										<div class="input-group">
											<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
												<span class="text-count-down">{$input.maxchar|intval}</span>
											</span>
										{/if}
										<input type="text"
											name="{$input.name|escape:'html':'UTF-8'}"
											id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
											value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
											class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
											{if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
											{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
											{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
											{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
											{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
											{if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}
											/>
										{if isset($input.suffix)}{$input.suffix|escape:'html':'UTF-8'}{/if}
										{if isset($input.maxchar) && $input.maxchar}
										</div>
										{/if}
										</div>
										<div class="col-lg-2">
											<button type="button" class="btn btn-default{if isset($input.button.attributes['class'])} {$input.button.attributes['class']|escape:'html':'UTF-8'}{/if}{if isset($input.button.class)} {$input.button.class|escape:'html':'UTF-8'}{/if}"
												{foreach from=$input.button.attributes key=name item=value}
													{if $name|lower != 'class'}
													 {$name|escape:'html':'UTF-8'}="{$value|escape:'html':'UTF-8'}"
													{/if}
												{/foreach} >
												{$input.button.label|escape:'html':'UTF-8'}
											</button>
										</div>
									</div>
									{if isset($input.maxchar) && $input.maxchar}
									<script type="text/javascript">
										$(document).ready(function() {
											countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
										});
									</script>
									{/if}
								{elseif $input.type == 'swap'}
									<div class="form-group swap-container">
										<div class="col-lg-9">
											<div class="form-control-static row">
												<div class="col-xs-6">
													<select {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} availableSwap" name="{$input.name|escape:'html':'UTF-8'}_available[]" multiple="multiple">
													{foreach $input.options.query AS $option}
														{if is_object($option)}
															{if !in_array($option->$input.options.id, $fields_value[$input.name])}
																<option value="{$option->$input.options.id|escape:'html':'UTF-8'}">{$option->$input.options.name|escape:'html':'UTF-8'}</option>
															{/if}
														{elseif $option == "-"}
															<option value="">-</option>
														{else}
															{if !in_array($option[$input.options.id], $fields_value[$input.name])}
																<option value="{$option[$input.options.id]|escape:'html':'UTF-8'}">{$option[$input.options.name]|escape:'html':'UTF-8'}</option>
															{/if}
														{/if}
													{/foreach}
													</select>
													<a href="#" class="btn btn-default btn-block addSwap">{l s='Add' d='Admin.Actions'} <i class="icon-arrow-right"></i></a>
												</div>
												<div class="col-xs-6">
													<select {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} selectedSwap" name="{$input.name|escape:'html':'UTF-8'}_selected[]" multiple="multiple">
													{foreach $input.options.query AS $option}
														{if is_object($option)}
															{if in_array($option->$input.options.id, $fields_value[$input.name])}
																<option value="{$option->$input.options.id|escape:'html':'UTF-8'}">{$option->$input.options.name|escape:'html':'UTF-8'}</option>
															{/if}
														{elseif $option == "-"}
															<option value="">-</option>
														{else}
															{if in_array($option[$input.options.id], $fields_value[$input.name])}
																<option value="{$option[$input.options.id]|escape:'html':'UTF-8'}">{$option[$input.options.name]|escape:'html':'UTF-8'}</option>
															{/if}
														{/if}
													{/foreach}
													</select>
													<a href="#" class="btn btn-default btn-block removeSwap"><i class="icon-arrow-left"></i> {l s='Remove' mod='paypalwithfee'}</a>
												</div>
											</div>
										</div>
									</div>
								{elseif $input.type == 'select'}
									{if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
										{$input.empty_message|escape:'html':'UTF-8'}
										{$input.required = false}
										{$input.desc = null}
									{else}
										<div class="select-wrapper">
										<select name="{$input.name|escape:'html':'UTF-8'}"
												class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} {*fixed-width-xl*}"
												id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
												{if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
												{if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
												{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if}
												{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}>
											{if isset($input.options.default)}
												<option value="{$input.options.default.value|escape:'html':'UTF-8'}">{$input.options.default.label|escape:'html':'UTF-8'}</option>
											{/if}
											{if isset($input.options.optiongroup)}
												{foreach $input.options.optiongroup.query AS $optiongroup}
													<optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'html':'UTF-8'}">
														{foreach $optiongroup[$input.options.options.query] as $option}
															<option value="{$option[$input.options.options.id]|escape:'html':'UTF-8'}"
																{if isset($input.multiple)}
																	{foreach $fields_value[$input.name] as $field_value}
																		{if $field_value == $option[$input.options.options.id]|escape:'html':'UTF-8'}selected="selected"{/if}
																	{/foreach}
																{else}
																	{if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
																{/if}
															>{$option[$input.options.options.name]|escape:'html':'UTF-8'}</option>
														{/foreach}
													</optgroup>
												{/foreach}
											{else}
												{foreach $input.options.query AS $option}
													{if is_object($option)}
														<option value="{$option->$input.options.id|escape:'html':'UTF-8'}"
															{if isset($input.multiple)}
																{foreach $fields_value[$input.name] as $field_value}
																	{if $field_value == $option->$input.options.id}
																		selected="selected"
																	{/if}
																{/foreach}
															{else}
																{if $fields_value[$input.name] == $option->$input.options.id}
																	selected="selected"
																{/if}
															{/if}
														>{$option->$input.options.name|escape:'html':'UTF-8'}</option>
													{elseif $option == "-"}
														<option value="">-</option>
													{else}
														<option value="{$option[$input.options.id]|escape:'html':'UTF-8'}"
															{if isset($input.multiple)}
																{foreach $fields_value[$input.name] as $field_value}
																	{if $field_value == $option[$input.options.id]}
																		selected="selected"
																	{/if}
																{/foreach}
															{else}
																{if $fields_value[$input.name] == $option[$input.options.id]}
																	selected="selected"
																{/if}
															{/if}
														>{$option[$input.options.name]|escape:'html':'UTF-8'}</option>

													{/if}
												{/foreach}
											{/if}
										</select>
										</div>
									{/if}
								{elseif $input.type == 'radio'}
									{foreach $input.values as $value}
										<div class="radio {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
											{strip}
											<label>
											<input type="radio"	name="{$input.name|escape:'html':'UTF-8'}" id="{$value.id|escape:'html':'UTF-8'}" value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if (isset($input.disabled) && $input.disabled) or (isset($value.disabled) && $value.disabled)} disabled="disabled"{/if}/>
												{$value.label|escape:'html':'UTF-8'}
											</label>
											{/strip}
										</div>
										{if isset($value.p) && $value.p}<p class="help-block">{$value.p|escape:'html':'UTF-8'}</p>{/if}
									{/foreach}
								{elseif $input.type == 'switch'}
                                    <div>
									<span class="btn-switch">
										{foreach $input.values as $value}
											<input type="radio" {if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} name="{$input.name|escape:'html':'UTF-8'}"{if $value.value == 1} id="{$input.name|escape:'html':'UTF-8'}_on" class="btn-switch__radio btn-switch__radio_yes"{else} id="{$input.name|escape:'html':'UTF-8'}_off" class="btn-switch__radio btn-switch__radio_no"{/if} value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if (isset($input.disabled) && $input.disabled) or (isset($value.disabled) && $value.disabled)} disabled="disabled"{/if}/>
										{/foreach}
                                        {foreach $input.values as $value}
                                            {strip}
										<label {if $value.value == 1} for="{$input.name|escape:'html':'UTF-8'}_on" class="btn-switch__label btn-switch__label_yes"{else} for="{$input.name|escape:'html':'UTF-8'}_off" class="btn-switch__label btn-switch__label_no"{/if}>
											{if $value.value == 1}
                                                <span class="btn-switch__txt">
												    {l s='Yes' d='Admin.Global'}
                                                </span>
											{else}
                                                <span class="btn-switch__txt">
												    {l s='No' d='Admin.Global'}
                                                </span>
											{/if}
										</label>
										{/strip}
                                        {/foreach}
									</span>
                                    </div>
								{elseif $input.type == 'textarea'}
									{assign var=use_textarea_autosize value=true}
									{if isset($input.lang) AND $input.lang}
										{foreach $languages as $language}
											{if $languages|count > 1}
											<div class="form-group translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
												<div class="col-lg-9">
											{/if}
													{if isset($input.maxchar) && $input.maxchar}
													<div class="input-group">
														<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
															<span class="text-count-down">{$input.maxchar|intval}</span>
														</span>
													{/if}
													<textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_{$language.id_lang|escape:'html':'UTF-8'}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
													{if isset($input.maxchar) && $input.maxchar}
													</div>
													{/if}
											{if $languages|count > 1}
												</div>
												<div class="col-lg-2">
													<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
														{$language.iso_code|escape:'html':'UTF-8'}
														<span class="caret"></span>
													</button>
													<ul class="dropdown-menu">
														{foreach from=$languages item=language}
														<li>
															<a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a>
														</li>
														{/foreach}
													</ul>
												</div>
											</div>
											{/if}
										{/foreach}
										{if isset($input.maxchar) && $input.maxchar}
											<script type="text/javascript">
											$(document).ready(function(){
											{foreach from=$languages item=language}
												countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter"));
											{/foreach}
											});
											</script>
										{/if}
									{else}
										{if isset($input.maxchar) && $input.maxchar}
											<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
												<span class="text-count-down">{$input.maxchar|intval}</span>
											</span>
										{/if}
										<textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'html':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'html':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'html':'UTF-8'}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
										{if isset($input.maxchar) && $input.maxchar}
											<script type="text/javascript">
											$(document).ready(function(){
												countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
											});
											</script>
										{/if}
									{/if}
								{elseif $input.type == 'checkbox'}
                                    <div class="module-table-holder">
									{if isset($input.expand)}
										<a class="btn btn-default show_checkbox{if strtolower($input.expand.default) == 'hide'} hidden{/if}" href="#">
											<i class="icon-{$input.expand.show.icon|escape:'html':'UTF-8'}"></i>
											{$input.expand.show.text|escape:'html':'UTF-8'}
											{if isset($input.expand.print_total) && $input.expand.print_total > 0}
												<span class="badge">{$input.expand.print_total|escape:'html':'UTF-8'}</span>
											{/if}
										</a>
										<a class="btn btn-default hide_checkbox{if strtolower($input.expand.default) == 'show'} hidden{/if}" href="#">
											<i class="icon-{$input.expand.hide.icon|escape:'html':'UTF-8'}"></i>
											{$input.expand.hide.text|escape:'html':'UTF-8'}
											{if isset($input.expand.print_total) && $input.expand.print_total > 0}
												<span class="badge">{$input.expand.print_total|escape:'html':'UTF-8'}</span>
											{/if}
										</a>
									{/if}
									{if $input.title}<span class="caption">{$input.title|escape:'html':'UTF-8'}</span>{/if}

                                    <table class="table">
                                    <tbody>
                                        <thead>
                                            <tr>
                                                <th>{if $input.title}{$input.title|escape:'html':'UTF-8'}{/if}</th>
                                                <th><input type="checkbox" class="checkAll"></th>
                                            </tr>
                                        </thead>
									{foreach $input.values.query as $value}
										{assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
                                        <tr {*class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}*}">
                                        {strip}
										<th>{$value[$input.values.name]|escape:'html':'UTF-8'}</th>
                                        <th><input type="checkbox" name="{$id_checkbox|escape:'html':'UTF-8'}" id="{$id_checkbox|escape:'html':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"{if isset($value.val)} value="{$value.val|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} /></th>
                                        {/strip}
										</tr>
									{/foreach}
                                    </tbody>
                                    </table>
                                    </div>
								{elseif $input.type == 'search_products'}
									<div class="module-table-holder">
										{if $input.title}<span class="caption">{$input.title|escape:'html':'UTF-8'}</span>{/if}
										<table class="table" id="{$input.type|escape:'html':'UTF-8'}">
											<thead>
												<tr>
													<th>{if $input.title}{$input.title|escape:'html':'UTF-8'}{/if}</th>
													<th></th>
												</tr>
												<tr>
													<th colspan="2">
														<input type="text" id="selectProduct" placeholder="{l s='Product ID, Reference, EAN13..' mod='paypalwithfee'}">
														<div class="resultados hidden"></div>
													</th>
												</tr>
											</thead>
											<tbody>
												{foreach $input.values.query as $value}
													{assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
													<tr class="exception" id_product="{$id_checkbox|escape:'html':'UTF-8'}">
														<th><span>{$value[$input.values.name]|escape:'html':'UTF-8'}</span><input type="hidden" id="productException{$id_checkbox|escape:'html':'UTF-8'}" name="products[]" value="{$value[$input.values.id]|escape:'html':'UTF-8'}"></th>
														<th class="remove_exception_product" id_product="{$id_checkbox|escape:'html':'UTF-8'}"><i></i></th>
													</tr>
												{/foreach}
											</tbody>
										</table>
									</div>
									<script>
										$(document).ready(function(){
											var url = "{$input.ajax_url|escape:'htmlall':'UTF-8'}";

											$(document).on('keyup', '#selectProduct', function(e){

											if ($(this).val().length) {
												keyword = $(this).val();
												setTimeout(function(){

													$.ajax({
														type : 'POST',
														url : url,
														async: false,
														data : {
														'keyword' : keyword,
														},
														success: function(data){
															$(".resultados").removeClass( "hidden" );
															if ( $("#reslist").length > 0 ) {
																$(".reslist").remove();
																}
															data=data.replace('"','');
															$( ".resultados" ).append(data.replace('"',''));
														},
													});
												}, 500);

											}else{

												$(".reslist").remove();
												$( ".resultados" ).addClass( "hidden" );

											}

											});

											$(document).on('click', '.add_exception_product', function(e){

												id_product = $(this).attr('value');
												name = $(this).text();




												row = $('<tr>').addClass('exception').attr('id_product', id_product).append('<th>'+name+'</th>').append('<th class="remove_exception_product" id_product="'+id_product+'"><i></i></th>');

												assignedRow = $('.exception[id_product="'+id_product+'"]');

												if (!assignedRow.length){

													$(this).closest('table#search_products').find('tbody').append(row);

													$(this).closest('form').append($('<input type="hidden" id="productException'+id_product+'"  name="products[]" value="'+id_product+'">'));

												}else{

												}

												$(".reslist").remove();

											});

											$(document).on('click', '.remove_exception_product', function(e){
												id_product = $(this).attr('id_product');
												$(this).closest('.exception').remove();
												$('form#cod #productException'+id_product).remove();
											});
										});
									</script>
								{elseif $input.type == 'search_categories'}
									<div class="module-table-holder">
										{if $input.title}<span class="caption">{$input.title|escape:'html':'UTF-8'}</span>{/if}
										{$input.render->render()}
									</div>
								{elseif $input.type == 'change-password'}
									<div class="row">
										<div class="col-lg-12">
											<button type="button" id="{$input.name|escape:'html':'UTF-8'}-btn-change" class="btn btn-default">
												<i class="icon-lock"></i>
												{l s='Change password...' mod='paypalwithfee'}
											</button>
											<div id="{$input.name|escape:'html':'UTF-8'}-change-container" class="form-password-change well hide">
												<div class="form-group">
													<label for="old_passwd" class="control-label col-lg-2 required">
														{l s='Current password' mod='paypalwithfee'}
													</label>
													<div class="col-lg-10">
														<div class="input-group fixed-width-lg">
															<span class="input-group-addon">
																<i class="icon-unlock"></i>
															</span>
															<input type="password" id="old_passwd" name="old_passwd" class="form-control" value="" required="required" autocomplete="off">
														</div>
													</div>
												</div>
												<hr />
												<div class="form-group">
													<label for="{$input.name|escape:'html':'UTF-8'}" class="required control-label col-lg-2">
														<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Password should be at least 8 characters long.' mod='paypalwithfee'}">
															{l s='New password' mod='paypalwithfee'}
														</span>
													</label>
													<div class="col-lg-9">
														<div class="input-group fixed-width-lg">
															<span class="input-group-addon">
																<i class="icon-key"></i>
															</span>
															<input type="password" id="{$input.name|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" value="" required="required" autocomplete="off"/>
														</div>
														<span id="{$input.name|escape:'html':'UTF-8'}-output"></span>
													</div>
												</div>
												<div class="form-group">
													<label for="{$input.name|escape:'html':'UTF-8'}2" class="required control-label col-lg-2">
														{l s='Confirm password' mod='paypalwithfee'}
													</label>
													<div class="col-lg-4">
														<div class="input-group fixed-width-lg">
															<span class="input-group-addon">
																<i class="icon-key"></i>
															</span>
															<input type="password" id="{$input.name|escape:'html':'UTF-8'}2" name="{$input.name|escape:'html':'UTF-8'}2" class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" value="" autocomplete="off"/>
														</div>
													</div>
												</div>
												<div class="form-group">
													<div class="col-lg-10 col-lg-offset-2">
														<input type="text" class="form-control fixed-width-md pull-left" id="{$input.name|escape:'html':'UTF-8'}-generate-field" disabled="disabled">
														<button type="button" id="{$input.name|escape:'html':'UTF-8'}-generate-btn" class="btn btn-default">
															<i class="icon-random"></i>
															{l s='Generate password' mod='paypalwithfee'}
														</button>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-12">
														<button type="button" id="{$input.name|escape:'html':'UTF-8'}-cancel-btn" class="btn btn-default">
															<i class="icon-remove"></i>
															{l s='Cancel' d='Admin.Actions'}
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
									<script>
										$(function(){
											var $oldPwd = $('#old_passwd');
											var $passwordField = $('#{$input.name|escape:'html':'UTF-8'}');
											var $output = $('#{$input.name|escape:'html':'UTF-8'}-output');
											var $generateBtn = $('#{$input.name|escape:'html':'UTF-8'}-generate-btn');
											var $generateField = $('#{$input.name|escape:'html':'UTF-8'}-generate-field');
											var $cancelBtn = $('#{$input.name|escape:'html':'UTF-8'}-cancel-btn');

											var feedback = [
												{ badge: 'text-danger', text: '{l s="Invalid" js=1 mod='paypalwithfee'}'  },
												{ badge: 'text-warning', text: '{l s="Okay" js=1 mod='paypalwithfee'}'  },
												{ badge: 'text-success', text: '{l s="Good" js=1 mod='paypalwithfee'}'  },
												{ badge: 'text-success', text: '{l s="Fabulous" js=1 mod='paypalwithfee'}'  }
											];
											$.passy.requirements.length.min = 8;
											$.passy.requirements.characters = 'DIGIT';
											$passwordField.passy(function(strength, valid) {
												$output.text(feedback[strength].text);
												$output.removeClass('text-danger').removeClass('text-warning').removeClass('text-success');
												$output.addClass(feedback[strength].badge);
												if (valid){
													$output.show();
												}
												else {
													$output.hide();
												}
											});
											var $container = $('#{$input.name|escape:'html':'UTF-8'}-change-container');
											var $changeBtn = $('#{$input.name|escape:'html':'UTF-8'}-btn-change');
											var $confirmPwd = $('#{$input.name|escape:'html':'UTF-8'}2');

											$changeBtn.on('click',function(){
												$container.removeClass('hide');
												$changeBtn.addClass('hide');
											});
											$generateBtn.click(function() {
												$generateField.passy( 'generate', 8 );
												var generatedPassword = $generateField.val();
												$passwordField.val(generatedPassword);
												$confirmPwd.val(generatedPassword);
											});
											$cancelBtn.on('click',function() {
												$container.find("input").val("");
												$container.addClass('hide');
												$changeBtn.removeClass('hide');
											});

											$.validator.addMethod('password_same', function(value, element) {
												return $passwordField.val() == $confirmPwd.val();
											}, '{l s="Invalid password confirmation" js=1  mod="paypalwithfee"}');

											$('#employee_form').validate({
												rules: {
													"email": {
														email: true
													},
													"{$input.name}" : {
														minlength: 8
													},
													"{$input.name}2": {
														password_same: true
													},
													"old_passwd" : {},
												},
												// override jquery validate plugin defaults for bootstrap 3
												highlight: function(element) {
													$(element).closest('.form-group').addClass('has-error');
												},
												unhighlight: function(element) {
													$(element).closest('.form-group').removeClass('has-error');
												},
												errorElement: 'span',
												errorClass: 'help-block',
												errorPlacement: function(error, element) {
													if(element.parent('.input-group').length) {
														error.insertAfter(element.parent());
													} else {
														error.insertAfter(element);
													}
												}
											});
										});
									</script>
								{elseif $input.type == 'password'}
									<div class="input-group fixed-width-lg">
										<span class="input-group-addon">
											<i class="icon-key"></i>
										</span>
										<input type="password"
											id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
											name="{$input.name|escape:'html':'UTF-8'}"
											class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
											value=""
											{if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if}
											{if isset($input.required) && $input.required } required="required" {/if} />
									</div>

								{elseif $input.type == 'birthday'}
								<div class="form-group">
									{foreach $input.options as $key => $select}
									<div class="col-lg-2">
										<select name="{$key|escape:'html':'UTF-8'}" class="fixed-width-lg{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
											<option value="">-</option>
											{if $key == 'months'}
												{*
													This comment is useful to the translator tools /!\ do not remove them
													{l s='January' mod='paypalwithfee'}}
													{l s='February' mod='paypalwithfee'}}
													{l s='March' mod='paypalwithfee'}}
													{l s='April' mod='paypalwithfee'}}
													{l s='May' mod='paypalwithfee'}}
													{l s='June' mod='paypalwithfee'}}
													{l s='July' mod='paypalwithfee'}}
													{l s='August' mod='paypalwithfee'}}
													{l s='September' mod='paypalwithfee'}}
													{l s='October' mod='paypalwithfee'}}
													{l s='November' mod='paypalwithfee'}}
													{l s='December' mod='paypalwithfee'}}
												*}
												{foreach $select as $k => $v}
													<option value="{$k|escape:'html':'UTF-8'}" {if $k == $fields_value[$key]}selected="selected"{/if}>{$v|escape:'html':'UTF-8'}</option>
												{/foreach}
											{else}
												{foreach $select as $v}
													<option value="{$v|escape:'html':'UTF-8'}" {if $v == $fields_value[$key]}selected="selected"{/if}>{$v|escape:'html':'UTF-8'}</option>
												{/foreach}
											{/if}
										</select>
									</div>
									{/foreach}
								</div>
								{elseif $input.type == 'group'}
									{assign var=groups value=$input.values}
									{include file='helpers/form/form_group.tpl'}
								{elseif $input.type == 'shop'}
									{$input.html}
								{elseif $input.type == 'categories'}
									{$categories_tree|escape:'html':'UTF-8'}
								{elseif $input.type == 'file'}
									{$input.file|escape:'html':'UTF-8'}
								{elseif $input.type == 'categories_select'}
									{$input.category_tree|escape:'html':'UTF-8'}
								{elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
									{$asso_shop|escape:'html':'UTF-8'}
								{elseif $input.type == 'color'}
								<div class="form-group">
									<div class="col-lg-2">
										<div class="row">
											<div class="input-group">
												<input type="color"
												data-hex="true"
												{if isset($input.class)} class="{$input.class|escape:'html':'UTF-8'}"
												{else} class="color mColorPickerInput"{/if}
												name="{$input.name|escape:'html':'UTF-8'}"
												value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
											</div>
										</div>
									</div>
								</div>
								{elseif $input.type == 'date'}
									<div class="row">
										<div class="input-group col-lg-4">
											<input
												id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
												type="text"
												data-hex="true"
												{if isset($input.class)} class="{$input.class|escape:'html':'UTF-8'}"
												{else}class="datepicker"{/if}
												name="{$input.name|escape:'html':'UTF-8'}"
												value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
											<span class="input-group-addon">
												<i class="icon-calendar-empty"></i>
											</span>
										</div>
									</div>
								{elseif $input.type == 'datetime'}
									<div class="row">
										<div class="input-group col-lg-4">
											<input
												id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
												type="text"
												data-hex="true"
												{if isset($input.class)} class="{$input.class|escape:'html':'UTF-8'}"
												{else} class="datetimepicker"{/if}
												name="{$input.name|escape:'html':'UTF-8'}"
												value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
											<span class="input-group-addon">
												<i class="icon-calendar-empty"></i>
											</span>
										</div>
									</div>
								{elseif $input.type == 'free'}
									{$fields_value[$input.name]|escape:'html':'UTF-8'}
								{elseif $input.type == 'html'}
									{if isset($input.html_content)}
										<hr>
										<h2>{$input.html_content}</h2>
									{else}
										{$input.name|escape:'html':'UTF-8'}
									{/if}
								{elseif $input.type == 'fw_renderlist'}
									{$input.renderlist|escape:'html':'UTF-8'}
								{/if}
								{/block}{* end block input *}
								{block name="description"}
									{if isset($input.desc) && !empty($input.desc)}
										<p class="help-block">
											{if is_array($input.desc)}
												{foreach $input.desc as $p}
													{if is_array($p)}
														<span id="{$p.id|escape:'html':'UTF-8'}">{$p.text|escape:'html':'UTF-8'}</span><br />
													{else}
														{$p|escape:'html':'UTF-8'}<br />
													{/if}
												{/foreach}
											{else}
												{$input.desc}
											{/if}
										</p>
									{/if}
								{/block}
								</div>
                                {if isset($input.hint)}
									<i data-text="{if is_array($input.hint)}
                                        {foreach $input.hint as $hint}
														{if is_array($hint)}
															{$hint.text|escape:'html':'UTF-8'}
														{else}
															{$hint|escape:'html':'UTF-8'}
														{/if}
													{/foreach}
												{else}
													{$input.hint|escape:'html':'UTF-8'}
												{/if}" class="module-input-circle">
                                    </i>
								{/if}
							{/block}{* end block field *}
						{/if}
						</div>
						{/block}
                    {/if}
					{/foreach}
					{hook h='displayAdminForm' fieldset=$f}
					{if isset($name_controller)}
						{capture name=hookName assign=hookName}display{$name_controller|ucfirst|escape:'htmlall':'UTF-8'}Form{/capture}
						{hook h=$hookName fieldset=$f}
					{elseif isset($smarty.get.controller)}
						{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities|escape:'htmlall':'UTF-8'}Form{/capture}
						{hook h=$hookName fieldset=$f}
					{/if}
                    </div>
				</div><!-- /.form-wrapper -->
                {$count_tab = $count_tab + 1}
                {/foreach}
                {/if}
				{elseif $key == 'desc'}
					<div class="alert alert-info col-lg-offset-3">
						{if is_array($field)}
							{foreach $field as $k => $p}
								{if is_array($p)}
									<span{if isset($p.id)} id="{$p.id|escape:'html':'UTF-8'}"{/if}>{$p.text|escape:'html':'UTF-8'}</span><br />
								{else}
									{$p|escape:'html':'UTF-8'}
									{if isset($field[$k+1])}<br />{/if}
								{/if}
							{/foreach}
						{else}
							{$field|escape:'html':'UTF-8'}
						{/if}
					</div>
				{/if}
				{block name="other_input"}{/block}
			{/foreach}
			{block name="footer"}
			{capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
				{if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
					<div class="module-panel-footer">
		            <div class="module-tab-section-footer">
						{if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
						<button type="submit" value="1"	id="{if isset($fieldset['form']['submit']['id'])}{$fieldset['form']['submit']['id']|escape:'html':'UTF-8'}{else}{$table|escape:'html':'UTF-8'}_form_submit_btn{/if}{if $smarty.capture.form_submit_btn > 1}_{($smarty.capture.form_submit_btn - 1)|intval}{/if}" name="{if isset($fieldset['form']['submit']['name'])}{$fieldset['form']['submit']['name']|escape:'html':'UTF-8'}{else}{$submit_action|escape:'html':'UTF-8'}{/if}{if isset($fieldset['form']['submit']['stay']) && $fieldset['form']['submit']['stay']}AndStay{/if}" class="module-footer-btn{*{if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']}{else}btn btn-default pull-right{/if}*}">
							<i class="module-save-module-btn{*{if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']}{else}process-icon-save{/if}*}"></i> {$fieldset['form']['submit']['title']|escape:'html':'UTF-8'}
						</button>
                        <span class="module-save-config"></span>
						{/if}
						{*{if isset($show_cancel_button) && $show_cancel_button}
						<a class="btn btn-default" {if $table}id="{$table}_form_cancel_btn"{/if} onclick="javascript:window.history.back();">
							<i class="process-icon-cancel"></i> {l s='Cancel' d='Admin.Actions'}
						</a>
						{/if}*}
						{*{if isset($fieldset['form']['reset'])}
						<button
							type="reset"
							id="{if isset($fieldset['form']['reset']['id'])}{$fieldset['form']['reset']['id']}{else}{$table}_form_reset_btn{/if}"
							class="{if isset($fieldset['form']['reset']['class'])}{$fieldset['form']['reset']['class']}{else}btn btn-default{/if}"
							>
							{if isset($fieldset['form']['reset']['icon'])}<i class="{$fieldset['form']['reset']['icon']}"></i> {/if} {$fieldset['form']['reset']['title']}
						</button>
						{/if}
						{if isset($fieldset['form']['buttons'])}
						{foreach from=$fieldset['form']['buttons'] item=btn key=k}
							{if isset($btn.href) && trim($btn.href) != ''}
								<a href="{$btn.href}" {if isset($btn['id'])}id="{$btn['id']}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']}{/if}" {if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.title}</a>
							{else}
								<button type="{if isset($btn['type'])}{$btn['type']}{else}button{/if}" {if isset($btn['id'])}id="{$btn['id']}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']}{/if}" name="{if isset($btn['name'])}{$btn['name']}{else}submitOptions{$table}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.title}</button>
							{/if}
						{/foreach}
						{/if}*}
                    </div>
					</div>
				{/if}
			{/block}
		</div>
		{/block}
		{block name="other_fieldsets"}{/block}
	{/foreach}
</form>
{/block}
{block name="after"}{/block}

{if isset($tinymce) && $tinymce}
<script type="text/javascript">
	var iso = '{$iso|addslashes|escape:'htmlall':'UTF-8'}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_|addslashes|escape:'htmlall':'UTF-8'}';
	var ad = '{$ad|addslashes|escape:'htmlall':'UTF-8'}';

	$(document).ready(function(){
		{block name="autoload_tinyMCE"}
			tinySetup({
				editor_selector :"autoload_rte"
			});
		{/block}
	});
</script>
{/if}
{if isset($color) && $color}
<script type="text/javascript">
	$.fn.mColorPicker.defaults.imageFolder = baseDir + 'img/admin/';
</script>
{/if}
{if $firstCall}
	<script type="text/javascript">
		var module_dir = '{$smarty.const._MODULE_DIR_|escape:'html':'UTF-8'}';
		var id_language = {$defaultFormLanguage|intval};
		var languages = new Array();
		var vat_number = {if isset($vat_number)}1{else}0{/if};
		// Multilang field setup must happen before document is ready so that calls to displayFlags() to avoid
		// precedence conflicts with other document.ready() blocks
		{foreach $languages as $k => $language}
			languages[{$k}] = {
				id_lang: {$language.id_lang|escape:'javascript':'UTF-8'},
				iso_code: '{$language.iso_code|escape:'javascript':'UTF-8'}',
				name: '{$language.name|escape:'javascript':'UTF-8'}',
				is_default: '{$language.is_default|escape:'javascript':'UTF-8'}'
			};
		{/foreach}
		// we need allowEmployeeFormLang var in ajax request
		allowEmployeeFormLang = {$allowEmployeeFormLang|intval};
		displayFlags(languages, id_language, allowEmployeeFormLang);

		$(document).ready(function() {

			$(".show_checkbox").click(function () {
				$(this).addClass('hidden')
				$(this).siblings('.checkbox').removeClass('hidden');
				$(this).siblings('.hide_checkbox').removeClass('hidden');
				$(this).closest('.module-table-holder').find('.table').removeClass('hidden');

				return false;
			});
			$(".hide_checkbox").click(function () {
				$(this).addClass('hidden')
				$(this).siblings('.checkbox').addClass('hidden');
				$(this).siblings('.show_checkbox').removeClass('hidden');
				$(this).closest('.module-table-holder').find('.table').addClass('hidden');
				return false;
			});

			{if isset($fields_value.id_state)}
				if ($('#id_country') && $('#id_state'))
				{
					ajaxStates({$fields_value.id_state|escape:'html':'UTF-8'});
					$('#id_country').change(function() {
						ajaxStates();
					});
				}
			{/if}

			if ($(".datepicker").length > 0)
				$(".datepicker").datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});

			if ($(".datetimepicker").length > 0)
			$('.datetimepicker').datetimepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd',
				// Define a custom regional settings in order to use PrestaShop translation tools
				currentText: '{l s='Now' js=1 mod='paypalwithfee'}',
				closeText: '{l s='Done' js=1 mod='paypalwithfee'}',
				ampm: false,
				amNames: ['AM', 'A'],
				pmNames: ['PM', 'P'],
				timeFormat: 'hh:mm:ss tt',
				timeSuffix: '',
				timeOnlyTitle: "{l s='Choose Time' js=1 mod='paypalwithfee'}",
				timeText: '{l s='Time' js=1 mod='paypalwithfee'}',
				hourText: '{l s='Hour' js=1 mod='paypalwithfee'}',
				minuteText: '{l s='Minute' js=1 mod='paypalwithfee'}',
			});
			{if isset($use_textarea_autosize)}
			$(".textarea-autosize").autosize();
			{/if}
		});

        $(document).ready(function(){

	$(document).on('click', '.module-tab-section-top', function(e){

		if ($(this).hasClass('tab-closed')){
			$(this).removeClass('tab-closed');
		}else{
			$(this).addClass('tab-closed');
		}

		$(this).parent().find('.module-tab-section-content').slideToggle();
	});

	$(document).on('click', '._tab_select', function(e){
		selected_tab = $(this).attr('open-tab');

		$('._tab_select').each(function(key, value){

			if ($(value).attr('open-tab') === selected_tab){

				$(value).addClass('module-tab-select-active');

				$('#selected_tab_input').val(selected_tab);

			}else{

				$(value).removeClass('module-tab-select-active');

			}
		});

		$('.module-tab-container').each(function(key, value){
			if ($(value).attr('id') != selected_tab){

				$(value).fadeOut('fast');
				$(value).removeClass('module-tab-active');

			}else{

				$(value).fadeIn('fast');
				$(value).addClass('module-tab-active');

			}
		});
	});

	$(document).on('mouseover mouseout', '.module-input-circle', function(e){

		var hover = $(this).is(':hover');

		if (hover && $('.module-info-box').length == 0) {

			var msg = $(this).attr('data-text');

			var msgBox = $('<div>').addClass('module-info-box').text(msg);
		    msgBox.css( 'position', 'absolute' );
		    msgBox.css( 'margin-left', '25px' );

			$(this).append(msgBox);

		}else{
			$('.module-info-box').fadeOut('fast', function(){
				$('.module-info-box').remove();
			});
		}
	});

});

$(document).on('click', '.checkAll', function(e){
	    	if ($(this).is(':checked')){
	    		$(this).closest('table').find('tbody input[type="checkbox"]').prop('checked', true)
	    	}else{
	    		$(this).closest('table').find('tbody input[type="checkbox"]').prop('checked', false)
	    	}
	    });


	state_token = '{getAdminToken tab='AdminStates'}';
	{block name="script"}{/block}
	</script>
{/if}
</div>
