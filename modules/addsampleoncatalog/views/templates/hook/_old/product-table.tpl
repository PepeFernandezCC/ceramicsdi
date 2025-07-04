            {assign var="otherMaterialsArray" value=[81, 82, 88]}
            {assign var="muestra_de_pago" value=""}
            {assign var="maxProductsInCart" value=false}
            {assign var="add_sample_blocked" value="button"}
            {assign var="muestraEnCarrito" value=false}
            {assign var="productoEnCarrito" value=false}

            
            {foreach from=$cart.products item='cartProduct'}
                {if $product.id == $cartProduct.id}
                    {assign var="encontrado" value=true}
                    {foreach from=$cartProduct.attributes key="attribute" item="value"}
                        {if ($attribute == 'Muestra' and $value == 'Sí')
                            || ($attribute == 'Échantillon' and $value == 'Oui')
                            || ($attribute == 'Sample' and $value == 'Yes')
                            || ($attribute == 'Muster' and $value == 'Ja')
                            || ($attribute == 'Amostra' and $value == 'Sim')
                            || ($attribute == 'Voorbeeld' and $value == 'Ja')
                        }
                            {$muestraEnCarrito = true}
                            {$encontrado = false}
                        {/if}
                    {/foreach}

                    {if $encontrado}
                        {$productoEnCarrito = true}
                    {/if}
                {/if}
            {/foreach}
            
            {if !$productoEnCarrito}
                {if $cart.products|count >= 10}
                    {$maxProductsInCart = true}
                    {foreach from=$cart.products item=cartProduct}
                        {if $cartProduct.id == $product.id && !$muestraEnCarrito}
                            {$maxProductsInCart = false}
                        {/if}
                    {/foreach}
                {/if}
            {/if}

            {foreach from=$product.grouped_features item=feature}
                {if $FEATURE_MUESTRA_DE_PAGO_ID === $feature.id_feature}
                    {assign var="muestra_de_pago" value="{$feature.value}"}
                {/if}
            {/foreach}

            {if !$product.add_to_cart_url || $muestraEnCarrito || $maxProductsInCart}
                {assign var="add_sample_blocked" value="blocked"}
            {/if}
           
            <div class="product-table">
                <div class="product-table-cell">
                    <span class="feature-box-title"> {l s='Format' d='Shop.Theme.Catalog'}</span>
                    <br />                  
                    {foreach from=$product.features item='feature'}
                        {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MEDIDA_ID}
                            <span class="product-new-feature-medida">{$feature.value}</span>
                        {/if}
                    {/foreach}
                </div>
                                
                <div class="product-table-cell middle">
                    <span class="feature-box-title">{l s='Material' d='Shop.Theme.Catalog'}</span>
                    <br />
                    {if !in_array($product.id_category_default, $otherMaterialsArray)}
                        {foreach from=$product.features item='feature'} 
                            {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_MATERIAL}
                                <span class="product-new-feature-material">{$feature.value}</span>
                            {/if}
                        {/foreach}
                    {else}
                         <span class="product-new-feature-material">{$product.category_name}</span>
                    {/if}
                </div>


            </div>