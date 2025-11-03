{assign var="categoriasProducto" value=Product::getProductCategories($product.id)}
{if $CATEGORY_INSTALACION_ID|in_array:$categoriasProducto || $CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto || $CATEGORY_ARTICULATIONS|in_array:$categoriasProducto}
    {assign var="normalSell" value=true}
{else}
    {assign var="normalSell" value=false}
{/if}

{assign var="tipologia" value="{l s='/m<sup>2</sup>' d='Shop.Theme.Catalog'}"}

{foreach from=$product.grouped_features item=feature}
    {if $FEATURE_TIPOLOGIA_PRECIO_ID === $feature.id_feature}
        {if $feature.id_feature_value === $ID_FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_M2}
            {assign var="tipologia" value="{l s='/m<sup>2</sup>' d='Shop.Theme.Catalog'}"}
        {elseif $feature.id_feature_value === $ID_FEATURE_TIPOLOGIA_PRECIO_VALUE_POR_PIEZA}
            {assign var="tipologia" value="{l s='/piece' d='Shop.Theme.Catalog'}"}
        {/if}
    {/if}
{/foreach}
{assign var="m2Caja" value="0"}
{assign var="m2Pieza" value="0"}
{foreach from=$product.features item='feature'}
    {if $feature.id_feature === $FEATURE_M2_CAJA_ID}
        {if isset($feature.value) && $feature.value != NULL}
            {assign var="m2Caja" value="{$feature.value|replace:',':'.'|floatval}"}
        {else}
            {foreach from=FeatureValue::getFeatureValueLang($feature.id_feature_value) item='featureValue'}
                {if intval($featureValue['id_lang']) === intval($language.id)}
                    {assign var="m2Caja" value="{$featureValue['value']|replace:',':'.'|floatval}"}
                {/if}
            {/foreach}
        {/if}
    {elseif $feature.id_feature === $FEATURE_M2_PIEZA_ID}
         {if isset($feature.value) && $feature.value != NULL}
            {assign var="m2Pieza" value="{$feature.value|replace:',':'.'|floatval}"}
        {else}
            {foreach from=FeatureValue::getFeatureValueLang($feature.id_feature_value) item='featureValue'}
                {if intval($featureValue['id_lang']) === intval($language.id)}
                    {assign var="m2Pieza" value="{$featureValue['value']|replace:',':'.'|floatval}"}
                {/if}
            {/foreach}
        {/if}
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
        {assign var="priceM2" value=0}
        {if $m2Caja == 0 and $m2Pieza == 0}
            {assign var="priceM2" value=0}
        {elseif $m2Pieza == 0}
            {* Situación de precio por metro cuadrado *}
            {if $regular_price|default:false}
                {if is_float($product.regular_price_amount)}
                    {assign var="priceM2" value="{$product.regular_price_amount / $m2Caja}"}
                {else}
                    {$productPriceFloat = $product.regular_price_amount|replace:'€':''|replace:',':'.'|floatval}
                    {assign var="priceM2" value="{$productPriceFloat / $m2Caja}"}
                {/if}
            {else}
                {if is_float($product.price_amount)}
                    {assign var="priceM2" value="{$product.price_amount / $m2Caja}"}
                {else}
                    {$productPriceFloat = $product.price_amount|replace:'€':''|replace:',':'.'|floatval}
                    {assign var="priceM2" value="{$productPriceFloat / $m2Caja}"}
                {/if}
            {/if}
        {else}
            {* Situación de precio por pieza *}
            {if $regular_price|default:false}
                {assign var="priceM2" value="{$product.regular_price_amount|floatval}"}
            {else}
                {assign var="priceM2" value="{$product.price_amount|floatval}"}
            {/if}
        {/if}

        {$priceM2|number_format:2|replace:'.':','}&nbsp;€{$tipologia nofilter}
    {/if}
{/if}
