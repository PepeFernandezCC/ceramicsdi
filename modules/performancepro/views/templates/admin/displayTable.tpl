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

{if $pp_table}
    {if $pp_top}
        <div style="padding-top: 20px"></div>
    {/if}
    <table class="table">
        <thead>
        <tr>
            {if $pp_dataHtml|@count > 0}
                {foreach from=$pp_dataHtml[0] key=headerHtml item=value}
                    <th><strong>{$headerHtml}</strong></th>
                {/foreach}
            {/if}
        </tr>
        </thead>
        <tbody>
        {foreach from=$pp_dataHtml item=row}
            <tr>
                {foreach from=$row item=cellHtml}
                    <td height="30">{$cellHtml}</td>
                {/foreach}
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    {foreach from=$pp_dataHtml item=cellHtml}
        <td height="30">{$cellHtml}</td>
    {/foreach}
{/if}
