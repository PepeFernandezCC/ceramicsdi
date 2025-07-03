{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{if !empty($smarty.get.order)}
    {capture assign='ordering'}order={$smarty.get.order}&amp;{/capture}
{else}
    {assign var='ordering' value=''}
{/if}

{if !empty($smarty.get.resultsPerPage)}
    {assign var='results_per_page' value=$smarty.get.resultsPerPage}
{else}
    {assign var='results_per_page' value=20}
{/if}

<nav class="pagination">
    <div class="col-xl-4 col-md-12">
        {block name='pagination_summary'}
            {l s='Showing %from%-%to% of %total% item(s)' d='Shop.Theme.Catalog' sprintf=['%from%' => $pagination.items_shown_from ,'%to%' => $pagination.items_shown_to, '%total%' => $pagination.total_items]}
        {/block}
    </div>

    <div class="col-xl-3 col-md-6 products-per-page">
        <label style="float:left;margin-right: 15px"
               class="form-control-label sort-label">{l s='Products per page:' d='Shop.Theme.Catalog'}</label>
        <div style="float:left;" class="sort-select dropdown js-dropdown">
            <a class="custom-select select-title" rel="nofollow" data-toggle="dropdown" aria-haspopup="true"
               aria-expanded="false">
                {$results_per_page}
            </a>
            <div class="dropdown-menu">
                <a rel="nofollow" href="?{$ordering}resultsPerPage=20" class="dropdown-item js-search-link">
                    20
                </a>
                <a rel="nofollow" href="?{$ordering}resultsPerPage=40" class="dropdown-item js-search-link">
                    40
                </a>
                <a rel="nofollow" href="?{$ordering}resultsPerPage=60" class="dropdown-item js-search-link">
                    60
                </a>
                <a rel="nofollow" href="?{$ordering}resultsPerPage=80" class="dropdown-item js-search-link">
                    80
                </a>
                <a rel="nofollow" href="?{$ordering}resultsPerPage=100" class="dropdown-item js-search-link">
                    100
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-5 col-md-6 pr-0 pl-0-xs">
        {block name='pagination_page_list'}
            {if $pagination.should_be_displayed}
                <ul class="page-list clearfix text-sm-center">
                    {foreach from=$pagination.pages item="page"}
                        <li {if $page.current} class="current" {/if}>
                            {if $page.type === 'spacer'}
                                <span class="spacer">&hellip;</span>
                            {else}
                                <a
                                        rel="{if $page.type === 'previous'}prev{elseif $page.type === 'next'}next{else}nofollow{/if}"
                                        href="{$page.url}"
                                        class="{if $page.type === 'previous'}previous {elseif $page.type === 'next'}next {/if}{['disabled' => !$page.clickable, 'js-search-link' => true]|classnames}"
                                >
                                    {if $page.type === 'previous'}
                                        <i class="material-icons">&#xE314;</i>
                                        {l s='Previous' d='Shop.Theme.Actions'}
                                    {elseif $page.type === 'next'}
                                        {l s='Next' d='Shop.Theme.Actions'}
                                        <i class="material-icons">&#xE315;</i>
                                    {else}
                                        {$page.page}
                                    {/if}
                                </a>
                            {/if}
                        </li>
                    {/foreach}
                </ul>
            {/if}
        {/block}
    </div>

</nav>
