{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<h4 class="ets_abancart_title">{l s='Reminder log' mod='ets_abandonedcart'}</h4>
<div class="content_reminder">
    {if isset($LOGs) && $LOGs || isset($cartLogs) && $cartLogs}
		<ul class="ets_abancart_log">
			{if isset($LOGs) && $LOGs}
                {foreach from=$LOGs item="LOG"}
    				<li class="ets_abancart_item">
    					{foreach from=$LOG item='L'}
    						<span>{$L nofilter}</span><br>
    					{/foreach}
    				</li>
    			{/foreach}
            {/if}
		</ul>
	{else}
		<p class="alert alert-info">{l s='No log' mod='ets_abandonedcart'}</p>
	{/if}
</div>