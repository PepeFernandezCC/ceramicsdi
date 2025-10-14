<div class="tab-panel" id="product-accordion-use" role="tabpanel">

{assign var="linkFeaturesArray" value=[3, 7, 26, 46, 52]}
    {block name='product_features'}
        {if $product.grouped_features}
            <section class="product-features">
                {foreach from=$product.grouped_features item=feature}
                    {if in_array($feature.id_feature, $USE_ARRAY)}
                        <p>
                            <span style="text-transform:capitalize">{$feature.name}{l s=': ' d='Shop.Theme.Catalog'}</span>
                            {if in_array($feature.id_feature, $linkFeaturesArray)}
                                {assign var="id_array" value=$link->getIdFeaturesArray($feature.id_feature_value)}
                                {assign var="values" value=$feature.value|escape:'htmlall'|regex_replace:"/[\r\n]/" : ", "}
                                {assign var="valueArray" value=", "|explode:$values}
                                {assign var="validFeatureidArray" value=[56, 448, 7578, 112067, 112063, 112066, 112061, 112068, 112062, 112060, 112064, 14, 19, 145, 1843, 7340, 7341, 7342, 7343, 7344, 7346, 7347,111962,111963,111964,111965,111966,111967,111968,111969,111970,111971,111972,111973]}
                                <span>
                                    {foreach from=$id_array item=id key=key}
                                        {if $key > 0}, {/if}
                                        {if in_array($id, $validFeatureidArray)}
                                            <a style="font-weight:700; text-underline-offset: 3px; text-decoration: underline" href="{$link->getCategoryLinkByIdFeatureValue($id|intval)}">{$valueArray[$key]|escape:'htmlall'}</a>
                                        {else}
                                            <span style="font-weight:bold">{$valueArray[$key]|escape:'htmlall'}</span>
                                        {/if}
                                    {/foreach}
                                </span>
                            {else}
                                 <span style="text-transform:capitalize"><strong>{$feature.value|escape:'htmlall'|regex_replace:"/[\r\n]/" : ", " nofilter}</strong></span>
                            {/if}
                        </p>
                    {/if}
                {/foreach}
            </section>
        {/if}
    {/block}

</div>