<div class="accordion-body">

    <form action="index.php?controller=AdminCorreosOficialProductsProcess" id="CorreosProductsForm" name="CorreosProductsForm" method="POST">
        <fieldset>
            <div id="products_container_general" class="row justify-content-around products_container_general hidden-block">
                <div id="advice_products" class="advice">
                    <h4>
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill" /></svg>
                        {l s='Select the products to be displayed in the checkout' mod='correosoficial'}
                    </h4>
                </div>

                {if isset($products_column2) && $products_column2}
                <div id="products_container_correos" class="col-sm-5 products_container {if $correos != true}hidden-block{/if}">
                    <h3>{l s='Correos' mod='correosoficial'}</h3>
                    {foreach from=$products_column2 item=product}
                        <div class="form-check">
                            <input name="products[{$product->id}]" class="form-check-input" id="products[{$product->id}]" type="checkbox" value="{$product->active}" {if $product->active == 1}checked{/if} />
                            <label for="products[{$product->id}]" class="form-check-label form-check-label-color"> {$product->name} </label>
                        </div>
                    {/foreach}
                </div>
                {/if}
                {if isset($products_column1) &&  $products_column1}
                <div id="products_container_cex" class="col-sm-5 products_container {if $cex != true}hidden-block{/if}">
                    <h3>{l s='Correos Express' mod='correosoficial'}</h3>
                    {foreach from=$products_column1 item=product}
                        <div class="form-check">
                            <input name="products[{$product->id}]" class="form-check-input" id="products[{$product->id}]" type="checkbox" value="{$product->active}" {if $product->active == 1}checked{/if} />
                            <label for="products[{$product->id}]" class="form-check-label form-check-label-color"> {$product->name}</label>
                        </div>
                    {/foreach}
                </div>
                {/if}
                <div class="col-sm-12">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <input class="co_primary_button" name="ProductsSaveButton" id="ProductsSaveButton" type="submit" value="{l s='SAVE PRODUCTS' mod='correosoficial'}" />
                    </div>
                </div>
            </div>
        </fieldset>
    </form>

    
    <div id="advice_no_products" class="advice hidden-block">
        <h4>
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill" /></svg>
            {l s='No Customers Active' mod='correosoficial'}
            <a id="go_to_customer_data" href="#customer_data">{l s='If you already has a Customer Code, please go to CUSTOMER DATA' mod='correosoficial'}</a>
        </h4>
    </div>

</div>

<script>
    var productsSaved = "{l s='Products successfully saved' mod='correosoficial'}";
</script>
