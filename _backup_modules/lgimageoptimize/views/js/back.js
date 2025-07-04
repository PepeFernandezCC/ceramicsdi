/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

/**
 * Related product management
 */

$(document).ready(function () {

    $(document).on('keyup','#search_product',function(){
        var searchProduct = $('#search_product');

        if(searchProduct.val().length>3)
        {
            $.ajax({
                type: 'POST',
                url: urlmoduleimageoptimize,
                dataType: 'json',
                data: {
                    searchProduct: searchProduct.val(),
                    action: 'productSearch',
                    ajax: true,
                },
                success: function (response) {
                    if(response.found==true) {
                        showSuccessMessage(response.message);
                        showProductList(response);
                    }
                    else{
                        showErrorMessage(response.message)
                    }
                }
            });

        }
    });

    $(document).on('click','.product_searched',function(){
        var id_product =  $(this).data('id_product');
        var type_image = $('#type_image').val();
        $.ajax({
            type: 'POST',
            url: urlmoduleimageoptimize,
            dataType: 'json',
            data: {
                selectProduct: id_product,
                action: 'selectProduct',
                ajax: true,
            },
            success: function (response) {
                if(response.found==true) {
                    showProductSelect(response);
                }
            }
        });
    });

    $(document).on('click','#generate_demo',function(){
        $.ajax({
            type: 'POST',
            url: urlmoduleimageoptimize,
            dataType: 'json',
            data: {
                action: 'drawImageZona',
                ajax: true,
            },
            success: function (response) {
                if(response.found==true) {
                    showImageZone(response);
                }
            }
        });
    });


});

function showProductList(response)
{
    $('#product_list_result').html("");
    response.products.forEach(showLine);
}

function showLine(item,index)
{
    $('#product_list_result').append(item.html);
}

function showProductSelect(item)
{
    $('#product_selected').html("");
    $('#image_zone').html("");
    $('#product_list_result').html("");
    $('#product_selected').append(item.html);
}

function showImageZone(item)
{
    $('#image_zone').html("");
    $('#image_zone').append(item.html);
}

