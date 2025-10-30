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

<style>
    .whatsapp-layout {
        position: fixed;
        bottom: 0;
        z-index: 9999;
        {if $position == 1}
        left: 0;
        {else}
        right: 0;
        {/if}
        margin: 10px;
        width: auto !important;
    }

    .whatsapp-layout span {
        border-radius: 4px;
        background: #25D366;
        color: #fff;
        font-size: 13px;
        padding: 6px 8px;
        display: inline-block;
        font-family: helvetica, arial, sans-serif;
        white-space: nowrap;
    }
    .whatsapp_icon {
        position: relative;
        background-image: url('{$this_path|escape:'htmlall':'UTF-8'}views/img/whatsapp.png');
        background-size: auto;
        background-repeat: no-repeat;
        display: inline-block;
        height: 16px;
        width: 16px;
        margin-right: 4px;
        top: -1px;
        vertical-align: middle;
    }

    .whatsapp-layout:focus {
        border: 0;
        outline: none !important;
    }
</style>

<a target="_blank" class="whatsapp-layout" href="{$url|escape:'html':'UTF-8'}">
    <span {if $color != ''}style="background-color: {$color|escape:'html':'UTF-8'}"{/if}>
        <i class="whatsapp_icon"></i>
        {$tab_message|escape:'quotes':'UTF-8'}
    </span>
</a>