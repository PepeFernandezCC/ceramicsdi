{*
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
*}
<div class="bootstrap">
	<div class="alert alert-warning">
	<ul class="list-unstyled">
		<li>{l s='Please Fix the duplicate URLs below(if any) in order to avoid 404 errors.' mod='prettyurls'}</li>
	</ul>
	</div>
</div>

<div class="alert alert-info">{l s='The following tables shows products and categories sharing same URL which should NOT happen. Click on the ID to go into edit page!' mod='prettyurls'}</div>
<div class="panel">
	<div class="panel-heading"><i class="icon-cogs"></i> {l s='Product URLs' mod='prettyurls'}</div>
	<div class="row row-margin-bottom">
	<table class="table">
		<thead>
			<tr>
				<th class="text-left"><span class="title_box active">{l s='ID' mod='prettyurls'}</span></th>
				<th class="text-left"><span class="title_box active">{l s='Name' mod='prettyurls'}</span></th>
				<th class="text-left"><span class="title_box active" style="color: red">{l s='Link Rewrite' mod='prettyurls'}</span></th>
				<th class="text-center"><span class="title_box active">{l s='Occurance' mod='prettyurls'}</span></th>
			</tr>
		</thead>
		<tbody>
			{if empty($product_coll)}
				<tr><td>{l s='No Collisions found' mod='prettyurls'}</td></tr>
			{else}
				{foreach from=$product_coll item=collision}
				{if strpos($collision.id_product, ',') !== false}
				{assign var="idslink" value=","|explode:$collision.id_product}
					<tr>
						<td class="text-left" style="max-width: 200px; word-wrap: break-word;">
							{if is_array($idslink)}
							{assign var="idlnk_count" value=$idslink|count}
								{foreach from=$idslink key=c item=idlnk}
									<a href="{$lnk->getAdminLink('AdminProducts',true,['id_product'=>$idlnk])|escape:'htmlall':'UTF-8'}" target="_blank">{$idlnk|escape:'htmlall':'UTF-8'}</a>{if $c < $idlnk_count-1},{/if}
								{/foreach}
							{else}
								{$collision.id_product|escape:'htmlall':'UTF-8'}
							{/if}
						</td>
						<td class="text-left">{$collision.name|escape:'htmlall':'UTF-8'}</td>
						<td class="text-left">{$collision.link_rewrite|escape:'htmlall':'UTF-8'}</td>
						<td class="text-center">{if $langs_active > 1}{$collision.times|escape:'htmlall':'UTF-8' - $langs_active|escape:'htmlall':'UTF-8'}{else}{$collision.times|escape:'htmlall':'UTF-8'}{/if}</td>
					</tr>
					{/if}
				{/foreach}
			{/if}
		</tbody>
	</table>
</div>
</div>

<div class="panel">
	<div class="panel-heading"><i class="icon-cogs"></i> {l s='Category URLs' mod='prettyurls'}</div>
	<div class="row row-margin-bottom">
	<table class="table">
		<thead>
			<tr>
				<th class="text-left"><span class="title_box active">{l s='ID' mod='prettyurls'}</span></th>
				<th class="text-left"><span class="title_box active">{l s='Name' mod='prettyurls'}</span></th>
				<th class="text-left"><span class="title_box active" style="color: red">{l s='Link Rewrite' mod='prettyurls'}</span></th>
				<th class="text-center"><span class="title_box active">{l s='Occurance' mod='prettyurls'}</span></th>
			</tr>
		</thead>
		<tbody>
			{if empty($category_coll)}
				<tr><td>{l s='No Collisions found' mod='prettyurls'}</td></tr>
			{else}
			{foreach from=$category_coll item=collision}
				{if strpos($collision.id_category, ',') !== false}
				{assign var="idslink" value=","|explode:$collision.id_category}
					<tr>
						<td class="text-left" style="max-width: 200px; word-wrap: break-word;">
							{if is_array($idslink)}
								{assign var="idlnk_count" value=$idslink|count}
									{foreach from=$idslink key=c item=idlnk}
										<a href="{$lnk->getAdminLink('AdminCategories',true,['action' => 'updatecategory','id_category'=>$idlnk])|escape:'htmlall':'UTF-8'}" target="_blank">{$idlnk|escape:'htmlall':'UTF-8'}</a>{if $c < $idlnk_count-1},{/if}
									{/foreach}
							{else}
								{$collision.id_product|escape:'htmlall':'UTF-8'}
							{/if}	
						</td>
						<td class="text-left">{$collision.name|escape:'htmlall':'UTF-8'}</td>
						<td class="text-left">{$collision.link_rewrite|escape:'htmlall':'UTF-8'}</td>
						<td class="text-center">{if $langs_active > 1}{$collision.times|escape:'htmlall':'UTF-8' - $langs_active|escape:'htmlall':'UTF-8'}{else}{$collision.times|escape:'htmlall':'UTF-8'}{/if}</td>
					</tr>
				{/if}
			{/foreach}
			{/if}
		</tbody>
	</table>
</div>
</div>

<div class="panel">
	<div class="panel-heading"><i class="icon-cogs"></i> {l s='Conflicts Between Category and Product URLs' mod='prettyurls'}</div>
	<div class="row row-margin-bottom">
	<table class="table">
		<thead>
			<tr>
				<th class="text-left"><span class="title_box active">{l s='ID Product' mod='prettyurls'}</span></th>
				<th class="text-left"><span class="title_box active">{l s='ID Category' mod='prettyurls'}</span></th>
				<th class="text-left"><span class="title_box active" style="color: red">{l s='Shared Link Rewrite' mod='prettyurls'}</span></th>
			</tr>
		</thead>
		<tbody>
			{if empty($compare_coll)}
				<tr><td>{l s='No Collisions found' mod='prettyurls'}</td></tr>
			{else}
			{foreach from=$compare_coll item=collision}
				<tr>
					<td class="text-left"><a href="{$lnk->getAdminLink('AdminProducts',true,['id_product'=>$collision.id_product])}" target="_blank">{$collision.id_product|escape:'htmlall':'UTF-8'}</a></td>
					<td class="text-left"><a href="{$lnk->getAdminLink('AdminCategories',true,['action' => 'updatecategory','id_category'=>$collision.id_category])|escape:'htmlall':'UTF-8'}" target="_blank">{$collision.id_category|escape:'htmlall':'UTF-8'}</a></td>
					<td class="text-left">{$collision.link_rewrite|escape:'htmlall':'UTF-8'}</td>
				</tr>
			{/foreach}
			{/if}
		</tbody>
	</table>
</div>
</div>