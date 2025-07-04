{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($vatConfirmation) && !empty($vatConfirmation)}
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
        {if count($vatConfirmation) <= 1}
            <p>{$vatConfirmation[0]}</p>
        {else}
            <ol>
                {foreach $vatConfirmation as $conf}
                    <li>{$conf|escape:'htmlall':'UTF-8'}</li>
                {/foreach}
            </ol>
        {/if}
    </div>
{/if}
{if isset($vatErrors) && !empty($vatErrors)}
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
        <ol>
            {foreach $vatErrors as $vatError}
                <li>{$vatError|escape:'htmlall':'UTF-8'}</li>
            {/foreach}
        </ol>
    </div>
{/if}
<div id="validateVATTabsController" class="VATTabs">
    <ul class="validateVATUl">
        <li><a href={$vatNumberLink|escape:'htmlall':'UTF-8'}> <i class="icon-backward"></i> {l s='Settings' mod='validatevatnumber'}</a></li>
        <li class="active"><a><i class="icon-list"></i> {l s='VAT Numbers' mod='validatevatnumber'}</a></li>
        <li><a href="{$vatNumberListController|escape:'htmlall':'UTF-8'}&updateUsersVAT=1"><i class="icon-check"></i> {l s='Verify all VATs in shop' mod='validatevatnumber'}</a></li>
    </ul>
</div>

