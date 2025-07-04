{**
* This file is part of the performancepro package.
*
* @author Mathias Reker
* @copyright Mathias Reker
* @license Commercial Software License
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*}

{if !empty($pp_preload_links)}
    {foreach from=$pp_preload_links item=pp_preload_link}
        <link href="{$pp_preload_link}" rel="preload" as="font">
    {/foreach}
{/if}

{if !empty($pp_preconnect_links)}
    {foreach from=$pp_preconnect_links item=pp_preconnect_link}
        <link href="{$pp_preconnect_link}" rel="preconnect" crossorigin>
    {/foreach}
{/if}
