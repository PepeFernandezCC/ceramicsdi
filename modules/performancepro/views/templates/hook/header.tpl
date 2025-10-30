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

{if !empty($pp_preload_links)}
    {foreach from=$pp_preload_links item=preload_link}
        <link href="{$preload_link|escape:'url':'UTF-8'}" rel="preload"
              as="font">
    {/foreach}
{/if}
{if !empty($pp_preconnect_links)}
    {foreach from=$pp_preconnect_links item=preconnect_link}
        <link href="{$preconnect_link|escape:'url':'UTF-8'}" rel="preconnect" crossorigin>
    {/foreach}
{/if}
