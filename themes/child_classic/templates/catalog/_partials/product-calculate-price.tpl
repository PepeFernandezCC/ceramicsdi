{assign var="productPrice" value=Product::calculateCustomPrice($product.id, $customer.id)}

{if !isset($regular_price)}
    {assign var="regular_price" value=false}
{/if}

{if $regular_price || $productPrice.volume}
    {$productPrice.original_price} €{$productPrice.tipologia nofilter}
{else}
    {$productPrice.price} €{$productPrice.tipologia nofilter}
{/if}



