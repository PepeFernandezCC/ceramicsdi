<div class="tab-panel" id="product-accordion-use" role="tabpanel">

    {assign var="useFeatures" value=$link->getGroupedUseFeatures($product.features, $USE_ARRAY)}

    {block name='product_features'}
        {if $useFeatures}
            <section class="product-features">
                <ul class="accordion-ul">

                    {foreach from=$useFeatures item=feature}
                        <li class="accordion-li">
                            <span style="text-transform:capitalize">
                                {$feature.name}{l s=': ' d='Shop.Theme.Catalog'}
                            </span>

                            <span>
                                {foreach from=$feature.values item=feature_value name=feature_iteration}
                                    {if not $smarty.foreach.feature_iteration.first}, {/if}

                                    {if $feature_value.is_link}
                                        <a style="font-weight:700; text-underline-offset:3px; text-decoration:underline"
                                           href="{$feature_value.url}">
                                            {$feature_value.value|escape:'htmlall'}
                                        </a>
                                    {else}
                                        <span style="font-weight:bold">
                                            {$feature_value.value|escape:'htmlall'}
                                        </span>
                                    {/if}
                                {/foreach}
                            </span>
                        </li>
                    {/foreach}

                </ul>
            </section>
        {/if}
    {/block}

</div>