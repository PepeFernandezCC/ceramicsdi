<section class="product-discounts js-product-discounts" style="margin-top: 2.5rem">
  {if $product.quantity_discounts}
    {assign var="isByPiece" value=false}
    {assign var="m2_caja" value=0}
    {assign var="customerShowTax" value=customer::getCustomerShowTax($customer.id)}

    {foreach from=$product.grouped_features item=feature}
      {if $FEATURE_M2_CAJA_ID === $feature.id_feature}
        {assign var="m2_caja" value=$feature.value|replace:',':'.'|floatval}
      {/if}

      {if $feature.id_feature === $FEATURE_M2_PIEZA_ID}
        {assign var="isByPiece" value=true}
      {/if}
    {/foreach}

    <p class="h6 product-discounts-title">
     <span style="color:#eac133; font-size:21px; padding-right: 10px;"><i class="fa-solid fa-angle-right"></i></span> {l s='Volume per Discount' d='Shop.Theme.Catalog'}
    </p>

    {block name='product_discount_table'}
      <table class="table-product-discounts" style="margin-top:20px">
        <thead>
          <tr>
            <th>{l s='Quantity' d='Shop.Theme.Catalog'}</th>
            <th>{l s='Unit Price' d='Shop.Theme.Catalog'} ({if $customerShowTax}{l s='Tax included' d='Admin.Global'}{else}{l s='Tax excluded' d='Admin.Global'}{/if})</th>
          </tr>
        </thead>

        <tbody>
          {foreach from=$product.quantity_discounts item='quantity_discount' name='quantity_discounts'}

            {assign var="discount_quantity" value=$quantity_discount.quantity}
            {assign var="discount_unit" value=""}
            {assign var="discount_clean" value=$quantity_discount.discount|replace:'€':''|replace:' ':''|replace:'.':''|replace:',':'.'|floatval}
            {assign var="unit_price_m2" value=$discount_clean}
            {if !$isByPiece && $m2_caja > 0}
              {assign var="discount_quantity" value=$quantity_discount.quantity * $m2_caja}
              {assign var="discount_unit" value=" m²"}
              
              {assign var="unit_price_m2" value=$discount_clean / $m2_caja}

            {/if}

            <tr
              data-discount-type="{$quantity_discount.reduction_type}"
              data-discount="{$quantity_discount.real_value}"
              data-discount-quantity="{$quantity_discount.quantity}"
            >
              <td id="amount-to-discount" data-discount="{$quantity_discount.quantity}">
                {l s='A partir de' d='Shop.Theme.Catalog'} {$discount_quantity|string_format:"%.2f"}{$discount_unit} <i class="fa-solid fa-arrow-right"></i> <span style="font-weight:700">{$quantity_discount.real_value}%</span>
              </td>

              <td>
                {$unit_price_m2|string_format:"%.2f"} €
                {if !$isByPiece && $m2_caja > 0}
                   / m²
                {/if}
              </td>
            </tr>

          {/foreach}
        </tbody>
      </table>
    {/block}
  {/if}
</section>