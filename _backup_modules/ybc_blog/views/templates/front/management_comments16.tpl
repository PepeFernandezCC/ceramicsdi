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
<script type="text/javascript">
        var ybc_blog_invalid_file="{l s='Invalid file' mod='ybc_blog'}";
</script>
<h1 class="page-heading">{l s='My blog comments' mod='ybc_blog'}</h1>
<div id="content-wrapper">
    <section id="content">
        <div class="ybc_blog_layout_{$blog_layout|escape:'html':'UTF-8'} ybc-blog-wrapper-form-managament">
            {if isset($errors_html) && $errors_html}
                {$errors_html nofilter}
            {else}
                {hook h='displayRightFormComments'}
            {/if}
            {hook h='displayFooterYourAccount'}
        </div>
        
    </section>
    
</div>