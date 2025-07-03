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
<!-- MODULE ybc_blog -->
<link rel="stylesheet" href="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/css/fix17.css")|escape:'html':'UTF-8'}" type="text/css" media="all" />
{if $author && !$suppened}
<li class="lnk_ybc_blog">
	<a href="{$link->getModuleLink('ybc_blog','managementblog',array(),true)|escape:'html':'UTF-8'}" title="{l s='Blog management' mod='ybc_blog'}">
		<i class="icon-user">&nbsp;</i>
        <span>{l s='My blog posts' mod='ybc_blog'}</span>
	</a>
</li>
{/if}
<li class="lnk_ybc_blog">
	<a href="{$link->getModuleLink('ybc_blog','managementcomments',array(),true)|escape:'html':'UTF-8'}" title="{l s='My blog comments' mod='ybc_blog'}">
		<i class="icon-comments">&nbsp;</i>
        <span>{l s='My blog comments' mod='ybc_blog'}</span>
	</a>
</li>
<li class="lnk_ybc_blog">
	<a href="{$link->getModuleLink('ybc_blog','managementmyinfo',array(),true)|escape:'html':'UTF-8'}" title="{l s='My blog info' mod='ybc_blog'}">
		<i class="fa fa-file-text-o">&nbsp;</i>
        <span>{l s='My blog info' mod='ybc_blog'}</span>
	</a>
</li>
<!-- END : MODULE ybc_blog -->