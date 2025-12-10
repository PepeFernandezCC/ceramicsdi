<div class="tab-panel" id="product-accordion-measures" role="tabpanel">

    {block name='product_features'}
        {if $product.grouped_features}
            <section class="product-features">
                <ul class="accordion-ul">
                    {foreach from=$product.grouped_features item=feature}
                        {if in_array($feature.id_feature, $MEASURES_ARRAY)}
                            <li class="accordion-li">
                                <span style="text-transform:capitalize">{$feature.name}{l s=': ' d='Shop.Theme.Catalog'}</span>

                                <span><strong>{$feature.value|escape:'htmlall'|regex_replace:"/[\r\n]/" : ", " nofilter}</strong></span>
                            
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </section>
        {/if}
    {/block}

</div>