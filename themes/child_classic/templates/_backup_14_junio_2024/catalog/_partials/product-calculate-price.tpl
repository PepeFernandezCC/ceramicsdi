{assign var="categoriasProducto" value=Product::getProductCategories($product.id)}
{assign var="perMeterArray"  value=["Por m2", "Per m2", "Par m2", "Pro m2"]}
{if $CATEGORY_INSTALACION_ID|in_array:$categoriasProducto || $CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto}
    {assign var="normalSell" value=true}
{else}
    {assign var="normalSell" value=false}
{/if}

{foreach from=$product.grouped_features item=feature}
    
    {if $FEATURE_TIPOLOGIA_PRECIO_ID === $feature.id_feature}

        {if in_array($feature.value, $perMeterArray)}

            {assign var="tipologia" value="{l s='/m<sup>2</sup>' d='Shop.Theme.Catalog'}"}
            
        {/if}

    {/if}

{/foreach}

{assign var="m2Caja" value="0"}
{assign var="m2Pieza" value="0"}
{foreach from=$product.features item='feature'}
    {if $feature.id_feature === $FEATURE_M2_CAJA_ID}
        {assign var="m2Caja" value="{$feature.value|replace:',':'.'}"}
    {elseif $feature.id_feature === $FEATURE_M2_PIEZA_ID}
        {assign var="m2Pieza" value="{$feature.value|replace:',':'.'}"}
    {/if}
{/foreach}

{capture name='custom_price'}{hook h='displayProductPriceBlock' product=$product type='custom_price' hook_origin='product_sheet'}{/capture}
{if '' !== $smarty.capture.custom_price}
    {$smarty.capture.custom_price nofilter}
{else}
    {if $normalSell}
        {if $regular_price|default:false}
            {$product.regular_price}
        {else}
            {$product.price}
        {/if}
    {else}
        {if $m2Caja == 0 and $m2Pieza == 0}
            {assign var="priceM2" value="0"}
        {elseif $m2Pieza == 0}
            {* Situación de precio por metro cuadrado *}
            {if $regular_price|default:false}
                {assign var="priceM2" value="{$product.regular_price_amount / $m2Caja}"}
            {else}
                {assign var="priceM2" value="{$product.price_amount / $m2Caja}"}
            {/if}
        {else}
            {* Situación de precio por pieza *}
            {if $regular_price|default:false}
                {assign var="priceM2" value="{$product.regular_price_amount}"}
            {else}
                {assign var="priceM2" value="{$product.price_amount}"}
            {/if}
        {/if}

        {if !isset($tipologia)}
            {assign var="tipologia" value="{l s='/piece' d='Shop.Theme.Catalog'}"}
        {/if}
        {$priceM2|number_format:2|replace:'.':','}&nbsp;€{$tipologia nofilter}
        
    {/if}
{/if}