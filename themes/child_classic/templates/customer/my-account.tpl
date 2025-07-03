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
{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='Your account' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
    <div class="">
        <div class="links">
            {assign var="block_count_lg" value="1"}
            {assign var="block_count_md" value="1"}

            <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 count-lg-{$block_count_lg} count-md-{$block_count_md}" id="identity-link"
               href="{$urls.pages.identity}">
                <span class="link-item">
                  <i class="material-icons">&#xE853;</i>
                  {l s='Information' d='Shop.Theme.Customeraccount'}
                </span>
            </a>
            {if $block_count_lg == 3}
                {$block_count_lg = 1}
            {else}
                {$block_count_lg = $block_count_lg + 1}
            {/if}
            {if $block_count_md == 2}
                {$block_count_md = 1}
            {else}
                {$block_count_md = $block_count_md + 1}
            {/if}

            {if $customer.addresses|count}
                <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 count-lg-{$block_count_lg} count-md-{$block_count_md}" id="addresses-link"
                   href="{$urls.pages.addresses}">
                  <span class="link-item">
                    <i class="material-icons">&#xE56A;</i>
                    {l s='Addresses' d='Shop.Theme.Customeraccount'}
                  </span>
                </a>
                {if $block_count_lg == 3}
                    {$block_count_lg = 1}
                {else}
                    {$block_count_lg = $block_count_lg + 1}
                {/if}
                {if $block_count_md == 2}
                    {$block_count_md = 1}
                {else}
                    {$block_count_md = $block_count_md + 1}
                {/if}
            {else}
                <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 count-lg-{$block_count_lg} count-md-{$block_count_md}" id="address-link"
                   href="{$urls.pages.address}">
                  <span class="link-item">
                    <i class="material-icons">&#xE567;</i>
                    {l s='Add first address' d='Shop.Theme.Customeraccount'}
                  </span>
                </a>
                {if $block_count_lg == 3}
                    {$block_count_lg = 1}
                {else}
                    {$block_count_lg = $block_count_lg + 1}
                {/if}
                {if $block_count_md == 2}
                    {$block_count_md = 1}
                {else}
                    {$block_count_md = $block_count_md + 1}
                {/if}
            {/if}

            {if !$configuration.is_catalog}
                <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 count-lg-{$block_count_lg} count-md-{$block_count_md}" id="history-link"
                   href="{$urls.pages.history}">
                  <span class="link-item">
                    <i class="material-icons">&#xE916;</i>
                    {l s='Order history and details' d='Shop.Theme.Customeraccount'}
                  </span>
                </a>
                {if $block_count_lg == 3}
                    {$block_count_lg = 1}
                {else}
                    {$block_count_lg = $block_count_lg + 1}
                {/if}
                {if $block_count_md == 2}
                    {$block_count_md = 1}
                {else}
                    {$block_count_md = $block_count_md + 1}
                {/if}
            {/if}

            {if !$configuration.is_catalog}
                <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 count-lg-{$block_count_lg} count-md-{$block_count_md}" id="order-slips-link"
                   href="{$urls.pages.order_slip}">
                  <span class="link-item">
                    <i class="material-icons">&#xE8B0;</i>
                    {l s='Credit slips' d='Shop.Theme.Customeraccount'}
                  </span>
                </a>
                {if $block_count_lg == 3}
                    {$block_count_lg = 1}
                {else}
                    {$block_count_lg = $block_count_lg + 1}
                {/if}
                {if $block_count_md == 2}
                    {$block_count_md = 1}
                {else}
                    {$block_count_md = $block_count_md + 1}
                {/if}
            {/if}

            {if $configuration.voucher_enabled && !$configuration.is_catalog}
                <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 count-lg-{$block_count_lg} count-md-{$block_count_md}" id="discounts-link"
                   href="{$urls.pages.discount}">
                  <span class="link-item">
                    <i class="material-icons">&#xE54E;</i>
                    {l s='Vouchers' d='Shop.Theme.Customeraccount'}
                  </span>
                </a>
                {if $block_count_lg == 3}
                    {$block_count_lg = 1}
                {else}
                    {$block_count_lg = $block_count_lg + 1}
                {/if}
                {if $block_count_md == 2}
                    {$block_count_md = 1}
                {else}
                    {$block_count_md = $block_count_md + 1}
                {/if}
            {/if}

            {if $configuration.return_enabled && !$configuration.is_catalog}
                <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 count-lg-{$block_count_lg} count-md-{$block_count_md}" id="returns-link"
                   href="{$urls.pages.order_follow}">
                  <span class="link-item">
                    <i class="material-icons">&#xE860;</i>
                    {l s='Merchandise returns' d='Shop.Theme.Customeraccount'}
                  </span>
                </a>
                {if $block_count_lg == 3}
                    {$block_count_lg = 1}
                {else}
                    {$block_count_lg = $block_count_lg + 1}
                {/if}
                {if $block_count_md == 2}
                    {$block_count_md = 1}
                {else}
                    {$block_count_md = $block_count_md + 1}
                {/if}
            {/if}

            {block name='display_customer_account'}
                {hook h='displayCustomerAccount'}
            {/block}

        </div>
    </div>
{/block}


{block name='page_footer'}
    {block name='my_account_links'}
        <div class="text-sm-center">
            <a href="{$urls.actions.logout}">
                {l s='Sign out' d='Shop.Theme.Actions'}
            </a>
        </div>
    {/block}
{/block}
