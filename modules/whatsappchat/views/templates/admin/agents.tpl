{**
* WhatsApp Chat
*
* ISC License
*
* Copyright (c) 2023 idnovate.com
* idnovate is a Registered Trademark & Property of idnovate.com, innovación y desarrollo SCP
*
* Permission to use, copy, modify, and/or distribute this software for any
* purpose with or without fee is hereby granted, provided that the above
* copyright notice and this permission notice appear in all copies.
*
* THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
* REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
* AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
* INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
* LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
* OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
* PERFORMANCE OF THIS SOFTWARE.
*
* @author    idnovate
* @copyright 2024 idnovate
* @license   https://www.isc.org/licenses/ https://opensource.org/licenses/ISC ISC License
*}
{if $agents !== false}
    {foreach key=key from=$agents item=agent}
        <div class="col-lg-4 col-md-6 col-xs-12">
            <a href="{$agent.edit|escape:'html':'UTF-8'}" class="whatsappchat-agents-content-agent">
                <div class="whatsappchat-agents-content-image{if !$agent.active} offline{/if}">
                    <img src="{$agents_img_dir|escape:'html':'UTF-8'}{$agent.image|escape:'html':'UTF-8'}" alt="{$agent.name|escape:'html':'UTF-8'} ({$agent.department|escape:'html':'UTF-8'})">
                </div>
                <div class="whatsappchat-agents-content-info whatsappchat-agents-content-info17">
                    <span class="whatsappchat-agents-content-department">{$agent.department|escape:'html':'UTF-8'}</span>
                    <span class="whatsappchat-agents-content-name whatsappchat-agents-content-name17">{$agent.name|escape:'html':'UTF-8'}</span>
                    <span class="whatsappchat-agents-content-phone"><i class="icon-phone"></i> {$agent.mobile_phone|escape:'html':'UTF-8'}</span>
                </div>
                <div class="clearfix" style="margin-top: 10px;">
                    <button class="btn btn-default" onclick="goChatAgent('{$agent.url|escape:'html':'UTF-8'}');return false;">
                        <i class="icon-whatsapp"></i> {l s='Chat' mod='whatsappchat'}
                    </button>
                    <button class="btn btn-default" onclick="goEditAgent('{$agent.edit|escape:'html':'UTF-8'}');return false;" style="float: right;margin-right: 5px;">
                        <i class="icon-edit"></i> {l s='Edit' mod='whatsappchat'}
                    </button>
                </div>
            </a>
        </div>
    {/foreach}
    <div class="col-lg-4 col-md-6 col-xs-12">
        <a href="{$agents_new_agent_url|escape:'html':'UTF-8'}" class="whatsappchat-agents-content-agent">
            <div class="whatsappchat-agents-content-image new">
                <img src="{$agents_img_default|escape:'html':'UTF-8'}" alt="{l s='Create a new agent' mod='whatsappchat'}">
            </div>
            <div class="whatsappchat-agents-content-info whatsappchat-agents-content-info17">
                <span class="whatsappchat-agents-content-name whatsappchat-agents-content-name17">{l s='Create a new agent' mod='whatsappchat'}</span>
            </div>
            <div class="clearfix"></div>
        </a>
    </div>
    <script>
        function goEditAgent(url) {
            window.location.href = url;

        }
        function goChatAgent(url) {
            window.open(url);
        }
    </script>
{/if}