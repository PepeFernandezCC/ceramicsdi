{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}

<div class="panel">
    <script type="text/javascript">
        var urlmoduleimageoptimize = "{$urlmoduleimageoptimize|escape:'html':'UTF-8'}";
    </script>
    <div class="panel-heading">
        <i class="icon-cogs"></i>{l s="Demo zone"  mod='lgimageoptimize'}
    </div>
    <div class="form-wrapper">
        <div id="product-content" class="row">
            <div class="col-md-12">
                {l s='Select a sample product' mod='lgimageoptimize'}
            </div>
            <div class="col-xl-8 col-lg-12">
                <fieldset class="form-group">
                    <input type="text" id="search_product"  name="product_search" style="max-width:250px">
                </fieldset>
            </div>
        </div>

        <div id="product_list_result">
        </div>

        <div id="product_selected">
            {include file="$self/views/templates/admin/_partials/product_select.tpl"}
        </div>
    </div>
</div>
