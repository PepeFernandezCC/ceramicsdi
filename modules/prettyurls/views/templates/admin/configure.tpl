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
<script type="text/javascript">
var tab_module = "{*$tab_module|escape:'htmlall':'UTF-8'*}";
</script>
<div class="bootstrap col-lg-2">
    <div class="custom-tab-prettyurls {$_ps_ver|escape:'htmlall':'UTF-8'}">
        <div class="prettyurls-page-head-tabs" id="prettyurls_head_tabs">
            <ul class="tab">
                <li class="tab-row{if !$module_tab OR empty($module_tab) OR $module_tab == 'seo'} active{/if}">
                    <a href="#urls" class="urls" data-toggle="tab"><i class="material-icons">link</i> {l s='URL Cleaner' mod='prettyurls'}</a>
                </li>
                <li class="tab-row{if $module_tab AND $module_tab == 'meta'} active{/if}">
                    <a href="#meta" class="meta" data-toggle="tab"><i class="material-icons">trending_up</i> {l s='Meta Tags' mod='prettyurls'}</a>
                </li>
                <li class="tab-row">
                    <a href="#report" class="report" data-toggle="tab"><i class="material-icons">content_copy</i> {l s='Duplicate URLs' mod='prettyurls'}</a>
                </li>
                <li class="tab-row{if $module_tab AND $module_tab == 'redirects'} active{/if}">
                    <a href="#redirects" class="redirects" data-toggle="tab"><i class="material-icons">mediation</i> {l s='URL Redirects' mod='prettyurls'}</a>
                </li>
                <li class="tab-row{if $module_tab AND $module_tab == 'graphs'} active{/if}">
                    <a href="#graphs" class="robots" data-toggle="tab"><i class="material-icons">share</i> {l s='OpenGraph' mod='prettyurls'}</a>
                </li>
                <li class="tab-row{if $module_tab AND $module_tab == 'robots'} active{/if}">
                    <a href="#robots" class="robots" data-toggle="tab"><i class="material-icons">edit</i> {l s='Robots.txt' mod='prettyurls'}</a>
                </li>
                <li class="tab-row last{if $module_tab AND $module_tab == 'sitemap'} active{/if}">
                    <a href="#sitemap" class="sitemap" data-toggle="tab"><i class="material-icons">code</i> {l s='Sitemap' mod='prettyurls'}</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="bootstrap col-lg-10" id="prettyurls_configuration">
    <!-- Module content -->
    <div id="modulecontent" class="clearfix">
        <!-- Tab panes -->
        <div class="tab-content row">
            <div class="tab-pane{if !$module_tab OR empty($module_tab) OR $module_tab == 'seo'} active{/if}" id="urls">
                <div class="tab_cap_listing">
                    {include file="./tabs/general.tpl"}
                </div>
            </div>
            <div class="tab-pane{if $module_tab AND $module_tab == 'meta'} active{/if}" id="meta">
                <div class="tab_cap_listing">
                    {include file="./tabs/meta.tpl"}
                </div>
            </div>
            <div class="tab-pane" id="report">
                <div class="tab_cap_listing">
                    {include file="./tabs/duplicate_list.tpl"}
                </div>
            </div>
            <div class="tab-pane{if $module_tab AND $module_tab == 'redirects'} active{/if}" id="redirects">
                <div class="tab_cap_listing">
                    {include file="./tabs/redirects.tpl"}
                </div>
            </div>
            <div class="tab-pane{if $module_tab AND $module_tab == 'graphs'} active{/if}" id="graphs">
                <div class="tab_cap_listing">
                    {include file="./tabs/graphs.tpl"}
                </div>
            </div>
            <div class="tab-pane{if $module_tab AND $module_tab == 'robots'} active{/if}" id="robots">
                <div class="tab_cap_listing">
                    {include file="./tabs/robots.tpl"}
                </div>
            </div>
            
            <div class="tab-pane{if $module_tab AND $module_tab == 'sitemap'} active{/if}" id="sitemap">
                <div class="tab_cap_listing">
                    {include file="./tabs/sitemap.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>
