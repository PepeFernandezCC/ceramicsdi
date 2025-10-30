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

<button id="ajaxCall-{$pp_id|escape:'htmlall':'UTF-8'}" class="btn btn-default"
        onclick="callAjax('{$pp_link|escape:'htmlall':'UTF-8'}', '{$pp_id|escape:'htmlall':'UTF-8'}', '{$pp_confMsg|escape:'htmlall':'UTF-8'}'); return false;"
        id="{$pp_id|escape:'htmlall':'UTF-8'}">
    {$pp_displayNameHtml}
</button>