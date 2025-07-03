{**
* NOTICE OF LICENSE
*
* This source file is subject to the Commercial License and is not open source.
* Each license that you purchased is only available for 1 website only.
* You can't distribute, modify or sell this code.
* If you want to use this file on more websites, you need to purchase additional licenses.
*
* DISCLAIMER
*
* Do not edit or add to this file.
* If you need help please contact <attechteams@gmail.com>
*
* @author    Alpha Tech <attechteams@gmail.com>
* @copyright 2022 Alpha Tech
* @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{extends file='customer/page.tpl'}

{block name='page_content'}
    <style>
        .core-request-page-content{
            padding: 1rem;
            background: #fff;
            font-size: .875rem;
            color: #7a7a7a;
        }
        .float-xs-right {
            float: right !important;
        }
    </style>
    <div class="page-header">
        <h1>
            {l s='Request' mod='corecookie'}
        </h1>
    </div>
    <section class="core-request-page-content">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group row">
                <label class="col-md-3 form-control-label required" for="field-firstname">
                    {l s='First name' mod='corecookie'}
                </label>
                <div class="col-md-6">
                    <input id="field-firstname" class="form-control" name="firstname" type="text" value="{$customer.firstname|escape:'htmlall':'UTF-8'}" disabled readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label required" for="field-lastname">
                    {l s='Last name' mod='corecookie'}
                </label>
                <div class="col-md-6">
                    <input id="field-lastname" class="form-control" name="lastname" type="text" value="{$customer.lastname|escape:'htmlall':'UTF-8'}" disabled readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label required" for="field-email">
                    {l s='Email' mod='corecookie'}
                </label>
                <div class="col-md-6">
                    <input id="field-email" class="form-control" name="email" type="email" value="{$customer.email|escape:'htmlall':'UTF-8'}" disabled readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label required" for="field-type">
                    {l s='Type' mod='corecookie'}
                </label>
                <div class="col-md-6">
                    <select class="form-control" id="field-type" name="metatype" required>
                        <option value="gdpr_request" {if $meta_type == 'gdpr_request'} selected {/if}>{l s='GDPR Request' mod='corecookie'}</option>
                        <option value="personal_information" {if $meta_type == 'personal_information'} selected {/if}>{l s='Personal Information' mod='corecookie'}</option>
                        <option value="report_request" {if $meta_type == 'report_request'} selected {/if}>{l s='Report Request' mod='corecookie'}</option>
                        <option value="deletion_request" {if $meta_type == 'deletion_request'} selected {/if}>{l s='Deletion Request' mod='corecookie'}</option>
                        <option value="ccpa_request" {if $meta_type == 'ccpa_request'} selected {/if}>{l s='CCPA Request' mod='corecookie'}</option>
                        <option value="do_not_sell_request" {if $meta_type == 'do_not_sell_request'} selected {/if}>{l s='Do Not Sell Request' mod='corecookie'}</option>
                        <option value="appi_request" {if $meta_type == 'appi_request'} selected {/if}>{l s='APPI Request' mod='corecookie'}</option>
                        <option value="pipeda_request" {if $meta_type == 'pipeda_request'} selected {/if}>{l s='PIPEDA Request' mod='corecookie'}</option>
                    </select>
                    <span class="form-control-comment">
                        {l s='Type of data you want to request.' mod='corecookie'}
                    </span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label" for="field-content">
                    {l s='Content' mod='corecookie'}
                </label>
                <div class="col-md-6">
                    <textarea id="field-content" class="form-control" name="content" placeholder="{l s='Content you want to send us' mod='corecookie'}"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label" for="field-files">
                    {l s='File' mod='corecookie'}
                </label>
                <div class="col-md-6">
                    <input type="file" class="form-control" name="file" id="field-files">
                    <span class="form-control-comment">
                        {l s='Only files with formats:' mod='corecookie'} pdf, txt, doc, docx, jpg, png.
                    </span>
                </div>
            </div>
            <footer class="form-footer clearfix">
                <input type="hidden" name="form_request" value="1">
                <input type="hidden" name="source_page" value="{$source_page|escape:'htmlall':'UTF-8'}">
                <button class="btn btn-primary form-control-submit float-xs-right" type="submit">
                    {l s='Send' mod='corecookie'}
                </button>
            </footer>
        </form>
    </section>
{/block}
