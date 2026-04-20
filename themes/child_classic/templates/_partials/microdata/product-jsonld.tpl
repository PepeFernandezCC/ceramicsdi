
{assign var=hasAggregateRating value=false}
{if !empty($product.productComments.averageRating) && !empty($product.productComments.nbComments)}
    {assign var=hasAggregateRating value=true}
    {assign var=ratingValue value=$product.productComments.averageRating}
    {assign var=ratingReviewCount value=$product.productComments.nbComments}
{/if}
{if !empty($ratings.avg) && !empty($nbComments)}
    {assign var=hasAggregateRating value=true}
    {assign var=ratingValue value=$ratings.avg}
    {assign var=ratingReviewCount value=$nbComments}
{/if}
{assign var=hasWeight value=false}
{if isset($product.weight) && ($product.weight != 0)}
    {assign var=hasWeight value=true}
{/if}
{assign var=hasOffers value=$product.show_price}

{assign var=priceWeb value=Product::getPriceWebIfExists($product.id)}
{assign var=calculatedPrice value=Product::getMinimalQuantityPrice($product.id)}

{if !$priceWeb }
  {assign var=priceWeb value=$product.price_amount}
{/if}

{assign var=productColor value=Product::getProductAttribute($product.id, 46)}
{assign var=productMaterial value=Product::getProductAttribute($product.id, 45)}
{assign var=ccpr value=Product::getProductRating($product.id)}
{assign var=reviews value=Product::getProductReviews($product.id)}
<script type="application/ld+json">
  {
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "{$product.name}",
    {if $productColor}"color": "{$productColor}",{/if}

    {if $productMaterial}"material": "{$productMaterial}",{/if}

    "description": "{$page.meta.description|regex_replace:"/[\r\n]/" : " "}",
    "category": "{$product.category_name}",
    {if !empty($product.cover)}"image" :"{$product.cover.bySize.home_default.url}",{/if}
    "sku": "{if $product.reference}{$product.reference}{else}{$product.id}{/if}",
    "mpn": "{if $product.mpn}{$product.mpn}{elseif $product.reference}{$product.reference}{else}{$product.id}{/if}"
    {if $product.ean13},"gtin13": "{$product.ean13}"
    {else if $product.upc},"gtin13": "{$product.upc}"
    {/if}
    {if $product_manufacturer->name OR $shop.name},
    "brand": {
      "@type": "Thing",
      "name": "{if $product_manufacturer->name}{$product_manufacturer->name|escape:'html':'UTF-8'}{else}{$shop.name}{/if}"
    },
    {/if}
    {if isset($ccpr.ccpr_micro_count) && $ccpr.ccpr_micro_count|intval > 0}
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{$ccpr.ccpr_micro_avg|floatval}",
        "reviewCount": "{$ccpr.ccpr_micro_count|intval}",
        "bestRating": "5",
        "worstRating": "1"
      },
      "review": [
        {foreach from=$reviews item=$review name=reviewLoop}{
          "@type": "Review",
          "author": {
              "@type": "Person",
              "name": "{$review.name}"
            },
          "datePublished": "{$review.date}",
          "reviewBody": "{$review.text}",
          "reviewRating": {
            "@type": "Rating",
            "bestRating": "5",
            "ratingValue": "{$review.rating}",
            "worstRating": "1"
          }
        }{if not $smarty.foreach.reviewLoop.last},{/if}

      {/foreach}],
    {else}
      {if $language.id == 1}
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.9",
        "reviewCount": "68",
        "bestRating": "5",
        "worstRating": "1"
      },
      {/if}
      {if $language.id == 2}
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "reviewCount": "29",
        "bestRating": "5",
        "worstRating": "1"
      },
      {/if}
      {if $language.id == 3}
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "5",
        "reviewCount": "2",
        "bestRating": "5",
        "worstRating": "1"
      },
      {/if}
    {/if}

    {if $hasWeight}"weight": {
        "@context": "https://schema.org",
        "@type": "QuantitativeValue",
        "value": "{$product.weight}",
        "unitCode": "{$product.weight_unit}"
    },
    {/if}

    {if $hasOffers}"offers": {
      "@type": "Offer",
      "priceCurrency": "{$currency.iso_code}",
      "name": "{$product.name|strip_tags:false}",
      "price": "{$calculatedPrice}",
      "url":"{$product.url|regex_replace:"/#.*/":""}",
      "priceValidUntil": "{($smarty.now + (int) (60*60*24*15))|date_format:"%Y-%m-%d"}",
      {if $product.images|count > 0}
        "image": {strip}[
          {if !empty($product.cover)}"{$product.cover.bySize.home_default.url}",{/if}
          {foreach from=$product.images item=p_img name="p_img_list"}
            "{$p_img.large.url}"{if not $smarty.foreach.p_img_list.last},{/if}
          {/foreach}
        ]{/strip},
      {/if}
      "sku": "{if $product.reference}{$product.reference}{else}{$product.id}{/if}",
      "mpn": "{if $product.mpn}{$product.mpn}{elseif $product.reference}{$product.reference}{else}{$product.id}{/if}",
      {if $product.ean13}"gtin13": "{$product.ean13}",{else if $product.upc}"gtin13": "0{$product.upc}",{/if}
      {if $product.condition == 'new'}"itemCondition": "https://schema.org/NewCondition",{/if}
      {if $product.show_condition > 0}
        {if $product.condition == 'used'}"itemCondition": "https://schema.org/UsedCondition",{/if}
        {if $product.condition == 'refurbished'}"itemCondition": "https://schema.org/RefurbishedCondition",{/if}
      {/if}
      "availability": "{$product.seo_availability}",
      "seller": {
        "@type": "Organization",
        "name": "{$shop.name}"
      }
    }
    {/if}
  }
</script>
