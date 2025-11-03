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
{if isset($is_17) && $is_17}
    <footer class="page-footer">
      <a href="{if isset($my_account_link) && $my_account_link}{$my_account_link|escape:'html':'UTF-8'}{else}#{/if}" class="account-link">
        <i class="material-icons">chevron_left</i>
        <span>{l s='Back to your account' mod='ybc_blog'}</span>
      </a>
      <a href="{if isset($home_link) && $home_link}{$home_link|escape:'html':'UTF-8'}{else}#{/if}" class="account-link">
        <i class="material-icons">home</i>
        <span>{l s='Home' mod='ybc_blog'}</span>
      </a>
    </footer>
{else}
    <ul class="footer_links clearfix">
    	<li>
    		<a class="btn btn-default button button-small" href="{if isset($my_account_link) && $my_account_link}{$my_account_link|escape:'html':'UTF-8'}{else}#{/if}">
    			<span><i class="icon-chevron-left"></i> {l s='Back to your account' mod='ybc_blog'}</span>
    		</a>
    	</li>
    	<li>
    		<a class="btn btn-default button button-small" href="{if isset($home_link) && $home_link}{$home_link|escape:'html':'UTF-8'}{else}#{/if}">
    			<span><i class="icon-chevron-left"></i> {l s='Home' mod='ybc_blog'}</span>
    		</a>
    	</li>
    </ul>
{/if}
