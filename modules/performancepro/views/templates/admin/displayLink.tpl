{**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Academic Free License (AFL 3.0)
 *
 * Additionally, this module is subject to a proprietary End User License Agreement (EULA).
 * For the full copyright, open source license, and EULA information, please view the LICENSE
 * that were distributed with this source code.
 *}

{if $pp_blank}
    <a style="white-space:nowrap;" href="{$pp_href|escape:'htmlall':'UTF-8'}" target="_blank"
       rel="noopener noreferrer nofollow"><i
                class="icon-external-link-sign"></i> {$pp_link|escape:'htmlall':'UTF-8'}</a>
{else}
    <a style="white-space:nowrap;" href="{$pp_href|escape:'htmlall':'UTF-8'}" rel="noopener noreferrer nofollow"><i
                class="icon-external-link-sign"></i> {$pp_link|escape:'htmlall':'UTF-8'}</a>
{/if}
