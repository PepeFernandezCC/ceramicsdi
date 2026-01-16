{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{assign var="categoriasProducto" value=Product::getProductCategories($product.id)}
{assign var="id_cart" value=Context::getContext()->cart->id} 
{assign var="muestraEnCarrito" value=Cart::checkProductInCartStatic($id_cart, $product.id, true)}
{assign var="productoEnCarrito" value=Cart::checkProductInCartStatic($id_cart, $product.id, false)}
{assign var="samplesInCart" value=0}
{assign var="samplesInCart" value=Cart::getSamplesNumberInCartStatic($id_cart)}
{assign var="maxProductsInCart" value=false}
{assign var="boxPrice" value=Product::getMinimalPriceTemplate($product.id, $customer.id)}
{if $samplesInCart >= 8}
    {$maxProductsInCart = true}
{/if}


{if $CATEGORY_INSTALACION_ID|in_array:$categoriasProducto || $CATEGORY_MANTENIMIENTO_ID|in_array:$categoriasProducto || $CATEGORY_ARTICULATIONS|in_array:$categoriasProducto}
    {assign var="normalSell" value=true}
{else}
    {assign var="normalSell" value=false}
{/if}

{assign var="isByPiece" value=false}
{assign var="hasSample" value=true}

    {foreach from=$product.features item='feature'}

        {if $feature.id_feature === $FEATURE_M2_PIEZA_ID}

            {assign var="isByPiece" value=true}

        {/if}
        {if $feature.id_feature === $FEATURE_SAMPLE_AVAILABLE}

            {assign var="hasSample" value=false}
            {assign var="sampleTextWarning" value=$feature.value}

        {/if}

    {/foreach}

<div class="product-add-to-cart js-product-add-to-cart">
     <input type="hidden" id="cartId" name="cartId" value="{Context::getContext()->cart->id}" />
     {assign var="amountDiscount" value=Product::getAmountDiscount($product.id)}
     <div id="amount-discount" style="display:none" data-discount="{$amountDiscount.discount}" data-amount="{$amountDiscount.amount}"></div>
    {if !$configuration.is_catalog}

        {block name='product_quantity'}
            
            {assign var="m2_caja" value="0"}
            {assign var="piezas_caja" value="0"}
            {assign var="muestra_de_pago" value=""}
            {foreach from=$product.grouped_features item=feature}
                {if $FEATURE_M2_CAJA_ID === $feature.id_feature}
                    {assign var="m2_caja" value="{$feature.value}"}
                {elseif $FEATURE_PIEZAS_CAJA_ID === $feature.id_feature}
                    {assign var="piezas_caja" value="{$feature.value}"}
                {elseif $FEATURE_MUESTRA_DE_PAGO_ID === $feature.id_feature}
                    {assign var="muestra_de_pago" value="{$feature.value}"}
                {/if}
            {/foreach}

            {if !$normalSell}
                   
                    <div class="capacity-format" style="display:none">
                        {foreach from=$product.features item='feature'}

                            {if $isByPiece}

                                {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_M2_PIEZA_ID}

                                    <span>m<sup>2</sup>/{l s='piece' d='Shop.Theme.Catalog'}{l s=': ' d='Shop.Theme.Catalog'}{$feature.value}</span>

                                    <br>

                                {/if}

                            {else}

                                {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_M2_CAJA_ID}

                                    <span>m<sup>2</sup>/{l s='box' d='Shop.Theme.Catalog'}{l s=': ' d='Shop.Theme.Catalog'}{$feature.value}</span>

                                    <br>

                                {/if}

                            {/if}
                        {/foreach}                    
                    </div>

                <div class="product-quantity-wrapper clearfix">

                    {if $isByPiece}

                        <div class="inputs-calculator">

                            <div class="row mx-auto row-calculator margin-calculator">

                                <div class="item-calculator request-quantity">
                                        <div class="label-query">
                                            <div class="queryCalculator">
                                                <strong>
                                                    {l s='How many pieces do you need?' d='Shop.Theme.Catalog'}
                                                </strong>
                                            </div>  
                                        </div>                              
                                

                                        <input
                                            type="number"
                                            name="pieces"
                                            id="pieces-input"
                                            inputmode="numeric"
                                            step="0.01"
                                            min="0.01"
                                            class="input-group boxInput"
                                            aria-label="{l s='Quantity' d='Shop.Theme.Actions'}"
                                            data-piezas-caja="{$piezas_caja}"
                                            placeholder="{l s='pieces needed' d='Shop.Theme.Actions'}"
                                            rquired
                                        >
                                
                                </div>

                                <div class="m2cajaInfo">
                                    
                                    {foreach from=$product.features item='feature'}
                                        {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_M2_PIEZA_ID}
                                            <span>{l s='pieces' d='Shop.Theme.Catalog'}/{l s='box' d='Shop.Theme.Catalog'}:<strong>{$piezas_caja}</strong></span>
                                        {/if}
                                    {/foreach}
                                    
                                    
                                </div>
                            </div>

                            <div class="actionButtons">
                                <div class="row mx-auto row-calculator">
                                    <div class="item-calculator">

                                            <div class="boxTextTitle">
                                                <strong>{l s='pieces' d='Shop.Theme.Catalog'}:</strong>
                                            </div>
                                            <div class="box-container">
                                                <button type="button" id="decrementPieces" class="boxButton" style="width:60px; background-color: #eac133">
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>                            
                                                <input type="number" id="inputPiecesBox" value="0" class="boxInput max-widt-box" inputmode="decimal">
                                                <button type="button" id="incrementPieces" class="boxButton" style="width:60px; background-color: #eac133">
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                            </div>
                    
                                    </div>
                                </div>
                                <div class="row mx-auto box-recommendation">
                                    {if $category->id_category != $CATEGORY_INSTALACION_Y_MONTAJE_ID}
                                        <div class="recomendation">

                                            <div class="wasteSwitch" style="padding-right: 15px">
                                                <input class="toggle" type="checkbox" id="recomendation-check-pieces" name="recomendation-check" />
                                                <label class="switch" for="recomendation-check-pieces" style="margin-bottom:0"></label>
                                            </div>

                                             <div class="ptd-10" style="font-weight:500; display:flex">
                                                <div class="addWasteInfo">
                                                {l s='Add 10–15% extra' d='Shop.Theme.Catalog'}
                                                </div>
                                                <div class="wasteLink" style="padding-left:5px">
                                                    <a href="#" id="openModal" data-toggle="modal" data-target="#wasteModal"  style="display:flex; font-weight: bold">
                                                        + INFO <span style="color:#eac133; padding-left:3px; font-size:21px"><i class="fa-solid fa-angle-right"></i></span>
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    {/if}
                                </div>
                            </div>

                        </div>



                        <hr>

                        <div class="row mx-auto" style="display:none">
                            <div class="col-xl-6 col-xs-6 item-calculator">
                                <div style="text-transform: uppercase">
                                    <label>
                                        <strong>
                                            {l s='Total' d='Shop.Theme.Actions'}:
                                        </strong>
                                    </label>
                                </div>
                                <div>
                                    <div style="position:relative;">
                                        <div>    
                                            <input type="text" id="pieces-real" disabled class="input-group boxInput cc-background-color-secondary">
                                        </div>
                                        <div class="position-coin" style="font-size: 16px">
                                           {l s='pieces' d='Shop.Theme.Catalog'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="subtotal-product" style="padding-bottom:15px; display:none">
                            <div class="subtotal-title-product">{l s='Total Boxes' d='Shop.Theme.Catalog'}:</div>
                            <div class="subtotal-quantity-product"><span id="pieceSubtotalBoxes">0</span> {l s='Boxes' d='Shop.Theme.Catalog'}</div>
                        </div>
                        <div class="subtotal-product">
                            <div class="subtotal-title-product">{l s='Total pieces' d='Shop.Theme.Catalog'}:</sup></div>
                            <div class="subtotal-quantity-product"><span id="pieceTotalMeters">0.00</span> {l s='pieces' d='Shop.Theme.Catalog'}</div>
                        </div>
                    {else}

                        <div class="inputs-calculator">

                            <div class="row mx-auto row-calculator margin-calculator">

                                <div class="item-calculator request-quantity">
                                        <div class="label-query">
                                            <div class="queryCalculator">
                                                <strong>
                                                    {l s='How many m2 do you need' d='Shop.Theme.Catalog'}
                                                </strong>
                                            </div>                                
                                        </div>

                                        <input
                                                type="number"
                                                name="surface"
                                                id="surface-input"
                                                inputmode="numeric"
                                                step="0.01"
                                                min="0.01"
                                                class="input-group boxInput"
                                                aria-label="{l s='Surface' d='Shop.Theme.Actions'}"
                                                data-m2-caja="{$m2_caja}"
                                                placeholder="{l s='m2 needed' d='Shop.Theme.Actions'}"
                                        >
                                </div>

                                <div class="m2cajaInfo">
                                    {foreach from=$product.features item='feature'}
                                        {if isset($feature.id_feature) && $feature.id_feature == $FEATURE_M2_CAJA_ID}
                                            <span>m<sup>2</sup>/{l s='box' d='Shop.Theme.Catalog'}{l s=': ' d='Shop.Theme.Catalog'}<strong>{$feature.value}</strong></span>
                                        {/if}
                                    {/foreach}
                                </div> 

                            </div>

                            <div class="actionButtons">

                                <div class="row mx-auto row-calculator">

                                    <div class="item-calculator">
        
                                        <div class="boxTextTitle">
                                            <strong>{l s='Number of Boxes' d='Shop.Theme.Catalog'}:</strong>
                                        </div>
                                        <div class="box-container">
                                            <button type="button" id="decrementButton" class="boxButton" style="width:60px; background-color: #eac133">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>                            
                                            <input type="number" id="numberInput" value="0" class="boxInput max-widt-box" inputmode="decimal">
                                            <button type="button" id="incrementButton" class="boxButton" style="width:60px; background-color: #eac133">
                                                <i class="fa-solid fa-plus"></i>

                                            </button>
                                        </div>

                                    </div> 

                                </div>

                                <div class="row mx-auto box-recommendation">
                                    {if $category->id_category != $CATEGORY_INSTALACION_Y_MONTAJE_ID}
                                        <div class="recomendation">

                                            <div class="wasteSwitch" style="padding-right: 15px">
                                                <input class="toggle" type="checkbox" id="recomendation-check" name="recomendation-check" />
                                                <label class="switch" for="recomendation-check" style="margin-bottom:0"></label>
                                            </div>

                                            <div class="ptd-10" style="font-weight:500; display:flex">
                                                <div class="addWasteInfo">{l s='Add 10–15% extra' d='Shop.Theme.Catalog'}</div>
                                                <div class="wasteLink" style="padding-left:5px">
                                                    <a href="#" id="openModal" data-toggle="modal" data-target="#wasteModal" style="display:flex; font-weight: bold">
                                                    + INFO<span style="color:#eac133; padding-left:3px; font-size:21px"><i class="fa-solid fa-angle-right"></i></span>
                                                    </a>
                                                </div>
                                                
                                                
                                            </div>

                                        </div>
                                    {/if}
                                </div>

                            </div>

                        </div>

                        <hr>

                        <div class="row mx-auto" style="display:none">
                            <div class="col-xl-6 col-xs-6 item-calculator">
                                <div style="text-transform: uppercase">
                                    <label>
                                        <strong>
                                            {l s='Total' d='Shop.Theme.Actions'}:
                                        </strong>
                                    </label>
                                </div>
                                <div>
                                    <div style="position:relative;">
                                        <div>    
                                            <input type="text" id="surface-real" disabled class="input-group boxInput cc-background-color-secondary">
                                        </div>
                                        <div class="position-coin" style="font-size: 16px">
                                           M<sup>2</sup>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="subtotal-product" style="padding-bottom:15px">
                            <div class="subtotal-title-product">{l s='Total Boxes' d='Shop.Theme.Catalog'}:</div>
                            <div class="subtotal-quantity-product"><span id="m2SubtotalBoxes">0</span> {l s='Boxes' d='Shop.Theme.Catalog'}</div>
                        </div>
                        <div class="subtotal-product">
                            <div class="subtotal-title-product">{l s='Total' d='Shop.Theme.Catalog'} M<sup>2:</sup></div>
                            <div class="subtotal-quantity-product"><span id="m2TotalMeters">0.00</span> M<sup>2</sup></div>
                        </div>

                    {/if}


                    <div id="quantity-wrapper" class="row mx-auto row-calculator" style="display:none">
                        <div class="col-xl-6 col-xs-12">

                            <label class="font-weight-bold" for="quantity-input" style="width: 100%">
                                <div class="capacity-format">
                                    <div>
                                        {l s='Total' d='Shop.Theme.Actions'}{l s=': ' d='Shop.Theme.Catalog'}
                                    </div>

                                </div>
                            </label>
                            <div style="position: relative;">
                                <div>
                                    <input
                                    type="number"
                                    name="qty"
                                    id="quantity-input"
                                    inputmode="numeric"
                                    step="1"
                                    min="0"
                                    value="0"
                                    class="input-group"
                                    aria-label="{l s='Total' d='Shop.Theme.Actions'}"
                                    readonly="readonly"
                                    style="display: none !important;"
                                    >
                        
                                    <input
                                    type="number"
                                    name="euros"
                                    id="euros-input"
                                    inputmode="numeric"
                                    step="0.01"
                                    min="0.00"
                                    value="0.00"
                                    class="input-group boxInput cc-background-color-secondary"
                                    aria-label="{l s='Total' d='Shop.Theme.Actions'}"
                                    readonly="readonly"
                                    data-price="{$boxPrice}"
                                    style="font-weight: bold;"
                                    >
                                </div>
                                <div class="position-coin">
                                    €
                                </div>
                            </div>

                        </div>
                        
                    </div>

                    <div id="variants-wrapper" class="row" style="display:none">
                        {* PLANATEC *}
                        {block name='product_variants'}
                            {include file='catalog/_partials/product-variants.tpl'}
                        {/block}
                        {* END PLANATEC *}
                    </div>

                    <hr>

                    <div class="total-product">
                        <div class="total-title-product">{l s='Total' d='Shop.Theme.Catalog'}:</div>
                        <div class="total-quantity-product"><span id="m2TotalPrice">0.00</span> €</div>
                    </div>

                   

                    
                    <div style="position:relative">
                        <div id="add-wrapper" class="buy-buttons-box">

                            <div class="col-xl-6 col-xs-6" style="padding: 0">
                                {if $hasSample}
                                    <div class="add-sample">
                                        <button 
                                                id="add-sample-to-cart-button"
                                                class="btn btn-primary add-to-cart add-to-cart-sample add-sample-to-cart"
                                                data-button-action="add-to-cart-sample"
                                                type="button"
                                                {if $muestraEnCarrito || $maxProductsInCart || $productoEnCarrito}
                                                    disabled
                                                {/if}
                                        >
                                            {if $muestra_de_pago !== ''}
                                                {l s='Request sample' d='Shop.Theme.Actions'}
                                            {else}
                                                {l s='Request free sample' d='Shop.Theme.Actions'}
                                            {/if}
                                        </button>
                                    </div>
                                    <div id="sample-in-cart" {if !$muestraEnCarrito}style="display:none"{/if}>
                                        {* l s='sample in cart' d='Shop.Theme.Actions' *}
                                        <img class="sample-in-cart-img" src="/themes/child_classic/assets/img/cc_cart.png" loading="lazy" alt="shopping cart icon">
                                    </div>
                                {/if}

                            </div>

                            <div class="col-xl-6 col-xs-6" style="padding: 0">
                                <div class="add">
                                    <button
                                            id="add-to-cart-submit"
                                            class="btn btn-primary add-to-cart add-product-to-cart"
                                            data-button-action="add-to-cart"
                                            type="submit"
                                            {if !$product.add_to_cart_url || $muestraEnCarrito}
                                                disabled
                                            {/if}
                                    >
                                        {l s='Add to cart' d='Shop.Theme.Actions'}
                                    </button>
                                </div>
                            </div>


                            
                        </div>
                    </div>
                    <div id="sticky-sentinel"></div>
                </div>
            
                {block name='waste_modal'}
                    {include file='catalog/_partials/_modal-waste.tpl'}
                {/block}                

            {/if}
            
            {if $normalSell}
                <div class="product-quantity-wrapper clearfix">
                    <div id="variants-wrapper" class="row">
                        {* PLANATEC *}
                        {block name='product_variants'}
                            {include file='catalog/_partials/product-variants.tpl'}
                        {/block}
                        {* END PLANATEC *}
                    </div>
                    {if $product.id_category_default == $CATEGORY_ARTICULATIONS}
                        <div class="row mobile-m-auto">
                                <input type="hidden" id="kgs_sack" name="kgs_sack" value={$product.weight|intval} />
                                <input type="hidden" id="manufacturer" name="manufacturer" value={$product.id_manufacturer|intval} />
                        </div>   
                        <div class="row normalShellPricing">
                            <div class="col-md-4 col-xs-12 pb-10">
                                <label style="text-transform:capitalize">{l s='Tile length' d='Shop.Theme.Checkout'} <span style="text-transform:lowercase">(mm)</span>:</label>
                                <input type="number" class="input-group boxInput" id="large_tile" inputmode="decimal" style="border: 2px solid #eac133;" placeholder="mm">
                            </div>
                            <div class="col-md-4 col-xs-12 pb-10">
                                <label style="text-transform:capitalize">{l s='Tile width' d='Shop.Theme.Checkout'} <span style="text-transform:lowercase">(mm)</span>:</label>
                                <input type="number" class="input-group boxInput" id="height_tile" inputmode="decimal" style="border: 2px solid #eac133;" placeholder="mm">
                            </div>
                            <div class="col-md-4 col-xs-12 pb-10">
                                <label style="text-transform:capitalize">{l s='Tile thickness' d='Shop.Theme.Checkout'} <span style="text-transform:lowercase">(mm)</span>:</label>
                                <input type="number" class="input-group boxInput" id="espessor_tile" inputmode="decimal" style="border: 2px solid #eac133;" placeholder="mm">
                            </div>
                        </div>
                        <div class="row normalShellPricing">
                            <div class="col-md-4 col-xs-12 pb-10">
                                <label style="text-transform:capitalize">{l s='Joint width' d='Shop.Theme.Checkout'} <span style="text-transform:lowercase">(mm)</span>:</label>
                                <input type="number" class="input-group boxInput" id="large_joint" inputmode="decimal" style="border: 2px solid #eac133;" placeholder="mm">
                            </div>

                            <div class="col-md-8 col-xs-12 pb-10">
                                <label style="text-transform:capitalize">{l s='Surface to apply' d='Shop.Theme.Checkout'} <span style="text-transform:lowercase">(m<sup>2</sup>)</span>:</label>
                                <input type="number" class="input-group boxInput" id="m2_area" inputmode="decimal" style="border: 2px solid #eac133;" placeholder="m²">
                            </div>
                        </div>
                        <div class="row normalShellPricing calculate-kgs-row">
                            <div class="col-md-4 col-xs-12 pb-10">
                                <button type="button" class="input-group boxInput" id="jointCalculatorProcess" style="text-transform:capitalize">{l s='Calculate' d='Shop.Theme.Checkout'}</button>
                            </div>
                            <div class="col-md-8 col-xs-12 pb-10">
                                                                      
                                <div style="position: relative;">
                                    <div>
                                        <input
                                        type="number"
                                        name="euros"
                                        id="total_kgs"
                                        inputmode="numeric"
                                        value="0"
                                        class="input-group boxInput cc-background-color-secondary"
                                        readonly="readonly"
                                        style="font-weight: bold;"
                                        >
                                    </div>
                                    <div class="position-coin">Kgs.</div>
                                </div>
                            </div>  
                        </div>
                    {/if}

                    <div class="row normalShellPricing">
                        <div class="col-xl-4 col-xs-12" style="padding-bottom: 15px">

                            <div style="text-transform: uppercase">
                                <label>
                                    <strong>{l s='Quantity' d='Shop.Theme.Actions'}:</strong>
                                </label>
                            </div>
                            <div class="box-container">
                                <button type="button" id="decrementQuantity" class="boxButton" style="width:65px; background-color: #eac133">
                                    <i class="fa-solid fa-minus"></i>
                                </button>

                                <input type="number" name="qty" id="quantity-input" inputmode="decimal" class="input-group boxInput" value="0">

                                <button type="button" id="incrementQuantity" class="boxButton" style="width:65px; background-color: #eac133">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-xl-8 col-xs-12" style="padding-bottom: 15px">
                            <label class="font-weight-bold" for="euros-input" style="width: 100%">
                                <div class="capacity-format">
                                    <div style="text-transform: uppercase">
                                        {l s='Total' d='Shop.Theme.Actions'}{l s=': ' d='Shop.Theme.Catalog'}
                                    </div>
                                </div>
                            </label>
                            <div style="position: relative;">
                                <div>
                                    <input
                                    type="number"
                                    name="euros"
                                    id="euros-input"
                                    inputmode="numeric"
                                    step="0.01"
                                    min="0.00"
                                    value="0.00"
                                    class="input-group boxInput cc-background-color-secondary"
                                    aria-label="{l s='Total' d='Shop.Theme.Actions'}"
                                    readonly="readonly"
                                    data-price="{$boxPrice}"
                                    style="font-weight: bold;"
                                    >
                                </div>
                                <div class="position-coin">
                                    €
                                </div>
                            </div>
            
                        </div>
                    </div>
                    
                    {if $product.id_category_default == $CATEGORY_ARTICULATIONS}
                        <div class="row mx-auto">

                            <div class="col-md-12 col-xs-12 joint-advertisement">
                                <div class="calculator-advertisement">
                                    {if $language.id == 1}
                                        <span style="font-weight:bold">Aviso:</span> El cálculo se basa en el volumen y la densidad del producto.
                                        Debido a que algunos factores pueden influir en el consumo real (como la rugosidad de las baldosas, la cantidad de residuos, las superficies no planas, etc.), 
                                        <span style="font-weight:bold">los datos suministrados son meramente indicativos y solo a efectos de estimación.</span>
                                    {elseif $language.id == 2}
                                        <span style="font-weight:bold">Avis :</span> Le calcul est basé sur le volume et la densité du produit.
                                        Comme certains facteurs peuvent influencer la consommation réelle (comme la rugosité des carreaux, la quantité de déchets, les surfaces irrégulières, etc.),
                                        <span style="font-weight:bold">les données fournies sont uniquement indicatives et à des fins d'estimation.</span>
                                    {elseif $language.id == 3}
                                        <span style="font-weight:bold">Notice:</span> The calculation is based on the volume and density of the product.
                                        Since some factors may influence actual consumption (such as tile roughness, amount of waste, uneven surfaces, etc.),
                                        <span style="font-weight:bold">the data provided are merely indicative and for estimation purposes only.</span>
                                    {elseif $language.id == 4}
                                        <span style="font-weight:bold">Hinweis:</span> Die Berechnung basiert auf dem Volumen und der Dichte des Produkts.
                                        Da einige Faktoren den tatsächlichen Verbrauch beeinflussen können (wie z.B. die Rauheit der Fliesen, die Menge des Abfalls, unebene Oberflächen usw.),
                                        <span style="font-weight:bold">die bereitgestellten Daten sind nur indikativ und dienen nur zu Schätzungszwecken.</span>
                                    {elseif $language.id == 5}
                                        <span style="font-weight:bold">Aviso:</span> O cálculo baseia-se no volume e na densidade do produto.
                                        Como alguns fatores podem influenciar o consumo real (como a rugosidade das telhas, a quantidade de resíduos, superfícies irregulares, etc.),
                                        <span style="font-weight:bold">os dados fornecidos são meramente indicativos e apenas para efeitos de estimativa.</span>
                                    {elseif $language.id == 6}
                                        <span style="font-weight:bold">Opmerking:</span> De berekening is gebaseerd op het volume en de dichtheid van het product.
                                        Omdat sommige factoren invloed kunnen hebben op het daadwerkelijke verbruik (zoals de ruwheid van tegels, de hoeveelheid afval, oneffen oppervlakken, enz.),
                                        <span style="font-weight:bold">zijn de verstrekte gegevens slechts indicatief en alleen voor schattingsdoeleinden.</span>
                                    {else}
                                        <span style="font-weight:bold; color: red">ERROR:: LANGUAGE NOT DETECTED</span>
                                    {/if}
                                </div>
                            </div>

                        </div>
                    {/if}

                    <div class="row mx-auto">
                        <div style="position:relative">
                            <div id="add-wrapper" class="normal-buy-buttons">
                                <div class="col-xl-12 col-xs-12" style="padding-top: 0; padding-bottom: 0">
                                    <div class="add" style="padding:10px">
                                        <button
                                                class="btn btn-primary add-to-cart add-product-to-cart"
                                                data-button-action="add-to-cart"
                                                type="submit"
                                                {if !$product.add_to_cart_url}
                                                    disabled
                                                {/if}
                                        >
                                            {l s='Add to cart' d='Shop.Theme.Actions'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="sticky-sentinel"></div>
                    </div>



                </div>
            {/if}

            {hook h='displayProductActions' product=$product}

        {/block}

    {/if}

</div>
