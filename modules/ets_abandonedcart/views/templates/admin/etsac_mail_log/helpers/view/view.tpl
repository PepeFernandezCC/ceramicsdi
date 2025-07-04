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

<div class="ets-abancart-overload ets_ac_mail_log_form">
	<div class="table">
		<div class="table-cell">
			<div class="ets-abancart-view">
				<span class="ets-abancart-close-view"></span>
				<div class="ets-abancart-content">
                    <div class="panel">
                        <div class="panel-heading">
                            <span>{l s='Mail log' mod='ets_abandonedcart'}{if !empty($object.customer_name)}: {$object.customer_name|escape:'html':'UTF-8'}{if !empty($object.email)} ({$object.email|escape:'html':'UTF-8'}){/if}{/if}</span>
                        </div>
                        <div class="form-wrapper">
                            <div class="subject">{$object.subject nofilter}</div>
                            <div class="content">{$object.content nofilter}</div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>