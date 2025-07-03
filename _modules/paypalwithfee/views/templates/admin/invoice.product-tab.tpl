{*
* 2020 4webs
*
* DEVELOPED By 4webs Prestashop Platinum Partner
*
* @author    4webs
* @copyright 4webs 2017
* @version 5.1.4
* @category payment_gateways
* @license 4webs
*}
<table class="product" width="100%" cellpadding="4" cellspacing="0">

    <thead>
        <tr>
            <th class="product header small" width="{$layout.reference.width|escape:'html':'UTF-8'}%">{l s='Reference' mod='paypalwithfee' pdf='true'}</th>
            <th class="product header small" width="{$layout.product.width|escape:'html':'UTF-8'}%">{l s='Product' mod='paypalwithfee' pdf='true'}</th>
            <th class="product header small" width="{$layout.tax_code.width|escape:'html':'UTF-8'}%">{l s='Tax Rate' mod='paypalwithfee' pdf='true'}</th>

            {if isset($layout.before_discount)}
                <th class="product header small" width="{$layout.unit_price_tax_excl.width|escape:'html':'UTF-8'}%">{l s='Base price' mod='paypalwithfee' pdf='true'} <br /> {l s='(Tax excl.)' mod='paypalwithfee' pdf='true'}</th>
                {/if}

            <th class="product header-right small" width="{$layout.unit_price_tax_excl.width|escape:'html':'UTF-8'}%">{l s='Unit Price' mod='paypalwithfee' pdf='true'} <br /> {l s='(Tax excl.)' mod='paypalwithfee' pdf='true'}</th>
            <th class="product header small" width="{$layout.quantity.width|escape:'html':'UTF-8'}%">{l s='Qty' mod='paypalwithfee' pdf='true'}</th>
            <th class="product header-right small" width="{$layout.total_tax_excl.width|escape:'html':'UTF-8'}%">{l s='Total' mod='paypalwithfee' pdf='true'} <br /> {l s='(Tax excl.)' mod='paypalwithfee' pdf='true'}</th>
        </tr>
    </thead>

    <tbody>

        <!-- PRODUCTS -->
        {foreach $order_details as $order_detail}
            {cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
            <tr class="product {$bgcolor_class|escape:'html':'UTF-8'}">

                <td class="product center">
                    {$order_detail.product_reference|escape:'html':'UTF-8'}
                </td>
                <td class="product left">
                    {if $display_product_images}
                        <table width="100%">
                            <tr>
                                <td width="15%">
                                    {if isset($order_detail.image) && $order_detail.image->id}
                                        {$order_detail.image_tag|escape:'html':'UTF-8'}
                                    {/if}
                                </td>
                                <td width="5%">&nbsp;</td>
                                <td width="80%">
                                    {$order_detail.product_name|escape:'html':'UTF-8'}
                                </td>
                            </tr>
                        </table>
                    {else}
                        {$order_detail.product_name|escape:'html':'UTF-8'}
                    {/if}

                </td>
                <td class="product center">
                    {$order_detail.order_detail_tax_label|escape:'html':'UTF-8'}
                </td>

                {if isset($layout.before_discount)}
                    <td class="product center">
                        {if isset($order_detail.unit_price_tax_excl_before_specific_price)}
                            {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl_before_specific_price}
                        {else}
                            --
                        {/if}
                    </td>
                {/if}

                <td class="product right">
                    {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl_including_ecotax}
                    {if $order_detail.ecotax_tax_excl > 0}
                        <br>
                        <small>{{displayPrice currency=$order->id_currency price=$order_detail.ecotax_tax_excl}|string_format:{l s='ecotax: %s' mod='paypalwithfee' pdf='true'}|escape:'htmlall':'UTF-8'}</small>
                    {/if}
                </td>
                <td class="product center">
                    {$order_detail.product_quantity|escape:'html':'UTF-8'}
                </td>
                <td  class="product right">
                    {displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl_including_ecotax}
                </td>
            </tr>

            {foreach $order_detail.customizedDatas as $customizationPerAddress}
                {foreach $customizationPerAddress as $customizationId => $customization}
                    <tr class="customization_data {$bgcolor_class|escape:'html':'UTF-8'}">
                        <td class="center"> &nbsp;</td>

                        <td>
                            {if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
                                <table style="width: 100%;">
                                    {foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
                                        <tr>
                                            <td style="width: 30%;">
                                                {$customization_infos.name|string_format:{l s='%s:' mod='paypalwithfee' pdf='true'}|escape:'htmlall':'UTF-8'}
                                            </td>
                                            <td>{if (int)$customization_infos.id_module}{$customization_infos.value|escape:'html':'UTF-8'}{else}{$customization_infos.value|escape:'html':'UTF-8'}{/if}</td>
                                        </tr>
                                    {/foreach}
                                </table>
                            {/if}

                            {if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 70%;">{l s='image(s):' mod='paypalwithfee' pdf='true'}</td>
                                        <td>{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]|escape:'html':'UTF-8')}</td>
                                    </tr>
                                </table>
                            {/if}
                        </td>

                        <td class="center">
                            ({if $customization.quantity == 0}1{else}{$customization.quantity|escape:'html':'UTF-8'}{/if})
                        </td>

                        {assign var=end value=($layout._colCount-3)}
                        {for $var=0 to $end}
                            <td class="center">
                                --
                            </td>
                        {/for}

                    </tr>
                    <!--if !$smarty.foreach.custo_foreach.last-->
                {/foreach}
            {/foreach}
        {/foreach}
        <!-- END PRODUCTS -->

        <!-- CART RULES -->

        {assign var="shipping_discount_tax_incl" value="0"}
        {foreach from=$cart_rules item=cart_rule name="cart_rules_loop"}
            {if $smarty.foreach.cart_rules_loop.first}
                <tr class="discount">
                    <th class="header" colspan="{$layout._colCount|escape:'html':'UTF-8'}">
                        {l s='Discounts' mod='paypalwithfee' pdf='true'}
                    </th>
                </tr>
            {/if}
            <tr class="discount">
                <td class="white right" colspan="{$layout._colCount - 1|escape:'html':'UTF-8'}">
                    {$cart_rule.name|escape:'html':'UTF-8'}
                </td>
                <td class="right white">
                    - {displayPrice currency=$order->id_currency price=$cart_rule.value_tax_excl}
                </td>
            </tr>
        {/foreach}

    </tbody>

</table>
