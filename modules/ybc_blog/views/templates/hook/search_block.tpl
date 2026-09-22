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
<div class="block ybc_block_search {$blog_config.YBC_BLOG_RTL_CLASS|escape:'html':'UTF-8'}">
    <h4 class="title_blog title_block">{l s='Search in blog' mod='ybc_blog'}</h4>
    <div class="content_block block_content">
        <form action="{$action|escape:'html':'UTF-8'}" method="post">
            <input class="form-control" type="text" name="blog_search" placeholder="{l s='Type in key words...' mod='ybc_blog'}" value="{$search|escape:'html':'UTF-8'}" />
            <input class="button" type="submit" value="{l s='Search' mod='ybc_blog'}" />
            <span class="icon_search"></span>
        </form>
    </div>
</div>
