           
            {assign var="otherMaterialsArray" value=[81, 82, 88]}
            {assign var="materialValue" value=" - "}
       
            {foreach from=$product.features item='feature'}
                {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}
                    {assign var="formatValue" value=$feature.value}
                {/if}

                {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MATERIAL}
                    {assign var="materialValue" value=$feature.value}
                {/if}
            {/foreach}

            {if in_array($product.id_category_default, $otherMaterialsArray)}
                {assign var="materialValue" value=$product.category_name}
            {/if}

            <div class="product-table">
                <div class="product-table-cell">
                    <div class="feature-box-title"> {l s='Format' d='Shop.Theme.Catalog'}: </div>
                                     
                    <div class="product-new-feature-medida">{$formatValue}</div>
                </div>
                                
                <div class="product-table-cell middle">
                    <div class="feature-box-title">{l s='Material' d='Shop.Theme.Catalog'}: </div>
                    
                    <div class="product-new-feature-material">{$materialValue}</div>
                </div>

            </div>