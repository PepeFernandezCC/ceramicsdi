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
<div id="header_bar" class="row bg-white">
    <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
        <div class="row">
            <div class="col-xs-3">
                <img  class="img-responsive" src="{$imagePath|escape:'htmlall':'UTF-8'}admin/logo.png" height="57" width="57" alt="" />
            </div>
            <div class="col-xs-6">
                <img class="img-responsive" src="{$imagePath|escape:'htmlall':'UTF-8'}admin/bt_logo.jpg" alt="" />
            </div>

        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
        <div class="text-center">
            <div id="step-by-step" class="row bs-wizard text-center" style="border-bottom:0;">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 bs-wizard-step step-1 {if empty($bConfigureStep1)}disabled{else}complete{/if} text-center">
                    <div class="text-center bs-wizard-stepnum">{l s='1 - Basic configuration' mod='gmerchantcenterpro'}</div>
                    <div class="progress"><div class="progress-bar"></div></div>
                    <a href="#" class="bs-wizard-dot"></a>
                    <div class="clr_5"></div>
                    <div class="workTabs">
                        {if empty($bConfigureStep1)}
                            <a class="btn btn-sm btn-warning btn-step-1" id="tab-2" ><i class="fa fa-cog"></i> {l s='Configure' mod='gmerchantcenterpro'} </a>
                        {/if}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 bs-wizard-step step-2 {if empty($bConfigureStep2)}disabled{else}complete{/if} text-center">
                    <div class="text-center bs-wizard-stepnum">{l s='2 - Data management' mod='gmerchantcenterpro'}</div>
                    <div class="progress"><div class="progress-bar"></div></div>
                    <a href="#" class="bs-wizard-dot"></a>
                    <div class="clr_5"></div>
                    <div class="workTabs">
                        {if empty($bConfigureStep2)}
                            <a class="btn btn-sm btn-warning btn-step-2" id="tab-001" ><i class="fa fa-cog"></i> {l s='Configure' mod='gmerchantcenterpro'} </a>
                        {/if}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 bs-wizard-step step-3 {if empty($bConfigureStep3)}disabled{else}complete{/if} text-center">
                    <div class="text-center bs-wizard-stepnum">{l s='3 - Import' mod='gmerchantcenterpro'}</div>
                    <div class="progress"><div class="progress-bar"></div></div>
                    <a href="#" class="bs-wizard-dot"></a>
                    <div class="clr_5"></div>
                    <div class="workTabs">
                        {if empty($bConfigureStep3)}
                            <a class="fancybox.ajax btn btn-sm btn-warning btn-step-3 bt_add-feed" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.stepPopup.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.stepPopup.type|escape:'htmlall':'UTF-8'}"  id="tab-030"><i class="fa fa-cog"></i> {l s='Configure' mod='gmerchantcenterpro'} </a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
        <a class="btn btn-info btn-sm col-xs-12" target="_blank" href="{$faqUrl|escape:'htmlall':'UTF-8'}/{$sFaqLang|escape:'htmlall':'UTF-8'}/product/68"><span class="fa fa-question-circle"></span>&nbsp;&nbsp;{l s='Online FAQ' mod='gmerchantcenterpro'}</a>
    </div>
</div>

<div class="alert alert-warning mt-2 hide">
    {l s='Google has announced a new, simplified version of Google Merchant Center called Merchant Center Next. As well as getting a brand new user interface, many features will also change, some being upgraded, others disappearing altogether. For the moment, the full impact of Merchant Center Next is unclear, and many uncertainties remain. Once we have more information, we\'ll update the module as necessary.' mod='gmerchantcenterpro'}
</div>

<script type="text/javascript">
	$("a.bt_add-feed").fancybox({
		'hideOnContentClick' : false
	});
</script>
