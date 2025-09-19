<div class="js-product-details tab-panel"
     id="product-details"
     data-product="{$product.embedded_attributes|json_encode}"
     role="tabpanel"
>

    {block name='product_quantities'}
        {if $product.show_quantities}
            <div class="product-quantities">
                <label class="label">{l s='In stock' d='Shop.Theme.Catalog'}</label>
                <span data-stock="{$product.quantity}"
                      data-allow-oosp="{$product.allow_oosp}">{$product.quantity} {$product.quantity_label}</span>
            </div>
        {/if}
    {/block}

    {block name='product_availability_date'}
        {if $product.availability_date}
            <div class="product-availability-date">
                <label>{l s='Availability date:' d='Shop.Theme.Catalog'} </label>
                <span>{$product.availability_date}</span>
            </div>
        {/if}
    {/block}

    {block name='product_out_of_stock'}
        <div class="product-out-of-stock">
            {hook h='actionProductOutOfStock' product=$product}
        </div>
    {/block}
    {assign var="linkFeaturesArray" value=[3, 7, 26, 52]}
    {block name='product_features'}
        {if $product.grouped_features}
            <section class="product-features">
                {foreach from=$product.grouped_features item=feature}
                    {if !in_array($feature.id_feature, $DONT_SHOW_THIS_FEATURES)}
                        <p>
                            <span style="text-transform:capitalize">{$feature.name}{l s=': ' d='Shop.Theme.Catalog'}</span>
                            {if in_array($feature.id_feature, $linkFeaturesArray)}
                                {assign var="id_array" value=$link->getIdFeaturesArray($feature.id_feature_value)}
                                {assign var="values" value=$feature.value|escape:'htmlall'|regex_replace:"/[\r\n]/" : ", "}
                                {assign var="valueArray" value=", "|explode:$values}
                                {assign var="validFeatureidArray" value=[56, 448, 7578, 112067, 112063, 112066, 112061, 112068, 112062, 112060, 112064, 14, 19, 145, 1843, 7340, 7341, 7342, 7343, 7344, 7346, 7347]}
                                <span>
                                    {foreach from=$id_array item=id key=key}
                                        {if $key > 0}, {/if}
                                        {if in_array($id, $validFeatureidArray)}
                                            <a style="text-decoration: underline" href="{$link->getCategoryLinkByIdFeatureValue($id|intval)}">{$valueArray[$key]|escape:'htmlall'}</a>
                                        {else}
                                            <span style="font-weight:bold">{$valueArray[$key]|escape:'htmlall'}</span>
                                        {/if}
                                    {/foreach}
                                </span>
                            {else}
                                <span><strong>{$feature.value|escape:'htmlall'|regex_replace:"/[\r\n]/" : ", " nofilter}</strong></span>
                            {/if}
                        </p>
                    {/if}
                {/foreach}
            </section>
        {/if}
    {/block}

    {* if product have specific references, a table will be added to product details section *}
    {block name='product_specific_references'}
        {if !empty($product.specific_references)}
            <section class="product-features">
                <p class="h6">{l s='Specific References' d='Shop.Theme.Catalog'}</p>
                <dl class="data-sheet">
                    {foreach from=$product.specific_references item=reference key=key}
                        <dt class="name">{$key}</dt>
                        <dd class="value">{$reference}</dd>
                    {/foreach}
                </dl>
            </section>
        {/if}
    {/block}

    {block name='product_condition'}
        {if $product.condition}
            <div class="product-condition">
                <label class="label">{l s='Condition' d='Shop.Theme.Catalog'} </label>
                <link href="{$product.condition.schema_url}"/>
                <span>{$product.condition.label}</span>
            </div>
        {/if}
    {/block}
</div>