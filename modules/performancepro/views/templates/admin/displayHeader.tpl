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

{if $pp_noTop}
    <h2 style="margin-top: -10px">{$pp_text|escape:'htmlall':'UTF-8'}</h2>
{else}
    <h2>{$pp_text|escape:'htmlall':'UTF-8'}</h2>
{/if}
