
jQuery(document).ready(function ($) {

    var product_technical_error=
    "Error al guardar productos.\r\n\
     Revise su configuración. En caso de persistir el error\r\n\
     por favor, póngase en contacto con el Soporte Técnico de Correos";

    $("#CorreosProductsForm").submit(function( e ) {
        $.ajax({
            url: AdminCorreosOficialProductsProcess + '&action=CorreosProductsForm',
            type: "POST",
            data:  new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            success: function(data)
            {
                showModalInfoWindow(productsSaved); 
                var obj = JSON.parse(data);
                switch (obj.info){
                case 'INFO 14503':
                case 'INFO 14504':
                case 'INFO 14505':
                case 'INFO 14506':
                    showModalInfoWindow(obj.desc);
                    break;
                }

                $.ajax({
                    type: 'post',
                    url: AdminCorreosOficialProductsProcess + '&action=getActiveProducts',
                    success: function (response) {
                        $("#CorreosZonesCarriersForm .scp_products").each(function(){
                            select_name = $(this).attr('name');
                            select_value = $(this).val();
                            if (select_value == null){
                                $(this).empty();
                                $('#'+ select_name).append('<option value="0">Select a product</option>');
                                $.each(JSON.parse(response), function(key,value) {   
                                    if (value.product_type == 'office' || value.product_type == 'citypaq'){
                                        $('#'+ select_name).append('<option value=' + value.id + ' disabled>' + value.name + '</option>');
                                    } else {
                                        $('#'+ select_name).append('<option value=' + value.id + '>' + value.name + '</option>');
                                    }                   
                                });
                            } else {
                                $(this).empty();
                                $('#'+ select_name).append('<option selected="" disabled="" value="0">Select a product</option>');
                                $.each(JSON.parse(response), function(key,value) { 
                                    if (select_value == value.id){
                                        $('#'+ select_name).append('<option selected="selected" value=' + value.id + '>' + value.name + '</option>');
                                    } else {
                                        if (value.product_type == 'office' || value.product_type == 'citypaq'){
                                            $('#'+ select_name).append('<option value=' + value.id + ' disabled>' + value.name + '</option>');
                                        } else {
                                            $('#'+ select_name).append('<option value=' + value.id + '>' + value.name + '</option>');
                                        } 
                                    }                  
                                });
                            }
                        });
                    }
                });

            },
            error: function(e)
            {
                showModalErrorWindow(
                "ERROR 14502: "+product_technical_error);
            }            
        });
        e.preventDefault();
    });  

    $('#go_to_customer_data').click(function(){
        scrollToAnchor('#customer_data');
        $('#customer_data').click();
    });

    function scrollToAnchor(aid){
        var aTag = $(aid);
        $('html,body').animate({scrollTop: aTag.offset().top},'slow');
    }

});