{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Revolut
 * @copyright Since 2020 Revolut
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 *}

{include './instruction.tpl'}

<form id="configuration-form" class="defaultForm form-horizontal revolutpayment" action="" method="post" enctype="multipart/form-data" novalidate="">

    <input type="hidden" name="submitrevolutpayment" value="1"/>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item {if empty($section) || $section == 'api-settings'}active{/if}">
            <a class="nav-link {if empty($section) || $section == 'api-settings'}active{/if}" id="home-tab" data-toggle="tab" href="#api-settings" role="tab"
               aria-controls="api-settings" aria-selected="true">
                <i class="icon-cogs"></i> {l s='API Settings' mod='revolutpayment'}
            </a>
        </li>

        <li class="nav-item {if $section == 'revolut-cc-settings'}active{/if}">
            <a class="nav-link {if $section == 'revolut-cc-settings'}active{/if}" id="revolut-cc-settings-tab" data-toggle="tab" href="#revolut-cc-settings" role="tab"
               aria-controls="revolut-cc-settings" aria-selected="false">
                <i class="icon-cogs"></i> {l s='Debit / Credit Cards' mod='revolutpayment'}
            </a>
        </li>
        
        <li class="nav-item {if $section == 'revolut-pay-settings'}active{/if}">
            <a class="nav-link {if $section == 'revolut-pay-settings'}active{/if}" id="revolut-pay-settings-tab" data-toggle="tab" href="#revolut-pay-settings" role="tab"
               aria-controls="revolut-pay-settings" aria-selected="false">
                <i class="icon-cogs"></i> {l s='Revolut Pay' mod='revolutpayment'}
            </a>
        </li>

        <li class="nav-item {if $section == 'prb-settings'}active{/if}">
            <a class="nav-link {if $section == 'prb-settings'}active{/if}" id="prb-settings-tab" data-toggle="tab" href="#prb-settings" role="tab"
               aria-controls="prb-settings" aria-selected="false">
                <i class="icon-cogs"></i> {l s='Apple Pay / Google Pay' mod='revolutpayment'}
            </a>
        </li>

        {if $pay_by_bank_method_allowed}
            <li class="nav-item {if $section == 'pay-by-bank-settings'}active{/if}">
                <a class="nav-link {if $section == 'pay-by-bank-settings'}active{/if}" id="pay-by-bank-settings-tab" data-toggle="tab" href="#pay-by-bank-settings" role="tab"
                   aria-controls="pay-by-bank-settings" aria-selected="false">
                    <i class="icon-cogs"></i> {l s='Pay by bank' mod='revolutpayment'}
                </a>
            </li>
        {/if}
        
        <li class="nav-item {if $section == 'adv-settings'}active{/if}">
            <a class="nav-link {if $section == 'adv-settings'}active{/if}" id="adv-settings-tab" data-toggle="tab" href="#adv-settings" role="tab"
               aria-controls="adv-settings" aria-selected="false">
                <i class="icon-cogs"></i> {l s='Advanced settings' mod='revolutpayment'}
            </a>
        </li>

        <li class="nav-item {if $section == 'promotional-banners-settings'}active{/if}">
            <a class="nav-link {if $section == 'promotional-banners-settings'}active{/if}" id="promotional-banners-settings-tab" data-toggle="tab" href="#promotional-banners-settings" role="tab"
               aria-controls="promotional-banners-settings" aria-selected="false">
                <i class="icon-cogs"></i> {l s='Rewards and promotions' mod='revolutpayment'}
            </a>
        </li>
    </ul>

    <div class="tab-content">
        {include './api-settings.tpl'}
        {include './revolut-cc-settings.tpl'}
        {include './revolut-pay-settings.tpl'}
        {include './adv-settings.tpl'}

        {if pay_by_bank_method_allowed}
            {include './pay-by-bank-settings.tpl'}
        {/if}
        
        {include './prb-settings.tpl'}
        {include './promotional-banners-settings.tpl'}
    </div>
</form>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    $('#REVOLUT_PRB_HEIGHT').attr('type', 'number').attr('min', 0).attr('max', 50);

    $('#api-settings button').on('click', function () {
        $('#configuration-form').append('<input type="hidden" name="section" value="api-settings"/>');
    });

    $('#revolut-cc-settings button').on('click', function () {
        $('#configuration-form').append('<input type="hidden" name="section" value="revolut-cc-settings"/>');
    });

    $('#revolut-pay-settings button').on('click', function () {
        $('#configuration-form').append('<input type="hidden" name="section" value="revolut-pay-settings"/>');
    });

    $('#adv-settings button').on('click', function () {
        $('#configuration-form').append('<input type="hidden" name="section" value="adv-settings"/>');
    });

    $('#pay-by-bank-settings button').on('click', function () {
        $('#configuration-form').append('<input type="hidden" name="section" value="pay-by-bank-settings"/>');
    });

    $('button[name="submitPRBSettings"]').on('click', function () {
        $('#configuration-form').append('<input type="hidden" name="section" value="prb-settings"/>');
    });

    $('#promotional-banners-settings button').on('click', function () {
        $('#configuration-form').append('<input type="hidden" name="section" value="promotional-banners-settings"/>');
    });
</script>
