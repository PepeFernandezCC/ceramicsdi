{block name='content'}

    <section id="ccrelatedproducts">

        <div class="ccrelated-container">
            {foreach from=$relatedproducts item='product'}
                <div class="cardproduct">
                    <a href="{$product.url}">
                        <div class="relatedimages">
                            <div class="portada">
                                <img class="relatedImage" loading="lazy" src="{$product.portada}" alt="{$product.name}" />
                            </div>
                            <div class="muestra">
                                <img class="relatedImage" loading="lazy" src="{$product.image}" alt="{$product.name}" />
                            </div>
                        </div>
                        {assign var='customPrice' value=Product::calculateCustomPrice($product.id, true, $idLang)}
                        <div class="relateddata">
                            <div class="relatedTitle">
                                {$product.name}
                            </div>
                            <div class="relatedFormat">{$product.formato}</div>
                            <div class="relatedMaterial">
                                {if $customPrice.discount > 0}
                                    <div class="discountFlag">-{$customPrice.discount}%</div>
                                    <div class="productPrice">{$customPrice.original_price} €{$customPrice.tipologia nofilter}</div>
                                {/if}

                                <div class="sellPrice">{$customPrice.price} €{$customPrice.tipologia nofilter}</div>
                            </div>
                        </div>
                    </a>
                </div>
            {/foreach}
        </div>

    </section>

{/block}