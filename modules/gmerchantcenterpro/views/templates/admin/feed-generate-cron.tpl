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
	{assign var=sep value="\n"}
	{foreach from=$aErrors name=condition key=nKey item=aError}
		{$aError.msg|escape:'htmlall':'UTF-8'}{$sep|escape:'htmlall':'UTF-8'}
		{if $bDebug == true}
			{if !empty($aError.code)}{l s='Error code' mod='gmerchantcenterpro'} : {$aError.code|escape:'htmlall':'UTF-8'}{$sep|escape:'htmlall':'UTF-8'}{/if}
			{if !empty($aError.file)}{l s='Error file' mod='gmerchantcenterpro'} : {$aError.file|escape:'htmlall':'UTF-8'}{$sep|escape:'htmlall':'UTF-8'}{/if}
			{if !empty($aError.line)}{l s='Error line' mod='gmerchantcenterpro'} : {$aError.line|escape:'htmlall':'UTF-8'}{$sep|escape:'htmlall':'UTF-8'}{/if}
			{if !empty($aError.context)}{l s='Error context' mod='gmerchantcenterpro'} : {$aError.context|escape:'htmlall':'UTF-8'}{$sep|escape:'htmlall':'UTF-8'}{/if}
		{/if}
	{/foreach}
{/if}